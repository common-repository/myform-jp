<?php
/**
 * MyForm、入力フォーム
 */
if( !defined('ABSPATH') ) exit;
	$sys_target= $dbio->myformjp_get_target();    // contact
	$data= $pierre->myformjp_get_data();
	$inputdata= $errmsg= "";
	if( isset($_POST['myformjp-token']) ){        // 正常送信？
		$sendok= "1";                             // 認証チェック、1:認証ok
		if( isset($_POST['g-recaptcha-response']) ){
			if( empty($_POST["g-recaptcha-response"]) ) $sendok="";
			else{
				$captcha= htmlspecialchars($_POST["g-recaptcha-response"],ENT_QUOTES,'UTF-8');
$google= "https://www.google.com/recaptcha/api/siteverify?secret={$sys_secretkey}&response={$captcha}";
				$resp= @file_get_contents($google);
				$resp_result= json_decode($resp,true);
				if( intval($resp_result["success"]) !== 1) {$sendok="";} //認証失敗
		}	}
		if( !isset($_POST['myformjp_form']) || !wp_verify_nonce($_POST['myformjp_form'],'myformjp') ) {;}
		else{
			$tablen= $sys_target;
			$pierre->myformjp_file_delete_ctrl($tablen); // 添付fileの定期削除

$cdatai= '<input type="hidden" name="'.myformjp_F.'{name}" value="{data}" />'."\n";
			$attacharray= array();
			$ptrnt= $pierre->myformjp_get_pattern_table();
			$db_data= $dbio->myformjp_get_field(); // 項目一覧
			foreach($db_data as $i => $rows){
				$field= $rows->name;
				if( $field=="id" || $field=="tyymd" ){;} // idはskip
				else{
					$rowx= $pierre->myformjp_get_idtable($tablen,$field);
					$key=    $rowx->d_idkey;      // name
					$type=   $rowx->d_type;       // type
					$title=  $rowx->d_title;      // 名称
					$hissu=  $rowx->d_hissu;      // 1:必須
					$mxsize= $rowx->d_maxsize;
					$keys=   myformjp_F.$key;     // field-name
					$data= "";
					$filen= ($type=="6") ?  $pierre->myformjp_file_ups($keys) : "";
					if( !empty($filen) )    {$$key=$filen; $data=$filen; $attacharray[$keys]=$filen;}
					else
					if( !isset($_POST[$keys]) ) {$$key=""; $data="";}
					else
					if( isset($_POST[$keys]) ){
						$data= $pierre->myformjp_post($keys);
						$$key= $data;             // ↓入力check
						if( $hissu=="1" && empty($data) ) $errmsg.= $title."は、入力必須です。<br />";
						$checkx= $rowx->d_checks; // パターンによる入力チェック
						if( empty($checkx) && isset($ptrnt[$type]) ) $checkx= $ptrnt[$type];
						$checkx= $pierre->myformjp_get_pattern_mod($type,$checkx);
						if( !empty($checkx) && !empty($data) ){ // パターンcheck
							if( !preg_match($checkx,$data) ) $errmsg.=$title."を、ご確認ください。<br />";
						}
						if( $type=="A" && !empty($mxsize) && mb_strlen($data)>$mxsize ) $errmsg.=$title."は文字数オーバーです。<br/>";
					}
					$da= is_array($data) ?  "@#+@#".implode("｜",$data) : $data; // hidden
					$inp= str_replace("{name}", $key, $cdatai);
					$inp= str_replace("{data}", $da,  $inp);
					$inputdata.= $inp;
			}	}
			$posts= array_merge($_POST,$attacharray);
			$pierre->myformjp_set_data($posts);   // 入力データの格納
			if( empty($errmsg) && $sendok == "1" ){
				$viewopt= "2";
				include_once(myformjp_PLUGIN_DIR."pg_confirm.php");
			}
			else{
				$sys_description= $errmsg;
				include("view/form_index.php");
	}	}	}
	else{
		include("view/form_index.php");
	}
?>
