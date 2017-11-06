function loadTxt() {
    document.getElementById("tab0").innerHTML = "Вставить";
    document.getElementById("tab1").innerHTML = "Изменить";
    document.getElementById("tab2").innerHTML = "Автоформат";
    document.getElementById("btnDelTable").value = "Удалить выбранную таблицу";
    document.getElementById("btnIRow1").value = "Новая строка (выше)";
    document.getElementById("btnIRow2").value = "Новая строка (ниже)";
    document.getElementById("btnICol1").value = "Новый столбец (слева)";
    document.getElementById("btnICol2").value = "Новый столбец (справа)";
    document.getElementById("btnDelRow").value = "Удалить строку";
    document.getElementById("btnDelCol").value = "Удалить столбец";
    document.getElementById("btnMerge").value = "Объединить ячейки";
    document.getElementById("lblFormat").innerHTML = "Формат:";
    document.getElementById("lblTable").innerHTML = "Таблица";
    document.getElementById("lblCell").innerHTML = "Ячейка";
    document.getElementById("lblEven").innerHTML = "Четные строки";
    document.getElementById("lblOdd").innerHTML = "Нечетные строки";
    document.getElementById("lblCurrRow").innerHTML = "Текущая строка";
    document.getElementById("lblCurrCol").innerHTML = "Текущий столбец";
    document.getElementById("lblBg").innerHTML = "Фон:";
    document.getElementById("lblText").innerHTML = "Текст:";
    document.getElementById("lblBorder").innerHTML = "Рамки:";
    document.getElementById("lblThickness").innerHTML = "Толщина:";
    document.getElementById("lblBorderColor").innerHTML = "Цвет:";
    document.getElementById("lblCellPadding").innerHTML = "Оступ в ячейках:";
    document.getElementById("lblFullWidth").innerHTML = "Полная ширина";
    document.getElementById("lblAutofit").innerHTML = "Авто";
    document.getElementById("lblFixedWidth").innerHTML = "Ширина:";
    document.getElementById("lnkClean").innerHTML = "Очистить";
    document.getElementById("lblTextAlign").innerHTML = "Выравнивание текста:";
    document.getElementById("btnAlignLeft").value = "Слева";
    document.getElementById("btnAlignCenter").value = "По центру";
    document.getElementById("btnAlignRight").value = "Справа";
    document.getElementById("btnAlignTop").value = "Сверху";
    document.getElementById("btnAlignMiddle").value = "По центру";
    document.getElementById("btnAlignBottom").value = "Снизу";

    document.getElementById("lblColor").innerHTML = "Цвет:";
    document.getElementById("lblCellSize").innerHTML = "Размер ячейки:";
    document.getElementById("lblCellWidth").innerHTML = "Ширина:";
    document.getElementById("lblCellHeight").innerHTML = "Высота:";
}
function writeTitle() {
    document.write("<title>" + "Таблица" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "Clean Formatting": return "Очистить форматирование";
    }
}
