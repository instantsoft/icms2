function loadTxt() {
    document.getElementById("lblProtocol").innerHTML= "PROTOCOL:";
    
    document.getElementById("tab0").innerHTML = "MINE FILER";
    document.getElementById("tab1").innerHTML = "FORMAT";
    document.getElementById("lblUrl").innerHTML = "Link:";
    document.getElementById("lblName").innerHTML = "NAME:";    
    document.getElementById("lblTitle").innerHTML = "TITEL:";
    document.getElementById("lblTarget1").innerHTML = "Åben i samme vindue";
    document.getElementById("lblTarget2").innerHTML = "Åben i nyt vindue";
    document.getElementById("lblTarget3").innerHTML = "Åben i en Lightbox";
    document.getElementById("lnkNormalLink").innerHTML = "Normaltlink &raquo;";
    document.getElementById("btnCancel").value = "annuller";
}
function writeTitle() {
    document.write("<title>" + "Link" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "indsæt";
        case "change": return "gem ændringer";
    }
}