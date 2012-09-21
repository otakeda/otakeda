
var twitter_user_id=0;
function setTwitterID(tid) {
	twitter_user_id=tid;
}
//入力項目のチェック => ボタンのdisableきりかえ
function checkValue() {
	okflg=true;
	if (document.frmtwi.corp_name.value.indexOf("(")==0)  okflg=false; 
	if (document.frmtwi.corp_name.value.length<5)  okflg=false; 
	if (document.frmtwi.address.value.indexOf("(")==0) okflg=false;
	if (document.frmtwi.address.value=="") okflg=false;

	var t = document.getElementById('wordcounter');
	if (t.style.color=='red') okflg=false;

	if (okflg)  document.frmtwi.twipost.disabled =false;
		else document.frmtwi.twipost.disabled =true;
}
//投稿直前のチェック
function valueCheckSubmit()
{
//企業名は必須
	if (document.frmtwi.corp_name.value.indexOf("(")==0) {  
		document.frmtwi.twipost.disabled = false;
		alert('企業名を入力してください'); return; 
	}
//	if (document.frmtwi.corp_name.value=="") { alert('企業名を入力してください');  document.frmtwi.twipost.disabled = false;return; }
	if (document.frmtwi.corp_name.value.length<5) 
	{ alert('企業名は５文字以上で入力してください'); document.frmtwi.twipost.disabled = false; return; }
//本社所在地
	if (document.frmtwi.address.value.indexOf("(")==0) { alert('本社所在地を入力してください(市区町村まででも可)');  document.frmtwi.twipost.disabled = false;return; }
	if (document.frmtwi.address.value=="") { alert('本社所在地を入力してください(市区町村まででも可)');  document.frmtwi.twipost.disabled = false;return; }

//	if (!(document.frmtwi.user_type_id.value>0)) { alert('属性を入力してください'); return; }

	if (document.frmtwi.url.value.indexOf("(")==0) { document.frmtwi.url.value=""; }
	if (document.frmtwi.corp_tag.value.indexOf("(")==0) { document.frmtwi.corp_tag.value=""; }
//	if (!(document.frmtwi.user_star.value>0 )) { alert('評価を選択してください'); return; }
	document.frmtwi.submit();
	 
}
// 星をクリックしたときの動作
function clickStar(no)
{
	starClicked=document.getElementById('star'+no);
	for (i = 1; i <= 5; i++) {
	if (no>=i)
	document.getElementById('star'+i).src='images/ico_star.gif';
	else
	document.getElementById('star'+i).src='images/ico_star_off.gif';
	}
	document.frmtwi.user_star.value=no;
	if (no==1) document.getElementById('startext').innerHTML='よくなかった';
	if (no==2) document.getElementById('startext').innerHTML='もうひとつ';
	if (no==3) document.getElementById('startext').innerHTML='まずまず';
	if (no==4) document.getElementById('startext').innerHTML='よかった';
	if (no==5) document.getElementById('startext').innerHTML='すばらしい';
}
function seltab(bpref, hpref, id_max, selected) {
//	document.frmtwi.defbox.value=selected;
  if (! document.getElementById) return;
  for (i = 0; i <= id_max; i++) {
    if (! document.getElementById(bpref + i)) continue;
    if (i == selected) {
      document.getElementById(bpref + i).style.visibility = "visible";
      document.getElementById(bpref + i).style.position = "";
      document.getElementById(hpref + i).className = "open";
//      document.getElementById(hpref + i).style.backgroundimage = "url("../images/btn_tab_new.gif")";
//      document.getElementById(hpref + i).style.backgroundimage = "url('../images/btn_tab_new.gif')";
    } else {
      document.getElementById(bpref + i).style.visibility = "hidden";
      document.getElementById(bpref + i).style.position = "absolute";
      document.getElementById(hpref + i).className = "close";
//     document.getElementById(hpref + i).style.backgroundimage = "url('../images/btn_tab_new_off.gif')";
    }
  }
}


var sg=null;
function initSuggest(suggest_obj) {
    sg = suggest_obj;
    //区切り文字を変更
//    sg.delim = '、';
    //検索ロジックを上書きして、何もしないように変更
    sg._search = function (text) { return 0; };
    //検索時のフック関数を指定
    sg.hookBeforeSearch = getList;
}
// ajaxで検索候補取得
function getList(text)
{
        httpObj = new XMLHttpRequest();
        var eu=encodeURI(text);
        httpObj.open('GET','corpjson.php?searchword='+eu);
        httpObj.send(null);
        httpObj.onreadystatechange = function(){
                if ( (httpObj.readyState == 4) && (httpObj.status == 200) ){
                        var rst = httpObj.responseText;
//alert(httpObj.responseText);
                        sg.candidateList = eval(rst);
                        var list_count=sg.candidateList.length;
                        sg.suggestIndexList = [];
                        if (list_count != 0) {
                        //インデックスを作成する
                        for (i = 0; i < list_count; i++){
                                sg.suggestIndexList.push(i);
                        }
                        //リストの表示
                        sg.createSuggestArea(sg.candidateList);
                        }

                }
        }
        return true;
}
function clearText(t)
{
	if (t.value.indexOf("(")==0) t.value="";
	else t.select();
	t.style.color='#000';
}
function clearURL(t)
{
	if (t.value.indexOf("(")==0) t.value="http://";
	t.style.color='#000';
}
function setURL(t,pretext)
{
	t.style.color='#000';
	if (t.value=="http://") { t.value=pretext;   t.style.color='#aaa';} 
	if (t.value=="")  { t.value=pretext;   t.style.color='#aaa';}

}
function setEx(t,pretext)
{
	if (t.value=="") 
	{
		t.value=pretext;
		t.style.color='#aaa';
	}
//	checkValue();
}

function editCorp(){
	var i=1;

        var newcorp = document.getElementById('newcorp');
	if (document.frmtwi.corp_name.disabled)
	{
	newcorp.style.display='block';
	document.frmtwi.corp_name.disabled=false;
	document.frmtwi.address.disabled=false;
	document.frmtwi.url.disabled=false;
	document.frmtwi.corp_tag.disabled=false;
//alert('企業名、所在地なども編集可能になりました');
	}
	else
	{
	newcorp.style.display='none';
	document.frmtwi.corp_name.disabled=true;
	document.frmtwi.address.disabled=true;
	document.frmtwi.url.disabled=true;
	document.frmtwi.corp_tag.disabled=true;
//alert('企業名、所在地などが編集できなくなりました');
	}
}
function delete_tweet(repid) {
	if (confirm('削除しますか? (ただしTwitterの投稿は削除されません)')) del_tweet(repid);
	setTimeout("location.reload()",1000); 
}
function modosu_tweet(repid) {
	if (confirm('削除を取り消しますか?')) del_tweet(repid);
	setTimeout("location.reload()",1000); 
}
function del_tweet(repid)
{
	httpObj = new XMLHttpRequest();
	var ret = true;
	imgdel =document.getElementById('del_'+repid);
	httpObj.open('GET',"corpdel.php?r="+repid,true);
	httpObj.send(null);
//	httpObj.open('POST',"corpdel.php",true);
//	httpObj.send("r="+repid);
        httpObj.onreadystatechange = function(){
		if ((httpObj.readyState == 4) && (httpObj.status == 200) ){
		if (parseJSON(httpObj.responseText)=='0') imgdel.src='images/delete.jpg'; else imgdel.src='images/modosu.jpg';
		}
	}
}
function parseJSON(jsData)
{
        var data = eval("("+jsData+")");
        for(var i=0; i<data.length; i++)
        {
                var delflg = data[i].delete_flg; // delete flagのチェック
        }
        return delflg;
}


function wc(str){
	var i;
	var zenLen = "Ａ".length;
	var charType = 0;
	var bytes = 0;
	var len = 0;
	var lines = 0;
	var codes = 0;
	var crbytes = 0;
	var unicode = ("｡".charCodeAt(0) == 0xFF61);
	var codeLF = "\n".charCodeAt(0);
	var codeCR = "\r".charCodeAt(0);
 
 
	for (i = 0; i < str.length; i++, len++) {
		code = str.charCodeAt(i);
		if (code < 0) code += 0x0100;
		if (code <= 0x7E) {
			bytes++;
				if (charType == 1) codes++;
				else if (charType == 2) codes += 3;
				charType = 0;
				if (code == codeLF) {
					lines++;
					crbytes++;
//					len--;
					len++;
				} else if (code == codeCR) {
					crbytes++;
//					len--;
					len++;
				}
			} else if ((!unicode && code >= 0xA1 && code <= 0xDF) || (unicode && code >= 0xFF61 && code <= 0xFF9F)) {
				bytes++;
				if (charType == 0) codes++;
				else if (charType == 2) codes += 4;
				else if (i == 0) codes++;
				charType = 1;
			} else {
				bytes += 2;
				if (charType == 0) {
					codes += 3;
				} else if (charType == 1) {
					codes += 4;
				} else if (i == 0) {
					codes += 3;
				}
				charType = 2;
				if (zenLen == 2) {	// 全角を2バイトと数える処理系
					i++;
				}
			}
		}
		if (charType == 1) {
			codes++;
		} else if (charType == 2) {
			codes += 3;
		}
		if (str.length != 0 && str.charAt(str.length - 1) != "\n") lines++;

	var t = document.getElementById('wordcounter');
	if ((len <1)||(len>110)) 
	{
	t.style.color='red'; 
	document.frmtwi.twipost.disabled=true;
	}
	else 
	{
		t.style.color='black';
		checkValue();
	}
	t.innerHTML = 110-len;
	
}


var twiw=null;
/*
function twiwin(searchword){
        var sw=encodeURI(searchword);
alert(sw);
	twiw=window.open("corptwi.php?searchword="+sw,"twiwin",
	"width=400,height=240,toolbar=no,scrollbars=no,left=200,top=100");
}
*/

var dest_elm;
var corp_id;

function twiSearch(searchword, dest) {

	dest_elm=dest;
//alert(searchword);
        var sresult= document.getElementById(dest);
        searchword = encodeURI(searchword);
        var url = "http://search.twitter.com/search.json?result_type=mixed&show_user=true&rpp=50&callback=twitterCallback&q="+searchword;
//alert(searchword );
        while(sresult.firstChild) sresult.removeChild(sresult.firstChild);
        var script =  document.createElement("script");
        script.src = url;
        script.type = "text/javascript";
        document.body.appendChild(script);
}
function twitterCallback(e) {
        var i=0;
//alert(dest_elm);
	divopens=0;
	if (e && e.results) {
		var li, sresult =  document.getElementById(dest_elm), html, bgcolor,userid;
		resultcount=e.results.length;
//alert(resultcount);

		var twitext=null;
		bgcolor="#EEEEEE";
		displayblk="block";
		html="";
		for (i = 0, len = e.results.length; i < len; i++) {
			if (i%10==0) 
			{ 
				if (i>0)
				{
					html += "</table>\n";
					html += '<p><a href="javascript:show2nd(\''+dest_elm+'_'+i+'\');" >more</a></p></div>';
				}
				html += "<div id="+dest_elm+"_"+i+" style=\"display:"+displayblk+";\">\n"; 
				html +="<table border=0 width=300 align=center style=\"word-break:break-all\">\n";
				displayblk="none";
			}
			twitext	= e.results[i].text.replace(/(http:\/\/[\x21-\x7e]+)/gi, "<a href='$1' target=_blank>$1</a>")
			var cdate = new Date(e.results[i].created_at);
			if (bgcolor=="#EEEEEE") bgcolor="#CCCCEE"; else bgcolor="#EEEEEE";

			user_id=e.results[i].from_user_id_str;
            		html += "<tr bgcolor="+bgcolor+"><td width=\"50px\"><a href=\"http://twitter.com/"
			+ e.results[i].from_user+"\" target=_blank><img src=\""
			+ e.results[i].profile_image_url + "\" width=40px height=40px border=0 /></a></td>";
			html += "<td width=250px>"+twitext+" - "+cdate.toLocaleString();
			if ((twitter_user_id > 0)&&(user_id!=twitter_user_id))
//			html += '<a href="?c='+corp_id+'&s='+e.results[i].id_str+'">RT</a></td></tr>\n';
			html += "<a href=\"javascript:reTweet('"+corp_id+"','"+e.results[i].id_str+"');\">RT</a></td></tr>\n";
		}
		html += "</table></div>\n";
		sresult.innerHTML = html;
	}
        if ((i ==0)&&(sresult)) sresult.innerHTML = "<p>Twitterの検索結果なし</p>";
}

// Blockの表示／非表示の制御だけ
function showBlock(c){
        var block1 = document.getElementById(c);
//alert(block1);
	if (block1.style.display!='block')
	{
		block1.style.display='block';
		return true;
	}
	else
	{
		block1.style.display='none';
		return false;
	}
}
function show2nd(c)
{
	var a=showBlock(c);
}

var lastsearchword="";
function showSearchTweet2(cid)
{
	corp_id=cid;
	var src = 'twisct_'+corp_id;
	var dest = 'twirst_'+corp_id;
	var blk1 = 'twiblk_'+corp_id;
        var srct= document.getElementById(src);
	if (srct) showSearchTweet(blk1,srct.value,dest);
}
function showSearchTweet(blk1, searchword, dest)
{
        var sresult= document.getElementById(dest);
//alert(sresult);


	if ( showBlock(blk1)&&(searchword!=lastsearchword)) 
	{
//alert(searchword);
		twiSearch(searchword,dest);
		lastsearchword=searchword;
	}
	
}

// Yahoo!blog Search
function showSearchYahoo(blk1, searchword, ifr)
{
	showBlock(blk1);
        var ifr1= document.getElementById(ifr);
	if (ifr1.src.indexOf(encodeURI(searchword))>0) null;
	else
	{
//alert(ifr1.src);
	ifr1.src="corpyahoo.php?searchword="+encodeURI(searchword);
	}
//	if ((sresult.innerHTML=="-")||(sresult.innerHTML=="")) ySearch(searchword,dest);
}

function reTweet(c,s)
{
	if (confirm('Twitter 公式 ReTweetします')) document.location="?c="+c+"&s="+s;
}
