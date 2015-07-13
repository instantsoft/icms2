function loadTxt() {
    document.getElementById("lblSearch").innerHTML = "Suche:";
    document.getElementById("lblReplace").innerHTML = "Ersetzen:";
    document.getElementById("lblMatchCase").innerHTML = "Gro&szlig;-/Kleinschreibung";
    document.getElementById("lblMatchWhole").innerHTML = "Nur ganze W&ouml;rter suchen";

    document.getElementById("btnSearch").value = "Weitersuchen";
    document.getElementById("btnReplace").value = "Ersetzen";
    document.getElementById("btnReplaceAll").value = "Alle ersetzen";
}
function getTxt(s) {
    switch (s) {
        case "Finished searching": return "Suche abgeschlossen.\Erneut suchen von oben?";
        default: return "";
    }
}
function writeTitle() {
    document.write("<title>Suchen &amp; Ersetzen</title>")
}