<?php
/*
Plugin Name: myform-jp
Plugin URI: https://pierre-soft.com/wordpress/
Description: This software is inquiry form for the individual.
Version: 1.7
Author: Tatsuro, Terunuma
Author URI: https://pierre-soft.com/
*/
if( !defined('ABSPATH') ) exit;
define('myformjp_VERS','1.7');
define('myformjp_KEYS','myformjp-0');
define('myformjp_SYS', 'sys_');
define('myformjp_SALT','b283b4181c940395167d95e23d150dd0fc8f4bad');
define('myformjp_HOME','https://pierre-soft.com');

define('myformjpMe', plugin_basename(dirname(__FILE__)));
define('myformjp_URL', plugins_url());                   // /plugins
define('myformjp_PLUGIN_DIR',plugin_dir_path(__FILE__)); // /myplugin/
define('myformjp_PLUGIN_URL', plugin_dir_url(__FILE__)); // /myplugin/

define('myformjp_P', 'myformjp_params');          // システム固有情報
define('myformjp_C', 'myformjp_css');
//fine('myformjp_D', 'myformjp_define');
define('myformjp_F', "field-");
define('myformjp_M', 'myformjp_mail');
define('myformjp_S', 'myformjp_sqlite3');
//特殊ファイルは、myformjp_params.php, myformjp_define.php,myformjp.css,myformjp_mail.php,pt1_simpty.sqlite3の5つです。

define('myformjp_DEF',  myformjp_PLUGIN_DIR."include/myformjp_define.php"); // 稼働環境定義(myformjp_define)
define('myformjp_PRC',  myformjp_PLUGIN_DIR."css/myformjp_cssadd.php"); // CSS定義
define('myformjp_PRM',  myformjp_PLUGIN_DIR."view/mail_define.php"); // メール情報定義(myformjp_mail)
define('myformjp_PRS',  myformjp_PLUGIN_DIR."dba/pt1_simpty.sqlite3"); // フォーム情報定義(myformjp_sqlite3)
define("myformjp_CSS",  myformjp_PLUGIN_URL."css/myformjp.css");
$wppath=wp_upload_dir();
define("myformjp_UPS",  $wppath[basedir]."/pt1_simpty.sqlite3");

	include_once(myformjp_PLUGIN_DIR."include/myformjp_dsystem_class.php");
	require_once(myformjp_PLUGIN_DIR."include/myformjp_setting.php"); // 設定の同期化更新


if( is_admin() ){
//	load_plugin_textdomain('myform-jp', false, myformjpMe.'/languages');
	require_once myformjp_PLUGIN_DIR.'admin.php';
	add_action('admin_menu', 'myformjp_admin_menu');
	if( function_exists('register_deactivation_hook') ){register_deactivation_hook( __FILE__, 'myformjp_deactivation' );} // 有効化解除
	if( function_exists('register_uninstall_hook') )   {register_uninstall_hook( __FILE__, 'myformjp_uninstall' );} // uninstall呼出し
}
else{
	require_once(myformjp_PLUGIN_DIR."include/myformjp_config.php");
	require_once(myformjp_PLUGIN_DIR."myform.php");
	add_filter('the_content', 'myformjp_content', 999999); // html change
}
?>
