<?php
add_action('wp_ajax_wqnew_entry', 'wqnew_entry_callback_function');
add_action('wp_ajax_nopriv_wqnew_entry', 'wqnew_entry_callback_function');

function wqnew_entry_callback_function() {
  global $wpdb;
  $wpdb->get_row( "SELECT * FROM `wp_historico` WHERE `title` = '".$_POST['wqtitle']."' AND `description` = '".$_POST['wqdescription']."' ORDER BY `id` DESC" );
 
  $apykey = get_option( 'APIKEY', '' );
  $clima = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=".$_POST['wqtitle']."&appid=$apykey");
  $json = json_decode($clima);

  
  if($wpdb->num_rows < 1) {
    $wpdb->insert("wp_historico", array(
      "title" => $_POST['wqtitle'],
      "description" => $_POST['wqdescription'],
      "temp"        => $json->main->temp,
      "temp_min"    => $json->main->temp_min,
      "temp_max"    => $json->main->temp_max,
      "pressure"    => $json->main->pressure,
      "humidity"    => $json->main->humidity,      
      "created_at" => time(),
      "updated_at" => time()
    ));

    $response = array('message'=>'Los datos se han insertado correctamente', 'rescode'=>200);
  } else {
    $response = array('message'=>'Los datos ya existen', 'rescode'=>404);
  }
  echo json_encode($response);
  exit();
  wp_die();
}



add_action('wp_ajax_wqedit_entry', 'wqedit_entry_callback_function');
add_action('wp_ajax_nopriv_wqedit_entry', 'wqedit_entry_callback_function');

function wqedit_entry_callback_function() {
  global $wpdb;
  
  $apykey = get_option( 'APIKEY', '' );
  $clima = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=".$_POST['wqtitle']."&appid=$apykey");
  $json = json_decode($clima);
  
  
    $wpdb->update( "wp_historico", array(
      "title" => $_POST['wqtitle'],
      "description" => $_POST['wqdescription'],
      "temp"        => $json->main->temp,
      "temp_min"    => $json->main->temp_min,
      "temp_max"    => $json->main->temp_max,
      "pressure"    => $json->main->pressure,
      "humidity"    => $json->main->humidity,       
      "updated_at" => time()
    ), array('id' => $_POST['wqentryid']) );

    $response = array('message'=>'Los datos se han actualizado correctamente', 'rescode'=>200);
  
  echo json_encode($response);
  exit();
  wp_die();
}

add_action('wp_ajax_wqparameters', 'wqparameters_callback_function');
add_action('wp_ajax_nopriv_wqparameters', 'wqparameters_callback_function');

function wqparameters_callback_function() {
 global $wpdb;
    add_option( 'APIKEY', $_POST['wqtitle'] , '', 'yes' );
    add_option( 'APIKEY_descripcion', $_POST['wqdescription'] , '', 'yes' );
    $response = array('message'=>'Los parametros se han insertado correctamente', 'rescode'=>200);

  echo json_encode($response);
  exit();
  wp_die();  
}