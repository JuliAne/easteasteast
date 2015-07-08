$(document).ready(function(){

 /**
  * ACTIVATE SOME PLUGINS ONLY IF A MOBILE DEVICE
  * IS USED (640px)
  */

 if($(window).width() < 640) {
  $('#menu li:has(ul)' ).doubleTapToGo();
 } else {
  $('#fullpage').fullpage({
	 anchors: ['Intro', 'Projekte', 'Karte', 'UeberUns', 'Footer'],
	 sectionsColor: ['#FFF', '#F7F7F7', '#F7F7F7', '#FFF'],
	 scrollBar: true,
   navigation: true,
	 navigationPosition: 'right',
	 navigationTooltips: ['Home', 'Projekte & Veranstaltungen', 'Interaktive Karte', '&Uuml;ber uns', 'Footer'],

   afterLoad: function(anchor, index){
      if (index == 1) {
       $('#fp-nav').fadeOut('fast');
      } else {
       $('#fp-nav').fadeIn('slow');
      }
    }

  });
 }

  L.mapbox.accessToken = 'pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg';
 var map = L.mapbox.map('map', 'matzelot.ke3420oc').setView([51.338, 12.458], 13);
 var markers = new L.MarkerClusterGroup({ showCoverageOnHover : false });

 for (var i = 0; i < addressPoints.length; i++) {
  var a = addressPoints[i];
  var title = a[2];
  var marker = L.marker(new L.LatLng(a[0], a[1]), {
      icon: L.mapbox.marker.icon({'marker-symbol': 'pitch', 'marker-color': '0044FF'}),
      title: title
  });

  marker.bindPopup(title);
  markers.addLayer(marker);
 }

 map.addLayer(markers);

 map.on('zoomend', function() {
  if (map.getZoom() >= 13) {
   $('#intro').fadeOut('slow');
  } else {
   $('#intro').fadeIn('slow');
  }
 });

});
