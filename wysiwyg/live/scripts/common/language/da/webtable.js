function loadTxt() {
    document.getElementById("tab0").innerHTML = "INDSÆT";
    document.getElementById("tab1").innerHTML = "REDIGER";
    document.getElementById("tab2").innerHTML = "AUTOFORMATER";
    document.getElementById("btnDelTable").value = "Slet den angivet tabel";
    document.getElementById("btnIRow1").value = "Indsæt række (Over)";
    document.getElementById("btnIRow2").value = "Indsæt række (Under)";
    document.getElementById("btnICol1").value = "Indsæt kolonne (Venstre)";
    document.getElementById("btnICol2").value = "Indsæt kolonne (Højre)";
    document.getElementById("btnDelRow").value = "Slet række";
    document.getElementById("btnDelCol").value = "Slet kolonne";
    document.getElementById("btnMerge").value = "Flet celler";
    document.getElementById("lblFormat").innerHTML = "FORMAT:";
    document.getElementById("lblTable").innerHTML = "Tabel";
    document.getElementById("lblEven").innerHTML = "Lige rækker";
    document.getElementById("lblOdd").innerHTML = "Ulige rækker";
    document.getElementById("lblCurrRow").innerHTML = "Aktuelle række";
    document.getElementById("lblCurrCol").innerHTML = "Aktuelle kolonne";
    document.getElementById("lblBg").innerHTML = "Baggrund:";
    document.getElementById("lblText").innerHTML = "Tekst:";   
    document.getElementById("lblBorder").innerHTML = "RAMME:";
    document.getElementById("lblThickness").innerHTML = "Tykkelse:";
    document.getElementById("lblBorderColor").innerHTML = "Farve:";
    document.getElementById("lblCellPadding").innerHTML = "Cellemargen:";
    document.getElementById("lblFullWidth").innerHTML = "Fuld bredde";
    document.getElementById("lblAutofit").innerHTML = "Automatisk tilpas";
    document.getElementById("lblFixedWidth").innerHTML = "Fast bredde:";
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
    document.write("<title>" + "Tabel" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "Clean Formatting": return "Slet formatering";
    }
}