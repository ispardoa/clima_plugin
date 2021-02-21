<?php

  $apykey = get_option( 'APIKEY', '' );
  $apykey_descripcion = get_option( 'APIKEY_descripcion', '' );
?>
<div class="wrap wqmain_body">
  <h3 class="wqpage_heading">Parametros</h3>
  <div class="wqform_body">
    <form name="parameters_form" id="parameters_form">

      <div class="wqlabel">Api Key [openweathermap.org]</div>
      <div class="wqfield">
        <input type="text" class="wqtextfield" name="wqtitle" id="wqtitle" placeholder="Ingrese al API KEY para ejecutar los servicios" value="<?=$apykey?>" />
      </div>
      <div id="wqtitle_message" class="wqmessage"></div>

      <div>&nbsp;</div>

      <div class="wqlabel">Descripcion</div>
      <div class="wqfield">
        <textarea name="wqdescription" class="wqtextfield" id="wqdescription" placeholder="Ingrese su descripcion"><?=$apykey_descripcion?></textarea>
      </div>
      <div id="wqdescription_message" class="wqmessage"></div>

      <div>&nbsp;</div>

      <div><input type="submit" class="wqsubmit_button" id="wqadd" value="Guardar" /></div>
      <div>&nbsp;</div>
      <div class="wqsubmit_message"></div>

    </form>
  </div>
</div>