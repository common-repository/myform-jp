<?php
if( !defined('ABSPATH') ) exit;

$cdata1= '<tr id="Row_{IDN}">
	<th class="col-head" width="150"><label>{title}</label>{hissu}</th>
	<td class="col-input">{inpline}';
$inpline= '
{befores}{input}{textarea}{afters}
';
$cdata2= '<{input} {type} name="{idn}" id="{idd}"{class}{val}{size}{placeh}{required}>';
$cdatas= '<span class="mail-attention" id="err_{idkey}"></span>
';
$tdtr= '	</td>
</tr>
';
?>
