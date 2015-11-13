<?php
/**
 * eventformular.php stellt ein Formular dar,
 * in welches alle Informationen über eine Veranstaltung
 * eingetragen werden können.
 * Pflichtfelder sind (bisher): Name, Veranstalter, Datum.
 * Anschließend werden die Daten gefiltert in die DB-Tabellen eingetragen
 * oder geupdated
 *
 * Ruth, 2015-07-20
 * Felix, 2015-09-02
 */

/**
 * FUNKTIONEN: ...
 * TODO: - Ersteller = "Privat" (ID = 0) miteinbringen
 *       - Im "Update/Bearbeiten"-Modus werden die Select-Felder nicht
 *         automatisch ausgewählt.
 */

Class eventformular extends aae_data_helper {

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
  var $all_sparten = ''; // Zur Darstellung des tokenizer (#akteurSpartenInput)

  //Variable zur Freigabe: muss true sein
  var $freigabe = true;
  var $fehler = array(); // In diesem Array werden alle Fehler gespeichert

  //Variablen, welche Texte in den Formularfeldern beschreiben ("placeholder"):
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

  var $user_id;
  var $event_id;
  var $resultakteure;
  var $resultbezirke;
  var $target = '';

  function __construct($action = false) {

    global $user;
    $this->user_id = $user->uid;

    // Sollen die Werte im Anschluss gespeichert oder geupdatet werden?
    if ($action == 'update') {
	   $this->target = 'update';
    }

    if (!user_is_logged_in()) {
	  drupal_access_denied();
    }
  } // END Constructor

  /**
   * Funktion, welche reihenweise POST-Werte auswertet, abspeichert bzw. ausgibt.
   * @returns $profileHTML;
   */
  public function run() {

    $path = current_path();
    $explodedpath = explode("/", $path);
    $this->event_id = $explodedpath[1];
    $output = '';

    if (isset($_POST['submit'])) {
      if ($this->eventCheckPost()) {
        if ($this->target == 'update') {
	      $this->eventUpdaten();
        } else {
	      $this->eventSpeichern();
        }
        $output = $this->eventDisplay();
      } else {
        $output = $this->eventDisplay();
      }
    } else {
      // Was passiert, wenn Seite zum ersten mal gezeigt wird?
      if ($this->target == 'update') {
	     $this->eventGetFields();
      }
      $output = $this->eventDisplay();
    }
    return $output;
  }

  /**
   * Wird ausgeführt, wenn auf "Speichern" gedrückt wird
   * @returns $this->freigabe [bool]
   */
  private function eventCheckPost() {
    //Wertezuweisung
    $this->name = $this->clearContent($_POST['name']);
    $this->veranstalter = $this->clearContent($_POST['veranstalter']);
    $this->start = $this->clearContent($_POST['start']);
    $this->url = $this->clearContent($_POST['url']);
    $this->ende = $this->clearContent($_POST['ende']);
    $this->zeit_von = $this->clearContent($_POST['zeit_von']);
    $this->zeit_bis = $this->clearContent($_POST['zeit_bis']);
    if (isset($_POST['bild'])) $this->bild = $_POST['bild']; // Extend!
    $this->kurzbeschreibung = $this->clearContent($_POST['kurzbeschreibung']);
    $this->strasse = $this->clearContent($_POST['strasse']);
    $this->nr = $this->clearContent($_POST['nr']);
    $this->adresszusatz = $this->clearContent($_POST['adresszusatz']);
    $this->plz = $this->clearContent($_POST['plz']);
    $this->ort = $this->clearContent($_POST['ort']);
    $this->gps = $this->clearContent($_POST['gps']);
    $this->sparten = $_POST['sparten'];

    //-------------------------------------

    //Check, ob ein Name eingegeben wurde:
    if (strlen($this->name) == 0) {
     $this->fehler['name'] = "Bitte einen Veranstaltungsnamen eingeben!";
	   $this->freigabe = false;
    }

    //Ckeck, ob Datum angegeben wurde
    if (strlen($this->start) == 0) {
      $this->fehler['start'] = "Bitte ein Startdatum angeben!";
      $this->freigabe = false;
    }

    //Check, ob Bezirk ausgewählt wurde
    if (strlen($this->ort) == 0) {
  	  $this->fehler['ort'] = "Bitte einen Bezirk auswählen!";
  	  $this->freigabe = false;
    }

    if (strlen($this->kurzbeschreibung) == 0) {
     $this->fehler['kurzbeschreibung'] = "Ein paar Beschreibungs-Zeilen werden Dir wohl einfallen...";
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

    // Um die bereits gewählten Tag's anzuzeigen benötigen wir deren Namen...
    if ($this->freigabe == false) {

     $neueSparten = array();

     foreach($this->sparten as $sparte) {

      $sparte = strtolower($this->clearContent($sparte));

      if (is_numeric($sparte)) {

      $spartenName = db_select($this->tbl_sparte, 's')
       ->fields('s', array('kategorie'))
       ->condition('KID', $sparte, '=')
       ->execute()
       ->fetchAll();

       $neueSparten[$sparte] = $spartenName[0]->kategorie;

     } else {

      $neueSparten[] = $sparte;

     }
    }
     $this->sparten = $neueSparten;
   }

    return $this->freigabe;
  } // END function eventCheckPost()

  /**
   * Wird ausgefuehrt, wenn Update der Daten verlangt ist
   */
  private function eventUpdaten() {

	//Abfrage, ob Adresse bereits in Adresstabelle

	$this->resultadresse = db_select($this->tbl_adresse, 'a')
	  ->fields('a', array( 'ADID', 'gps' ))
	  ->condition('strasse', $this->strasse, '=')
	  ->condition('nr', $this->nr, '=')
	  ->condition('adresszusatz', $this->adresszusatz, '=')
	  ->condition('plz', $this->plz, '=')
	  ->condition('bezirk', $this->ort, '=')
	  ->execute();

    $i = $this->resultadresse->rowCount();
    if ($i == 0) {
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
      foreach ($this->resultadresse as $row) {
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
	    $this->adresse = $row->ADID; //Adress-ID merken
	  }
	}

	//Zeitformatierung
	if (strlen($this->ende) == 0) {
      $this->ende = $this->start;
	} else {
	  $this->ende = $this->ende . ' ' . $this->zeit_bis;
	}
	$this->start = $this->start . ' ' . $this->zeit_von;

	$eventupdate = db_update($this->tbl_event)
   	->fields(array(
		'name' => $this->name,
		'ort' => $this->adresse,
		'start' => $this->start,
		'url' => $this->url,
		'ende' => $this->ende,
		'bild' => $this->bild,
		'kurzbeschreibung' => $this->kurzbeschreibung,
	  ))
	  ->condition('EID', $this->event_id, '=')
	  ->execute();

	$akteureventupdate = db_update($this->tbl_akteur_events)
   	->fields(array(
		'AID' => $this->veranstalter
	  ))
	  ->condition('EID', $this->event_id, '=')
	  ->execute();

    // Update Tags

    if (is_array($this->sparten) && $this->sparten != "") {

     foreach ($this->sparten as $id => $sparte) {
     // Tag bereits in DB?

     $sparte_id = '';
     $sparte = strtolower($this->clearContent($sparte));

     $resultsparte = db_select($this->tbl_sparte, 's')
      ->fields('s')
      ->condition('KID', $sparte, '=')
      ->execute();

      if ($resultsparte->rowCount() == 0) {
        // Tag in DB einfügen
       $sparte_id = db_insert($this->tbl_sparte)
        ->fields(array('kategorie' => $sparte))
        ->execute();

       } else {

         foreach ($resultsparte as $row) {
           $sparte_id = $row->KID;
         }

       }

       // Hat das Event dieses Tag bereits zugeteilt bekommen?

       $hatEventSparte = db_select($this->tbl_event_sparte, 'es')
        ->fields('es')
        ->condition('hat_EID', $this->event_id, '=')
        ->condition('hat_KID', $sparte_id, '=')
        ->execute();

        if ($hatEventSparte->rowCount() == 0) {
         // Nein, daher rein damit

         db_insert($this->tbl_event_sparte)
          ->fields(array(
           'hat_EID' => $this->event_id,
           'hat_KID' => $sparte_id
          ))
          ->execute();

        }
       }
      }

    // Gebe auf der nächsten Seite eine Erfolgsmeldung aus:
    session_start();
    $_SESSION['sysmsg'][] = 'Das Event wurde erfolgreich bearbeitet!';
  	header("Location: Eventprofil/" . $this->event_id);
    // Event erstellt uuuund.... tschüss ;)

  } // END function eventUpdaten()

  /**
   * Wird aufgerufen, wenn "Akteur bearbeiten" ausgewählt wurde
   * Daten aus DB in Felder schreiben
   */
  private function eventGetFields() {

    //Auswahl der Daten des ausgewählten Events
    $resultevent = db_select($this->tbl_event, 'e')
      ->fields('e', array(
	    'name',
	    'start',
	    'ende',
	    'url',
	    'bild',
	    'kurzbeschreibung',
	    'ort',
	  ))
	  ->condition('EID', $this->event_id, '=')
      ->execute();

    $resultveranstalter = db_select($this->tbl_akteur_events, 'a')
     ->fields('a', array( 'AID' ))
	   ->condition('EID', $this->event_id, '=')
     ->execute();

    //Speichern der Daten in den Arbeitsvariablen
    foreach ($resultevent as $row) {
	  $this->name = $row->name;
	  $ths->ort = $row->ort;
	  $this->start = $row->start;
	  $this->ende = $row->ende;
	  $this->url = $row->url;
	  $this->bild = $row->bild;
	  $this->kurzbeschreibung = $row->kurzbeschreibung;
	}

    foreach ($resultveranstalter as $row) {
	   $this->veranstalter = $row->AID;
    }

    $akteur_id = $this->veranstalter;

    //Adressdaten aus DB holen:
    $this->resultadresse = db_select($this->tbl_adresse, 'd')
      ->fields('d', array(
	    'strasse',
	    'nr',
	    'adresszusatz',
	    'plz',
	    'bezirk',
	    'gps',
	  ))
	   ->condition('ADID', $this->ort, '=')
     ->execute();

    //Speichern der Adressdaten in den Arbeitsvariablen
    foreach ($this->resultadresse as $row) {
	   $this->strasse = $row->strasse;
	   $this->nr = $row->nr;
	   $this->adresszusatz = $row->adresszusatz;
	   $this->plz = $row->plz;
	   $this->ort = $row->bezirk;
	   $this->gps = $row->gps;
    }

    //Akteurnamen aus DB holen:
    $this->resultakteur = db_select($this->tbl_akteur, 'a')
     ->fields('a', array( 'name' ))
	   ->condition('AID', $this->veranstalter, '=')
     ->execute();

    //Speichern der Adressdaten in den Arbeitsvariablen
    foreach ($this->resultakteur as $row) {
	   $this->veranstalter = $row->name;
    }
    //Zeit auflösen
    $explodedstart = explode(' ', $this->start);
    $explodedende = explode(' ', $this->ende);
    $this->ende = $explodedende[0];
    $this->start = $explodedstart[0];

    if (count($explodedstart) == 2) {
	   $this->zeit_von = $explodedstart[1];
    }
    if (count($explodedende) == 2) {
	   $this->zeit_bis = $explodedende[1];
    }
  } // END function eventUpdaten()

  /**
   * Schreibt die Eventdaten in die DB
   */
  private function eventSpeichern() {

    //Wenn Bilddatei ausgewählt wurde...
    if ($_FILES) {
     $this->bild = $this->upload_image($_FILES['bild']['name'], $this->clearContent($_POST['oldPic']));
    }

	//Abfrage, ob Adresse bereits in Adresstabelle
	//Addressdaten aus DB holen:
	$this->resultadresse = db_select($this->tbl_adresse, 'a')
	  ->fields('a', array( 'ADID', 'gps' ))
	  ->condition('strasse', $this->strasse, '=')
	  ->condition('nr', $this->nr, '=')
	  ->condition('adresszusatz', $this->adresszusatz, '=')
	  ->condition('plz', $this->plz, '=')
	  ->condition('bezirk', $this->ort, '=')
	  ->execute();

    //wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen
    if ($this->resultadresse->rowCount() == 0) {
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
	  foreach ($this->resultadresse as $row) {
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
	if (strlen($this->ende) == 0) {
      $this->ende = $this->start . ' ' . $this->zeit_bis;
	} else {
	  $this->ende = $this->ende . ' ' . $this->zeit_bis;
	}
	$this->start = $this->start . ' ' . $this->zeit_von;

	$this->event_id = db_insert($this->tbl_event)
   	->fields(array(
		'name' => $this->name,
		'ort' => $this->adresse,
		'start' => $this->start,
		'url' => $this->url,
		'ende' => $this->ende,
		'bild' => $this->bild,
		'kurzbeschreibung' => $this->kurzbeschreibung,
		'ersteller' => $this->user_id
	  ))
	  ->execute();

	 // Falls Akteur angegeben wurde:
    if (isset($this->veranstalter)) {

	    $akteurevents = db_insert($this->tbl_akteur_events)
   	   ->fields(array(
		  'AID' => $this->veranstalter,
		  'EID' => $this->event_id,
	    ))
	    ->execute();
  	}

    if (is_array($this->sparten) && $this->sparten != "") {

     foreach ($this->sparten as $id => $sparte) {
 		 // Tag bereits in DB?

     $sparte = strtolower($this->clearContent($sparte));

     $sparte_id = '';

 		 $resultsparte = db_select($this->tbl_sparte, 's')
 		  ->fields('s', array( 'KID' ))
 		  ->condition('kategorie', $sparte, '=')
 		  ->execute();

  		if ($resultsparte->rowCount() == 0) {
      // Tag in DB einfügen
 		 $sparte_id = db_insert($this->tbl_sparte)
 		  ->fields(array('kategorie' => $sparte))
 		  ->execute();

 		} else {

 		  foreach ($resultsparte as $row) {
 		    $sparte_id = $row->KID;
 		  }

 		}

 		// Event & Tag in Tabelle $tbl_hat_sparte einfügen

 		$insertAkteurSparte = db_insert($this->tbl_event_sparte)
 		  ->fields(array(
 		    'hat_EID' => $this->event_id,
 		    'hat_KID' => $sparte_id,
 		  ))
 		  ->execute();
 	  }
 	 }

    // Gebe auf der nächsten Seite eine Erfolgsmeldung aus:
    session_start();
    $_SESSION['sysmsg'][] = 'Das Event wurde erfolgreich erstellt!';
	  header("Location: Eventprofil/" . $this->event_id);

  } // END function event_speichern()


  /**
   * Darstellung der Formularinformationen
   */

  private function eventDisplay() {
    // Ausgabe des Eventformulars
    
    if (array_intersect(array('administrator'), $user->roles)) {
      //alle Akteure abfragen, die in DB: nur Admin
      $this->resultakteure = db_select($this->tbl_akteur, 'a')
        ->fields('a', array( 'AID', 'name' ))
        ->execute();
    } else {
      //Akteure abfragen, die in DB und für welche User Schreibrechte hat
      $res = db_select($this->tbl_akteur, 'a');
      $res->join($this->tbl_hat_user, 'u', 'a.AID = u.hat_AID AND u.hat_UID = :uid', array(':uid' => $this->user_id));
      $res->fields('a', array('AID','name'));
      $this->resultakteure = $res->execute();
    } // GGF. ALLES HIER DRÜBER ANPASSEN

    $this->resultbezirke = db_select($this->tbl_bezirke, 'b')
      ->fields('b', array( 'BID', 'bezirksname' ))
      ->execute();
    $countbezirke = $this->resultbezirke->rowCount();

    $all_sparten = db_select($this->tbl_sparte, 's')
      ->fields('s')
      ->execute()
      ->fetchAll();

    foreach ($all_sparten as $id => $sparte) {
     $this->all_sparten[$id] = $sparte;
    }

    $pathThisFile = $_SERVER['REQUEST_URI'];
    ob_start(); // Aktiviert "Render"-modus
    include_once path_to_theme() . '/templates/eventformular.tpl.php';
    return ob_get_clean(); // Übergabe des gerenderten "eventformular.tpl.php"

  } // END function eventDisplay()
} // END class eventformular()
