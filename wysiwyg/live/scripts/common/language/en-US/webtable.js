function loadTxt() {
    document.getElementById("tab0").innerHTML = "INSERT";
    document.getElementById("tab1").innerHTML = "MODIFY";
    document.getElementById("tab2").innerHTML = "AUTOFORMAT";
    document.getElementById("btnDelTable").value = "Delete Selected Table";
    document.getElementById("btnIRow1").value = "Insert Row (Above)";
    document.getElementById("btnIRow2").value = "Insert Row (Below)";
    document.getElementById("btnICol1").value = "Insert Column (Left)";
    document.getElementById("btnICol2").value = "Insert Column (Right)";
    document.getElementById("btnDelRow").value = "Delete Row";
    document.getElementById("btnDelCol").value = "Delete Column";
    document.getElementById("btnMerge").value = "Merge Cell";
    document.getElementById("lblFormat").innerHTML = "FORMAT:";
    document.getElementById("lblTable").innerHTML = "Table";
    document.getElementById("lblEven").innerHTML = "Even Rows";
    document.getElementById("lblOdd").innerHTML = "Odd Rows";
    document.getElementById("lblCurrRow").innerHTML = "Current Row";
    document.getElementById("lblCurrCol").innerHTML = "Current Column";
    document.getElementById("lblBg").innerHTML = "Background:";
    document.getElementById("lblText").innerHTML = "Text:";    
    document.getElementById("lblBorder").innerHTML = "BORDER:";
    document.getElementById("lblThickness").innerHTML = "Thickness:";
    document.getElementById("lblBorderColor").innerHTML = "Color:";
    document.getElementById("lblCellPadding").innerHTML = "CELL PADDING:";
    document.getElementById("lblFullWidth").innerHTML = "Full Width";
    document.getElementById("lblAutofit").innerHTML = "Autofit";
    document.getElementById("lblFixedWidth").innerHTML = "Fixed Width:";
    document.getElementById("lnkClean").innerHTML = "CLEAN";
    document.getElementById("lblTextAlign").innerHTML = "TEXT ALIGN:";
    document.getElementById("btnAlignLeft").value = "Left";
    document.getElementById("btnAlignCenter").value = "Center";
    document.getElementById("btnAlignRight").value = "Right";
    document.getElementById("btnAlignTop").value = "Top";
    document.getElementById("btnAlignMiddle").value = "Middle";
    document.getElementById("btnAlignBottom").value = "Bottom";

    document.getElementById("lblColor").innerHTML = "COLOR:";
    document.getElementById("lblCellSize").innerHTML = "CELL SIZE:";
    document.getElementById("lblCellWidth").innerHTML = "Width:";
    document.getElementById("lblCellHeight").innerHTML = "Height:";       
}
function writeTitle() {
    document.write("<title>" + "Table" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "Clean Formatting": return "Clean Formatting";
    }
}