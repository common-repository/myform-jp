<?php
if( !defined('ABSPATH') ) exit;

// 設定メニュー下にサブメニューを追加
function myformjp_admin_menu(){
	$myformjp_table= myformjp_tables();
	add_menu_page( 'Myform-JP', 'Myform-JP', 'activate_plugins', myformjp_KEYS, 'myformjp_conf');
	foreach($myformjp_table as $i => $key){
		$kname= "環境定義[".$key."]";
		add_submenu_page(myformjp_KEYS, __($kname,'myform-jp'), __($kname,'myform-jp'), 'activate_plugins', 'myformjp-'.$i, 'myformjp_conf');
	}
	add_submenu_page(myformjp_KEYS, __('定義済情報の取得','myform-jp'), __('定義済情報の取得','myform-jp'), 'activate_plugins', 'myformjp-gets', 'myformjp_gets');
}
function myformjp_radio($iname,$selected,$table){
$cdat='<label><input type="radio" name="{iname}" id="{iname}_{n}" value="{key}"{select} />{data}</label>';
	$ans= "";
	$n= 0;
	foreach($table as $key => $data){
		$select= ($key==$selected) ?  " checked" : "";
		$n++;
		$da= str_replace("{iname}", $iname, $cdat);
		$da= str_replace("{n}",     $n,     $da);
		$da= str_replace("{key}",   $key,   $da);
		$da= str_replace("{data}",  $data,  $da);
		$da= str_replace("{select}",$select,$da);
		$ans.= $da."<br />\n";
	}
	return $ans;
}
function myformjp_params($pwd){
	$myform_pwd= $pwd['myform_pwd'];
	$myform_key= $pwd['myform_key'];
	$host= isset($_SERVER['HTTP_HOST']) ?  $_SERVER['HTTP_HOST'] : "";
	$ip= gethostbyname($host);
	$ans= '?u='.sha1(myformjp_SALT.$host) .'&p='.$myform_pwd .'&k='.$myform_key .'&d='.$host .'&i='.$ip .'&v='.myformjp_VERS;
	return $ans;
}
function myformjp_conf(){
	$myformjp_table= myformjp_tables();
//項目id群
	$pwd= get_option(myformjp_P);                 // システム固有情報の取得
	$cpm= myformjp_params($pwd);                  // callパラメータ生成

//データ処理
	if( !empty($_GET['page']) && ($_GET['page']=="myformjp-0" || $_GET['page']=="myformjp-1" ||
			$_GET['page']=="myformjp-2" || $_GET['page']=="myformjp-3" || $_GET['page']=="myformjp-4") ){
		$no= str_replace("myformjp-","", $_GET['page']); // 0-4
		$mytable= 'myformjp_define['.$myformjp_table[$no].']';
		if( isset($_POST['myformjptoken']) && !empty($_POST['myformjptoken']) ){
			check_admin_referer('myformjp', 'myformjp_def');
			$prm= "<?php\n"."if( !defined('ABSPATH') ) exit;\n";
			$flds= myformjp_fld();
			foreach($flds as $i => $key){
				$keys= myformjp_SYS.$key;
				$da= isset($_POST[$keys]) ?  htmlspecialchars($_POST[$keys],ENT_QUOTES,'UTF-8') : "";
				$prm.= '$'.$keys.'= "'.$da.'";';
				$prm.= "\n";
			}
			$prm.= "?>\n";
			myformjp_fileup($mytable, "", $prm);
			$mesg= "稼働環境を定義しました。";
		}
		else $mesg= "【稼働環境の定義】";

//画面表示処理
		$flds= myformjp_fld(); foreach($flds as $i => $key){$keys=myformjp_SYS.$key; $$keys="";}
		$fld= myformjp_option($mytable);
		if( !$fld ) {$fld= myformjp_option("myformjp_define[contact]");} // なければcontactを流用
		foreach((array)$fld as $key => $data) {$$key=$data;}

		if( empty($sys_homeurl) )    $sys_homeurl=   home_url();
		if( empty($sys_topurl) )     $sys_topurl=    site_url();
		if( empty($sys_system) )     $sys_system=    "お問い合わせフォーム";
		if( empty($sys_company) )    $sys_company=   "テスト用";
		if( empty($sys_title) )      $sys_title=     "お問い合わせ";
		if( empty($sys_trformat) )   $sys_trformat=  "2";
		if( empty($sys_cssformat) )  $sys_cssformat= "1";
		if( empty($sys_formname) )   $sys_formname=  "入力フォーム";
		if( empty($sys_formname2) )  $sys_formname2= "入力内容確認";
		if( empty($sys_formname3) )  $sys_formname3= "メール送信完了";
		if( empty($sys_description) )$sys_description= "お問い合わせの内容を入力してください。";

		$dopt= array("1" => "左右に表示", "2" => "上下段で表示（default）");
		$dcss= array("1" => "線なし（default）", "2" => "枠線を引く", "3" => "区切り線を引く");
//		$pult= array("1" => "定義済フォーム情報を取得する","0" => "なし");
		$dop= myformjp_radio("sys_trformat", $sys_trformat, $dopt);
		$dcs= myformjp_radio("sys_cssformat",$sys_cssformat,$dcss);
//		$pul= myformjp_radio("sys_pull", "", $pult);
?>
<h2><?php echo esc_html($mesg) ?>（myform-jp）Ver<?php echo esc_html(myformjp_VERS) ?></h2>
<form id="myformjp-conf" method="post" action="">
code: <?php echo esc_html($mytable) ?>　　※&nbsp;<span style="color: #ff0000;">全項目が必須です。</span><br />
<table border="1" cellspacing="0" cellpadding="8" summary=" ">
    <tr>
        <td bgcolor="#f3f3f3">ホームページのurl</td>
        <td><input type="text" name="sys_homeurl" size="45" maxlength="255" value="<?php echo esc_url($sys_homeurl) ?>" placeholder="例：<?php echo esc_url(myformjp_HOME) ?>" /></td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">利用ページTopのurl</td>
        <td><input type="text" name="sys_topurl" size="45" maxlength="255" value="<?php echo esc_url($sys_topurl) ?>" placeholder="例：<?php echo esc_url(myformjp_HOME) ?>/wordpress/" /></td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">システム名称</td>
        <td><input type="text" name="sys_system" size="45" maxlength="50" value="<?php echo esc_html($sys_system) ?>" placeholder="例：お問い合わせフォーム" /></td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">サイト（会社）名称</td>
        <td><input type="text" name="sys_company" size="45" maxlength="50" value="<?php echo esc_html($sys_company) ?>" placeholder="例：ピエールソフト" /></td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">フォームのタイトル</td>
        <td><input type="text" name="sys_title" size="45" maxlength="50" value="<?php echo esc_html($sys_title) ?>" placeholder="例：お問い合わせ" /></td>
    </tr>
    <tr >
        <td bgcolor="#f3f3f3">タイトル／項目の表示</span></td>
        <td><?php echo($dop) ?></td>
    </tr>
    <tr >
        <td bgcolor="#f3f3f3">簡単なstyle変更</span></td>
        <td><?php echo($dcs) ?>※詳細はWordpressの機能で変更して下さい。</td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">入力画面名称</td>
        <td><input type="text" name="sys_formname" size="45" maxlength="50" value="<?php echo esc_html($sys_formname) ?>" placeholder="例：入力フォーム" /></td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">確認画面名称</td>
        <td><input type="text" name="sys_formname2" size="45" maxlength="50" value="<?php echo esc_html($sys_formname2) ?>" placeholder="例：入力内容確認" /></td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">完了画面名称</td>
        <td><input type="text" name="sys_formname3" size="45" maxlength="50" value="<?php echo esc_html($sys_formname3) ?>" placeholder="例：メール送信完了" /></td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">入力案内の説明</td>
        <td><input type="text" name="sys_description" size="45" maxlength="50" value="<?php echo esc_html($sys_description) ?>" placeholder="例：お問い合わせの内容を入力してください。" /></td>
    </tr>
<!--
    <tr>
        <td bgcolor="#f3f3f3">定義情報の取り込み</td>
        <td><?php echo($pul) ?><span style="color:orange;">※フォーム様式定義が終ってから指示して下さい。</span></td>
    </tr>
-->
    <tr>
        <td bgcolor="#f3f3f3">サイトキー<br />シークレットキー<br />任意（ボット対策を行う場合）</td>
        <td>
<input type="text" name="sys_sitekey" size="55" maxlength="55" value="<?php myformjp_D($sys_sitekey) ?>" /><br />
<input type="text" name="sys_secretkey" size="55" maxlength="55" value="<?php myformjp_D($sys_secretkey) ?>" /><br />
【googleのreCAPTCHAから取得して下さい】
        </td>
    </tr>

</table>
<br />
▼フォームを定義する（<?php echo esc_html(myformjp_HOME) ?>）→　<a href="<?php echo esc_url(myformjp_HOME."/myform_world/datapull.php".$cpm) ?>" target="_blank">【フォーム様式定義】</a><br />
▼ ← 取り込みの指示はメニュー側です。<br />
<br />
<div class="btn-area">
	<ul><li>
		<input type="hidden" name="myformjptoken" value="1"/>
<?php wp_nonce_field( 'myformjp', 'myformjp_def' ); ?>
		<input id="submit" class="button button-primary" type="submit" value="この内容で登録する" name="submit" />
	</li></ul>
</div>
</form>
<?php
	}
}

function myformjp_gets(){
	$mesg= "定義済情報の取得は失敗しました。";
	if( !empty($_GET['page']) && $_GET['page'] == 'myformjp-gets' ){
		$pwd= get_option(myformjp_P);             // システム固有情報の取得
		$cpm= "/myform_world/datacheck.php".myformjp_params($pwd); // callパラメータ生成
		$url1= myformjp_HOME.$cpm."&o=1";
		$res= wp_remote_get($url1);
		if( !empty($res) ){
			$res= wp_remote_retrieve_body($res);
			$res= rawurldecode($res);
			$res= esc_url($res);                  // urlの無害化
			copy($res."/pt1_simpty.sqlite3", myformjp_UPS);
//
			$url2= myformjp_HOME.$cpm."&o=2";
			$res= wp_remote_get($url2);           // data gets
			if( !empty($res) ){
				$res= wp_remote_retrieve_body($res);
				$res= stripslashes($res);
				$res= html_entity_decode($res,ENT_QUOTES,'UTF-8');
				$res= str_replace("<?php","", $res);
				$res= str_replace("?>",   "", $res);
				$res= str_replace("{","{sys_",$res);
				$res= htmlspecialchars($res,ENT_NOQUOTES,'UTF-8');
				$res= "<?php\nif( !defined('ABSPATH') ) exit;\n". $res ."?>\n";
				myformjp_fileup(myformjp_M, "", $res);
				$mesg= "定義済フォーム情報を取得しました。";
		}	}
	}
?>
<h2><?php echo esc_html($mesg) ?></h2>
<?php
}
function myformjp_css(){
	if( !empty($_GET['page']) && $_GET['page'] == myformjp_KEYC ){
		if( isset($_POST['myformjpcss']) && !empty($_POST['myformjpcss']) ){
			check_admin_referer('myformjp', 'myformjp_css');
			$data= isset($_POST['cssdata']) ?  $_POST['cssdata'] : "";
			$data= stripslashes($data);
			$data= html_entity_decode($data,ENT_QUOTES,'UTF-8');
			if( !empty($data) ){
//				file_put_contents(myformjp_PRC, $data);
//				myformjp_fileup(myformjp_C, myformjp_PRC);
			}
			 $mesg= "CSS定義を更新しました。";
		}
		else $mesg= "【CSSの更新定義】";

//		$data= @file_get_contents(myformjp_PRC);
		$data= htmlspecialchars($data,ENT_QUOTES,'UTF-8');
?>
<h2><?php echo esc_html($mesg) ?>（myform-css）Ver<?php echo esc_html(myformjp_VERS) ?></h2>
<form id="myformjp-css" method="post" action="">

<table border="1" cellspacing="0" cellpadding="8" summary=" ">
    <tr>
        <td>
<textarea type="text" name="cssdata" id="css_data" cols="150" rows="28" required><?php echo esc_html($data) ?></textarea>
        </td>
    </tr>
</table>
<div class="btn-area">
	<ul><li>
		<input type="hidden" name="myformjpcss" value="1"/>
<?php wp_nonce_field( 'myformjp', 'myformjp_css' ); ?>
		<input id="submit" class="button button-primary" type="submit" value="この内容で登録する" name="submit" />
	</li></ul>
</div>
</form>
<?php
	}
}
function myformjp_deactivation(){
	global $dbio;
	$dbio->myformjp_DBopen();
	$dbio->myformjp_DBclose();
}
function myformjp_uninstall(){                    // uninstall処理
	$array= array();
//	$array[myformjp_C]= myformjp_PRC;             // CSS定義(myformjp_css)
//	$array[myformjp_D]= myformjp_DEF;             // 稼働環境定義(myformjp_define)
	$array[myformjp_M]= myformjp_PRM;             // メール情報定義(myformjp_mail)
	$array[myformjp_S]= myformjp_UPS;             // フォーム情報定義(myformjp_sqlite3)
	foreach($array as $key => $fname){
		if( $key == myformjp_S ) unlink($fname);
		else  delete_option($key);
	}
	delete_option(myformjp_P);
}
?>
