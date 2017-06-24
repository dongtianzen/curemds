
function googleMapLoad() {
	//show map on page
	var center = new google.maps.LatLng(53.508742, -90.120850);
	var map = new google.maps.Map(document.getElementById('map'), {
	  'zoom': 3,
	  'center': center,
	  'mapTypeId': google.maps.MapTypeId.ROADMAP,
	  'streetViewControl': false,
	  'panControl': false,
	  'mapTypeControl': false,
	});

	// get json file
	var jsonData ='';
	var jsonPath = drupalSettings.path.baseUrl + 'modules/custom/dashpage/angular/googlemap.json';
	jQuery.ajax({
	  'async': false,
	  'global': false,
	  'url': jsonPath,
	  'dataType': "json",
	  'success': function(data) {
	     jsonData = data;
	  }
	});

	/**
	 * map cluster style
	 */
	function mapClusterStyle() {

		var mapMarkerUrlPath = drupalSettings.path.baseUrl + 'libraries/google_map/images/mapmaker/';
		var clusterStyles = [{
		  textColor: '#000000',
		  url: mapMarkerUrlPath + 'm3.png',
		  height: 45,
		  width: 30,
		  anchorText: [-7, 0]
		}, {
		  textColor: '#000000',
		  url: mapMarkerUrlPath + 'map_marker_blue.png',
		  height: 55,
		  width: 38,
		  anchorText: [-10, -2]
		}, {
		  textColor: '#000000',
		  url: mapMarkerUrlPath + 'map_marker_red.png',
		  height: 42,
		  width: 42,
		}];
		var mapClusterOptions = {
		  gridSize: 50,
		  styles: clusterStyles,
		  maxZoom: 15,
		};

		return mapClusterOptions;
	}

	/**
	 *
	 */
	function infoBubbleSettings() {
		var infoBubble = new InfoBubble({
		  minWidth: 20,
		  maxWidth: 320,
		  minHeight: 100,
		  maxHeight: 500,
		  disableAutoPan: false,
		  shadowStyle: 1,
		  padding: '0px',
		  borderRadius: 5,
		  arrowSize: 10,
		  hideCloseButton: false,
		  arrowPosition: 7,
		  backgroundClassName: 'phoney',
		  pixelOffset: new google.maps.Size(130, 120),
		  arrowStyle: 2,
		  content: '',
		});

		return infoBubble;
	}

	var markers = [];
	jQuery.each(jsonData, function(key, value) {
	  var latitude = Number(value.lat).toFixed(3);
	  var longitude = Number(value.lng).toFixed(3);
		var latLng = new google.maps.LatLng(latitude, longitude);
		var marker = new google.maps.Marker({
	    position: latLng,
	    map: map,
	    icon: drupalSettings.path.baseUrl + 'libraries/google_map/images/' + 'marker_icon.png',
	  });
	  markers.push(marker);

	  var unitInfoPopupHtml = " ";
	  unitInfoPopupHtml += '<div id="wrapper" class="overflow-hidden white-space-nowrap">';
		  unitInfoPopupHtml += '<div class="col-md-12 bg-0082ba margin-bottom-20 padding-bottom-12" >';
		    unitInfoPopupHtml += '<div>';
		    unitInfoPopupHtml += '<div class="col-md-12 color-fff font-size-16 padding-top-12"><span>' + value.programName + '</span></div>';
		    unitInfoPopupHtml += '</div>';
		  unitInfoPopupHtml += '</div>';


		  unitInfoPopupHtml += '<div class="row margin-top-15 padding-left-14">';
		    unitInfoPopupHtml += '<div class="col-md-12 ">';
		      unitInfoPopupHtml += '<div class="col-md-6 margin-top-6 font-size-16 color-0082ba">';
		        unitInfoPopupHtml += '<span class="fa fa-bookmark padding-6"></span>';
		        unitInfoPopupHtml += '<span class="color-000 font-size-14">' + value.unitName + '</span>';
		      unitInfoPopupHtml += '</div>';
		      unitInfoPopupHtml += '<div class="col-md-6 margin-top-6 font-size-14 color-0082ba padding-0">';
		        unitInfoPopupHtml += '<span class="fa fa-calendar padding-6"></span>';
		        unitInfoPopupHtml += '<span class="color-000 font-size-14">' + value.meetingDate + '</span>';
		      unitInfoPopupHtml += '</div>';
	      unitInfoPopupHtml += '</div>';
		    unitInfoPopupHtml += '<hr>';

		    unitInfoPopupHtml += '<div class="row">';
		    	unitInfoPopupHtml += '<div class="col-md-12 margin-top-12">';
	          unitInfoPopupHtml += '<span class="color-767676 padding-top-3 padding-left-10 font-size-14">Location: </span>';
	          unitInfoPopupHtml += '<span class="color-000 font-size-14">' + value.meetingLocation + '</span>';
	        unitInfoPopupHtml += '</div>';
		      unitInfoPopupHtml += '<div class="col-md-12 margin-top-12">';
		        unitInfoPopupHtml += '<span class="color-767676 padding-top-3 padding-left-10 font-size-14">Speaker: </span>';
	          unitInfoPopupHtml += '<span class="color-000 font-size-14">' + value.speakerName + '</span>';
	        unitInfoPopupHtml += '</div>';
	        unitInfoPopupHtml += '<div class="col-md-12 margin-top-12">';
	        	unitInfoPopupHtml += '<span class="color-767676 padding-top-3 padding-left-10 font-size-14">Rep: </span>';
	          unitInfoPopupHtml += '<span class="color-000 font-size-14">' + value.repName + '</span>';
	        unitInfoPopupHtml += '</div>';
	        unitInfoPopupHtml += '<div class="col-md-12 margin-top-12">';
	        	unitInfoPopupHtml += '<span class="color-767676 padding-top-3 padding-left-10 font-size-14">Venue: </span>';
	          unitInfoPopupHtml += '<span class="color-000 font-size-14">' + value.venuName + '</span>';
	        unitInfoPopupHtml += '</div>';
	        unitInfoPopupHtml += '<div class="col-md-12 margin-top-12">';
	       	  unitInfoPopupHtml += '<span class="color-767676 padding-top-3 padding-left-10 font-size-14">Speaker Evaluation: </span>';
	          unitInfoPopupHtml += '<span class="color-000 font-size-14">' + value.eventLink + '</span>';
	        unitInfoPopupHtml += '</div>';
	      unitInfoPopupHtml += '</div>';
		    unitInfoPopupHtml += '<hr>';

		    unitInfoPopupHtml += '<div class="col-md-12 text-center">';
		      unitInfoPopupHtml += '<div class="col-md-6 margin-top-6">';
		        unitInfoPopupHtml += '<span class="color-767676 padding-top-3 font-size-18">ATTENDEES</span>';
		      unitInfoPopupHtml += '</div>';
		      unitInfoPopupHtml += '<div class="col-md-6 margin-top-6">';
		        unitInfoPopupHtml += '<span class="color-767676 padding-top-3 font-size-18">RESPONSES</span>';
		      unitInfoPopupHtml += '</div>';
		      unitInfoPopupHtml += '<div class="col-md-6 margin-top-6">';
		        unitInfoPopupHtml += '<span class="color-0082ba font-size-24 font-bold">' + value.attendiesCount + '</span>';
		      unitInfoPopupHtml += '</div>';
		      unitInfoPopupHtml += '<div class="col-md-6 margin-top-6">';
		        unitInfoPopupHtml += '<span class="color-0082ba font-size-24 font-bold">' + value.evaluationCount + '</span>';
		      unitInfoPopupHtml += '</div>';
		    unitInfoPopupHtml += '</div><br/>';
		  unitInfoPopupHtml += '</div>';
		unitInfoPopupHtml += '</div>';
		bindInfoWindow(marker, map, infoBubbleSettings(), unitInfoPopupHtml);
	});

	var markerCluster = new MarkerClusterer(map, markers, mapClusterStyle());

	function bindInfoWindow(marker, map, infoBubble, html) {
	  google.maps.event.addListener(marker, 'click', function() {
	    infoBubble.setContent(html);
	    infoBubble.open(map, marker);
	  });
	  // google.maps.event.addListener(marker, 'mouseout', function() {
	  //   // infoBubble.close();
	  // });
	}
}

