(function ($) {
  Drupal.behaviors.zencharts = {
    attach: function (context, settings) {

    var center = new google.maps.LatLng(57.508742, -96.120850);
      var options = {
        'zoom': 3,
        'center': center,
        'mapTypeId': google.maps.MapTypeId.ROADMAP
		
      };

      var map = new google.maps.Map(document.getElementById("zencharts-google-maps-api-canvas"), options);

      var markers = [];
      for (var i = 0; i < 10; i++) {
        var lat = (Math.random() * 10 + 50) ;
        var lng = (Math.random() * 50 - 120);
        var latLng = new google.maps.LatLng(lat, lng);
        var marker = new google.maps.Marker({position: latLng});
        markers.push(marker);
      }

      /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
      // push('London, Ontario');
      for (var i = 0; i < 5; i++) {
        var latLng2 = new google.maps.LatLng((42.9869502 + (i * 0.00001)) , (-81.243177 + (i * 0.00001)) );
        var marker2 = new google.maps.Marker({position: latLng2});
        markers.push(marker2);
      }
      /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
      // push('Ottawa, Ontario');
      for (var i = 0; i < 6; i++) {
        var latLng2 = new google.maps.LatLng((45.421530 + (i * 0.00001)) , (-75.697193 + (i * 0.00001)) );
        var marker2 = new google.maps.Marker({position: latLng2});
        markers.push(marker2);
      }
      /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
      // push('Montreal, Quebec');
      for (var i = 0; i < 8; i++) {
        var latLng2 = new google.maps.LatLng((45.50168945 + (i * 0.00001)) , (-73.567256 + (i * 0.00001)) );
        var marker2 = new google.maps.Marker({position: latLng2});
        markers.push(marker2);
      }
      /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
      // push('Saint-Polycarpe, Quebec');
      for (var i = 0; i < 2; i++) {
        var latLng2 = new google.maps.LatLng((45.300571 + (i * 0.00001)) , (-74.30 + (i * 0.00001)) );
        var marker2 = new google.maps.Marker({position: latLng2});
        markers.push(marker2);
      }
      /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
      // push('Saint-Eustache, Quebec');
      for (var i = 0; i < 2; i++) {
        var latLng2 = new google.maps.LatLng((45.555904 + (i * 0.00001)) , (-73.906445 + (i * 0.00001)) );
        var marker2 = new google.maps.Marker({position: latLng2});
        markers.push(marker2);
      }
      /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
      // push('Kirkland, QC);
      for (var i = 0; i < 3; i++) {
        var latLng2 = new google.maps.LatLng((45.453228 + (i * 0.00001)) , (-73.865118 + (i * 0.00001)) );
        var marker2 = new google.maps.Marker({position: latLng2});
        markers.push(marker2);
      }
      /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
      // push('East Hawkesbury, ON);
      for (var i = 0; i < 1; i++) {
        var latLng2 = new google.maps.LatLng((45.514127 + (i * 0.00001)) , (-74.455976 + (i * 0.00001)) );
        var marker2 = new google.maps.Marker({position: latLng2});
        markers.push(marker2);
      }

      /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
      var markerCluster = new MarkerClusterer(map, markers);
    } // --- end for attach: function()
  };
})(jQuery);