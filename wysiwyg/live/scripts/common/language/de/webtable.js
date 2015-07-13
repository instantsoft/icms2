function loadTxt() {
    document.getElementById("tab0").innerHTML = "EINF&Uuml;GEN";
    document.getElementById("tab1").innerHTML = "&Auml;NDERN";
    document.getElementById("tab2").innerHTML = "AUTOFORMAT";
    document.getElementById("btnDelTable").value = "Ausgew\u00E4hlte Tabelle l\u00F6schen";
    document.getElementById("btnIRow1").value = "Reihe einf\u00fcgen (Oben)";
    document.getElementById("btnIRow2").value = "Reihe einf\u00fcgen (Unten)";
    document.getElementById("btnICol1").value = "Spalte einf\u00fcgen (Links)";
    document.getElementById("btnICol2").value = "Spalte einf\u00fcgen (Rechts)";
    document.getElementById("btnDelRow").value = "Reihe l\u00F6schen";
    document.getElementById("btnDelCol").value = "Spalte l\u00F6schen";
    document.getElementById("btnMerge").value = "Zellen verbinden";
    document.getElementById("lblFormat").innerHTML = "FORMATIERUNG:";
    document.getElementById("lblTable").innerHTML = "Tabelle";
    document.getElementById("lblEven").innerHTML = "Gerade Reihe";
    document.getElementById("lblOdd").innerHTML = "Ungerade Reihe";
    document.getElementById("lblCurrRow").innerHTML = "Aktuelle Reihe";
    document.getElementById("lblCurrCol").innerHTML = "Aktuelle Spalte";
    document.getElementById("lblBg").innerHTML = "Hintergrund:";
    document.getElementById("lblText").innerHTML = "Text:";    
    document.getElementById("lblBorder").innerHTML = "RAHMEN:";
    document.getElementById("lblThickness").innerHTML = "St&auml;rke:";
    document.getElementById("lblBorderColor").innerHTML = "Farbe:";
    document.getElementById("lblCellPadding").innerHTML = "Zellenabstand:";
    document.getElementById("lblFullWidth").innerHTML = "100%";
    document.getElementById("lblAutofit").innerHTML = "Automatisch";
    document.getElementById("lblFixedWidth").innerHTML = "Fix:";
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
    document.write("<title>" + "Tabelle" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "Clean Formatting": return "Formatierung entfernen";
    }
}