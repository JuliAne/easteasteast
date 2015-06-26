<?php
/**
 * User: Hans-Gert Gräbe
 * Date: 11.06.2015
 * 
 */

require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

$graph = new EasyRdf_Graph("http://kultur-initiative.net/Data/Akteure/");
$graph->parseFile("../Daten/Akteure.ttl");
//$graph->parseFile("Akteure.json");
echo $graph->serialise("json");
//echo $graph->serialise("turtle");

?>


