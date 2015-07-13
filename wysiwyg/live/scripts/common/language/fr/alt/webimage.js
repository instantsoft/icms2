function loadTxt() {
    document.getElementById("tab0").innerHTML = "FLICKR";
    document.getElementById("tab1").innerHTML = "MES FICHIERS";
    document.getElementById("tab2").innerHTML = "STYLES";
    document.getElementById("tab3").innerHTML = "EFFECTS";
    document.getElementById("lblTag").innerHTML = "TAG:";
    document.getElementById("lblFlickrUserName").innerHTML = "Nom Flickr:";
    document.getElementById("lnkLoadMore").innerHTML = "Charger plus...";
    document.getElementById("lblImgSrc").innerHTML = "SOURCE IMAGE:";
  document.getElementById("lblWidthHeight").innerHTML = "LARGEUR x HAUTEUR:";

    var optAlign = document.getElementsByName("optAlign");
    optAlign[0].text = ""
    optAlign[1].text = "Gauche"
    optAlign[2].text = "Droite"

    document.getElementById("lblTitle").innerHTML = "TITRE:";
    document.getElementById("lblAlign").innerHTML = "ALIGNEMENT:";
    document.getElementById("lblMargin").innerHTML = "MARGIN: (TOP / RIGHT / BOTTOM / LEFT)";
    document.getElementById("lblSize1").innerHTML = "PETIT CARRÉ";
    document.getElementById("lblSize2").innerHTML = "ICONE";
    document.getElementById("lblSize3").innerHTML = "PETIT";
    document.getElementById("lblSize5").innerHTML = "MOYEN";
    document.getElementById("lblSize6").innerHTML = "GRAND";

    document.getElementById("lblOpenLarger").innerHTML = "OUVRIR PLUS GRAND DANS FENÊTRE, OU";
    document.getElementById("lblLinkToUrl").innerHTML = "LIEN VERS URL:";
    document.getElementById("lblNewWindow").innerHTML = "OUVRIR DANS NOUVELLE FENÊTRE.";
    document.getElementById("btnCancel").value = "Annuler";

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
        case "insert": return "Insérer";
        case "change": return "Modifier";
        case "notsupported": return "External image is not supported.";
    }
}