<?php
if(isset($_REQUEST['entryid']) && $_REQUEST['entryid']!='') {
  global $wpdb;
  $data = $wpdb->get_row( "SELECT * FROM `wp_historico` WHERE id = '".$_REQUEST['entryid']."'" );
?>
  <div class="wrap wqmain_body">
    <h3 class="wqpage_heading">Actualizar entrada</h3>
    <div class="wqform_body">
      <form name="update_form" id="update_form">
        <input type="hidden" name="wqentryid" id="wqentryid" value="<?=$_REQUEST['entryid']?>" />
        <div class="wqlabel">Ciudad</div>
        <div class="wqfield">
          <input type="text" class="wqtextfield" name="wqtitle" id="wqtitle" placeholder="Ingrese el nombre de la ciudad" value="<?=$data->title?>" />
        </div>
        <div id="wqtitle_message" class="wqmessage"></div>

        <div>&nbsp;</div>

        <div class="wqlabel">Description</div>
        <div class="wqfield">
          <textarea name="wqdescription" class="wqtextfield" id="wqdescription" placeholder="Ingrese su descripcion"><?=$data->description?></textarea>
        </div>
        <div id="wqdescription_message" class="wqmessage"></div>

        <div>&nbsp;</div>

        <div><input type="submit" class="wqsubmit_button" id="wqedit" value="Actualizar" /></div>
        <div>&nbsp;</div>
        <div class="wqsubmit_message"></div>

      </form>
    </div>
  </div>
<?php
} else {
?>
<div class="wrap wqmain_body">
  <h3 class="wqpage_heading">Nueva entrada</h3>
  <div class="wqform_body">
    <form name="entry_form" id="entry_form">

      <div class="wqlabel">Ciudad</div>
      <div class="wqfield">
        <input type="text" class="wqtextfield" name="wqtitle" id="wqtitle" placeholder="Ingrese el nombre de la ciudad" value="" />
      </div>
      <div id="wqtitle_message" class="wqmessage"></div>

      <div>&nbsp;</div>

      <div class="wqlabel">Descripcion</div>
      <div class="wqfield">
        <textarea name="wqdescription" class="wqtextfield" id="wqdescription" placeholder="Ingrese su descripcion"></textarea>
      </div>
      <div id="wqdescription_message" class="wqmessage"></div>

      <div>&nbsp;</div>

      <div><input type="submit" class="wqsubmit_button" id="wqadd" value="Adicionar" /></div>
      <div>&nbsp;</div>
      <div class="wqsubmit_message"></div>

    </form>
  </div>
</div>
<?php } ?>
