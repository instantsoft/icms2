function loadTxt() {
    document.getElementById("lblSearch").innerHTML = "CHERCHE:";
    document.getElementById("lblReplace").innerHTML = "REMPLACE:";
    document.getElementById("lblMatchCase").innerHTML = "Sensible à la casse";
    document.getElementById("lblMatchWhole").innerHTML = "Mot entier";

    document.getElementById("btnSearch").value = "Suivant"; ;
    document.getElementById("btnReplace").value = "Remplace";
    document.getElementById("btnReplaceAll").value = "Rempl. tout";
}
function getTxt(s) {
    switch (s) {
        case "Recherche terminee": return "Fin du document.\nRechercher depuis le haut?";
        default: return "";
    }
}
function writeTitle() {
    document.write("<title>Cherche & Remplace</title>")
}