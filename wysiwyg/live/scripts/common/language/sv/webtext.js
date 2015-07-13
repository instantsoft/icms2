function loadTxt() {
    document.getElementById("tab0").innerHTML = "TEXT";
    document.getElementById("tab1").innerHTML = "EFFEKT";
    document.getElementById("tab2").innerHTML = "RUBRIK";
    document.getElementById("tab3").innerHTML = "LISTNING";
    document.getElementById("tab4").innerHTML = "Storlek";

    document.getElementById("lblColor").innerHTML = "FÄRG:";
    document.getElementById("lblHighlight").innerHTML = "HIGHLIGHT:";
    document.getElementById("lblLineHeight").innerHTML = "RADHÖJD:";
    document.getElementById("lblLetterSpacing").innerHTML = "TECKENMELLANRUM:";
    document.getElementById("lblWordSpacing").innerHTML = "ORDMELLANRUM:";
    document.getElementById("lblNote").innerHTML = "Denna funktion stöds för närvarande inte av MSIE.";
    document.getElementById("divShadowClear").innerHTML = "RENSA";    
}
function writeTitle() {
    document.write("<title>" + "Textformatering" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "DEFAULT SIZE": return "STANDARD STORLEK";
        case "Heading 1": return "Rubrik 1";
        case "Heading 2": return "Rubrik 2";
        case "Heading 3": return "Rubrik 3";
        case "Heading 4": return "Rubrik 4";
        case "Heading 5": return "Rubrik 5";
        case "Heading 6": return "Rubrik 6";
        case "Preformatted": return "Förformaterad";
        case "Normal": return "Normal";
    }
}