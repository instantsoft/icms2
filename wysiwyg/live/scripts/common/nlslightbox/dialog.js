var box = new NlsLightBox("mybox");
function icClose() { }
function modalDialog(url, width, height, title) {
    try {
        if (icClose != 'function icClose() { }') { icClose(); icClose = function () { } };
        var a = box.close;
        box.close = function () {
            try { icClose(); }
            catch (e) { } box.close = a; box.close();
        };
        icClose = addClose;
    }
    catch (e) { }
  
    var clean = function() {
      if(oUtil) oUtil.onSelectionChanged=null;
    };
    
    if (title)
        box.open({ url: url, type: 'iframe', width: width + 'px', height: height + 'px', title: title, adjY: '20px', overlay: false, parent:document.body, onClose:function() {clean();} });
    else
        box.open({ url: url, type: 'iframe', width: width + 'px', height: height + 'px', adjY: '20px', overlay: false, parent:document.body, onClose:function() {clean();} });
    if (navigator.appName.indexOf('Microsoft') == -1) {/* Selain IE. Sebenarnya hanya utk Ipad */
        document.getElementById('mybox$box_title').addEventListener("touchstart", touchHandler, true);
        document.getElementById('mybox$box_title').addEventListener("touchmove", touchHandler, true);
        document.getElementById('mybox$box_title').addEventListener("touchend", touchHandler, true);
    }
}

var myAsset = new NlsLightBox("myAsset");
function showMyAsset(url, width, height, title) {
    if (title)
        myAsset.open({ url: url, type: 'iframe', width: width + 'px', height: height + 'px', title: title, adjY: '20px', overlay: false, parent:document.body  });
    else
        myAsset.open({ url: url, type: 'iframe', width: width + 'px', height: height + 'px', adjY: '20px', overlay: false, parent:document.body  });
};

function touchHandler(event) {
    var touches = event.changedTouches,
            first = touches[0],
            type = "";

    switch (event.type) {
        case "touchstart": type = "mousedown"; break;
        case "touchmove": type = "mousemove"; break;
        case "touchend": type = "mouseup"; break;
        default: return;
    }
    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1,
                          first.screenX, first.screenY,
                          first.clientX, first.clientY, false,
                          false, false, false, 0/*left*/, null);

    first.target.dispatchEvent(simulatedEvent);
    event.preventDefault();
}