<?php

/**
*
* -- Einstellungen für den Startseite-Slider --
*
* Ein $slider-Listenelement = Ein horizontaler Slider
* Verwendung von HTML möglich - zur Trennung von Absätzen <br /> verwenden
*
* Mögliche Parameter (sonst jeweils default = 0):
*
*  'image' [Pfad.Dateiendung] = Hintergrundbild, welches in themes/aae/img zu liegen hat.
*  'headline' [string] = Überschrift (<h1>)
*
*  'description' [string] = Beschreibungstext (<p>)
*
*  'whiteText' [boolean] = Soll der Text in weiß dargestellt werden?
*
*  'blueButton' [array] = Zeige blauen Button an.
*     @example 'bluebutton' = array('text' => 'lorem ipsum', 'link' => '/events/all');
*
*  'whiteButton' [array] = s. bluebutton
*
*/

require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';

$blocks = new aae_blocks();
$counts = $blocks->count_projects_events();

$sliders = array(

 $slider1 = array(
   'image' => 'slider_bg.jpg',
   'headline' => 'Den Leipziger Osten entdecken',
   'description' => '<p>Deine Stadtteilplattform: Erfahre mehr über Vereine, Initiativen und Akteure in deiner Umgebung, </p>
    <p>werde Teil der Community und registriere dich.</p>
    <p class="slogan"><strong>Offen. Lokal. Vernetzt.</strong></p>',
   'whiteText' => true,
   'blueButton' => array(
     'text' => 'Mehr erfahren...',
     'link' => base_path().'faq'
    )
 ),

 $slider2 = array(
   'image' => 'slider_2_min.jpg',
   'headline' => '<strong>'.$counts['akteure'].'</strong> Akteure. <strong>'.$counts['events'].'</strong> Events.<br /> Eine Plattform.',
   'description' => '<p>Jetzt anmelden, Akteur werden, Veranstaltungen einstellen und einfach mitmischen.</p>',
   'whiteText' => true,
   'blueButton' => array(
     'text' => '<span style="color:white;">Jetzt registrieren!</span>',
     'link' => base_path().'user/register'
   )
 ),

 $slider3 = array(
   'image' => 'rabet_abends_ida.jpg',
   'headline' => 'Der Leipziger Osten',
   'description' => '<p>vom „Grafischen Viertel” über Neustadt-Neuschönefeld, Volkmarsdorf, Schönefeld bis Sellerhausen-Stünz</p>
    <p class="slogan"><strong>vernetzt und kooperativ für ein buntes Leipzig</strong></p>',
   'whiteText' => true,
   'blueButton' => array(
     'text' => 'Mehr erfahren...',
     'link' => base_path().'faq'
    )
 ),

  $slider4 = array(
   'image' => 'rabet_seil_kids_ida.jpg',
   'headline' => '<strong>'.$counts['akteure'].'</strong> Akteure. <strong>'.$counts['events'].'</strong> Events.<br /> Eine Plattform.',
   'description' => '<p>Jetzt anmelden, Projekt einstellen oder einfach nur mitmischen.</p>',
   'whiteText' => true,
   'blueButton' => array(
     'text' => '<span>Jetzt registrieren!</span>',
     'link' => base_path().'user/register'
   )
 )

);

?>
