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

//Eingeloggter User
global $user;
$user_id = $user->uid;

//Sicherheitsschutz
if(!user_is_logged_in()){
  drupal_access_denied();
}

//DB-Tabellen
$tbl_adresse = "aae_data_adresse";
$tbl_event = "aae_data_event";
$tbl_akteur_events = "aae_data_akteur_hat_events";
$tbl_bezirke = "aae_data_bezirke";
$tbl_akteur = "aae_data_akteur";
$tbl_hat_user = "aae_data_hat_user";
$tbl_event_sparte = "aae_data_event_hat_sparte";
$tbl_sparte = "aae_data_kategorie";

//-----------------------------------

//Variablen zum Speichern von Werten, welche in die DB-Tabellen eingefügt werden sollen
//$tbl_event
$name = "";
$veranstalter = "";
$start = "";
$zeit_von = "";
$zeit_bis = "";
$ende = "";
$bild = "";
$kurzbeschreibung = "";
$url = "";

//$tbl_adresse
$strasse = "";
$nr = "";
$adresszusatz = "";
$plz = "";
$ort = "";
$gps = "";
$adresse = "";

//Tags:
$sparten="";

//Speicherort fuer Bilder
$bildpfad = "/home/swp15-aae/drupal/sites/default/files/styles/large/public/field/image/";
$short_bildpfad = "sites/default/files/styles/large/public/field/image/";

//-----------------------------------

//Variable zur Freigabe: muss true sein
$freigabe = true;

//Fehlervariablen
$fehler_name = "";
$fehler_veranstalter = "";
$fehler_start = "";
$fehler_zeit_von = "";
$fehler_zeit_bis = "";
$fehler_ende = "";
$fehler_bild = "";
$fehler_kurzbeschreibung = "";
$fehler_url = "";
$fehler_strasse = "";
$fehler_nr = "";
$fehler_adresszusatz = "";
$fehler_plz = "";
$fehler_ort = "";
$fehler_gps = "";
$fehler_sparten = "";

//-----------------------------------

//Variablen, welche Texte in den Formularfeldern beschreiben ("placeholder")

$ph_name = "Veranstaltungsname";
$ph_veranstalter = "Veranstalter";
$ph_start = "Starttag (yyyy-mm-dd)";
$ph_zeit_von = "von (Uhrzeit: hh:mm)";
$ph_zeit_bis = "bis (Uhrzeit: hh:mm)";
$ph_ende = "Endtag (yyyy-mm-dd)";
$ph_bild = "Bild";
$ph_kurzbeschreibung = "Beschreibung";
$ph_ort = "Ort der Veranstaltung";
$ph_url = "URL";
$ph_strasse = "Strasse";
$ph_nr = "Hausnummer";
$ph_adresszusatz = "Adresszusatz";
$ph_plz = "PLZ";
$ph_ort = "Bezirk";
$ph_gps = "GPS Koordinaten (durch Leerzeichen getrennt!)";
$ph_sparten = "Tags kommasepariert eingeben!";

//-----------------------------------

// Wird ausgeführt, wenn auf "Speichern" gedrückt wird
// TODO: Werte Filtern - entweder durch Drupal-interne Funktionen zur
// Absicherung von POST-Data oder Einsatz von PHP-Bibliothek "phpsec"

if (isset($_POST['submit'])) {

  //Wertezuweisung
  $name = $_POST['name'];
  $veranstalter = $_POST['veranstalter'];
  $start = $_POST['start'];
  $url = $_POST['url'];
  $ende = $_POST['ende'];
  $zeit_von = $_POST['zeit_von'];
  $zeit_bis = $_POST['zeit_bis'];
  if(isset($_POST['bild'])){
    $bild = $_POST['bild'];
  }
  $kurzbeschreibung = $_POST['kurzbeschreibung'];

  $strasse = $_POST['strasse'];
  $nr = $_POST['nr'];
  $adresszusatz = $_POST['adresszusatz'];
  $plz = $_POST['plz'];
  $ort = $_POST['ort'];
  $gps = $_POST['gps'];

  $sparten = $_POST['sparten'];
  $explodedsparten = "";
  if($sparten != ""){
	$explodedsparten = explode(",", $sparten);
  }

//-------------------------------------
  //Check-Klauseln

  $fehler = array(); // In diesem Array werden alle Fehler gespeichert

  //Check, ob ein Name eingegeben wurde:
  if(strlen($name) == 0) {
   $fehler['name'] = "Bitte einen Veranstaltungsnamen eingeben!";
	 $freigabe = false;
  }

  //Ckeck, ob Datum angegeben wurde
  if(strlen($start) == 0) {
   $fehler['start'] = "Bitte ein Datum angeben!";
  	$freigabe = false;
  }

  //Check, ob Bezirk ausgewählt wurde
  if(strlen($ort) == 0){
  	$fehler['ort'] = "Bitte einen Bezirk auswählen!";
  	$freigabe = false;
  }

  //überflüssige Leerzeichen am Anfang entfernen
  $name=trim($name);
  $veranstalter = trim($veranstalter);
  $start = trim($start);
  $zeit_von = trim($zeit_von);
  $zeit_bis = trim($zeit_bis);
  $ende = trim($ende);
  $url = trim($url);
  $bild = trim($bild);
  $kurzbeschreibung = trim($kurzbeschreibung);
  $strasse = trim($strasse);
  $nr = trim($nr);
  $adresszusatz = trim($adresszusatz);
  $plz = trim($plz);
  $ort = trim($ort);
  $gps = trim($gps);

  // Tags
  if($sparten != ""){
	$countsparten = count($explodedsparten);
	$i = 0;
	while($i < $countsparten) {
	  $explodedsparten[$i] = trim($explodedsparten[$i]);
	  $explodedsparten[$i] = strip_tags($explodedsparten[$i]);
	  $i++;
	 }
  }

  //und alle Tags entfernen (Hacker)
  $name=strip_tags($name);
  $veranstalter = strip_tags($veranstalter);
  $start = strip_tags($start);
  $zeit_von = strip_tags($zeit_von);
  $zeit_bis = strip_tags($zeit_bis);
  $ende = strip_tags($ende);
  $url = strip_tags($url);
  $bild = strip_tags($bild);
  $kurzbeschreibung = strip_tags($kurzbeschreibung);
  $strasse = strip_tags($strasse);
  $nr = strip_tags($nr);
  $adresszusatz = strip_tags($adresszusatz);
  $plz = strip_tags($plz);
  $ort = strip_tags($ort);
  $gps = strip_tags($gps);

  //Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.

  // TODO (Felix): Vlt. sollten wir die Länge der Werte im 32/64/128/... - Abstand
  // gestalten; habe gehört, das sei besser für die DB-Performance...

  if (strlen($name) > 100) {
	 $fehler['name'] = "Bitte geben Sie einen kürzeren Namen an oder verwenden Sie ein Kürzel.";
   $freigabe = false;
  }

  if (strlen($url) > 200) {
	 $fehler['url'] = "Bitte geben Sie eine kürzere URL an.";
	 $freigabe = false;
  }

  if (strlen($kurzbeschreibung) > 500) {
   $fehler['kurzbeschreibung'] = "Bitte geben Sie eine kürzere Beschreibung an.";
	 $freigabe = false;
  }

  if (strlen($strasse) > 100) {
	 $fehler['strasse'] = "Bitte geben Sie einen kürzeren Strassennamen an.";
	 $freigabe = false;
  }

  if (strlen($nr) > 100) {
	 $fehler['nr'] = "Bitte geben Sie eine kuerzere Nummer an.";
	 $freigabe = false;
  }

  if (strlen($adresszusatz) > 100) {
	 $fehler['adresszusatz'] = "Bitte geben Sie einen kuerzeren Adresszusatz an.";
   $freigabe = false;
  }

  if (strlen($plz) > 100) {
	 $fehler['plz'] = "Bitte geben Sie eine kürzere PLZ an.";
   $freigabe = false;
  }

  if (strlen($ort) > 100) {
   $fehler['ort'] = "Bitte geben Sie einen kürzeren Ortsnamen an.";
	 $freigabe = false;
  }

  if (strlen($gps) > 100) {
   $fehler['gps'] = "Bitte geben Sie kürzere GPS-Daten an.";
	 $freigabe = false;
  }

  //Wenn Bilddatei ausgewählt wurde...
  if($_FILES){
	 $bildname = $_FILES['bild']['name'];

	 if($bildname != ""){
	  if (!move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname)) {
      echo 'Error: Konnte Bild nicht hochladen. Bitte <a href="'.base_path.'contact">informieren Sie den Administrator</a>. Bildname: <br />'.$bildname;
      exit();
    }
	  $bild = base_path().$short_bildpfad.$bildname;
	 }
  }

//---------------------------------

  //Wenn $goodtogo true, ab in die DB mit den Daten
  if ($freigabe == true) {
	require_once $modulePath . '/database/db_connect.php';
	//include $modulePath . '/templates/utils/rest_helper.php'; Ist aus dem Künstlermodul übernommen
	$db = new DB_CONNECT();
	//Das Ergebnis von db_insert()->...->execute(); ist die ID, von diesem Eintrag

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
	    if(strlen($gps) != 0 && strlen($row->gps) == 0 ){
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
	if(strlen($ende) == 0){
	  $ende = $start.' '.$zeit_bis;
	}else{
	  $ende = $ende.' '.$zeit_bis;
	}
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
	if($veranstalter != ""){
	//tbl_akteur_events INSERT!!!
	$akteurevents = db_insert($tbl_akteur_events)
   	  ->fields(array(
		'AID' => $veranstalter,
		'EID' => $event_id,
	  ))
	  ->execute();
	}

	// Falls Tags angegeben wurden

	if($sparten != ""){

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
	    $i = $i+1;
	  }
	}

	header("Location: Event/".$event_id);
    // Hier muss hin, welche Seite aufgerufen werden soll,
	  // nachdem die Daten erfolgreich gespeichert wurden.
	}

} else {
 //Formular wird zum ersten Mal aufgerufen: nichts tun
}

 if(array_intersect(array('administrator'), $user->roles)){
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
}

$resultbezirke = db_select($tbl_bezirke, 'b')
  ->fields('b', array(
  'BID',
	'bezirksname',
  ))
  ->execute();
$countbezirke = $resultbezirke->rowCount();

$pathThisFile = $_SERVER['REQUEST_URI'];

// Ausgabe des Eventformulars

ob_start(); // Aktiviert "Render"-modus

include_once path_to_theme().'/templates/eventformular.tpl.php';

$profileHTML = ob_get_clean(); // Übergabe des gerenderten "eventformular.tpl.php"
