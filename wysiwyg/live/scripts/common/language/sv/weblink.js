function loadTxt() {
    document.getElementById("lblProtocol").innerHTML= "PROTOCOL:";
    
    document.getElementById("tab0").innerHTML = "MINA FILER";
    document.getElementById("tab1").innerHTML = "STILAR";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("lblName").innerHTML = "NAME:";
    document.getElementById("lblTitle").innerHTML = "TITEL:";
    document.getElementById("lblTarget1").innerHTML = "Öppna från sida";
    document.getElementById("lblTarget2").innerHTML = "Öppna i nytt fönster";
    document.getElementById("lblTarget3").innerHTML = "Öppna i en Lightbox";
    document.getElementById("lnkNormalLink").innerHTML = "Normal Länk &raquo;";
    document.getElementById("btnCancel").value = "Stäng";
}
function writeTitle() {
    document.write("<title>" + "Länk" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "Infoga";
        case "change": return "OK";
    }
}