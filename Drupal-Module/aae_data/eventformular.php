<?php
/**
 * eventformular.php stellt ein Formular dar,
 * in welches alle Informationen über eine Veranstaltung
 * eingetragen werden können.
 * Pflichtfelder sind: Name, Veranstalter, Datum.
 * Anschließend werden die Daten in die DB-Tabellen eingetragen.
 *
 * Ruth, 2015-07-20
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

//Speicherort fuer Bilder
$bildpfad = "sites/all/modules/aae_data/images/";

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

//-----------------------------------

//Variablen, welche Texte in den Formularfeldern halten
$ph_name = "Veranstaltungsname";
$ph_veranstalter = "Veranstalter";
$ph_start = "Starttag (dd.mm.yyyy)";
$ph_zeit_von = "von (Uhrzeit: hh:mm)";
$ph_zeit_bis = "bis (Uhrzeit: hh:mm)";
$ph_ende = "Endtag (dd:mm:yyyy)";
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

//-----------------------------------

//das wird ausgeführt, wenn auf "Speichern" gedrückt wird
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
	
//-------------------------------------
  //Check-Klauseln
	
  //Check, ob ein Name eingegeben wurde:
  if(strlen($name) == 0){
    //Feld nicht ausgefüllt
    $fehler_name = "Bitte einen Veranstaltungsnamen eingeben!";
	$freigabe = false;
  }
  //Ckeck, ob Veranstalter angegeben wurde
  if(strlen($veranstalter) == 0){
    //Feld nicht ausgefüllt
    $fehler_veranstalter = "Bitte einen Veranstalter auswählen!";
	$freigabe = false;
  }
  //Ckeck, ob Datum angegeben wurde
  if(strlen($start) == 0){
    //Feld nicht ausgefüllt
    $fehler_start = "Bitte eine Datum angeben!";
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
  if (strlen($name) > 100){
	$fehler_name = "Bitte geben Sie einen kuerzeren Namen an oder verwenden Sie ein Kuerzel.";
	$freigabe = false;
  }
  if (strlen($url) > 200){
	$fehler_url = "Bitte geben Sie eine kuerzere URL an.";
	$freigabe = false;
  }
  if (strlen($kurzbeschreibung) > 500){
	$fehler_kurzbeschreibung = "Bitte geben Sie eine kuerzere Beschreibung an.";
	$freigabe = false;
  }
  if (strlen($strasse) > 100){
	$fehler_strasse = "Bitte geben Sie einen kuerzeren Strassennamen an.";
	$freigabe = false;
  }
  if (strlen($nr) > 100){
	$fehler_nr = "Bitte geben Sie eine kuerzere Nummer an.";
	$freigabe = false;
  }
  if (strlen($adresszusatz) > 100){
	$fehler_adresszusatz = "Bitte geben Sie einen kuerzeren Adresszusatz an.";
	$freigabe = false;
  }
  if (strlen($plz) > 100){
	$fehler_plz = "Bitte geben Sie eine kuerzere PLZ an.";
	$freigabe = false;
  }
  if (strlen($ort) > 100){
	$fehler_ort = "Bitte geben Sie einen kuerzeren Ortsnamen an.";
	$freigabe = false;
  }
  if (strlen($gps) > 100){
	$fehler_gps = "Bitte geben Sie kuerzere GPS-Daten an.";
	$freigabe = false;
  }

  //Wenn Bilddatei ausgewählt wurde...
  if($_FILES){
	$bildname = $_FILES['bild']['name'];
	if($bildname != ""){
	  move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname);//Upload
	  $bild = "images/".$bildname;
	}
  }
  
//---------------------------------

  //Wenn $goodtogo true, ab in die DB mit den Daten
  if($freigabe == true){
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
    $i = $resultadresse->rowCount();
	if($i == 0){//Adresse nicht vorhanden
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
	}else {//Adresse bereits vorhanden
	  foreach ($resultadresse as $row) {
	    //Abfrage, ob GPS-Angaben gemacht wurden
	    if(strlen($gps) != 0 && strlen($row->gps) == 0 ){//ja UND es sind bisher keine GPS-Daten zu der Adresse in der DB
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
	  $ende = $start;
	}
	$start = $start.' '.$zeit_von.' '.$zeit_bis;

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
	  ))
	  ->execute();
	//tbl_akteur_events INSERT!!!
	$akteurevents = db_insert($tbl_akteur_events)
   	  ->fields(array(
		'AID' => $veranstalter,
		'EID' => $event_id,
	  ))
	  ->execute();
		
	header("Location: ?q=Events"); //Hier muss hin, welche Seite aufgerufen werden soll,
	  //nach dem die Daten erfolgreich gespeichert wurden.
	}
	
}else{
 //Formular wird zum ersten Mal aufgerufen: nichts tun
}

$pathThisFile = $_SERVER['REQUEST_URI']; 

//Darstellung
$profileHTML = <<<EOF
<form action='$pathThisFile' method='POST' enctype='multipart/form-data'>

  <label>Name (Pflichtfeld):</label>
  <input type="text" class="event" id="eventNameInput" name="name" value="$name" placeholder="$ph_name" required>$fehler_name

  <label>Veranstalter (Pflichtfeld):</label>
  <!--<input type="text" class="event" id="eventVeranstalterInput" name="veranstalter" value="$veranstalter" placeholder="$ph_veranstalter">$fehler_veranstalter-->

EOF;

//Akteure abfragen, die in DB
$resultakteure = db_select($tbl_akteur, 'a')
  ->fields('a', array(
    'AID',
	'name',
  ))
  ->execute();
$countakteure = $resultakteure->rowCount();
//Dropdownliste zur Akteurauswahl
$profileHTML .= '<select name="veranstalter" size="'.$countakteure.'" >';
foreach ($resultakteure as $row) {
  $profileHTML .= '<option value="'.$row->AID.'">'.$row->name.'</option>';
}
$profileHTML .= '</select>';

$profileHTML .= <<<EOF
  <label>Datum (Pflichtfeld):</label>
  <input type="text" class="event" id="eventStartdatumInput" name="start" value="$start" placeholder="$ph_start">$fehler_start
  <label>von (Uhrzeit; ganztägig: keine Uhrzeit angeben):</label>
  <input type="text" class="event" id="eventZeitvonInput" name="zeit_von" value="$zeit_von" placeholder="$ph_zeit_von">$zeit_von
  <label>bis (Uhrzeit; ganztägig: keine Uhrzeit angeben):</label>
  <input type="text" class="event" id="eventZeitbisInput" name="zeit_bis" value="$zeit_bis" placeholder="$ph_zeit_bis">$zeit_bis
  <label>Ende (Datum):</label>
  <input type="text" class="event" id="eventEnddatumInput" name="ende" value="$ende" placeholder="$ph_ende">$fehler_ende

  <label>Addresse:</label>
  <label>Straße:</label>
  <input type="text" class="event" id="eventStrasseInput" name="strasse" value="$strasse" placeholder="$ph_strasse">$fehler_strasse
  <label>Nr.:</label>
  <input type="text" class="event" id="eventNrInput" name="nr" value="$nr" placeholder="$ph_nr">$fehler_nr
  <label>Adresszusatz:</label>
  <input type="text" class="event" id="eventAdresszusatzInput" name="adresszusatz" value="$adresszusatz" placeholder="$ph_adresszusatz">$fehler_adresszusatz
  <label>PLZ:</label>
  <input type="text" class="event" id="eventPLZInput" name="plz" value="$plz" placeholder="$ph_plz">$fehler_plz
  <label>Bezirk:</label>
  <!--<input type="text" class="event" id="eventOrtInput" name="ort" value="$ort" placeholder="$ph_ort">$fehler_ort-->
EOF;

//Bezirke abfragen, die in DB
$resultbezirke = db_select($tbl_bezirke, 'b')
  ->fields('b', array(
    'BID',
	'bezirksname',
  ))
  ->execute();
$countbezirke = $resultbezirke->rowCount();
//Dropdownliste zur Bezirkauswahl
$profileHTML .= '<select name="ort" size="'.$countbezirke.'" >';
foreach ($resultbezirke as $row) {
  $profileHTML .= '<option value="'.$row->BID.'">'.$row->bezirksname.'</option>';
}
$profileHTML .= '</select>';

$profileHTML .= <<<EOF
  <label>Geodaten:</label>
  <input type="text" class="event" id="eventGPSInput" name="gps" value="$gps" placeholder="$ph_gps">$fehler_gps
	

  <label>Website:</label>
  <input type="text" class="event" id="akteurURLInput" name="url" value="$url" placeholder="$ph_url">$fehler_url


  <label>Beschreibung:</label>
  <textarea name="kurzbeschreibung" class="event" cols="45" rows="3" placeholder="$ph_kurzbeschreibung">$kurzbeschreibung</textarea>$fehler_kurzbeschreibung
  <label>Bild:</label>
  <input type="file" class="event" id="akteurBildInput" name="bild" /><br>


  <input type="submit" class="event" id="akteureSubmit" name="submit" value="Speichern">
</form>
<a href="javascript:history.go(-1)">Abbrechen/Zurück</a>
EOF;

