function loadTxt() {
    document.getElementById("tab0").innerHTML = "Рисунок";
    document.getElementById("tab1").innerHTML = "Настройки";
    document.getElementById("tab3").innerHTML = "Сохранено";

    document.getElementById("lblWidthHeight").innerHTML = "Размер полотна:";
    
    var optAlign = document.getElementsByName("optAlign");
    optAlign[0].text = ""
    optAlign[1].text = "Слева"
    optAlign[2].text = "Справа"

    document.getElementById("lblTitle").innerHTML = "Заголовок:";
    document.getElementById("lblAlign").innerHTML = "Выравнивание:";
    document.getElementById("lblSpacing").innerHTML = "Верт.отступ:";
    document.getElementById("lblSpacingH").innerHTML = "Гор.отступ:";

    document.getElementById("btnCancel").value = "Закрыть";
}
function writeTitle() {
    document.write("<title>" + "Рисунок" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "Вставить";
        case "change": return "Ок";
        case "DELETE": return "Удалить";
    }
}
