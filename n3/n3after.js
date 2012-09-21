	refresh();
	setInterval( function(){
	refresh();
	}, 10000);
	localStorage.refresh_count=0;
	
	function $(id) { return document.getElementById(id); }
	var dropbox = $("dropbox");

	window.onload = function(){
	window.addEventListener("dragenter",dragenter,true);
	window.addEventListener("dragleave",dragleave,true);
	dropbox.addEventListener("dragover",dragover,true);
	dropbox.addEventListener("drop",drop,true);
	};
	function  dragenter(e) { dropbox.style.backgroundColor='red'; }
	function  dragleave(e) { dropbox.style.backgroundColor='blue'; }
	function  dragover(e) { e.preventDefault(); }
	function drop(e){
		var dt=e.dataTransfer;
		var files=dt.files;
		e.preventDefault();
		if (files.length==0) return false;
		if (!files[0].type.match(/image\/\w+/)){
		alert ('画像ファイルだけ'); 
		return ;
		}
		var reader = new FileReader();
		reader.onload = function() {
			var imgd= $("imgd");
			imgd.src=reader.result;
		};
		reader.onerror = function(e){
			dropbox.innerHTML="ERROR";
			for (var key in reader.error){
				dropbox.innerHTML+= key+"="+reader.error[key]	+"<br />";
			}
		};
		reader.readAsDataURL(files[0]);
		dropbox.innerHTML="name=" + files[0].name + "<br>type=" +files[0].type ;
	}

