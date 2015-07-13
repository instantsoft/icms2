function loadTxt() {
    document.getElementById("lblSearch").innerHTML = "RECHERCHER:";
    document.getElementById("lblReplace").innerHTML = "REMPLACER:";
    document.getElementById("lblMatchCase").innerHTML = "Respecter la casse";
    document.getElementById("lblMatchWhole").innerHTML = "Mot entier seulement";

    document.getElementById("btnSearch").value = "rechercher"; ;
    document.getElementById("btnReplace").value = "remplacer";
    document.getElementById("btnReplaceAll").value = "tout remplacer";
}
function getTxt(s) {
    switch (s) {
        case "Finished searching": return "La recherche a atteint la fin du document.\nSouhaitez-vous reprendre la recherche depuis le d\u00E9but?";
        default: return "";
    }
}
function writeTitle() {
    document.write("<title>Rechercher & Remplacer</title>")
}