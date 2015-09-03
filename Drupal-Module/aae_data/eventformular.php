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
  var $sparten="";

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
  var $ph_ort = "Ort der Veranstaltung";
  var $ph_url = "URL";
  var $ph_strasse = "Strasse";
  var $ph_nr = "Hausnummer";
  var $ph_adresszusatz = "Adresszusatz";
  var $ph_plz = "PLZ";
  var $ph_ort = "Bezirk";
  var $ph_gps = "GPS Koordinaten";
  var $ph_sparten = "Tags kommasepariert eingeben!";

 /* Einfache Funktion zum Filtern von POST-Daten. Gerne erweiterbar. */

 private function clearContent($trimTag) {
  $clear = trim($trimTag);
  return strip_tags($clear);
 }

 public function eventPageInit() {

   global $user;
   $user_id = $user->uid;

 // Sicherheitsschutz
 if(!user_is_logged_in()) drupal_access_denied();

//-----------------------------------



} // END function eventPageInit()

//-----------------------------------

public function eventCheckPost() {

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

  if ($sparten != "") {
   $countsparten = count($explodedsparten);
   $i = 0;

   while($i < $countsparten) {
    $explodedsparten[$i] = $this->clearContent($explodedsparten[$i]);
    $i++;
   }
  }

  //Check-Klauseln

  //Check, ob ein Name eingegeben wurde:
  if(strlen($name) == 0) {
   $this->fehler['name'] = "Bitte einen Veranstaltungsnamen eingeben!";
	 $this->freigabe = false;
  }

  //Ckeck, ob Datum angegeben wurde
  if(strlen($start) == 0) {
   $this->fehler['start'] = "Bitte ein Datum angeben!";
   $this->freigabe = false;
  }

  //Check, ob Bezirk ausgewählt wurde
  if(strlen($ort) == 0){
  	$this->fehler['ort'] = "Bitte einen Bezirk auswählen!";
  	$this->freigabe = false;
  }

  //Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.

  // TODO (Felix): Vlt. sollten wir die max. Länge der Werte im 32/64/128/256/... - Abstand
  // gestalten; habe gehört, das sei besser für die DB-Performance...

  if (strlen($name) > 100) {
	 $this->fehler['name'] = "Bitte geben Sie einen kürzeren Namen an oder verwenden Sie ein Kürzel.";
   $this->freigabe = false;
  }

  if (strlen($url) > 200) {
	 $this->fehler['url'] = "Bitte geben Sie eine kürzere URL an.";
	 $this->freigabe = false;
  }

  if (strlen($kurzbeschreibung) > 500) {
   $this->fehler['kurzbeschreibung'] = "Bitte geben Sie eine kürzere Beschreibung an.";
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
	 $this->fehler['plz'] = "Bitte geben Sie eine kürzere PLZ an.";
   $this->freigabe = false;
  }

  if (strlen($ort) > 100) {
   $this->fehler['ort'] = "Bitte geben Sie einen kürzeren Ortsnamen an.";
	 $this->freigabe = false;
  }

  if (strlen($gps) > 100) {
   $this->fehler['gps'] = "Bitte geben Sie kürzere GPS-Daten an.";
	 $this->freigabe = false;
  }

  return $this->freigabe;
 } // END function eventCheckPost()


public function eventSpeichern () {

  // DB-Tabellen
  $tbl_adresse = "aae_data_adresse";
  $tbl_event = "aae_data_event";
  $tbl_akteur_events = "aae_data_akteur_hat_events";
  $tbl_bezirke = "aae_data_bezirke";
  $tbl_akteur = "aae_data_akteur";
  $tbl_hat_user = "aae_data_hat_user";
  $tbl_event_sparte = "aae_data_event_hat_sparte";
  $tbl_sparte = "aae_data_kategorie";

	require_once $modulePath . '/database/db_connect.php';
	$db = new DB_CONNECT();

  //Wenn Bilddatei ausgewählt wurde...
  // TODO: Bild skalieren (Beste Breite???) bzw. komprimieren, s.
  // https://api.drupal.org/api/drupal/modules!image!image.module/7

  if ($_FILES) {
   $bildname = $_FILES['bild']['name'];

   if ($bildname != "") {
    if (!move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname)) {
      echo 'Error: Konnte Bild nicht hochladen. Bitte <a href="'.base_path.'contact">informieren Sie den Administrator</a>. Bildname: <br />'.$bildname;
      exit();
    }
    $bild = base_path().$short_bildpfad.$bildname;
   }
  }

	//Abfrage, ob Adresse bereits in Adresstabelle
	//Addressdaten aus DB holen:
	$resultadresse = db_select($tbl_adresse, 'a')
	  ->fields('a', array(
	    'ADID',
	   	'gps',
	  ))
	  ->condition('strasse', $strasse, '=')
	  ->condition('nr', $nr, '=')
	  ->condition('adresszusatz', $adresszusatz, '=')
	  ->condition('plz', $plz, '=')
	  ->condition('bezirk', $ort, '=')
	  ->execute();

 	 //wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen
   if($resultadresse->rowCount() == 0) {

    //Adresse nicht vorhanden
	  $adresse = db_insert($tbl_adresse)
	    ->fields(array(
		  'strasse' => $strasse,
		  'nr' => $nr,
		  'adresszusatz' => $adresszusatz,
		  'plz' => $plz,
		  'bezirk' => $ort,
		  'gps' => $gps,
		))
		->execute();
	} else {

    //Adresse bereits vorhanden
	  foreach ($resultadresse as $row) {
	    //Abfrage, ob GPS-Angaben gemacht wurden
	    if (strlen($gps) != 0 && strlen($row->gps) == 0 ) {
        //ja UND es sind bisher keine GPS-Daten zu der Adresse in der DB
	      //Update der Adresse
	      $adresse_updated = db_update($tbl_adresse)
	 	    ->fields(array(
			  'gps' => $gps,
	        ))
	        ->condition('ADID', $row->ADID, '=')
	        ->execute();
	    }
	    $adresse = $row->ADID;//Adress-ID merken
	  }
	}

	//Zeitformatierung
	if (strlen($ende) == 0) $ende = $start.' '.$zeit_bis;
	else $ende = $ende.' '.$zeit_bis;

	$start = $start.' '.$zeit_von;

  //tbl_event INSERT!!!
	$event_id = db_insert($tbl_event)
   	->fields(array(
		'name' => $name,
		'ort' => $adresse,
		'start' => $start,
		'url' => $url,
		'ende' => $ende,
		'bild' => $bild,
		'kurzbeschreibung' => $kurzbeschreibung,
		'ersteller' => $user->uid,
	  ))
	  ->execute();
	//falls Akteur angegeben wurde

	if ($veranstalter != "") {
	//tbl_akteur_events INSERT!!!
	$akteurevents = db_insert($tbl_akteur_events)
   	  ->fields(array(
		'AID' => $veranstalter,
		'EID' => $event_id,
	  ))
	  ->execute();
	}

	// Falls Tags angegeben wurden

	if ($sparten != "") {

    $sparte_id = "";
    $countsparten = count($explodedsparten);
	  $i = 0;

	  while($i < $countsparten){
		//1. Prüfen, ob Tag bereits in Tabelle $tbl_sparte
		$resultsparte = db_select($tbl_sparte, 's')
		->fields('s', array( 'KID' ))
		->condition('kategorie', $explodedsparten[$i], '=')
		->execute();

		$countresult = $resultsparte->rowCount();

		if($countresult == 0){
      //nein: Tag in $tbl_sparte einfügen

		  $sparte_id = db_insert($tbl_sparte)
		  ->fields(array( 'kategorie' => $explodedsparten[$i] ))
			->execute();

		} else {
      // ja: KID des Tags holen
		  foreach ($resultsparte as $row) {
		   $sparte_id = $row->KID;
		  }
		}

		//2. Event+Tag in Tabelle $tbl_event_sparte einfügen
		$inserteventsparte = db_insert($tbl_event_sparte)
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

public function eventDisplay() {
 // Ausgabe des Eventformulars:

 // DB-Tabellen
 $tbl_adresse = "aae_data_adresse";
 $tbl_event = "aae_data_event";
 $tbl_akteur_events = "aae_data_akteur_hat_events";
 $tbl_bezirke = "aae_data_bezirke";
 $tbl_akteur = "aae_data_akteur";
 $tbl_hat_user = "aae_data_hat_user";
 $tbl_event_sparte = "aae_data_event_hat_sparte";
 $tbl_sparte = "aae_data_kategorie";

 global $user;
 $user_id = $user->uid;

 if (array_intersect(array('administrator'), $user->roles)) {
 //alle Akteure abfragen, die in DB: nur Admin
  $resultakteure = db_select($tbl_akteur, 'a')
  ->fields('a', array(
    'AID',
    'name',
    ))
    ->execute();
 } else {
  //Akteure abfragen, die in DB und für welche User Schreibrechte hat
  $res = db_select($tbl_akteur, 'a');
  $res->join($tbl_hat_user, 'u', 'a.AID = u.hat_AID AND u.hat_UID = :uid', array(':uid' => $user->uid));
  $res->fields('a', array('AID','name'));
  $resultakteure=$res->execute();
 } // GGF. ALLES HIER DRÜBER ANPASSEN

 $resultbezirke = db_select($tbl_bezirke, 'b')
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
