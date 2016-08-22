<?php

  /** From here we call the festival-templates and read the aliase
   *  TODO: Right now the template-functionality has been mostly hardcoded
   *  so try to read in the festival-model for basic aae-data-functionality!
   */

  $path = strtolower(trim($_GET['q'])); // TODO: Call filter_xss()
  $tpl = db_query('SELECT alias FROM {url_alias} u WHERE u.source = :path', array(':path' => $path))->fetchObject();
  $fId = db_query('SELECT FID FROM {aae_data_festival} f WHERE f.alias = :alias', array(':alias' => $tpl->alias))->fetchObject();
  if (session_status() == PHP_SESSION_NONE) session_start();
  $_SESSION['fid'] = $fId->FID; // TODO: Well, could be much more beautiful...

  require_once($tpl->alias.'.php');

?>