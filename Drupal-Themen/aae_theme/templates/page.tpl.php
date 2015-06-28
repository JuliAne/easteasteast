<script src="<?= base_path().path_to_theme(); ?>/LOdata.js"></script>

<div id="mainnav">
 <div class="row">

 <nav id="nav" role="navigation">
  <a href="#nav" class="show-for-small-only" title="Show navigation">&#9776;</a>
  <a href="#" class="show-for-small-only" title="Hide navigation">&#9776;</a>

  <?php if ($main_menu): ?>
    <ul class="large-8 small-8 columns">
      <?php print theme('links__system_main_menu', array(
        'links' => $main_menu,
        'attributes' => array(
          'id' => 'main-menu-links',
          'class' => array('links', 'clearfix'),
        )
      )); ?>
    </ul>
  <?php endif; ?>

 </nav>

  <aside id="actions" class="large-1 small-2 columns panel radius right">
   <a id="search-button" href="#search-popup" class="popup-link" title="Suchen"><img src="<?= base_path().path_to_theme(); ?>/img/search.svg" /></a>
    <div id="search-popup" class="popup large-3 columns">
     <input type="text" placeholder="Suchen..." />
     <input type="submit" class="button secondary isSubmitButton" value="" />
    </div>

   <a href="#login-popup" class="popup-link" title="Einloggen"><img src="<?= base_path().path_to_theme(); ?>/img/user.svg" /></a>
    <div id="login-popup" class="popup large-3 columns">
     <h4>Login</h4>
     <form>
      <input type="text" placeholder="Benutzername" />
      <input type="password" placeholder="Passwort" />
      <input type="submit" class="small button" value="Einloggen" />
     </form>

     <div class="divider"></div>

     <p>Neu hier? <a href="#">Registrieren</a></p>
     <p><a href="#">Passwort vergessen?</a></p>

     <div class="divider"></div>
    </div>
  </aside>
 </div>
</div>

<div id="fullpage">

 <header class="section" id="header">

  <div id="intro" class="row">
   <h1>Den <strong>Leipziger Osten</strong> neu entdecken.</h1>
   <div id="introtext">
   <p>Deine Plattform f&uuml;r den ganzen Stadtteil: Lerne <strong>kreative Projekte</strong> aus Deiner Umgebung kennen & erfahre, wann und wo sich etwas in Deinem Bezirk bewegt!</p>
   <p>Kostenlos, offen, lokal.</p>
   </div>
   <a href="#pageProjects"><button class="button radius">Zu den <strong>Projekten</strong></button></a>
   <a href="#"><button class="button radius secondary"><strong>Veranstaltungen</strong></button></a>
  </div>

	<div id="map"></div>
 </header>

 <section class="section" id="projects">

  <h1>Neueste <strong>Projekte</strong></h1>



	<div class="row">
    <?php print render($page['content']); ?>
  </div>

  <h1>N&auml;chste <strong>Veranstaltungen</strong></h1>

  <div class="row">

   <div class="large-3 large-offset-1 columns event">
    <a href="#"><button class="button blue date">08<br />Sept</button></a>
    <a href="#"><h4>Cosplay Workshop</h4></a>
    <aside><a href="#">
     <img src="img/location.svg" />UT Connewitz <br/>
     <img src="img/clock.svg" /><strong>8:00</strong> - <strong>12:30</strong></p>
    </a></aside>
   </div>

   <div class="large-3 large-offset-1 columns event">
    <a href="#"><button class="button blue date">08<br />Sept</button></a>
    <a href="#"><h4>Cosplay Workshop</h4></a>
    <aside><a href="#">
     <img src="img/location.svg" style="width: 15px;" />UT Connewitz <br/>
     <img src="img/clock.svg" style="width: 15px;" /><strong>8:00</strong> - <strong>12:30</strong></p>
    </a></aside>
   </div>

   <div class="large-3 large-offset-1 columns event">
    <a href="#"><button class="button blue date">08<br />Sept</button></a>
    <a href="#"><h4>Cosplay Workshop</h4></a>
    <aside><a href="#">
     <img src="img/location.svg" style="width: 15px;" />UT Connewitz <br/>
     <img src="img/clock.svg" style="width: 15px;" /><strong>8:00</strong> - <strong>12:30</strong></p>
    </a></aside>
   </div>

  </div>

 </section>

 <section class="section" id="bottom">

  <div class="row" id="teaser">
	 <h1><strong>45</strong> Initiativen, <strong>213</strong> Veranstaltungen, <strong>eine</strong> Plattform.</h1>

   <p>Mach' mit! Stelle jetzt Dein Projekt ein oder mische als registrierter Nutzer mit:</p>

   <a href="#"><button class="button radius">Registrieren</button></a>
   <a href="#"><button class="button radius secondary">Anmelden</button></a>

  </div>


 </section>

 <footer class="section" id="footer">
  <div class="row">

  <div class="large-4 columns" id="about">
   <h4>&Uuml;ber den Stadtteil.</h4>
   <p>Der <strong>Leipziger Ostens</strong> besteht aus neuen Bezirken mit insgesamt 205.000 Einwohnern. Lorem ipsum sed dolor sit amet blakeks und so weiter hier kommt wohl noch was, damit der User in k&uuml;rze bescheid wei&szlig;, was wir hier machen...</p>
   <p><strong>Weitere Informationen:</strong><br />
   <a href="#">About</a> |
   <a href="#">FAQ</a> |
   <a href="#">Blog</a> |
   <a href="#">Kontakt</a>
   </p>
  </div>

  <div class="large-4 columns" id="mini-nav">
   <h4>Navigation.</h4>

   <ul class="side-nav">
    <li class="active"><a href="#">Home</a></li>
    <li><a href="#">Projekte</a></li>
    <li><a href="#">Events</a></li>
    <li><a href="#">Leipziger Osten</a></li>
   </ul>

   <form method="post" action="#">
    <input type="text" placeholder="Suche..."/>
    <input type="submit" class="button secondary isSubmitButton" value="" />
   </form>

  </div>

  <div id="mini-calendar" class="large-4 columns">
   <h4>Kalender.</h4>
   <div class="month-view">
    <div class="date-nav-wrapper clear-block">
     <div class="date-nav">
      <div class="date-heading">
      <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/month/2015-06" title="View full page month">Juni 2015</a>
      </div>
     </div>
    </div>

    <table class="mini">
     <thead>
      <tr>
              <th class="days mon">
          M        </th>
              <th class="days tue">
          T        </th>
              <th class="days wed">
          W        </th>
              <th class="days thu">
          T        </th>
              <th class="days fri">
          F        </th>
              <th class="days sat">
          S        </th>
              <th class="days sun">
          S        </th>
          </tr>
      </thead>
      <tbody>
          <tr>
                  <td id="calendar-2015-06-01" class="mon mini today has-no-events">
            <div class="year mini-day-off"> 1 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-02" class="tue mini future has-events">
            <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-02">2</a> </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-03" class="wed mini future has-events">
            <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-03">3</a> </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-04" class="thu mini future has-events">
            <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-04">4</a> </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-05" class="fri mini future has-events">
            <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-05">5</a> </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-06" class="sat mini future has-events">
            <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-06">6</a> </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-07" class="sun mini future has-events">
            <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-07">7</a> </div><div class="calendar-empty">&nbsp;</div>
          </td>
              </tr>
          <tr>
                  <td id="calendar-2015-06-08" class="mon mini future has-no-events">
            <div class="year mini-day-off"> 8 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-09" class="tue mini future has-events">
            <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-09">9</a> </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-10" class="wed mini future has-no-events">
            <div class="year mini-day-off"> 10 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-11" class="thu mini future has-no-events">
            <div class="year mini-day-off"> 11 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-12" class="fri mini future has-no-events">
            <div class="year mini-day-off"> 12 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-13" class="sat mini future has-no-events">
            <div class="year mini-day-off"> 13 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-14" class="sun mini future has-no-events">
            <div class="year mini-day-off"> 14 </div><div class="calendar-empty">&nbsp;</div>
          </td>
              </tr>
          <tr>
                  <td id="calendar-2015-06-15" class="mon mini future has-no-events">
            <div class="year mini-day-off"> 15 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-16" class="tue mini future has-no-events">
            <div class="year mini-day-off"> 16 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-17" class="wed mini future has-no-events">
            <div class="year mini-day-off"> 17 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-18" class="thu mini future has-no-events">
            <div class="year mini-day-off"> 18 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-19" class="fri mini future has-no-events">
            <div class="year mini-day-off"> 19 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-20" class="sat mini future has-no-events">
            <div class="year mini-day-off"> 20 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-21" class="sun mini future has-no-events">
            <div class="year mini-day-off"> 21 </div><div class="calendar-empty">&nbsp;</div>
          </td>
              </tr>
          <tr>
                  <td id="calendar-2015-06-22" class="mon mini future has-no-events">
            <div class="year mini-day-off"> 22 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-23" class="tue mini future has-no-events">
            <div class="year mini-day-off"> 23 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-24" class="wed mini future has-no-events">
            <div class="year mini-day-off"> 24 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-25" class="thu mini future has-no-events">
            <div class="year mini-day-off"> 25 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-26" class="fri mini future has-no-events">
            <div class="year mini-day-off"> 26 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-27" class="sat mini future has-no-events">
            <div class="year mini-day-off"> 27 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-28" class="sun mini future has-no-events">
            <div class="year mini-day-off"> 28 </div><div class="calendar-empty">&nbsp;</div>
          </td>
              </tr>
          <tr>
                  <td id="calendar-2015-06-29" class="mon mini future has-no-events">
            <div class="year mini-day-off"> 29 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-06-30" class="tue mini future has-no-events">
            <div class="year mini-day-off"> 30 </div><div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-07-01" class="wed mini empty">
            <div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-07-02" class="thu mini empty">
            <div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-07-03" class="fri mini empty">
            <div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-07-04" class="sat mini empty">
            <div class="calendar-empty">&nbsp;</div>
          </td>
                  <td id="calendar-2015-07-05" class="sun mini empty">
            <div class="calendar-empty">&nbsp;</div>
          </td>
              </tr>
      </tbody>
</table>
</div>
  </div>

 </div>
</section>

</div>
