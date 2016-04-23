<?php
/**
 * @file eventformular.php
 * Stellt ein Formular dar, in welches alle Informationen über eine Veranstaltung
 * eingetragen werden können.
 * Pflichtfelder sind (bisher): Name, Veranstalter, Datum (Anfang) & Beschreibung
 * Anschließend werden die Daten gefiltert in die DB-Tabellen eingetragen
 * oder geupdatet.
 */

Class eventformular extends aae_data_helper {

  //$tbl_event
  var $name = "";
  var $veranstalter = "";
  var $start = "";
  var $ende = "";
  var $zeit_von = "";
  var $zeit_bis = "";
  var $hat_zeit_von = true;
  var $hat_zeit_bis = true;
  var $eventRecurres;
  var $bild = "";
  var $kurzbeschreibung = "";
  var $url = "";
  var $created = "";
  var $modified = "";

  //$tbl_adresse
  var $strasse = "";
  var $nr = "";
  var $adresszusatz = "";
  var $plz = "";
  var $ort = "";
  var $gps = "";
  var $adresse = "";

  //$tbl_sparte
  var $sparten = "";
  var $all_sparten = ''; // Zur Darstellung des tokenizer (#akteurSpartenInput)

  var $freigabe = true;   //Variable zur Freigabe: muss true sein
  var $fehler = array(); // In diesem Array werden alle Fehler gespeichert

  var $user_id;
  var $event_id;
  var $resultakteure;
  var $resultbezirke;
  var $target = '';
  var $removedTags;
  var $removedPic;

  function __construct($action = false) {

   global $user;
   $this->user_id = $user->uid;

   // Sollen die Werte im Anschluss gespeichert oder geupdatet werden?
   if ($action == 'update')
	  $this->target = 'update';

   if (!user_is_logged_in())
	  drupal_access_denied();

  }

  /**
   * Funktion, welche reihenweise POST-Werte auswertet, abspeichert bzw. ausgibt.
   * @return $profileHTML;
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
   * @returns $this->freigabe
   */
  private function eventCheckPost() {

    $this->name = $this->clearContent($_POST['name']);
    $this->veranstalter = $this->clearContent($_POST['veranstalter']);
    $this->start = $this->clearContent($_POST['start']);
    $this->url = $this->clearContent($_POST['url']);
    $this->ende = $this->clearContent($_POST['ende']);
    $this->zeit_von = $this->clearContent($_POST['zeit_von']);
    $this->zeit_bis = $this->clearContent($_POST['zeit_bis']);
    if (isset($_POST['bild'])) $this->bild = $_POST['bild'];
    $this->kurzbeschreibung = $this->clearContent($_POST['kurzbeschreibung']);
    $this->strasse = $this->clearContent($_POST['strasse']);
    $this->nr = $this->clearContent($_POST['nr']);
    $this->adresszusatz = $this->clearContent($_POST['adresszusatz']);
    $this->plz = $this->clearContent($_POST['plz']);
    $this->ort = $this->clearContent($_POST['ort']);
    $this->gps = $this->clearContent($_POST['gps']);
    $this->sparten = $_POST['sparten'];
    $this->removedTags = $this->clearContent($_POST['removedTags']);
    $this->removedPic = $this->clearContent($_POST['removeCurrentPic']);
    $this->eventRecurres = $this->clearContent($_POST['eventRecurres']);
    $this->eventRecurringType = $this->clearContent($_POST['eventRecurringType']);

    //-------------------------------------

    if (empty($this->name)) {
     $this->fehler['name'] = t("Bitte einen Veranstaltungsnamen eingeben!");
	   $this->freigabe = false;
    }

    if (strlen($this->start) != 0 && DateTime::createFromFormat('Y-m-d', $this->start) == false) {
      $this->fehler['start'] = t("Bitte ein (gültiges) Startdatum angeben!");
      $this->freigabe = false;
    }

    if (strlen($this->ende) != 0 && DateTime::createFromFormat('Y-m-d', $this->ende) == false) {
      $this->fehler['ende'] = t("Bitte ein (gültiges) Enddatum angeben!");
      $this->freigabe = false;
    }

    if (empty($this->ort)) {
  	  $this->fehler['ort'] = t("Bitte einen Bezirk auswählen!");
  	  $this->freigabe = false;
    }

    if (empty($this->kurzbeschreibung)) {
     $this->fehler['kurzbeschreibung'] = t("Ein paar Beschreibungs-Zeilen werden Dir einfallen...");
     $this->freigabe = false;
    }

    if (!empty($this->zeit_von) && DateTime::createFromFormat('H:i', $this->zeit_von) == false) {
     $this->fehler['zeit_von'] = t("Bitte eine (gültige) Start-Uhrzeit angeben!");
     $this->freigabe = false;
    }

    if (!empty($this->zeit_bis) && DateTime::createFromFormat('H:i', $this->zeit_bis) == false) {
     $this->fehler['zeit_bis'] = t("Bitte eine (gültige) End-Uhrzeit angeben!");
     $this->freigabe = false;
    }

    if (!empty($this->url) && preg_match('/\A(http:\/\/|https:\/\/)(\w*[.|-]\w*)*\w+\.[a-z]{2,3}(\/.*)*\z/',$this->url)==0) {
     $this->fehler['url'] = t("Bitte eine gültige URL zur Eventwebseite anngeben! (z.B. <i>http://meinevent.de</i>)");
     $this->freigabe = false;
    }

    /*if ((count($this->sparten)/2) > 11) {
     $this->fehler['sparten'] = "Bitte max. 10 Tags angeben.";
     $this->freigabe = false;
   } */

    if (strlen($this->name) > 64) {
	   $this->fehler['name'] = t("Bitte geben Sie einen kürzeren Namen an oder verwenden Sie ein Kürzel.");
     $this->freigabe = false;
    }

    if (strlen($this->url) > 200) {
	   $this->fehler['url'] = t("Bitte geben Sie eine kürzere URL an.");
	   $this->freigabe = false;
    }

    if (strlen($this->kurzbeschreibung) > 65000) {
     $this->fehler['kurzbeschreibung'] = t("Bitte geben Sie eine kürzere Beschreibung an.");
	   $this->freigabe = false;
    }

    if (strlen($this->strasse) > 64) {
	   $this->fehler['strasse'] = t("Bitte geben Sie einen kürzeren Strassennamen an.");
	   $this->freigabe = false;
    }

    if (strlen($this->nr) > 8) {
	   $this->fehler['nr'] = t("Bitte geben Sie eine kürzere Nummer an.");
	   $this->freigabe = false;
    }

    if (strlen($this->adresszusatz) > 100) {
	   $this->fehler['adresszusatz'] = t("Bitte geben Sie einen kürzeren Adresszusatz an.");
     $this->freigabe = false;
    }

    if (strlen($this->plz) > 8) {
	   $this->fehler['plz'] = t("Bitte geben Sie eine kürzere PLZ an.");
     $this->freigabe = false;
    }

    if (strlen($this->ort) > 100) {
     $this->fehler['ort'] = t("Bitte geben Sie einen kürzeren Ortsnamen an.");
	   $this->freigabe = false;
    }

    if (strlen($this->gps) > 100) {
     $this->fehler['gps'] = t("Bitte geben Sie kürzere GPS-Daten an.");
	   $this->freigabe = false;
    }

    if ($this->gps == t('Ermittle Geo-Koordinaten...')) $this->gps = '';

    /*if (!empty($this->gps) && preg_match('\s.\s,\s.\s',$this->gps)==0) {
      echo ':/';
      exit(); } */

    // Um die bereits gewählten Tag's anzuzeigen benötigen wir deren Namen...
    if ($this->freigabe == false) {

     $neueSparten = array();
     $this->sparten = array_unique($this->sparten);

     foreach($this->sparten as $sparte) {

      $sparte = strtolower($this->clearContent($sparte));

      if (is_numeric($sparte)) {

      $spartenName = db_select($this->tbl_sparte, 's')
       ->fields('s', array('kategorie'))
       ->condition('KID', $sparte, '=')
       ->execute()
       ->fetchObject();

      $neueSparten[$sparte] = $spartenName->kategorie;

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
   * TODO Veranstalter wechselbar machen
   */
  private function eventUpdaten() {

	 // Abfrage, ob Adresse bereits in Adresstabelle
	 $this->resultAdresse = db_select($this->tbl_adresse, 'a')
	  ->fields('a', array('ADID', 'gps_lat', 'gps_long'))
	  ->condition('strasse', $this->strasse, '=')
	  ->condition('nr', $this->nr, '=')
	  ->condition('adresszusatz', $this->adresszusatz, '=')
	  ->condition('plz', $this->plz, '=')
	  ->condition('bezirk', $this->ort, '=')
	  ->execute();

  /*  TODO Felix: Überarbeite Funktion zum Adresspeichern
     $resultEvent = db_select($this->tbl_event, 'e')
     ->fields('ort') = ADID
     ->condition('EID', $this->event_id)
     ->execute(); */

    $i = $this->resultAdresse->rowCount();

    if ($i == 0) {
     // Adresse nicht vorhanden
     $gps = explode(',', $this->gps, 2);

	   $this->adresse = db_insert($this->tbl_adresse)
	    ->fields(array(
		   'strasse' => $this->strasse,
		   'nr' => $this->nr,
		   'adresszusatz' => $this->adresszusatz,
		   'plz' => $this->plz,
		   'bezirk' => $this->ort,
		   'gps_lat' => $gps[0],
       'gps_long' => $gps[1]
		  ))
		  ->execute();

    } else {
      // Adresse bereits vorhanden
      foreach ($this->resultAdresse as $row) {
	    // Abfrage, ob GPS-Angaben gemacht wurden
        if (strlen($this->gps) != 0 && strlen($row->gps) == 0 ) {
        //ja UND es sind bisher keine GPS-Daten zu der Adresse in der DB
        $gps = explode(',', $this->gps, 2);

	      $adresse_updated = db_update($this->tbl_adresse)
	 	     ->fields(array(
          'gps_lat' => $gps[0],
          'gps_long' => $gps[1]
         ))
	       ->condition('ADID', $row->ADID, '=')
	       ->execute();
	    }
	    $this->adresse = $row->ADID;
	  }
	}

  if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
   $this->bild = $this->upload_image($_FILES['bild']);
  } else if (isset($_POST['oldPic'])) {
   $this->bild = $this->clearContent($_POST['oldPic']);
  }

  // remove current picture manually

  if (!empty($this->removedPic)) {

   $b = end(explode('/', $this->removedPic));

   if (file_exists($this->short_bildpfad.$b))
     unlink($this->short_bildpfad.$b);

   if ($_POST['oldPic'] == $this->removedPic)
     $this->bild = '';

  }

  $startQuery = $this->start.' '.(!empty($this->zeit_von) ? $this->zeit_von.':01' : '00:00:00');
  $endeQuery  = (!empty($this->ende) ? $this->ende : '1000-01-01').' '.(!empty($this->zeit_bis) ? $this->zeit_bis.':01' : '00:00:00');

	$eventUpdate = db_update($this->tbl_event)
   ->fields(array(
		'name' => $this->name,
		'ort' => $this->adresse,
		'start_ts' => $startQuery,
		'url' => $this->url,
		'ende_ts' => $endeQuery,
		'bild' => $this->bild,
		'kurzbeschreibung' => $this->kurzbeschreibung,
    'modified' => date('Y-m-d H:i:s', time())
	 ))
	 ->condition('EID', $this->event_id, '=')
	 ->execute();

	 $akteurEventUpdate = db_update($this->tbl_akteur_events)
   	->fields(array(	'AID' => $this->veranstalter ))
	  ->condition('EID', $this->event_id, '=')
	  ->execute();

   // remove tags manually

   if (!empty($this->removedTags) && is_array($this->removedTags)) {

    foreach($this->removedTags as $tag) {

     $tag = $this->clearContent($tag);

     db_delete($this->tbl_event_sparte)
      ->condition('hat_KID', $tag, '=')
      ->condition('hat_EID', $this->event_id, '=')
      ->execute();

     }
    }

    // Update Tags
    if (is_array($this->sparten) && !empty($this->sparten)) {

     $this->sparten = array_unique($this->sparten);

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

    // Call hooks
    module_invoke_all('hook_event_modified');

    if (session_status() == PHP_SESSION_NONE) session_start();
    drupal_set_message(t('Das Event wurde erfolgreich bearbeitet!'));
  	header('Location: '. $base_url .'/eventprofil/' . $this->event_id);

  } // END function eventUpdaten()

  /**
   * Wird aufgerufen, wenn "Event bearbeiten" ausgewählt wurde
   * Daten aus DB in Felder schreiben
   */
  private function eventGetFields() {

    //Auswahl der Daten des ausgewählten Events
    $resultEvent = db_select($this->tbl_event, 'e')
     ->fields('e')
	   ->condition('EID', $this->event_id)
     ->execute();

    foreach ($resultEvent as $row) {
     $startTime = new DateTime($row->start_ts);
     $endeTime  = new DateTime($row->ende_ts);
     $this->hat_zeit_von = ($startTime->format('s') == '01') ? true : false;
     $this->hat_zeit_bis = ($endeTime->format('s') == '01') ? true : false;

     $this->name = $row->name;
     $this->ort = $row->ort;
     $this->start = $startTime->format('Y-m-d');
     $this->ende = $endeTime->format('Y-m-d');
     $this->zeit_von = $startTime->format('H:i');
     $this->zeit_bis = $endeTime->format('H:i');
     $this->url = $row->url;
     $this->bild = $row->bild;
     $this->kurzbeschreibung = $row->kurzbeschreibung;
     $this->created = new DateTime($row->created);
     $this->modified = new DateTime($row->modified);
     $this->recurringEventType = $row->recurring_event_type;
    }

    $resultVeranstalter = db_select($this->tbl_akteur_events, 'a')
     ->fields('a', array( 'AID' ))
	   ->condition('EID', $this->event_id, '=')
     ->execute();

     foreach ($resultVeranstalter as $row) {
      $this->veranstalter = $row->AID;
     }

    $akteur_id = $this->veranstalter;

    //Adressdaten aus DB holen:
    $this->resultAdresse = db_select($this->tbl_adresse, 'd')
     ->fields('d')
	   ->condition('ADID', $this->ort, '=')
     ->execute();

    //Speichern der Adressdaten in den Arbeitsvariablen
    foreach ($this->resultAdresse as $row) {
	   $this->strasse = $row->strasse;
	   $this->nr = $row->nr;
	   $this->adresszusatz = $row->adresszusatz;
	   $this->plz = $row->plz;
	   $this->ort = $row->bezirk;
	   $this->gps = $row->gps_lat.','.$row->gps_long;
    }

    $resultSparten = db_select($this->tbl_event_sparte, 'es')
     ->fields('es')
     ->condition('hat_EID', $this->event_id, '=')
     ->execute()
     ->fetchAll();

    $sparten = array();

    foreach($resultSparten as $sparte) {

     $sparten[] = db_select($this->tbl_sparte, 's')
     ->fields('s')
     ->condition('KID', $sparte->hat_KID, '=')
     ->execute()
     ->fetchAll();

    }

    $this->sparten = $sparten;

  } // END function eventUpdaten()


  private function eventSpeichern() {

   if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
    // TODO $this->check_image_compatibility($_FILES['bild'])
    $this->bild = $this->upload_image($_FILES['bild']);
   }

	 //Abfrage, ob Adresse bereits in Adresstabelle
	 $this->resultAdresse = db_select($this->tbl_adresse, 'a')
	  ->fields('a', array('ADID', 'gps_lat', 'gps_long'))
	  ->condition('strasse', $this->strasse, '=')
	  ->condition('nr', $this->nr, '=')
	  ->condition('adresszusatz', $this->adresszusatz, '=')
	  ->condition('plz', $this->plz, '=')
	  ->condition('bezirk', $this->ort, '=')
	  ->execute();

    // Wenn ja: Holen der ID der Adresse, wenn nein: einfuegen
    if ($this->resultAdresse->rowCount() == 0) {

     $gps = explode(',', $this->gps, 2);

	   $this->adresse = db_insert($this->tbl_adresse)
	    ->fields(array(
		   'strasse' => $this->strasse,
		   'nr' => $this->nr,
		   'adresszusatz' => $this->adresszusatz,
		   'plz' => $this->plz,
		   'bezirk' => $this->ort,
		   'gps_lat' => $gps[0],
       'gps_long' => $gps[1]
		 ))
		 ->execute();

	 } else {

	  foreach ($this->resultAdresse as $row) {
	    // Abfrage, ob GPS-Angaben gemacht wurden

	    if (strlen($this->gps) != 0 && strlen($row->gps) == 0) {
        // Ja UND es sind bisher keine GPS-Daten zu der Adresse in der DB

        $gps = explode(',', $this->gps, 2);

	      $adresse_updated = db_update($this->tbl_adresse)
	 	     ->fields(array(
          'gps_lat' => $gps[0],
          'gps_long' => $gps[1]
         ))
	       ->condition('ADID', $row->ADID, '=')
	       ->execute();
	    }

	    $this->adresse = $row->ADID;
	  }
	}

  $startQuery = $this->start.' '.(!empty($this->zeit_von) ? $this->zeit_von.':01' : '00:00:00');
  $endeQuery  = (!empty($this->ende) ? $this->ende : '1000-01-01').' '.(!empty($this->zeit_bis) ? $this->zeit_bis.':01' : '00:00:00');

	$this->event_id = db_insert($this->tbl_event)
   ->fields(array(
		'name' => $this->name,
		'ort' => $this->adresse,
		'start_ts' => $startQuery,
		'url' => $this->url,
		'ende_ts' => $endeQuery,
		'bild' => $this->bild,
		'kurzbeschreibung' => $this->kurzbeschreibung,
		'ersteller' => $this->user_id,
    'created' => date('Y-m-d H:i:s', time()),
    'recurring_event_type' => ($this->eventRecurres && !empty($this->eventRecurringType) ? '1' : NULL)
	  ))
	 ->execute();

	 // Falls Akteur angegeben wurde:
    if (!empty($this->veranstalter)) {

	    db_insert($this->tbl_akteur_events)
   	  ->fields(array(
		   'AID' => $this->veranstalter,
	     'EID' => $this->event_id,
	    ))
	    ->execute();

  	}

    if (is_array($this->sparten) && !empty($this->sparten)) {

     foreach ($this->sparten as $id => $sparte) {
 		 // Tag bereits in DB?

     $sparte = strtolower($this->clearContent($sparte));

     $sparte_id = '';

 		 $resultSparte = db_select($this->tbl_sparte, 's')
 		  ->fields('s')
 		  ->condition('KID', $sparte, '=')
 		  ->execute();

  		if ($resultSparte->rowCount() == 0) {
      // Tag in DB einfügen
 	  	 $sparte_id = db_insert($this->tbl_sparte)
 		   ->fields(array('kategorie' => $sparte))
 		   ->execute();

 		} else {
 		 foreach ($resultSparte as $row) {
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

  if ($this->eventRecurres && !empty($this->eventRecurringType)) {

   $datePeriod = NULL;

   switch ($this->eventRecurringType) {
     case '2' :
      $datePeriod = 'P1W';
     break;
     case '3' :
      $datePeriod = 'P2W';
     break;
     case '4' :
      $datePeriod = 'P1M';
     break;
     case '5' :
      $datePeriod = 'P2M';
     break;
   }

  for ($i = 0; $i < 5; $i++) {

   $start = new DateTime($startQuery);
   $start->add(new DateInterval($datePeriod));
   $startQuery = $start->format('Y-m-d H:i:s');
   
   $ende = new DateTime($endeQuery);
   $ende->add(new DateInterval($datePeriod));
   $endeQuery = $ende->format('Y-m-d H:i:s');

   $recurringEvent = db_insert($this->tbl_event)
    ->fields(array(
    'start_ts' => $startQuery,
    'ende_ts' => $endeQuery,
		'parent_EID' => $this->event_id,
    'recurring_event_type' => $this->eventRecurringType
   ))
   ->execute();

   }
  }

    // Tell Drupal about the new eventprofil/ID-item
    $parentItem = db_query(
     "SELECT menu_links.mlid
      FROM {menu_links} menu_links
      WHERE menu_name = :menu_name AND link_path = :link_path",
      array(":menu_name" => "navigation", ":link_path" => 'events'));

    $plid = $parentItem->fetchObject();

    $item = array(
     'menu_name' => 'navigation',
     'weight' => 1,
     'link_title' => t('Eventprofil von !username', array('!username' => $this->name)),
     'module' => 'aae_data',
     'link_path' => 'eventprofil/'.$this->event_id,
     'plid' => $plid->mlid
    );

    menu_link_save($item);

    // Call hooks
    module_invoke_all('hook_event_created');

    if (session_status() == PHP_SESSION_NONE) session_start();
    drupal_set_message(t('Das Event wurde erfolgreich erstellt!'));
	  header("Location: ". $base_url ."/eventprofil/" . $this->event_id);

  } // END function event_speichern()


  /**
   * Darstellung der Formularinformationen
   */

  private function eventDisplay() {

    if (array_intersect(array('administrator'), $user->roles)) {

      // Zeige Admin alle Akteure
      $this->resultakteure = db_select($this->tbl_akteur, 'a')
       ->fields('a', array( 'AID', 'name' ))
       ->execute();

    } else {

      // Akteure abfragen, die in DB und für welche User Schreibrechte hat
      $user_hat_akteure = db_select($this->tbl_hat_user, 'hu')
       ->fields('hu')
       ->condition('hat_UID', $this->user_id, '=')
       ->execute()
       ->fetchAll();

      foreach($user_hat_akteure as $akteur) {

       $this->resultakteure[] = db_select($this->tbl_akteur, 'a')
       ->fields('a', array('AID', 'name'))
       ->condition('AID', $akteur->hat_AID, '=')
       ->execute()
       ->fetchAll();
     }
    }

    $this->resultbezirke = db_select($this->tbl_bezirke, 'b')
     ->fields('b', array( 'BID', 'bezirksname' ))
     ->execute();

    $all_sparten = $this->getAllTags();

    foreach ($all_sparten as $id => $sparte) {
     $this->all_sparten[$id] = $sparte;
    }

    return $this->render('/templates/eventformular.tpl.php');

  } // END function eventDisplay()
} // END class eventformular()
