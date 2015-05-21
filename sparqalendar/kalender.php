<?php 

/** run a sparql query at leipzig-data.de enpoint and request the result in json format 
  *
  *  type can be 'select' or 'construct'
  */
$sp_events = array();
function run_query($query,$type) 
{
	$store = 'http://leipzig-data.de:8890/sparql';
	
	$format = ($type=='select') ? 'application%2Fsparql-results%2Bjson' : 'application%2Frdf%2Bjson'; //  -- application%2Fsparql-results%2Bjson for select requests
	                                                                                                  //  -- application%2Frdf%2Bjson for construct requests
																									  
	$get_parameters = 		'?default-graph-uri=' .                         //probably have to change parameters when using another store than virtuoso
							'&query=' . urlencode ($query) .
							'&format='.$format .							//esp. format parameter may vary  
							'&timeout=0' .																  
							'&debug=on';

	$req = $store . $get_parameters;
	$result = wp_remote_get($req);
	$r = json_decode($result['body'],true);
	//print_r($r);
	return $r;
}

/** returns yyyy-mm-dd and inserts leading zeros for month and day if necessary*/
function convert_date($jahr,$monat,$tag)
{
	return $jahr.'-'.str_pad($monat,2, '0', STR_PAD_LEFT).'-'.str_pad($tag,2, '0', STR_PAD_LEFT);
}

function reduce_dateTime($date)
{
	if (strpos($date,'T')===false)
		return $date;
	else
		return substr($date,0,strpos($date,'T'));
}

function fetch_events($jahr,$monat) 
{

	global $sp_events;
	$sp_events = array();
	
	$query = 
		'	PREFIX ld: <http://leipzig-data.de/Data/Model/>
			PREFIX ical:<http://www.w3.org/2002/12/cal/ical#>
			select ?a ?l ?s ?e
			Where { 
					?a a ld:Event .
					?a ical:dtstart ?s; rdfs:label ?l. OPTIONAL { ?a ical:dtend ?e}.
					Filter(    (xsd:date(?s) >= "'.convert_date($jahr,$monat,'1').'"^^xsd:date && xsd:date(?s) < "'.convert_date($jahr,$monat+1,'1').'"^^xsd:date)
							|| (xsd:date(?e) >= "'.convert_date($jahr,$monat,'1').'"^^xsd:date && xsd:date(?e) < "'.convert_date($jahr,$monat+1,'1').'"^^xsd:date) ).
			}
            order by (?s)';
			
	$r = run_query($query,'select');
		//print_r($r);
	if (isset($r)) {	//generate a data structure where for every day of the current month the list of events is stored
	foreach ($r['results']['bindings'] as $k => $v)
	{
		$id=$v['a']['value']; $s = reduce_dateTime($v['s']['value']); $e = reduce_dateTime($v['e']['value']); $l = $v['l']['value'];

			//evaluate dtstart and dtend of every event and "attach" it to the days when it takes place
		$date1 = date_create($s); $date2 = ($e) ?  date_create($e) : date_create($s); //assume duration of one day if dtend is not specified - NOTE
		$aday = new DateInterval('P1D');
		while ($date1 <= $date2)
		{
			$sp_events[$date1->format('Y-m-d')][]=array('id'=>$id,'label'=>$l);
			$date1->add($aday);
		}
		
		/* Does not work because of PHP BUG SEE https://bugs.php.net/bug.php?id=51184 
		
		//simple example 		actual result +2, real result +6015
		
		$format = 'Y-m-d';
		$datetime1 = DateTime::createFromFormat($format, '2009-10-11');
		$datetime2 = DateTime::createFromFormat($format, '2009-10-13');
		$interval = date_diff($datetime1, $datetime2);
		echo $interval->format('%R%a days');
		
		//simple example end
		
		/*
		$date = date_create($s); 
		$datediff = date_diff( date_create($e), $date); print_r($datediff);
		$days = $datediff->format("%a");
		
		echo '<br>'.$id.'  '.$days.'  '.$date.'  '.$datediff.' | '.$s.' | '.$e;
		/*for ($i=0; $i<=$days; $i++)
		{
			$events[date('y-m-d',$date)][]=array('id'=>$id,'label'=>$l);
			$date = $date + (60*60*24);
		}*/
		
	}}

}

function getTimeInterval($buchung) {
  $a=preg_replace('/:00$/','',$buchung['zeit_beginn'])." - "
    .preg_replace('/:00$/','',$buchung['zeit_ende']);
  return $a;
}

function getDatum($datum) {
  /* Macht aus Datum yyyy-mm-dd einen String dd.mm.yyy */
  $uhu=explode("-",$datum);
  return $uhu[2].".".$uhu[1].".".$uhu[0];  
}

function printEventsPerDay($datum) 
{
  global $sp_events;
  $datstring=getDatum($datum);
  $bu="";
  foreach($sp_events[$datum] as $k => $v) { 
	$bu.='<li><a href="'.get_param_url(array('eid'=>$v['id'])).'#buchungstabelle">'.$v['label']."</a></li>";
  }
  $out='<div id="buchungsinfo">
  <p>Am <b>'.$datstring.'</b> ' ;
  if ($bu) { 
    $out.=' finden folgende Events statt:</p>
  <ul>'.$bu.'</ul></div>';}
  else {
    $out.=' liegen keine Daten zu Events vor.</div>';
  }
  return $out;

}

function printEventInfo($eid) 
{
	$query = 
		'	PREFIX ld: <http://leipzig-data.de/Data/Model/>
			select distinct *
			Where 	{ 
						<'.$eid.'> ?p ?o 
					}';
			
	$r = run_query($query,'select');
	$r = transformEventData($r['results']['bindings']);
	$table ='<div id="EventInfoHead">Informationen zum Event:</br> '.$r['rdf-schema#label']['value'].'</div><table cellspacing="0" id="EventInfo">';
	foreach ( $r as $k => $v)
	{
		$table.='<tr class="EventInfoRow"><td class="EventInfoPredicate">'.$k.'</td class="EventInfoObject"><td>'.printResource($v).'</td></tr>';
	}
	$table.='</table>';
	return $table;
}

function printResource($v)
{
	if ($v['type']=='uri')
		return '<a href="'.$v['value'].'">'.$v['value'].'</a>';
	else
		return $v['value'];
		
}

function displayKalenderGet($atts) {
  $action=$atts['action'];
  global $url; $url= curPageURL(); ####! kann weg
  global $sp_events;

  /* GET Parameter auslesen, wenn vorhanden. Diese werden innerhalb des Short
   Code in verschiedenen Links verwendet, um den Kalenderteil in einem anderen
   Zustand neu aufzurufen.  */

  $tag   = (empty($_GET['sday']))   ?   0       : $_GET['sday'] ; 
  $monat = (empty($_GET['smonth'])) ? date('n') : $_GET['smonth'] ;
  $jahr  = (empty($_GET['syear']))  ? date('Y') : $_GET['syear'] ;
  $eid   = (empty($_GET['eid']))   ? false     : $_GET['eid'] ; 

  fetch_events($jahr,$monat);
  
  $link_alt_monat=$monat-1;
  $link_alt_jahr=$jahr;
  $link_neu_monat=$monat+1;
  $link_neu_jahr=$jahr;

  if($monat==1){ $link_alt_monat=12; $link_alt_jahr=$jahr-1; }
  if($monat==12){ $link_neu_monat=1; $link_neu_jahr=$jahr+1; }

  $namen=array("0", "Januar", "Februar", "März", "April", "Mai", "Juni", 
	       "Juli", "August", "September", "Oktober", "November",
	       "Dezember");
		   
		   
  $perm = curPageURL(); /*echo $perm;*/ //print_r($_GET); #####!
  $out= '<table id="sparqalendar"><tr><td><div id="buchungstabelle">
   <table cellspacing="0">
     <tr>
       <th class="termin" id="letzterMonat">
		<a href="'.get_param_url(array('syear'=>$link_alt_jahr,'smonth'=>$link_alt_monat,'sday'=>'0','eid'=>'')).'#buchungstabelle">&larr;</a>
	   </th>
       <th colspan="5" id="monat">'.$namen[$monat].' '.$jahr.'
	   </th>
       <th class="termin" id="naechsterMonat">
		<a href="'.get_param_url(array('syear'=>$link_neu_jahr,'smonth'=>$link_neu_monat,'sday'=>'0','eid'=>'')).'#buchungstabelle">&rarr;</a>
	   </th>
     </tr>

     <tr id="Tageleiste"> <th>Mo</th> <th>Di</th> <th>Mi</th> <th>Do</th> <th>Fr</th>
       <th>Sa</th> <th>So</th> 
     </tr>
';

  // Tagzählung erfolgt nach Julian Days
  $erster_tag = gregoriantojd($monat,date('j'),$jahr)-date('j')+1;
  $tage=cal_days_in_month(CAL_GREGORIAN, $monat, $jahr); 
  $letzter_tag=$erster_tag+$tage-1;

  $out.= "<tr>";
  for($i=0;$i<$erster_tag%7;$i++){ $out.="<td>&nbsp;</td>"; }

  for($i=$erster_tag;$i<=$letzter_tag;$i++){
    $date = cal_from_jd($i, CAL_GREGORIAN);
	$dt=convert_date($jahr,$monat,$date['day']);
  //  $anmeldungen=fetch_events($date['year'],$date['month']);
		
    /* Trage nun den Tag in den Kalender ein */
    if($i%7==6 or $i==2454953 or $i>2454902 and $i<2454941){
	// Tage, an denen die Inspirata geschlossen ist
		if ($sp_events[$dt])
			$out.='
		<td class="zu">  
		  <a class="tabelle" href="'.get_param_url(array('syear'=>$date['year'],'smonth'=>$date['month'],'sday'=>$date['day'],'eid'=>'')).'#buchungstabelle">'
		  .$date['day'].'</a></td>';
	  else 
			$out.='
		<td class="zu">  
		  '.$date['day'].'</td>';	  
    }
    else{
      // Buchungen zu diesem Tag liegen vor. GET-Parameter, um im nächsten
      // Schritt Buchungen für dieses $datum listen zu lassen.
      if($sp_events[$dt]){	
	$out.='
    <td class="teilfrei">
      <a class="tabelle" href="'.get_param_url(array('syear'=>$date['year'],'smonth'=>$date['month'],'sday'=>$date['day'],'eid'=>'')).'#buchungstabelle">'
	  .$date['day'].'</a></td>';
      }
      else { $out.='<td class="frei">'.$date['day'].'</td>'; }
    }

    if($i%7==6){ $out.='</tr><tr>'; }
  }

  for($i=6;$i>$letzter_tag%7;$i--){ $out.='<td>&nbsp;</td>'; }
  $out.='</tr></table></div></td>'; // Ende buchungstabelle linke Seite

  if     ($eid) { $out.='<td>'.printEventInfo($eid).'</td></tr></table>'; }
  elseif ($tag) { $out.='<td>'.printEventsPerDay(convert_date($jahr,$monat,$tag)).'</td></tr></table>'; }
  else {$out.='</td></tr></table>'; }
  

  return "<div>$out</div>";

  }

 /** used to retrieve the current requested url and (re)set the given parameters in that uri
     while reusing the parameters of the old request, which have not been changed #### workaround for blogs without permalinks */
 function get_param_url($param_array)
 {
	$GET = $_GET;
	foreach ($param_array as $k => $v) 	//set new parameters and override the old ones if neccessary
	{
		$GET[$k]=$v;
	}
	
	$b = strpos(curPageURL(),'?');
	if ($b ===false)
		$u = curPageURL().'?';
	else
		$u = substr(curPageURL(),0, $b).'?';
	foreach ($GET as $k => $v)			//append the parameters to url
	{
		$u = $u.$k.'='.urlencode($v).'&';
	}
	return $u;
 }
  
 /** retrieve the current requested url */
 function curPageURL() {
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER['REQUEST_URI'];
	 } 
	 else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI'];
	 }
	 return $pageURL;
}
?>
