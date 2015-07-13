function loadTxt() {
    document.getElementById("lblProtocol").innerHTML= "PROTOCOL:";
    
    document.getElementById("tab0").innerHTML = "LIEN";
    document.getElementById("tab1").innerHTML = "STYLES";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("lblTitle").innerHTML = "TITRE:";
    document.getElementById("lblTarget1").innerHTML = "Ouvre dans la page actuelle";
    document.getElementById("lblTarget2").innerHTML = "Ouvre dans une nouvelle fenêtre";
    document.getElementById("lblTarget3").innerHTML = "Ouvre dans une LightBox";
    document.getElementById("lnkNormalLink").innerHTML = "Style Standard";
    document.getElementById("btnCancel").value = "Annuler"; 
}
function writeTitle() {
    document.write("<title>" + "Liens" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "Insérer";
        case "change": return "Modifier";
    }
}