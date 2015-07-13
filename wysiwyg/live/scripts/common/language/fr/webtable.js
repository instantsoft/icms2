function loadTxt() {
    document.getElementById("tab0").innerHTML = "INSERER";
    document.getElementById("tab1").innerHTML = "MODIFIER";
    document.getElementById("tab2").innerHTML = "AUTOFORMATAGE";
    document.getElementById("btnDelTable").value = "Supprimer le tableau s\u00E9l\u00E9ctionn\u00E9";
    document.getElementById("btnIRow1").value = "Ins\u00E9rer une ligne (Haut)";
    document.getElementById("btnIRow2").value = "Ins\u00E9rer une ligne (Bas)";
    document.getElementById("btnICol1").value = "Ins\u00E9rer une colonne (G.)";
    document.getElementById("btnICol2").value = "Ins\u00E9rer une colonne (D.)";
    document.getElementById("btnDelRow").value = "Supprimer la ligne";
    document.getElementById("btnDelCol").value = "Supprimer la colonne";
    document.getElementById("btnMerge").value = "Fusionner les celulles";
    document.getElementById("lblFormat").innerHTML = "FORMAT:";
    document.getElementById("lblTable").innerHTML = "Tableau";
    document.getElementById("lblEven").innerHTML = "Lignes paires";
    document.getElementById("lblOdd").innerHTML = "Lignes impaires";
    document.getElementById("lblCurrRow").innerHTML = "Ligne s\u00E9l\u00E9ctionn\u00E9e";
    document.getElementById("lblCurrCol").innerHTML = "Colonne s\u00E9l\u00E9ctionn\u00E9e";
    document.getElementById("lblBg").innerHTML = "Couleur de fond:";
    document.getElementById("lblText").innerHTML = "Texte:";    
    document.getElementById("lblBorder").innerHTML = "BORDURE:";
    document.getElementById("lblThickness").innerHTML = "Epaisseur:";
    document.getElementById("lblBorderColor").innerHTML = "Couleur:";
    document.getElementById("lblCellPadding").innerHTML = "ESPACE ENTRE LES LIGNES:";
    document.getElementById("lblFullWidth").innerHTML = "Largeur totale";
    document.getElementById("lblAutofit").innerHTML = "Largeur auto.";
    document.getElementById("lblFixedWidth").innerHTML = "Largeur fixe:";
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
    document.write("<title>" + "Tableau" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "Clean Formatting": return "Nettoyer le formatage";
    }
}