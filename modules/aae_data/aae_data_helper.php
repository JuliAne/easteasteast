<?php

 /**
  * Kleine Helferklasse (!=model) für wiederkehrende Funktionen, Variablen
  *  & Pfade. Gerne erweiterbar :)
  *
  * @function ...
  */

 Class aae_data_helper {

   //DB-Tabellen
   var $tbl_hat_sparte = "aae_data_akteur_hat_sparte";
   var $tbl_adresse = "aae_data_adresse";
   var $tbl_akteur = "aae_data_akteur";
   var $tbl_sparte = "aae_data_sparte";
   var $tbl_hat_user = "aae_data_akteur_hat_user";
   var $tbl_bezirke = "aae_data_bezirke";
   var $tbl_event = "aae_data_event";
   var $tbl_akteur_events = "aae_data_akteur_hat_event";
   var $tbl_event_sparte = "aae_data_event_hat_sparte";

   //Speicherort fuer Bilder
   var $bildpfad = "/var/www/virtual/grinch/leipziger-ecken.de/sites/default/files/pictures/aae/";
   var $short_bildpfad = "sites/default/files/pictures/aae/";

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
$image->thumbnailImage(400, 400);
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

  protected function addMapContent($geocord) {

    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
    drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
    drupal_add_js('L.mapbox.accessToken = "pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg";', 'inline');

    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css', array('type' => 'external'));
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css', array('type' => 'external'));
    drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');

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
