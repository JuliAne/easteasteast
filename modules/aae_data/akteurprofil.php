<?php
/**
 * @file akteurprofil.php
 * Shows the profile of an akteur
 */

namespace Drupal\AaeData;

class akteurprofil extends akteure {
  
 public function __construct(){
  parent::__construct();

  #$this->akteur = new akteure();

  $explodedpath = explode("/", current_path());
  $this->akteur_id = $this->clearContent($explodedpath[1]);

 }

 public function run(){

  $hat_recht = $this->isAuthorized($this->akteur_id);

  $this->__setSingleAkteurVars(reset($this->getAkteure(array('AID' => $this->akteur_id), 'complete')));

  if (empty($this->name)) {

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
    $rssFeedUrl = $feed->url;
   }
  }

  // Ziehe Informationen über Events + Festivals vom Akteur
  $events = db_query('
   SELECT * FROM {aae_data_event} AS e JOIN {aae_data_akteur_hat_event} AS he
   WHERE he.EID = e.EID AND he.AID = :aid
   ORDER BY start_ts DESC',
   array(':aid' => $this->akteur_id));

  $resultEvents = $events->fetchAll();

  $festivals = db_query('
   SELECT * FROM {aae_data_festival} AS f JOIN {aae_data_akteur_hat_festival} AS hf
   WHERE hf.hat_FID = f.FID AND hf.hat_AID = :aid
   ORDER BY name DESC',
   array(':aid' => $this->akteur_id));

  $resultFestivals = $festivals->fetchAll();

  $showMap = false;
  
  // Generiere Mapbox-taugliche Koordinaten, übergebe diese ans Frontend
  if (!empty($this->adresse->gps_lat)) {

    $showMap = true;
    $koordinaten = $this->adresse->gps_lat.','.$this->adresse->gps_long;
    $this->addMapContent($koordinaten, array(
     'gps' => $koordinaten,
     'name' => $this->adresse->name,
     'strasse' => $this->adresse->strasse,
     'nr' => $this->adresse->nr
    ));

  }

  $tags = db_select($this->tbl_hat_sparte, 'a')
  ->fields('a', array('hat_KID'))
  ->condition('hat_AID', $this->akteur_id)
  ->execute()
  ->fetchAll();

  if (!empty($tags)) {

   foreach ($tags as $tag) {

    $resultTags[] = db_select($this->tbl_sparte, 't')
    ->fields('t')
    ->condition('KID', $tag->hat_KID)
    ->execute()
    ->fetchObject();

   }
  }

  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme() . '/templates/akteurprofil.tpl.php';
  return ob_get_clean(); // Übergabe des gerenderten "akteurprofil.tpl"

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

 return '<div class="callout row">
 <h3>Möchten Sie den Akteur wirklich löschen?</h3><br />
 <form action="#" method="POST" enctype="multipart/form-data">
   <a class="secondary button" href="javascript:history.go(-1)">Abbrechen</a>
   <input type="submit" class="button" id="akteurSubmit" name="submit" value="Löschen">
 </form>
 </div>';

 }
} // end function removeAkteur()

 /**
  * Moeglichkeit, einen einzelnen Akteur als .vcf-Datei (VCard-Format)
  * zu exportieren.
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
   exit();
  }

} // end class aae_akteurprofil

?>
