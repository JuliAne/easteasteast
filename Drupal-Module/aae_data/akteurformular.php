<?php
/**
 * akteurformular.php stellt ein Formular dar,
 * in welches alle Informationen über einen Akteur
 * eingetragen werden können.
 * Einziges Pflichtfeld ist bisher der Name und die Emailadresse.
 * Anschließend werden die Daten in die DB-Tabellen eingetragen.
 *
 * Ruth, 2015-07-04
 * Felix, 2015-09-04
 */

Class akteurformular {

//DB-Tabellen
var $tbl_hat_sparte = "aae_data_hat_sparte";
var $tbl_adresse = "aae_data_adresse";
var $tbl_akteur = "aae_data_akteur";
var $tbl_sparte = "aae_data_kategorie";
var $tbl_hat_user = "aae_data_hat_user";
var $tbl_bezirke = "aae_data_bezirke";

//$tbl_akteur
var $name = "";
var $adresse = ""; //Address-ID: Addressinformationen muessen aus Addressdbtabelle geholt werden
var $email = "";
var $telefon = "";
var $url = "";
var $ansprechpartner = "";
var $funktion = "";
var $bild = "";
var $beschreibung = "";
var $oeffnungszeiten = "";

//$tbl_adresse
var $strasse = "";
var $nr = "";
var $adresszusatz = "";
var $plz = "";
var $ort = "";
var $gps = "";

//Tags:
var $sparten = "";

//Speicherort fuer Bilder
var $bildpfad = "/home/swp15-aae/drupal/sites/default/files/styles/large/public/field/image/";
var $short_bildpfad = "sites/default/files/styles/large/public/field/image/";

var $akteur_id = "";
var $user_id = "";
var $fehler = array();
var $freigabe = true; // Variable zur Freigabe: muss true sein

//Variablen, welche Texte in den Formularfeldern halten ("Placeholder")
//$tbl_akteur
var $ph_name = "Name des Vereins/ der Organisation";
var $ph_email = "E-mail Addresse";
var $ph_telefon = "Telefonnummer";
var $ph_url = "Website";
var $ph_ansprechpartner = "Kontaktperson";
var $ph_funktion = "Funktion der Kontaktperson";
var $ph_bild = "Dateiname mit Endung";
var $ph_beschreibung = "Beschreibung";
var $ph_oeffnungszeiten = "Öffnungszeiten";

//$tbl_adresse
var $ph_strasse = "Strasse";
var $ph_nr = "Hausnummer";
var $ph_adresszusatz = "Adresszusatz";
var $ph_plz = "PLZ";
var $ph_ort = "Bezirk";
var $ph_gps = "GPS-Addresskoordinaten";

//$tbl_hat_Sparte
var $ph_sparten = "Tags kommasepariert eingeben!";
var $explodedsparten = "";
var $countsparten = "";
var $sparte_id = "";
var $resultbezirke = "";
var $target = "";

//-----------------------------------

function __construct($action) {

 //Sicherheitsschutz
 if(!user_is_logged_in()) drupal_access_denied();

 global $user;
 $this->user_id = $user->uid;

 // Sollen die Werte im Anschluss gespeichert oder geupdatet werden?
 if($action == 'update') $this->target = 'update';

} // END Constructor


public function run() {
/** Funktion, welche reihenweise POST-Werte auswertet, abspeichert bzw. ausgibt.
*   @returns $profileHTML;
*/

$path = current_path();
$explodedpath = explode("/", $path);
$this->akteur_id = $explodedpath[1];

$output = '';

if (isset($_POST['submit'])) {

   if ($this->akteurCheckPost()) {
     if ($this->target == 'update') $this->akteurUpdaten();
     else $this->akteurSpeichern();

     $output = $this->akteurDisplay();
   } else {
     $output = $this->akteurDisplay();
  }
} else {
  // Was passiert, wenn Seite zum ersten mal gezeigt wird?
  // Lade Feld-Werte via ID (akteurGetFields) und gebe diese aus

  if ($this->target == 'update') $this->akteurGetFields();
  $output = $this->eventDisplay();
}

return $output;

}


private function clearContent($trimTag) {
 /* Einfache Funktion zum Filtern von POST-Daten. Gerne erweiterbar. */
 $clear = trim($trimTag);
 return strip_tags($clear);
}

private function akteurCheckPost() {

 // Wird ausgeführt, wenn auf "Speichern" gedrückt wird
 // @returns $this->freigabe;

 //Wertezuweisung
 $this->name = clearContent($_POST['name']);
 $this->email = clearContent($_POST['email']);
 $this->telefon = clearContent($_POST['telefon']);
 $this->url = clearContent($_POST['url']);
 $this->ansprechpartner = clearContent($_POST['ansprechpartner']);
 $this->funktion = clearContent($_POST['funktion']);
 if(isset($_POST['bild'])) $this->bild = $_POST['bild'];
 $this->beschreibung = clearContent($_POST['beschreibung']);
 $this->oeffnungszeiten = clearContent($_POST['oeffnungszeiten']);

 $this->strasse = clearContent($_POST['strasse']);
 $this->nr = clearContent($_POST['nr']);
 $this->adresszusatz = clearContent($_POST['adresszusatz']);
 $this->plz = clearContent($_POST['plz']);
 $this->ort = clearContent($_POST['ort']);
 $this->gps = clearContent($_POST['gps']);
 $this->sparten = clearContent($_POST['sparten']);

 $this->explodedsparten = "";
 if ($this->sparten != "") $this->explodedsparten = explode(",", $this->sparten);

//-------------------------------------
  //Check-Klauseln

  //Check, ob ein Name eingegeben wurde:
  if (strlen($this->name) == 0) {
   $this->fehler['name'] = "Bitte einen Organisationsnamen eingeben!";
   $this->freigabe = false;
  }
  //Check, ob Email angegeben wurde
  if (strlen($this->email) == 0) {
   $this->fehler['email'] = "Bitte eine Emailadresse eingeben!";
	 $this->freigabe = false;
  }

  //Tags:
  if ($this->sparten != "") {
   $this->countsparten = count($this->explodedsparten);
	 $i = 0;

	 while($i < $this->countsparten){
	  $this->explodedsparten[$i] = clearContent($explodedsparten[$i]);
	  $i++;
	 }
  }

  //Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.
  if (strlen($this->name) > 100) {
	 $this->fehler['name'] = "Bitte geben Sie einen kürzeren Namen an oder verwenden Sie ein Kürzel.";
	 $this->freigabe = false;
  }

  if (strlen($email) > 100) {
	 $this->fehler['email'] = "Bitte geben Sie eine kürzere Emailadresse an.";
	 $this->freigabe = false;
  }

  if (strlen($telefon) > 100) {
 	 $this->fehler['telefon'] = "Bitte geben Sie eine kürzere Telefonnummer an.";
	 $this->freigabe = false;
  }

  if (strlen($url) > 100) {
	 $this->fehler['url'] = "Bitte geben Sie eine kürzere URL an.";
	 $this->freigabe = false;
  }

  if (strlen($ansprechpartner) > 100){
	 $this->fehler['ansprechpartner'] = "Bitte geben Sie einen kürzeren Ansprechpartner an.";
	 $this->freigabe = false;
  }

  if (strlen($funktion) > 100) {
	 $this->fehler['funktion'] = "Bitte geben Sie eine kürzere Funktion an.";
   $this->freigabe = false;
  }

  if (strlen($beschreibung) > 500) {
	 $this->fehler['beschreibung'] = "Bitte geben Sie eine kürzere Beschreibung an.";
	 $this->freigabe = false;
  }

  if (strlen($oeffnungszeiten) > 200) {
	 $this->fehler['oeffnungszeiten'] = "Bitte geben Sie kürzere Oeffnungszeiten an.";
	 $this->freigabe = false;
  }

  if (strlen($strasse) > 100) {
 	 $this->fehler['strasse'] = "Bitte geben Sie einen kürzeren Strassennamen an.";
	 $this->freigabe = false;
  }

  if (strlen($nr) > 100) {
	 $this->fehler['nr'] = "Bitte geben Sie eine kürzere Nummer an.";
	 $this->freigabe = false;
  }

  if (strlen($adresszusatz) > 100) {
	 $this->fehler['adresszusatz'] = "Bitte geben Sie einen kürzeren Adresszusatz an.";
	 $this->freigabe = false;
  }

  if (strlen($plz) > 100) {
	 $this->fehler['plz '] = "Bitte geben Sie eine kürzere PLZ an.";
	 $this->freigabe = false;
  }

  if (strlen($gps) > 100) {
	 $this->fehler['gps'] = "Bitte geben Sie kürzere GPS-Daten an.";
	 $this->freigabe = false;
  }

  return $this->freigabe;
} // END akteurCheckPost

//---------------------------------

private function akteurSpeichern() {

 require_once $modulePath . 'database/db_connect.php';
 $db = new DB_CONNECT();

	//Abfrage, ob Adresse bereits in Adresstabelle
	$resultadresse = db_select($this->tbl_adresse, 'a')
	  ->fields('a', array( 'ADID', 'gps' ))
	  ->condition('strasse', $this->strasse, '=')
	  ->condition('nr', $this->nr, '=')
	  ->condition('adresszusatz', $this->adresszusatz, '=')
	  ->condition('plz', $this->plz, '=')
	  ->condition('bezirk', $this->ort, '=')
	  ->execute();

	//wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen

	if ($resultadresse->rowCount() == 0) {

   //Adresse nicht vorhanden
	 $this->adresse = db_insert($this->tbl_adresse)
	  ->fields(array(
	  'strasse' => $this->strasse,
	  'nr' => $this->nr,
	  'adresszusatz' => $this->adresszusatz,
	  'plz' => $this->plz,
	  'bezirk' => $this->ort,
	  'gps' => $this->gps,
	 ))
	 ->execute();
	} else {

   //Adresse bereits vorhanden
	 foreach ($resultadresse as $row) {
	    //Abfrage, ob GPS-Angaben gemacht wurden
	    if (strlen($this->gps) != 0 && strlen($row->gps) == 0 ) {
        //ja UND es sind bisher keine GPS-Daten zu der Adresse in der DB
	      //Update der Adresse
	      $adresse_updated = db_update($this->tbl_adresse)
	 	    ->fields(array( 'gps' => $this->gps ))
	      ->condition('ADID', $row->ADID, '=')
	      ->execute();
	    }

	    $this->adresse = $row->ADID; //Adress-ID merken
	  }
	}

  //Wenn Bilddatei ausgewählt wurde...
  if ($_FILES) {

   $bildname = $_FILES['bild']['name'];

   if($bildname != "") {
    if (!move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname)) {
      echo 'Error: Konnte Bild nicht hochladen. Bitte informieren Sie den Administrator. Bildname: <br />'.$bildname;
      exit();
    }
    $this->bild = base_path().$short_bildpfad.$bildname;
   }

  }

  //tbl_akteur INSERT!!!
	$this->akteur_id = db_insert($this->tbl_akteur)
   	->fields(array(
		'name' => $this->name,
		'adresse' => $this->adresse,
		'email' => $this->email,
		'telefon' => $this->telefon,
		'url' => $this->url,
		'ansprechpartner' => $this->ansprechpartner,
		'funktion' => $this->funktion,
		'bild' => $this->bild,
		'beschreibung' => $beschreibung,
		'oeffnungszeiten' => $oeffnungszeiten,
		'ersteller' => $this->user_uid,
	  ))
	  ->execute();

	//tbl_hat_user insert
	db_insert($this->tbl_hat_user)
	  ->fields(array(
	    'hat_UID' => $this->user_id,
	    'hat_AID' => $this->akteur_id,
	  ))
	  ->execute();

	//falls Tags angegeben wurden
	if ($this->sparten != "") {

	 $this->sparte_id = "";
   $this->countsparten = count($explodedsparten);
	 $i = 0;

   while($i < $this->countsparten) {
		//1. Prüfen, ob Tag bereits in Tabelle $tbl_sparte
		$resultsparte = db_select($this->tbl_sparte, 's')
		  ->fields('s', array( 'KID' ))
		  ->condition('kategorie', $this->explodedsparten[$i], '=')
		  ->execute();

		if ($resultsparte->rowCount() == 0) {
      //nein: Tag in $tbl_sparte einfügen
		  $this->sparte_id = db_insert($tbl_sparte)
		   ->fields(array( 'kategorie' => $this->explodedsparten[$i] ))
			 ->execute();
		} else {
      //ja: KID des Tags holen
		  foreach ($resultsparte as $row) {
			 $this->sparte_id = $row->KID;
		  }
		}

		//2. Akteur+Tag in Tabelle $tbl_hat_sparte einfügen
		$insertakteursparte = db_insert($this->tbl_hat_sparte)
		  ->fields(array(
		    'hat_AID' => $this->akteur_id,
		    'hat_KID' => $this->sparte_id,
		  ))
		  ->execute();
	    $i++1;
	  }
	}

	header("Location: Akteurprofil/".$this->akteur_id);
  // Beamen wir dich mal auf die neue Seite...

} // END function akteurSpeichern()

private function akteurDisplay() {

 $this->resultbezirke = db_select($this->tbl_bezirke, 'b')
 ->fields('b', array( 'BID', 'bezirksname' ))
 ->execute();

 $pathThisFile = $_SERVER['REQUEST_URI'];

 //Darstellung

 ob_start(); // Aktiviert "Render"-modus

 include_once path_to_theme().'/templates/akteurformular.tpl.php';

 return ob_get_clean(); // Übergabe des gerenderten "akteurformular.tpl"

 } // END function akteurDisplay()
} // END class akteurformular
