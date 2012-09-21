//////////////////////////////////////////////////
//
//	jQuery GPS
//
//////////////////////////////////////////////////
$(function(){
	//GPS情報取得を開始
	$('#start_gps').click(function(){
		navigator.geolocation.watchPosition(
			function(position){
				$('#latitude').html(position.coords.latitude); //緯度
				$('#longitude').html(position.coords.longitude); //経度

				//GoogleMapLOAD
				if (GBrowserIsCompatible()) {
					var map = new GMap2(document.getElementById("map"));
					map.addControl(new GLargeMapControl());

					map.addControl(new GMapTypeControl());

					var latlng = new GLatLng(position.coords.latitude,position.coords.longitude);
					map.setCenter(latlng, 14, G_NORMAL_MAP);

					var marker = new GMarker(latlng);
					map.addOverlay(marker);


					GEvent.addListener(map,'click',function(overlay, point){
						if(point){
							document.getElementById('click_lat').value = point.y;
							document.getElementById('click_long').value = point.x;
						}
					});
				}
			}
		);
	});
});