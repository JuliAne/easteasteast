<aside id="alert" class="drupal-error" style="display:<?= (!empty($messages) && !strpos($messages, 'Warning') && !strpos($messages,'temporary') && !strpos($messages, 'session') ? 'block' : 'none'); ?>;">
 <div id="alert-content"><?php if(!empty($messages) && !strpos($messages, 'Warning') && !strpos($messages, 'session')) : ?>
<?php print($messages); ?><?php endif; ?></div>
 <a href="#" class="close button hollow closeBtn" title="<?= t('Schliessen'); ?>">x</a>
</aside>

<aside id="aaeModal" class="aaeModal">
 <div class="aaeModalWrapper">
  <div class="content"></div>
  <a href="#" class="button secondary round closeBtn" title="<?= t('Fenster schliessen'); ?>">x</a>
 </div>
</aside>

<div id="mainnav">
 <div class="row">

 <nav id="nav" role="navigation" class="large-8 small-8 columns">
  <!-- for responsive menu (CSS only) -->
  <input type="checkbox" id="responsive-menu" name="responsive-menu" class="show-for-small-only" title="<?= t('Zeige/Verstecke Menü'); ?>">
  <label for="responsive-menu" onclick="javascript:$('#mainnav').removeClass('scrolled');"></label>

  <a href="<?= base_path(); ?>"><img id="logo" class="left" src="<?= base_path().path_to_theme(); ?>/img/logo_new_new.png" alt="Leipziger Ecken Logo" title="<?= t('Zur Startseite'); ?>" /></a>

  <?php print render($page['mainnav']); global $user ?>
 </nav>

  <aside id="actions" class="large-1 small-4 medium-2 columns panel radius right">
   <a href="#" id="searchByLocation" title="<?= t('NEU: Alle Events & Akteure in meiner Nähe'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/location_white.svg" /></a>
   <a id="search-button" href="#search-popup" class="popup-link" title="Suchen"><img src="<?= base_path().path_to_theme(); ?>/img/search.svg" /></a>
    <div id="search-popup" class="popup large-3 small-12 columns">
     <!-- <input type="text" placeholder="Suchen..." />
     <input type="submit" class="button secondary isSubmitButton" value="" /> -->
     <?php print drupal_render(drupal_get_form('search_block_form', TRUE)); ?>
    </div>

   <a id="login-button" href="#login-popup" class="popup-link" title="<?= t('Einloggen'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/user.svg" /></a>
    <div id="login-popup" class="popup small-12 columns">

     <?php if (!user_is_logged_in()) : ?>
      <ul class="tabs large-12 columns" data-tabs id="mainnav-tabs">
       <li class="tabs-title is-active"><a href="#panelLogin" aria-selected="true"><?= t('Login'); ?></a></li>
       <li class="tabs-title"><a href="#panelRegister"><?= t('Registrieren'); ?></a></li>
      </ul>
      <?php else : ?>
      <h5 style="padding: 10px 0;"><?= t('Hallo, <strong>!username</strong>.', array('!username' => $user->name)); ?></h5>
      <div class="divider"></div>
      <?php require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
            $blocks = new Drupal\AaeData\aae_blocks();
            $myAkteure = $blocks->print_my_akteure();

            if (!empty($myAkteure)) : ?>
            <p><?= t('Meine Akteure'); ?>:</p>
            <?php foreach($myAkteure as $myAkteur) : ?>
            <p><a href="<?= base_path(); ?>akteurprofil/<?= $myAkteur[0]->AID; ?>"><?= $myAkteur[0]->name; ?></a></p>
        <?php endforeach; endif; ?>
        <a href="<?= base_path(); ?>akteure/new" class="large-12 columns small button" style="margin:5px 0;">+ <?= t('Akteur erstellen'); ?></a>
        <a href="<?= base_path(); ?>events/new" class="large-12 columns small hollow button">+ <?= t('Event erstellen'); ?></a>
     <?php endif; ?>

     <?php if(!user_is_logged_in()) : ?>
     <div class="tabs-content" data-tabs-content="mainnav-tabs">
      <div class="tabs-panel is-active" id="panelLogin">
       <?php print render($page['user_region']); ?>
       <?php if (module_exists('simple_fb_connect')) : ?>
       <a href="<?= base_path(); ?>user/simple-fb-connect" class="small button large-12 columns hollow" title="<?= t('Mit Facebook anmelden') ?>"><img src="<?= base_path().path_to_theme(); ?>/img/social-facebook-blue.svg" /><?= t('Mit Facebook anmelden');?></a>
       <?php endif; ?>
       <p><a href="<?= base_path(); ?>user/password" style="color:grey;"><?= t('Passwort vergessen?'); ?></a></p>
      </div>
      <div class="tabs-panel" id="panelRegister">
       <p><strong>Neu hier?</strong> Registriere Dich kostenfrei, um die Leipziger Ecken voll nutzen zu können und zu einem schöneren Platz für uns alle zu machen. <a href="<?= base_path(); ?>faq" title="FAQ öffnen">Zum "Wieso & warum?"</a></p><br />
       <a href="<?= base_path(); ?>user/register" class="large-12 columns small button" title="<?= t('Jetzt registrieren'); ?>"><?= t('Registrieren'); ?></a>
       <?php if (module_exists('simple_fb_connect')) : ?>
       <a href="<?= base_path(); ?>user/simple-fb-connect" class="large-12 columns small hollow button" title="<?= t('Mit Facebook registrieren'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/social-facebook-blue.svg" /><?= t('Mit Facebook registrieren'); ?></a><p>
       <?php endif; ?>
      </div>
     </div>
     <?php else : ?>
     <p><a href="<?= base_path(); ?>user/<?= $user->uid; ?>/edit" style="color:grey;"><?= t('Einstellungen'); ?></a> | <a href="<?= base_path(); ?>user/logout" style="color:grey;"><?= t('Logout'); ?></a></p><br />
     <?php endif; ?>

    </div>
  </aside>
  
  <?php if (user_is_logged_in() && module_exists('invite')) : ?>
  <a href="#" id="inviteBtn" style="display:none;" class="hollow secondary button right show-for-large" title="<?= t('Bekannte einladen'); ?>">+ <?= t('Bekannte einladen'); ?></a>
  <aside id="invite-modal" class="aaeModal">
   <div class="aaeModalWrapper">
    <div class="content">
    <h3>Spread the word.</h3>
    <p>Wie im Kiez, so auch im Web: Die <strong>Leipziger Ecken</strong> leben vom Engagement und der Vielfältigkeit ihrer Benutzer. Wenn Dir dieser Ort gefällt, dann teile ihn auf diesem Wege ganz komfortabel via Mail Deinen Bekannten mit.</p>
    <br />
    <p>Speicherung der Angaben nur zu Anmeldezwecken. Keine Weitergabe an Dritte. <a style="text-align:right;" href="<?= $base_path; ?>user/<?= $user->uid; ?>/invites">Offene Einladungen einsehen.</a></p>
    <div class="divider"></div>
    <?php print render($page['user_invite_by_mail']); ?>
    <a href="#" class="button secondary round closeBtn" title="<?= t('Fenster schliessen'); ?>">x</a>
    </div>
   </div>
 </aside>
  <?php endif; ?>

 </div>
</div>

<div id="karibu-modal" class="aaeModalX" style="display:none;">
 <div id="karibu">
  <div class="row">
    <div class="large-12 columns">
     <h3>Herzlich willkommen bei den <strong>Leipziger Ecken</strong>...</h3>
     <p class="headline">...Eurer freien Plattform für xy. Mit unserem wachsenden Angebot möchten wir Kreativität & Engagement durch gemeinsamen Austausch und Interaktion fördern. Hier ein paar erste Schritte:</p>
     <div class="button hollow closeBtn" title="<?= t('Fenster schließen'); ?>">x</div>
    </div>

    <div class="large-3 large-offset-1 columns">
     <h4><img src="<?= base_path().path_to_theme(); ?>/img/akteur_icon.png" />Akteurprofil</h4>
     <p>Stellt Eure Projekte unkompliziert der Öffentlichkeit zur Verfügung</p>
    </div>
    <div class="large-3 large-offset-1 columns">
     <h4><img src="<?= base_path().path_to_theme(); ?>/img/events_icon.png" />Events</h4>
     <p></p>
    </div>
    <div class="large-3 large-offset-1 columns">
     <h4><img src="<?= base_path().path_to_theme(); ?>/img/journal_icon.png" />Dranbleiben</h4>
     <p></p>
    </div>

  </div>
 </div>
</div>
