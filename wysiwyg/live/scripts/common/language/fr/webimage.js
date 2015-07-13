function loadTxt() {
    document.getElementById("tab0").innerHTML = "FLICKR";
    document.getElementById("tab1").innerHTML = "MES FICHIERS";
    document.getElementById("tab2").innerHTML = "STYLES";
    document.getElementById("tab3").innerHTML = "EFFECTS";
    document.getElementById("lblTag").innerHTML = "TAG:";
    document.getElementById("lblFlickrUserName").innerHTML = "Utilisateur Flickr:";
    document.getElementById("lnkLoadMore").innerHTML = "En charger d'avantage";
    document.getElementById("lblImgSrc").innerHTML = "SOURCE DE L'IMAGE:";
    document.getElementById("lblWidthHeight").innerHTML = "LARGEUR x HAUTEUR:";
    
    var optAlign = document.getElementsByName("optAlign");
    optAlign[0].text = ""
    optAlign[1].text = "Gauche"
    optAlign[2].text = "Droite"

    document.getElementById("lblTitle").innerHTML = "TITRE:";
    document.getElementById("lblAlign").innerHTML = "ALIGNER:";
    document.getElementById("lblMargin").innerHTML = "MARGIN: (TOP / RIGHT / BOTTOM / LEFT)";
    document.getElementById("lblSize1").innerHTML = "PETIT CARRE";
    document.getElementById("lblSize2").innerHTML = "ICONNE";
    document.getElementById("lblSize3").innerHTML = "PETIT";
    document.getElementById("lblSize5").innerHTML = "MEDIUM";
    document.getElementById("lblSize6").innerHTML = "GRAND";

    document.getElementById("lblOpenLarger").innerHTML = "OUVRIR UNE IMAGE PLUS GRANDE DANS UN POPUP, OU";
    document.getElementById("lblLinkToUrl").innerHTML = "LIEN VERS L'URL:";
    document.getElementById("lblNewWindow").innerHTML = "OUVRIR DANS UNE NOUVELLE FENETRE.";
    document.getElementById("btnCancel").value = "annuler";
    document.getElementById("btnSearch").value = " Rechercher ";

    document.getElementById("lblMaintainRatio").innerHTML = "MAINTAIN RATIO";
    document.getElementById("resetdimension").innerHTML = "RESET DIMENSION";
    
    document.getElementById("btnRestore").value = "Original Image";
    document.getElementById("btnSaveAsNew").value = "Save As New Image";  
}
function writeTitle() {
    document.write("<title>" + "Image" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "ins\u00E9rer";
        case "change": return "changer";
        case "notsupported": return "External image is not supported.";
    }
}