<?php
/**
 * MyForm、入力画面
 */
if( !defined('ABSPATH') ) exit;
	wp_enqueue_style("myformjp", myformjp_CSS);
?>
<div id="Crumbs">
<a href="<?php echo esc_html($sys_topurl) ?>">ホーム</a> &gt; <strong><?php echo esc_html($sys_title) ?></strong>
</div>



<div id="Wrap" class="clearfix">
<section id="ContentsBody" class="contents-body">

<h2><?php echo esc_html($sys_formname) ?></h2>

<?php if( isset($sys_description) ): ?>
	<div class="form-description"><p><?php echo esc_html($sys_description) ?></p></div>
<?php endif ?>

<div id="detail">
<!-- [input_form_define] -->
<?php $pierre->myformjp_view('view/form_input.php','', $inputdata) ?>
</div>

</section>
</div>
