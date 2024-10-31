Myf.form_check=function(){
var idc= "form-description";
var ide= "err_";
var idf= "errorMessage";
var idn= "field-";
var idd= "field_";
var msg= "";
	for(var i=0;i<MY.chkchk.length;i++){          // 必須チェック
		var use= "";
		var pm= MY.chkchk[i].split("｜");         // idn｜お名前
		if( pm[2]=="" ){
			if( document.getElementById(idd+pm[0]).value != "" ) use="1";
		} else {
			if( pm[2]=="R" ) pm[2]="";
			var da= document.getElementsByName(idn+pm[0]+pm[2]);
			for(var j=0;j<da.length;j++) {if(da[j].checked)use="1";}
		}
		if( use == "1" ) Myf.formerrset(idd+pm[0],ide+pm[0], "", "red", "");
		else  {msg+=" "; Myf.formerrset(idd+pm[0],ide+pm[0], "<br>"+pm[1]+"は、入力必須です。\n", "red", "");}
	}
	for(var i=0;i<MY.checks.length;i++){          // パターンチェック
		var pm= MY.checks[i].split("｜");         // idn｜お名前｜パターン
		var da= document.getElementsByName(idn+pm[0]);
		for(var j=0;j<da.length;j++) {
			var dt= da[j].value;
			if( dt!="" ){
				var mt= new RegExp(pm[2]);
				if(dt.match(mt)) Myf.formerrset(idd+pm[0],ide+pm[0], "", "red", "");
				else  {msg+=" "; Myf.formerrset(idd+pm[0],ide+pm[0], "<br>"+pm[1]+"を、ご確認ください。\n", "red", "");}
	}	}	}
	if( msg=="" ) return true;
	else {
		var da= document.getElementsByClassName(idc);
		for(var j=0;j<da.length;j++){
			var dat= da[j].innerHTML;
			if( dat.match(/<p>/) ) {da[j].id= idf;}
		}
		if( document.getElementById(idf) ){
			var dat= document.getElementById(idf);
			msg= msg.replace("\n", "<br />");
			dat.innerHTML= "【入力エラーです】内容を確認して再度送信してください。<br />"+ msg;
		}
		else alert(msg);
		return false;
	}
}
Myf.formerrset=function(idwa,idms,mesg,iro, opt){
	if( document.getElementById(idwa) && opt=="" ){
		var obj= document.getElementById(idwa);
		if( mesg=="" ) {obj.style.border= "1px solid #ccc";}
		else           {obj.style.border= "1px solid red"; obj.style.outline="0";}
	}
	if( document.getElementById(idms) ){
		var obj= document.getElementById(idms);
		obj.innerHTML= mesg;
		obj.style.fontSize= '90%';
		if( iro!="" ) obj.style.color= iro;
	}
};
Myf.emcount=function(no){
	var pm= MY.txtcnt[no].split("｜");            // idn｜maxlength
	var idn= pm[0];
	var max= pm[1];
	var iro= "";
	var len= document.getElementById(idn).value.length;
	     if( len == 0 )   var ans= "(zzz文字以内)".replace(/zzz/g, max); // 初期状態
	else if( len == max ) {var ans= "(-FULL-)"; iro="red";} // 一杯
	else if( len >  max ) {var ans= "(*over*)"; iro="red";} // over
	else {
		var zan= max - len;
		var ans= "(残り yyy/zzz文字)".replace(/yyy/g, zan); // 残文字数
		ans= ans.replace(/zzz/g, max);
	}
	var eid= idn.replace("field_","err_");
	Myf.formerrset(idn,eid, "<br />"+ans, iro, "1");
};
