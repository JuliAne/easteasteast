<?php
/**
 * eventformular.php stellt ein Formular dar,
 * in welches alle Informationen über eine Veranstaltung
 * eingetragen werden können.
 * Pflichtfelder sind: Name, Veranstalter, Datum.
 * Anschließend werden die Daten in die DB-Tabellen eingetragen.
 *
 * Ruth, 2015-07-20
 * Felix, 2015-09-02
 */

/**
 * FUNKTIONEN: ...
 * TODO: "Privat" miteinbringen
 */

Class eventformular {

  //Variablen zum Speichern von Werten, welche in die DB-Tabellen eingefügt werden sollen
  //$tbl_event
  var $name = "";
  var $veranstalter = "";
  var $start = "";
  var $zeit_von = "";
  var $zeit_bis = "";
  var $ende = "";
  var $bild = "";
  var $kurzbeschreibung = "";
  var $url = "";

  //$tbl_adresse
  var $strasse = "";
  var $nr = "";
  var $adresszusatz = "";
  var $plz = "";
  var $ort = "";
  var $gps = "";
  var $adresse = "";

  //Tags:
  var $sparten= "";

  //Speicherort fuer Bilder
  var $bildpfad = "/home/swp15-aae/drupal/sites/default/files/styles/large/public/field/image/";
  var $short_bildpfad = "sites/default/files/styles/large/public/field/image/";

  //Variable zur Freigabe: muss true sein
  var $freigabe = true;
  var $fehler = array(); // In diesem Array werden alle Fehler gespeichert

  //Variablen, welche Texte in den Formularfeldern beschreiben ("placeholder")

  var $ph_name = "Veranstaltungsname";
  var $ph_veranstalter = "Veranstalter";
  var $ph_start = "Starttag (yyyy-mm-dd)";
  var $ph_zeit_von = "von (Uhrzeit: hh:mm)";
  var $ph_zeit_bis = "bis (Uhrzeit: hh:mm)";
  var $ph_ende = "Endtag (yyyy-mm-dd)";
  var $ph_bild = "Bild";
  var $ph_kurzbeschreibung = "Beschreibung";
  var $ph_url = "URL";
  var $ph_strasse = "Strasse";
  var $ph_nr = "Hausnummer";
  var $ph_adresszusatz = "Adresszusatz";
  var $ph_plz = "PLZ";
  var $ph_ort = "Bezirk";
  var $ph_gps = "GPS Koordinaten";
  var $ph_sparten = "Tags kommasepariert eingeben!";

  // DB-Tabellen
  var $tbl_adresse = "aae_data_adresse";
  var $tbl_event = "aae_data_event";
  var $tbl_akteur_events = "aae_data_akteur_hat_events";
  var $tbl_bezirke = "aae_data_bezirke";
  var $tbl_akteur = "aae_data_akteur";
  var $tbl_hat_user = "aae_data_hat_user";
  var $tbl_event_sparte = "aae_data_event_hat_sparte";
  var $tbl_sparte = "aae_data_kategorie";

  var $user_id;

 function __construct() {

   global $user;
   $this->user_id = $user->uid;

 // Sicherheitsschutz
 if(!user_is_logged_in()) drupal_access_denied();

 } // END Constructor

 /* Funktion, welche reihenweise POST-Werte auswertet, abspeichert bzw. ausgibt */

 public function run() {

   $output = '';

   if (isset($_POST['submit'])) {

     if ($this->eventCheckPost()) {
       $this->eventSpeichern();
       $output = $this->eventDisplay();
     } else {
       $output = $this->eventDisplay();
    }
  } else {
    $output = $this->eventDisplay();
 }

 return $output;

}

 /* Einfache Funktion zum Filtern von POST-Daten. Gerne erweiterbar. */

 private function clearContent($trimTag) {
  $clear = trim($trimTag);
  return strip_tags($clear);
 }

//-----------------------------------

private function eventCheckPost() {

// Wird ausgeführt, wenn auf "Speichern" gedrückt wird

  //Wertezuweisung
  $this->name = $this->clearContent($_POST['name']);
  $this->veranstalter = $this->clearContent($_POST['veranstalter']);
  $this->start = $this->clearContent($_POST['start']);
  $this->url = $this->clearContent($_POST['url']);
  $this->ende = $this->clearContent($_POST['ende']);
  $this->zeit_von = $this->clearContent($_POST['zeit_von']);
  $this->zeit_bis = $this->clearContent($_POST['zeit_bis']);
  if(isset($_POST['bild'])) $this->bild = $_POST['bild'];
  $this->kurzbeschreibung = $this->clearContent($_POST['kurzbeschreibung']);
  $this->strasse = $this->clearContent($_POST['strasse']);
  $this->nr = $this->clearContent($_POST['nr']);
  $this->adresszusatz = $this->clearContent($_POST['adresszusatz']);
  $this->plz = $this->clearContent($_POST['plz']);
  $this->ort = $this->clearContent($_POST['ort']);
  $this->gps = $this->clearContent($_POST['gps']);
  $this->sparten = $this->clearContent($_POST['sparten']);
  $explodedsparten = "";

  if($sparten != "") $explodedsparten = explode(",", $sparten);

  //überflüssige Leerzeichen am Anfang entfernen

  if ($this->sparten != "") {
   $countsparten = count($explodedsparten);
   $i = 0;

   while($i < $countsparten) {
    $explodedsparten[$i] = $this->clearContent($explodedsparten[$i]);
    $i++;
   }
  }

  //Check-Klauseln

  //Check, ob ein Name eingegeben wurde:
  if(strlen($this->name) == 0) {
   $this->fehler['name'] = "Bitte einen Veranstaltungsnamen eingeben!";
	 $this->freigabe = false;
  }

  //Ckeck, ob Datum angegeben wurde
  if(strlen($this->start) == 0) {
   $this->fehler['start'] = "Bitte ein Datum angeben!";
   $this->freigabe = false;
  }

  //Check, ob Bezirk ausgewählt wurde
  if(strlen($this->ort) == 0){
  	$this->fehler['ort'] = "Bitte einen Bezirk auswählen!";
  	$this->freigabe = false;
  }

  //Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.

  // TODO (Felix): Vlt. sollten wir die max. Länge der Werte im 32/64/128/256/... - Abstand
  // gestalten; habe gehört, das sei besser für die DB-Performance...

  if (strlen($this->name) > 100) {
	 $this->fehler['name'] = "Bitte geben Sie einen kürzeren Namen an oder verwenden Sie ein Kürzel.";
   $this->freigabe = false;
  }

  if (strlen($this->url) > 200) {
	 $this->fehler['url'] = "Bitte geben Sie eine kürzere URL an.";
	 $this->freigabe = false;
  }

  if (strlen($this->kurzbeschreibung) > 500) {
   $this->fehler['kurzbeschreibung'] = "Bitte geben Sie eine kürzere Beschreibung an.";
	 $this->freigabe = false;
  }

  if (strlen($this->strasse) > 100) {
	 $this->fehler['strasse'] = "Bitte geben Sie einen kürzeren Strassennamen an.";
	 $this->freigabe = false;
  }

  if (strlen($this->nr) > 100) {
	 $this->fehler['nr'] = "Bitte geben Sie eine kürzere Nummer an.";
	 $this->freigabe = false;
  }

  if (strlen($this->adresszusatz) > 100) {
	 $this->fehler['adresszusatz'] = "Bitte geben Sie einen kürzeren Adresszusatz an.";
   $this->freigabe = false;
  }

  if (strlen($this->plz) > 100) {
	 $this->fehler['plz'] = "Bitte geben Sie eine kürzere PLZ an.";
   $this->freigabe = false;
  }

  if (strlen($this->ort) > 100) {
   $this->fehler['ort'] = "Bitte geben Sie einen kürzeren Ortsnamen an.";
	 $this->freigabe = false;
  }

  if (strlen($this->gps) > 100) {
   $this->fehler['gps'] = "Bitte geben Sie kürzere GPS-Daten an.";
	 $this->freigabe = false;
  }

  return $this->freigabe;
 } // END function eventCheckPost()


private function eventSpeichern () {

	require_once $modulePath . '/database/db_connect.php';
	$db = new DB_CONNECT();

  //Wenn Bilddatei ausgewählt wurde...
  // TODO: Bild skalieren (Beste Breite???) bzw. komprimieren, s.
  // https://api.drupal.org/api/drupal/modules!image!image.module/7

  if ($_FILES) {
   $bildname = $_FILES['bild']['name'];

   if ($bildname != "") {
    if (!move_uploaded_file($_FILES['bild']['tmp_name'], $this->bildpfad.$this->bildname)) {
      echo 'Error: Konnte Bild nicht hochladen. Bitte <a href="'.base_path.'contact">informieren Sie den Administrator</a>. Bildname: <br />'.$bildname;
      exit();
    }
    $this->bild = base_path().$short_bildpfad.$bildname;
   }
  }

	//Abfrage, ob Adresse bereits in Adresstabelle
	//Addressdaten aus DB holen:
	$resultadresse = db_select($this->tbl_adresse, 'a')
	  ->fields('a', array(
	    'ADID',
	   	'gps',
	  ))
	  ->condition('strasse', $this->strasse, '=')
	  ->condition('nr', $this->nr, '=')
	  ->condition('adresszusatz', $this->adresszusatz, '=')
	  ->condition('plz', $this->plz, '=')
	  ->condition('bezirk', $this->ort, '=')
	  ->execute();

 	 //wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen
   if($resultadresse->rowCount() == 0) {

    //Adresse nicht vorhanden
	  $adresse = db_insert($this->tbl_adresse)
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
	 	    ->fields(array(
			  'gps' => $this->gps,
	        ))
	        ->condition('ADID', $row->ADID, '=')
	        ->execute();
	    }
	    $this->adresse = $row->ADID;//Adress-ID merken
	  }
	}

	//Zeitformatierung
	if (strlen($this->ende) == 0) $this->ende = $this->start.' '.$this->zeit_bis;
	else $this->ende = $this->ende.' '.$this->zeit_bis;

	$this->start = $this->start.' '.$this->zeit_von;

  //tbl_event INSERT!!!
	$event_id = db_insert($this->tbl_event)
   	->fields(array(
		'name' => $this->name,
		'ort' => $this->adresse,
		'start' => $this->start,
		'url' => $this->url,
		'ende' => $this->ende,
		'bild' => $this->bild,
		'kurzbeschreibung' => $this->kurzbeschreibung,
		'ersteller' => $this->user_id,
	  ))
	  ->execute();
	//falls Akteur angegeben wurde

	if ($this->veranstalter != "") {
	//tbl_akteur_events INSERT!!!
	$akteurevents = db_insert($this->tbl_akteur_events)
   	  ->fields(array(
		'AID' => $this->veranstalter,
		'EID' => $this->event_id,
	  ))
	  ->execute();
	}

	// Falls Tags angegeben wurden

	if ($this->sparten != "") {

    $sparte_id = "";
    $countsparten = count($explodedsparten);
	  $i = 0;

	  while($i < $countsparten){
		//1. Prüfen, ob Tag bereits in Tabelle $tbl_sparte
		$resultsparte = db_select($this->tbl_sparte, 's')
		->fields('s', array( 'KID' ))
		->condition('kategorie', $explodedsparten[$i], '=')
		->execute();

		$countresult = $resultsparte->rowCount();

		if($countresult == 0){
      //nein: Tag in $tbl_sparte einfügen

		  $sparte_id = db_insert($this->tbl_sparte)
		  ->fields(array( 'kategorie' => $explodedsparten[$i] ))
			->execute();

		} else {
      // ja: KID des Tags holen
		  foreach ($resultsparte as $row) {
		   $sparte_id = $row->KID;
		  }
		}

		//2. Event+Tag in Tabelle $tbl_event_sparte einfügen
		$inserteventsparte = db_insert($this->tbl_event_sparte)
		  ->fields(array(
		    'hat_EID' => $event_id,
		    'hat_KID' => $sparte_id,
		  ))
		  ->execute();
	    $i++;
	  }
	}

	header("Location: Event/".$event_id);
    // Hier muss hin, welche Seite aufgerufen werden soll,
	  // nachdem die Daten erfolgreich gespeichert wurden.

} // END function event_speichern()

private function eventDisplay() {
 // Ausgabe des Eventformulars:

 if (array_intersect(array('administrator'), $user->roles)) {
 //alle Akteure abfragen, die in DB: nur Admin
  $resultakteure = db_select($this->tbl_akteur, 'a')
  ->fields('a', array(
    'AID',
    'name',
    ))
    ->execute();
 } else {
  //Akteure abfragen, die in DB und für welche User Schreibrechte hat
  $res = db_select($this->tbl_akteur, 'a');
  $res->join($this->tbl_hat_user, 'u', 'a.AID = u.hat_AID AND u.hat_UID = :uid', array(':uid' => $this->user_id));
  $res->fields('a', array('AID','name'));
  $resultakteure=$res->execute();
 } // GGF. ALLES HIER DRÜBER ANPASSEN

 $resultbezirke = db_select($this->tbl_bezirke, 'b')
  ->fields('b', array(
  'BID',
  'bezirksname',
  ))
  ->execute();

 $countbezirke = $resultbezirke->rowCount();

 $pathThisFile = $_SERVER['REQUEST_URI'];

 ob_start(); // Aktiviert "Render"-modus

 include_once path_to_theme().'/templates/eventformular.tpl.php';

 return ob_get_clean(); // Übergabe des gerenderten "eventformular.tpl.php"

 } // END function eventDisplay()
}
