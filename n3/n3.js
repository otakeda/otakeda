
var activecolor="#ffaaaa";
var wincolor="red";
var winback="rgb(50,50,50)";
var num3="";
var set_mode="entry";
var set_num=1;
var hit_row=1;
var player_no=0;
var player_key="";
var game_key="";
var locked=false;
var answer="";
var display_rows=5;

function showDebug(str)
{
//	var t = document.getElementById("info_area");
//	alert(str);
//	t.innerHTML+=str+"<br />\n";
	return ;
}

function len(str)
{
	if (isnull(str)) return 0;
	else return str.length;
}
function isnull(val)
{
	if (val==null) return true;
	if (val=="null") return true;
	if (val=="") return true;
	if (val==undefined) return true;
	if (val=="undefined") return true;
	return false;
}
function savePara()
{
	localStorage.set_mode=set_mode;
	localStorage.answer=answer;
	localStorage.player_key=player_key;
	localStorage.game_key=game_key;
	localStorage.hit_row=hit_row;
	localStorage.player_no=player_no;
	localStorage.refresh_count++;
	showInfo();
}
function showInfo()
{
	var t ;
	t="[game_key]"+game_key;
	t+="[player_key]"+player_key;
	t+="[player_no]"+player_no;
	t+="[set_mode]"+set_mode;
	t+="[answer]"+answer;
	t+="[hit_row]"+hit_row;
	t+="[set_num]"+set_num;
	t+="[COUNTER]"+localStorage.refresh_count;

	if (navigator.geolocation==undefined) t+="Cannot use geo-location";
	$("#info_area").html(t);
/*
	navigator.geolocation.getCurrentPosition( successCallback, errorCallback);
	var lat=0,lng=0;
	function successCallback(position)
	{
		t.innerHTML+="Success!:";
		for (var prop in position.coords){
		if (prop=="latitude") lat=position.coords[prop];
		if (prop=="longitude") lng=position.coords[prop];
		t.innerHTML+= prop+":"+position.coords[prop]+"/";
		}	
		if ((lat>0)&&(lng>0))	
		{
		var mapdiv=document.getElementById("map_area");
		
		var latlng = new google.maps.LatLng(29.1,30.222);
//		var latlng = new google.maps.LatLng(lat,lng);
		var gmap= new google.maps.Map(
		mapdiv, {
			zoom: 2,
			center: latlng,
			mapType_Id: google.maps.MapTypeId.ROADMAP
			}
		);
		}
	}	
	function errorCallback(err)
	{
		t.innerHTML+="ERROR!:"+err.code+":"+err.message;
	}
*/
	
}
function setAnswer()
{
	resumeNumLinks();
	var str0 =set_mode;
	str0+= '_'+player_no;
	if (set_mode=='hit' ) str0+='_'+hit_row;
	var val=$("#"+str0+"_1").html();
	val+=$("#"+str0+"_2").html();
	val+=$("#"+str0+"_3").html();
showDebug("setAnswer:"+set_mode+":"+val);
	if (set_mode=='hit')  //下の数字
	{
		save_hit(val);
		hit_row++;
			while  (display_rows<hit_row)
			{
				display_rows++;
				addRow(display_rows);
			}
	}

	if (set_mode=='answer') {  //上の数字
		set_mode='hit'; hit_row=1; 
		save_answer(val);
	}
	set_num=1;
	num3="";
	setActive();
}
function cancelAnswer()
{
	resumeNumLinks();
	if (set_mode=='hit')  //下の数字
	{
	$("#hit_"+player_no+"_"+hit_row+"_1").innerHTML="";
	$("#hit_"+player_no+"_"+hit_row+"_2").innerHTML="";
	$("#hit_"+player_no+"_"+hit_row+"_3").innerHTML="";
	}
	if (set_mode=='answer') {  //上の数字
	$("#answer_"+player_no+"_1").innerHTML="";
	$("#answer_"+player_no+"_2").innerHTML="";
	$("#answer_"+player_no+"_e3").innerHTML="";
	}
	num3="";
	set_num=1;
	setActive();
}
function addRow(rownum)
{
	var table1 = document.getElementById("tab_hits");
	var row1 = table1.insertRow(-1);
	var cell_num = row1.insertCell(-1);
	var cell_21 = row1.insertCell(-1);
	var cell_22 = row1.insertCell(-1);
	var cell_23 = row1.insertCell(-1);
	var cell_2 = row1.insertCell(-1);
	var cell_11 = row1.insertCell(-1);
	var cell_12 = row1.insertCell(-1);
	var cell_13 = row1.insertCell(-1);
	var cell_1 = row1.insertCell(-1);

	cell_num.setAttribute("class","h_header");
	cell_21.setAttribute("class","hit");
	cell_22.setAttribute("class","hit");
	cell_23.setAttribute("class","hit");
	cell_2.setAttribute("class","hits");
	cell_11.setAttribute("class","hit");
	cell_12.setAttribute("class","hit");
	cell_13.setAttribute("class","hit");
	cell_1.setAttribute("class","hits");


	cell_num.innerHTML=rownum;
	cell_21.innerHTML = "<div id=\"hit_2_"+rownum+"_1\"></div>";
	cell_22.innerHTML = "<div id=\"hit_2_"+rownum+"_2\"></div>";
	cell_23.innerHTML = "<div id=\"hit_2_"+rownum+"_3\"></div>";
	cell_2.innerHTML = "<div id=\"hits_2_"+rownum+"\"></div>";
	cell_11.innerHTML = "<div id=\"hit_1_"+rownum+"_1\"></div>";
	cell_12.innerHTML = "<div id=\"hit_1_"+rownum+"_2\"></div>";
	cell_13.innerHTML = "<div id=\"hit_1_"+rownum+"_3\"></div>";
	cell_1.innerHTML = "<div id=\"hits_1_"+rownum+"\"></div>";
}

function resumeNumLinks()
{

	for (i=0;i<10;i++)
	{
	$('#num_'+i).attr("href","javascript:setNum("+i+");");
	$('#num_'+i).css("background","");
	}
}
function clearNumLink(nid)
{
	$('#num_'+nid).attr("href","");
	$('#num_'+nid).css("background"," rgb(100,100, 100)");
}
function setNum(nid)
{
	if ((set_mode!='hit')&&(set_mode!='answer'))
	{
		alert("参加者2名そろった後スタートできます");
		return;
	}
	if (locked) {alert("Lock中です。相手まちです。"); return;}
	if (num3.indexOf(String(nid),0) >= 0) { alert("同じ数字は設定できません"); return; }
	if (set_num>3) alert("Cancelボタンでクリアすると最初から設定できます");
	else
	{
		var str0 =set_mode, nextstr, curstr;
		str0+= '_'+player_no;
		if (set_mode== 'hit') str0+='_'+hit_row;
		curstr= str0+'_'+set_num;
showDebug("setNum:"+curstr);
		$("#"+curstr).html(nid);
//		document.getElementById(curstr).style.background="";

		clearNumLink(nid);
		num3+=String(nid);
		
		set_num++;
		setActive();
	}
}
function save_hit(val)
{
	if (confirm('Hit? '+val))
	{
		var url = "n3json.php?game_key="+game_key+"&player="+player_key+"&hits="+hit_row+"&hit="+val;
		send_rest(url);
	}
	else setActive();
}
function save_answer(val)
{
	if (confirm('Answer? '+val))
	{
		var url = "n3json.php?game_key="+game_key+"&player="+player_key+"&answer="+val;
		send_rest(url);
	}
	else setActive();
}
function entering(uid)
{
	var randnum = uid*100+Math.floor(Math.random()*100);
	if (confirm('Enter? '+randnum))
	{
		player_key=randnum;
		var str0 =$('#entry_'+uid);
		$("#"+str0).text("Entering...");
		player_no=uid;
		set_num='1';

		var url = "n3json.php?player="+randnum;
//		if (!isnull(game_key)) url+= "&game_key="+game_key;
		if (uid==2) url+= "&game_key="+game_key;
		send_rest(url);
	}
}
function clear_storage()
{
	localStorage.clear();
	alert("Clear LocalStorage");
	set_mode="entry";
	set_num=1;
	hit_row=1;
	player_no=0;
	player_key="";
	game_key="";
	locked=false;
	display_rows=5;
	refresh_count=0;
	refresh();
	location.reload();
}
function refresh()
{
	if (isnull(game_key) && (localStorage.game_key!=undefined))
	{
		game_key=localStorage.game_key;
	}
	if (isnull(player_key) && (localStorage.player_key!=undefined))
	{
		player_key=localStorage.player_key;
	}

	var url = "n3json.php";
	if (!isnull(game_key))
	{
		url = "n3json.php?game_key="+game_key;
//alert("gamekey="+game_key);
	}
	send_rest(url);
}
function send_rest(url)
{

showDebug("send_rest:"+url);
	httpObj = new XMLHttpRequest();
	httpObj.open('GET',url,true);
	httpObj.setRequestHeader('Pragma', 'no-cache');
	httpObj.setRequestHeader('Cache-Control', 'no-cache');
	httpObj.setRequestHeader('If-Modified-Since', 'Thu, 01 Jun 1970 00:00:00 GMT');
	httpObj.send(null);
        httpObj.onreadystatechange = function(){
		if ( (httpObj.readyState == 4) && (httpObj.status == 200) ){
		if (parseJSON(httpObj.responseText)==0) showDebug("JSON data:No data found");
		savePara();
		setActive();

		}
	}
}
function parseJSON(jsData)
{
        var data = eval("("+jsData+")");
showDebug("JSON-DATA:"+jsData);
	var fellow_key="";
	var fellow_no=0;
        if (isnull(game_key)) game_key = data.game_key; 

	if (!isnull(data.player2))
	{
	if (player_key==data.player2) { player_no=2; fellow_no=1; fellow_key=data.player1;}
		else { fellow_key=data.player2; fellow_no=2;}
	}
	else 
	{
		document.getElementById("entry_2").innerHTML="<a href=\"javascript:entering(2);\">Please Entry</a>";
	}
	
	if (!isnull(data.player1))
	{
		if (player_key==data.player1) { player_no=1; fellow_no=2; fellow_key=data.player2;}
		else { fellow_key=data.player1; fellow_no=1;}
	}
	else{
		document.getElementById("entry_1").innerHTML="<a href=\"javascript:entering(1);\">Please Entry</a>";

		document.getElementById("entry_2").innerHTML="";
	}

	if (player_no > 0)
	{
		var str0 =document.getElementById('entry_'+player_no);
		str0.innerHTML="You";
		if (player_no==1) document.getElementById("entry_2").innerHTML="Waiting";
	}
	if (len(fellow_key)>0) 
	{
		var str1 =document.getElementById('entry_'+fellow_no);
		str1.innerHTML=fellow_key;
	}


	if (!isnull(data.status)) set_mode=data.status;
showDebug("status:"+data.status);

//	t.innerHTML+="<br />Answer1="+data.answer1;
	
	var str0="answer_1";
	var str="-";
	if (player_no==1)
	{	
		if ((player_no==1) && (len(data.answer1)==3))
		{
		answer=data.answer1;
		document.getElementById(str0+"_1").innerHTML= data.answer1.substr(0,1);
		document.getElementById(str0+"_2").innerHTML= data.answer1.substr(1,1);
		document.getElementById(str0+"_3").innerHTML= data.answer1.substr(2,1);
		}
	}
	else
	{
	valstr="-";
	if (len(data.answer1)==3) valstr="*";
	document.getElementById(str0+"_1").innerHTML= valstr;
	document.getElementById(str0+"_2").innerHTML= valstr;
	document.getElementById(str0+"_3").innerHTML= valstr;
	}

	str0="answer_2";
	if (player_no==2)
	{
		if (len(data.answer2)==3) //３つあれば上書き	
		{
		answer=data.answer2;
		document.getElementById(str0+"_1").innerHTML= data.answer2.substr(0,1);
		document.getElementById(str0+"_2").innerHTML= data.answer2.substr(1,1);
		document.getElementById(str0+"_3").innerHTML= data.answer2.substr(2,1);
		}
	//3つなければ＝＞なにもしない
	}
	else
	{
	if (len(data.answer2)==3) valstr="*";  //３つなら*まだなら-
	else valstr="-";
	document.getElementById(str0+"_1").innerHTML= valstr;
	document.getElementById(str0+"_2").innerHTML= valstr;
	document.getElementById(str0+"_3").innerHTML= valstr;
	}


	if ((len(data.answer1)==3)&&(len(data.answer2)==3)) //答えをどちらもセット済	
	{
		set_mode="hit";
		max_row=0;
        for(var i=0; i<len(data.hit); i++)
        {
		if (isnull( data.hit[i].playerno)) break;
//		t.innerHTML+="<br />p=" + data.hit[i].playerno;
		for (var j=1; j< 4;j++)
		{
			var str0="hit_"+data.hit[i].playerno+"_"+data.hit[i].hits+"_"+j;
			while  (display_rows-1<data.hit[i].hits)
			{
				display_rows++;
				addRow(display_rows);
			}
			document.getElementById(str0).innerHTML = data.hit[i].hit.substr(j-1,1);
		}
		str0="hits_"+data.hit[i].playerno+"_"+data.hit[i].hits;
		document.getElementById(str0).innerHTML = data.hit[i].eats+"-"+data.hit[i].bites;
		if (data.hit[i].eats==3)
		{
			document.getElementById(str0).style.background=winback;
			document.getElementById(str0).style.color=wincolor;
			document.getElementById(str0).style.fontsize="120%";
		}

		if ((document.getElementById("hits_1_"+data.hit[i].hits).innerHTML!="") && (document.getElementById("hits_2_"+data.hit[i].hits).innerHTML!="") && (max_row<data.hit[i].hits)) max_row=parseInt(data.hit[i].hits);
        }
	hit_row=max_row+1;
		locked=false;
		if ((player_no==1)&&(document.getElementById("hits_1_"+hit_row).innerHTML!=""))  locked=true;
		if ((player_no==2)&&(document.getElementById("hits_2_"+hit_row).innerHTML!=""))  locked=true;
	}
	else
	{
	if (len(answer)==3) locked=true; //自分の答えだけ。
	}
        return len(data.hit);
}
function setActive()
{
	if (set_mode=="entry")
	{
		document.getElementById("entry_1").style.background=activecolor;
		document.getElementById("entry_2").style.background=activecolor;
	}
	else
	{
		document.getElementById("entry_1").style.background="";
		document.getElementById("entry_2").style.background="";
	}
	for (i=1;i<4;i++)
	{
		document.getElementById("answer_1_"+i).style.background="";
		document.getElementById("answer_2_"+i).style.background="";
	}
	if ((set_mode=="answer") &&(set_num<4)&&(!locked))
	{
		var str0 =set_mode, nextstr, curstr;
		str0+= '_'+player_no;
		if (set_mode== 'hit') str0+='_'+hit_row;
		curstr= str0+'_'+set_num;
		document.getElementById(curstr).style.background=activecolor;
	}
	for (j=1;j<display_rows+1;j++)
	{
	for (i=1;i<4;i++)
	{
		document.getElementById("hit_1_"+j+"_"+i).style.background="";
		document.getElementById("hit_2_"+j+"_"+i).style.background="";
	}
	}
	if ((set_mode=="hit")&&(set_num<4)&&(!locked))
	{
		var str0 =set_mode, nextstr, curstr;
		str0+= '_'+player_no;
		if (set_mode== 'hit') str0+='_'+hit_row;
		curstr= str0+'_'+set_num;
//alert(curstr);
		document.getElementById(curstr).style.background=activecolor;
		document.getElementById(curstr).innerHTML="-";
	}

// ボタンの状態
	if (set_num>3)
	{
		document.getElementById('btn_set').disabled=false;
	}
	else
		document.getElementById('btn_set').disabled=true;
	
	if (set_num==1)
		document.getElementById('btn_cancel').disabled=true;
	else
		document.getElementById('btn_cancel').disabled=false;
}

