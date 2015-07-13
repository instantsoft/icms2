function loadTxt() {
    document.getElementById("lblSearch").innerHTML = "SEARCH:";
    document.getElementById("lblReplace").innerHTML = "REPLACE:";
    document.getElementById("lblMatchCase").innerHTML = "Match case";
    document.getElementById("lblMatchWhole").innerHTML = "Match whole word";

    document.getElementById("btnSearch").value = "search next"; ;
    document.getElementById("btnReplace").value = "replace";
    document.getElementById("btnReplaceAll").value = "replace all";
}
function getTxt(s) {
    switch (s) {
        case "Finished searching": return "Finished searching the document.\nSearch again from the top?";
        default: return "";
    }
}
function writeTitle() {
    document.write("<title>Search & Replace</title>")
}