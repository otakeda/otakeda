<?php 
/**	
 *    フッター (DB connectionのクローズも)
 **/
//        pg_close($db);
?>
<table width=90% valign=bottom> 
    <tr><td> 
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"> 
   {lang: 'ja'}
</script> 
<g:plusone></g:plusone> 
    </td></tr><tr>
    <td > 
<a href="http://twitter.com/share" class="twitter-share-button" data-url="http://partners.deecorp.jp/" data-count="horizontal" data-via="q2dee" data-lang="ja">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script> 
    </td> </tr><tr>
    <td> 
<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fpartners.deecorp.jp%2F&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:35px;" allowTransparency="true"></iframe> 
    </td> 
    </tr> 
</table>
<table><tr><td>
<!-- Begin Yahoo! JAPAN Web Services Attribution Snippet -->
<a href="http://developer.yahoo.co.jp/about" rel="shadowbox" target=_blank>
<img src="http://i.yimg.jp/images/yjdn/yjdn_attbtn1_125_17.gif" title="Webサービス by Yahoo! JAPAN" alt="Web Services by Yahoo! JAPAN" width="125" height="17" border="0" style="margin:15px 15px 15px 15px"></a>
<!-- End Yahoo! JAPAN Web Services Attribution Snippet -->
</td><td>
<?php 
        if (($is_login)||(SERVENV!="hon"))
        {
        print "<a href=\"javascript:clearCookie();\">こちらからログアウトできます</a>\n";
	}
?>
</td></tr>
</table>


<div id="footer-utility-area"> 
<ul> 
<li class="pseudo-first-child"><a href="http://www.deecorp.jp/" class="rollover" target=_blank><img src="images/footer-utility_btn_01.gif" width="142" height="13" alt="ディーコープホームページ" /></a></li> 
<li><a href="http://s.deecorp.jp/" class="rollover" target=_blank><img src="images/footer-utility_btn_02.gif" width="84" height="13" alt="企業のクチコミ" /></a></li> 
<li><a href="https://with.deecorp.jp/dee/supentry/" class="rollover" target=_blank><img src="images/footer-utility_btn_03.gif" width="53" height="13" alt="会員登録" /></a></li> 
<li><a href="https://www2.deecorp.jp/dee-hp/contact_input.jsp" class="rollover" target="_blank"><img src="images/footer-utility_btn_04.gif" width="69" height="13" alt="お問い合わせ" /></a></li> 
</ul> 
<!-- /footer-utility-area --></div> 
<!-- /wrap --></div> 
 
<div id="body-footer"> 
<div id="footer-area"> 
<div id="footer-address"> 
<address class="corporate-name"><img src="images/footer_corporate-name.gif" width="104" height="12" alt="ディーコープ株式会社" /></address> 
<address class="copyright"><img src="images/footer_copyright.gif" width="181" height="9" alt="Copyright &copy; DeeCorp. All rights reserved." /></address> 
<!-- /footer-address --></div> 
<!-- /footer-area --></div> 
<!-- /body-footer --></div> 
</body> 
</html> 
