<?php
/**
 * profilePage.php generates a HTML Body for Drupal to paste on call.
 * If the user set any user data before, PHP manages to retrieve those data, and show them.
 * If not, there will be some nice placeholders, and changes will be inserted or updated. Depending
 * wheater you already set data or not.
 *
 *
 * Watte, 11:47 29-01-2015
 */


//$pathThisFile = $_SERVER['REQUEST_URI'];
if (easyrdf()) {
  // Do something with EasyRdf... e.g.
  $graph = new EasyRdf_Graph("http://www.bbc.co.uk/music/artists/70248960-cb53-4ea4-943a-edb18f7d336f.rdf");
}
$uri = $graph->getURI();
$dump = $graph->dump('text');


$profileHTML = <<<EOF
<div id="aae_data">
<h5>aae_data</h5>
<p>Graph: $graph</p><br>
<p>URI: $dump</p><br>
<p>Hallihallo!</p>
</div>
EOF;
