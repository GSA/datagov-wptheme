// WebTrends SmartSource Data Collector Tag
// Version: 9.4.0     
// Tag Builder Version: 4.1
// Created: 3/18/2013 4:33:01 PM

function WebTrends(){
	var that=this;
	// begin: user modifiable
	this.dcsid="dcscde6kv7bv0hc1auudkkpvh_6c5h";
	this.domain="statse.webtrendslive.com";
	this.timezone=-5;
	this.onsitedoms="";
	this.downloadtypes="xls,doc,pdf,txt,csv,zip,docx,xlsx";
	this.navigationtag="div,table";
	this.trackevents=true;
	this.trimoffsiteparams=true;
	this.enabled=true;
	this.i18n=false;
	this.paidsearchparams="gclid";
	this.splitvalue="";
	this.preserve=true;
	this.cookieTypes = "all";
	this.FPCConfig = {
		enabled: (this.cookieTypes === "all" || this.cookieTypes == "firstPartyOnly"),
		name: "WT_FPC",
		domain: ".catalog.data.gov",
		expires: 63113851500
	};
	this.TPCConfig = {
		enabled: (this.cookieTypes === "all"),
		cfgType: (this.cookieTypes === "all") ? "":"1"
	};
	// end: user modifiable
	this.DCS={};
	this.WT={};
	this.DCSext={};
	this.images=[];
	this.index=0;

	this.exre=(function(){return(window.RegExp?new RegExp("dcs(uri)|(ref)|(aut)|(met)|(sta)|(sip)|(pro)|(byt)|(dat)|(p3p)|(cfg)|(redirect)|(cip)","i"):"");})();
	this.re=(function(){return(window.RegExp?(that.i18n?{"%25":/\%/g,"%26":/\&/g}:{"%09":/\t/g,"%20":/ /g,"%23":/\#/g,"%26":/\&/g,"%2B":/\+/g,"%3F":/\?/g,"%5C":/\\/g,"%22":/\"/g,"%7F":/\x7F/g,"%A0":/\xA0/g}):"");})();
}
WebTrends.prototype.dcsGetId=function(){
	if (this.enabled&&(document.cookie.indexOf(this.FPCConfig.name+"=")==-1)&&(document.cookie.indexOf("WTLOPTOUT=")==-1)&&this.TPCConfig.enabled){
		document.write("<scr"+"ipt type='text/javascript' src='"+"http"+(window.location.protocol.indexOf('https:')==0?'s':'')+"://"+this.domain+"/"+this.dcsid+"/wtid.js"+"'><\/scr"+"ipt>");
	}
}
WebTrends.prototype.setCookieTypes = function (types) {
	this.FPCConfig.enabled = (types === "all" || types == "firstPartyOnly");	
	this.TPCConfig.enabled = (types === "all");
	this.TPCConfig.cfgType = (types === "all") ? "":"1"
}
WebTrends.prototype.dcsGetCookie=function(name){
	var cookies=document.cookie.split("; ");
	var cmatch=[];
	var idx=0;
	var i=0;
	var namelen=name.length;
	var clen=cookies.length;
	for (i=0;i<clen;i++){
		var c=cookies[i];
		if ((c.substring(0,namelen+1))==(name+"=")){
			cmatch[idx++]=c;
		}
	}
	var cmatchCount=cmatch.length;
	if (cmatchCount>0){
		idx=0;
		if ((cmatchCount>1)&&(name==this.FPCConfig.name)){
			var dLatest=new Date(0);
			for (i=0;i<cmatchCount;i++){
				var lv=parseInt(this.dcsGetCrumb(cmatch[i],"lv"));
				var dLst=new Date(lv);
				if (dLst>dLatest){
					dLatest.setTime(dLst.getTime());
					idx=i;
				}
			}
		}
		return unescape(cmatch[idx].substring(namelen+1));
	}
	else{
		return null;
	}
}
WebTrends.prototype.dcsGetCrumb=function(cval,crumb,sep){
	var aCookie=cval.split(sep||":");
	for (var i=0;i<aCookie.length;i++){
		var aCrumb=aCookie[i].split("=");
		if (crumb==aCrumb[0]){
			return aCrumb[1];
		}
	}
	return null;
}
WebTrends.prototype.dcsGetIdCrumb=function(cval,crumb){
	var id=cval.substring(0,cval.indexOf(":lv="));
	var aCrumb=id.split("=");
	for (var i=0;i<aCrumb.length;i++){
		if (crumb==aCrumb[0]){
			return aCrumb[1];
		}
	}
	return null;
}
WebTrends.prototype.dcsIsFpcSet=function(name,id,lv,ss){
	var c=this.dcsGetCookie(name);
	if (c){
		return ((id==this.dcsGetIdCrumb(c,"id"))&&(lv==this.dcsGetCrumb(c,"lv"))&&(ss==this.dcsGetCrumb(c,"ss")))?0:3;
	}
	return 2;
}
WebTrends.prototype.dcsDeleteCookie=function(name, path, domain) {	
	var cDelete = name + "=";
	cDelete += "; expires=expires=Thu, 01 Jan 1970 00:00:01 GMT";
	cDelete += "; path=" + path;
	cDelete += (domain) ? ";domain="+domain : "";		
	document.cookie = cDelete;
}
WebTrends.prototype.dcsFPC=function(){
	if (document.cookie.indexOf("WTLOPTOUT=")!=-1){
		return;
	}

	if (!this.FPCConfig.enabled && !this.TPCConfig.enabled)
		this.WT["ce"] = "0"
	else if (this.FPCConfig.enabled && !this.TPCConfig.enabled)
		this.WT["ce"] = "1";
	else
		this.WT["ce"] = "2";

	if (!this.FPCConfig.enabled) {
		this.dcsDeleteCookie(this.FPCConfig.name, "/", this.FPCConfig.domain);
		return;
	}

	var WT=this.WT;
	var name=this.FPCConfig.name;
	var dCur=new Date();
	var adj=(dCur.getTimezoneOffset()*60000)+(this.timezone*3600000);
	dCur.setTime(dCur.getTime()+adj);
	var dExp=new Date(dCur.getTime()+315360000000);
	var dSes=new Date(dCur.getTime());
	WT.co_f=WT.vtid=WT.vtvs=WT.vt_f=WT.vt_f_a=WT.vt_f_s=WT.vt_f_d=WT.vt_f_tlh=WT.vt_f_tlv="";
	if (document.cookie.indexOf(name+"=")==-1){
		if ((typeof(gWtId)!="undefined")&&(gWtId!="")){
			WT.co_f=gWtId;
		}
		else if ((typeof(gTempWtId)!="undefined")&&(gTempWtId!="")){
			WT.co_f=gTempWtId;
			WT.vt_f="1";
		}
		else{
			WT.co_f="2";
			var curt=dCur.getTime().toString();
			for (var i=2;i<=(32-curt.length);i++){
				WT.co_f+=Math.floor(Math.random()*16.0).toString(16);
			}
			WT.co_f+=curt;
			WT.vt_f="1";
		}
		if (typeof(gWtAccountRollup)=="undefined"){
			WT.vt_f_a="1";
		}
		WT.vt_f_s=WT.vt_f_d="1";
		WT.vt_f_tlh=WT.vt_f_tlv="0";
	}
	else{
		var c=this.dcsGetCookie(name);
		var id=this.dcsGetIdCrumb(c,"id");
		var lv=parseInt(this.dcsGetCrumb(c,"lv"));
		var ss=parseInt(this.dcsGetCrumb(c,"ss"));
		if ((id==null)||(id=="null")||isNaN(lv)||isNaN(ss)){
			return;
		}
		WT.co_f=id;
		var dLst=new Date(lv);
		WT.vt_f_tlh=Math.floor((dLst.getTime()-adj)/1000);
		dSes.setTime(ss);
		if ((dCur.getTime()>(dLst.getTime()+1800000))||(dCur.getTime()>(dSes.getTime()+28800000))){
			WT.vt_f_tlv=Math.floor((dSes.getTime()-adj)/1000);
			dSes.setTime(dCur.getTime());
			WT.vt_f_s="1";
		}
		if ((dCur.getDay()!=dLst.getDay())||(dCur.getMonth()!=dLst.getMonth())||(dCur.getYear()!=dLst.getYear())){
			WT.vt_f_d="1";
		}
	}
	WT.co_f=escape(WT.co_f);
	WT.vtid=(typeof(this.vtid)=="undefined")?WT.co_f:(this.vtid||"");
	WT.vtvs=(dSes.getTime()-adj).toString();
	var expiry= (this.FPCConfig.expires) ? "; expires="+ new Date(new Date().getTime() + (this.FPCConfig.expires)).toGMTString():"";
	var cur=dCur.getTime().toString();
	var ses=dSes.getTime().toString();
	document.cookie=name+"="+"id="+WT.co_f+":lv="+cur+":ss="+ses+expiry+"; path=/"+(((this.FPCConfig.domain!=""))?("; domain="+this.FPCConfig.domain):(""));
	var rc=this.dcsIsFpcSet(name,WT.co_f,cur,ses);
	if (rc!=0){
		WT.co_f=WT.vtvs=WT.vt_f_s=WT.vt_f_d=WT.vt_f_tlh=WT.vt_f_tlv="";
		if (typeof(this.vtid)=="undefined"){
			WT.vtid="";
		}
		WT.vt_f=WT.vt_f_a=rc;
    }
}
WebTrends.prototype.dcsIsOnsite=function(host){
	if (host.length>0){
	    host=host.toLowerCase();
	    if (host==window.location.hostname.toLowerCase()){
		    return true;
	    }
	    if (typeof(this.onsitedoms.test)=="function"){
		    return this.onsitedoms.test(host);
	    }
	    else if (this.onsitedoms.length>0){
		    var doms=this.dcsSplit(this.onsitedoms);
		    var len=doms.length;
		    for (var i=0;i<len;i++){
			    if (host==doms[i]){
			        return true;
			    }
		    }
	    }
	}
	return false;
}
WebTrends.prototype.dcsTypeMatch=function(pth, typelist){
	var type=pth.toLowerCase().substring(pth.lastIndexOf(".")+1,pth.length);
	var types=this.dcsSplit(typelist);
	var tlen=types.length;	
	for (var i=0;i<tlen;i++){
		if (type==types[i]){
			return true;
		}
	}
	return false;
}
WebTrends.prototype.dcsEvt=function(evt,tag){
	var e=evt.target||evt.srcElement;
	while (e&&e.tagName&&(e.tagName.toLowerCase()!=tag.toLowerCase())){
		e=e.parentElement||e.parentNode;
	}
	return e;
}
WebTrends.prototype.dcsNavigation=function(evt){
	var id="";
	var cname="";
	var elems=this.dcsSplit(this.navigationtag);
	var elen=elems.length;	
	var i,e,elem;
	for (i=0;i<elen;i++){
		elem=elems[i];
		if (elem.length){
			e=this.dcsEvt(evt,elem);
			id=(e.getAttribute&&e.getAttribute("id"))?e.getAttribute("id"):"";
			cname=e.className||"";
			if (id.length||cname.length){
				break;
			}
		}
	}
	return id.length?id:cname;
}
WebTrends.prototype.dcsBind=function(event,func){
	if ((typeof(func)=="function")&&document.body){
		if (document.body.addEventListener){
			document.body.addEventListener(event, func.wtbind(this), true);
		}
		else if(document.body.attachEvent){
			document.body.attachEvent("on"+event, func.wtbind(this));
		}
	}
}
WebTrends.prototype.dcsET=function(){
	var e=(navigator.appVersion.indexOf("MSIE")!=-1)?"click":"mousedown";
	this.dcsBind(e,this.dcsDownload);
	this.dcsBind(e,this.dcsOffsite);
	this.dcsBind("contextmenu",this.dcsRightClick);
}
WebTrends.prototype.dcsMultiTrack=function(){
	var args=dcsMultiTrack.arguments?dcsMultiTrack.arguments:arguments;
	if (args.length%2==0){
	    this.dcsSaveProps(args);
		this.dcsSetProps(args);
		var dCurrent=new Date();
		this.DCS.dcsdat=dCurrent.getTime();
		this.dcsFPC();
		this.dcsTag();
		this.dcsRestoreProps();
	}
}

WebTrends.prototype.dcsCleanUp=function(){
	this.DCS={};
	this.WT={};
	this.DCSext={};
	if (arguments.length%2==0){
		this.dcsSetProps(arguments);
	}
}
WebTrends.prototype.dcsSetProps=function(args){
	for (var i=0;i<args.length;i+=2){
		if (args[i].indexOf('WT.')==0){
			this.WT[args[i].substring(3)]=args[i+1];
		}
		else if (args[i].indexOf('DCS.')==0){
			this.DCS[args[i].substring(4)]=args[i+1];
		}
		else if (args[i].indexOf('DCSext.')==0){
			this.DCSext[args[i].substring(7)]=args[i+1];
		}
	}
}
WebTrends.prototype.dcsSaveProps=function(args){
	var i,x,key,param;
	if (this.preserve){
		this.args=[];
		for (i=0,x=0;i<args.length;i+=2){
			param=args[i];
			if (param.indexOf('WT.')==0){
				key=param.substring(3);
				this.args[x]=param;
				this.args[x+1]=this.WT[key]||"";
				x+=2;
			}
			else if (param.indexOf('DCS.')==0){
				key=param.substring(4);
				this.args[x]=param;
				this.args[x+1]=this.DCS[key]||"";
				x+=2;
			}
			else if (param.indexOf('DCSext.')==0){
				key=param.substring(7);
				this.args[x]=param;
				this.args[x+1]=this.DCSext[key]||"";
				x+=2;
			}
		}
	}
}
WebTrends.prototype.dcsRestoreProps=function(){
	if (this.preserve){
		this.dcsSetProps(this.args);
		this.args=[];
	}
}
WebTrends.prototype.dcsSplit=function(list){
	var items=list.toLowerCase().split(",");
	var len=items.length;
	for (var i=0;i<len;i++){
		items[i]=items[i].replace(/^\s*/,"").replace(/\s*$/,"");
	}
	return items;
}
// Code section for Track clicks to download links.
WebTrends.prototype.dcsDownload=function(evt){
	evt=evt||(window.event||"");
	if (evt&&((typeof(evt.which)!="number")||(evt.which==1))){
		var e=this.dcsEvt(evt,"A");
		if (e.href){
		    var hn=e.hostname?(e.hostname.split(":")[0]):"";
		    if (this.dcsIsOnsite(hn)&&this.dcsTypeMatch(e.pathname,this.downloadtypes)){
		        var qry=e.search?e.search.substring(e.search.indexOf("?")+1,e.search.length):"";
		        var pth=e.pathname?((e.pathname.indexOf("/")!=0)?"/"+e.pathname:e.pathname):"/";
		        var ttl="";
		        var text=document.all?e.innerText:e.text;
		        var img=this.dcsEvt(evt,"IMG");
		        if (img.alt){
			        ttl=img.alt;
		        }
		        else if (text){
			        ttl=text;
		        }
		        else if (e.innerHTML){
			        ttl=e.innerHTML;
		        }
		        this.dcsMultiTrack("DCS.dcssip",hn,"DCS.dcsuri",pth,"DCS.dcsqry",e.search||"","WT.ti","Download:"+ttl,"WT.dl","20","WT.nv",this.dcsNavigation(evt));
		    }
		}
	}
}
// Code section for Track right clicks to download links.
WebTrends.prototype.dcsRightClick=function(evt){
	evt=evt||(window.event||"");
	if (evt){
		var btn=evt.which||evt.button;
		if ((btn!=1)||(navigator.userAgent.indexOf("Safari")!=-1)){
			var e=this.dcsEvt(evt,"A");
			if ((typeof(e.href)!="undefined")&&e.href){
				if ((typeof(e.protocol)!="undefined")&&e.protocol&&(e.protocol.indexOf("http")!=-1)){
					if ((typeof(e.pathname)!="undefined")&&this.dcsTypeMatch(e.pathname,this.downloadtypes)){
						var pth=e.pathname?((e.pathname.indexOf("/")!=0)?"/"+e.pathname:e.pathname):"/";
						var hn=e.hostname?(e.hostname.split(":")[0]):"";
						this.dcsMultiTrack("DCS.dcssip",hn,"DCS.dcsuri",pth,"DCS.dcsqry","","WT.ti","RightClick:"+pth,"WT.dl","25");
					}
				}
			}
		}
	}
}
// Code section for Track clicks to links leading offsite.
WebTrends.prototype.dcsOffsite=function(evt){
	evt=evt||(window.event||"");
	if (evt&&((typeof(evt.which)!="number")||(evt.which==1))){
		var e=this.dcsEvt(evt,"A");
		if (e.href){
		    var hn=e.hostname?(e.hostname.split(":")[0]):"";
		    var pr=e.protocol||"";
		    if ((hn.length>0)&&(pr.indexOf("http")==0)&&!this.dcsIsOnsite(hn)){
			    var qry=e.search?e.search.substring(e.search.indexOf("?")+1,e.search.length):"";
			    var pth=e.pathname?((e.pathname.indexOf("/")!=0)?"/"+e.pathname:e.pathname):"/";
			    this.dcsMultiTrack("DCS.dcssip", hn, "DCS.dcsuri", pth, "DCS.dcsqry", this.trimoffsiteparams ? "" : qry, "DCS.dcsref", window.location, "WT.ti", "Offsite:" + hn + pth + (qry.length ? ("?" + qry) : ""), "WT.dl", "24", "WT.nv", this.dcsNavigation(evt));
		    }
		}
	}
}

WebTrends.prototype.dcsAdv=function(){
	if (this.trackevents&&(typeof(this.dcsET)=="function")){
		if (window.addEventListener){
			window.addEventListener("load",this.dcsET.wtbind(this),false);
		}
		else if (window.attachEvent){
			window.attachEvent("onload",this.dcsET.wtbind(this));
		}
	}
	this.dcsFPC();
}
WebTrends.prototype.dcsVar=function(){
	var dCurrent=new Date();
	var WT=this.WT;
	var DCS=this.DCS;
	WT.tz=parseInt(dCurrent.getTimezoneOffset()/60*-1)||"0";
	WT.bh=dCurrent.getHours()||"0";
	WT.ul=navigator.appName=="Netscape"?navigator.language:navigator.userLanguage;
	if (typeof(screen)=="object"){
		WT.cd=navigator.appName=="Netscape"?screen.pixelDepth:screen.colorDepth;
		WT.sr=screen.width+"x"+screen.height;
	}
	if (typeof(navigator.javaEnabled())=="boolean"){
		WT.jo=navigator.javaEnabled()?"Yes":"No";
	}
	if (document.title){
		if (window.RegExp){
			var tire=new RegExp("^"+window.location.protocol+"//"+window.location.hostname+"\\s-\\s");
			WT.ti=document.title.replace(tire,"");
		}
		else{
			WT.ti=document.title;
		}
	}
	WT.js="Yes";
	WT.jv=(function(){
		var agt=navigator.userAgent.toLowerCase();
		var major=parseInt(navigator.appVersion);
		var mac=(agt.indexOf("mac")!=-1);
		var ff=(agt.indexOf("firefox")!=-1);
		var ff0=(agt.indexOf("firefox/0.")!=-1);
		var ff10=(agt.indexOf("firefox/1.0")!=-1);
		var ff15=(agt.indexOf("firefox/1.5")!=-1);
		var ff20=(agt.indexOf("firefox/2.0")!=-1);
		var ff3up=(ff&&!ff0&&!ff10&!ff15&!ff20);
		var nn=(!ff&&(agt.indexOf("mozilla")!=-1)&&(agt.indexOf("compatible")==-1));
		var nn4=(nn&&(major==4));
		var nn6up=(nn&&(major>=5));
		var ie=((agt.indexOf("msie")!=-1)&&(agt.indexOf("opera")==-1));
		var ie4=(ie&&(major==4)&&(agt.indexOf("msie 4")!=-1));
		var ie5up=(ie&&!ie4);
		var op=(agt.indexOf("opera")!=-1);
		var op5=(agt.indexOf("opera 5")!=-1||agt.indexOf("opera/5")!=-1);
		var op6=(agt.indexOf("opera 6")!=-1||agt.indexOf("opera/6")!=-1);
		var op7up=(op&&!op5&&!op6);
		var jv="1.1";
		if (ff3up){
			jv="1.8";
		}
		else if (ff20){
			jv="1.7";
		}
		else if (ff15){
			jv="1.6";
		}
		else if (ff0||ff10||nn6up||op7up){
			jv="1.5";
		}
		else if ((mac&&ie5up)||op6){
			jv="1.4";
		}
		else if (ie5up||nn4||op5){
			jv="1.3";
		}
		else if (ie4){
			jv="1.2";
		}
		return jv;
	})();
	WT.ct="unknown";
	if (document.body&&document.body.addBehavior){
		try{
			document.body.addBehavior("#default#clientCaps");
			WT.ct=document.body.connectionType||"unknown";
			document.body.addBehavior("#default#homePage");
			WT.hp=document.body.isHomePage(location.href)?"1":"0";
		}
		catch(e){
		}
	}
	if (document.all){
		WT.bs=document.body?document.body.offsetWidth+"x"+document.body.offsetHeight:"unknown";
	}
	else{
		WT.bs=window.innerWidth+"x"+window.innerHeight;
	}
	WT.fv=(function(){
		var i,flash;
		if (window.ActiveXObject){
			for(i=15;i>0;i--){
				try{
					flash=new ActiveXObject("ShockwaveFlash.ShockwaveFlash."+i);
					return i+".0";
				}
				catch(e){
				}
			}
		}
		else if (navigator.plugins&&navigator.plugins.length){
			for (i=0;i<navigator.plugins.length;i++){
				if (navigator.plugins[i].name.indexOf('Shockwave Flash')!=-1){
					return navigator.plugins[i].description.split(" ")[2];
				}
			}
		}
		return "Not enabled";
	})();
	WT.slv=(function(){
		var slv="Not enabled";
		try{     
			if (navigator.userAgent.indexOf('MSIE')!=-1){
				var sli = new ActiveXObject('AgControl.AgControl');
				if (sli){
					slv="Unknown";
				}
			}
			else if (navigator.plugins["Silverlight Plug-In"]){
				slv="Unknown";
			}
		}
		catch(e){
		}
		if (slv!="Not enabled"){
			var i,m,M,F;
			if ((typeof(Silverlight)=="object")&&(typeof(Silverlight.isInstalled)=="function")){
				for(i=9;i>0;i--){
					M=i;
					if (Silverlight.isInstalled(M+".0")){
							break;
					}
					if (slv==M){
						break;
					}
				}
				for (m=9;m>=0;m--){
					F=M+"."+m;
					if (Silverlight.isInstalled(F)){
						slv=F;
						break;
					}
					if (slv==F){
						break;
					}
				}
			}
		}
		return slv;
	})();
	if (this.i18n){
		if (typeof(document.defaultCharset)=="string"){
			WT.le=document.defaultCharset;
		} 
		else if (typeof(document.characterSet)=="string"){
			WT.le=document.characterSet;
		}
		else{
			WT.le="unknown";
		}
	}
	WT.tv="9.4.0";
	WT.sp=this.splitvalue;
	WT.dl="0";
	WT.ssl=(window.location.protocol.indexOf('https:')==0)?"1":"0";
	DCS.dcsdat=dCurrent.getTime();
	DCS.dcssip=window.location.hostname;
	DCS.dcsuri=window.location.pathname;
	WT.es=DCS.dcssip+DCS.dcsuri;
	if (window.location.search){
		DCS.dcsqry=window.location.search;
	}
	if (DCS.dcsqry){
		var dcsqry=DCS.dcsqry.toLowerCase();
		var params=this.paidsearchparams.length?this.paidsearchparams.toLowerCase().split(","):[];
		for (var i=0;i<params.length;i++){
			if (dcsqry.indexOf(params[i]+"=")!=-1){
				WT.srch="1";
				break;
			}
		}
	}
	if ((window.document.referrer!="")&&(window.document.referrer!="-")){
		if (!(navigator.appName=="Microsoft Internet Explorer"&&parseInt(navigator.appVersion)<4)){
			DCS.dcsref=window.document.referrer;
		}
	}

	DCS["dcscfg"] = this.TPCConfig.cfgType;
	
}
WebTrends.prototype.dcsEscape=function(S, REL){
	if (REL!=""){
		S=S.toString();
		for (var R in REL){
 			if (REL[R] instanceof RegExp){
				S=S.replace(REL[R],R);
 			}
		}
		return S;
	}
	else{
		return escape(S);
	}
}
WebTrends.prototype.dcsA=function(N,V){
	if (this.i18n&&(this.exre!="")&&!this.exre.test(N)){
		if (N=="dcsqry"){
			var newV="";
			var params=V.substring(1).split("&");
			for (var i=0;i<params.length;i++){
				var pair=params[i];
				var pos=pair.indexOf("=");
				if (pos!=-1){
					var key=pair.substring(0,pos);
					var val=pair.substring(pos+1);
					if (i!=0){
						newV+="&";
					}
					newV+=key+"="+this.dcsEncode(val);
				}
			}
			V=V.substring(0,1)+newV;
		}
		else{
			V=this.dcsEncode(V);
		}
	}
	return "&"+N+"="+this.dcsEscape(V, this.re);
}
WebTrends.prototype.dcsEncode=function(S){
	return (typeof(encodeURIComponent)=="function")?encodeURIComponent(S):escape(S);
}
WebTrends.prototype.dcsCreateImage=function(dcsSrc){
	if (document.images){
		this.images[this.index]=new Image();
		this.images[this.index].src=dcsSrc;
		this.index++;
	}
}
WebTrends.prototype.dcsMeta=function(){
	var elems;
	if (document.documentElement){
		elems=document.getElementsByTagName("meta");
	}
	else if (document.all){
		elems=document.all.tags("meta");
	}
	if (typeof(elems)!="undefined"){
		var length=elems.length;
		for (var i=0;i<length;i++){
			var name=elems.item(i).name;
			var content=elems.item(i).content;
			var equiv=elems.item(i).httpEquiv;
			if (name.length>0){
				if (name.toUpperCase().indexOf("WT.")==0){
					this.WT[name.substring(3)]=content;
				}
				else if (name.toUpperCase().indexOf("DCSEXT.")==0){
					this.DCSext[name.substring(7)]=content;
				}
				else if (name.toUpperCase().indexOf("DCS.")==0){
					this.DCS[name.substring(4)]=content;
				}
			}
		}
	}
}
WebTrends.prototype.dcsTag=function(){
	if (document.cookie.indexOf("WTLOPTOUT=")!=-1){
		return;
	}
	var WT=this.WT;
	var DCS=this.DCS;
	var DCSext=this.DCSext;
	var i18n=this.i18n;
	var P="http"+(window.location.protocol.indexOf('https:')==0?'s':'')+"://"+this.domain+(this.dcsid==""?'':'/'+this.dcsid)+"/dcs.gif?";
	if (i18n){
		WT.dep="";
	}
	for (var N in DCS){
 		if (DCS[N]&&(typeof DCS[N]!="function")){
			P+=this.dcsA(N,DCS[N]);
		}
	}
	for (N in WT){
		if (WT[N]&&(typeof WT[N]!="function")){
			P+=this.dcsA("WT."+N,WT[N]);
		}
	}
	for (N in DCSext){
		if (DCSext[N]&&(typeof DCSext[N]!="function")){
			if (i18n){
				WT.dep=(WT.dep.length==0)?N:(WT.dep+";"+N);
			}
			P+=this.dcsA(N,DCSext[N]);
		}
	}
	if (i18n&&(WT.dep.length>0)){
		P+=this.dcsA("WT.dep",WT.dep);
	}
	if (P.length>2048&&navigator.userAgent.indexOf('MSIE')>=0){
		P=P.substring(0,2040)+"&WT.tu=1";
	}
	this.dcsCreateImage(P);
	this.WT.ad="";
}
WebTrends.prototype.dcsDebug=function(){
	var t=this;
	var i=t.images[0].src;
	var q=i.indexOf("?");
	var r=i.substring(0,q).split("/");
	var m="<b>Protocol</b><br><code>"+r[0]+"<br></code>";
	m+="<b>Domain</b><br><code>"+r[2]+"<br></code>";
	m+="<b>Path</b><br><code>/"+r[3]+"/"+r[4]+"<br></code>";
	m+="<b>Query Params</b><code>"+i.substring(q+1).replace(/\&/g,"<br>")+"</code>";
	m+="<br><b>Cookies</b><br><code>"+document.cookie.replace(/\;/g,"<br>")+"</code>";
	if (t.w&&!t.w.closed){
		t.w.close();
	}
	t.w=window.open("","dcsDebug","width=500,height=650,scrollbars=yes,resizable=yes");
	t.w.document.write(m);
	t.w.focus();
}
WebTrends.prototype.dcsCollect=function(){
    if (this.enabled){
        this.dcsVar();
        this.dcsMeta();
        this.dcsAdv();
        if (typeof(this.dcsCustom)=="function"){
			this.dcsCustom();
        }
        this.dcsTag();
    }
}

function dcsMultiTrack(){
	if (typeof(_tag)!="undefined"){
		return(_tag.dcsMultiTrack());
	}
}

function dcsDebug(){
	if (typeof(_tag)!="undefined"){
		return(_tag.dcsDebug());
	}
}

Function.prototype.wtbind = function(obj){
	var method=this;
	var temp=function(){
		return method.apply(obj,arguments);
	};
	return temp;
}