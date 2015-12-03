$(document).ready(function(){

 /**
  * ACTIVATE SOME PLUGINS ONLY IF A MOBILE DEVICE
  * IS USED (640px)
  */

 if($(window).width() < 640) {
  $('#menu li:has(ul)' ).doubleTapToGo();
 } else {
  $('#fullpage').fullpage({
	 anchors: ['home', 'blog', 'events', 'akteure', 'ueberuns'],
	 sectionsColor: ['#FFF', '#F7F7F7', '#F7F7F7', '#F7F7F7', '#FFF'],
	 scrollBar: true,
   navigation: true,
	 navigationPosition: 'right',
	 navigationTooltips: ['Start', 'Journal', 'Veranstaltungen', 'Akteure', 'Footer'],
   fixedElements: '#mainnav',

   afterLoad: function(anchor, index){
      if (index == 1) {
       $('#fp-nav').fadeOut('fast');
      } else {
       $('#fp-nav').fadeIn('slow');
      }
    }

  });
 }

//*********************MAPBOX ADVICES**************************************************
//Bereits in aae_data_helper.php implementiert --> helper Funktion addMapContent
//Bei der Nutzung hier müssen folgende Dateien erneut im Template geladen werden:
//  drupal_add_js('https://api.mapbox.com/mapbox.js/v2.2.3/mapbox.js');
//  drupal_add_js('http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js');
//  drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');
//  drupal_add_css('https://api.mapbox.com/mapbox.js/v2.2.3/mapbox.css');
// --> evtl. global im Header laden?
// Zudem ist Variable addressPoints hier nicht verfügbar, müste als globale Variable angelegt werden, wenn hier Nutzung erfolgen soll

//********************MAPBOX CODE*****************************************************
 // L.mapbox.accessToken = 'pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg';
 // // Bug: Map changes view to the left when being used in a slider :(
 // var map = L.mapbox.map('map', 'matzelot.ke3420oc').setView([51.34, 12.735], 13);
 // var markers = new L.MarkerClusterGroup({ showCoverageOnHover : false });

 // for (var i = 0; i < addressPoints.length; i++) {
 //  var a = addressPoints[i];
 //  var title = a[2];
 //  var marker = L.marker(new L.LatLng(a[0], a[1]), {
 //      icon: L.mapbox.marker.icon({'marker-symbol': 'pitch', 'marker-color': '0044FF'}),
 //      title: title
 //  });

 //  marker.bindPopup(title);
 //  markers.addLayer(marker);
 // }

 // map.addLayer(markers);

 /*map.on('zoomend', function() {
  if (map.getZoom() >= 13) {
   $('#intro').fadeOut('slow');
  } else {
   $('#intro').fadeIn('slow');
  }
}); */

});
