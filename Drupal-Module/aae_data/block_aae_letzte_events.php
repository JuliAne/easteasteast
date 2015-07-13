<?php
/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten drei (=$limit) eingetragenen Events.
 * Wird in theme/page--front.tpl.php aufgerufen.
 *
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/block_aae_letzte_events.php';
 */

function block_aae_print_letzte_events($limit = 3) {

  $tbl_event = "aae_data_event";

  require_once 'database/db_connect.php';
  $db = new DB_CONNECT();

  $letzteEvents = db_select($tbl_event, 'a')
    ->fields('a')
    ->range(0, $limit)
    ->execute()
    ->fetchAll();

  /*   ->fields('a', array(
      'name',
      'EID',
    )) */

  $resultEvents = array();

 foreach($letzteEvents as $row){
    $resultEvents[] = $row;
  }

  return $resultEvents;
}
