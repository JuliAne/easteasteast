<?php

namespace Drupal\AaeData;

 /**
  * Kleine Helferklasse (!=model) für wiederkehrende Funktionen, Variablen
  * & Pfade. Gerne erweiterbar :)
  *
  * TODO: Konfigurations- und Pfaddaten via Backend verwaltbar machen
  *
  */

  spl_autoload_register(array((new aae_data_helper), 'aae_autoload'));

 Class aae_data_helper {

   // DB-Tables
   var $tbl_hat_sparte = "aae_data_akteur_hat_sparte";
   var $tbl_adresse = "aae_data_adresse";
   var $tbl_akteur = "aae_data_akteur";
   var $tbl_sparte = "aae_data_sparte";
   var $tbl_hat_user = "aae_data_akteur_hat_user";
   var $tbl_bezirke = "aae_data_bezirke";
   var $tbl_event = "aae_data_event";
   var $tbl_akteur_events = "aae_data_akteur_hat_event";
   var $tbl_event_sparte = "aae_data_event_hat_sparte";
   var $tbl_festival = "aae_data_festival";
   var $tbl_hat_festivals = "aae_data_akteur_hat_festival";

   // Image-paths
   var $bildpfad = "/var/www/virtual/grinch/leipziger-ecken.de/sites/default/files/pictures/aae/";
   var $localbildpfad = "/opt/lampp/htdocs/drupal/sites/default/files/pictures/aae/";
   var $testbildpfad = "/var/www/virtual/grinch/test.leipziger-ecken.de/sites/default/files/pictures/aae/";
   var $short_bildpfad = "sites/default/files/pictures/aae/";

   // Mapbox-API-Data
   var $mapboxAccessToken = "pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg";
   var $mapboxMap = "matzelot.ke3420oc";
   var $mapboxDefaultView = "51.336, 12.433";
   var $mapboxDefaultZoom = "13";

   // Mapbox-File's (unsauber, aber zweckdienlich)
   var $mapboxCss = 'https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css';
   var $mapboxJs = 'https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js';
   var $mapboxJsInline; # set down there

   var $uses;
   var $servercheck;
   var $modulePath;
   var $themePath;

   var $monat_short;
   var $monat_lang;
   var $dayNames;
   var $user_id;

   function __construct(){

    $this->dayNames = array(
     t('Montag'),
     t('Dienstag'),
     t('Mittwoch'),
     t('Donnerstag'),
     t('Freitag'),
     t('Samstag'),
     t('Sonntag')
    );

    $this->monat_lang = array(
    '01' => t('Januar'),
    '02' => t('Februar'),
    '03' => t('März'),
    '04' => t('April'),
    '05' => t('Mai'),
    '06' => t('Juni'),
    '07' => t('Juli'),
    '08' => t('August'),
    '09' => t('September'),
    '10' => t('Oktober'),
    '11' => t('November'),
    '12' => t('Dezember'),
    );

    foreach ($this->monat_lang as $key => $monat) {
     $this->monat_short[$key] = substr($monat, 0, 3);
    }

    $this->mapboxJsInline = "L.mapbox.accessToken = '".$this->mapboxAccessToken."';";

    global $user;
    $this->user_id = $user->uid;
    $this->modulePath = drupal_get_path('module', 'aae_data');
    # $this->themePath = drupal_get_path('theme', 'aae_data'/*$GLOBALS['theme']*/);
    $this->themePath = path_to_theme();
    // TODO: Return the universal path to aae_theme in order to enable
    // templates for other themes ($this->themePath = 'themes/aae_theme';)
    
   }

   public function aae_autoload($class){

    $class = explode('\\', $class)[2];
    include_once('models/'. $class .'.php');
    $class = __NAMESPACE__ . '\\' . $class;
   # $this->{$class} = new $class();

   }

   /**
    *  Einfache Funktion zum Filtern von POST- und GET-Daten ("escape-function")
    *  Da Drupal automatisch PDO verwendet, brauchen wir hier nicht allzu viel für DB-queries.
    *  Entfernt Whitespaces (oder andere Zeichen) am Anfang und Ende eines Strings und
    *  filtert HTML um XSS Attacken vorzubeugen.
    */
   public function clearContent($trimTag) {
    $clear = trim($trimTag);
    //return strip_tags($clear);
    return filter_xss($clear, $allowed_tags = array('a', 'em', 'strong', 'cite', 'blockquote', 'img', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'br', 'video', 'p'));
   }

   public function getJournalEntries($limit = 5) {

    $query = new \EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
     ->entityCondition('bundle', 'article')
     ->propertyCondition('status', 1)
     ->propertyOrderBy('created', 'DESC')
     ->range(0,$limit);

    $result = $query->execute();
    $nodes = array();

    if (isset($result['node'])) {
     $nids = array_keys($result['node']);
     $nodes = node_load_multiple($nids);
    }

    return $nodes;

   }
   
   /* TODO: Untested function */
   protected function check_image_compatibility($img){
    
    $image = new \Imagick($img['tmp_name']);
    $formats = array('jpeg','jpg','png','gif','bmp');
   
    if (!in_array(strtolower($image->getImageFormat()), $formats)) {
     return t('Ungültiges Dateiformat (erlaubt: Jpeg, png, gif, bmp)');
    } elseif ($image->getimagesize() > 4194304) {
     return t('Die max. Bildgröße beträgt lediglich 4MB');
    } else {
     return true;
    }
    
   }

   protected function upload_image($img,$servercheck = null) {

    $image = new \Imagick($img['tmp_name']);    
    
    $image->setImageBackgroundColor('white'); // Entfernt Transparenz bein png's
    $image->scaleImage(800, 400, true);
    $image = $image->flattenImages(); // Deprecated!
    $image->setImageCompressionQuality(90); // = Idealer Wert???
    $image->setImageFormat('jpg'); // see also: $image->getImageFormat();

    if ($_SERVER['SERVER_NAME'] == "localhost"){
      $image->writeImage($this->localbildpfad.substr(md5($img['name']),0,18).'.jpg');
    } elseif ($_SERVER['SERVER_NAME'] == "test.leipziger-ecken.de") {
      $image->writeImage($this->testbildpfad.substr(md5($img['name']),0,18).'.jpg');
    } else {
      $image->writeImage($this->bildpfad.substr(md5($img['name']),0,18).'.jpg');
    }

    return base_path() . $this->short_bildpfad.substr(md5($img['name']),0,18) . '.jpg';

   }

   /**
    * function render()
    * Returns a template file
    *
    * @param $tpl : Path within current theme
    * @param $setVars : Array that'll be extracted
    * @return rendered HTML
    */
  public function render($tpl, $setVars = NULL) {
   
    if ($setVars) {
     extract($setVars);
    }

    ob_start(); // Aktiviert "Render"-modus
    include_once path_to_theme().$tpl;
    return ob_get_clean(); // Übergabe des gerenderten Templates

  }

  protected function addMapContent($geoCord = '', $pointData = '', $markerData = '') {

    if (empty($geoCord)) $geoCord = $this->mapboxDefaultView;

    drupal_add_css($this->mapboxCss, array('type' => 'external'));
    drupal_add_js($this->mapboxJs);
    drupal_add_js($this->mapboxJsInline, 'inline', array('type' => 'inline', 'scope' => 'footer', 'weight' => -1));

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

     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css', array('type' => 'external', 'scope' => 'footer'));
     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css', array('type' => 'external', 'scope' => 'footer'));
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

    drupal_add_js($js, array('type' => 'inline', 'weight' => 999));

  }

  protected function getAllBezirke() {

    return db_select($this->tbl_bezirke, 'b')
     ->fields('b')
     ->execute();

  }

  protected function getDuplicates($ids, $num) {

   $result = array();
   $counts = array_count_values($ids);

   foreach ($counts as $id => $count){
     if ($count >= $num) $result[$id] = $id;
   }

   return (empty($result)) ? NULL : $result;

  } // end protected function getDuplicates

  /* Helper method for INSERT- and UPDATE-actions
     which forges a db_query from the given parameters
     TODO (if required): Allow multiple $conditions */
  protected function __db_action($tbl, $fields, $condition = NULL, $putTimestamp = false){

   if ($putTimestamp){
    $fields[(!empty($condition) ? 'modified' : 'created')] = date('Y-m-d H:i:s', time());
   }

   if ($tbl == $this->tbl_akteur && !$condition){ # || $this->tbl_events
    $fields['ersteller'] = $this->user_id;
   }
      
   if ($condition){
     db_update($tbl)->fields($fields)->condition($condition[0], $condition[1])->execute();
     return $condition[1];
   } else {
     return db_insert($tbl)->fields($fields)->execute();
   }

  } // end protected function __db_action

 }
?>
