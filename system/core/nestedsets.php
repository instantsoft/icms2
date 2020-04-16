<?php

class cmsNestedsets {

    private $TableName = '';
    private $FieldID;
    private $FieldIDParent;
    private $FieldLeft;
    private $FieldRight;
    private $FieldDiffer;
    private $FieldLevel;
    private $FieldOrder;
    private $FieldIgnore;
    private $db;

    public function __construct($db) {

        $this->FieldID       = 'id';
        $this->FieldIDParent = 'parent_id';
        $this->FieldOrder    = 'ordering';
        $this->FieldLeft     = 'ns_left';
        $this->FieldRight    = 'ns_right';
        $this->FieldDiffer   = 'ns_differ';
        $this->FieldLevel    = 'ns_level';
        $this->FieldIgnore   = 'ns_ignore';

        $this->db = $db;

    }

    public function setTable($table) {
        $this->TableName = "{#}$table";
    }

    public function _safe_set(&$var_true, $var_false = '') {
        if (!isset($var_true)) {
            $var_true = $var_false;
        }
    }

    public function _safe_query($query) {
        if (empty($query)) {
            return false;
        }
        return $this->db->query($query);
    }

    public function ClearNodes($Differ = '') {
        $sql_delete = 'DELETE FROM ' . $this->TableName . ' WHERE ' . $this->FieldDiffer . " = '" . $Differ . "'";
        $this->_safe_query($sql_delete);
    }

    public function DeleteNode($IDNode = -1, $Differ = '') {
        $sql_select = 'SELECT * FROM ' . $this->TableName . ' WHERE ' . $this->FieldID . ' = ' . $IDNode . ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
        $rs_select  = $this->_safe_query($sql_select);
        if ($rs_select && ($row_select = $this->db->fetchAssoc($rs_select))) {

            $this->_safe_set($row_select[$this->FieldID], -1);
            $this->_safe_set($row_select[$this->FieldLeft], -1);
            $this->_safe_set($row_select[$this->FieldRight], -1);
            $delete_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft];

            // Delete sub nodes
            $sql_delete = 'DELETE FROM ' . $this->TableName .
                    ' WHERE ' . $this->FieldLeft . ' >= ' . $row_select[$this->FieldLeft] .
                    ' AND ' . $this->FieldLeft . ' <= ' . $row_select[$this->FieldRight] .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
            $this->_safe_query($sql_delete);

            // Update FieldLeft
            $sql_update = 'UPDATE ' . $this->TableName .
                    ' SET ' . $this->FieldLeft . ' = ' . $this->FieldLeft . ' - ' . ($delete_offset + 1) .
                    ' WHERE ' . $this->FieldLeft . ' > ' . $row_select[$this->FieldRight] .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
            $this->_safe_query($sql_update);

            // Update FieldRight
            $sql_update = 'UPDATE ' . $this->TableName .
                    ' SET ' . $this->FieldRight . ' = ' . $this->FieldRight . ' - ' . ($delete_offset + 1) .
                    ' WHERE ' . $this->FieldRight . ' > ' . $row_select[$this->FieldRight] .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
            $this->_safe_query($sql_update);

            // Update Ordering
            $sql_update = 'UPDATE ' . $this->TableName .
                    ' SET ' . $this->FieldOrder . ' = ' . $this->FieldOrder . ' - 1' .
                    ' WHERE ' . $this->FieldOrder . ' > ' . $row_select[$this->FieldOrder] .
                    ' AND ' . $this->FieldLevel . ' = ' . $row_select[$this->FieldLevel] .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
            $this->_safe_query($sql_update);

            $this->db->freeResult($rs_select);

            return true;
        } else {
            return false;
        }
    }

    public function AddRootNode($Differ = '') {
        $sql_insert = 'INSERT INTO ' . $this->TableName .
                ' (title, ' . $this->FieldIDParent . ', ' . $this->FieldLeft . ', ' . $this->FieldRight .
                ', ' . $this->FieldLevel . ', ' . $this->FieldOrder . ', ' . $this->FieldDiffer . ') ' .
                " VALUES ('---', 0, 1, 2, 0, 1, '" . $Differ . "')";
        $this->_safe_query($sql_insert);
        return $this->db->lastId();
    }

    public function AddNode($IDParent = -1, $Order = -1, $Differ = '') {

        $sql_select = 'SELECT * FROM ' . $this->TableName . ' WHERE ' . $this->FieldID . ' = ' . $IDParent . ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
        $rs_select  = $this->_safe_query($sql_select);
        if (($rs_select) && ($row_select = $this->db->fetchAssoc($rs_select))) {
            $this->_safe_set($row_select[$this->FieldID], -1);
            $this->_safe_set($row_select[$this->FieldLeft], -1);
            $this->_safe_set($row_select[$this->FieldRight], -1);
            $this->_safe_set($row_select[$this->FieldLevel], -1);

            $left = $row_select[$this->FieldLeft] + 1;

            // Update Order (set order = order +1 where order>$Order)
            if ($Order == -1) {
                $sql_order = 'SELECT * FROM ' . $this->TableName .
                        ' WHERE ' . $this->FieldIDParent . ' = ' . $IDParent .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                        ' ORDER BY ' . $this->FieldOrder . ' DESC ' .
                        ' LIMIT 0,1';
                $rs_order  = $this->_safe_query($sql_order);
                if (($rs_order) && ($row_order = $this->db->fetchAssoc($rs_order))) {
                    $this->_safe_set($row_order[$this->FieldOrder], 0);
                    $Order = $row_order[$this->FieldOrder] + 1;
                    $this->db->freeResult($rs_order);
                } else {
                    $Order = 1;
                }
            }

            $sql_update = 'UPDATE ' . $this->TableName .
                    ' SET ' . $this->FieldOrder . ' = ' . $this->FieldOrder . ' + 1' .
                    ' WHERE ' . $this->FieldIDParent . ' = ' . $IDParent .
                    ' AND ' . $this->FieldOrder . ' >= ' . $Order .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
            $this->_safe_query($sql_update);

            $sql_order = 'SELECT * FROM ' . $this->TableName .
                    ' WHERE ' . $this->FieldIDParent . ' = ' . $IDParent .
                    ' AND ' . $this->FieldOrder . ' <= ' . $Order .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                    ' ORDER BY ' . $this->FieldOrder . ' DESC ' .
                    ' LIMIT 0,1';
            $rs_order  = $this->_safe_query($sql_order);
            if (($rs_order) && ($row_order = $this->db->fetchAssoc($rs_order))) {
                $this->_safe_set($row_order[$this->FieldRight], -1);
                $left = $row_order[$this->FieldRight] + 1;
                $this->db->freeResult($rs_order);
            }

            $right = $left + 1;

            // Update FieldLeft
            $sql_update = 'UPDATE ' . $this->TableName .
                    ' SET ' . $this->FieldLeft . ' = ' . $this->FieldLeft . ' + 2' .
                    ' WHERE ' . $this->FieldLeft . ' >= ' . $left .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
            $this->_safe_query($sql_update);

            // Update FieldRight
            $sql_update = 'UPDATE ' . $this->TableName .
                    ' SET ' . $this->FieldRight . ' = ' . $this->FieldRight . ' + 2' .
                    ' WHERE ' . $this->FieldRight . ' >= ' . $left .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
            $this->_safe_query($sql_update);

            // Insert
            $sql_insert = 'INSERT INTO ' . $this->TableName .
                    ' (' . $this->FieldIDParent . ', ' . $this->FieldLeft . ', ' . $this->FieldRight .
                    ', ' . $this->FieldLevel . ', ' . $this->FieldOrder . ', ' . $this->FieldDiffer . ') ' .
                    ' VALUES (' . $IDParent . ', ' . $left . ', ' . $right .
                    ', ' . ($row_select[$this->FieldLevel] + 1) . ', ' . $Order . ", '" . $Differ . "')";
            $this->_safe_query($sql_insert);

            $this->db->freeResult($rs_select);

            return $this->db->lastId();
        } else {
            return false;
        }
    }

    public function MoveOrdering($IDNode, $dir = 1) {

        $sql      = "SELECT * FROM {$this->TableName} WHERE {$this->FieldID}='{$IDNode}'";
        $res      = $this->_safe_query($sql);
        $move_row = $this->db->fetchAssoc($res);
        $this->db->freeResult($res);

        if ($move_row[$this->FieldDiffer]){
            $Differ = 'AND ' . $this->FieldDiffer . ' = ' . $move_row[$this->FieldDiffer];
        } else {
            $Differ = '';
        }

        // максимальное значение сортировки
        $sql = "SELECT MAX({$this->FieldOrder}) FROM {$this->TableName} WHERE {$this->FieldIDParent}={$move_row[$this->FieldIDParent]}";
        $res = $this->_safe_query($sql);
        list($maxordering) = $this->db->fetchRow($res);
        if (!$maxordering){
            $maxordering = 1;
        }
        // минимальное значение сортировки
        $sql_min     = "SELECT MIN({$this->FieldOrder}) FROM {$this->TableName} WHERE {$this->FieldIDParent}={$move_row[$this->FieldIDParent]}";
        $res_min     = $this->_safe_query($sql_min);
        list($minordering) = $this->db->fetchRow($res_min);
        if (!$minordering){
            $minordering = 1;
        }
        $this->db->freeResult($res);

        if ($dir == -1 && $move_row[$this->FieldOrder] == $minordering){
            return;
        }
        if ($dir == 1 && $move_row[$this->FieldOrder] == $maxordering){
            return;
        }

        if ($dir == -1) {

            $sql   = "UPDATE {$this->TableName} SET {$this->FieldIgnore} = 1
                    WHERE {$this->FieldLeft} >= {$move_row[$this->FieldLeft]} AND {$this->FieldRight} <= {$move_row[$this->FieldRight]} {$Differ}";
            $this->_safe_query($sql);
            $count = $this->db->affectedRows() * 2;

            $sql  = "SELECT * FROM {$this->TableName}
                    WHERE {$this->FieldIDParent} = {$move_row[$this->FieldIDParent]} AND {$this->FieldOrder} = " . ($move_row[$this->FieldOrder] - 1);
            $res  = $this->_safe_query($sql);
            $near = $this->db->fetchAssoc($res);
            $this->db->freeResult($res);

            $sql = "UPDATE {$this->TableName}
                    SET {$this->FieldLeft} = {$this->FieldLeft} + {$count},
                        {$this->FieldRight} = {$this->FieldRight} + {$count}
                    WHERE {$this->FieldLeft} >= {$near[$this->FieldLeft]} AND {$this->FieldRight} <= {$near[$this->FieldRight]}
                    {$Differ}";

            $this->_safe_query($sql);
            $count2 = $this->db->affectedRows() * 2;

            $sql = "UPDATE {$this->TableName}
                    SET {$this->FieldLeft} = {$this->FieldLeft} - {$count2},
                        {$this->FieldRight} = {$this->FieldRight} - {$count2},
                        {$this->FieldIgnore} = 0
                    WHERE {$this->FieldIgnore} = 1
                    {$Differ}";
            $this->_safe_query($sql);

            $sql = "UPDATE {$this->TableName} SET {$this->FieldOrder} = {$this->FieldOrder} - 1
                    WHERE {$this->FieldID} = {$IDNode}";
            $this->_safe_query($sql);

            $sql = "UPDATE {$this->TableName} SET {$this->FieldOrder} = {$this->FieldOrder} + 1
                    WHERE {$this->FieldID} = {$near[$this->FieldID]}";
            $this->_safe_query($sql);
        }

        if ($dir == 1) {

            $sql   = "UPDATE {$this->TableName} SET {$this->FieldIgnore} = 1
                    WHERE {$this->FieldLeft} >= {$move_row[$this->FieldLeft]} AND {$this->FieldRight} <= {$move_row[$this->FieldRight]} {$Differ}";
            $this->_safe_query($sql);
            $count = $this->db->affectedRows() * 2;

            $sql  = "SELECT * FROM {$this->TableName}
                    WHERE {$this->FieldIDParent} = {$move_row[$this->FieldIDParent]} AND {$this->FieldOrder} = " . ($move_row[$this->FieldOrder] + 1);
            $res  = $this->_safe_query($sql);
            $near = $this->db->fetchAssoc($res);
            $this->db->freeResult($res);

            $sql = "UPDATE {$this->TableName}
                    SET {$this->FieldLeft} = {$this->FieldLeft} - {$count},
                        {$this->FieldRight} = {$this->FieldRight} - {$count}
                    WHERE {$this->FieldLeft} >= {$near[$this->FieldLeft]} AND {$this->FieldRight} <= {$near[$this->FieldRight]}
                    {$Differ}";

            $this->_safe_query($sql);
            $count2 = $this->db->affectedRows() * 2;

            $sql = "UPDATE {$this->TableName}
                    SET {$this->FieldLeft} = {$this->FieldLeft} + {$count2},
                        {$this->FieldRight} = {$this->FieldRight} + {$count2},
                        {$this->FieldIgnore} = 0
                    WHERE {$this->FieldIgnore} = 1
                    {$Differ}";
            $this->_safe_query($sql);

            $sql = "UPDATE {$this->TableName} SET {$this->FieldOrder} = {$this->FieldOrder} + 1
                    WHERE {$this->FieldID} = {$IDNode}";
            $this->_safe_query($sql);

            $sql = "UPDATE {$this->TableName} SET {$this->FieldOrder} = {$this->FieldOrder} - 1
                    WHERE {$this->FieldID} = {$near[$this->FieldID]}";
            $this->_safe_query($sql);
        }

        return true;

    }

    public function MoveNode($IDNode = -1, $IDParent = -1, $Order = -1, $Differ = '') {

        $sql_select = 'SELECT * FROM ' . $this->TableName .
                ' WHERE ' . $this->FieldID . ' = ' . $IDNode .
                ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";

        $rs_select  = $this->_safe_query($sql_select);

        if (($rs_select) && ($row_select = $this->db->fetchAssoc($rs_select))) {

            $this->_safe_set($row_select[$this->FieldID], -1);
            $this->_safe_set($row_select[$this->FieldLeft], -1);
            $this->_safe_set($row_select[$this->FieldRight], -1);
            $this->_safe_set($row_select[$this->FieldLevel], -1);

            $sql_select_parent = 'SELECT * FROM ' . $this->TableName .
                    ' WHERE ' . $this->FieldID . ' = ' . $IDParent .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";

            $rs_select_parent  = $this->_safe_query($sql_select_parent);

            if (($rs_select_parent) && ($row_select_parent = $this->db->fetchAssoc($rs_select_parent))) {

                $this->_safe_set($row_select_parent[$this->FieldID], -1);
                $this->_safe_set($row_select_parent[$this->FieldLeft], -1);
                $this->_safe_set($row_select_parent[$this->FieldRight], -1);
                $this->_safe_set($row_select_parent[$this->FieldLevel], -1);

                $left = $row_select_parent[$this->FieldLeft] + 1;

                //Set node tree as ignore
                $sql_ignore = 'UPDATE ' . $this->TableName .
                        ' SET ' . $this->FieldIgnore . ' = 1' .
                        ' WHERE ' . $this->FieldLeft . ' >= ' . $row_select[$this->FieldLeft] .
                        ' AND ' . $this->FieldRight . ' <= ' . $row_select[$this->FieldRight] .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query($sql_ignore);

                // Update Order (set order = order +1 where order>$Order)
                if ($Order == -1) {
                    $sql_order = 'SELECT * FROM ' . $this->TableName .
                            ' WHERE ' . $this->FieldIDParent . ' = ' . $IDParent .
                            ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                            ' ORDER BY ' . $this->FieldOrder . ' DESC ' .
                            ' LIMIT 0,1';
                    $rs_order  = $this->_safe_query($sql_order);
                    if (($rs_order) && ($row_order = $this->db->fetchAssoc($rs_order))) {
                        $this->_safe_set($row_order[$this->FieldOrder], 0);
                        $Order = $row_order[$this->FieldOrder] + 1;
                        $this->db->freeResult($rs_order);
                    } else {
                        $Order = 1;
                    }
                }

                $sql_update = 'UPDATE ' . $this->TableName .
                        ' SET ' . $this->FieldOrder . ' = ' . $this->FieldOrder . ' + 1' .
                        ' WHERE ' . $this->FieldIDParent . ' = ' . $IDParent .
                        ' AND ' . $this->FieldOrder . ' >= ' . $Order .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query($sql_update);

                $sql_order = 'SELECT * FROM ' . $this->TableName .
                        ' WHERE ' . $this->FieldIDParent . ' = ' . $IDParent .
                        ' AND ' . $this->FieldOrder . ' <= ' . $Order .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                        ' ORDER BY ' . $this->FieldOrder . ' DESC ' .
                        ' LIMIT 0,1';
                $rs_order  = $this->_safe_query($sql_order);

                if (($rs_order) && ($row_order = $this->db->fetchAssoc($rs_order))) {
                    $this->_safe_set($row_order[$this->FieldRight], -1);
                    $left = $row_order[$this->FieldRight] + 1;
                    $this->db->freeResult($rs_order);
                }

                $child_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft] + 1;

                // Update FieldLeft
                if ($left < $row_select[$this->FieldLeft]) { // Move to left
                    $sql_update = 'UPDATE ' . $this->TableName .
                            ' SET ' . $this->FieldLeft . ' = ' . $this->FieldLeft . ' + (' . $child_offset . ')' .
                            ' WHERE ' . $this->FieldLeft . ' >= ' . $left .
                            ' AND ' . $this->FieldLeft . ' <= ' . $row_select[$this->FieldLeft] .
                            ' AND ' . $this->FieldIgnore . ' = 0' .
                            ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
                } else { // Move to right
                    $sql_update = 'UPDATE ' . $this->TableName .
                            ' SET ' . $this->FieldLeft . ' = ' . $this->FieldLeft . ' - ' . $child_offset .
                            ' WHERE ' . $this->FieldLeft . ' <= ' . $left .
                            ' AND ' . $this->FieldLeft . ' >= ' . $row_select[$this->FieldLeft] .
                            ' AND ' . $this->FieldIgnore . ' = 0' .
                            ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
                }
                $this->_safe_query($sql_update);

                // Update FieldRight
                if ($left < $row_select[$this->FieldLeft]) { // Move to left
                    $sql_update = 'UPDATE ' . $this->TableName .
                            ' SET ' . $this->FieldRight . ' = ' . $this->FieldRight . ' + (' . $child_offset . ')' .
                            ' WHERE ' . $this->FieldRight . ' >= ' . $left .
                            ' AND ' . $this->FieldRight . ' <= ' . $row_select[$this->FieldRight] .
                            ' AND ' . $this->FieldIgnore . ' = 0' .
                            ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
                } else { // Move to right
                    $sql_update = 'UPDATE ' . $this->TableName .
                            ' SET ' . $this->FieldRight . ' = ' . $this->FieldRight . ' - ' . $child_offset .
                            ' WHERE ' . $this->FieldRight . ' < ' . $left .
                            ' AND ' . $this->FieldRight . ' >= ' . $row_select[$this->FieldRight] .
                            ' AND ' . $this->FieldIgnore . ' = 0' .
                            ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
                }
                $this->_safe_query($sql_update);

                $level_difference = $row_select_parent[$this->FieldLevel] - $row_select[$this->FieldLevel] + 1;
                $new_offset       = $row_select[$this->FieldLeft] - $left;
                if ($left > $row_select[$this->FieldLeft]) { // i.e. move to right
                    $new_offset += $child_offset;
                }

                //Update new tree left
                $sql_update = 'UPDATE ' . $this->TableName .
                        ' SET ' . $this->FieldLeft . ' = ' . $this->FieldLeft . ' - (' . $new_offset . '), ' .
                        $this->FieldRight . ' = ' . $this->FieldRight . ' - (' . $new_offset . '),' .
                        "{$this->FieldLevel} = {$this->FieldLevel} + {$level_difference}" .
                        ' WHERE ' . $this->FieldLeft . ' >= ' . $row_select[$this->FieldLeft] .
                        ' AND ' . $this->FieldRight . ' <= ' . $row_select[$this->FieldRight] .
                        ' AND ' . $this->FieldIgnore . ' = 1' .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query($sql_update);

                //Remove ignore statis from node tree
                $sql_ignore = 'UPDATE ' . $this->TableName .
                        ' SET ' . $this->FieldIgnore . ' = 0' .
                        ' WHERE ' . $this->FieldLeft . ' >= ' . ($row_select[$this->FieldLeft] - $new_offset) .
                        ' AND ' . $this->FieldRight . ' <= ' . ($row_select[$this->FieldRight] - $new_offset) .
                        ' AND ' . $this->FieldIgnore . ' = 1' .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query($sql_ignore);

                //Update insert root field
                $sql_update = 'UPDATE ' . $this->TableName . ' SET ' . $this->FieldIDParent . ' = ' . $IDParent . ', ' .
                        $this->FieldOrder . ' = ' . $Order . ' WHERE ' . $this->FieldID . ' = ' . $IDNode;
                $this->_safe_query($sql_update);

                $this->db->freeResult($rs_select_parent);
                return true;
            } else {
                return false;
            }

            $this->db->freeResult($rs_select);
            return true;
        } else {
            return false;
        }
    }

    public function SelectPath($IDNode = -1, $Differ = '') {
        $sql_select = 'SELECT * FROM ' . $this->TableName . ' WHERE ' . $this->FieldID . ' = ' . $IDNode . ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
        $rs_select  = $this->_safe_query($sql_select);
        if (($rs_select) && ($row_select = $this->db->fetchAssoc($rs_select))) {
            $this->_safe_set($row_select[$this->FieldID], -1);
            $this->_safe_set($row_select[$this->FieldLeft], -1);
            $this->_safe_set($row_select[$this->FieldRight], -1);
            $sql_result = 'SELECT * FROM ' . $this->TableName .
                    ' WHERE ' . $this->FieldLeft . ' <= ' . $row_select[$this->FieldLeft] .
                    ' AND ' . $this->FieldRight . ' >= ' . $row_select[$this->FieldRight] .
                    ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                    ' ORDER BY ' . $this->FieldLeft;
            $this->db->freeResult($rs_select);
            return $this->_safe_query($sql_result); // Remember to free result
        } else {
            return false;
        }
    }

    public function SelectSubNodes($IDNode = -1, $Level = -1, $Differ = '') {
        $sql_select = 'SELECT * FROM ' . $this->TableName . ' WHERE ' . $this->FieldID . ' = ' . $IDNode . ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
        $rs_select  = $this->_safe_query($sql_select);
        if (($rs_select) && ($row_select = $this->db->fetchAssoc($rs_select))) {
            $this->_safe_set($row_select[$this->FieldID], -1);
            $this->_safe_set($row_select[$this->FieldLeft], -1);
            $this->_safe_set($row_select[$this->FieldRight], -1);
            $this->_safe_set($row_select[$this->FieldLevel], -1);
            if ($Level == -1) { // All child nodes
                $sql_result = 'SELECT * FROM ' . $this->TableName .
                        ' WHERE ' . $this->FieldLeft . ' > ' . $row_select[$this->FieldLeft] .
                        ' AND ' . $this->FieldRight . ' < ' . $row_select[$this->FieldRight] .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                        ' ORDER BY ' . $this->FieldLeft . ',' . $this->FieldOrder;
            } else { // Only $Level child nodes
                $sql_result = 'SELECT * FROM ' . $this->TableName .
                        ' WHERE ' . $this->FieldLeft . ' > ' . $row_select[$this->FieldLeft] .
                        ' AND ' . $this->FieldRight . ' < ' . $row_select[$this->FieldRight] .
                        ' AND ' . $this->FieldLevel . ' <= ' . ($Level + $row_select[$this->FieldLevel]) .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                        ' ORDER BY ' . $this->FieldLeft . ',' . $this->FieldOrder;
            }
            $this->db->freeResult($rs_select);
            return $this->_safe_query($sql_result); // Remember to free result
        } else {
            return false;
        }

    }

    public function SelectCountSubNodes($IDNode = -1, $Level = -1, $Differ = '') {
        $sql_select = 'SELECT * FROM ' . $this->TableName . ' WHERE ' . $this->FieldID . ' = ' . $IDNode . ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'";
        $rs_select  = $this->_safe_query($sql_select);
        if (($rs_select) && ($row_select = $this->db->fetchAssoc($rs_select))) {
            $this->_safe_set($row_select[$this->FieldID], -1);
            $this->_safe_set($row_select[$this->FieldLeft], -1);
            $this->_safe_set($row_select[$this->FieldRight], -1);
            $this->_safe_set($row_select[$this->FieldLevel], -1);
            if ($Level == -1) { // All child nodes
                $sql_result = 'SELECT count(' . $this->FieldID . ') FROM ' . $this->TableName .
                        ' WHERE ' . $this->FieldLeft . ' > ' . $row_select[$this->FieldLeft] .
                        ' AND ' . $this->FieldRight . ' < ' . $row_select[$this->FieldRight] .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                        ' ORDER BY ' . $this->FieldLeft . ',' . $this->FieldOrder;
            } else { // Only $Level child nodes
                $sql_result = 'SELECT count(' . $this->FieldID . ') FROM ' . $this->TableName .
                        ' WHERE ' . $this->FieldLeft . ' > ' . $row_select[$this->FieldLeft] .
                        ' AND ' . $this->FieldRight . ' < ' . $row_select[$this->FieldRight] .
                        ' AND ' . $this->FieldLevel . ' <= ' . ($Level + $row_select[$this->FieldLevel]) .
                        ' AND ' . $this->FieldDiffer . " = '" . $Differ . "'" .
                        ' ORDER BY ' . $this->FieldLeft . ',' . $this->FieldOrder;
            }
            $this->db->freeResult($rs_select);
            $res = $this->_safe_query($sql_result); // Remember to free result
            list($count) = $this->db->fetchRow($res);
            $this->db->freeResult($res);
            return $count;
        } else {
            return false;
        }
    }

}
