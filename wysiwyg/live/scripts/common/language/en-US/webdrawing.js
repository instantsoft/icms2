function loadTxt() {
    document.getElementById("tab0").innerHTML = "DRAWING";
    document.getElementById("tab1").innerHTML = "SETTINGS";
    document.getElementById("tab3").innerHTML = "SAVED";

    document.getElementById("lblWidthHeight").innerHTML = "CANVAS SIZE:";
    
    var optAlign = document.getElementsByName("optAlign");
    optAlign[0].text = ""
    optAlign[1].text = "Left"
    optAlign[2].text = "Right"

    document.getElementById("lblTitle").innerHTML = "TITLE:";
    document.getElementById("lblAlign").innerHTML = "ALIGN:";
    document.getElementById("lblSpacing").innerHTML = "V-SPACING:";
    document.getElementById("lblSpacingH").innerHTML = "H-SPACING:";

    document.getElementById("btnCancel").value = "close";
}
function writeTitle() {
    document.write("<title>" + "Drawing" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "insert";
        case "change": return "ok";
        case "DELETE": return "DELETE";
    }
}