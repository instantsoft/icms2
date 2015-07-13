function loadTxt() {
    document.getElementById("lblSearch").innerHTML = "SØG:";
    document.getElementById("lblReplace").innerHTML = "ERSTAT:";
    document.getElementById("lblMatchCase").innerHTML = "Forskel på store og små bogstaver";
    document.getElementById("lblMatchWhole").innerHTML = "Kun hele ord";

    document.getElementById("btnSearch").value = "find næste"; ;
    document.getElementById("btnReplace").value = "erstat";
    document.getElementById("btnReplaceAll").value = "erstat alle";
}
function getTxt(s) {
    switch (s) {
        case "Søgningen gennemført": return "Færdig med at søge i dokumentet.\nSøg igen fra toppen?";
        default: return "";
    }
}
function writeTitle() {
    document.write("<title>Søg & erstat</title>")
}