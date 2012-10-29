// called by index.php
// cate_id: <div> element name for inserting html contents.
// listUrl: rss url to get list.
function getLinks(cate_id,listUrl)
{
//	alert(cate_id);
	var cateList =document.getElementById(cate_id);
	if (cateList.innerHTML=="")
	{
		httpObj = new XMLHttpRequest();
		var ret = true;
		var cateList =document.getElementById(cate_id);
		url=encodeURI(listUrl);
		httpObj.open('GET',"catelist2.php?url="+url);
		httpObj.send(null);
       		httpObj.onreadystatechange = function(){
			if ( (httpObj.readyState == 4) && (httpObj.status == 200) ){
			cateList.innerHTML=httpObj.responseText;
//alert(httpObj.responseText);
			}
		}
	}
}
// yjsdk_at, style, SessID
function clearCookie()
{
	cName= "yjsdk_at=";
	dTime = new Date();
	dTime.setYear(dTime.getYear()-1);
	document.cookie = cName+";expires="+dTime.toGMTString();
	location.href="index.php";
}
