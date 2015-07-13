function loadTxt() {
    document.getElementById("tab0").innerHTML = "Постер";
    document.getElementById("tab1").innerHTML = "Видео MPEG4";
    document.getElementById("tab2").innerHTML = "Видео Ogg";
    document.getElementById("tab3").innerHTML = "Видео WebM";
    document.getElementById("lbImage").innerHTML = "Постер/превью (.png или .jpg):";
    document.getElementById("lblMP4").innerHTML = "Видео MPEG4 (.mp4):";
    document.getElementById("lblOgg").innerHTML = "Видео Ogg (.ogv):";
    document.getElementById("lblWebM").innerHTML = "Видео WebM (.webm):";
    document.getElementById("lblDimension").innerHTML = "Размер видео (ширина x высота):";
    document.getElementById("divNote1").innerHTML = "Подробнее о HTML5 видео: <a href='http://www.w3schools.com/html5/html5_video.asp' target='_blank'>www.w3schools.com/html5/html5_video.asp</a>." +
        "Поддерживаются три формата видео: MP4, WebM (для MSIE 9+), и Ogg (для FireFox). Каждый браузер будет использовать поддерживаемый им формат." +
        "Также необходимо изображение превью (постер).";
    document.getElementById("divNote2").innerHTML = "Чтобы преобразовать видео в HTML5-совместимый формат (MP4, WebM & Ogg) вы можете использовать: <a href='http://www.mirovideoconverter.com/' target='_blank'>www.mirovideoconverter.com</a>";

    document.getElementById("btnCancel").value = "Закрыть";
    document.getElementById("btnInsert").value = "Вставить";
}
function writeTitle() {
    document.write("<title>" + "HTML5 видео" + "</title>")
}
