<?php
/**
 * @file akteure.php
 * Listet alle Akteure auf.
 * Filterbar nach Keyword, Tag und Bezirk.
 */

Class akteure extends aae_data_helper {

 var $presentationMode;
 var $maxAkteure;
 var $sparten;
 var $hasFilters = false;
 var $filter = array();
 var $filteredAkteurIds;
 var $filteredTags = array();
 var $filteredBezirke = array();
 var $numFilters = 0;

 public function run(){

  /*

  global $user;

  $values = array(
  'arguments' => array(
    '@name' => '<a href="'.base_path().'eventprofil/'.$akteurResult->AID.'">'.$akteurResult->name.'</a>',
    '@message' => 'hat den Akteur xy hinzugefügt'
  ));

  $message = message_create('aae_message', $values);
  $wrapper = entity_metadata_wrapper('message', $message);
  $wrapper->save(); */


  $this->presentationMode = (isset($_GET['presentation']) && !empty($_GET['presentation']) ? $this->clearContent($_GET['presentation']) : 'boxen');

  $this->maxAkteure = (isset($_GET['display_number']) && !empty($_GET['display_number']) ? $this->clearContent($_GET['display_number']) : '25' );

  if (isset($_GET['filterTags']) && !empty($_GET['filterTags'])) {
   $this->filter['tags'] = $_GET['filterTags'];
  }

  if (isset($_GET['filterKeyword']) && !empty($_GET['filterKeyword'])) {
   $this->filter['keyword'] = $this->clearContent($_GET['filterKeyword']);
  }

  if (isset($_GET['filterBezirke']) && !empty($_GET['filterBezirke'])) {
   $this->filter['bezirke'] = $_GET['filterBezirke'];
  }


 //-----------------------------------

 // Paginator: Auf welcher Seite befinden wir uns?
 $explodedPath = explode("/", current_path());
 $currentPageNr = ($explodedPath[1] == '' ? '1' : $explodedPath[1]);

 $itemsCount = db_query("SELECT COUNT(AID) AS count FROM " . $this->tbl_akteur)->fetchField();

 // Paginator: Wie viele Seiten gibt es?
 $maxPages = ceil($itemsCount / $this->maxAkteure);

 if ($currentPageNr > $maxPages) {
 // Diese URL gibt es nicht, daher zurueck...
  header("Location: Akteure/" . $maxPages);
 } elseif ($currentPageNr > 1) {
  $start = $this->maxAkteure * ($currentPageNr - 1);
  $ende = $this->maxAkteure * $currentPageNr;
 } else {
  $start = 0;
  $ende = $this->maxAkteure;
 }

 // Filter nach Tags, falls gesetzt

 if (isset($this->filter['tags'])){

  $sparten = db_select($this->tbl_hat_sparte, 'hs')
   ->fields('hs', array('hat_AID'));

  $and = db_and();

  foreach($this->filter['tags'] as $tag) {

   $this->numFilters++;
   $tag = $this->clearContent($tag);
   $this->filteredTags[$tag] = $tag;
   $and->condition('hat_KID', $tag, '=');

  }

 $filterSparten = $sparten->condition($and)
  ->execute()
  ->fetchAll();

 foreach ($filterSparten as $sparte){
  $this->filteredAkteurIds[] = $sparte->hat_AID;
 }
} // end Tag-Filter

if (isset($this->filter['bezirke'])){

 foreach ($this->filter['bezirke'] as $bezirk) {

  $this->numFilters++;
  $bezirkId = $this->clearContent($bezirk);
  $this->filteredBezirke[$bezirkId] = $bezirkId;

  $adressen = db_select($this->tbl_adresse, 'a')
   ->fields('a', array('ADID'))
   ->condition('bezirk', $bezirkId, '=')
   ->execute()
   ->fetchAll();

  foreach ($adressen as $adresse) {

   $filterBezirke = db_select($this->tbl_akteur, 'a')
     ->fields('a', array('AID'))
     ->condition('adresse', $adresse->ADID, '=')
     ->execute()
     ->fetchAll();

    foreach ($filterBezirke as $bezirk) {
     $this->filteredAkteurIds[] = $bezirk->AID;
    }
   }
  }
 } // end Bezirke-Filter

if (isset($this->filter['keyword'])) {

 $this->numFilters++;

 $or = db_or()
  ->condition('name', '%'.$this->filter['keyword'].'%', 'LIKE')
  ->condition('beschreibung', '%'.$this->filter['keyword'].'%', 'LIKE');

 $filterKeyword = db_select($this->tbl_akteur, 'e')
  ->fields('e', array('AID'))
  ->condition($or)
  ->execute()
  ->fetchAll();

 foreach ($filterKeyword as $keyword){
  $this->filteredAkteurIds[] = $keyword->AID;
 }
} // end Keyword-Filter

$this->hasFilters = ($this->numFilters >= 1) ? true : false;
$this->filteredAkteurIds = $this->getDuplicates($this->filteredAkteurIds, $this->numFilters);

// Auswahl aller Akteure
$akteure = db_select($this->tbl_akteur, 'a')
 ->fields('a', array(
	'AID',
  'name',
  'beschreibung',
  'bild',
  'adresse'
 ))
 ->orderBy('created', DESC)
 ->orderBy('name', ASC)
 ->range($start, $ende);

 if ($this->hasFilters && !empty($this->filteredAkteurIds)) {

  $or = db_or();

  foreach ($this->filteredAkteurIds as $akteur) {
   $or->condition('AID', $akteur, '=');
  }

  $akteure->condition($or);
  $akteure->range(0,9999);

 } else if ($this->hasFilters && empty($this->filteredAkteurIds)) {

   // Keine Akteure mit entsprechendem Tag gefunden, daher negatives resultAkteure
   $akteure->condition('name', 'LASDFJKASDFSKFDLJ', '=');

 }

 $resultAkteure = $akteure->execute()
  ->fetchAll();

  // Get additional data
  foreach ($resultAkteure as $counter => $akteur) {

   $renderSmallName = false;
   $akName = explode(" ", $akteur->name);

   foreach ($akName as $name) {
    if (strlen($name) >= 17 || strlen($akteur->name) >= 30) $renderSmallName = true;
   }

   // Get short-text
   $numwords = 30;
   preg_match("/(\S+\s*){0,$numwords}/", $akteur->beschreibung, $regs);

   $adresse = db_select($this->tbl_adresse, 'ad')
    ->fields('ad', array('bezirk','gps'))
    ->condition('ADID', $akteur->adresse, '=')
    ->execute()
    ->fetchObject();

   $bezirk = db_select($this->tbl_bezirke, 'b')
    ->fields('b')
    ->condition('BID', $adresse->bezirk, '=')
    ->execute()
    ->fetchObject();

   // Hack: add variable to $resultAkteure-object
   $resultAkteure[$counter] = (array)$resultAkteure[$counter];
   $resultAkteure[$counter]['bezirk'] = $bezirk->bezirksname;
   $resultAkteure[$counter]['gps'] = ($adresse->gps != 'Ermittle Geo-Koordinaten...' ? $adresse->gps : '');
   $resultAkteure[$counter]['renderSmallName'] = $renderSmallName;
   $resultAkteure[$counter]['kurzbeschreibung'] = trim($regs[0]);
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
  return ob_get_clean(); // Uebergabe des gerenderten Template's

 }
} // end class akteure
?>
