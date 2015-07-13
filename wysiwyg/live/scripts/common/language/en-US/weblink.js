function loadTxt() {
    document.getElementById("lblProtocol").innerHTML= "PROTOCOL:";
    
    document.getElementById("tab0").innerHTML = "MY FILES";
    document.getElementById("tab1").innerHTML = "STYLES";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("lblName").innerHTML = "NAME:";
    document.getElementById("lblTitle").innerHTML = "TITLE:";
    document.getElementById("lblTarget1").innerHTML = "Open in Page";
    document.getElementById("lblTarget2").innerHTML = "Open in a New Window";
    document.getElementById("lblTarget3").innerHTML = "Open in a Lightbox";
    document.getElementById("lnkNormalLink").innerHTML = "Normal Link &raquo;";    
    document.getElementById("btnCancel").value = "close";
    
}
function writeTitle() {
    document.write("<title>" + "Link" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "insert";
        case "change": return "ok";
    }
}