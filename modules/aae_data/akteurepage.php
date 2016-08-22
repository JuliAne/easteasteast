<?php
/**
 * @file akteurepage.php
 * Listet alle Akteure auf.
 * Filterbar nach Keyword, Tags und Bezirk.
 */

/* TODO-stuff: 2b put within function invite->hasAddedAkteur()...

  $values = array(
  'arguments' => array(
    '@name' => '<a href="'.base_path().'eventprofil/'.$akteurResult->AID.'">'.$akteurResult->name.'</a>',
    '@message' => 'hat den Akteur xy hinzugefügt'
  ));

  $message = message_create('aae_message', $values);
  $wrapper = entity_metadata_wrapper('message', $message);
  $wrapper->save(); 

 __

 TODO: Check for user_akteure and mark them by CSS-class .userIsAkteur

 if (user_is_logged_in()) {
 $userHasAkteure = db_select($this->tbl_hat_user, 'hu')
  ->fields('hu',array('hat_AID'))
  ->condition('hat_UID', $user->uid)
  ->execute();

  $userHasAkteure = $userHasAkteure->fetchAll();
} */

namespace Drupal\AaeData;

Class akteurepage extends aae_data_helper {

 var $presentationMode;
 var $maxAkteure;
 var $sparten;
 var $hasFilters = false;
 var $filter = array();
 
 public function __construct(){
  
  parent::__construct();
  
  require_once('models/akteure.php');
  $this->akteure = new akteure();
  
 }

 public function run(){

  $this->presentationMode = (isset($_GET['presentation']) && !empty($_GET['presentation']) ? $this->clearContent($_GET['presentation']) : 'boxen');
  $this->maxAkteure = (isset($_GET['display_number']) && !empty($_GET['display_number']) ? $this->clearContent($_GET['display_number']) : '40');

  if (isset($_GET['filterTags']) && !empty($_GET['filterTags'])) {
   $this->filter['tags'] = $_GET['filterTags']; # Becomes escaped in model
  }

  if (isset($_GET['filterKeyword']) && !empty($_GET['filterKeyword'])) {
   $this->filter['keyword'] = $this->clearContent($_GET['filterKeyword']);
  }

  if (isset($_GET['filterBezirke']) && !empty($_GET['filterBezirke'])) {
   $this->filter['bezirke'] = $_GET['filterBezirke']; # Becomes escaped in model
  }

  // Paginator (will be replaced by dynamic AJAX-loads)
  $explodedPath = explode("/", $this->clearContent(current_path()));
  $currentPageNr = ($explodedPath[1] == '' ? '1' : $explodedPath[1]);
  $itemsCount = db_query("SELECT COUNT(AID) AS count FROM " . $this->tbl_akteur)->fetchField();
  $maxPages = ceil($itemsCount / $this->maxAkteure); # How many pages?
  # TODO, if filtered, too much page-numbers
  if ($currentPageNr > $maxPages) {
   // Diese URL gibt es nicht, daher zurueck...
   header('Location: '. $base_url . '/akteure/' . $maxPages);
  } elseif ($currentPageNr > 1) {
   $start = $this->maxAkteure * ($currentPageNr - 1);
   $ende = $this->maxAkteure * $currentPageNr;
  } else {
   $start = 0;
   $ende = $this->maxAkteure;
  }

  $resultAkteure = $this->akteure->getAkteure(array('range' => array('start' => $start, 'end' => $ende),'filter' => $this->filter), 'minimal');

  // Prepare for rendering
  foreach ($resultAkteure as $counter => $akteur) {
   
   $renderSmallName = false;
   $renderBigImg = false;
   $akName = explode(" ", $akteur->name);

   foreach ($akName as $name) {
    if (strlen($name) >= 17 || strlen($akteur->name) >= 30)
      $renderSmallName = true;
   }

   // Check image-relations, adjust height via CSS-Class
   if (!empty($akteur->bild)){
    $img = str_replace('/'.$this->short_bildpfad, '', $akteur->bild);
    $img = getimagesize($this->bildpfad . $img);
    $renderBigImg = ($img[0] / $img[1] < 1.3) ? 1 : 0;
   }

   // Hack: add variable to $resultAkteure-object
   $resultAkteure[$counter] = (array)$resultAkteure[$counter];
   $resultAkteure[$counter]['renderSmallName'] = $renderSmallName;
   $resultAkteure[$counter]['renderBigImg'] = $renderBigImg;
   $resultAkteure[$counter] = (object)$resultAkteure[$counter];

  }

  if ($this->presentationMode == 'map') {
   // Generiere Map-Content...

   $js = 'var addressPoints = [';

   foreach ($resultAkteure as $akteur) {

    if (!empty($akteur->gps)) {
     $beschreibung = (!empty($akteur->kurzbeschreibung)) ? ' - '.$akteur->kurzbeschreibung.'...' : '';
     $js .= '['.$akteur->gps.',"<a href=\''.base_path().'akteurprofil/'.$akteur->AID.'\'>'.$akteur->name.'</a>'.$beschreibung.'"],';
    }

   }

   $js .= '];';
   drupal_add_js($js, 'inline');
   // Needed to add Map-Files:
   $this->addMapContent('','',array('something' => 'bla'));
  }

  $resultBezirkeRelevance = db_query_range('SELECT COUNT(*) AS count, b.BID, b.bezirksname FROM {aae_data_bezirke} b
                                            INNER JOIN {aae_data_adresse} ad ON b.BID = ad.bezirk
                                            INNER JOIN {aae_data_akteur} a ON a.adresse = ad.ADID
                                            GROUP BY b.BID HAVING COUNT(*) > 0 ORDER BY count DESC', 0, 5);

  $resultTags = $this->getAllTags('akteure');
  $resultBezirke = $this->getAllBezirke('akteure');

  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme().'/templates/akteure.tpl.php';
  return ob_get_clean(); // Uebergabe des gerenderten Templates

 }
} // end class akteure
?>
