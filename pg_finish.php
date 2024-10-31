<?php
/**
 * MyForm、メール送信完了
 */
if( !defined('ABSPATH') ) exit;
	wp_enqueue_style("myformjp", myformjp_CSS);
?>
<meta http-equiv="refresh" content="5;URL=<?php echo esc_html($sys_homeurl) ?>">
<div id="Crumbs">
<a href="<?php echo esc_html($sys_topurl) ?>">ホーム</a> &gt; <strong><?php echo esc_html($sys_title) ?></strong>
</div>



<div id="Wrap" class="clearfix">
<section id="ContentsBody" class="contents-body">

<h2><?php echo esc_html($sys_formname3) ?></h2>

<br />

<div id="detail">
	<p>お問い合わせ頂きありがとうございました。<br />
		内容を確認次第、ご連絡させて頂きます。</p>
	<p>※５秒後にトップページへ自動的に移動します。</p>
</div>
<br />
<br />
<br />



</section>
</div>
