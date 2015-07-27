<?php
/**
 * akteuredit.php stellt ein Formular dar,
 * durch welches die Informationen zu einem Akteur geändert werden können.
 * Einziges Pflichtfeld ist bisher der Name und die Emailadresse.
 * Anschließend werden die Daten in die DB-Tabellen eingetragen.
 *
 * Ruth, 2015-07-04
 */

//Eingeloggter User
global $user;
$user_id = $user->uid;

//AID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$akteur_id = $explodedpath[1];

//DB-Tabellen
$tbl_hat_Sparte = "aae_data_hat_sparte";
$tbl_adresse = "aae_data_adresse";
$tbl_akteur = "aae_data_akteur";
$tbl_kategorie = "aae_data_kategorie";
$tbl_hat_user = "aae_data_hat_user";
$tbl_bezirke = "aae_data_bezirke";

//Prüfen ob Schreibrecht vorliegt
$resultUser = db_select($tbl_hat_user, 'u')
  ->fields('u', array(
    'hat_UID',
    'hat_AID',
  ))
  ->condition('hat_AID', $akteur_id, '=')
  ->condition('hat_UID', $user_id, '=')
  ->execute();
$hat_recht = $resultUser->rowCount();

if(!array_intersect(array('redakteur','administrator'), $user->roles)){
  if($hat_recht != 1){
    drupal_access_denied();
  }
}

//-----------------------------------

//Variablen zum Speichern von Werten, welche in die DB-Tabellen eingefügt werden sollen
$name = "";
$adresse = "";//Address-ID: Addressinformationen muessen aus Addressdbtabelle geholt werden
$email = "";
$telefon = "";
$url = "";
$ansprechpartner = "";
$funktion = "";
$bild = "";
$beschreibung = "";
$oeffnungszeiten = "";

$strasse = "";
$nr = "";
$adresszusatz = "";
$plz = "";
$ort = "";
$gps = "";

$hatuser = "";
$explodedhatuser = "";

//$tbl_hat_Sparte
//$kategorie = array();

//Speicherort fuer Bilder
$bildpfad = "/home/swp15-aae/drupal/sites/default/files/styles/large/public/field/image/";
$short_bildpfad = "sites/default/files/styles/large/public/field/image/";

//-----------------------------------

//$akteur_id = "";
//Variable zur Kategoriebestimmung
//$sent = "";

//Variable zur Freigabe: muss true sein
$freigabe = true;

//Fehlervariablen
$fehler_name = "";
$fehler_email = "";
$fehler_telefon = "";
$fehler_url = "";
$fehler_ansprechpartner = "";
$fehler_funktion = "";
$fehler_bild = "";
$fehler_beschreibung = "";
$fehler_oeffnungszeiten = "";
$fehler_strasse = "";
$fehler_nr = "";
$fehler_adresszusatz = "";
$fehler_plz = "";
$fehler_ort = "";
$fehler_gps = "";
$fehler_hatuser = "";

//-----------------------------------

//Variablen, welche Texte in den Formularfeldern halten
//$tbl_akteur
$ph_name = "Name des Vereins/ der Organisation";
$ph_email = "E-mail Addresse";
$ph_telefon = "Telefonnummer";
$ph_url = "Website";
$ph_ansprechpartner = "Kontaktperson";
$ph_funktion = "Funktion der Kontaktperson";
$ph_beschreibung = "Beschreibung";
$ph_oeffnungszeiten = "Öffnungszeiten";

//$tbl_adresse
$ph_strasse = "Strasse";
$ph_nr = "Hausnummer";
$ph_adresszusatz = "Adresszusatz";
$ph_plz = "PLZ";
$ph_ort = "Bezirk";
$ph_gps = "GPS-Addresskoordinaten";

$ph_hatuser = "User ID";

//$tbl_hat_Sparte

//-----------------------------------

//das wird ausgeführt, wenn auf "Speichern" gedrückt wird
if (isset($_POST['submit'])) {

  $akteur_id = $_POST['akteur_id'];

  //Wertezuweisung
  $name = $_POST['name'];
  $email = $_POST['email'];
  $telefon = $_POST['telefon'];
  $url = $_POST['url'];
  $ansprechpartner = $_POST['ansprechpartner'];
  $funktion = $_POST['funktion'];
  if(isset($_POST['bild'])){
    $bild = $_POST['bild'];
  }
  $bild_alt=$_POST['bild_alt'];
  $beschreibung = $_POST['beschreibung'];
  $oeffnungszeiten = $_POST['oeffnungszeiten'];

  $strasse = $_POST['strasse'];
  $nr = $_POST['nr'];
  $adresszusatz = $_POST['adresszusatz'];
  $plz = $_POST['plz'];
  $ort = $_POST['ort'];
  $gps = $_POST['gps'];

  $hatuser = $_POST['hatuser'];
  $explodedhatuser=array();
  if($hatuser != ""){
	$explodedhatuser = explode(",", $hatuser);
  }

  //$kategorie = $_POST['kategorie'];

//-------------------------------------

  //Check-Klauseln

  //Check, ob ein Name eingegeben wurde:
  if(strlen($name) == 0){
    //Feld nicht ausgefüllt
	$fehler_name = "Bitte einen Organisationsnamen eingeben!";
	$freigabe = false;
  }
  //Ckeck, ob Email angegeben wurde
  if(strlen($email) == 0){
	//Feld nicht ausgefüllt
    $fehler_email = "Bitte eine Emailadresse eingeben!";	$freigabe = false;
  }

  //überflüssige Leerzeichen am Anfang entfernen
  $name=trim($name);
  $email = trim($email);
  $telefon = trim($telefon);
  $url = trim($url);
  $ansprechpartner = trim($ansprechpartner);
  $funktion = trim($funktion);
  $bild = trim($bild);
  $beschreibung = trim($beschreibung);
  $oeffnungszeiten = trim($oeffnungszeiten);
  $strasse = trim($strasse);
  $nr = trim($nr);
  $adresszusatz = trim($adresszusatz);
  $plz = trim($plz);
  $ort = trim($ort);
  $gps = trim($gps);
  $laengehatuser = count($explodedhatuser);
  if($laengehatuser > 0){
	$i = 0;
	while($i < $laengehatuser){
	  $explodedhatuser[$i] = trim($explodedhatuser[$i]);
	  $explodedhatuser[$i] = strip_tags($explodedhatuser[$i]);
	  //prüfen, ob es User ID in der DB gibt:
	  $resultuserid = db_select("users", 'u')
	    ->fields('u', array(
	      'uid',
	    ))
	    ->condition('uid', $explodedhatuser[$i], '=')
	    ->execute();
	  $anzresultuserid = $resultuserid->rowCount();
	  if($anzresultuserid == 0){
		$fehler_hatuser = $explodedhatuser[$i] + " ist keine gültige UserID.";
		$freigabe = false;
		$i = $laengehatuser;
	  }else{
	    $i = $i + 1;
	  }
	}
  }

  //und alle Tags entfernen (Hacker)
  $name=strip_tags($name);
  $email = strip_tags($email);
  $telefon = strip_tags($telefon);
  $url = strip_tags($url);
  $ansprechpartner = strip_tags($ansprechpartner);
  $funktion = strip_tags($funktion);
  $bild = strip_tags($bild);
  $beschreibung = strip_tags($beschreibung);
  $oeffnungszeiten = strip_tags($oeffnungszeiten);
  $strasse = strip_tags($strasse);
  $nr = strip_tags($nr);
  $adresszusatz = strip_tags($adresszusatz);
  $plz = strip_tags($plz);
  $ort = strip_tags($ort);
  $gps = strip_tags($gps);
  $hatuser = strip_tags($hatuser);

  //Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.
  if (strlen($name) > 100){
    $fehler_name = "Bitte geben Sie einen kuerzeren Namen an oder verwenden Sie ein Kuerzel.";
	$freigabe = false;
  }
  if (strlen($email) > 100){
	$fehler_email = "Bitte geben Sie eine kuerzere Emailadresse an.";
	$freigabe = false;
  }
  if (strlen($telefon) > 100){
	$fehler_telefon = "Bitte geben Sie eine kuerzere Telefonnummer an.";
	$freigabe = false;
  }
  if (strlen($url) > 100){
	$fehler_url = "Bitte geben Sie eine kuerzere URL an.";
	$freigabe = false;
  }
  if (strlen($ansprechpartner) > 100){
	$fehler_ansprechpartner = "Bitte geben Sie einen kuerzeren Ansprechpartner an.";
	$freigabe = false;
  }
  if (strlen($funktion) > 100){
	$fehler_funktion = "Bitte geben Sie eine kuerzere Funktion an.";
	$freigabe = false;
  }
  if (strlen($beschreibung) > 500){
	$fehler_beschreibung = "Bitte geben Sie eine kuerzere Beschreibung an.";
	$freigabe = false;
  }
  if (strlen($oeffnungszeiten) > 200){
	$fehler_oeffnungszeiten = "Bitte geben Sie kuerzere Oeffnungszeiten an.";
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
  if (strlen($gps) > 100){
	$fehler_gps = "Bitte geben Sie kuerzere GPS-Daten an.";
	$freigabe = false;
  }

  //Wenn Bilddatei ausgewählt wurde...
  //Wenn Bilddatei ausgewählt wurde...
  if($_FILES){
  $bildname = $_FILES['bild']['name'];

  if($bildname != ""){
    if (!move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname)) {
      echo 'Error: Konnte Bild nicht hochladen. Bitte informieren Sie den Administrator. Bildname: <br />'.$bildname;
      exit();
    }
    $bild = base_path().$short_bildpfad.$bildname;
	}
  } else{
		$bild=$bild_alt;
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
	}else {
	  foreach ($resultadresse as $row) {
		$adresse = $row->ADID;
	  }
	}

	//tbl_akteur UPDATE!!!
	$akteur_updated = db_update($tbl_akteur)
      ->fields(array(
		'name' => $name,
		'adresse' => $adresse,
		'email' => $email,
		'telefon' => $telefon,
		'url' => $url,
		'ansprechpartner' => $ansprechpartner,
		'funktion' => $funktion,
		'bild' => $bild,
		'beschreibung' => $beschreibung,
		'oeffnungszeiten' => $oeffnungszeiten,
	  ))
	  ->condition('AID', $akteur_id, '=')
	  ->execute();

	//tbl_hat_user
	if ($laengehatuser != 0){
	  $j = 0;
	  while($j < $laengehatuser){
		$inserthatuser = db_insert($tbl_hat_user)
		  ->fields(array(
		    'hat_UID' => $explodedhatuser[$j],
			'hat_AID' => $akteur_id,
		  ))
		  ->execute();
	    $j = $j + 1;
	  }
	}

	//Kategorien
	/*
	foreach ($kategorie as $row) {
	  $result_kategorie_id = db_select($tbl_kategorie)//ID der ausgewählten Kategorie holen
		->fields(array(
		  'kategorie_id'
		))
		->condition('kategorie', $row, '=')
		->execute();
	  foreach ($result_kategorie_id as $row1) {
		//Abfragen, ob Kategorie bereits zu dem Akteur vorhanden ist:
		$result_kategorie_akteur = db_select($tbl_hat_Sparte)
		  ->fields(array(
			'akteur',
			'kategorie',
		  ))
		  ->condition('akteur', $akteur_id, '=')
		  ->condition('kategorie', $row1->kategorie_id)
		  ->execute();
		$count1 = $result_kategorie_akteur->rowCount();
		if($count1 == 0){//Kategorie noch nicht zugeordnet
		  //tbl_hat_Sparte INSERT!!!
		  $hat_sparte_insert = db_insert($tbl_hat_Sparte)
		 	->fields(array(
		  	  'kategorie' => $row1->kategorie_id,
			))
		    ->condition('akteur_id', $akteur_id, '=')
		    ->execute();
		}
	  }
	}
	//Unchecked Kategorien löschen:
	//Alle markierten Kategorien holen:
	$result_alle_kategorien = db_select($tbl_hat_Sparte)
	  ->fields(array(
		'kategorie',
	  ))
	  ->condition('akteur', $akteur_id, '=')
	  ->execute();
	foreach ($result_alle_kategorien as $row) {
	  $zaehler = 0;
	  foreach ($kategorie as $row1) {
		if($row->kategorie == $row1){//Kategorie markiert
		  $zaehler = $zaehler + 1;
		}
	  }
	  if(zaehler == 0){//Kategorie wurde unchecked
		$kat_deleted = db_delete($tbl_hat_Sparte)//Aus Tabelle loeschen!
		  ->condition('akteur', $akteur_id, '=')
		  ->condition('kategorie', $row->kategorie, '=')
		  ->execute();
	  }
	}
	*/

	header("Location: ?q=Akteurprofil/".$akteur_id);
  // Leite nach Abschluss auf Profil weiter
  }

} else{
  //Erstmaliger Aufruf: Daten aus DB in Felder schreiben
  require_once $modulePath . '/database/db_connect.php';
  $db = new DB_CONNECT();

  //Auswahl der Daten des eingeloggten Akteurs:
  $resultakteur = db_select($tbl_akteur, 'c')
    ->fields('c', array(
	  'name',
	  'adresse',
	  'email',
	  'telefon',
	  'url',
	  'ansprechpartner',
	  'funktion',
	  'bild',
	  'beschreibung',
	  'oeffnungszeiten',
	))
	->condition('AID', $akteur_id, '=')
    ->execute();
  //Speichern der Daten in den Arbeitsvariablen
  foreach($resultakteur as $row){
	$name = $row->name;
	$adresse = $row->adresse;//Address-ID: Addressinformationen muessen aus Addressdbtabelle geholt werden
	$email = $row->email;
	$telefon = $row->telefon;
	$url = $row->url;
	$ansprechpartner = $row->ansprechpartner;
	$funktion = $row->funktion;
	$bild = $row->bild;
	$beschreibung = $row->beschreibung;
	$oeffnungszeiten = $row->oeffnungszeiten;
  }
  if($bild == ""){
    $bild = "images/xxx.png";
  }

  //Adressdaten aus DB holen:
  $resultadresse = db_select($tbl_adresse, 'd')
    ->fields('d', array(
	  'strasse',
	  'nr',
	  'adresszusatz',
	  'plz',
	  'bezirk',
	  'gps',
	))
	->condition('ADID', $adresse, '=')
    ->execute();
  //Speichern der Adressdaten in den Arbeitsvariablen
  foreach ($resultadresse as $row) {
	$strasse = $row->strasse;
	$nr = $row->nr;
	$adresszusatz = $row->adresszusatz;
	$plz = $row->plz;
	$ort = $row->bezirk;
	$gps = $row->gps;
  }
  /*
  //alle Kategorien holen
  $resultkategorie = db_select($tbl_kategorie, 'f')
    ->fields('f', array(
	  'kategorie_id',
	  'kategorie',
	))
    ->execute();
  */
}

$pathThisFile = $_SERVER['REQUEST_URI'];

//Darstellung
$profileHTML = <<<EOF
<form action='$pathThisFile' method='POST' enctype='multipart/form-data'>
  <input name="akteur_id" type="hidden" id="akteurAIDInput" value="$akteur_id" />
  <!-- verstecktes Feld für bild -->
  <input name="bild_alt" type="hidden" id="bild_alt" value="$bild" />

  <label>Name:</label>
  <input type="text" class="akteur" id="akteurNameInput" name="name" value="$name" placeholder="$ph_name" required>$fehler_name

  <label>Addresse:</label>
  <label>Straße:</label>
  <input type="text" class="akteur" id="akteurStrasseInput" name="strasse" value="$strasse" placeholder="$ph_strasse">$fehler_strasse
  <label>Nr.:</label>
  <input type="text" class="akteur" id="akteurNrInput" name="nr" value="$nr" placeholder="$ph_nr">$fehler_nr
  <label>Adresszusatz:</label>
  <input type="text" class="akteur" id="akteurAdresszusatzInput" name="adresszusatz" value="$adresszusatz" placeholder="$ph_adresszusatz">$fehler_adresszusatz
  <label>PLZ:</label>
  <input type="text" class="akteur" id="akteurPLZInput" name="plz" value="$plz" placeholder="$ph_plz">$fehler_plz
  <label>Bezirk:</label>
  <!--<input type="text" class="akteur" id="akteurOrtInput" name="ort" value="$ort" placeholder="$ph_ort">$fehler_ort-->

EOF;

//Bezirke abfragen, die in DB
$resultbezirke = db_select($tbl_bezirke, 'b')
  ->fields('b', array(
    'BID',
	'bezirksname',
  ))
  ->execute();
$countbezirke = $resultbezirke->rowCount();
//Dropdownliste zur Akteurauswahl
$profileHTML .= '<select name="ort" size="'.$countbezirke.'" >';
foreach ($resultbezirke as $row) {
  if($row->BID == $ort){
	$profileHTML .= '<option value="'.$row->BID.'" selected="selected" >'.$row->bezirksname.'</option>';
  }else{
	$profileHTML .= '<option value="'.$row->BID.'">'.$row->bezirksname.'</option>';
  }
}
$profileHTML .= '</select>';

$profileHTML .= <<<EOF
  <label>Geodaten:</label>
  <input type="text" class="akteur" id="akteurGPSInput" name="gps" value="$gps" placeholder="$ph_gps">$fehler_gps
  <label>Öffnungszeiten:</label>
  <input type="text" class="akteur" id="akteurOeffnungszeitenInput" name="oeffnungszeiten" value="$oeffnungszeiten" placeholder="$ph_oeffnungszeiten">$fehler_oeffnungszeiten

  <label>Kontakt:</label>
  <label>Ansprechpartner:</label>
  <input type="text" class="akteur" id="akteurAnsprechpartnerInput" name="ansprechpartner" value="$ansprechpartner" placeholder="$ph_ansprechpartner">$fehler_ansprechpartner
  <label>Rolle des Ansprechpartners:</label>
  <input type="text" class="akteur" id="akteurFunktionInput" name="funktion" value="$funktion" placeholder="$ph_funktion">$fehler_funktion
  <label>Telefonnummer:</label>
  <input type="text" class="akteur" id="akteurTelefonInput" name="telefon" value="$telefon" placeholder="$ph_telefon">$fehler_telefon
  <label>Website:</label>
  <input type="text" class="akteur" id="akteurURLInput" name="url" value="$url" placeholder="$ph_url">$fehler_url
  <label>Emailaddresse:</label>
  <input type="email" class="akteur" id="akteurEmailInput" name ="email" value="$email" placeholder="$ph_email"><br>$fehler_email

  <label>Beschreibung:</label>
  <textarea name="beschreibung" class="akteur" cols="45" rows="3" placeholder="ph_beschreibung">$beschreibung</textarea>$fehler_beschreibung
  <label>Bild:</label><input type="file" class="akteur" id="akteurBildInput" name="bild" value="$bild" /><br>
  <img src="sites/all/modules/aae_data/$bild" width=200 ><br>
  <label>Neue Schreibrechte vergeben an:</label>
  <input type="test" class="akteur" id="akteurHatUser" name="hatuser" value="$hatuser" placeholder="$ph_hatuser">$fehler_hatuser
  <!--<label>Sparten:</label>
  //Tags
  <input type="hidden" name="sent" value="yes">-->
  <br>
  <input type="submit" class="akteure" id="akteureSubmit" name="submit" value="Übernehmen">
</form>
EOF;

/*
//Kategoriehandling
foreach($resultkategorie as $row){
	//Abfrage, ob Kategorie markiert ist (fuer den AKteur gespeichert ist)
	$resulthatsparte = db_select($tbl_hat_Sparte, 'g')
	    ->fields('g', array(
			'kategorie',
		))
		->condition('akteur', $akteur_id, '=')
		->condition('kategorie', $row->kategorie_id, '=')
	    ->execute();
	//$count holt Anzahl des Ergebnisses, um zu prüfen, ob Kategorie dem Akteur zugeordnet ist
	$count = $resulthatsparte->rowCount();
	if($count == 1){//Kategorie ist markiert
		$profileHTML .= '<input type="checkbox" class="akteur" name="kategorie[]" value="$row->kategorie" checked>'.$row->kategorie.'</p>';//CHECKED!!!
	}else{//Kategorie ist nicht markiert
		$profileHTML .= '<input type="checkbox" class="akteur" name="kategorie[]" value="$row->kategorie">'.$row->kategorie.'</p>';//UNCHECKED!!!
	}
}
$profileHTML .= '<input type="submit" class="akteure" id="akteureSubmit" name="submit" value="Übernehmen">';
$profileHTML .= '</form>';
*/
