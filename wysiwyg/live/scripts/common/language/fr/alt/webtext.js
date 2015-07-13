function loadTxt() {
    document.getElementById("tab0").innerHTML = "TEXTE";
    document.getElementById("tab1").innerHTML = "OMBRES";
    document.getElementById("tab2").innerHTML = "PARAGRAPHES";
    document.getElementById("tab3").innerHTML = "LISTES";
    document.getElementById("tab4").innerHTML = "TAILLE";

    document.getElementById("lblColor").innerHTML = "COULEUR:";
    document.getElementById("lblHighlight").innerHTML = "SURLIGNER:";
    document.getElementById("lblLineHeight").innerHTML = "HAUTEUR DE LIGNE:";
    document.getElementById("lblLetterSpacing").innerHTML = "ESPACES INTER LETTRES:";
    document.getElementById("lblWordSpacing").innerHTML = "ESPACES INTER MOTS:";
    document.getElementById("lblNote").innerHTML = "Non supporté pour l'instant sur IE.";
    document.getElementById("divShadowClear").innerHTML = "CLEAR";   
}
function writeTitle() {
    document.write("<title>" + "Texte" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "DEFAULT SIZE": return "TAILLE NORMALE";
        case "Heading 1": return "Heading 1";
        case "Heading 2": return "Heading 2";
        case "Heading 3": return "Heading 3";
        case "Heading 4": return "Heading 4";
        case "Heading 5": return "Heading 5";
        case "Heading 6": return "Heading 6";
        case "Preformatted": return "Preformatted";
        case "Normal": return "Normal";
    }
}