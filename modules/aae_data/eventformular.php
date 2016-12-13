<?php
/**
 * @file eventformular.php
 *
 * Stellt ein Formular dar, in welches alle Informationen über eine Veranstaltung
 * eingetragen werden können.
 * Pflichtfelder sind (bisher): Name, Veranstalter, Datum (Anfang) & Beschreibung
 * Anschließend werden die Daten gefiltert in die DB-Tabellen eingetragen
 * oder geupdatet (s. $this->target).
 *
 * TODO: Outsource functionality into events_model->setUpdateEvent()
 * TODO: Integrate with akteure- and festivals-models
 */

namespace Drupal\AaeData;

Class eventformular extends aae_data_helper {

  var $event_id = '';
  var $name = '';
  var $veranstalter = '';
  var $start = ''; # day-start
  var $ende = ''; # day-end
  var $zeit_von = ''; # time-start
  var $zeit_bis = ''; # time-end
  var $hat_zeit_von = true;
  var $hat_zeit_bis = true;
  var $bild = '';
  var $kurzbeschreibung = '';
  var $url = '';
  var $created = '';
  var $modified = '';

  //$tbl_adresse
  var $strasse = '';
  var $nr = '';
  var $adresszusatz = '';
  var $plz = '';
  var $ort = '';
  var $gps = '';
  var $adresse = '';

  //$tbl_sparte
  var $tags = '';
  var $allTags = ''; // Zur Darstellung des tokenizer (#akteurSpartenInput)
  
  var $isFestival = false; // Only for submit-actions
  var $ownedFestivals;
  var $FID; // Only for edit-mode
  var $freigabe = true;  // Variable zur Freigabe: muss true sein
  var $fehler = array(); // In diesem Array werden alle Fehler gespeichert

  var $akteur_id;
  var $resultAkteure;
  var $resultbezirke;
  var $target = '';
  var $removedTags;
  var $removedPic;
  var $eventRecurres;
  var $eventRecurringType;
  var $eventRecurresTill;

  function __construct($action = false) {

   parent::__construct();
   
   $explodedpath = explode('/', current_path());
   $this->event_id = $this->clearContent($explodedpath[1]);

   $this->event        = new events();
   $this->tagsHelper   = new tags();
   $this->adressHelper = new adressen();

   // Sollen die Werte im Anschluss gespeichert oder geupdatet werden?
   if ($action == 'update') {
     
	  $this->target = 'update';
    if (!user_is_logged_in() || !$this->event->isAuthorized($this->event_id, $this->user_id)){
  	 drupal_access_denied();
     drupal_exit();
    }
    
   } else {

    if (!user_is_logged_in()){
	   drupal_access_denied();
     drupal_exit();
    }
     
   }
     
  }

  /**
   * Funktion, welche reihenweise POST-Werte auswertet, abspeichert bzw. ausgibt.
   * @return $profileHTML;
   */
  public function run() {

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
   * @return $this->freigabe : boolean
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
    $this->adresse = (object)$_POST['adresse'];
    $this->tags = $_POST['tags'];
    $this->removedTags = $this->clearContent($_POST['removedTags']);
    $this->removedPic = $this->clearContent($_POST['removeCurrentPic']);
    $this->eventRecurres = $this->clearContent($_POST['eventRecurres']);
    $this->eventRecurringType = $this->clearContent($_POST['eventRecurringType']);
    $this->eventRecurresTill = $this->clearContent($_POST['eventRecurresTill']);

    //-------------------------------------

    if (empty($this->name)) {
     $this->fehler['name'] = t("Bitte einen Veranstaltungsnamen eingeben!");
    }

    if (strlen($this->start) != 0 && $this->__validateDate($this->start) === false) {
     $this->fehler['start'] = t("Bitte ein (gültiges) Startdatum angeben!");
    }

    if (strlen($this->ende) != 0 && $this->__validateDate($this->ende) === false) {
     $this->fehler['ende'] = t("Bitte ein (gültiges) Enddatum angeben!");
    }

    if (strlen($this->eventRecurresTill) != 0 && $this->__validateDate($this->eventRecurresTill) === false) {
     $this->fehler['eventRecurresTill'] = t("Bitte ein (gültiges) Maximaldatum angeben!");
    }

    if (empty($this->adresse->bezirk)) {
  	 $this->fehler['bezirk'] = t("Bitte einen Bezirk auswählen!");
    }

    if (empty($this->kurzbeschreibung)) {
     $this->fehler['kurzbeschreibung'] = t("Ein paar Beschreibungs-Zeilen werden Dir einfallen...");
    }
   
    if (!empty($this->zeit_von) && $this->__validateDate($this->zeit_von, 'H:i') === false) {
     $this->fehler['zeit_von'] = t("Bitte eine (gültige) Start-Uhrzeit angeben!");
    }

    if (!empty($this->zeit_bis) && $this->__validateDate($this->zeit_bis, 'H:i') === false) {
     $this->fehler['zeit_bis'] = t("Bitte eine (gültige) End-Uhrzeit angeben!");
    }

    if (!empty($this->url) && preg_match('/\A(http:\/\/|https:\/\/)(\w*[.|-]\w*)*\w+\.[a-z]{2,3}(\/.*)*\z/',$this->url)==0) {
     $this->fehler['url'] = t("Bitte eine gültige URL zur Eventwebseite anngeben! (z.B. <i>http://meinevent.de</i>)");
    }

    /*if ((count($this->tags)/2) > 11) {
     $this->fehler['tags'] = "Bitte max. 10 Tags angeben.";
     $this->freigabe = false;
   } */

    if (strlen($this->name) > 64) {
	   $this->fehler['name'] = t("Bitte geben Sie einen kürzeren Namen an oder verwenden Sie ein Kürzel.");
    }

    if (strlen($this->url) > 200) {
	   $this->fehler['url'] = t("Bitte geben Sie eine kürzere URL an.");
    }

    if (strlen($this->kurzbeschreibung) > 65000) {
     $this->fehler['kurzbeschreibung'] = t("Bitte geben Sie eine kürzere Beschreibung an.");
    }

    if (strlen($this->adresse->strasse) > 64) {
	   $this->fehler['strasse'] = t("Bitte geben Sie einen kürzeren Strassennamen an.");
    }

    if (strlen($this->adresse->nr) > 8) {
	   $this->fehler['nr'] = t("Bitte geben Sie eine kürzere Nummer an.");
    }

    if (strlen($this->adresse->adresszusatz) > 100) {
	   $this->fehler['adresszusatz'] = t("Bitte geben Sie einen kürzeren Adresszusatz an.");
    }

    if (strlen($this->adresse->plz) > 8) {
	   $this->fehler['plz'] = t("Bitte geben Sie eine kürzere PLZ an.");
    }

    if (strlen($this->adresse->gps) > 100) {
     $this->fehler['gps'] = t("Bitte geben Sie kürzere GPS-Daten an.");
    }

    if ($this->gps == t('Ermittle Geo-Koordinaten...')) $this->gps = '';
    
    /*if (!empty($this->gps) && preg_match('\s.\s,\s.\s',$this->gps)==0) {
      echo ':/';
      exit(); } */
/*print_r($this->bild); echo ' b '.$this->bild; exit();
    if ($this->bild){
     $errMsg = $this->check_image_compatibility($this->bild);
     if (!is_bool($errMsg)) {
      $this->fehler['bild'] = $errMsg;
      $this->freigabe = false;
     }
    } */
    
    // Um die bereits gewählten Tag's anzuzeigen benötigen wir deren Namen...
   if (!empty($this->fehler)) {
    $this->freigabe = false;
    $this->tags = $this->tagsHelper->__getKategorieForTags($this->tags);
   }

   if (!is_numeric($this->veranstalter)){
    $this->isFestival = true;
   }
   
   return $this->freigabe;

  } // END function eventCheckPost()

  /**
   * Wird ausgefuehrt, wenn Update der Daten verlangt ist
   * TODO Veranstalter wechselbar machen
   */
  private function eventUpdaten() {

   if ($this->isFestival)
     $this->FID = str_replace('f','',$this->veranstalter);

   $eventOrt = db_select($this->tbl_event, 'e')
    ->fields('e', array('ort'))
    ->condition('EID', $this->event_id)
    ->execute()
    ->fetchObject();

   $this->adresse->ADID = $eventOrt->ort;
   
   $this->adresse = $this->adressHelper->setUpdateAdresse($this->adresse);

   if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) {
    $this->bild = $this->upload_image($_FILES['bild']);
   } else if (isset($_POST['oldPic'])) {
    $this->bild = $this->clearContent($_POST['oldPic']);
   }
  
  // remove current picture manually
  // TODO: Check for universal functionality
  if (!empty($this->removedPic)) {

   $b = end(explode('/', $this->removedPic));

   if (file_exists($this->short_bildpfad.$b))
     @unlink($this->short_bildpfad.$b);

   if ($_POST['oldPic'] == $this->removedPic)
     $this->bild = '';

  }

  $startQuery = $this->start.' '.(!empty($this->zeit_von) ? $this->zeit_von.':01' : '00:00:00');
  $endeQuery  = (!empty($this->ende) ? $this->ende : '1000-01-01').' '.(!empty($this->zeit_bis) ? $this->zeit_bis.':01' : '00:00:00');
  
  // Has event children? Which type? Set them new?
  $eventStatus = db_select($this->tbl_event, 'e')
   ->fields('e', array('start_ts','ende_ts','recurring_event_type','event_recurres_till'))
   ->condition('EID', $this->event_id)
   ->execute();
   
  $status = $eventStatus->fetchObject();
  
  if ($this->eventRecurres == 'on' && $status->recurring_event_type < 6 && (($status->recurring_event_type != $this->eventRecurringType) 
                                                                             || ((new \DateTime($status->event_recurres_till))->format('Y-m-d') != $this->eventRecurresTill)
                                                                             || ($status->start_ts != $startQuery)
                                                                             || ($status->ende_ts != $endeQuery))) {
                                          
   // Repeat-staus or any dates changed: Recreate child-events with new params
   $this->event->removeEventChildren($this->event_id);
   $recurresTill = (!empty($this->eventRecurresTill)) ? (new \DateTime($this->eventRecurresTill))->format('Y-m-d') : NULL;
   $this->event->addEventChildren($this->event_id, $this->eventRecurringType, $startQuery, $endeQuery, $recurresTill);

  } else if ($this->eventRecurres != 'on' && $status->recurring_event_type >= 2 && $status->recurring_event_type < 6) {
   
   // "Event recurres" turned off
   $this->event->removeEventChildren($this->event_id);
   
  } else if ($this->eventRecurres == 'on' && empty($status->recurring_event_type)){
    
    // "Event recurres" turned on -> Create Event Children
    $recurresTill = (!empty($this->eventRecurresTill)) ? (new \DateTime($this->eventRecurresTill))->format('Y-m-d') : NULL;
    $this->event->addEventChildren($this->event_id, $this->eventRecurringType, $startQuery, $endeQuery, $recurresTill);
   
  }
  
	$eventUpdate = db_update($this->tbl_event)
   ->fields(array(
		'name' => $this->name,
		'ort' => $this->adresse,
		'start_ts' => $startQuery,
		'url' => $this->url,
		'ende_ts' => $endeQuery,
		'bild' => $this->bild,
		'kurzbeschreibung' => $this->kurzbeschreibung,
    'recurring_event_type' => ($this->eventRecurres == 'on' ? $this->eventRecurringType : ''),
    'FID' => $this->FID,
    'modified' => date('Y-m-d H:i:s', time())
	 ))
	 ->condition('EID', $this->event_id)
	 ->execute();

   if (!empty($this->isFestival)){
     db_update($this->tbl_event)
     ->fields(array('recurring_event_type' => '6'))
     ->condition('EID', $this->event_id)
     ->execute();

     $this->veranstalter = db_select($this->tbl_festival, 'f')
      ->fields('f', array('admin'))
      ->condition('FID', $this->FID)
      ->execute()
      ->fetchObject();

     $this->veranstalter = $this->veranstalter->admin;
   }
   
   // TODO: Do db_insert if events was private before
	 $akteurEventUpdate = db_update($this->tbl_akteur_events)
   	->fields(array('AID' => $this->veranstalter))
	  ->condition('EID', $this->event_id)
	  ->execute();

    // UPDATE, INSERT or REMOVE Tags
    $this->tagsHelper->setRemoveTags($this->tags, array('event', $this->event_id), $this->removedTags);

    module_invoke_all('hook_event_modified');

    if (session_status() == PHP_SESSION_NONE) session_start();
    drupal_set_message(t('Das Event wurde erfolgreich bearbeitet!'));
  
  	if ($this->isFestival) {
     header('Location: '. $base_url .'/events/new');
    } else {
     header('Location: '. $base_url .'/eventprofil/' . $this->event_id);
    }

  } // END function eventUpdaten()

  /**
   * Wird aufgerufen, wenn "Event bearbeiten" ausgewählt wurde
   * Daten aus DB in Felder schreiben
   */
  private function eventGetFields() {

   $resultEvent = reset($this->event->GetEvents(array('EID' => $this->event_id), 'complete'));

   $this->akteur_id = $resultEvent->akteur->AID;
   $this->hat_zeit_von = ($resultEvent->start->format('s') == '01') ? true : false;
   $this->hat_zeit_bis = ($resultEvent->ende->format('s') == '01') ? true : false;
   $this->name = $resultEvent->name;
   $this->ort = $resultEvent->ort;
   $this->start = $resultEvent->start->format('Y-m-d');
   $this->ende = $resultEvent->ende->format('Y-m-d');
   $this->zeit_von = $resultEvent->start->format('H:i');
   $this->zeit_bis = $resultEvent->ende->format('H:i');
   $this->url = $resultEvent->url;
   $this->bild = $resultEvent->bild;
   $this->kurzbeschreibung = $resultEvent->kurzbeschreibung;
   $this->created = $resultEvent->created;
   $this->modified = $resultEvent->modified;
   $this->eventRecurres = ($resultEvent->recurring_event_type >= 1);
   $this->recurringEventType = $resultEvent->recurring_event_type;
   $this->eventRecurresTill = $resultEvent->eventRecurresTill->format('Y-m-d');
	 $this->adresse = $resultEvent->adresse;
   $this->adresse->gps = (!empty($resultEvent->adresse->gps_lat)) ? $resultEvent->adresse->gps_lat.','.$resultEvent->adresse->gps_long : '';
   $this->FID = $resultEvent->FID;
   $this->veranstalter = $resultEvent->ersteller;
   $this->tags = $resultEvent->tags;

   #$this->tags = $this->tagsHelper->getTags('events', array('hat_EID', $this->event_id));

  } // END function eventGetFields()


  private function eventSpeichern() {

   if (isset($_FILES['bild']['name']) && !empty($_FILES['bild']['name'])) // TODO: try-catch with $this->imageUploadable()
     $this->bild = $this->upload_image($_FILES['bild']);

   if ($this->isFestival)
     $this->FID = str_replace('f','',$this->veranstalter);
   
   $BID = $this->adresse->bezirk;
	 $this->adresse = $this->adressHelper->setUpdateAdresse($this->adresse);

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
    'recurring_event_type' => ($this->eventRecurres && !empty($this->eventRecurringType) ? $this->eventRecurringType : NULL),
    'event_recurres_till' => ($this->eventRecurres && !empty($this->eventRecurresTill) ? $this->eventRecurresTill.' 00:00:00' : '1000-01-01 00:00:00'),
	  'FID' => $this->FID,
    ))
	  ->execute();
   
   if (!empty($this->isFestival)){

     db_update($this->tbl_event)
     ->fields(array('recurring_event_type' => '6'))
     ->condition('EID', $this->event_id)
     ->execute();

     $this->veranstalter = db_query($this->tbl_akteur_festivals, 'f')
      ->fields('f', array('admin'))
      ->condition('FID', $this->FID)
      ->execute()
      ->fetchObject();

     $this->veranstalter = $this->veranstalter->admin;

   }

	 // if Veranstalter != 'privat'
   if (!empty($this->veranstalter)) {

	  db_insert($this->tbl_akteur_events)
     ->fields(array(
	   'AID' => $this->veranstalter,
	   'EID' => $this->event_id,
	   ))
	   ->execute();

   }

   $this->tagsHelper->setRemoveTags($this->tags, array('event', $this->event_id), $this->removedTags);

   if ($this->eventRecurres == 'on' && !empty($this->eventRecurringType)) {
   
    $recurresTill = (!empty($this->eventRecurresTill)) ? (new \DateTime($this->eventRecurresTill))->format('Y-m-d') : NULL;
    $this->event->addEventChildren($this->event_id, $this->eventRecurringType, $startQuery, $endeQuery, $recurresTill);

   }

    // Tell Drupal about the new eventprofil/ID-item, get Bezirksname
    $parentItem = db_query(
     "SELECT menu_links.mlid
      FROM {menu_links} menu_links
      WHERE menu_name = :menu_name AND link_path = :link_path",
      array(":menu_name" => "navigation", ":link_path" => 'events'));

    $bezirk = db_select($this->tbl_bezirke, 'b')
     ->fields('b')
     ->condition('BID', $BID)
     ->execute();
    
    $plid = $parentItem->fetchObject();
    $bezirk = trim(preg_replace("/\(\w+\)/", "", $bezirk->fetchObject()->bezirksname));
    
    $item = array(
     'menu_name' => 'navigation',
     'weight' => 1,
     'link_title' => t('!name am !datum in !ort | Events', array('!name' => $this->name, '!datum' => (new \DateTime($startQuery))->format('d.m.Y'),'!ort' => $bezirk)),
     'module' => 'aae_data',
     'link_path' => 'eventprofil/'.$this->event_id,
     'plid' => $plid->mlid
    );
    menu_link_save($item);
    # SEO: ['options']['attributes']['description'] ?

    // Call hooks
    module_invoke_all('hook_event_created');

    if (session_status() == PHP_SESSION_NONE) session_start();
    drupal_set_message(t('Das Event wurde erfolgreich erstellt!'));
	  
    if ($this->isFestival) {
     header('Location: '. $base_url .'/events/new');
    } else {
     header('Location: '. $base_url .'/eventprofil/' . $this->event_id);
    }
  } // END function eventSpeichern()


  /**
   * Darstellung der Formularinformationen
   */
  private function eventDisplay() {
    
    global $user;

    if (array_intersect(array('administrator'), $user->roles)) {

      // Zeige Admin alle Akteure
      $this->resultAkteure = db_select($this->tbl_akteur, 'a')
       ->fields('a', array( 'AID', 'name' ))
       ->execute()
       ->fetchAll();

    } else {

      // Akteure abfragen, die in DB und für welche User Schreibrechte hat
      $user_hat_akteure = db_select($this->tbl_hat_user, 'hu')
       ->fields('hu')
       ->condition('hat_UID', $this->user_id)
       ->execute()
       ->fetchAll();

      foreach($user_hat_akteure as $akteur) {

       $this->resultAkteure[] = db_select($this->tbl_akteur, 'a')
       ->fields('a', array('AID', 'name'))
       ->condition('AID', $akteur->hat_AID)
       ->execute()
       ->fetchObject();
     }
    }

    $this->resultBezirke = $this->adressHelper->getAllBezirke();

    $this->allTags = $this->tagsHelper->getTags();

    $this->ownedFestivals = $this->event->userHasFestivals($this->user_id);

    return $this->render('/templates/eventformular.tpl.php');

 } // END function eventDisplay()
  
 private function __validateDate($date, $format = 'Y-m-d'){
   $d = \DateTime::createFromFormat($format, $date);
   #print_r(\DateTime::getLastErrors());
   return $d && $d->format($format) == $date;
 }
} // END class eventformular()
