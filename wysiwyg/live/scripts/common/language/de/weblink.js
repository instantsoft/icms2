function loadTxt() {
    document.getElementById("lblProtocol").innerHTML= "PROTOCOL:";
    
    document.getElementById("tab0").innerHTML = "ASSETS";
    document.getElementById("tab1").innerHTML = "STILE";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("lblName").innerHTML = "NAME:";
    document.getElementById("lblTitle").innerHTML = "TITEL:";
    document.getElementById("lblTarget1").innerHTML = "&Ouml;ffnen in dieser Seite";   
    document.getElementById("lblTarget2").innerHTML = "&Ouml;ffnen in neuem Fenster";
    document.getElementById("lblTarget3").innerHTML = "&Ouml;ffnen in Lightbox";
    document.getElementById("lnkNormalLink").innerHTML = "Normaler Link &raquo;";
    document.getElementById("btnCancel").value = "abbrechen";
}
function writeTitle() {
    document.write("<title>" + "Link" + "</title>")
}
function getTxt(s) {
    switch (s) {
    case "insert": return "einf\u00fcgen";
        case "change": return "wechseln";
    }
}