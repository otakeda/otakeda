
function checkValue()
{
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
function valueCheckSubmit()
{
//企業名は必須
	if (document.frmtwi.corp_name.value.indexOf("(")==0) {  document.frmtwi.twipost.disabled = false;
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
function clickStar(no)
{
	starClicked=document.getElementById('star'+no);
	for (i = 1; i <= 5; i++) {
	if (no>=i)
//	a = document.getElementById('star'+i);
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
function getList(text)
{
        httpObj = new XMLHttpRequest();
        var eu=encodeURI(text);
        httpObj.open('GET','qajson.php?searchword='+eu);
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
	if (t.value=="http://") t.value=pretext;
	if (t.value=="") t.value=pretext;

	t.style.color='#aaa';
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
function showTweet(c){
	var i=1;
        var block1 = document.getElementById(c);
	if (block1.style.display=='none')
	block1.style.display='block';
	else
	block1.style.display='none';
}

function editCorp(){
	var i=1;
        var newcorp = document.getElementById('newcorp');
//	if (document.frmtwi.corp_name.disabled)
	if (newcorp.style.display=='none')
	{
	newcorp.style.display='block';
//alert('企業名、所在地なども編集可能になりました');
	}
	else
	{
	newcorp.style.display='none';
	}
}
function delete_tweet(repid)
{
	if (confirm('削除しますか?')) del_tweet(repid);
}
function modosu_tweet(repid)
{
	if (confirm('削除を取り消しますか?')) del_tweet(repid);
}
function del_tweet(repid)
{
	httpObj = new XMLHttpRequest();
	var ret = true;
	imgdel =document.getElementById('del_'+repid);
	httpObj.open('GET',"corpdel.php?r="+repid,true);
	httpObj.send(null);
        httpObj.onreadystatechange = function(){
		if ( (httpObj.readyState == 4) && (httpObj.status == 200) ){
		if (parseJSON(httpObj.responseText)=='0') imgdel.src='images/delete.jpg'; else imgdel.src='images/modosu.jpg';
		}
	}


}
function parseJSON(jsData)
{
        var data = eval("("+jsData+")");
        for(var i=0; i<data.length; i++)
        {
                var delflg = data[i].delete_flg; // 商品コード
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


