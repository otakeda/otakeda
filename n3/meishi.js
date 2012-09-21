
var member_id=null;
var token=null;
var rss_success=false;
var use_cache=false;
var userAgent = window.navigator.userAgent.toLowerCase();
var device="pc";
var pictureSource;
var destinationType;

function getDevice(){
	if (userAgent.indexOf('iphone')>0) device="iphone";
	if (userAgent.indexOf('android')>0) device="android";
}
function dateParse(str){
    var objDate = new Date(str);
    str = objDate.toLocaleDateString();
    return str;
}
function dateCompe(str1, datetime2){
	var date1= new Date(str1);
	var diffday=(date1.getTime()-datetime2)/86400000;
	return diffday;
}

function clearSaved(){
	if (window.confirm('アプリを初期化します')){
	localStorage.clear();
	$(".systemmsg").text("clear storage");
	}
}
var commenthtml="";
function getCommentRss(cxml){
	commenthtml="";
	var i=0;
	var link = $(cxml).find('link:first').text();
	var linkele = link.split("/");
	var nid = linkele.pop();
//	$("div[name=answers]:first").clone(true).attr("id", "answers_"+nid).insertAfter(this);

	$("#ans").clone().attr("id", "answers_"+nid).appendTo("body");
	//commenthtml="<ul data-role=\"listview\" id=\"anslist\">";
       	$(cxml).find('item').each(function(){
		var datestr = dateParse($(this).find('pubDate').text());
		var title = $(this).find('title').text() ;
		var link = $(this).find('link').text();
		var desc = $(this).find('description').text();
//		commenthtml+="<p>"+title+":"+datestr+"</p>";

		commenthtml+="<br />"+title+":"+datestr+"";
		if (i<10) $("ul[name=anslist]:last").append("<li>"+datestr+":"+$(desc).text()+"</li>");
		i++;
	});
//	commenthtml+="</ul>";
	if (i ==0) {
	$("#link_"+nid).remove();
	}
	else	{
	$("#link_"+nid).attr("rel","").text("回答:"+i+"").attr("href","#answers_"+nid);
	}
//alert($("#ans").html());
}
function setRss(xml){
	var i=0;
        $(xml).find('item').each(function(){
//	var emptyrow = $("div[name=qas]:last").clone(true);
//alert ("html:"+$("#qalist").html());
		var datestr = dateParse($(this).find('pubDate').text());
		var title = $(this).find('title').text() ;
		var link = $(this).find('link').text();
		var desc = $(this).find('description').text();
		var desc2 = $(desc).find('p').text();
		var catename = $(this).find('category').text() ;

//		var catename= $(desc).find('a').text();
//		var catelink= $(desc).find('a').attr("href");
		$("h3[name=qa_title]:eq("+i+"):empty").text( "["+catename+"] "+title+":"+datestr+"");
		$("h3[name=qa_title]:eq("+i+") span[class*='text']").prepend( "["+catename+"] "+title+":"+datestr+"");
//alert( $("h3[name=qa_title]:eq("+i+") span[class*='text']").html());
		var linkele = link.split("/");
		var nid = linkele.pop();
		$("p[name=qa_desc]:eq("+i+")").html(desc2+":<a href=\""+link+"/rss.xml\" rel=\"external\" id=\"link_"+nid+"\"  data-rel=\"dialog\" data-transition=\"pop\" >link</a>");
//		$("div[name=qas]:eq("+i+")").attr("id","qas_"+nid);

//alert(nid);
//		$("p[name=qa_desc]:eq("+i+")").html(desc2+":<a href=\"#answers\" data-transition=\"pop\" data-role=\"button\" data-inline=\"true\" data-rel=\"dialog\"  data-theme=\"c\" data-content-theme=\"c\">回答:"+comments+"</a>"+commenthtml);
//		$("#qalist").append(emptyrow);;

//alert ("Success:"+$("div[name=qas]:last").html());
//alert ("Success:"+$("#qalist").html());
	i++;

        });
	$("div[name=qas]:gt("+(i-1)+")").remove(); /// 10未満のときは残りを消す
}

function ajaxError(req,texterror,errorthrown){
	$(".systemmsg").html("cannot coummnicate web server"+texterror);
	rss_success=false;
	alert("サーバとの通信ができません");
//	if (xml!=null) setRss(xml);

	use_cache=true;
}

var lati,longi;
var marker_data = new Array();//マーカー位置の緯度経度
var map_scale=10;
function searchUsers(member_id,lati,longi){
	marker_data.push({position: new google.maps.LatLng(lati, longi), id: member_id, content: member_id+'You' , image:null});
	$.ajax({
		type: "POST",
		async:false,
       		url : "meishi.php",
		dataType: "json",
		data: { "client_id": "a", "lati":lati, "longi":longi ,"member_id":member_id },
		cache : true,
		timeout : 5000,
		error: ajaxError,
		success:  function(data){
			min_dist2=100000.000000000011;
			$(".systemmsg").text("count="+data.cards.length);
			for (i =0; i<data.cards.length;i++){
			if (data.cards[i].lati>0&&data.cards[i].longi>0){
				marker_data.push({position: new google.maps.LatLng(data.cards[i].lati, data.cards[i].longi), 
				id: data.cards[i].meishi_id, content: '<img src="'+data.cards[i].imgfile+'" onclick=\"javascript:createRelation('+data.cards[i].meishi_id+');\" />',
				image: data.cards[i].imgfile});
//marker_data.push({position: new google.maps.LatLng(data.cards[i].lati, data.cards[i].longi), content: data.cards[i].imgfile});
			if (min_dist2>Math.pow(lati-data.cards[i].lati,2)+Math.pow(longi-data.cards[i].longi,2)) min_dist2=Math.pow(lati-data.cards[i].lati,2)+Math.pow(longi-data.cards[i].longi,2);
			}
			}
			if (min_dist2<0.1) map_scale=12;
			if (min_dist2<0.001) map_scale=14;
			if (min_dist2<0.00001) map_scale=16;
			if (min_dist2<0.0000001) map_scale=18;
		}
	});
}
function createRelation(partner_id){
	$.ajax({
		type: "POST",
       		url : "meishi.php",
		dataType: "json",
		data: { "client_id": "a", "member_id":member_id, "partner_id":partner_id},
		cache : false,
		timeout : 5000,
		error: ajaxError,
		success:  function(data){
			$(".systemmsg").html(data.METHOD);
			alert("名刺をもらいました。(会員番号:"+partner_id+")");
		}
	});

}
function adminUsers(member_id){
	$.ajax({
		type: "GET",
       		url : "meishi.php",
		dataType: "json",
		data: { "client_id": "a", "member_id":member_id, "admin":"1"},
		cache : false,
		timeout : 5000,
		error: ajaxError,
		success:  function(data){
			$(".systemmsg").text("ユーザ数:"+data.cards.length);
			var basedate= new Date();
			var basesec= basedate.getTime()-86400000*1;
			var baseline=1;
			var baseword="きのう";
			for (i =0; i<data.cards.length;i++){
				$("li:has(.cimg_admin):last").clone(true).insertAfter("li:has(.cimg_admin):last");
				$(".cimg_admin:last").attr("src", data.cards[i].imgfile);
				$(".cimg_admin+p:last").text(data.cards[i].r_update_date+":"+data.cards[i].meishi_id);

				if (dateCompe(data.cards[i].r_update_date,basesec) <0) {
				$("li:has(.cimg_admin):last").before("<li data-role=\"list-divider\">"+baseword+"</li>\n");
				if (baseline==1) { basesec-=86400000*7; baseword="１週前";}
				if (baseline==2) { basesec-=86400000*30; baseword="１ヶ月前";}
				if (baseline==3) { basesec-=86400000*365; baseword="１年前"}
				baseline++;
				}
			}
			$("li:has(.cimg_admin):first").remove();
		}
	});
}
function getUserInfo(member_id){
		$.ajax({
		type: "GET",
		url : "meishi.php",
		data: { "client_id": "a", "member_id":member_id },
		dataType: "json",
		cache : false,
		timeout : 5000,
		error: ajaxError,
		success : function(data){ 
			$('#img_mycard').attr('src', data.mycard.imgfile);
			$(".systemmsg").text(data.mycard.imgfile);
			}
		});
}
$('#mycard').live('pageinit',function(event){
	getToken();
	if (loadParam()){   //member_id is in localStorage
		$('.cmember_id').text(member_id);
		getUserInfo(member_id);
	}
	$("#img_before").draggable({
		snap:"#img_mycard",
		containment:"#img_zone",
		helper:"clone"

	});
	$("#img_mycard").droppable({
		accept:"#img_before",
		drop: function(e,ui){
			alert ("replace image");
		}
	});
});
$( '#admin' ).live( 'pageinit',function(event){
	if (loadParam()){   //member_id is in localStorage
		$('.cmember_id').text(member_id);
		getUserInfo(member_id);
	}
	if (member_id>0) adminUsers(member_id);
});
/*
function setMyPosition(lati,longi){
	$.ajax({
		type: "POST",
       		url : "meishi.php",
		dataType: "json",
		data: { "client_id": "a", "member_id":member_id, "lati":lati, "longi":longi},
		cache : false,
		timeout : 5000,
		error: ajaxError,
		success:  function(data){
			$(".systemmsg").html(data.METHOD);
			alert("Position set");
		}
	});
	
}
*/
$( '#search' ).live( 'pageinit',function(event){
	getToken();
	if (loadParam()){   //member_id is in localStorage
		$('.cmember_id').text(member_id);
		getUserInfo(member_id);
	}
        navigator.geolocation.watchPosition(
            function(position){
                $('#latitude').html(position.coords.latitude); //緯度
                $('#longitude').html(position.coords.longitude); //経度
                lati= position.coords.latitude;
                longi=position.coords.longitude;
		//setMyPosition(lati,longi);
		url= 'meishi.php?client_id=a&lati='+lati+'&longi='+longi;
//alert(url);
		function attachMessage(marker, msg) {
			google.maps.event.addListener(marker, 'click', function(event) {
			new google.maps.InfoWindow({
			content: msg
			}).open(marker.getMap(), marker);
    		});
  		}

		searchUsers(member_id,lati,longi);
		var myMap = new google.maps.Map(document.getElementById('gmap'), {
			zoom: map_scale,//地図縮尺
			center: new google.maps.LatLng(lati, longi),//地図の中心点
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
		for (i = 0; i < marker_data.length; i++) {
			var image = new google.maps.MarkerImage(marker_data[i].image,
				new google.maps.Size(70,50),
				new google.maps.Point(0,0),
				new google.maps.Point(0,50)
				);
			var myMarker = new google.maps.Marker({
			position: marker_data[i].position,
			map: myMap,
//			icon: image,
			title: marker_data[i].image
			});
			attachMessage(myMarker, marker_data[i].content);
			$("li:has(.cimg_search):last").clone(true).insertAfter("li:has(.cimg_search):last");
			$(".cimg_search:last").attr("src", marker_data[i].image);
			$(".cimg_search+p:last").text(marker_data[i].id);
			
		}
			$("li:has(.cimg_search):first").remove();

            }
	);
});

function saveParam() {
        localStorage.member_id=member_id;
	$('.cmember_id').text(member_id);
}
function loadParam(){
	if (localStorage.member_id!=undefined)
		member_id=localStorage.member_id;
	if (member_id>0) return true; else return false;
}

/*
function parseJSON(jsData) {
        var data = eval("("+jsData+")");
	member_id=data.mycard.meishi_id;
	$('#img_mycard').attr('src', 'upload/'+data.imgfile.filename);
	member_id=data.mycard.meishi_id;
alert('member_id='+member_id);
	saveParam();
}
*/
function getToken(){
		$.ajax({
		type: "GET",
		url : "meishi.php",
		dataType: "json",
		cache : false,
		timeout : 5000,
		error: ajaxError,
		success : function(msg){ $('.ctoken').text(msg.token); token=msg.token; }
		});
}
//
function onPhotoDataSuccess(imageData) {
      // 下記のコメントを外すことでBase64形式のデータをログに出力
      // console.log(imageData);

      // 画像ハンドルを取得
      //
      var smallImage = document.getElementById('smallImage');

      // 画像要素を表示
      //
      smallImage.style.display = 'block';

      // 取得した写真を表示
      // 画像のリサイズにインラインCSSを使用
      //
      smallImage.src = "data:image/jpeg;base64," + imageData;
}

    // 写真の撮影に成功した場合（その2）
    //
function onPhotoURISuccess(imageURI) {
      // 下記のコメントを外すことでファイルURIをログに出力
      // console.log(imageURI);

      // 画像ハンドルを取得
      //
      var largeImage = document.getElementById('largeImage');

      // 画像要素を表示
      //
      largeImage.style.display = 'block';

      // 取得した写真を表示
      // 画像のリサイズにインラインCSSを使用
      //
      largeImage.src = imageURI;
}

function onCameraFail(msg){
	alert(" Error :"+msg);
}
function capturePhoto(){
	navigator.camera.getPicture(onPhotoDataSuccess,onCameraFail,{ quality:50 });
}
function getPhoto(source){
	navigator.camera.getPicture(onPhotoURISuccess,onCameraFail,{quality:50,destinationType:destinationTYpe.FILE_URI,sourceType:source});
}
function onDeviceReady(){
	pictureSource=navigator.camera.PictureSourceType;
	destinationType=navigator.camera.DestinationType;
}
$(function(){

	getDevice();


	document.addEventListener("deviceready",onDeviceReady,false);


	$('input[type=file]').change(function() {
		var params = 'client_id=a';
		if (member_id > 0) params+= "&member_id="+member_id;   //update or insert
		params+= "&token="+token;
alert("param="+params);
		$(this).upload('meishi.php', params,
			function(data) {
				$('.systemmsg').html(data);
				member_id=data.mycard.meishi_id;
				$('#img_before').attr('src',$('#img_mycard').attr('src'));
				$('#img_mycard').attr('src', 'upload/'+data.imgfile.filename);
				member_id=data.mycard.meishi_id;
alert('filename='+data.imgfile.filename);
				saveParam();
			}, 'json');
	});


});
