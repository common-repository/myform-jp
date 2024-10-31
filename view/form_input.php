<?php
/**
 * MyForm、tableデータ画面
 */
if( !defined('ABSPATH') ) exit;
//exit($sys_target."##");
	$cstyle= $pierre->myformjp_cstyle($viewopt);
	$temp= $pierre->myformjp_DBdata($sys_target,$viewopt); // html生成
	$html=   $temp[0];
	$attach= $temp[1];
	$captcha=$temp[2];
	$token= $pierre->myformjp_eisu_codegen();     // 12桁
?>
<?php echo($cstyle) ?>
<form name="<?php echo esc_html("Form_".$sys_target) ?>" id="<?php echo esc_html("Form_".$sys_target) ?>" method="post" action="" enctype="multipart/form-data"
<?php if( $viewopt == "" ){ ?>
 onsubmit="return Myf.form_check();"
<?php } ?>
 accept-charset="UTF-8">


<table cellpadding="0" cellspacing="0" class="row-table-01">
<?php echo($html) ?>
</table>
<?php if( $viewopt=="" && !empty($sys_sitekey) && count($captcha)>0 ){ ?>
<div class="g-recaptcha" data-sitekey="<?php myformjp_D($sys_sitekey) ?>"></div>
<?php } ?>


<div class="submit">
<?php if( $viewopt == "2" ){ ?>
<?php echo($inputdata) ?>
	<input type="submit" class="button" value="戻る" onclick="document.getElementById('myformjp-back').value='1';">
　　　<input type="submit" class="button" id="submit" value="　次へ（送信する）　"/>
	<input type="hidden" name="myformjp-sendr" value="<?php echo esc_html($token) ?>" />
	<input type="hidden" name="myformjp-back" id="myformjp-back" value="" />
<?php } else { ?>
	<input type="submit" class="button" id="submit" value="　次へ（入力内容の確認）　"/>
	<input type="hidden" name="myformjp-token" value="<?php echo esc_html($token) ?>" />
<?php } ?>
	<input type="hidden" name="myformjp-keysets" value="1" />
	<?php wp_nonce_field( 'myformjp', 'myformjp_form' ); ?>
</div>
</form>
