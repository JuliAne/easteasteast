<div id="mainnav">
 <div class="row">

 <nav id="nav" role="navigation">
  <a href="#nav" class="show-for-small-only" title="Show navigation">&#9776;</a>
  <a href="#" class="show-for-small-only" title="Hide navigation">&#9776;</a>

  <a href="<?= base_path(); ?>"><img id="logo" class="left" src="<?= base_path().path_to_theme(); ?>/logo.png" /></a>

  <?php print render($page['mainnav']); ?>

 </nav>

  <aside id="actions" class="large-1 small-2 columns panel radius right">
   <a id="search-button" href="#search-popup" class="popup-link" title="Suchen"><img src="<?= base_path().path_to_theme(); ?>/img/search.svg" /></a>
    <div id="search-popup" class="popup large-3 columns">
     <!-- <input type="text" placeholder="Suchen..." />
     <input type="submit" class="button secondary isSubmitButton" value="" /> -->
     <?php print drupal_render(drupal_get_form('search_block_form', TRUE)); ?>
    </div>

   <a href="#login-popup" class="popup-link" title="Einloggen"><img src="<?= base_path().path_to_theme(); ?>/img/user.svg" /></a>
    <div id="login-popup" class="popup large-3 columns">

     <?php if(!user_is_logged_in()) print render($page['user_region']);
           else { ?>
        <h5 style="padding-top: 8px;">Hallo, <strong><?php global $user; echo $user->name; ?></strong>.</h5>
        <div class="divider"></div>

        <?php require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
              foreach(block_aae_print_my_akteure() as $myAkteur) : ?>

        <p><a href="<?= base_path().$myAkteur->AID; ?>"><?= $myAkteur->name; ?></a></p>

        <?php endforeach; ?>

        <a href="<?= base_path(); ?>Akteurformular" class="small button">+ Akteur erstellen</a>
     <?php } ?>

     <div class="divider"></div>

     <?php if(!user_is_logged_in()) : ?>
     <p>Neu hier? <a href="<?= base_path(); ?>user/register">Registrieren</a></p>
     <p><a href="<?= base_path(); ?>user/password">Passwort vergessen?</a></p>
     <?php else : ?>
     <p><a href="<?= base_path(); ?>user">Profil</a></p>
     <p><a href="<?= base_path(); ?>user/logout">Logout</a></p>
     <?php endif; ?>


    </div>
  </aside>

 </div>
</div>
