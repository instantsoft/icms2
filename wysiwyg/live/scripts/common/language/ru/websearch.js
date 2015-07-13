function loadTxt() {
    document.getElementById("lblSearch").innerHTML = "Найти:";
    document.getElementById("lblReplace").innerHTML = "Заменить:";
    document.getElementById("lblMatchCase").innerHTML = "Учитывать регистр";
    document.getElementById("lblMatchWhole").innerHTML = "Совпадение целиком";

    document.getElementById("btnSearch").value = "Найти далее"; ;
    document.getElementById("btnReplace").value = "Заменить";
    document.getElementById("btnReplaceAll").value = "Заменить все";
}
function getTxt(s) {
    switch (s) {
        case "Finished searching": return "Достигнут конец документ.\nПродолжить поиск с начала?";
        default: return "";
    }
}
function writeTitle() {
    document.write("<title>Найти и заменить</title>")
}
