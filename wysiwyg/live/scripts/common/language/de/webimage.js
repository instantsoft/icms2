function loadTxt() {
    document.getElementById("tab0").innerHTML = "FLICKR";
    document.getElementById("tab1").innerHTML = "BILDER";
    document.getElementById("tab2").innerHTML = "STILE";
    document.getElementById("tab3").innerHTML = "EFFECTS";
    document.getElementById("lblTag").innerHTML = "TAG:";
    document.getElementById("lblFlickrUserName").innerHTML = "Flickr Benutzername:";
    document.getElementById("lnkLoadMore").innerHTML = "Mehr laden";
    document.getElementById("lblImgSrc").innerHTML = "BILDQUELLE:";
    document.getElementById("lblWidthHeight").innerHTML = "BREITE x H&Ouml;HE:";
    
    var optAlign = document.getElementsByName("optAlign");
    optAlign[0].text = ""
    optAlign[1].text = "Links"
    optAlign[2].text = "Rechts"

    document.getElementById("lblTitle").innerHTML = "TITEL:";
    document.getElementById("lblAlign").innerHTML = "AUSRICHTUNG:";
    document.getElementById("lblMargin").innerHTML = "MARGIN: (TOP / RIGHT / BOTTOM / LEFT)";
    document.getElementById("lblSize1").innerHTML = "KLEINES RECHTECK";
    document.getElementById("lblSize2").innerHTML = "VORSCHAUBILD";
    document.getElementById("lblSize3").innerHTML = "KLEIN";
    document.getElementById("lblSize5").innerHTML = "MITTEL";
    document.getElementById("lblSize6").innerHTML = "GROSS";

    document.getElementById("lblOpenLarger").innerHTML = "GROSSES BILD IN LIGHTBOX &Ouml;FFNEN, ODER";
    document.getElementById("lblLinkToUrl").innerHTML = "LINK ZU URL:";
    document.getElementById("lblNewWindow").innerHTML = "IN NEUEM FENSTER &Ouml;FFNEN.";
    document.getElementById("btnCancel").value = "abbrechen";
    document.getElementById("btnSearch").value = " Suche ";

    document.getElementById("lblMaintainRatio").innerHTML = "MAINTAIN RATIO";
    document.getElementById("resetdimension").innerHTML = "RESET DIMENSION";
    
    document.getElementById("btnRestore").value = "Original Image";
    document.getElementById("btnSaveAsNew").value = "Save As New Image"; 
}
function writeTitle() {
    document.write("<title>" + "Bild" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "einf\u00fcgen";
        case "change": return "wechseln";
        case "notsupported": return "External image is not supported.";
    }
}