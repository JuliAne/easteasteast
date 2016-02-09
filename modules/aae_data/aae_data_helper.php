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
   var $localbildpfad = "/opt/lampp/htdocs/drupal/sites/default/files/pictures/aae/";
   var $testbildpfad = "/var/www/virtual/grinch/test.leipziger-ecken.de/sites/default/files/pictures/aae/";
   var $short_bildpfad = "sites/default/files/pictures/aae/";

   // Mapbox-Data
   var $mapboxAccessToken = "pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg";
   var $mapboxMap = "matzelot.ke3420oc";
   var $mapboxDefaultView = "51.336, 12.433";
   var $mapboxDefaultZoom = "12";

   var $servercheck;

   var $monat_short = array(
     '01' => 'Jan',
     '02' => 'Feb',
     '03' => 'Mär',
     '04' => 'Apr',
     '05' => 'Mai',
     '06' => 'Jun',
     '07' => 'Jul',
     '08' => 'Sep',
     '09' => 'Aug',
     '10' => 'Okt',
     '11' => 'Nov',
     '12' => 'Dez',
   );

   var $monat_lang = array(
    '01' => 'Januar',
    '02' => 'Februar',
    '03' => 'März',
    '04' => 'April',
    '05' => 'Mai',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'August',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Dezember',
   );

   /**
    *  Einfache Funktion zum Filtern von POST- und GET-Daten ("escape-function")
    *  Da Drupal automatisch PDO verwendet, brauchen wir hier nicht allzu viel.
    */
   public function clearContent($trimTag) {
     $clear = trim($trimTag);
     return strip_tags($clear);
     /*$var=stripslashes($var);
       $var=htmlentities($var);
       $var=mysql_real_escape_string($var);*/
   }


   protected function upload_image($bild,$servercheck = null) {

    $image = new Imagick($bild['tmp_name']);
    $image->setImageBackgroundColor('white'); // Entfernt Transparenz bein png's
    $image->scaleImage(800, 400, true);
    $image = $image->flattenImages();
    $image->setImageCompressionQuality(90); // = Idealer Wert???
    $image->setImageFormat('jpg'); // see also: $image->getImageFormat();

    $servername_local = "localhost";
    $servername_test = "test.leipziger-ecken.de";

    if($_SERVER['SERVER_NAME'] == $servername_local ){
      $image->writeImage($this->localbildpfad.substr(md5($bild['name']),0,18).'.jpg');
    } elseif ($_SERVER['SERVER_NAME'] == $servername_test) {
      $image->writeImage($this->testbildpfad.substr(md5($bild['name']),0,18).'.jpg');
    }else{
      $image->writeImage($this->bildpfad.substr(md5($bild['name']),0,18).'.jpg');
    }

    return base_path() . $this->short_bildpfad.substr(md5($bild['name']),0,18) . '.jpg';

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
          icon: L.mapbox.marker.icon({"marker-symbol": "star", "marker-color": "#2199E8"}),
          title: title
      });
      marker.bindPopup(title);
      markers.addLayer(marker);
     }

     map.addLayer(markers);';

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
      "marker-color": "#2199E8",
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

  protected function getAllBezirke() {

    return db_select($this->tbl_bezirke, 'b')
     ->fields('b')
     ->execute();

  }

  protected function getDuplicates($ids, $num) {

   $result = array();
   $counts =  array_count_values($ids);

   foreach ($counts as $id => $count){
     if ($count >= $num) $result[$id] = $id;
   }

   return (empty($result)) ? NULL : $result;

  } // end protected function getDuplicates
 }
?>
