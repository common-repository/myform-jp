<?php
if( !defined('ABSPATH') ) exit;
function myformjp_content($output, $opt=""){
	global $dbio,$pierre,$myformjp_table,$sys_target;
	if( !empty($output) ) return $output;
	if( !isset($myformjp_table[$sys_target]) ) return $output;
	$ans= $output;


	$fld= myformjp_option(myformjp_D);
	foreach((array)$fld as $key => $data) {$$key=$data;}
//print_r($fld);
//exit($sys_target);

	$viewopt= isset($viewopt) ?  $viewopt : "";


	if( isset($_POST['myformjp-sendr']) ){
		include_once(myformjp_PLUGIN_DIR."pg_confirm.php");
	}
	else
	if( isset($_POST['myformjp-token']) ){
		include_once(myformjp_PLUGIN_DIR."pg_index.php");
	}
	else
	if( empty($viewopt) ){
		include_once(myformjp_PLUGIN_DIR."pg_index.php");
	}


	return $ans;
}
?>
