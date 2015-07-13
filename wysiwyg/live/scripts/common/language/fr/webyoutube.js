function loadTxt() {
    document.getElementById("tab0").innerHTML = "YOUTUBE";
    document.getElementById("tab1").innerHTML = "STYLES";
    document.getElementById("tab2").innerHTML = "DIMENSION";
    document.getElementById("lnkLoadMore").innerHTML = "En charger d'avantage";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("btnCancel").value = "annuler";
    document.getElementById("btnInsert").value = "ins\u00E9rer";
    document.getElementById("btnSearch").value = " Rechercher ";    
}
function writeTitle() {
    document.write("<title>" + "Vidéos Youtube" + "</title>")
}