<?php
/**
 * MyForm、入力内容確認
 */
if( !defined('ABSPATH') ) exit;
	$sys_target= $dbio->myformjp_get_target();    // contact

	$data= $pierre->myformjp_get_data();

	if( !isset($_POST['myformjp-keysets']) ){
		include_once(myformjp_PLUGIN_DIR."pg_index.php");
	}
	else
	if( isset($_POST['myformjp-back']) && $_POST['myformjp-back']=="1" ){
		$data= $pierre->myformjp_get_postdata($sys_target);
		$pierre->myformjp_set_data($data);
		include_once(myformjp_PLUGIN_DIR."pg_index.php");
	}
	else
	if( isset($_POST['myformjp-sendr']) ){        // メール送信？
		if( !isset($_POST['myformjp_form']) || !wp_verify_nonce($_POST['myformjp_form'],'myformjp') ) {;}
		else{
			$postdata= $pierre->myformjp_get_postdata($sys_target);
			$pierre->myformjp_set_data($postdata);

			$viewopt= "s";
			$temp= $pierre->myformjp_DBdata($sys_target,$viewopt); // html生成
			$html=  $temp[0];
			$attach=$temp[1];
//			$name= isset($postdata[myformjp_F.'name']) ?   $postdata[myformjp_F.'name'] : "";
//			$to=   isset($postdata[myformjp_F.'email']) ?  $postdata[myformjp_F.'email']: "";
			$id1= myformjp_F. $pierre->myformjp_name_search($sys_target,'name');
			$id2= myformjp_F. $pierre->myformjp_name_search($sys_target,'email');
			$name= isset($postdata[$id1]) ?  $postdata[$id1] : 'name';
			$to=   isset($postdata[$id2]) ?  $postdata[$id2] : 'email';
			$attacharray= array();
			if( isset($attach) && count($attach)>0 ){
				$attacharray= $attach;            // 添付ファイルパス
			}
			if( isset($sys_dbout) && $sys_dbout=="Y" ){ // DB出力？
				include("output_db.php");
			}

			mb_language("japanese");              // 言語設定、内部エンコーディングを指定する
			mb_internal_encoding("UTF-8");

//			include(myformjp_PRM);                // メール情報の定義
			$fld= myformjp_option(myformjp_M);
			foreach((array)$fld as $key => $data) {$$key=$data;}

			$system_define= array();
			$fld= myformjp_fld();
			foreach($fld as $i => $key) {$keys=myformjp_SYS.$key; $system_define[$keys]= $$keys;}

			$mesg= mail_change($system_define, $name,$ma_hikae,$html);
			mailsends($ma_email,$ma_subject,$mesg,$attacharray, $sy_from,$sy_fromname); // 管理者控え

			$mesg= mail_change($system_define, $name,$hikae,$html);
			if( !empty($to) ) mailsends($to,$subject,$mesg,$attacharray, $sy_from,$sy_fromname); // 問い合わせ控え
			$viewopt= "3";
			include_once(myformjp_PLUGIN_DIR."pg_finish.php");
//			exit;
//			header("Location: finish.html");      // 完了へ
		}
	}
	else {
		$viewopt= "2";
		include("view/form_confirm.php");
	}

function mail_change($system_define, $name,$msg,$html){
	$date= date_i18n("Y/m/d H:i:s");
	$mesg= $msg;
	foreach($system_define as $key => $data){
		$keys= "{".$key."}";
		$mesg= str_replace($keys, $data, $mesg);
	}
	$mesg= str_replace("{sys_name}",      $name, $mesg);
	$mesg= str_replace("{sys_form_imput}",$html, $mesg);
	$mesg= str_replace("{sys_sdate}",     $date, $mesg);
	return $mesg;
}
function mailsends($to,$subject,$mesg,$attacharray, $sy_from="",$sy_fromname=""){
	$attachments= array();
	if( !is_array($attacharray) ) $attacharray= array();
	foreach($attacharray as $i => $attachfile){
		$attachments[]= myformjp_PLUGIN_DIR.myformjp_temp."/".$attachfile;
	}
	$headers= "";
	if( !empty($sy_from) && !empty($sy_fromname) ) $headers= "From: ".$sy_fromname." <".$sy_from.">\r\n";
	if( !wp_mail($to,$subject,$mesg,$headers,$attachments) ) {echo("メールが送信できませんでした。");}
}
?>
