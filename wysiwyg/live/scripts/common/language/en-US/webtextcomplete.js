function loadTxt() {
    document.getElementById("tab0").innerHTML = "FONTS";
    //document.getElementById("tab1").innerHTML = "BASIC FONTS";
    document.getElementById("tab2").innerHTML = "SIZE";
    document.getElementById("tab3").innerHTML = "SHADOWS";
    document.getElementById("tab4").innerHTML = "PARAGRAPHS";
    document.getElementById("tab5").innerHTML = "LISTINGS";


    document.getElementById("lblColor").innerHTML = "COLOR:";
    document.getElementById("lblHighlight").innerHTML = "HIGHLIGHT:";
    document.getElementById("lblLineHeight").innerHTML = "LINE HEIGHT:";
    document.getElementById("lblLetterSpacing").innerHTML = "LETTER SPACING:";
    document.getElementById("lblWordSpacing").innerHTML = "WORD SPACING:";
    document.getElementById("lblNote").innerHTML = "This feature is not currently supported in IE.";
    document.getElementById("divShadowClear").innerHTML = "CLEAR";   
}
function writeTitle() {
    document.write("<title>" + "Text" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "DEFAULT SIZE": return "DEFAULT SIZE";
        case "Heading 1": return "Heading 1";
        case "Heading 2": return "Heading 2";
        case "Heading 3": return "Heading 3";
        case "Heading 4": return "Heading 4";
        case "Heading 5": return "Heading 5";
        case "Heading 6": return "Heading 6";
        case "Preformatted": return "Preformatted";
        case "Normal": return "Normal";
        case "Google Font": return "GOOGLE FONTS:";
    }
}