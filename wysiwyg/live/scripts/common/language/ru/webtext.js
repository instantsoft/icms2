function loadTxt() {
    document.getElementById("tab0").innerHTML = "Текст";
    document.getElementById("tab1").innerHTML = "Тени";
    document.getElementById("tab2").innerHTML = "Параграфы";
    document.getElementById("tab3").innerHTML = "Списки";
    document.getElementById("tab4").innerHTML = "Размер";

    document.getElementById("lblColor").innerHTML = "Цвет:";
    document.getElementById("lblHighlight").innerHTML = "Фон:";
    document.getElementById("lblLineHeight").innerHTML = "Высота строки:";
    document.getElementById("lblLetterSpacing").innerHTML = "Межсимвольный интервал:";
    document.getElementById("lblWordSpacing").innerHTML = "Интервал между словами:";
    document.getElementById("lblNote").innerHTML = "Эта возможность недоступна в IE.";
    document.getElementById("divShadowClear").innerHTML = "Очистить";    
}
function writeTitle() {
    document.write("<title>" + "Text" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "DEFAULT SIZE": return "По-умолчанию";
        case "Heading 1": return "Заголовок 1";
        case "Heading 2": return "Заголовок 2";
        case "Heading 3": return "Заголовок 3";
        case "Heading 4": return "Заголовок 4";
        case "Heading 5": return "Заголовок 5";
        case "Heading 6": return "Заголовок 6";
        case "Preformatted": return "Код";
        case "Normal": return "Нормальный";
    }
}
