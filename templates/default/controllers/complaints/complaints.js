var loc = window.location;
var mis;
nN = navigator.appName; 

function getText(e){
        if (!e) e= window.event; 
        if((e.ctrlKey) && ((e.keyCode==10)||(e.keyCode==13))) 
        {CtrlEnter();} 
    return true;
}
    
function mis_get_sel_text(){
   if (window.getSelection) {
    txt = window.getSelection();
    selected_text = txt.toString();
    full_text = txt.anchorNode.textContent;
    selection_start = txt.anchorOffset;
    selection_end = txt.focusOffset;
  }
  else if (document.getSelection) {
    txt = document.getSelection();
    selected_text = txt.toString();
    full_text = txt.anchorNode.textContent;
    selection_start = txt.anchorOffset;
    selection_end = txt.focusOffset;
  }
  else if (document.selection) {
    txt = document.selection.createRange();
    selected_text = txt.text;
    full_text = txt.parentElement().innerText;

    var stored_range = txt.duplicate();
    stored_range.moveToElementText(txt.parentElement());
    stored_range.setEndPoint('EndToEnd', txt);
    selection_start = stored_range.text.length - txt.text.length;
    selection_end = selection_start + selected_text.length;
  } else { return; }
  
  var txt = {
    selected_text: selected_text,
    full_text: full_text,
    selection_start: selection_start,
    selection_end: selection_end
  };  
  return txt;
}

function mis_get_sel_context(sel) {
  selection_start = sel.selection_start;
  selection_end = sel.selection_end;
  if (selection_start > selection_end) {
    tmp = selection_start;
    selection_start = selection_end;
    selection_end = tmp;
  }
  context = sel.full_text; 

  context_first = context.substring(0, selection_start);
  context_second = context.substring(selection_start, selection_end);
  context_third = context.substring(selection_end, context.length);
  context = context_first + '[' + context_second + ']' + context_third;

  context_start = selection_start - 50;
  if (context_start < 0) {
    context_start = 0;
  }

  context_end = selection_end + 50;
  if (context_end > context.length) {
    context_end = context.length;
  }

  context = context.substring(context_start, context_end);

  context_start = context.indexOf(' ') + 1;

  if (selection_start + 50 < context.length) {
    context_end = context.lastIndexOf(' ', selection_start + 50);
  }
  else {
    context_end = context.length;
  }

  selection_start = context.indexOf('[');
  if (context_start > selection_start) {
    context_start = 0;
  }

  if (context_start) {
    context = context.substring(context_start, context_end);
  }
  return context;
}

function CtrlEnter(){
    var sel = mis_get_sel_text();
    if (sel.selected_text.length > 300) {
        
        icms.modal.openHtml('<div style="width: 300px;height: 70px;line-height: 70px; padding: 20px;color: red; text-align: center;">Выделен слишком большой участок текста.</div>');
            
    }
    else if (sel.selected_text.length == 0) {        
        
        return false; 
        
    } else {
        
    // Get selection context.
    mis = mis_get_sel_context(sel);  
    
    var complaint = {orfo: mis, url: window.location.href};

    icms.modal.openAjax('/complaints/orfo', complaint);    

    }
  };
document.onkeypress = getText;