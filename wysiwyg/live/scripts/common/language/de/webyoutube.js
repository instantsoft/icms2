function loadTxt() {
    document.getElementById("tab0").innerHTML = "YOUTUBE";
    document.getElementById("tab1").innerHTML = "STILE";
    document.getElementById("tab2").innerHTML = "DIMENSIONEN";
    document.getElementById("lnkLoadMore").innerHTML = "Mehr Laden";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("btnCancel").value = "abbrechen";
    document.getElementById("btnInsert").value = "einf\u00fcgen";
    document.getElementById("btnSearch").value = " Suchen ";    
}
function writeTitle() {
    document.write("<title>" + "Youtube Video" + "</title>")
}