$('#graph').live('pageinit',function(event){
	var gets = window.location.search;
//	alert(gets);
	var a= $.jqplot('graph0',  [[[1, 2],[3,5.12],[5,13.1],[7,33.6],[9,85.9],[11,219.9]]]);
});

