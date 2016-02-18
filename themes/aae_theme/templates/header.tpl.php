<div id="mainnav">
 <div class="row">

 <nav id="nav" role="navigation">

  <!-- for responsive menu (CSS only) -->
  <input type="checkbox" id="responsive-menu" name="responsive-menu" class="show-for-small-only" title="Zeige/Verstecke Menü">
  <label for="responsive-menu" onclick></label>

  <a href="<?= base_path(); ?>"><img id="logo" class="left" src="<?= base_path().path_to_theme(); ?>/logo.png" /></a>

  <?php print render($page['mainnav']); ?>

 </nav>

  <aside id="actions" class="large-1 small-2 columns panel radius right">
   <a id="search-button" href="#search-popup" class="popup-link" title="Suchen"><img src="<?= base_path().path_to_theme(); ?>/img/search.svg" /></a>
    <div id="search-popup" class="popup large-3 small-12 columns">
     <!-- <input type="text" placeholder="Suchen..." />
     <input type="submit" class="button secondary isSubmitButton" value="" /> -->
     <?php print drupal_render(drupal_get_form('search_block_form', TRUE)); ?>
    </div>

   <a id="login-button" href="#login-popup" class="popup-link" title="Einloggen"><img src="<?= base_path().path_to_theme(); ?>/img/user.svg" /></a>
    <div id="login-popup" class="popup small-12 columns">

     <?php if (!user_is_logged_in()) : ?>
      <ul class="tabs large-12 columns" data-tabs id="mainnav-tabs">
       <li class="tabs-title is-active"><a href="#panelLogin" aria-selected="true">Login</a></li>
       <li class="tabs-title"><a href="#panelRegister">Registrieren</a></li>
      </ul>
      <?php else : ?>
      <h5 style="padding-top: 8px;">Hallo, <strong><?php global $user; echo $user->name; ?></strong>.</h5>
      <div class="divider"></div>

        <?php require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
              $blocks = new aae_blocks();

              foreach($blocks->print_my_akteure() as $myAkteur) : ?>

        <p><a href="<?= base_path(); ?>akteurprofil/<?= $myAkteur[0]->AID; ?>"><?= $myAkteur[0]->name; ?></a></p>

        <?php endforeach; ?>

        <a href="<?= base_path(); ?>akteurformular" class="large-12 columns small button">+ Akteur erstellen</a>
     <?php endif; ?>

     <div class="divider"></div>
     <?php if(!user_is_logged_in()) : ?>
     <div class="tabs-content" data-tabs-content="mainnav-tabs">
      <div class="tabs-panel is-active" id="panelLogin">
       <?php print render($page['user_region']); ?>
       <?php if (module_exists('simple_fb_connect')) : ?>
       <a href="<?= base_path(); ?>user/simple-fb-connect" class="small button large-12 columns hollow" title="Mit Facebook anmelden"><img src="<?= base_path().path_to_theme(); ?>/img/social-facebook-blue.svg" />Mit Facebook anmelden</a>
       <?php endif; ?>
       <p><a href="<?= base_path(); ?>user/password">Passwort vergessen?</a></p>
      </div>
      <div class="tabs-panel" id="panelRegister">
       <p><strong>Neu hier?</strong> Registriere Dich kostenfrei, um die Leipziger Ecken voll nutzen zu können und zu einem schöneren Platz für uns alle zu machen. <a href="<?= base_path(); ?>faq" title="FAQ öffnen">Wieso & warum?</a></p><br />
       <a href="<?= base_path(); ?>user/register" class="large-12 columns small button" title="Jetzt registrieren">Registrieren</a>
       <?php if (module_exists('simple_fb_connect')) : ?>
       <a href="<?= base_path(); ?>user/simple-fb-connect" class="large-12 columns small hollow button" title="Mit Facebook registrieren"><img src="<?= base_path().path_to_theme(); ?>/img/social-facebook-blue.svg" />Mit Facebook registrieren</a><p>
       <?php endif; ?>
      </div>
     </div>
     <?php else : ?>
     <p><a href="<?= base_path(); ?>user/<?= $user->uid; ?>/edit">Einstellungen</a> | <a href="<?= base_path(); ?>user/logout">Logout</a></p>
     <?php endif; ?>


    </div>
  </aside>

 </div>
</div>
