<?php

 /**
  * Kleine Helferklasse (!=model) für wiederkehrende Funktionen, Variablen
  *  & Pfade. Gerne erweiterbar :)
  *
  */

 Class aae_data_helper {

   // DB-Tabellen
   var $tbl_hat_sparte = "aae_data_akteur_hat_sparte";
   var $tbl_adresse = "aae_data_adresse";
   var $tbl_akteur = "aae_data_akteur";
   var $tbl_sparte = "aae_data_sparte";
   var $tbl_hat_user = "aae_data_akteur_hat_user";
   var $tbl_bezirke = "aae_data_bezirke";
   var $tbl_event = "aae_data_event";
   var $tbl_akteur_events = "aae_data_akteur_hat_event";
   var $tbl_event_sparte = "aae_data_event_hat_sparte";

   // Speicherort fuer Bilder
   var $bildpfad = "/var/www/virtual/grinch/leipziger-ecken.de/sites/default/files/pictures/aae/";
   var $short_bildpfad = "sites/default/files/pictures/aae/";

   // Mapbox-Data
   var $mapboxAccessToken = "pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg";
   var $mapboxMap = "matzelot.ke3420oc";
   var $mapboxDefaultView = "51.336, 12.433";
   var $mapboxDefaultZoom = "13";

   /**
    *  Einfache Funktion zum Filtern von POST- und GET-Daten. Gerne erweiterbar.
    */
   public function clearContent($trimTag) {
     $clear = trim($trimTag);
     return strip_tags($clear);
     //return mysql_real_escape_string($clear);
   }

   protected function upload_image($bild) {

    $image = new Imagick($bild['tmp_name']);
    //$image->thumbnailImage(700, 400);
    $image->scaleImage(700, 300, true);
    $image->writeImage($this->bildpfad.$bild['name']);

    return base_path() . $this->short_bildpfad . $bild['name'];

   }

   /**
    * Dickes fettes TODO... (bisher ungenutzte Funktion)
    */

   protected function render($tpl) {

    ob_start(); // Aktiviert "Render"-modus
    include_once path_to_theme().$tpl;
    return ob_get_clean(); // Uebergabe des gerenderten Template's

  }

  protected function addMapContent($geoCord = '', $pointData = '', $markerData = '') {

    if (empty($geoCord)) $geoCord = $this->mapboxDefaultView;

    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
    drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
    drupal_add_js('L.mapbox.accessToken = "'.$this->mapboxAccessToken.'";', 'inline', array('type' => 'inline', 'scope' => 'footer'));

    $js = '$(window).ready(function(){var map = L.mapbox.map("map", "'.$this->mapboxMap.'").setView(['.$geoCord.'], '.$this->mapboxDefaultZoom.');';

    if (is_array($markerData) && !empty($markerData)) {

     $js .= 'var markers = new L.MarkerClusterGroup({ showCoverageOnHover : false });

     for (var i = 0; i < addressPoints.length; i++) {
      var a = addressPoints[i];
      var title = a[2];
      var marker = L.marker(new L.LatLng(a[0], a[1]), {
          icon: L.mapbox.marker.icon({"marker-symbol": "pitch", "marker-color": "0044FF"}),
          title: title
      });

      marker.bindPopup(title);
      markers.addLayer(marker);
     }

     map.addLayer(markers);';

     //drupal_add_js(base_path().drupal_get_path('module', 'aae_data').'/LOdata.js');
     if (!empty($markerData['file'])) drupal_add_js($markerData['file']);

     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css', array('type' => 'external'));
     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css', array('type' => 'external'));
     drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');

    }

    if (is_array($pointData) && !empty($pointData)) {

    $kHelper = explode(',', $pointData['gps'], 2);
    $koordinaten = $kHelper[1] . ',' . $kHelper[0];

    $js.='var myLayer = L.mapbox.featureLayer().addTo(map);

    var geojson = [
    {
    "type": "Feature",
    "geometry": {
      "type": "Point",
      "coordinates": ['.$koordinaten.']
    },
    "properties": {
      "title": "'. $pointData['name'] .'",
      "description": "'.$pointData['strasse'].' '. $pointData['nr'] .'",
      "marker-color": "#1087bf",
      "marker-size": "large",
      "marker-symbol": "star"
     }
    }
   ];
   myLayer.setGeoJSON(geojson);';

    }

    $js .=  '});';

    drupal_add_js($js, array( 'type' => 'inline', 'scope' => 'footer'));

  }

  protected function getAllTags() {

    return db_select($this->tbl_sparte, 't')
     ->fields('t', array(
     'KID',
     'kategorie',
     ))
    ->execute();

  }

 }

?>
