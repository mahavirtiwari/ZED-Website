function initMap() {
	if($('#map').length){
		var styledMapType = new google.maps.StyledMapType(
			[
			{
				"featureType": "water",
				"elementType": "geometry.fill",
				"stylers": [
				{
					"color": "#53a92c"
				}
				]
			}
			],
			{name: 'ZED'});
		var map = new google.maps.Map(document.getElementById('map'), {
			center: {lat:28.6275964, lng:77.24532},
			zoom: 18,
			scrollwheel:false,
			icon: 'assets/images/map-icon.svg',
			mapTypeControlOptions: {
				mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain',
				'styled_map']
			}
		});
		var myLatlng = new google.maps.LatLng(28.6275964, 77.24532);		
		var marker = new google.maps.Marker({
			position: myLatlng,
			icon: "assets/images/home01/map-icon.svg",
			title:"ZED QCI"
		});	
		marker.setMap(map);
		map.mapTypes.set('styled_map', styledMapType);
		map.setMapTypeId('styled_map');
	}
}