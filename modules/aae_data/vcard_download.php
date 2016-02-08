<?php
/**
 * Moeglichkeit, einen einzelnen Akteur als .vcf-Datei (VCard-Format)
 * zu exportieren.
 */

Class vcard_download extends aae_data_helper {

 public function run(){

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
}
