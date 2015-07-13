function loadTxt() {
    document.getElementById("lblSearch").innerHTML = "SÖK:";
    document.getElementById("lblReplace").innerHTML = "ERSÄTT:";
    document.getElementById("lblMatchCase").innerHTML = "Matcha gemener/versaler";
    document.getElementById("lblMatchWhole").innerHTML = "Matcha hela ord";

    document.getElementById("btnSearch").value = "Sök nästa"; ;
    document.getElementById("btnReplace").value = "Ersätt";
    document.getElementById("btnReplaceAll").value = "Ersätt alla";
}
function getTxt(s) {
    switch (s) {
        case "Finished searching": return "Sökning i dokumentet klar.\nVill du söka igen från början?";
        default: return "";
    }
}
function writeTitle() {
    document.write("<title>Sök & Ersätt</title>")
}