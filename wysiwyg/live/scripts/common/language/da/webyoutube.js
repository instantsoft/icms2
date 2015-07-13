function loadTxt() {
    document.getElementById("tab0").innerHTML = "YOUTUBE";
    document.getElementById("tab1").innerHTML = "FORMAT";
    document.getElementById("tab2").innerHTML = "STØRRELSE";
    document.getElementById("lnkLoadMore").innerHTML = "Indlæs flere";
    document.getElementById("lblUrl").innerHTML = "Link:";
    document.getElementById("btnCancel").value = "annuller";
    document.getElementById("btnInsert").value = "indsæt";
    document.getElementById("btnSearch").value = " Søg ";    
}
function writeTitle() {
    document.write("<title>" + "YouTube video" + "</title>")
}