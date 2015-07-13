function loadTxt() {
    document.getElementById("tab0").innerHTML = "FLICKR";
    document.getElementById("tab1").innerHTML = "MINA FILER";
    document.getElementById("tab2").innerHTML = "STILAR";
    document.getElementById("tab3").innerHTML = "EFFEKTER";
    document.getElementById("lblTag").innerHTML = "TAGG:";
    document.getElementById("lblFlickrUserName").innerHTML = "Flickr Användarnamn:";
    document.getElementById("lnkLoadMore").innerHTML = "Ladda fler";
    document.getElementById("lblImgSrc").innerHTML = "BILDKÄLLA:";
    document.getElementById("lblWidthHeight").innerHTML = "BREDD x HÖJD:";
    
    var optAlign = document.getElementsByName("optAlign");
    optAlign[0].text = ""
    optAlign[1].text = "Vänster"
    optAlign[2].text = "Höger"

    document.getElementById("lblTitle").innerHTML = "TITEL:";
    document.getElementById("lblAlign").innerHTML = "JUSTERING:";
    document.getElementById("lblMargin").innerHTML = "MARGIN: (TOP / RIGHT / BOTTOM / LEFT)";
    document.getElementById("lblSize1").innerHTML = "LITEN RUTA";
    document.getElementById("lblSize2").innerHTML = "THUMBNAIL";
    document.getElementById("lblSize3").innerHTML = "LITEN";
    document.getElementById("lblSize5").innerHTML = "MEDIUM";
    document.getElementById("lblSize6").innerHTML = "LARGE";

    document.getElementById("lblOpenLarger").innerHTML = "ÖPPNA STÖRRE BILD I LIGHTBOX, ELLER";
    document.getElementById("lblLinkToUrl").innerHTML = "LÄNKA TILL URL:";
    document.getElementById("lblNewWindow").innerHTML = "ÖPPNA I NYTT FÖNSTER.";
    document.getElementById("btnCancel").value = "Stäng";
    document.getElementById("btnSearch").value = " Sök ";

    document.getElementById("lblMaintainRatio").innerHTML = "MAINTAIN RATIO";
    document.getElementById("resetdimension").innerHTML = "RESET DIMENSION";
    
    document.getElementById("btnRestore").value = "Originalbild";
    document.getElementById("btnSaveAsNew").value = "Spara som ny bild"; 
}
function writeTitle() {
    document.write("<title>" + "Bild" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "Infoga";
    case "cancel": return "Avbryt";
        case "change": return "OK";
        case "notsupported": return "Extern bild stöds inte.";
    }
}