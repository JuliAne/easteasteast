<?php  $account = menu_get_object('user');
//print_r($account); ?>

<div class="row">

  <div class="large-2 columns">
   <img src="<?= base_path().path_to_theme(); ?>/img/project_bg.png" />
  </div>

 <div class="large-9 large-offset-1 columns">
<h3>Willkommen zurück, <strong><?= $account->name; ?></strong></h3>
<a id="editProfileBtn" href="<?= base_path().'user/'.$account->uid.'/edit'; ?>" class="secondary hollow button right">Profil bearbeiten.</a>
<div class="divider" style="margin:15px 0;"></div>

<p>Du bist registriertes Mitglied bei den Leipziger Ecken seit <strong><?php echo format_interval(REQUEST_TIME - $account->created); ?></strong> unter der Mail-Adresse <a href="mailto:<?= $account->mail; ?>"><?= $account->mail; ?></a></p>
</div>

</div>

<div class="row" style="margin-top:20px;">

 <div class="large-9 right">

  <ul class="accordion" data-accordion>
  <li class="accordion-item is-active" data-accordion-item>
    <a href="#" class="accordion-title"><?= t('Hinweise & Tipps'); ?></a>
    <div class="accordion-content" data-tab-content>
      <img src="<?= base_path().path_to_theme(); ?>/img/megaphone.svg" style="width:25px;margin-right:15px;" />Hinweise & Tipp's:</h5>
      <ul style="padding:12px;font-size:0.9em;">
       <li>...Einige Deiner eingestellten Events sind vergangen. Möchtest Du Sie <a href="#">wiederholen?</a></li>
       <li>...Wusstest Du schon, dass Du bestehende RSS-Feeds bequem in Dein <a href="#">Akteursprofil eintragen kannst</a>?</li>
      </ul>
    </div>
  </li>

  <li class="accordion-item" data-accordion-item>
    <a href="#" class="accordion-title"><?= t('Meine Akteure'); ?></a>
    <div class="accordion-content" data-tab-content>
      <table>
      <thead>
        <tr>
          <th width="250"><?= t('Akteur'); ?></th>
          <th width="100"><?= t('Zugriffe'); ?></th>
          <th width="200"><?= t('Registriert / Bearbeitet'); ?></th>
          <th width="50"><?= t('Aktionen'); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Content Goes Here</td>
          <td>This is longer content Donec id elit non mi porta gravida at eget metus.</td>
          <td>Content Goes Here</td>
          <td>Content Goes Here</td>
        </tr>
        <tr>
          <td>Content Goes Here</td>
          <td>This is longer Content Goes Here Donec id elit non mi porta gravida at eget metus.</td>
          <td>Content Goes Here</td>
          <td>Content Goes Here</td>
        </tr>
        <tr>
          <td>Content Goes Here</td>
          <td>This is longer Content Goes Here Donec id elit non mi porta gravida at eget metus.</td>
          <td>Content Goes Here</td>
          <td>Content Goes Here</td>
        </tr>
      </tbody>
    </table>
    </div>
  </li>

    <li id="acooEvents" class="accordion-item" data-accordion-item>
      <a href="#" class="accordion-title">Meine Events</a>
      <div class="accordion-content" data-tab-content>
        <p class="secondary"><i>Du hast noch keine Events erstellt.</i></p>
        <a href="#" class="button medium large-12"><?= t('Event erstellen'); ?></a>
      </div>
    </li>

</ul>

<div id="widget-invite" class="large-5 columns callout secondary">
 <h5>Platzhalter Einladen via social network</h5>
 </form>
</div>

<div id="widget-invite" class="large-5 columns callout secondary">
 <h5>Einladen via Mail</h5>
 <p>Hinterlasse Deinen Kollegen oder Freunden eine Einladung für die Leipziger Ecken. Ganz unverbindlich natürlich.</p>
 <form method="post" action="#">
  <input type="text" placeholder="kontakt@...de" />
  <input type="submit" class="button hollow medium" value="Einladen" />
 </form>
</div>

</div>

</div>
