<?php
if( !defined('ABSPATH') ) exit;

//first処理
	$pwd= get_option(myformjp_P);                 // システム固有情報の取得
	if( !$pwd ){                                  // first初期化（最初の1回のみの処理）
		$temp= "t1".myformjp_str(10);
		$pwd= array();
		$pwd['myform_pwd']= myformjp_str(15);
		$pwd['myform_key']= myformjp_str(15);
		$pwd['myform_tmp']= $temp;
		update_option(myformjp_P, $pwd);          // 新規に追加する
		if( !file_exists(myformjp_PLUGIN_DIR.$temp) ) {mkdir(myformjp_PLUGIN_DIR.$temp);}
		$array= array();
//		$array[myformjp_C]=   myformjp_PRC;       // CSS定義(myformjp_css)
		$array[myformjp_M]=   myformjp_PRM;       // メール情報定義(myformjp_mail)
		$array[myformjp_S]=   myformjp_PRS;       // フォーム情報定義(myformjp_sqlite3)
		$array['myformjp_define[contact]']=myformjp_DEF; // 稼働環境定義(myformjp_define)
		foreach($array as $key => $fname) {myformjp_fileup( $key,$fname);} // 初期取り込み
	}
	else{                                         // cssの同期化処理
		$temp= isset($pwd['myform_tmp']) ?  $pwd['myform_tmp'] : "t1".myformjp_str(10);
		if( !file_exists(myformjp_PLUGIN_DIR.$temp) ) {mkdir(myformjp_PLUGIN_DIR.$temp);}
	}
	define("myformjp_temp", $temp);


function myformjp_fileup($keyword,$fname, $new=""){
	if( $keyword == myformjp_S ){
		if( file_exists($fname) ) {copy($fname, myformjp_UPS);}
	}
	else{
		$data= "";
		if( !empty($new) ) $data= $new;           // 生data
		else
		if( file_exists($fname) ) {$data= @file_get_contents($fname);}
		if( !empty($data) ){
			$data= myformjp_load_esc($data);
			update_option($keyword, $data);       // 追加/更新
	}	}
}
function myformjp_str($leng){
	$ans= null;
	$str= array_merge( range('a','z'),range('0','9'),range('A','Z') );
	for( $i=0;$i<$leng;$i++ ) {$ans.= $str[rand(0,count($str)-1)];}
	return $ans;
}
function myformjp_load_esc($data){
	$res= str_replace("<?php","", $data);
	$res= str_replace("?>",   "", $res);
	$res= htmlspecialchars($res,ENT_QUOTES,'UTF-8');
	return serialize($res);
}
function myformjp_option($keyw){
	$ans= array();
	$data= get_option($keyw);                     // get option data
	$data= unserialize($data);
	$data= html_entity_decode($data,ENT_QUOTES,'UTF-8');
	$data= str_replace("if( !defined('ABSPATH') ) exit;","", $data);
	$data= str_replace("<?php","", $data);
	$data= str_replace("?>",   "", $data);
	$line= explode('";', $data);
	foreach($line as $i => $dat){
		$dat= trim($dat);
		$da= explode("=", $dat);                  // $abc= "def"
		if( substr($dat,0,1)=="$" && count($da) == 2 ){
			$d1= trim($da[0]);
			$d2= trim($da[1]);
			if( strlen($d1)>=2 ) {$d0=substr($d1,1); $ans[$d0]=trim($d2,'"');}
	}	}
	return $ans;
}
function myformjp_tables(){
	global $dbio;
	$myformjp_table= array();
	$dbio->myformjp_DBopen();
	$db_data= $dbio->myformjp_DBall("formlist_sys","","hyymd");
	foreach($db_data as $i => $rows){
		if( $i < 5 ){                             // max5個
			$code= $rows->code;
			$name= $rows->name;
			$myformjp_table[]= $code;             // 対象フォーム一覧
	}	}
	$dbio->myformjp_DBclose();
	return $myformjp_table;
}
function myformjp_fld(){
return array("homeurl", "topurl", "system", "company", "title", "trformat", "cssformat",
		"formname", "formname2", "formname3", "description", "sitekey", "secretkey");
}
function myformjp_D($da){echo esc_html($da);}
?>
