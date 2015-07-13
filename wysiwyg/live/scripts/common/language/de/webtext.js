function loadTxt() {
    document.getElementById("tab0").innerHTML = "TEXT";
    document.getElementById("tab1").innerHTML = "SCHATTEN";
    document.getElementById("tab4").innerHTML = "ABSATZ";
    document.getElementById("tab5").innerHTML = "AUFZ&Auml;HLUNGEN";
    document.getElementById("tab2").innerHTML = "GR&Ouml;SSE";

    document.getElementById("lblColor").innerHTML = "FARBE:";
    document.getElementById("lblHighlight").innerHTML = "HERVORHEBEN:";
    document.getElementById("lblLineHeight").innerHTML = "ZEILENH&Ouml;HE:";
    document.getElementById("lblLetterSpacing").innerHTML = "ZEICHENABSTAND:";
    document.getElementById("lblWordSpacing").innerHTML = "WORTABSTAND:";
    document.getElementById("lblNote").innerHTML = "Dieses Feature wird zur Zeit nicht unterst&uuml;tzt im IE.";
    document.getElementById("divShadowClear").innerHTML = "CLEAR";   
}
function writeTitle() {
    document.write("<title>" + "Text" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "DEFAULT SIZE": return "NORMALE GR&Ouml;SSE";
        case "Heading 1": return "&Uuml;berschrift 1";
        case "Heading 2": return "&Uuml;berschrift 2";
        case "Heading 3": return "&Uuml;berschrift 3";
        case "Heading 4": return "&Uuml;berschrift 4";
        case "Heading 5": return "&Uuml;berschrift 5";
        case "Heading 6": return "&Uuml;berschrift 6";
        case "Preformatted": return "Vorformatiert";
        case "Normal": return "Normal";
    }
}