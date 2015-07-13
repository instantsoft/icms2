function loadTxt() {
    document.getElementById("tab0").innerHTML = "POSTER";
    document.getElementById("tab1").innerHTML = "MPEG4 VIDEO";
    document.getElementById("tab2").innerHTML = "Ogg VIDEO";
    document.getElementById("tab3").innerHTML = "WebM VIDEO";
    document.getElementById("lbImage").innerHTML = "Poster/preview image (.png or .jpg):";
    document.getElementById("lblMP4").innerHTML = "MPEG4 video (.mp4):";
    document.getElementById("lblOgg").innerHTML = "Ogg video (.ogv):";
    document.getElementById("lblWebM").innerHTML = "WebM video (.webm):";
    document.getElementById("lblDimension").innerHTML = "Enter video size (width x height):";
    document.getElementById("divNote1").innerHTML = "For info on HTML5 video see: <a href='http://www.w3schools.com/html5/html5_video.asp' target='_blank'>www.w3schools.com/html5/html5_video.asp</a>." +
        "There are 3 supported video sources: MP4, WebM (e.g. for MSIE 9+), and Ogg (e.g. for FireFox). The browser will use the first recognized format." +
        "Also, you will need a preview or 'poster' image.";
    document.getElementById("divNote2").innerHTML = "To convert a video into HTML5 video (MP4, WebM & Ogg) you can use: <a href='http://www.mirovideoconverter.com/' target='_blank'>www.mirovideoconverter.com</a>";

    document.getElementById("btnCancel").value = "close";
    document.getElementById("btnInsert").value = "insert";
}
function writeTitle() {
    document.write("<title>" + "HTML5 Video" + "</title>")
}