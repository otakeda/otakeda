<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja" dir="ltr"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<meta http-equiv="Content-Script-Type" content="text/javascript" /> 
<meta http-equiv="Content-Style-Type" content="text/css" /> 
<title>見積＠Dee最新案件情報</title> 
<meta name="description" content="ディーコープ株式会社が提供する見積＠Dee案件最新情報です。" /> 
<meta name="keywords" content="ソフトバンク,Softbank,ディーコープ株式会社,DeeCorp" /> 
<meta name="viewport" content="width=device-width"> 
<?php 
$ua = $_SERVER['HTTP_USER_AGENT'];
 
if (preg_match("/^DoCoMo\/2\.0/i", $ua)) { // DoCoMo FOMA (XHTML)
    $isMobile = true;
} else if (preg_match("/^DoCoMo\/1\.0/i", $ua)) { // DoCoMo MOVA
    $isMobile = true;
} else if (preg_match("/^SoftBank/i", $ua)) { // SoftBank
    $isMobile = true;
} else if (preg_match("/^(Vodafone|MOT-)/i", $ua)) { // Vodafone 3G
    $isMobile = true;
} else if (preg_match("/^J\-PHONE/i", $ua)) { // Vodafone 1G,2G
    $isMobile = true;
} else if (preg_match("/iPhone/i", $ua)) { // iPhone
    $isMobile = true;
} else if (preg_match("/^KDDI\-/i", $ua)) { // au (XHTML)
    $isMobile = true;
} else if (preg_match("/UP\.Browser/i", $ua)) { // au (HDML) TU-KA
    $isMobile = true;
} else if (preg_match("/WILLCOM/i", $ua) ||
           preg_match("/DDIPOCKET/i", $ua)){ // WILLCOM Air EDGE
    $isMobile = true;
} else if (preg_match("/^PDXGW/i", $ua)) { // WILLCOM EDGE LINK
    $isMobile = true;
} else if (preg_match("/^(L\-mode)/i", $ua)) { // L-mode
    $isMobile = true;
} else {
    $isMobile = false;
}
	if ($isMobile)
	print " <link media=\"all\" href=\"css/smart.css\" type=\"text/css\" rel=\"stylesheet\" />\n";
	else
	print " <link media=\"all\" href=\"css/styles.css\" type=\"text/css\" rel=\"stylesheet\" />\n";
	
//<link media="screen" href="css/styles2.css" type="text/css" rel="alternate stylesheet" title="style2"/>
//<link media="screen" href="css/smart.css" type="text/css" rel="alternate stylesheet" title="smart"/>
?>
<script src="cate_script.js" type="text/javascript"></script> 
<script type='text/javascript' src='js/jquery.js'></script> 
<script type='text/javascript' src='js/styleswitcher.js'></script>
<script type="text/javascript"> 
$(document).ready(function(){
	$('.accordion_head').click(function() {
		$(this).siblings('.accordion_body:not(:animated)').slideToggle("fast",function(){
			$(this).siblings('.accordion_head').toggleClass('accordion_head_active');
			$(this).parent().toggleClass('active_li'); }); 
	}).siblings('.accordion_body').hide();
	
	$('.ac_close').click(function() {
		$(this).parent().parent().slideToggle("fast",function(){
			$(this).siblings('.accordion_head').toggleClass('accordion_head_active');
			$(this).parent().toggleClass('active_li');
			});
	}).siblings('.accordion_body').hide();
 
});
 
</script> 
 
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-21853860-3']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head> 
 
<body> 
<div id="wrap"> 
<div id="header" style="background:url(images/hbg.jpg) no-repeat bottom;">
<h1><a href="?"><img src="images/header_logo.gif" width="108" height="35" alt="DeeCorp" /></a></h1> 
<!--
<script type="text/javascript">
if ((navigator.userAgent.indexOf('iPhone') > 0 && navigator.userAgent.indexOf('iPad') == -1) || navigator.userAgent.indexOf('iPod') > 0 || navigator.userAgent.indexOf('Android') > 0 || (true)) {
document.write('<div class="h_r">');
document.write('<a href="javascript:setActiveStyleSheet(\'smart\');" onkeypress="setActiveStyleSheet(\'smart\');">＞スリム版（画面が狭い方向け）</a><br />');
document.write('<a href="javascript:setActiveStyleSheet(\'style2\');" onkeypress="setActiveStyleSheet(\'style2\');">＞ワイド版（画面が広い方向け）</a>');
document.write('</div>');
}
</script>
-->

</div> 

<div class="navi"> 
<strong>見積&#64;Dee最新案件情報</strong> 
<ul> 
	<li><a href="https://with.deecorp.jp/dee/supentry/Index.do" class="link01" target=\"_blank\"><img src="images/dot.gif" width="81" height="22" alt="会員登録" /></a></li> 
    <li><a href="https://www2.deecorp.jp/dee-hp/contact_input.jsp" class="link02" target=\"_blank\"><img src="images/dot.gif" width="108" height="22" alt="お問い合わせ" /></a></li> 
</ul> 
</div> 
