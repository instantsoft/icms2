function loadTxt() {
    document.getElementById("lblProtocol").innerHTML= "Протокол:";
    
    document.getElementById("tab0").innerHTML = "Мои файлы";
    document.getElementById("tab1").innerHTML = "Стили";
    document.getElementById("lblUrl").innerHTML = "URL:";
    document.getElementById("lblName").innerHTML = "Имя:";
    document.getElementById("lblTitle").innerHTML = "Заголовок:";
    document.getElementById("lblTarget1").innerHTML = "Открывать в текущем окне";
    document.getElementById("lblTarget2").innerHTML = "Открывать в новом окне";
    document.getElementById("lblTarget3").innerHTML = "Открывать в лайтбоксе";
    document.getElementById("lnkNormalLink").innerHTML = "Обычная ссылка &raquo;";    
    document.getElementById("btnCancel").value = "Закрыть";
    
}
function writeTitle() {
    document.write("<title>" + "Ссылка" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "insert": return "Вставить";
        case "change": return "Ок";
    }
}
