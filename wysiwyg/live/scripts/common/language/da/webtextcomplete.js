function loadTxt() {
    document.getElementById("tab0").innerHTML = "FONTS";
    //document.getElementById("tab1").innerHTML = "BASIC FONTS";
    document.getElementById("tab2").innerHTML = "STR.";
    document.getElementById("tab3").innerHTML = "SKYGGE";
    document.getElementById("tab4").innerHTML = "AFSNIT";
    document.getElementById("tab5").innerHTML = "LISTE";


    document.getElementById("lblColor").innerHTML = "FARVE:";
    document.getElementById("lblHighlight").innerHTML = "BAGGRUNDSFARVE:";
    document.getElementById("lblLineHeight").innerHTML = "LINJEAFSTAND:";
    document.getElementById("lblLetterSpacing").innerHTML = "TEGNAFSTAND:";
    document.getElementById("lblWordSpacing").innerHTML = "ORDAFSTAND:";
    document.getElementById("lblNote").innerHTML = "Denne funktion er ikke understøttet i IE.";
    document.getElementById("divShadowClear").innerHTML = "CLEAR";   
}
function writeTitle() {
    document.write("<title>" + "Tekst" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "DEFAULT SIZE": return "Standard str.";
        case "Heading 1": return "Overskrift 1";
        case "Heading 2": return "Overskrift 2";
        case "Heading 3": return "Overskrift 3";
        case "Heading 4": return "Overskrift 4";
        case "Heading 5": return "Overskrift 5";
        case "Heading 6": return "Overskrift 6";
        case "Preformatted": return "Præformateret";
        case "Normal": return "Normal";
        case "Google Font": return "GOOGLE FONTS:";
    }
}