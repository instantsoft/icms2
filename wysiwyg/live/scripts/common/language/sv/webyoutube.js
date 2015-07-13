function loadTxt() {
    document.getElementById("tab0").innerHTML = "YOUTUBE";
    document.getElementById("tab1").innerHTML = "STYLES";
    document.getElementById("tab2").innerHTML = "DIMENSION";
    document.getElementById("lnkLoadMore").innerHTML = "Load More";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("btnCancel").value = "close";
    document.getElementById("btnInsert").value = "insert";
    document.getElementById("btnSearch").value = " Search ";    
}
function writeTitle() {
    document.write("<title>" + "Youtube Video" + "</title>")
}