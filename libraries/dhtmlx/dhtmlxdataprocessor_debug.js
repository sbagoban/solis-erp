//v.3.5 build 120731

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
To use this component please contact sales@dhtmlx.com to obtain license
*/
dataProcessor.prototype._o_init=dataProcessor.prototype.init;dataProcessor.prototype.init=function(a){this._console=this._console||this._createConsole();this.attachEvent("onValidatationError",function(a){this._log("Validation error for ID="+(a||"[multiple]"));return!0});return this._o_init(a)};
dataProcessor.prototype._createConsole=function(){if(window.dataProcessorConsole)return window.dataProcessorConsole;var a=document.createElement("DIV");a.style.cssText="width:450px; height:420px; overflow:auto; position:absolute; z-index:99999; background-color:white; top:0px; right:0px; border:1px dashed black; font-family:Tahoma; Font-size:10pt;";a.innerHTML="<div style='width:100%; background-color:gray; font-weight:bold; color:white;'><span style='cursor:pointer;float:right;' onclick='this.parentNode.parentNode.style.display=\"none\"'><sup>[close]&nbsp;</sup></span><span style='cursor:pointer;float:right;' onclick='this.parentNode.parentNode.childNodes[2].innerHTML=\"\"'><sup>[clear]&nbsp;</sup></span>&nbsp;DataProcessor</div><div style='width:100%; height:200px; overflow-Y:scroll;'>&nbsp;Current state</div><div style='width:100%; height:200px; overflow-Y:scroll;'>&nbsp;Log:</div>";
document.body?document.body.insertBefore(a,document.body.firstChild):dhtmlxEvent(window,"load",function(){document.body.insertBefore(a,document.body.firstChild)});dhtmlxEvent(window,"dblclick",function(){a.style.display=""});return window.dataProcessorConsole=a};dataProcessor.prototype._error=function(a){this._log("<span style='color:red'>"+a+"</span>")};
dataProcessor.prototype._log=function(a){var b=document.createElement("DIV");b.innerHTML=a;var c=this._console.childNodes[2];c.appendChild(b);c.scrollTop=c.scrollHeight};
dataProcessor.prototype._updateStat=function(a){for(var a=["&nbsp;Current state"],b=0;b<this.updatedRows.length;b++)a.push("&nbsp;ID:"+this.updatedRows[b]+" Status: "+(this.obj.getUserData(this.updatedRows[b],"!nativeeditor_status")||"updated")+", "+(this.is_invalid(this.updatedRows[b])||"valid"));this._console.childNodes[1].innerHTML=a.join("<br/>")+"<hr/>Current mode: "+this.updateMode};
dataProcessor.prototype.xml_analize=function(a){if(_isFF)if(a.xmlDoc.responseXML)if(a.xmlDoc.responseXML.firstChild.tagName=="parsererror")this._error(a.xmlDoc.responseXML.firstChild.textContent);else return!0;else this._error("Not an XML, probably incorrect content type specified ( must be text/xml ), or some text output was started before XML data");else if(_isIE)if(a.xmlDoc.responseXML.parseError.errorCode)this._error("XML error : "+a.xmlDoc.responseXML.parseError.reason);else if(a.xmlDoc.responseXML.documentElement)return!0;
else this._error("Not an XML, probably incorrect content type specified ( must be text/xml ), or some text output was started before XML data");return!1};dataProcessor.wrap=function(a,b,c){var d=dataProcessor.prototype;if(!d._wrap)d._wrap={};d._wrap[a]=d[a];d[a]=function(){b&&b.apply(this,arguments);var e=d._wrap[a].apply(this,arguments);c&&c.apply(this,[arguments,e]);return e}};
dataProcessor.wrap("setUpdated",function(a,b,c){this._log("&nbsp;row <b>"+a+"</b> "+(b?"marked":"unmarked")+" ["+(c||"updated")+","+(this.is_invalid(a)||"valid")+"]")},function(){this._updateStat()});
dataProcessor.wrap("sendData",function(a){a&&(this._log("&nbsp;Initiating data sending for <b>"+a+"</b>"),this.obj.mytype=="tree"?this.obj._idpull[a]||this._log("&nbsp;Error! item with such ID not exists <b>"+a+"</b>"):this.obj.rowsAr&&(this.obj.rowsAr[a]||this._log("&nbsp;Error! row with such ID not exists <b>"+a+"</b>")))},function(){});dataProcessor.wrap("sendAllData",function(){this._log("&nbsp;Initiating data sending for <b>all</b> rows ")},function(){});
dataProcessor.wrap("_sendData",function(a,b){b?this._log("&nbsp;Sending in one-by-one mode, current ID = "+b):this._log("&nbsp;Sending all data at once");this._log("&nbsp;Server url: "+this.serverProcessor+" <a onclick='this.parentNode.nextSibling.firstChild.style.display=\"block\"' href='#'>parameters</a>");var c=[];this._log("<blockquote style='display:none;'>null<blockquote>")},function(){});
dataProcessor.wrap("afterUpdate",function(a,b,c,d,e){a._log("&nbsp;Server response received <a onclick='this.nextSibling.style.display=\"block\"' href='#'>details</a><blockquote style='display:none'><code>"+(e.xmlDoc.responseText||"").replace(/\&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")+"</code></blockquote>");if(a.xml_analize(e)){var f=e.doXPath("//data/action");f||(a._log("&nbsp;No actions found"),(f=e.getXMLTopNode("data"))?a._log("&nbsp;Incorrect content type - need to be text/xml"):
a._log("&nbsp;XML not valid"))}},function(){});dataProcessor.wrap("afterUpdateCallback",function(a,b,c){this.obj.mytype=="tree"?this.obj._idpull[a]||this._log("Incorrect SID, item with such ID not exists in grid"):this.obj.rowsAr&&(this.obj.rowsAr[a]||this._log("Incorrect SID, row with such ID not exists in grid"));this._log("&nbsp;Action: "+c+" SID:"+a+" TID:"+b)},function(){});

//v.3.5 build 120731

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
To use this component please contact sales@dhtmlx.com to obtain license
*/