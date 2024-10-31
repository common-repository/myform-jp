<?php
if( !defined('ABSPATH') ) exit;
class myformjp_psystem {
	var $ver= "1.29";
	var $datax="";
function myformjp_psystem_base(){
}
function myformjp_view($member,$viewopt, $inputdata=""){
	global $pierre,$sys_target;
//	require(myformjp_DEF);
	$fld= myformjp_option(myformjp_D);
	foreach((array)$fld as $key => $data) {$$key=$data;}
	include(myformjp_PLUGIN_DIR.$member);
}
function myformjp_set_data($datax){$this->datax=$datax;}
function myformjp_get_data(){return $this->datax;}
function myformjp_get_postdata($tablen){
	global $dbio;
	$array= array();
	$db_data= $dbio->myformjp_get_field();        // 項目一覧
	foreach($db_data as $i => $rows){
		$field= $rows->name;
		if( $field == "id" || $field == "tyymd" ){;} // idはskip
		else {
			$idn= myformjp_F.$field;
			$data= $this->myformjp_post($idn);
			if( strlen($data)>5 && substr($data,0,5)=="@#+@#" ) {$wk=substr($data,5); $data= explode("｜",$wk); $idn.="[]";}
			$array[$idn]= $data;
	}	}
//print_r($array);
	return $array;
}
function myformjp_post($keys){
	$data= isset($_POST[$keys]) ?  $_POST[$keys] : "";
	if( get_magic_quotes_gpc() ) $data= stripslashes($data);
	if( is_array($data) ){
		$ans= array();
		foreach($data as $i => $dat) {$ans[]= htmlspecialchars($dat,ENT_QUOTES,'UTF-8');}
	}
	else $ans= htmlspecialchars($data,ENT_QUOTES,'UTF-8');
	return $ans;
}
function myformjp_cstyle($viewopt){
	global $sys_cssformat;
	$ans= "";
	if( $viewopt=="" || $viewopt=="2" ){
		include(myformjp_PRC);
		if( $sys_cssformat=="1" ) {;}
		else
		if( $sys_cssformat=="2" && isset($cssadd_2) ) $ans=$cssadd_2;
		else
		if( $sys_cssformat=="3" && isset($cssadd_3) ) $ans=$cssadd_3;
		if( !empty($ans) ){
			$ans= htmlspecialchars($ans,ENT_QUOTES,'UTF-8');
			$ans= '<style type="text/css">'.$ans."\n</style>\n";
	}	}
	return $ans;
}
function myformjp_name_search($table,$keyw="", $tablen="idlist_sys"){ // 上位でopen
	global $dbio;
	$name= $keyw;
	$dbio->myformjp_DBopen();
	$where= "d_tables='$table' and d_idkey like '".$keyw."%'";
	$db_data= $dbio->myformjp_DBall($tablen,$where,"");
	if( count($db_data) == 1 ){
		foreach($db_data as $i => $rows){$name= $rows->d_idkey;}
	}
	return $name;
}
function myformjp_DBdata($tablen,$viewopt){       // form本体の処理
	global $dbio,$sys_trformat;
$filemax= '<input type="hidden" name="MAX_FILE_SIZE" value="2048000">';
	if( $sys_trformat=="1" ) include("myformjp_trformat1.php");
	else                     include("myformjp_trformat2.php");
	$kanakey= 'kana_';                            // 自動カナ・キーワード
	$ans= "";
	$wgroups="";
	$typetb= $this->myformjp_get_type_table();
	$preftb= $this->myformjp_get_prefect_table();
	$ptrnt=  $this->myformjp_get_pattern_table();
	$idtbl= array("d_tables","d_idkey","d_type","d_name","d_title","d_placeholder","d_hissu","d_checks",
		"d_befores","d_afters","d_option","d_size","d_line","d_maxsize","d_groups","d_class","d_use");
	$focus="";                                    // focus(最初の項目)
	$attacharray= array();                        // 添付ファイル
	$captcha=array();                             // captcha用
	$chkchk= array();                             // checkboxチェック用
	$checks= array();                             // dataチェック用
	$stmode= array();                             // セットimemode用
	$txtcnt= array();                             // 文字数カウント用
	$komktb= array();                             // id一覧
	$kanatb= array();                             // 自動カナ変換
	$zipuse= "";                                  // 1:郵便番号有り
	$yympick="";                                  // 和暦年月
	$emailauto="";                                // email補完
	$postdata= $this->myformjp_get_data();
//print_r($postdata);
	$dbio->myformjp_DBopen();
	$db_data= $dbio->myformjp_get_field();        // 項目一覧
//echo($tablen."##");
//print_r($db_data);
	foreach($db_data as $i => $rows){
		$field= $rows->name;
		$rowx= $this->myformjp_get_idtable($tablen,$field); // 詳細定義情報を取得
		foreach($idtbl as $d => $da){$$da= htmlspecialchars_decode($rowx->$da);}
		if( $field == "id" || $d_use == "1" ){;}  // idはskip
		else {
//echo $field."@@<br>";
			$idkey=  $d_idkey;
			$type=   $d_type;
			$hissu=  $d_hissu;
			$groups= $d_groups;
			$idkeyc= ($type=="C") ?  $idkey."[]" : $idkey;
			$idn=    "field-".$idkeyc;            // field-xxxxx
			$idd=    "field_".$idkey;             // field_xxxxx
			$komktb[$idkey]= $idd;
			if( strncmp($idkey,$kanakey,5)==0 ){  // 自動カナ変換
				$wkey= str_replace($kanakey,"", $idkey);
				if( isset($komktb[$wkey]) ){
					$widd= $komktb[$wkey];
					$kanatb[]= '"'.$widd.'｜'.$idd.'｜'.'1"'; // field_name｜field_kana_name｜1:カタカナ
			}	}
			if( $type == "P" ) $optable= $preftb;      // 都道府県
			else               $optable= $this->myformjp_optable($d_option); // optionテーブル
			$id1= "field-".$idkey;
			$id2= $id1."[]";
			if( isset($postdata[$id1]) )  $selected= $postdata[$id1];
			else
			if( isset($postdata[$id2]) )  $selected= $postdata[$id2];
			else  $selected= "";
//echo $viewopt.":".$idkey.":".$selected."<br>";
			if( $type == "98" ){;}                // 管理用日時
			else
			if( $type == "97" ){$captcha[]=$idd;} // captcha認証
			else
// メール送信用データ部
			if( $viewopt == "s" && $type == "6" && !empty($selected) ) $attacharray[]= $selected; // file添付
			else
			if( $viewopt == "s" ){
				$selected= $this->myformjp_selectdisp($type,$selected,$optable);
				$dbu= "　".$d_befores.$selected.$d_afters;
				$dbu= str_replace("<br>",  "\n", $dbu);
				$dbu= str_replace("<br />","\n", $dbu);
				if( !empty($wgroups) && empty($groups) ) $ans.= "\n\n";
				if( !empty($wgroups) && $wgroups==$groups ) $ans.= $dbu; // 確認
				else {
					if( !empty($groups) ) {$nl= "";}
					else
					if( $dbu=="　" ) $nl= "\n";
					else  $nl= "\n\n";
					$ans.= $d_title."：\n".$dbu.$nl;
				}
				$wgroups= $groups;
			}
			else{
//入力フィールドの生成
				$array= array();                  // 追加オプション
				$array["id"]= $idd;
				if( !empty($d_class) ) $array["class"]= $d_class;
				if( !empty($hissu) )   $array["required"]= "";
				if( $type == "R" ) $input= $this->myformjp_radio_gen($idn,$selected,$optable,$array);
				else
				if( $type == "C" ) $input= $this->myformjp_check_gen($idn,$selected,$optable,$array);
				else {
					$inptyp=$this->myformjp_inptyp_gen($type); //input/select/textarea
					$typec= $this->myformjp_type_gen($type, $typetb); //type="text"
					$size=  $this->myformjp_size_gen($type,$d_size,$d_line,$d_maxsize);
					$class= $this->myformjp_svalue("class",$d_class);
					$value= ($type=="5" || $type=="A") ?  "" : $this->myformjp_svalue("value",$selected);
					$placeh= empty($d_placeholder) ?  "" : $this->myformjp_svalue("placeholder",$d_placeholder);
					$required= empty($hissu) ?  "" : " required";
					$input= str_replace("{input}",   $inptyp,   $cdata2);
					$input= str_replace("{type}",    $typec,    $input);
					$input= str_replace("{idn}",     $idn,      $input);
					$input= str_replace("{idd}",     $idd,      $input);
					$input= str_replace("{class}",   $class,    $input);
					$input= str_replace("{val}",     $value,    $input);
					$input= str_replace("{size}",    $size,     $input);
					$input= str_replace("{placeh}",  $placeh,   $input);
					$input= str_replace("{required}",$required, $input);
					if( $type=="6" ) {$input= $filemax.$input;} // 添付するMAX_FILE_SIZE
				}
//データ部の生成
				if( $viewopt=="2" ){
					$texta= "";
					$input= $this->myformjp_selectdisp($type,$selected,$optable);
				}
				else
	if( $type=="5" || $type=="P" ) $texta= $this->myformjp_select_option($selected,$optable,$d_title)."</select>";
				else
				if( $type == "A" ) $texta= $selected."</textarea>";
				else  $texta= "";
				$inplinen= $inpline;
				if( $viewopt != "2" ) $inplinen.= $cdatas; // <span>error_message_field</span>
				$dbu= str_replace("{input}",   $input,    $inplinen);
				$dbu= str_replace("{befores}", $d_befores,$dbu);
				$dbu= str_replace("{textarea}",$texta,    $dbu);
				$dbu= str_replace("{afters}",  $d_afters, $dbu);
				$dbu= str_replace("{idkey}",   $idkey,    $dbu);
				if( !empty($wgroups) && empty($groups) ) $ans.= $tdtr;
				if( !empty($wgroups) && $wgroups==$groups ) $ans.= $dbu; // データ部のみ出力
				else {
					if( empty($wgroups) && !empty($ans) ) $ans.= $tdtr;
					$hissuw= empty($hissu) ?  "" : '<span class="required">必須</span>';
					$da= str_replace("{inpline}", $dbu,      $cdata1);
					$da= str_replace("{IDN}",ucwords($idkey),$da);
					$da= str_replace("{title}",   $d_title,  $da);
					$da= str_replace("{hissu}",   $hissuw,   $da);
					$ans.= $da;
					$wgroups= $groups;
				}
				if( $type=="U" )  $zipuse= "1";   // 郵便番号欄？
				if( $type=="11" ) $yympick= $idd; // 和暦年月判定
				if( $type=="53" ) $emailauto=$idd;// email補完判定
				if( $type=="51" || $type=="52" || $type=="53" ) $stmode[]= '"'.$idd.'"';
//必須チェック処理
				if( $hissu=="1" ){                // 必須チェック
					if( $type=="C" ) $mark= "[]";
					else
					if( $type=="R" ) $mark= "R";
					else             $mark= "";
					$chkchk[]= '"'.$idkey.'｜'.$d_title.'｜'.$mark.'"';
				}
//入力チェック処理等
if( empty($focus) && $type!="C" && $type!="R" && $type!="P" &&
		$type!="5" && $type!="6" && $type!="11" && $type!="54" && $type!="57" && $type!="98" && $type!="99" ) $focus= $idd;
				if( !empty($d_maxsize) && $type == "A" ) $txtcnt[]= '"'.$idd.'｜'.$d_maxsize.'"';
				$checkx= $d_checks;               // パターンによる入力チェック
				if( empty($checkx) && isset($ptrnt[$type]) ) $checkx= $ptrnt[$type];
				if( !empty($checkx) ){                          // パターンcheck
					$checks[]= '"'.$idkey.'｜'.$d_title.'｜'.trim($checkx,"/").'"';
		}	}	}
	}
	if( ($viewopt=="" || $viewopt=="2") && empty($wgroups) && !empty($ans) ) $ans.= $tdtr;
	if( $viewopt == "" ){                         // 入力時のみ
		if( count($captcha) > 0 ) {
			$ans.= '<script src="https://www.google.com/recaptcha/api.js"></script>';
		}
		if( !empty($zipuse) ){                        // 郵便番号有り？
			wp_enqueue_script("myform",  "https://zipaddr.github.io/myform.js") ;
			wp_enqueue_script("zipaddrx","https://zipaddr.github.io/zipaddrx.js") ;
		}
		if( !empty($yympick) ){
			$fname= myformjp_PLUGIN_URL."/js/formjquery.dat";
			$prm= @file_get_contents($fname);     // GET_js
			$prm= str_replace("[yympicker]", $yympick, $prm); // 追加js
			$ans.= $prm;
			wp_enqueue_script("formyympick", myformjp_PLUGIN_URL."/js/formyympick.js");
		}
		if( !empty($emailauto) ){                 // email補完
			wp_enqueue_script("email-auto", myformjp_PLUGIN_URL."/js/jquery.email-autocomplete.min.js");
			$fname= myformjp_PLUGIN_URL."/js/emailjquery.dat";
			$prm= @file_get_contents($fname);     // GET_js
			$prm= str_replace("[######]", $emailauto, $prm); // 追加js
			$ans.= $prm;
		}
		if( count($kanatb)>0 ) wp_enqueue_script("autoruby2", myformjp_PLUGIN_URL."/js/autoruby2.js"); // 自動カナ変換
		$fname= myformjp_PLUGIN_URL."/js/formdata.dat";
		$prm= @file_get_contents($fname);         // GET_js
		$cc= implode(",", $chkchk);
		$ck= implode(",", $checks);
		$ie= implode(",", $stmode);
		$ct= implode(",", $txtcnt);
		$kn= implode(",", $kanatb);
		$prm= str_replace("#@#@#",$focus,$prm);
		$prm= str_replace("#####", $cc,  $prm);   // checkbox必須チェック
		$prm= str_replace("@@@@@", $ck,  $prm);   // パターン・チェック
		$prm= str_replace("#%#%#", $ie,  $prm);   // imemode
		$prm= str_replace("%%%%%", $ct,  $prm);   // サイズ表示
		$prm= str_replace("@%@%@", $kn,  $prm);   // カナ変換
		$ans.= $prm;
		wp_enqueue_script("forminit", myformjp_PLUGIN_URL."/js/forminit.js");
		wp_enqueue_script("formcheck",myformjp_PLUGIN_URL."/js/formcheck.js",array(),myformjp_VERS);
	}
	$array= array();
	$array[0]= $ans;                              // html
	$array[1]= $attacharray;                      // upファイル
	$array[2]= $captcha;                          // captcha認証
	$dbio->myformjp_DBclose();
	return $array;
}
function myformjp_get_idtable($tablen,$field, $tablet="idlist_sys"){
	global $dbio;
	$dbio->myformjp_DBopen();
	$where= "d_tables='$tablen' and d_idkey='$field' and (d_use is null || d_use!='1')";
	$db_datx= $dbio->myformjp_DBall($tablet,$where,"");
	if( count($db_datx) != 1 ) exit(count($db_datx)."SystemError".$where);
	$rowx= $db_datx[0];
	return $rowx;
}
function myformjp_optable($option="",$sepa="|"){
	$ans= array();
	if( !empty($option) ){
		$table= explode($sepa, $option);
		foreach($table as $i => $dat) {$ans[($i+1)]= $dat;}
	}
	return $ans;
}
function myformjp_select_option($selected,$table,$title, $opt=""){
	$ans= '<option value="">--'.$title.'</option>';
	foreach($table as $keys => $data){
		if( $opt != "" )  $keys= $data;
		$select= ($keys==$selected) ?  " selected" : "";
		$ans.= '<option value="'.$keys.'"'.$select.'>'.$data.'</option>';
	}
	return $ans;
}
function myformjp_radio_gen($iname,$selected,$table,$array=array(), $opt="",$sep="&nbsp;&nbsp;"){
	$ans= "";
	$n= 1;
	foreach($table as $keys => $data) {
		$etc= "";
		if( $opt != "" )  $keys= $data;
		$select= ($keys==$selected) ?  " checked" : "";
		$msg= $this->myformjp_radio_check("radio",$iname,$keys,$data,$select,$array,$n);
		$ans.= empty($ans) ?  $msg : $sep.$msg;
		$n++;
	}
	return $ans;
}
function myformjp_check_gen($iname,$selected,$table,$array=array(), $opt="",$sep="&nbsp;&nbsp;"){
	$ans= "";
	if( !is_array($selected) ) $selected= array();
	$n= 1;
	foreach($table as $keys => $data){
		if( $opt != "" )  $keys= $data;
		$select= "";
		foreach($selected as $i => $dat){
			if( $keys==$dat ) $select= " checked";
		}
		$msg= $this->myformjp_radio_check("checkbox",$iname,$keys,$data,$select,$array,$n);
		$ans.= empty($ans) ?  $msg : $sep.$msg;
		$n++;
	}
	return $ans;
}
function myformjp_radio_check($type,$iname,$keys,$data,$select,$array,$n){
$cdatac= '<label><input type="{type}" name="{idn}" value="{val}"{sel}{etc} />';
	if( !is_array($array) ) $array= array();
	$etc= "";
	foreach($array as $key => $dat) {
		if( $key=="required" && $type=="radio" ) $etc.= " required";
		else
		if( $key=="required" ) {;}
		else {
			$datn= ($key=="id") ?  $dat.$n : $dat;
			$etc.= ' '.$key.'="'.$datn.'"';
	}	}
	$da= str_replace("{type}",$type,  $cdatac);
	$da= str_replace("{idn}", $iname, $da);
	$da= str_replace("{val}", $keys,  $da);
	$da= str_replace("{sel}", $select,$da);
	$da= str_replace("{etc}", $etc,   $da);
	$ans= $da."&nbsp;".$data."</label>";
	return $ans;
}
function myformjp_table_dispm($selected,$table, $opt="",$sep="、") {
	$ans= "";
	if( !is_array($selected) ) {$selected= array("" => $selected);}
	foreach($selected as $i => $key) {
		foreach($table as $skey => $data) {
			if( $opt != "" )  $skey= $data;
			if( $key == $skey ) {$ans.= empty($ans) ?  $data : $sep.$data;}
	}	}
	return $ans;
}
function myformjp_inptyp_gen($type){
	if( $type == "5" || $type == "P" ) $ans="select";
	else
	if( $type == "A" ) $ans="textarea";
	else  $ans="input";
	return $ans;
}
function myformjp_type_gen($type, $typetb){
	if( $type == "6" ) $ans= "file";
	else
	if( $type =="99" ) $ans= "hidden";
	else
	if( 51 <= $type && $type <= 70 ) $ans= $typetb[$type];
	else  $ans= "text";
	$ans= 'type="'.$ans.'"';
	return $ans;
}
function myformjp_size_gen($type,$d_size,$d_line,$d_maxsize){
	$ans= "";
	if( !empty($d_size) )   $ans.= $this->myformjp_svalue("size",$d_size);
	if( !empty($d_maxsize)) $ans.= $this->myformjp_svalue("maxlength",$d_maxsize);
	if( !empty($d_line) && $type=="A" ) $ans.= $this->myformjp_svalue("rows",$d_line);
	return $ans;
}
function myformjp_svalue($keyw,$data){
	if( empty($data) ) return "";
	else return ' '.$keyw.'="'.$data.'"';
}
function myformjp_selectdisp($type,$selected,$optable, $sepa="/"){
	if($type=="C"||$type=="R"||$type=="5"||$type=="P") $ans= $this->myformjp_table_dispm($selected,$optable);
	else
//	if( $type=="6" && !empty($selected) ) $ans= '<img src="'.myformjp_PLUGIN_URL.myformjp_temp.$sepa.$selected.'" width="60" height="40" />';
	if( $type=="6" && !empty($selected) ){
		$keta= rand(5,8);                         // 5-8
		$ans= $this->myformjp_eisu_codegen($keta).$selected;
	}
	else  $ans= str_replace("\n", "<br>", $selected);
	return $ans;
}
function myformjp_eisu_codegen($size="") {
	$leng= 12;
	$lengs= strlen($size);
	if( 1 <= $lengs && $lengs <= 6 ) {
		if( preg_match("/^[0-9]+$/",$size) )  {$leng=$size;}
	}
	$n= "1234567890";
	$a= "abcefghkmnpqrstuvwxyz";                  // dijlo抜き
	$u= strtoupper($a);
	$moji= $a.$n.$u;                              // 英小、数字、英大
	$mojisu= strlen($moji) -1;                    // 21*2 +10 => 52文字
	$ans= "";
	for( $i=0;$i<$leng;$i++ ) {
		$work= rand(0,$mojisu);
		$ans.= substr($moji,$work,1);
	}
	return $ans;
}
function myformjp_file_ups($ifname, $sepa="/"){   // ファイルアップロード
	$fname= "";
	$img1tmp=  $_FILES[$ifname]['tmp_name'];      // upファイル
	if( !isset($_FILES[$ifname]['error']) || !is_int($_FILES[$ifname]['error']) ) return $fname;
	if( $_FILES[$ifname]['error']==0 && is_uploaded_file($img1tmp) ){
		$type= $_FILES[$ifname]['type'];          // image/gif
		$tempn= explode("/", $type);
		$cnt= count($tempn);
		if( $cnt == 2 && !empty($tempn[$cnt-1]) ){
			$kaku= ".".$tempn[$cnt-1];            // .gif
			$fname= $this->myformjp_file_temp($img1tmp,$kaku);
	}	}
	return $fname;
}
function myformjp_file_temp($img1tmp,$kaku, $sepa="/"){
	$fname= "";
	$cnt= 5;
	while(1){
		$fname= $this->myformjp_eisu_codegen(16).$kaku; // 未使用ファイル名の生成
		$hostfname= myformjp_PLUGIN_DIR.myformjp_temp.$sepa.$fname;
		if( !file_exists($hostfname) ){
			$boRtn= move_uploaded_file($img1tmp, $hostfname);
//			if( !$boRtn ) {exit("アップロードに失敗しました。[$hostfname]_$boRtn");}
			break;
		}
		else{
			$cnt--;
			if( $cnt <= 0 ) break;
	}	}
	return $fname;
}
function myformjp_file_delete_ctrl($tablen, $tables="pooltable_sys"){
	global $dbio;
	$dbio->myformjp_DBopen();
	$t= strtotime("now -1 day", time());
	$dates= date_i18n("Ymd", $t);
	$today= date_i18n("Ymd");
	$code= $tablen."_delete";
	$where= "code='$code'";
	$db_data= $dbio->myformjp_DBall($tables, $where); // 処理済み確認
//echo($dates.count($db_data).$where);
	if( count($db_data) <= 0 ){                   // 初回
		$this->myformjp_file_delete($dates);
		$array= array("code" => $code, "data" => $today);
		$dbio->myformjp_DBisrt($tables,$array);
	}
	else {
		$rows= $db_data[0];
		if( $rows->data == $today ){;}            // 本日処理済み
		else {
			$this->myformjp_file_delete($dates);
			$array= array("data" => $today);
			$dbio->myformjp_DBupdt($tables,$array, $where);
	}	}
	$dbio->myformjp_DBclose();                    // 更新の掃き出し
}
function myformjp_file_delete($dates, $sepa="/") { // Dir内のfile削除
	$dir= myformjp_PLUGIN_DIR.myformjp_temp;
//exit($dir."####");
    $fp1= opendir($dir);
	while( $entry= readdir($fp1) ){
		$fname= $dir.$sepa.$entry;
		if( $entry == "." || $entry == ".." ) {;}
		else
		if( is_file($fname) ){
			$fdate= date_i18n("Ymd", filemtime($fname));
			if( $fdate <= $dates ) unlink($fname);
	}	}
    closedir($fp1);
}
function myformjp_get_type_table() {
	return array(
		"1"  => "text",
		"A"  => "textarea",        //
		"R"  => "radio",           //
		"C"  => "multi_check",     //
		"5"  => "select",
		"6"  => "file",            //
//		"10" => "date_time_calender", //サポート外にする
		"11" => "text",            //和暦年月
		"U"  => "text",            //郵便番号
		"P"  => "pref",            //都道府県
		"51" => "tel",             //<-html5
		"52" => "url",
		"53" => "email",           //
		"54" => "date",
//		"55" => "month",
//		"56" => "week",
		"57" => "time",
//		"58" => "datetime-local",
		"59" => "number",          //html5->
		"98" => "datetime",
		"99" => "hidden",
	);
}
function myformjp_get_pattern_table() {
	return array(
 "10" => "/^[0-9]{4}/[0-9]{2}/[0-9]{2}$/",
 "11" => "/^[0-9]{4}[-/]{1}[0-9]{2}$/",           // yyyy/mm
 "U"  => "/^[0-9]{3}-[0-9]{4}$|^[0-9]{7}$/",       // 郵便番号
 "51" => "/^0[0-9]0-[0-9]{4}-[0-9]{4}$|^[0-9]{11}$|^[0-9]{2,5}-[0-9]{1,4}-[0-9]{4}$|^[0-9]{10}$/", // TEL
 "52" => "/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/", // url
 "53" => "/^[a-zA-Z0-9.!#$%&'*+?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/", // email
 "54" => "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",
 "57" => "/^[0-9]{2}:[0-9]{2}$/",
 "58" => "/^[0-9]{4}/[0-9]{2}/[0-9]{2}$/",
	);
}
function myformjp_get_pattern_mod($type,$ptrn){
	if( $type=="11" ) return "/^[0-9]{4}[-\/]{1}[0-9]{2}$/";
	if( $type=="53" ) return "/^[a-zA-Z0-9\.!#\$%&'\*\+\?^_`\{|\}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)+$/";
	return $ptrn;
}
function myformjp_get_prefect_table() {
return array(
       "1"  => "北海道","2"  => "青森県","3"  => "岩手県","4"  => "宮城県",  "5"  => "秋田県",
       "6"  => "山形県","7"  => "福島県","8"  => "茨城県","9"  => "栃木県",  "10" => "群馬県",
       "11" => "埼玉県","12" => "千葉県","13" => "東京都","14" => "神奈川県","15" => "新潟県",
       "16" => "富山県","17" => "石川県","18" => "福井県","19" => "山梨県",  "20" => "長野県",
       "21" => "岐阜県","22" => "静岡県","23" => "愛知県","24" => "三重県",  "25" => "滋賀県",
       "26" => "京都府","27" => "大阪府","28" => "兵庫県","29" => "奈良県",  "30" => "和歌山県",
       "31" => "鳥取県","32" => "島根県","33" => "岡山県","34" => "広島県",  "35" => "山口県",
       "36" => "徳島県","37" => "香川県","38" => "愛媛県","39" => "高知県",  "40" => "福岡県",
       "41" => "佐賀県","42" => "長崎県","43" => "熊本県","44" => "大分県",  "45" => "宮崎県",
	 "46" => "鹿児島県","47" => "沖縄県",);
}
}
$pierre= new myformjp_psystem;
$pierre->myformjp_psystem_base();
?>
