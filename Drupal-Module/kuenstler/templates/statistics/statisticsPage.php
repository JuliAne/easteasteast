<?php
/**
 * statisticsPage.php generates a HTML Body for Drupal to paste on call. 
 * It will contain some Facebook-Like information about the users facebookpage.
 * 
 * Watte: 13;39, 29-01-2015
 */

include $modulePath . "/templates/facebook.php";




//Define output. Google Maps JavaScript stuff.
//<editor-fold>
//add your Google API Key here
$statisticsHTMLMap = <<<EOF
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE&sensor=FALSE"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="sites/all/modules/kuenstler/templates/statistics/fanlocation.js"></script>
<div id="map_canvas">
        <div>
        öoihsdlkjhsdfs
        </div>
        <script type="text/javascript">initializeMap()</script>
</div>
EOF;
// </editor-fold>
