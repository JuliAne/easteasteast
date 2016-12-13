<?php

/**
 *  Class to handle Ajax-requests
 *  TODO: Connect with models, if necessary
 *  TODO: drupal_add_http_header('Generator', 'AAE Data');
 */

namespace Drupal\AaeData;

Class aae_data_ajax_requests extends aae_data_helper {
  
 public function __construct(){
  parent::__construct();
 }

/**
 * @function getAllLocations()
 *
 * Returns everything necessary to build a map/modal in frontend 
 * TODO: Response-data, especially those regarding additional Html-Head-files,
 * should be caplsulated in a more native function/automated process (in both, JS and Drupal)
 */
 
 public function getAllLocations(){

  $akteure = new akteure();
  $events  = new events();
  
  # We only want future events to be shown...
  $start = array(
   '0' => array(
    'date' => (new \DateTime(date()))->format('Y-m-d 00:00:00'),
    'operator' => '>='
   )
  );

  $resultAkteure = $akteure->getAkteure(array('filter' => array('mustHaveGps' => 1)), 'minimal');
  $resultEvents  = $events->getEvents(array('start' => $start, 'filter' => array('mustHaveGps' => 1), 'parent_EID' => NULL), 'complete');

  drupal_add_http_header('Content-Type', 'application/json');

  echo '{ "response" :
  {
   "akteure" : '.json_encode($resultAkteure).',
   "events"  : '.json_encode($resultEvents).',
   "htmlHeaders" :
   {
    "css" : "'.$this->mapboxCss.'",
    "js"  : "'.$this->mapboxJs.'",
    "jsInline" : "'.$this->mapboxJsInline.'",
    "mapName" : "'.$this->mapboxMap.'"
   }
   }}';

  drupal_exit();

 }

/**
 * @function getAkteurKontakt()
 *
 * Gibt div wider, welcher Kontaktinformationen zum Akteur beinhaltet.
 * Aufgerufen über das Akteurprofil.
 */

 public function getAkteurKontakt($id) {

  $akteur_id = $this->clearContent($id);

  if (!is_numeric($akteur_id)) exit();

  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a', array('name','email','telefon','ansprechpartner','funktion'))
   ->condition('AID', $akteur_id)
   ->execute()
   ->fetchAll();

  include_once path_to_theme() . '/templates/ajax.akteurkontakt.tpl.php';

 }

 /**
  * @function getAkteurAdresse()
  *
  * Gibt Adresse eines Akteurs wieder, welche (wie im eventformular) zur
  * dynamischen Anzeige dient
  */

 public function getAkteurAdresse($id) {

   $akteur_id = $this->clearContent($id);

   if (!is_numeric($akteur_id)){

    // Festival = Get festival-admin-akteur
    $fAkteurId = db_select($this->tbl_festival, 'f')
      ->fields('f', array('admin'))
      ->condition('FID', str_replace('f','',$akteur_id))
      ->execute()
      ->fetchObject();

    $akteur_id = $fAkteurId->admin;
     
   }

   if (empty($akteur_id) || $akteur_id == 0)
     drupal_exit();

   $akteurAdresse = db_select($this->tbl_akteur, 'a')
    ->fields('a', array('adresse'))
    ->condition('AID', $akteur_id)
    ->execute()
    ->fetchObject();

   $resultAdresse = db_select($this->tbl_adresse, 'ad')
    ->fields('ad')
    ->condition('ADID', $akteurAdresse->adresse)
    ->execute()
    ->fetchObject();

    echo json_encode($resultAdresse);
    drupal_exit();

 }

 /**
  * @function getKalender()
  * Dient dem Einblenden eines neuen Kalender-Monats im Footer
  */

 public function getKalender(){

    $modulePath = drupal_get_path('module', 'aae_data');
    include_once $modulePath . '/kalender.php';

    $kal = new kalender();
    echo $kal->run();

  }
  
  /**
   * @function removeEvent()
   * TODO: Kann raus? Muss überarbeitet werden!
   */
   public function removeEventChildren($eid){
    
    $eid = $this->clearContent($eid);
    
    if (!user_is_logged_in())
     drupal_access_denied();
    
    $parentEID = db_select($this->tbl_event,'e')
     ->fields('e', array('parent_EID'))
     ->condition('EID', $eid)
     ->execute()
     ->fetchObject();

    // Sicherheitsschutz, ob User entsprechende Rechte hat
    $resultAkteurEvent = db_select($this->tbl_akteur_events, 'e')
     ->fields('e')
     ->condition('EID', $parentEID->parent_EID)
     ->execute()
     ->fetchObject();

    $akteur_id = $resultAkteurEvent->AID;
   
    // Prüfen ob Schreibrecht vorliegt: ob User zu dem Akteur gehört
    $resultUser = db_select($this->tbl_hat_user, 'u')
     ->fields('u')
     ->condition('hat_AID', $akteur_id)
     ->condition('hat_UID', $this->user_id)
     ->execute();

    if (!$resultUser->rowCount()) {
     if (!array_intersect(array('administrator'), $user->roles)) {
      echo '0';
      exit();
     }
    }
     
    db_delete($this->tbl_event)
     ->condition('EID', $eid)
     ->execute();
    
   echo '1';
     
   }
  
}
?>
