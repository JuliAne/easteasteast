<div class="row">

<h3 class="large-4 columns"><strong><?= $itemsCount; ?> Akteure</strong><?= $pageNr; ?></h3>

<?php if(user_is_logged_in()) : ?>
  <a class="small button round right" href="<?= base_path(); ?>?q=Akteurformular">+ Akteur hinzufügen</a><br />
<?php endif; ?>

</div>

<div class="row" style="padding-bottom: 20px;">
  <div class="divider"></div>

<?php foreach($resultAkteure as $akteur): ?>
  <div class="large-3 large-offset-1 columns pcard">
   <header <?php if($akteur->bild != '') echo 'style="background:url('.base_path.$modulePath.'/'.$akteur->bild.');"'; ?>>
     <h3><a href="<?= base_path().'?q=Akteurprofil/'.$akteur->AID; ?>"><?= $akteur->name; ?></a></h3>
    </header>
    <section>
     <p><?= $akteur->kurzbeschreibung; ?></p>
     <p><a href="<?= base_path(); ?>?q=Akteurprofil/<?= $akteur->AID; ?>">Zum Projekt...</a></p>
    </section>
   </div>
<?php endforeach; ?>

</div>

<div class="divider"></div>

<div class="row">
  <ul class="pagination large-4 columns large-offset-5" style="padding-top:15px;">
   <li class="arrow unavailable"><a href="">&laquo;</a></li>
   <li class="current"><a href="">1</a></li>
   <li><a href="">2</a></li>
   <!-- <li class="unavailable"><a href="">&hellip;</a></li>-->
   <li class="arrow"><a href="">&raquo;</a></li>
 </ul>
</div>
