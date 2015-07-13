function loadTxt() {
    document.getElementById("tab0").innerHTML = "POLICES";
    //document.getElementById("tab1").innerHTML = "BASIC FONTS";
    document.getElementById("tab2").innerHTML = "TAILLE";
    document.getElementById("tab3").innerHTML = "OMBRES";
    document.getElementById("tab4").innerHTML = "PARAGRAPH";
    document.getElementById("tab5").innerHTML = "LISTES";


    document.getElementById("lblColor").innerHTML = "COULEUR:";
    document.getElementById("lblHighlight").innerHTML = "SURLIGNAGE:";
    document.getElementById("lblLineHeight").innerHTML = "HAUTEUR LIGNE:";
    document.getElementById("lblLetterSpacing").innerHTML = "INTERLETTRAGE:";
    document.getElementById("lblWordSpacing").innerHTML = "ESPACE ENTRE MOTS:";
    document.getElementById("lblNote").innerHTML = "Non supporté sur Internet Explorer";
    document.getElementById("divShadowClear").innerHTML = "CLEAR";   
}
function writeTitle() {
    document.write("<title>" + "Texte" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "DEFAULT SIZE": return "TAILLE STANDARD";
        case "Heading 1": return "Entête 1";
        case "Heading 2": return "Entête 2";
        case "Heading 3": return "Entête 3";
        case "Heading 4": return "Entête 4";
        case "Heading 5": return "Entête 5";
        case "Heading 6": return "Entête 6";
        case "Preformatted": return "Préformatté";
        case "Normal": return "Normal";
        case "Google Font": return "POLICES GOOGLE:";
    }
}