<?php
/**
 * akteure.php stellt ein Formular dar,
 * in welches alle Informationen über einen Akteur
 * eingetragen werden können.
 * Einziges Pflichtfeld ist bisher der Name (orgname).
 * Anschließend werden die Daten in die DB-Tabellen eingetragen
 *
 * Ruth, 2015-06-25
 */

//DB-Tabellen
$tbl_vcardadd = "aae_data_address";
$tbl_vcardorg = "aae_data_organization";
$tbl_profil = "aae_data_profil";
$tbl_akteur = "aae_data_akteur";

//-----------------------------------

//Variablen zum Speichern von Werten, welche in die DB-Tabellen eingefügt werden sollen
//$tbl_vcardadd: VCardAddress
$street = "";
$postalcode = "";
$locality = "";
$geo = "";
//$tbl_vcardorg: VCardOrganization
$orgname = ""; //entspricht labelprofil, labelakteur
$telephone = "";
$web = "";
//$tbl_profil: Profil
$labelprofil = "";
$shortinfo = "";
//$tbl_akteur: Akteur
$labelakteur = "";
$contact = "";
$role = "";
//noch nicht in DB: (TODO: Schema muss dringend und möglichst endgültig überarbeitet werden!!!)
$mail = "";

//-----------------------------------

//Variable zur Freigabe: muss true sein
$goodtogo = true;
//Fehlervariablen
$fail_orgname = "";

//Variablen, welche Texte in den Formularfeldern halten
$ph_orgname = "Name";
$ph_street = "Strasse";
$ph_postalcode = "PLZ";
$ph_locality = "Stadt";
$ph_geo = "8934,983";
$ph_telephone = "0123456789";
$ph_web = "web.de";
$ph_shortinfo = "Kurzbeschreibung";
$ph_contact = "Kontaktperson";
$ph_role = "Rolle";
$ph_mail = "Mail";

//-----------------------------------

//das wird ausgeführt, wenn das auf "Speichern" gedrückt wird
if (isset($_POST['submit'])) {
	
	//Wertezuweisung
	$orgname = $_POST['orgname'];

    $street = $_POST['street'];
    $postalcode = $_POST['postalcode'];
	$locality = $_POST['locality'];
	$geo = $_POST['geo'];
	
	$contact = $_POST['contact'];
	$role = $_POST['role'];
	$telephone = $_POST['telephone'];
	$web = $_POST['web'];
	$mail = $_POST['mail'];
	
	$shortinfo = $_POST['shortinfo'];
	
	//Check, ob ein Name eingegeben wurde:
	if(strlen($orgname) == 0){
		//Feld nicht ausgefüllt
		$fail_orgname = "Bitte einen Organisationsnamen eingeben!";
		$goodtogo = false;
	}
	
	//Wenn $goodtogo true, ab in die DB mit den Daten
	if($goodtogo == true){
		require_once $modulePath . '/database/db_connect.php';
		//include $modulePath . '/templates/utils/rest_helper.php'; Ist aus dem Künstlermodul übernommen
		//Betroffene Tabellen der Datenbank
		$db = new DB_CONNECT();
		//Das Ergebnis von db_insert()->...->execute(); ist die ID, von diesem Eintrag
		//$tbl_vcardadd
		$vaid = db_insert($tbl_vcardadd)
			 	->fields(array(
					'streetaddress' => $street,
					'postalcode' => $postalcode,
					'locality' => $locality,
					'hasGeo' => $geo,
				))
				->execute();
		
		//tbl_vcardorg
		$oid = db_insert($tbl_vcardorg)
			 	->fields(array(
					'hasAddress' => $vaid,
					'organizationname' => $orgname,
					'hasTelephone' => $telephone,
					'hasURL' => $web,
				))
				->execute();
				
		//tbl_profil
		$pid = db_insert($tbl_profil)
			 	->fields(array(
					'label' => $orgname,
					'hatKurzbeschreibung' => $shortinfo,
				))
				->execute();
				
		//tbl_akteur
		$aid = db_insert($tbl_akteur)
			 	->fields(array(
					'hatAkteurVCard' => $oid,
					'hatAkteurProfil' => $pid,
					'label' => $orgname,
					'hatAnsprechpartner' => $contact,
					'hatRolle' => $role,
				))
				->execute();
		//header("Location: displayAkteure.php"); Hier muss hin, welche Seite aufgerufen werden soll,
		//nach dem die Daten erfolgreich gespeichert wurden.
	}
	
} else{
	//Erstmaliger Aufruf: nichts tun!
}

$pathThisFile = $_SERVER['REQUEST_URI']; 

//Darstellung
$profileHTML = <<<EOF
<form action='$pathThisFile' method='POST'>
    <label>Name:</label>
    <input type="text" class="akteur" id="akteurNameInput" name="orgname" value="$orgname" placeholder="$ph_orgname" required>$fail_orgname

    <label>Addresse:</label>
	<label>Straße:</label>
	<input type="text" class="akteur" id="akteurStreetInput" name="street" value="$street" placeholder="$ph_street">
	<label>PLZ:</label>
	<input type="text" class="akteur" id="akteurPostalcodeInput" name="postalcode" value="$postalcode" placeholder="$ph_postalcode">
	<label>Stadt:</label>
	<input type="text" class="akteur" id="akteurLocalityInput" name="locality" value="$locality" placeholder="$ph_locality">
	<label>Geodaten:</label>
	<input type="text" class="akteur" id="akteurGeoInput" name="geo" value="$geo" placeholder="$ph_geo">
	
	<label>Kontakt:</label>
	<label>Ansprechpartner:</label>
	<input type="text" class="akteur" id="akteurContactInput" name="contact" value="$contact" placeholder="$ph_contact">
	<label>Rolle des Ansprechpartners:</label>
	<input type="text" class="akteur" id="akteurRoleInput" name="role" value="$role" placeholder="$ph_role">
	<label>Telefonnummer:</label>
	<input type="text" class="akteur" id="akteurTelephoneInput" name="telephone" value="$telephone" placeholder="$ph_telephone">
	<label>Website:</label>
	<input type="text" class="akteur" id="akteurURLInput" name="web" value="$web" placeholder="$ph_web">
	<label>Emailaddresse:</label>
    <input type="email" class="akteur" id="akteurMailInput" name ="mail" value="$mail" placeholder="$ph_mail"><br>

	<label>Beschreibung:</label>
	<input type="text" class="akteur" id="akteurShortinfoInput" name="shortinfo" value="$shortinfo" placeholder="$ph_shortinfo">

    <input type="submit" class="akteure" id="akteureSubmit" name="submit" value="Speichern">
</form>
EOF;

