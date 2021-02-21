<?php
/*
Plugin Name: Clima Aplicacion
Plugin URI: https://github.com/ispardoa/clima_plugin.git
Description: Recupera el clima de una ciudad y su historico
Author: Israel Pardo
Author URI: https://github.com/ispardoa
Version: 1.0.0
*/

global $wpdb;
define('CLIMA_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('CLIMA_PLUGIN_PATH', plugin_dir_path( __FILE__ ));

register_activation_hook( __FILE__, 'activate_clima_plugin_function' );
register_deactivation_hook( __FILE__, 'deactivate_clima_plugin_function' );

function activate_clima_plugin_function() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = 'wp_historico';
 
  $sql = "CREATE TABLE $table_name (
    `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255),
    `description` text,
    `temp` varchar(255),
    `temp_min` varchar(255),
    `temp_max` varchar(255),
    `pressure` varchar(255),
    `humidity` varchar(255),    
    `created_at` varchar(255),
    `updated_at` varchar(255),
    PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}

function deactivate_clima_plugin_function() {
  global $wpdb;
  $table_name = 'wp_historico';
  $sql = "DROP TABLE IF EXISTS $table_name";
  $wpdb->query($sql);
}

function load_custom_css_js() {
  wp_register_style( 'my_custom_css', CLIMA_PLUGIN_URL.'/css/style.css', false, '1.0.0' );
  wp_enqueue_style( 'my_custom_css' );
  wp_enqueue_script( 'my_custom_script1', CLIMA_PLUGIN_URL. '/js/custom.js' );
  wp_enqueue_script( 'my_custom_script2', CLIMA_PLUGIN_URL. '/js/jQuery.min.js' );
  wp_localize_script( 'my_custom_script1', 'ajax_var', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
}
add_action( 'admin_enqueue_scripts', 'load_custom_css_js' );

require_once(CLIMA_PLUGIN_PATH.'/ajax/ajax_action.php');

add_action('admin_menu', 'my_menu_pages');
function my_menu_pages(){
    add_menu_page('CLIMA', 'CLIMA', 'manage_options', 'new-entry', 'my_menu_output' );
    add_submenu_page('new-entry', 'CLIMA Aplicacion', 'Parametros', 'manage_options', 'parameters', 'my_menu_parameters' );
    add_submenu_page('new-entry', 'CLIMA Aplicacion', 'Nueva entrada', 'manage_options', 'new-entry', 'my_menu_output' );
    add_submenu_page('new-entry', 'CLIMA Aplicacion', 'Visualizar entradas', 'manage_options', 'view-entries', 'my_submenu_output' );
}

function my_menu_output() {
  require_once(CLIMA_PLUGIN_PATH.'/admin-templates/new_entry.php');
}

function my_menu_parameters() {
  require_once(CLIMA_PLUGIN_PATH.'/admin-templates/parameters.php');
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class EntryListTable extends WP_List_Table {

    function __construct() {
      global $status, $page;
      parent::__construct(array(
        'singular' => 'Entry Data',
        'plural' => 'Entry Datas',
      ));
    }

    function column_default($item, $column_name) {
        switch($column_name){
          case 'action': echo '<a href="'.admin_url('admin.php?page=new-entry&entryid='.$item['id']).'">Actualizar</a>';
        }
        return $item[$column_name];
    }

    function column_feedback_name($item) {
      $actions = array( 'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id']) );
      return sprintf('%s %s', $item['id'], $this->row_actions($actions) );
    }

    function column_cb($item) {
      return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
    }

    function get_columns() {
      $columns = array(
        'cb' => '<input type="checkbox" />',
			  'title'=> 'Ciudad',
        'description'=> 'Descripcion',
        'temp' => 'Temperatura',
        'temp_max' => 'Temp_Max',
        'temp_min' => 'Temp_Min',
        'pressure' => 'Presion',
        'humidity' => 'Humedad',
        'action' => 'Accion'
      );
      return $columns;
    }

    function get_sortable_columns() {
      $sortable_columns = array(
        'title' => array('title', true)
      );
      return $sortable_columns;
    }

    function get_bulk_actions() {
      $actions = array( 'delete' => 'Borrar' );
      return $actions;
    }

    function process_bulk_action() {
      global $wpdb;
      $table_name = "wp_historico";
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    function prepare_items() {
      global $wpdb,$current_user;

      $table_name = "wp_historico";
		  $per_page = 5;
      $columns = $this->get_columns();
      $hidden = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array($columns, $hidden, $sortable);
      $this->process_bulk_action();
      $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

      $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
      $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
      $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

		  if(isset($_REQUEST['s']) && $_REQUEST['s']!='') {
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `title` LIKE '%".$_REQUEST['s']."%' OR `description` LIKE '%".$_REQUEST['s']."%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);
		  } else {
			  $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);
		  }

      $this->set_pagination_args(array(
        'total_items' => $total_items,
        'per_page' => $per_page,
        'total_pages' => ceil($total_items / $per_page)
      ));
    }
}

function my_submenu_output() {
  global $wpdb;
  $table = new EntryListTable();
  $table->prepare_items();
  $message = '';
  if ('delete' === $table->current_action()) {
    $message = '<div class="div_message" id="message"><p>' . sprintf('Items deleted: %d', count($_REQUEST['id'])) . '</p></div>';
  }
  ob_start();
?>
  <div class="wrap wqmain_body">
    <h3>Listado de entradas de Clima</h3>
    <?php echo $message; ?>
    <form id="entry-table" method="GET">
      <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
      <?php $table->search_box( 'Buscar', 'search_id' ); $table->display() ?>
    </form>
  </div>
<?php
  $wq_msg = ob_get_clean();
  echo $wq_msg;
}

// Cllase para ver los datos en frondend

class dbTable2dataTable
{
    protected static $instance;

    protected $defaults = array();
    protected $jsObject = array();
    
    /**
     * Singleton Factory
     *
     * @return object
     */
    public static function instance() {
        if ( !isset( self::$instance ) )
            self::$instance = new dbTable2dataTable();

        return self::$instance;
    }

    /**
     * Construct
     *
     * @uses function add_shortcode
     */
    protected function __construct() {
        add_shortcode( 'dbtable', array( $this, 'shortcode' ) ); // New shortcode.
        $this->defaults = array(
              'from'        => null,    // Source file url.
              'select'      => null,    // column ignored
              'except'      => null,    // column ignored
              'cssclass'    => null,    // Specify custom CSS class for the <table>
              'comments'    => false,   // Use field comments instead of column name
              'pagination'  => false,   // Enable / Disable pagination
              'limit'       => 5,      // Limit of results per page
              'language'    => 'Spanish' // Default language : French
            );
    }

    /**
     * Construct
     *
     * @uses function shortcode_atts
     */
    function shortcode( $atts ) {
      global $wpdb;

      $atts = shortcode_atts($this->defaults, $atts);
     // if(is_null($atts['from']) or substr($atts['from'], 0,strlen($wpdb->prefix)) === $wpdb->prefix){
     //      return '<span style="color:red;">You can not display datas from all tables starting with "'.$wpdb->prefix.'" or you have forgotten to specify the "from" parameter.</span>';
     //  }
 
      // Enqueue plugin CSS only on pages where shortcode is used.
      wp_enqueue_style('dbtable2table', plugin_dir_url(__FILE__).'css/datatables.min.css', array(), '1.0', true);

      // Enqueue plugin JS only on pages where shortcode is used.
      // ---- Lib DataTable
      wp_enqueue_script('dbtable2tableLib', plugin_dir_url(__FILE__).'js/datatables.min.js', array(), '1.0', true);
      // ---- Custom Script
      $jsObject = array('language' => $atts['language'], 'limit' => (int)$atts['limit'], 'pagination' => ((bool)$atts['pagination'] ? 'true' : 'false'), 'filter');
      wp_register_script( 'dbtable2tableMain', plugin_dir_url(__FILE__).'js/main.js', array( 'jquery' )); //if jQuery is not needed just remove the last argument. 
      wp_localize_script( 'dbtable2tableMain', 'dbtable2tableOptions', $jsObject ); //pass 'object_name' to script.js
      wp_enqueue_script( 'dbtable2tableMain' );   
      add_action( 'wp_enqueue_scripts', 'dbtable2tableMain' );

      // Render table and return HTML string.
      return $this->renderTable($atts);
    }

    /**
     * Construct
     *
     * @uses global variable $wpdb
     */
    function renderTable($atts){
      global $wpdb;

      $tableRendered = $cssClass = '';
      $select = $except = array();

      if(is_null($atts['from']))
        return null;

      if(is_null($atts['select'])){
        if(!is_null($atts['except'])){
          if(strpos($atts['except'], ','))
            $except = explode(',', $atts['except']);
          else
            $except[] = $atts['except'];
        }
      }else{
          if(strpos($atts['select'], ','))
            $select = explode(',', $atts['select']);
          else
            $select[] = $atts['select'];
      }
      
      $cssClass = (!is_null($atts['cssclass']) AND strpos($atts['cssclass'], ',')) ? str_replace(',', ' ', $atts['cssclass']) : $atts['cssclass'];

      $tableRendered .= '<table class="dbtable2databable '.$cssClass.'">';
        $tableRendered .= '<thead>';
          $tableRendered .= '<tr>';

            //We get column names
            $myrows = $wpdb->get_results('SHOW full COLUMNS FROM '.$atts['from']);
            foreach ($myrows as $oneColumn) {

              if(is_null($atts['select'])){
                if(!in_array($oneColumn->Field, $except)){
                  if($atts['comments'] && strlen($oneColumn->Comment) > 0){
                    $tableRendered .= '<th>'.$oneColumn->Comment.'</th>';
                  }else{
                    $tableRendered .= '<th>'.$oneColumn->Field.'</th>';
                  }
                }
              }else{
                if(in_array($oneColumn->Field, $select)){
                  if($atts['comments'] && strlen($oneColumn->Comment) > 0){
                    $tableRendered .= '<th>'.$oneColumn->Comment.'</th>';
                  }else{
                    $tableRendered .= '<th>'.$oneColumn->Field.'</th>';
                  }
                }
              }

            }

        $tableRendered .= '</tr>';
        $tableRendered .= '</thead>';
        $tableRendered .= '<tbody>';

          //We add datas to the table
          $myrows = $wpdb->get_results('SELECT * FROM '.$atts['from']);

          foreach ($myrows as $oneRow) {
            $tableRendered .= '<tr>';
              foreach ($oneRow as $key => $value) {
                if(is_null($atts['select'])){
                  if(!in_array($key, $except))
                    $tableRendered .= '<td>'.$value.'</td>';
                }else{
                  if(in_array($key, $select))
                    $tableRendered .= '<td>'.$value.'</td>';
                }
              }
            $tableRendered .= '</tr>';
          }

        $tableRendered .= '</tbody>';
      $tableRendered .= '</table>';

      return $tableRendered;
    }

}
dbTable2dataTable::instance();


