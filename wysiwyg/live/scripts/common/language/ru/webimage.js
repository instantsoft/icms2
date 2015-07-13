function loadTxt() {
    document.getElementById("tab0").innerHTML = "FLICKR";
    document.getElementById("tab1").innerHTML = "Мои файлы";
    document.getElementById("tab2").innerHTML = "Стили";
    document.getElementById("tab3").innerHTML = "Эффекты";
    document.getElementById("lblTag").innerHTML = "Тэг:";
    document.getElementById("lblFlickrUserName").innerHTML = "Пользователь Flickr:";
    document.getElementById("lnkLoadMore").innerHTML = "Загрузить еще";
    document.getElementById("lblImgSrc").innerHTML = "URL изображения:";
    document.getElementById("lblWidthHeight").innerHTML = "Ширина x Высота:";
    
    var optAlign = document.getElementsByName("optAlign");
    optAlign[0].text = ""
    optAlign[1].text = "Слева"
    optAlign[2].text = "Справа"

    document.getElementById("lblTitle").innerHTML = "Заголовок:";
    document.getElementById("lblAlign").innerHTML = "Выравнивание:";
    document.getElementById("lblMargin").innerHTML = "Отступ: (Верх / Право / Низ / Лево)";
    document.getElementById("lblSize1").innerHTML = "Маленький квадрат";
    document.getElementById("lblSize2").innerHTML = "Превью";
    document.getElementById("lblSize3").innerHTML = "Маленький";
    document.getElementById("lblSize5").innerHTML = "Средний";
    document.getElementById("lblSize6").innerHTML = "Большой";

    document.getElementById("lblOpenLarger").innerHTML = "Открыть больший размер в лайтбоксе, или";
    document.getElementById("lblLinkToUrl").innerHTML = "Ссылка URL:";
    document.getElementById("lblNewWindow").innerHTML = "Открывать в новом окне";
    document.getElementById("btnCancel").value = "Закрыть";
    document.getElementById("btnSearch").value = " Поиск ";

    document.getElementById("lblMaintainRatio").innerHTML = "Сохранять пропорции";
    document.getElementById("resetdimension").innerHTML = "Сбросить размеры";

    document.getElementById("btnRestore").value = "Вернуть оригинал";
    document.getElementById("btnSaveAsNew").value = "Сохранить как новый"; 
}
function writeTitle() {
    document.write("<title>" + "Изображение" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "Вставить";
        case "change": return "Ок";
        case "notsupported": return "Внешние изображения не поддерживаются";
    }
}
