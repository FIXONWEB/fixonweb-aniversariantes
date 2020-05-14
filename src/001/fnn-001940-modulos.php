<?php
function fix001941_create_tables() {
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  global $wpdb;
  global $charset_collate;
  $sql = "
  CREATE TABLE IF NOT EXISTS ".$GLOBALS['wpdb']->prefix."fix001940 (
    fix001940_codigo int(11) NOT NULL AUTO_INCREMENT,
    fix001940_tabela varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001940_descri varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001940_sql_sort varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001940_sql_limit int(11) DEFAULT NULL,
    fix001940_sql_dir varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001940_ativo varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
    PRIMARY KEY (fix001940_codigo),
    UNIQUE KEY fix001940_codigo (fix001940_codigo)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
  ";
  $wpdb->query($sql);
  $sql = "
  CREATE TABLE IF NOT EXISTS ".$GLOBALS['wpdb']->prefix."fix001941 (
    fix001941_codigo int(11) NOT NULL AUTO_INCREMENT,
    fix001941_tabela varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001941_campo varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001941_label varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001941_ordem int(11) DEFAULT NULL,
    fix001941_ctr_new varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001941_ctr_edit varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001941_ctr_view varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001941_ctr_list varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    fix001941_ativo varchar(1) COLLATE utf8_unicode_ci DEFAULT 's',
    fix001941_tipo varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    PRIMARY KEY (fix001941_codigo),
    UNIQUE KEY fix001941_codigo (fix001941_codigo)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
  ";
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $mysqli->query($sql);
}
function fix001941_delete_tables() {
  global $wpdb;
  $wpdb->query( "DROP TABLE IF EXISTS ".$GLOBALS['wpdb']->prefix."fix001940");
  $wpdb->query( "DROP TABLE IF EXISTS ".$GLOBALS['wpdb']->prefix."fix001941");
}
//--request
add_action( 'parse_request', 'fix001941_parse_request');
function fix001941_parse_request( &$wp ) {
  // echo $wp->request;
  if($wp->request == 'fix001941_create_tables'){
    if(!current_user_can('administrator')) return '<!--não disponivel-->';
    fix001941_create_tables();
    exit;
  }
  if($wp->request == 'fix001941_delete_tables'){
    if(!current_user_can('administrator')) return '<!--não disponivel-->';
    fix001941_delete_tables();
    exit;
  }
  if($wp->request == 'fix001941_reset_all_triggers'){
    $sql = "select * from ".$GLOBALS['wpdb']->prefix."fix001940";
    $tb = fix_001940_db_exe($sql,'rows');
    $rows = $tb['rows'];
    foreach ($rows as $row) {
      $func_exe = $row['fix001940_tabela']."_create_trigger";
      echo '<pre>';
      print_r($func_exe);
      
      if(function_exists($func_exe)){
        $func_exe();
        print_r('sim');
      }
      echo '</pre>';
    }
    exit;
  }
  if($wp->request == 'fix001941_delete_all_tables') {
    $sql = "select * from ".$GLOBALS['wpdb']->prefix."fix001940";
    $tb = fix_001940_db_exe($sql,'rows');
    $rows = $tb['rows'];
    foreach ($rows as $row) {
      $func_exe = $row['fix001940_tabela']."_delete_table";
      echo '<pre>';
      print_r($func_exe);
      echo "\n";
      if(function_exists($func_exe)){
        $func_exe();
        echo "\n";
        echo 'sim';
        
      }
      echo '</pre>';

    }
    exit;
  }

if($wp->request == 'fix001941_create_all_table') {
    $sql = "select * from ".$GLOBALS['wpdb']->prefix."fix001940";
    $tb = fix_001940_db_exe($sql,'rows');
    echo '<pre>';
    print_r($tb);
    echo '</pre>';
    $rows = $tb['rows'];
    foreach ($rows as $row) {
      $func_exe = $row['fix001940_tabela']."_create_table";
      echo '<pre>';
      print_r($func_exe);
      echo "\n";
      if(function_exists($func_exe)){
        $func_exe();
        echo "\n";
        echo 'sim';
        
      }
      echo '</pre>';

    }
    exit;
  }


}

add_shortcode("fix_001940_mnu_modulos", "fix_001940_mnu_modulos");
function fix_001940_mnu_modulos($atts, $content = null){
    $sql = "select * from ".$GLOBALS['wpdb']->prefix."fix001940";
    $tb = fix_001940_db_exe($sql,'rows');
    $rows = $tb['rows'];
    foreach ($rows as $row) {
      $descri = $row['fix001940_descri'];
      if(!$descri) $descri = $row['fix001940_tabela'];
      ?>
        <div><a href="<?php echo site_url()."/".$row['fix001940_tabela']  ?>/listagem/"><?php echo $descri ?></a></div>
      <?php
    }

}