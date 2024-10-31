Myf.focus=function(ee){
	ee.focus();
	var size= ee.value.length;                    // 入力位置まで移動
	if( ee.createTextRange ) {                    // IE,Opera
		var range= ee.createTextRange();
		range.move('character', size);
		range.select();
	}
	else if( ee.setSelectionRange ) {             // Firefox
		ee.setSelectionRange(size, size);
	}
};
Myf.setime=function(){
	for(var i=0;i<MY.stmode.length;i++){
		var idn= MY.stmode[i];
		if( document.getElementById(idn) ){
			document.getElementById(idn).style.imeMode="disabled";
	}	}
};
Myf.emcount_init=function(){
	for(var i=0;i<MY.txtcnt.length;i++){          // event設定
		var pm= MY.txtcnt[i].split("｜");         // idn｜maxlength
		if( i > 5 ) alert("textarea max 6");
		else
		if( document.getElementById(pm[0]) ){
			var obj= document.getElementById(pm[0]);
			     if( i==0 ) Myf.aEv(obj,"keyup",Myf.ev0);
			else if( i==1 ) Myf.aEv(obj,"keyup",Myf.ev1);
			else if( i==2 ) Myf.aEv(obj,"keyup",Myf.ev2);
			else if( i==3 ) Myf.aEv(obj,"keyup",Myf.ev3);
			else if( i==4 ) Myf.aEv(obj,"keyup",Myf.ev4);
			else if( i==5 ) Myf.aEv(obj,"keyup",Myf.ev5);
	}	}
};
Myf.ev0=function(){Myf.emcount(0);};
Myf.ev1=function(){Myf.emcount(1);};
Myf.ev2=function(){Myf.emcount(2);};
Myf.ev3=function(){Myf.emcount(3);};
Myf.ev4=function(){Myf.emcount(4);};
Myf.ev5=function(){Myf.emcount(5);};
Myf.aEv=function(obj,type,mod){
	if(obj.addEventListener){obj.addEventListener(type,mod,false);}
	else if(obj.attachEvent){obj.attachEvent('on'+type,mod);}
};
if( MY.focus!="" ) Myf.focus(document.getElementById(MY.focus));
Myf.setime();
Myf.emcount_init();
if( MY.kanatb.length>0 ) {Rub.entry();}
