function loadTxt() {
    document.getElementById("lblProtocol").innerHTML= "PROTOCOL:";
    
    document.getElementById("tab0").innerHTML = "MES FICHIERS";
    document.getElementById("tab1").innerHTML = "STYLES";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("lblName").innerHTML = "NAME:";
    document.getElementById("lblTitle").innerHTML = "TITRE:";
    document.getElementById("lblTarget1").innerHTML = "Ouvrir dans la page";
    document.getElementById("lblTarget2").innerHTML = "Ouvrir dans une nouvelle fenetre";
    document.getElementById("lblTarget3").innerHTML = "Ouvrir dans une \"Lightbox\"";
    document.getElementById("lnkNormalLink").innerHTML = "Lien normal &raquo;";
    document.getElementById("btnCancel").value = "annuler";
}
function writeTitle() {
    document.write("<title>" + "Lien" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "ins\u00E9rer";
        case "change": return "changer";
    }
}