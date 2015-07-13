/***********************************************************
InnovaStudio WYSIWYG Editor 5.5
© 2010, InnovaStudio (www.innovastudio.com). All rights reserved.
************************************************************/
var UA = navigator.userAgent.toLowerCase();
var isIE = (UA.indexOf('msie') >= 0) ? true : false;
var isNS = (UA.indexOf('mozilla') >= 0) ? true : false;
var isIE7 = (UA.indexOf('msie 7.0') >= 0) ? true : false;

function ISToolbarManager(id) {
  this.id=id;
  this.btns=new Object();
  this.tbars=new Object();
  this.groups=new Object();
  this.tabCtl=null;
  this.iconPath = "";  
  
  this.createToolbar=function(id){
    var tb=new ISToolbar(id);
    tb.mgr=this;
    this.tbars[id]=tb;
    return tb;
  }
  
  this.createTbGroup=function(id){
    var gr=new ISToolbarGroup(id);
    gr.mgr=this;
    this.groups[id]=gr;
    return gr;
  }
  
  this.createTbTab=function(id) {
    var tab=new ISTabCtl(id);
    tab.mgr=this;
    this.tabCtl=tab;
    return tab;
  }

  this.render = function () {
      if (this.tabCtl) return this.tabCtl.render();

      var ret = "", flow = false;
      for (var i in this.groups) {
          if (this.groups[i].render) ret += this.groups[i].render();
          if (this.groups[i].groupFlow) flow = true;
      }

      if (this.groups[i].draggable) {
          if (ret != "") return "<div class=\"istoolbar_handler\"><div style=\"cursor:auto\">" + ret + "</div></div>";
      } else {
          if (ret != "") return "<div class=\"istoolbar_container\" style=\"" + (flow ? "position:relative;" : "") + "padding:3px 5px 3px 2px;background-image:url(" + this.iconPath + "grpbg.gif)\">" + ret + "</div>";
      }

      for (var i in this.tbars) { if (this.tbars[i].render) ret += this.tbars[i].render(); }
      if (ret != "") return ret;
      return "";
  }
}

/*------------------*/
/* Toolbar */
/*------------------*/
var isTbars=new Object();

function ISToolbar(tId) {
  this.id=tId;
  this.mgr=null;
  this.btns=new Object();
  
  this.btnHeight=25;
  this.btnWidth=23;
  
  this.iconPath="icons/";
  
  this.floating=false;
  
  this.rt=new Object();
  this.rt.sepCnt=0;
  this.rt.brkCnt=0;
  
  this.style={toolbar:"istoolbar"};
  
  isTbars[tId]=this;
  
  return this;
};

var ISTbar=ISToolbar.prototype;

ISTbar.add=function(btn) {
  btn.container=this;
  if(!btn.width)btn.width=this.btnWidth;
  if(!btn.height)btn.height=this.btnHeight;
  this.btns[btn.id]=btn;
  if(this.mgr)this.mgr.btns[btn.id]=btn;
};

ISTbar.addButton=function(id, icon, text, width, height) {
  this.add(new ISButton(id, icon, text, width, height));
};

ISTbar.addToggleButton=function(id, group, checked, icon, text, width, height) {
  this.add(new ISToggleButton(id, group, checked, icon, text, width, height));
}

ISTbar.addDropdownButton=function(id, ddId, icon, text, width, height){
  this.add(new ISDropdownButton(id, ddId, icon, text, width, height));
};

ISTbar.addSeparator=function(icon) {
  var s=new ISSeparator((!icon || icon==""?"brkspace.gif":icon));
  s.id="sep"+ ++this.rt.sepCnt;
  s.container=this;
  s.height=this.btnHeight;
  this.btns[s.id]=s;
  if(this.mgr)this.mgr.btns[s.id]=s;
};

ISTbar.addBreak=function() {
  var s=new ISBreak();
  s.id="brt"+ ++this.rt.brkCnt;
  s.container=this;
  s.height=this.btnHeight;
  this.btns[s.id]=s;
  if(this.mgr)this.mgr.btns[s.id]=s;
};

ISTbar.show=function(x, y) {
  var tb=document.getElementById(this.id);
  tb.style.left=x+"px";
  tb.style.top=y+"px";
  tb.style.display="";
  tb.style.zIndex=100;
  this.rt.active=true;
};

ISTbar.hide=function() {
  var tb=document.getElementById(this.id);
  tb.style.display="none";
  this.rt.active=false;
}

ISTbar.changeState=function() {};
ISTbar.onClick=function(e, s) {
};

ISTbar.render=function() {
  var s=[], j=0;
  s[j++]="<div id='"+this.id+"' style='"+(this.floating?"position:absolute;top:0px;left:0px;display:none":"")+"'><table class='"+this.style.toolbar+"'  cellpadding=0 cellspacing=0 style=\"margin:0px;padding:0px;"+(this.floating?"":"width:100%;")+"\"><tr>";
  if(this.floating) {
    s[j++]="<td unselectable=\"on\" onmousedown=\"$mvmsDown(event, this, '"+this.id+"')\" onmouseover=\"this.style.cursor='move';\" onmouseout=\"this.style.cursor='default'\" style='background-image:url("+this.iconPath+"btndrag.gif)'>";
    s[j++]="&nbsp;</td>";
  }
  s[j++]="<td unselectable='on'>";
  s[j++]="<table cellpadding=0 cellspacing=0 style='width:100%'><tr><td style='background-image:url("+this.iconPath+"bg.gif);' unselectable='on'>";
  for(var it in this.btns) {
    if(this.btns[it].toHTML) s[j++]=this.btns[it].toHTML();
  }
  s[j++]="</td></tr></table>";  
  s[j++]="</td></tr></table></div>";
  
  return s.join("");
};

/**/
function ISButton(id, icon, text, width, height) {
  this.id=id;
  this.container=null;
  this.state=1;
  this.text=text;
  this.icon=icon;
  this.height=height;
  this.width=width;
  this.type="STD";
  this.domObj=null;
  
  /*set button state, 1=normal, 2=over 3=down 4=active 5=disable*/
  this.setState=function(s) {
    this.state=s;
    var btn=this.domObj;
    if(!btn) { btn=document.getElementById(this.id).childNodes[0];this.domObj=btn;}
    btn.style.top=-this.height*(s-1)+"px";
  };
  
  this.toHTML=function() {
    var s=[], j=0, tbId=this.container.id;
    s[j++]="<table cellpadding=0 cellspacing=0 align='left' style='"+(isIE7?("width:"+this.width+"px;"):"")+"margin:0px;margin-bottom:1px;'><tr><td unselectable='on' style='text-align:left;padding:0px;padding-right:0px;VERTICAL-ALIGN: top;margin-left:0;margin-right:1px;margin-bottom:1px;width:"+this.width+"px;height:"+this.height+"px;'>";
    s[j++]="<div id=\""+id+"\" style=\"position:absolute;clip:rect(0px "+this.width+"px "+this.height+"px 0px);\" onmouseover=\"$msOver(event, '"+tbId+"', '"+this.id+"')\" onmouseout=\"$msOut(event, '"+tbId+"', '"+this.id+"')\" onmousedown=\"$msDown(event, '"+tbId+"', '"+this.id+"')\" onmouseup=\"$msUp(event, '"+tbId+"', '"+this.id+"')\" >";
    
    var sIconURL;
    if (this.icon.indexOf("/") == -1) sIconURL = this.container.iconPath + this.icon;
    else sIconURL = this.icon;

    s[j++]="<img unselectable='on' onmousedown='if(event.preventDefault) event.preventDefault();' src=\""+sIconURL+"\" style=\"position:relative;top:0px;left:0px\" alt='"+this.text+"' title='"+this.text+"'/>"
    s[j++]="</div>";
    s[j++]="</td></tr></table>";
    
    if (this.type=="DD") s[j++]=isDDs[this.ddId].toHTML();
    return s.join("");
  };
};

function ISToggleButton(id, group, checked, icon, text, width, height) {
  this.constr=ISButton;
  this.constr(id, icon, text, width, height);
  delete this.constr;
  
  this.type="TGL";
  this.checked=checked;
  this.group=group;
};

function ISSeparator(icon) {
  this.icon=icon;
  this.height=25;
  
  this.toHTML=function() {
    var s=[], j=0;
    s[j++]="<table align=left cellpadding=0 cellspacing=0 style='table-layout:fixed;'><tr>";
    s[j++]="<td unselectable='on' style='padding:0px;padding-left:0px;padding-right:0px;VERTICAL-ALIGN:top;margin-bottom:1px;width:5px;height:"+this.height+"px;'><img unselectable='on' src='"+this.container.iconPath+this.icon+"' width='5px'></td>";
    s[j++]="</tr></table>";
    return s.join("");
  }
};

function ISBreak() {  
  this.toHTML=function() {
    var s=[], j=0;
    s[j++]="</td></tr><tr><td style='height:0px'></td></tr><tr><td style='background-image:url("+this.container.iconPath+"bg.gif);height:"+this.height+"px'>";
    return s.join("");
  };
};

function ISDropdownButton(id, ddId, icon, text, width, height) {
  this.constr=ISButton;
  this.constr(id, icon, text, width, height);
  delete this.constr;
  
  this.type="DD";

  this.ddId=ddId;
};

var isDDs=new Object();
function ISDropdown(id) {
  this.id=id;
  this.items=new Object();
  this.maxRowItems=20;
  this.iconPath="";
  
  this.add=function(it) { this.items[it.id]=it; it.container=this;};
  this.addItem=function(id, text, icon) {
    this.add(new ISDropdownItem(id, text, icon));
  };
  
  this.enableItem=function(id, f){
    this.items[id].enable=f;
    document.getElementById(id).className=(f?"isdd_norm":"isdd_disb");
  };
  
  this.selectItem=function(id, f) {
    this.clearSelection();
    this.items[id].selected=f;
    document.getElementById(id).className=(f?"isdd_sel":"isdd_norm");
  };
  
  this.clearSelection=function() {
    for(var it in this.items) {
      if(this.items[it].selected) {
        document.getElementById(it).className="isdd_norm";
        this.items[it].selected=false;
      }
    }
  }
  
  this.toHTML=function() {
    var s=[], j=0; it=null; 
    s[j++]="<table id='"+this.id+"' cellpadding=0 cellspacing=0 style='line-height:normal;z-index:100000;display:none;position:absolute;border:#80788D 1px solid; cursor:default;background-color:#ffffff;' unselectable=on><tr><td>";
    s[j++]="<table cellpadding=0 cellspacing=0>";
    var r=1;
    for (var i in this.items) {
      it=this.items[i];
      if (!it.toHTML) continue;
      s[j++]=it.toHTML();
      if (r%this.maxRowItems==0) {
        s[j++]="</table>";
        s[j++]="</td><td valign=top style='padding:0px;border-left:#80788D 1px solid'>";
        s[j++]="<table cellpadding=0 cellspacing=0>";
      }
      r++;
    }
    s[j++]="</table></td></tr></table>";
    return s.join("");
  };
  
  this.onClick=function(itId) {}
  
  isDDs[id]=this;
};

function ISDropdownItem(id, text, icon) {
  this.id=id;
  this.text=text;
  this.icon=icon?icon:null;
  this.enable=true;
  this.selected=false;
  this.container=null;
  this.toHTML=function() {
    return "<tr><td id='"+this.id+"' onclick=\"$ddmsClick('"+this.container.id+"', '"+this.id+"', this)\" class='"+(this.enable?"isdd_norm":"isdd_disb")+"' onmouseover=\"$ddmsOver('"+this.container.id+"', '"+this.id+"', this)\" onmouseout=\"$ddmsOut('"+this.container.id+"', '"+this.id+"', this)\" unselectable=on nowrap>"
    + (this.icon?"<img style='vertical-align:middle;margin-right:2px;' src='"+this.container.iconPath+this.icon+"' />":"")
    + "<span unselectable='on'>" + this.text + "</span>"
    + "</td></tr>";
  }
};

function ISCustomDDItem(id, s) { 
  this.id=id;
  this.html=s;
  this.toHTML=function() {return ("<tr><td>"+this.html+"</td></tr>"); } 
};

/*------------------*/

/*floating functions*/
function $mvmsDown(e, el, tbId) {
  var tb=isTbars[tbId];
  tb.rt.clOff=(isIE?[e.offsetX, e.offsetY]:[e.layerX, e.layerY]);
  
  var d=document, de=d.documentElement;
  tb.rt.scrl1=(isIE?(de?[de.scrollLeft, de.scrollTop]:[d.body.scrollLeft, d.body.scrollTop]):[window.scrollX, window.scrollY]);

  d.onmousemove=function(e) {$tbStartDrag_1((isIE?event:e), tb, document.getElementById(tbId));}
  d.onmouseup=function(e) {$tbEndDrag((isIE?event:e), tb)}
  d.onselectstart=function() { return false;}
  d.onmousedown=function() { return false;}
  d.ondragstart=function() { return false;}  
  
  d=tb.rt.document;
  //tb.rt.scrl2=(isIE?[d.body.scrollLeft, d.body.scrollTop]:[d.body.scrollX, d.body.scrollY]);
  if(d) {
    d.onmousemove=function(e) {$tbStartDrag_2((isIE?d.parentWindow.event:e), tb, document.getElementById(tbId));}
    d.onmouseup=function(e) {$tbEndDrag((isIE?d.parentWindow.event:e), tb)}
    d.onselectstart=function() { return false;}
    d.onmousedown=function() { return false;}
    d.ondragstart=function() { return false;}   
  }
};

function $tbStartDrag_1(e, tb, oTb) {
  //window.status="x:"+(e.clientX)+"-y:"+(e.clientY);
  oTb.style.left=e.clientX-tb.rt.clOff[0]+tb.rt.scrl1[0] + "px";
  oTb.style.top=e.clientY-tb.rt.clOff[1]+tb.rt.scrl1[1] + "px";
};

function $tbStartDrag_2(e, tb, oTb) {
  //window.status="x:"+(e.clientX)  + "-y:"+(e.clientY);
  oTb.style.left=e.clientX-tb.rt.clOff[0]+tb.rt.docOff[0]+ "px";
  oTb.style.top=e.clientY-tb.rt.clOff[1]+tb.rt.docOff[1]+ "px";
};


function $tbEndDrag(e, tb) {
  //var d=tb.rt.document;
  var d=[document, tb.rt.document];
  for (var i=0;i<d.length;i++) {
    if (!d[i]) continue;
    d[i].onmousemove=null;
    d[i].onmouseup=null;
    d[i].onmousedown=function() { return true;}
    d[i].onselectstart=function() { return true;}
    d[i].onselectstart=function() { return true;}      
  }
};

function $ddmsOver(ddId, id, t) {
  var it=isDDs[ddId].items[id];
  if(!it.enable || it.selected)return;
  t.className="isdd_over";
};

function $ddmsOut(ddId, id, t) {
  var it=isDDs[ddId].items[id];
  if(!it.enable || it.selected)return;
  t.className="isdd_norm";
};

function $ddmsClick(ddId, id, t) {
  if(!isDDs[ddId].items[id].enable)return;
  isDDs[ddId].selectItem(id, true);
  hideDD(ddId);
  isDDs[ddId].onClick(id);
};

/*end of floating functions*/

var $bCancel=false;
function $msOver(e, tbId, btnId) {
  var btn=isTbars[tbId].btns[btnId];
  if(btn.state==1) btn.setState(2);
};

function $msOut(e, tbId, btnId) {
  var btn=isTbars[tbId].btns[btnId];
  if(btn.state==3) {$bCancel=true;}
  if(btn.state==2) btn.setState(1);
};

function $msDown(e, tbId, btnId) {
  var btn=isTbars[tbId].btns[btnId];
  if(btn.state!=5) btn.setState(3);
};

function $msUp(e, tbId, btnId) {
  var tbar=isTbars[tbId];
  var btn=tbar.btns[btnId];
  if($bCancel) {$bCancel=false; btn.setState(1); return false;}
  if(btn.state==5) return false;
  if (btn.type=="STD") {
    btn.setState(2);
    tbar.onClick(btnId);
  } else if(btn.type=="TGL") { 
    if (btn.group!=null && btn.group!="") {
      //find all other button with the same group and set 
      var tBtn=null;
      for (var it in tbar.btns) {
        tBtn=tbar.btns[it];
        if(!tBtn.id) continue;
        if (tBtn.group==btn.group && tBtn.id!=btn.id) {tBtn.setState(1); tBtn.checked=false;}
      }
    }
    //toggle button      
    btn.setState(btn.checked?2:4);
    btn.checked=!btn.checked;
    tbar.onClick(btnId);
  } else if(btn.type=="DD") {
    tbar.onClick(btnId);
    showDD(tbId, btnId, btn.ddId);
    btn.setState(2);
  }
  return true;
};

function showDD(tbId, btnId, ddId) {
  hideAllDD();
  
  var btn=document.getElementById(btnId);
  var dd=document.getElementById(ddId);
  var tmp=btn; var x=0, y=0;
  x=btn.offsetLeft; y=btn.offsetTop;
  dd.style.left=x+"px";
  dd.style.top=y+25+"px";
  dd.style.display="block";

  if (!isDDs[ddId].container) isDDs[ddId].container=isTbars[tbId].btns[btnId];
};

function hideDD(ddId) {
  if (document.getElementById(ddId)) document.getElementById(ddId).style.display="none";
};

function hideAllDD() {
  for (var tId in isDDs) { hideDD(tId); }
};
/*--------------------*/

var isTGroups=new Object();

function ISToolbarGroup(id) {
  this.id=id;
  this.mgr=null;
  this.grps=new Object();
  this.visible=true;
  this.groupFlow=false;
  this.draggable = false;

  isTGroups[id]=this;
}
var ISTbarGrp=ISToolbarGroup.prototype;

ISTbarGrp.addGroup=function(id, name, tbId) {
  var g=new ISGroup(id, name, tbId);
  this.grps[id]=g;
};


ISTbarGrp.render=function() {
  var s=[], j=0;
  if(this.groupFlow==true) {
      s[j++]="<table id='"+this.id+"' cellpadding=0 cellspacing=0 border=0 style='"+(this.visible?"":"display:none;")+"'><tr><td>";
      for (var it in this.grps) {
        if(this.grps[it].render) {
          if(isIE7) {
            s[j++]="<table cellspacing='0' cellspacing='0' style='float:left'><tr><td>";
            s[j++]=this.grps[it].render();
            s[j++]="</td></tr></table>";
          } else {
            s[j++] = "<div style='float:left;margin:2px;margin-right:0px;'>";
            s[j++]=this.grps[it].render();
            s[j++]="</div>";
          }
        }
      }  
      s[j++]="</td></tr></table>";
  } else {
      s[j++]="<table id='"+this.id+"' cellpadding=0 cellspacing=0 border=0 style='"+(this.visible?"":"display:none;")+"'><tr>";
      for (var it in this.grps) {
        if(this.grps[it].render) {
          s[j++]="<td unselectable='on'>"+this.grps[it].render()+"</td>";
        }
      }  
      s[j++]="</tr></table>"; 
  }
  
  return s.join("");
};

ISTbarGrp.setVisibility=function(b) {
  this.visible=b;
  var e=document.getElementById(this.id);
  if(e) e.style.display=(b?"":"none");
};

function ISGroup(id, name, tbId) {
  this.id=id;
  this.name=name;
  this.tbId=tbId;
  return this;
};


ISGroup.prototype.render=function() {
  var s=[], j=0;
  s[j++]="<table cellpadding=0 cellspacing=0 style='margin-right:3px;font-size:8px;' unselectable='on'>";
  s[j++]="<tr><td class='bdrgrptopleft'>&nbsp;</td><td class='bdrgrptop'></td><td class='bdrgrptopright'>&nbsp;</td></tr>";
  s[j++]="<tr><td colspan='3' width='100%'>";
  
  s[j++]="<table cellpadding=0 cellspacing=0 class='isgroup' width='100%' style='width:100%;font-size:8px;'><tr><td class='bdrgrpleft'></td><td class='isgroupcontent' unselectable='on'>";
  s[j++]=isTbars[this.tbId].render();
  s[j++]="</td><td class='bdrgrpright'></td></tr>";  
  //s[j++]="<tr><td class='bdrgrpleft'></td><td class='isgrouptitle' align='center'>";
  //s[j++]=this.name;
  //s[j++]="</td><td class='bdrgrpright'></td></tr>";
  s[j++]="</table>";
  
  s[j++]="</td></tr>";
  s[j++]="<tr><td class='bdrgrpbottomleft'>&nbsp;</td><td class='bdrgrpbottom'></td><td class='bdrgrpbottomright'>&nbsp;</td></tr>";
  s[j++]="</table>";
  

  return s.join("");  
};

/*--------------------*/
var isTabs=new Object();

function ISTabCtl(id) {
  this.id=id;
  this.mgr=null;
  this.tabs=new Object();
  this.tabIdx=[];
  this.selTab=null;
  isTabs[id]=this;
  return this;
};

function ISTab(id, capt, obj) {
  this.id=id;
  this.capt=capt;
  this.obj=obj;
  this.selected=false;
  return this;
};

ISTab.prototype.render=function() {
  var s=[], j=0, sf=(this.selected?"sel":"")
  s[j++]="<table id='"+this.id+"' cellpadding=0 cellspacing=0 class='istab' align='left' onclick=\"isTabs."+this.tab.id+".setTab('"+this.id+"')\" style='cursor:default;' unselectable='on'><tr>";
  s[j++]="<td class='tableft"+sf+"' width='5px'></td>";
  s[j++]="<td class='tabtitle"+sf+"' unselectable='on'>"+this.capt+"</td>";
  s[j++]="<td class='tabright"+sf+"' width='5px'></td>";
  s[j++]="</tr></table>";
  return s.join("");    
};

ISTabCtl.prototype.addTab=function(id, capt, obj) {
  var t=new ISTab(id, capt, obj);
  t.tab=this;
  this.tabs[id]=t;
  if(this.tabIdx.length==0) this.selTab=id;
  this.tabIdx[this.tabIdx.length]=id;
};

ISTabCtl.prototype.render=function() {
  var s=[], j=0, o=null;
  s[j++]="<table cellpadding=0 cellspacing=0 class='istabctl'><tr><td class='bdrtabtopleft' unselectable='on'></td><td class='bdrtabtop' unselectable=\"on\" style='padding-left:3px;'>";
  for (var it in this.tabs) {
    o=this.tabs[it];
    if(!o.render) continue;
    o.selected=(this.selTab==o.id);
    s[j++]=o.render(); 
  }
  s[j++]="</td><td class='bdrtabtopright'></td></tr>";
  s[j++]="<tr><td class='bdrtableft' style='font-size:7pt'>&nbsp;</td><td class='tabcontent' unselectable='on'>";
  if(!isIE)s[j++]="<div style='position:relative'>";
  for (var it in this.tabs) { 
    o=this.tabs[it].obj;
    if(!o) continue;
    o.visible=(this.selTab==this.tabs[it].id);
    s[j++]=this.tabs[it].obj.render(); 
  }
  if(!isIE)s[j++]="</div>";
  s[j++]="</td><td class='bdrtabright' style='font-size:7pt'>&nbsp;</td></tr>";
  s[j++]="<tr><td class='bdrtabbottomleft'></td><td class='bdrtabbottom'></td><td class='bdrtabbottomright'></td></tr>";
  s[j++]="</table>";
  return s.join("");
};

ISTabCtl.prototype.setTab=function(id) {
  //current selected
  var t=document.getElementById(this.selTab);
  if (t) {
    t.rows[0].cells[0].className="tableft";
    t.rows[0].cells[1].className="tabtitle";
    t.rows[0].cells[2].className="tabright";
  }
  this.tabs[this.selTab].selected=false;
  this.tabs[this.selTab].obj.setVisibility(false);
  
  t=document.getElementById(id);
  t.rows[0].cells[0].className="tableftsel";
  t.rows[0].cells[1].className="tabtitlesel";
  t.rows[0].cells[2].className="tabrightsel";
  this.tabs[id].selected=true; 
  this.tabs[id].obj.setVisibility(true);
  this.selTab=id;
};

function ISWindow(id) {
  
  var ua = navigator.userAgent.toUpperCase();
  var isIE =(ua.indexOf('MSIE') >= 0) ? true : false,
  isIE7=(ua.indexOf("MSIE 7.0") >=0),
  isIE8=(ua.indexOf("MSIE 8.0") >=0),
  isIE6=(!isIE7 && !isIE8 && isIE),
  IEBackCompat = (isIE && document.compatMode=="BackCompat");
  
  var me=this;
  
  this.id=id;
  this.opts=null;
  this.rt={};
  this.iconPath="icons/";
  
  ISWindow.objs[id] = this;
    
  this.show=function(opt) {
  
    if(!document.getElementById(this.id)) {
      //render
      var e = document.createElement("div");
      e.id = "cnt$"+this.id;
      e.innerHTML = this.render(opt.url);
      document.body.insertBefore(e, document.body.childNodes[0]);
    }
  
    if(!this.rt.win) {
      this.rt.win = document.getElementById(this.id);
      this.rt.frm = document.getElementById("frm$"+this.id);
      this.rt.ttl = document.getElementById("ttl$"+this.id);
    }
    
    if(opt.overlay==true) this.showOverlay();
    
    this.setSize(opt.width, opt.height, opt.center);
    ISWindow.zIndex+=2;
    this.rt.win.style.zIndex = ISWindow.zIndex;
    this.rt.win.style.display="block";
    
    var fn = 
        
        function() {
          me.rt.ttl.innerHTML = me.rt.frm.contentWindow.document.title;
          me.rt.frm.contentWindow.openerWin = opt.openerWin ? opt.openerWin : window;
          me.rt.frm.contentWindow.opener = opt.openerWin ? opt.openerWin : window;
          me.rt.frm.contentWindow.options = opt.options?opt.options:{};
          me.rt.frm.contentWindow.closeWin=function() {
            me.close();
          };
          me.rt.frm.contentWindow.close=function() {
            me.close();
          };          
          if (typeof(me.rt.frm.contentWindow.bodyOnLoad) != "undefined") me.rt.frm.contentWindow.bodyOnLoad();
        } ;
    
    if(this.rt.frm.attachEvent) {
      this.rt.frm.attachEvent("onload", fn);
    } else {
      this.rt.frm.addEventListener("load", fn, true);
    }
    
    
    setTimeout(function() {me.rt.frm.src = opt.url;}, 0);
    
  };
  
  this.close = function() {
    var d = document.getElementById("cnt$"+this.id);    
    if(d) {
      if(this.rt.frm.contentWindow.bodyOnUnload) this.rt.frm.contentWindow.bodyOnUnload();
      d.parentNode.removeChild(d);
    }
    this.hideOverlay();
  };
  
  this.hide=function() {
    if(!this.rt.win) {
      this.rt.win = document.getElementById(this.id);
    }
    this.rt.win.style.display="none";
  };
  
  this.showOverlay=function() {
    
    var ov=document.getElementById("ovr$"+this.id);
    if(!ov) {
      ov = document.createElement("div");
      ov.id = "ovr$"+this.id;
      ov.style.display="none";
      ov.style.position=(isIE6 || IEBackCompat ? "absolute" : "fixed");
      ov.style.backgroundColor="#ffffff";
      ov.style.filter = "alpha(opacity=35)";
      ov.style.mozOpacity = "0.4";
      ov.style.opacity = "0.4";
      ov.style.Opacity = "0.4";
      document.body.insertBefore(ov, document.body.childNodes[0]);
    }
    
    var cl = ISWindow.clientSize();
      
    if(isIE6 || IEBackCompat) {
        
      var db=document.body, de=document.documentElement, w, h;
      w=Math.min(
            Math.max(db.scrollWidth, de.scrollWidth),
            Math.max(db.offsetWidth, de.offsetWidth)
          );

      var mf=((de.scrollHeight<de.offsetHeight) || (db.scrollHeight<db.offsetHeight))?Math.min:Math.max;  
      h=mf(
            Math.max(db.scrollHeight, de.scrollHeight),
            Math.max(db.offsetHeight, de.offsetHeight)
          );

      cl.w = Math.max(cl.w, w);
      cl.h = Math.max(cl.h, h);
      
      ov.style.position="absolute";
    }
      
    ov.style.width = cl.w+"px";
    ov.style.height = cl.h+"px";
    ov.style.top="0px";
    ov.style.left="0px";
    ov.style.zIndex = ISWindow.zIndex+1;
    ov.style.display="block";
    
  };
  
  this.hideOverlay=function() {
    var ov=document.getElementById("ovr$"+this.id);
    if(ov) ov.style.display="none";
  };
  
  this.setSize=function(w, h, center) {
    this.rt.win.style.width=w;
    this.rt.win.style.height=h;
    this.rt.frm.style.height=parseInt(h, 10)-30 + "px";
    if(center) {
      this.center();
    }
  };
  
  this.center=function() {
    
    var c=ISWindow.clientSize();
    var px=parseInt(this.rt.win.style.width, 10), py=parseInt(this.rt.win.style.height, 10);
    px=(isNaN(px)?0:(px>c.w?c.w:px));
    py=(isNaN(py)?0:(py>c.h?c.h:py));
    var p = {x:(c.w-px)/2, y:(c.h-py)/2};
    if(isIE6 || IEBackCompat) {
      p.x=p.x+(document.body.scrollLeft||document.documentElement.scrollLeft);
      p.y=p.y+(document.body.scrollTop||document.documentElement.scrollTop);
    }
    this.setPosition(p.x, p.y);
  
  };
  
  this.setPosition=function(x, y) {
    
    this.rt.win.style.top=y+"px";
    this.rt.win.style.left=x+"px";    
    
  };
  
  this.render=function(attr) {
    
    var s=[],j=0,ps=isIE6 || IEBackCompat ?"absolute":"fixed";
    s[j++] = "<div style='position:"+ps+";display:none;z-index:100000;background-color:#ffffff;filter:alpha(opacity=25);opacity:0.25;-moz-opacity:0.25;border:#999999 1px solid' id=\"dd$"+this.id+"\"></div>";
    s[j++] = "<div unselectable='on' id=\""+this.id+"\" style='position:"+ps+";z-index:100000;border:#d4d4d4 6px solid;border-bottom:#d4d4d4 9px solid;display:none'>";
    s[j++] = "<div style='border:#d2d2d2 1px solid;'>";
    s[j++] = "  <div unselectable=\"on\" style=\"cursor:move;height:30px;background-image:url("+this.iconPath+"dialogbg.gif);\" onmousedown=\"ISWindow._ddMouseDown(event, '"+this.id+"');\"><span style=\"font-weight:bold;float:left;margin-top:7px;margin-left:11px;\" id=\"ttl$"+this.id+"\"></span><img src=\""+this.iconPath+"btnClose.gif\" onmousedown=\"event.cancelBubble=true;if(event.preventDefault) event.preventDefault();\" onclick=\"ISWindow.objs['" + this.id + "'].close();\" style='float:right;margin-top:5px;margin-right:5px;cursor:pointer' /></div>";
    s[j++] = "  <iframe id=\"frm$"+this.id+"\" style=\"width:100%;\" src=\""+ this.iconPath+"blank.gif" +"\" frameborder='no'></iframe>";
    s[j++] = "</div>";
    s[j++] = "</div>";
    return s.join("");
  };
  
  ISWindow.clientSize=function() {
    return {w:window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth, 
            h:window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight};
  };
  
  ISWindow._ddMouseDown=function(ev, elId) {
    
    var d=document;    
    
    d.onmousemove=function(e) {ISWindow._startDrag(e?e:ev);}
    d.onmouseup=function(e) {ISWindow._endDrag(e?e:ev);}
    d.onselectstart=function() { return false;}
    d.onmousedown=function() { return false;}
    d.ondragstart=function() { return false;}
    
    ISWindow.trgElm = document.getElementById(elId);
    
    ISWindow.gstElm = document.getElementById("dd$"+elId);
    ISWindow.gstElm.style.top=ISWindow.trgElm.style.top;
    ISWindow.gstElm.style.left=ISWindow.trgElm.style.left;
    ISWindow.gstElm.style.width=ISWindow.trgElm.style.width;
    ISWindow.gstElm.style.height=ISWindow.trgElm.style.height;
    ISWindow.gstElm.style.display="block";
    
    ISWindow.posDif = {x:ev.clientX-parseInt(ISWindow.trgElm.style.left, 10),
                       y:ev.clientY-parseInt(ISWindow.trgElm.style.top, 10)};    
  };
  
  ISWindow._startDrag = function(ev) {
    ISWindow.gstElm.style.left=(ev.clientX-ISWindow.posDif.x)+"px";
    ISWindow.gstElm.style.top=(ev.clientY-ISWindow.posDif.y)+"px";
  };
  
  ISWindow._endDrag = function(ev) {
    
    ISWindow.gstElm.style.display="none";
    
    ISWindow.trgElm.style.top=ISWindow.gstElm.style.top;
    ISWindow.trgElm.style.left=ISWindow.gstElm.style.left;
    
    document.onmousemove=null;
    document.onmouseup=null;
    document.onmousedown=function() { return true;};
    document.onselectstart=function() { return true;};
    document.onselectstart=function() { return true;};
    
  };
  
};

ISWindow.objs={};
ISWindow.zIndex=2000;