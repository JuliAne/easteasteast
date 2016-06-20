<?php
 /* First draft for the "About Leipziger Ecken"-page */
?>
<style type="text/css">
#leHeader { padding:18px 20px;margin:0 auto;margin-top:90px;height:226px;background:url('sites/all/themes/aae_theme/img/le_about.jpg');color:#fff;}
#leHeader h5 {font-size:1em;padding-top:3px;}
#leHeader p {padding-top:27px;width:550px; }
.row {max-width:1150px;}
.region-content {margin-top:50px;}
</style>
<div id="lePage" class="singlesite">

	<?php include_once('header.tpl.php'); ?>

	<div id="leHeader" class="row">
     <h3>Leipziger Ecken</h3>
	 <h5>Offen. lokal. vernetzt.</h5>
	 <p><strong>Die erste Stadtteilplattform für Initiativen, Projekte und Akteure aus dem Leipziger Osten.</strong> Mit der Seite wollen wir dem besonderen kulturellen Profil der Viertel des Leipziger Ostens eine neue Sichtbarkeit verleihen. Wie an einer Litfaßsäule können sich hier die (sozio)kulturellen, gemeinwohlorientierten Akteure kosten- und barrierefrei präsentieren. So wird den Nachbarn und Gästen der Quartiere der einfache Überblick über Akteure und Angebote ermöglicht. </p>
	</div>

	<div id="contentRow" class="row">
	<?php print render($page['content']); ?>
    </div>
	<?php include_once('footer.tpl.php'); ?>

</div>
