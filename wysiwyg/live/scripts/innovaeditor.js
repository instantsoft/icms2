var oScripts=document.getElementsByTagName('script');
var sEditorPath;
for (var i = 0; i < oScripts.length; i++) {
    var sSrc = oScripts[i].src.toLowerCase();
    if (sSrc.indexOf('scripts/innovaeditor.js') !== -1) {
        sEditorPath = oScripts[i].src.replace(/innovaeditor.js/, "");
    }
}

function addJSTag(url){
    t = document.createElement('script');
    t.type = 'text/javascript';
    t.src = url;
    $('head').append(t);
}
function addCSSTag(url){
    t = document.createElement('link');
    t.type = 'text/css';
    t.href = url;
    t.rel  = 'stylesheet';
    $('head').append(t);
}

if(!window.innovaeditor_script_loaded){

    addJSTag(sEditorPath+'common/nlslightbox/nlslightbox.js');
    addJSTag(sEditorPath+'common/nlslightbox/nlsanimation.js');
    addJSTag(sEditorPath+'common/nlslightbox/dialog.js');
    addJSTag(sEditorPath+'istoolbar.js');

    var UA = navigator.userAgent.toLowerCase();
    var LiveEditor_isIE = (UA.indexOf('msie') >= 0) ? true : false;

    if (LiveEditor_isIE) {
        addJSTag(sEditorPath+'editor.js');
    } else if (UA.indexOf('safari') != -1) {
        addJSTag(sEditorPath+'saf/editor.js');
    } else { //ie11 use moz script now.
        addJSTag(sEditorPath+'moz/editor.js');
    }

    addCSSTag(sEditorPath+'common/nlslightbox/nlslightbox.css', 'link');
    addCSSTag(sEditorPath+'style/istoolbar.css', 'link');

}

window['innovaeditor_script_loaded'] = true;