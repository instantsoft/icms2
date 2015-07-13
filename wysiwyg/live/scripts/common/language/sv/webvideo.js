function loadTxt() {
    document.getElementById("tab0").innerHTML = "POSTER";
    document.getElementById("tab1").innerHTML = "MPEG4 VIDEO";
    document.getElementById("tab2").innerHTML = "Ogg VIDEO";
    document.getElementById("tab3").innerHTML = "WebM VIDEO";
    document.getElementById("lbImage").innerHTML = "Poster/preview image (.png or .jpg):";
    document.getElementById("lblMP4").innerHTML = "MPEG4 video (.mp4):";
    document.getElementById("lblOgg").innerHTML = "Ogg video (.ogv):";
    document.getElementById("lblWebM").innerHTML = "WebM video (.webm):";
    document.getElementById("lblDimension").innerHTML = "Ange videostorlek (bredd x höjd):";
    document.getElementById("divNote1").innerHTML = "För info om HTML5 video se: <a href='http://www.w3schools.com/html5/html5_video.asp' target='_blank'>www.w3schools.com/html5/html5_video.asp</a>." +
        "Det finns 3 videoformat som stöds: MP4, WebM (för MSIE 9+), och Ogg (för FireFox). Webbläsaren använder det första format den känner igen." +
        "Also, you will need a preview or 'poster' image.";
    document.getElementById("divNote2").innerHTML = "För att konvertera en video till HTML5 video (MP4, WebM & Ogg) kan du använda: <a href='http://www.mirovideoconverter.com/' target='_blank'>www.mirovideoconverter.com</a>";

    document.getElementById("btnCancel").value = "Stäng";
    document.getElementById("btnInsert").value = "Infoga";
}
function writeTitle() {
    document.write("<title>" + "HTML5 Video" + "</title>")
}