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
   var $bildpfad = "/var/www/virtual/grinch/leipziger-ecken.de/sites/default/files/styles/large/public/field/image/";
   var $short_bildpfad = "sites/default/files/styles/large/public/field/image/";

   /**
    *  Einfache Funktion zum Filtern von POST-Daten. Gerne erweiterbar.
    */
   protected function clearContent($trimTag) {
     $clear = trim($trimTag);
     return strip_tags($clear);
   }

   protected function upload_image($bildname, $oldpic = '') {

     if (!empty($bildname)) {
      if (!move_uploaded_file($_FILES['bild']['tmp_name'], $this->bildpfad . md5($bildname))) {
         echo 'Error: Konnte Bild nicht hochladen. Bitte informieren Sie den Administrator. Bildname: <br />' . $bildname;
         exit();
       }
       return base_path() . $this->short_bildpfad . md5($bildname);
     }
   }

   /**
    * Dickes fettes TODO...
    */

   protected function render($tpl) {

    ob_start(); // Aktiviert "Render"-modus
    include_once path_to_theme().$tpl;
    return ob_get_clean(); // Uebergabe des gerenderten Template's

  }
 }

?>
