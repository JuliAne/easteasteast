<?php
/**
 * Zeigt das Profil eines Akteurs an.
 */

class aae_akteurprofil extends aae_data_helper {

 public function run(){

  $explodedpath = explode("/", current_path());
  $akteur_id = $this->clearContent($explodedpath[1]);

  global $user;
  $user_id = $user->uid;

  //Prüfen ob Schreibrecht vorliegt
  $resultUser = db_select($this->tbl_hat_user, 'u')
  ->fields('u')
  ->condition('hat_AID', $akteur_id, '=')
  ->condition('hat_UID', $user_id, '=')
  ->execute();

  // Anzeige Edit-Button?
  if ($resultUser->rowCount() == 1 || array_intersect(array('administrator'), $user->roles)) $hat_recht = 1;

  //Auswahl der Daten des Akteurs
  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a')
   ->condition('AID', $akteur_id, '=')
   ->execute()
   ->fetchAll();

  if (empty($resultAkteur)) {
  // Akteur nicht vorhanden, beame ihn zur Akteure-Seite

   if (session_status() == PHP_SESSION_NONE) session_start();
   drupal_set_message('Dieses Akteurprofil konnte nicht gefunden werden...');
   header("Location: ".base_path()."akteure");

 } else {

  foreach ($resultAkteur as $row) {

   $aResult['row1'] = $row;
   $resultAdresse = db_select($this->tbl_adresse, 'b')
    ->fields('b')
    ->condition('ADID', $row->adresse, '=');

   $aResult['adresse'] = $resultAdresse->execute()->fetchObject();

  }

  // Ziehe Informationen über Events vom Akteur

  $events = db_query('
   SELECT * FROM {aae_data_event} AS e JOIN {aae_data_akteur_hat_event} AS he
   WHERE he.EID = e.EID AND he.AID = :aid
   ORDER BY start_ts DESC',
   array(':aid' => $akteur_id));

  $resultEvents = $events->fetchAll();

  // Generiere Mapbox-taugliche Koordinaten, übergebe diese ans Frontend

  if (!empty($aResult['adresse']->gps)) {

    $koordinaten = $aResult['adresse']->gps;
    $this->addMapContent($koordinaten, array(
     'gps' => $koordinaten,
     'name' => $aResult['adresse']->name,
     'strasse' => $aResult['adresse']->strasse,
     'nr' => $aResult['adresse']->nr
    ));

  }

  $kategorien = db_select($this->tbl_hat_sparte, 'a')
  ->fields('a', array('hat_KID'))
  ->condition('hat_AID', $akteur_id, '=')
  ->execute()
  ->fetchAll();

  if (!empty($kategorien)) {

  foreach($kategorien as $kategorie) {

   $resultTags[] = db_select($this->tbl_sparte, 't')
   ->fields('t')
   ->condition('KID', $kategorie->hat_KID, '=')
   ->execute()
   ->fetchObject();

  }
 }

  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme() . '/templates/single_akteur.tpl.php';
  return ob_get_clean(); // Übergabe des gerenderten "single_akteur.tpl"

  }
 }

 /**
  * Moeglichkeit, einen einzelnen Akteur als .vcf-Datei (VCard-Format)
  * zu exportieren.
  */

  public function vcard_download(){

   global $user;

   $explodedpath = explode("/", current_path());
   $akteurId = $this->clearContent($explodedpath[1]);

   $resultAkteur = db_select($this->tbl_akteur, 'a')
    ->fields('a')
    ->condition('AID', $akteurId)
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
   $var .= "SOURCE:http://leipzger-ecken.de/download_vcard/".$resultAkteur->AID."\r\n";
   $var .= "URL:http://leipziger-ecken.de/akteurprofil/".$resultAkteur->AID."\r\n";

   if (!empty($resultAkteur->bild)){
    $var .= "PHOTO;VALUE=URL;TYPE=JPEG:http://leipziger-ecken.de".$resultAkteur->bild."\r\n";
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
   header('Connection', 'close');
   echo $var;

  }

} // end class aae_akteurprofil

?>
