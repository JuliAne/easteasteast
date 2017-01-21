<?php
/**
 * @file pages/akteurprofil.php
 * Presents the profile of an akteur
 *
 * TODO: The output for events in .tpl may be outsourced
 *       into a view-component
 */

namespace Drupal\AaeData;

class akteurprofil extends akteure {
  
 public function __construct(){

  parent::__construct();

  $explodedpath = explode("/", current_path());
  $this->akteur_id = $this->clearContent($explodedpath[1]);

 }

 public function run(){

  $this->hasPermission = $this->isAuthorized($this->akteur_id);

  $this->__setSingleAkteurVars(reset($this->getAkteure(array('AID' => $this->akteur_id), 'complete')));
  
  if (empty($this->name)) {

   // Well, not very beautiful, cuz if __setSingleAk... throws an error, we are done...

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message(t('Dieses Akteurprofil konnte nicht gefunden werden...'));
   header('Location: '. $base_url .'/akteure');

  } else {

   // Watch out for possible RSS-Feeds...
  if (module_exists('aggregator')) {

   $feed = db_query('SELECT fid, title, block, url FROM {aggregator_feed} WHERE title = :title', array(':title' => 'aae-feed-'.$this->akteur_id))->fetchObject();

   if ($feed) {

    $result = db_query('SELECT * FROM {aggregator_item} WHERE fid = :fid ORDER BY timestamp DESC, iid DESC LIMIT 5', array(':fid' => $feed->fid));
    $this->rssFeed = $result->fetchAll();
    $this->rssFeedUrl = $feed->url;

   }
  }

  // Festivals of Akteur
  $festivals = db_query('
   SELECT * FROM {aae_data_festival} AS f JOIN {aae_data_akteur_hat_festival} AS hf
   WHERE hf.hat_FID = f.FID AND hf.hat_AID = :aid
   ORDER BY name DESC',
   array(':aid' => $this->akteur_id));

  $this->resultFestivals = $festivals->fetchAll();

  $this->showMap = false;
  
  // Generiere Mapbox-taugliche Koordinaten, übergebe diese ans Frontend
  if (!empty($this->adresse->gps_lat)) {

    $this->showMap = true;
    $koordinaten = $this->adresse->gps_lat.','.$this->adresse->gps_long;
    $this->addMapContent($koordinaten, array(
     'gps' => $koordinaten,
     'name' => $this->adresse->name,
     'strasse' => $this->adresse->strasse,
     'nr' => $this->adresse->nr
    ));

  }
  
  return $this->render('/templates/akteurprofil.tpl.php');

 }
} // end function run()

/**
 *  @function removeAkteur()
 *  Removes an Akteur from DB
 *  TODO: t()!!! 
 */

public function removeAkteur(){

 if (!user_is_logged_in() || !$this->isAuthorized($this->akteur_id)) {
  drupal_access_denied();
  drupal_exit();
 }

 if (isset($_POST['submit'])) {

  $this->__removeAkteur($this->akteur_id);

  if (session_status() == PHP_SESSION_NONE) session_start();
  drupal_set_message(t('Der Akteur wurde gelöscht.'));
  header('Location: '. $base_url .'/akteure');

 } else {

 $pathThisFile = $_SERVER['REQUEST_URI'];

 return '<div class="callout row" style="padding:1rem !important;"><div class="large-12 columns">
 <h3>'. t('Möchten Sie den Akteur wirklich löschen?') .'</h3><br />
 <form action="#" method="POST" enctype="multipart/form-data">
   <a class="secondary button" href="javascript:history.go(-1)">'. t('Abbrechen') .'</a>
   <input type="submit" class="button" id="akteurSubmit" name="submit" value="'. t('Löschen') .'">
 </form>
 </div></div>';

 }
} // end function removeAkteur()

 /**
  * Möglichkeit, einen einzelnen Akteur als .vcf-Datei (VCard-Format)
  * zu exportieren.
  * TODO: Behebe invalidität (ein Zeilenumbruch zu viel)
  */

  public function vcard_download(){

   global $user;

   $resultAkteur = db_select($this->tbl_akteur, 'a')
    ->fields('a')
    ->condition('AID', $this->akteur_id)
    ->execute()
    ->fetchObject();

   $resultAdresse = db_select($this->tbl_adresse, 'ad')
    ->fields('ad')
    ->condition('ADID', $resultAkteur->adresse)
    ->execute()
    ->fetchObject();

   //Generierung der .vcf-Datei
   $var .= "BEGIN:VCARD\r";
   $var .= "VERSION:3.0\r\n";
   $var .= "REV:".date('Y-m-d H:i:s')."\r\n";
   $var .= "TITLE:".$resultAkteur->name."\r\n";
   $var .= "N:".str_replace(" ",";",$resultAkteur->name)."\r\n";
   $var .= "FN:".$resultAkteur->ansprechpartner."\r\n";
   $var .= "TZ:".date('O')."\r\n";
   $var .= "EMAIL;TYPE=PREF,INTERNET:".$resultAkteur->email."\r\n";
   $var .= "SOURCE:https://leipzger-ecken.de/download_vcard/".$resultAkteur->AID."\r\n";
   $var .= "URL:https://leipziger-ecken.de/akteurprofil/".$resultAkteur->AID."\r\n";

   if (!empty($resultAkteur->bild)){
    $var .= "PHOTO;VALUE=URL;TYPE=JPEG:https://leipziger-ecken.de".$resultAkteur->bild."\r\n";
   }

   if (!empty($resultAdresse->strasse) && !empty($resultAdresse->nr) && !empty($resultAdresse->plz)){
    $var .= "ADR;TYPE=WORK:;;".$resultAdresse->strasse." ".$resultAdresse->nr.";".$resultAdresse->plz.";Leipzig\r\n";
    $var .= "LABEL;TYPE=WORK:".$resultAdresse->strasse." ".$resultAdresse->nr.", ".$resultAdresse->plz." Leipzig\r\n";
   }

   if (!empty($resultAkteur->telefon)){
    $var .= "TEL;TYPE=WORK,VOICE:".$resultAkteur->telefon."\r\n";
   }

   if (!empty($resultAkteur->beschreibung)) {
    $numwords = 30;
    preg_match("/(\S+\s*){0,$numwords}/", $resultAkteur->beschreibung, $regs);
    $var .= "NOTE:".trim($regs[0])."...\r\n";
   }

   $var .= "END:VCARD\r";

   $fileName = htmlspecialchars($resultAkteur->name);
   $fileName = str_replace(" ", "_", $fileName);
   $fileName = str_replace(".", "_", $fileName);

   header('Content-Length: ' . strlen($var));
   header("Content-type:text/x-vCard; charset=utf-8");
   header("Content-Disposition: attachment; filename=vcard_".$fileName.".vcf");
   //header('Connection', 'close');
   echo $var;
   drupal_exit();
  }

} // end class aae_akteurprofil

?>
