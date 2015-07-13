function loadTxt() {
    document.getElementById("tab0").innerHTML = "INFOGA";
    document.getElementById("tab1").innerHTML = "ÄNDRA";
    document.getElementById("tab2").innerHTML = "AUTOFORMAT";
    document.getElementById("btnDelTable").value = "Radera vald tabell";
    document.getElementById("btnIRow1").value = "Infoga rad (ovan)";
    document.getElementById("btnIRow2").value = "Infoga rad (nedan)";
    document.getElementById("btnICol1").value = "Infoga kolumn\nvänster";
    document.getElementById("btnICol2").value = "Infoga kolumn\nhöger";
    document.getElementById("btnDelRow").value = "Radera rad";
    document.getElementById("btnDelCol").value = "Radera kolumn";
    document.getElementById("btnMerge").value = "Sammanfoga celler";
    document.getElementById("lblFormat").innerHTML = "FORMAT:";
    document.getElementById("lblTable").innerHTML = "Tabell";
    document.getElementById("lblEven").innerHTML = "Jämna rader";
    document.getElementById("lblOdd").innerHTML = "Ojämna rader";
    document.getElementById("lblCurrRow").innerHTML = "Aktuell rad";
    document.getElementById("lblCurrCol").innerHTML = "Aktuell kolumn";
    document.getElementById("lblBg").innerHTML = "Bakgrund:";
    document.getElementById("lblText").innerHTML = "Text:";    
    document.getElementById("lblBorder").innerHTML = "KANTLINJER:";
    document.getElementById("lblThickness").innerHTML = "Tjocklek:";
    document.getElementById("lblBorderColor").innerHTML = "Färg:";
    document.getElementById("lblCellPadding").innerHTML = "CELLUTFYLLNAD:";
    document.getElementById("lblFullWidth").innerHTML = "Full bredd";
    document.getElementById("lblAutofit").innerHTML = "Autopassa";
    document.getElementById("lblFixedWidth").innerHTML = "Fast bredd:";
    document.getElementById("lnkClean").innerHTML = "RENSA";
    document.getElementById("lblTextAlign").innerHTML = "JUSTERA TEXT:";
    document.getElementById("btnAlignLeft").value = "Vänster";
    document.getElementById("btnAlignCenter").value = "Centrerad";
    document.getElementById("btnAlignRight").value = "Höger";
    document.getElementById("btnAlignTop").value = "Topp";
    document.getElementById("btnAlignMiddle").value = "Mitten";
    document.getElementById("btnAlignBottom").value = "Botten";

    document.getElementById("lblColor").innerHTML = "COLOR:";
    document.getElementById("lblCellSize").innerHTML = "CELL SIZE:";
    document.getElementById("lblCellWidth").innerHTML = "Width:";
    document.getElementById("lblCellHeight").innerHTML = "Height:";   
}
function writeTitle() {
    document.write("<title>" + "Tabell" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "Clean Formatting": return "Rensa formatering";
    }
}