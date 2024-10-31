<?php
if( !defined('ABSPATH') ) exit;

	$urlpath= isset($_SERVER["REQUEST_URI"]) ?  $_SERVER["REQUEST_URI"] : "";
	$wk= explode("/", $urlpath);
	if( $wk[count($wk)-1] != "" ) $sys_target=$wk[count($wk)-1];
	else
	if( $wk[count($wk)-2] != "" ) $sys_target=$wk[count($wk)-2];
	else  {echo("対象フォームcodeが見つかりません。"); $sys_target="contact";}

	$myformjp_table= array_flip( myformjp_tables() );


	if( isset($myformjp_table[$sys_target]) ){
		$define= 'myformjp_define['.$sys_target.']';
		define('myformjp_D', $define);

		$fld= myformjp_option(myformjp_D);
		foreach((array)$fld as $key => $data) {$$key=$data;}
		include_once(myformjp_PLUGIN_DIR."include/myformjp_psystem_class.php");
		$dbio->myformjp_set_target($sys_target);      // 処理対象フォームcode
		$dbio->myformjp_set_field($sys_target);       // 項目一覧
	}
?>
