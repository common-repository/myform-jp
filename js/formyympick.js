function Yym(){                                   // yyyy/mm築年などの入力用
this.idn;
this.px= "x_";
this.py= "y_";
this.pm= "m_";
} var YA= new Yym;
Yym.entry=function(yymd,idn){
	YA.idn= idn;
	var date=new Date(); var wk1=date.getFullYear(); var wk2=date.getMonth()+1;
	var wk= yymd.split("/");
	if( yymd!="" && wk.length==2 ) {wk1=Yym.bef0(wk[0],4); wk2=Yym.bef0(wk[1],2);}
	Yym.ymdisp(wk1,wk2);
};
Yym.ymdisp=function(yyyy,mmmm){
var mesg="&nbsp;"+Yym.select(YA.py+YA.idn, yyyy, Yym.yytable() );
	mesg+=        Yym.select(YA.pm+YA.idn, mmmm, Yym.mmtable() );
	mesg+='<input type="button" value="更新" style="font-size:14px" onClick="';
	mesg+='javascript:Yym.dym_select();Yym.close();">&nbsp;';
	var psx= jQuery('#'+YA.idn).offset().left;
	var psy= jQuery('#'+YA.idn).offset().top + jQuery('#'+YA.idn).outerHeight();
	var idn= YA.px + YA.idn;
	if( !document.getElementById(idn) ){
		var obj= document.createElement('div');  obj.id=idn;
		var objBody= document.getElementsByTagName("body").item(0);
		objBody.appendChild(obj);
	}
	var obj= document.getElementById(idn);
	obj.style.position= 'absolute';
	obj.style.backgroundColor= "#0099cc";
	obj.style.lineHeight= "1.5";
	obj.style.fontSize= '14px';
	obj.style.left= Number(psx) +"px";
	obj.style.top=  Number(psy) +"px";
	obj.innerHTML= mesg;
};
Yym.dym_select=function(){                        // yyyy/mm
	var yy= document.getElementById(YA.py+YA.idn).value;
	var mm= document.getElementById(YA.pm+YA.idn).value;
	var ans= (yy!="" && mm!="") ?  yy + "/" + Yym.bef0(mm,2) : "";
	document.getElementById(YA.idn).value= ans;
};
Yym.yytable=function(){
	var ans= new Array();
	var date=new Date();
	var eyy= Number( date.getFullYear() ) + 20;
	for( var i=eyy;i>=1912;i-- ) {
//			 if( i >= 2019 ) {var g="清明"; var j=i-2018;} // 2019-清明元年
			 if( i >= 1989 ) {var g="平成"; var j=i-1988;} // 1989-平成元年
		else if( i >= 1926 ) {var g="昭和"; var j=i-1925;} // 1926-昭和元年
		else if( i >= 1912 ) {var g="大正"; var j=i-1911;} // 1912-大正元年
		else                 {var g="明治"; var j=i-1867;} // 1911-明治44年
		if( j == 1 ) j="元";
		var key= Number(10000-i);
		ans[key]= i + "("+ g + j +")年";
	}
	return ans;
};
Yym.mmtable=function(){var ans=new Array();  for(var i=1;i<=12;i++){ans[i]= i +"月";}  return ans;};
Yym.bef0=function(dat,max){return ("0000"+dat).slice(-max);};
Yym.select=function(name, data, table){
	var ans= '<select name="'+ name +'" id="'+ name +'" style="font-size:14px;">';
	ans+= '<option value="">---選択</option>';
	for(var key in table){
		var i= (key>100) ?  Number(10000 - key) : key;
		var sel= (Number(data)==i) ?  " selected" : "";
		ans+= '<option value="'+ i +'"'+ sel +'>'+ table[key] +'</option>';
	}
	return ans + '</select>';
};
Yym.close=function(){
	if( document.getElementById(YA.px+YA.idn) ){
		var obj= document.getElementById(YA.px+YA.idn);  obj.innerHTML="";
		var objBody= document.getElementsByTagName("body").item(0);
		objBody.removeChild(obj);
	}
};
