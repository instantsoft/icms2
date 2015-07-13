function loadTxt() {
    document.getElementById("tab0").innerHTML = "TEXTE";
    document.getElementById("tab1").innerHTML = "OMBRES";
    document.getElementById("tab2").innerHTML = "PARAGRAPHES";
    document.getElementById("tab3").innerHTML = "LISTES";
    document.getElementById("tab4").innerHTML = "TAILLE";

    document.getElementById("lblColor").innerHTML = "COULEUR:";
    document.getElementById("lblHighlight").innerHTML = "SURBRILLANCE:";
    document.getElementById("lblLineHeight").innerHTML = "HAUTEUR DE LIGNE:";
    document.getElementById("lblLetterSpacing").innerHTML = "ESPACE ENTRE LES LETTRES:";
    document.getElementById("lblWordSpacing").innerHTML = "ESPACE ENTRE LES MOTS:";
    document.getElementById("lblNote").innerHTML = "Cette option n'est pas compatible avec IE.";
    document.getElementById("divShadowClear").innerHTML = "CLEAR";   
}
function writeTitle() {
    document.write("<title>" + "Texte" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "DEFAULT SIZE": return "TAILLE PAR DEFAUT";
        case "Heading 1": return "Entete 1";
        case "Heading 2": return "Entete 2";
        case "Heading 3": return "Entete 3";
        case "Heading 4": return "Entete 4";
        case "Heading 5": return "Entete 5";
        case "Heading 6": return "Entete 6";
        case "Preformatted": return "Pr\u00E9formatt\u00E9";
        case "Normal": return "Normale";
    }
}