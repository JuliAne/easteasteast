<?php 

/* used to rename predicates/objects into readable strings and to sort the predicates list as required 
   and to convert the sparql select result in a normalized key-value array*/
function transformEventData($array)
{
	$r= array();
	foreach ($array as $k => $v)
	{
		$r[basename($v['p']['value'])]=$v['o'];
	}
	return $r;
} 

?>