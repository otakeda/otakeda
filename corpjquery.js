$(function(){
	var a=0;
       	var width0=425;
	var height0=344;
/*
       	$(".videotag iframe").each(function(){
        	width0=$(this).attr("width");
		height0=$(this).attr("height");
		url = $(this).attr("src");
//	alert($(this).parent().parent().html());
		$(this).parent().parent().attr("width",width0);
		$(this).parent().attr("height",height0);
		if(url.match(/^http:\/\/(?:www\.youtube\.com\/embed\/)([\w-]+)/)) {
			$(this).parent().after('<p><a class="youtube" href="'+url+'" ><img src="http://i.ytimg.com/vi/' + RegExp.$1 + '/0.jpg" width=300 /></a></p>');
		$(this).parent().css("display","none");
		}
        });
*/
       	$(".videotag").each(function(){
		url = $(this).html();
		if (url.match(/http:\/\/(?:youtu\.be\/)([\w-]+)/)) {
			$(this).after('<p><a class="youtube" href="http://www.youtube.com/embed/'+RegExp.$1+'" ><img src="http://i.ytimg.com/vi/' + RegExp.$1 + '/0.jpg" width=300 /></a></p>');
		}
		if(url.match(/http:\/\/(?:www\.youtube\.com\/embed\/)([\w-]+)/)) {
			$(this).after('<p><a class="youtube" href="'+url+'" ><img src="http://i.ytimg.com/vi/' + RegExp.$1 + '/0.jpg" width=300 /></a></p>');
		}
		$(this).css("display","none");
	});
	$(".youtube").colorbox({iframe:true, innerWidth:425, innerHeight:344, opacity:0.5});
	$(".howtopaste").colorbox({ opacity:0.5});

});
