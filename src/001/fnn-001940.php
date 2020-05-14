<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }
function fix_001940_view($atts, $content = null) {
  extract(shortcode_atts(array(
    "cnn" => '',
    "md" => '0',
    "cod" => '0',
    "style" => '',
    "un_show" => '',
    "access" => '',
    "role" => '',
    "on_op" => '',
    "inner" => '',
    "col_replace" => '',
    "title" => ''
  ), $atts));
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){if(!fix_001940_is_role($role)) return '';}
  $df=array();
  $df['inner'] = $inner;
  $df['col_replace'] = $col_replace;
  // return $on_op;
  $get_url_if_op = fix_001940_get_op();
  if($on_op=="empty"){
    if($get_url_if_op) return '';
  } else{
    if($on_op){
      if($on_op<>$get_url_if_op) return '';
    }
  }
  // return '---'.$get_url_if_op.'---';
  // if($on_op) {
    // if($on_op=="empty"){
      // if($get_url_if_op) return '';
    // }else{
    // }
    // if(!$get_url_if_op)  return '';
    // if($get_url_if_op<>$on_op) return '';
  // }
  $df['md'] =$md;
  $cod = preg_replace("/__cod__/", fix_001940_get_cod(), $cod);
  $cod = preg_replace("/__pai__/", fix_001940_get_pai(), $cod);
  $md = preg_replace("/__md__/", fix_001940_get_md(), $md);
  $cod = preg_replace("/__pessoa_by_user__/", get_user_meta( get_current_user_id(), "pessoa_by_user", true ) , $cod);
  
  $ret ="";
  if(!$md) {$ret = "fix_001940_view - md não especificado";}
  if(!$cod) {$ret = "fix_001940_view - cod não especificado";}
  if($ret) {return $ret;exit;}
  $view = fix_001940_md_view($md,$cod,$cnn,$df);
  ?>
  <style type="text/css">
    .fix_001940_view_label {
      text-align:right;
      font-style: italic;
      font-size: 12px;
      padding-right: 15px;
      margin: 0px;
      text-transform: uppercase;
    }
    .fix_001940_view_data {
      min-height:30px;
      font-weight: bolder;
      margin: 0px;
    }
  </style>
  <?php 
  $ret = "";
  $ret .= '';
  $ret .= $title;
  $ret .= ' <form action="" method="POST">';
  $ret .= '<div style="border-bottom:1px solid gray;"">';
  for ($i=0; $i < count($view['campo']); $i++) {
    
    if(($un_show) && (preg_match("/".$view['campo'][$i]['name']."/i", $un_show))){
    } else{
      $view['campo'][$i]['fieldLabel'] = preg_replace("/_/", " ", $view['campo'][$i]['fieldLabel']);
      // $view['campo'][$i]['fieldLabel'] = strtoupper($view['campo'][$i]['fieldLabel']);
      $ret .= ' <div style="display: grid;grid-template-columns: 3fr 7fr;border-top:1px solid gray;" >';
      $ret .= '   <div class="fix_001940_view_label" >'.$view['campo'][$i]['fieldLabel'].':</div>';
      $ret .= '   <div class="fix_001940_view_data" >';
      $ret .= '     '.$view['campo'][$i]['value'].' ';
      $ret .= '   </div>';
      $ret .= ' </div>';
    }
  }
  $ret .= ' </div>';
  $ret .= '</form>';
  return $ret;
}
add_shortcode("fix_001940_view", "fix_001940_view");
function fix_001940_text($atts, $content = null){
  extract(shortcode_atts(array(
    "on_op" => '',
    "access" => '',
    "role" => '',
    "url" => '',
    "id" => ''
  ), $atts));
  // return $on_op;
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){if(!fix_001940_is_role($role)) return '';}
  
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';
    }
  }
  return $content;
}
add_shortcode("fix_001940_text", "fix_001940_text");
function fix_001940_recent($atts, $content = null){
  global $wpdb;
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "target" => "",
    "md" => "0",
    "on_op" => ''
  ), $atts));
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
    }
    if(!$get_url_if_op)  return '';
    if($get_url_if_op<>$on_op) return '';
  }
  $modulo_conf  = fix_001940_get_modulo_conf($md);
  $tabela_name = $GLOBALS['wpdb']->prefix.$modulo_conf['tabela'];
  $tabela_campo = $modulo_conf['tabela'];
  $campo_codigo  = $tabela_campo."_codigo";
  $sql = "select $campo_codigo from $tabela_name order by $campo_codigo desc limit 0, 1";
  $tb = fix_001940_db_exe($sql,'rows');
  if(!$tb['r']) exit;
  if (!$target) {
    return 'target';
  }
  
  if($tb['r']){
    $cod = $tb['rows'][0][$campo_codigo];
    $target = preg_replace("/__cod__/", $cod , $target);
    echo '<script type="text/javascript">';
    echo  'window.location.href = "'.$target.'"';
    echo '</script>';
    exit;
  }
}
add_shortcode("fix_001940_recent", "fix_001940_recent");


function fix_001940_paginacao($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0'
  ), $atts));
  $get_start = isset($_GET['start']) ? sanitize_text_field($_GET['start']) : 0;
  $get_limit = isset($_GET['limit']) ? sanitize_text_field($_GET['limit']) : 20;
  if(!$md) {echo "paginação - $md nao especificado";exit;}
  $total = isset($_SESSION['md'.$md.'_total']) ? $_SESSION['md'.$md.'_total'] : 0;
  $start = $get_start;
  $limit = $get_limit;
  $start_preview  = $start - $limit;
  $start_next   = $start + $limit;
  if($start_preview < 0 ) $start_preview = 0;
  if($start_next > $total ) $start_next = $start_next;
  $paginas = ceil($total / $limit);
  $pagina = 1;
  if(($start+1) > $limit){
    $pagina = ceil(($start+1) / $limit) ;
  }
  $tt = $start+$limit;
  $pagina_last = $paginas * $limit;
  $limit_end = $start + $limit;
  if($limit_end > $total) $limit_end = $total;
//----ini
  $cls = "";
  $csl_last = "";
  $csl_preview = "";
  if (($pagina_last+$limit) > $total) {
    $tt = $total;
    if(($start+$limit) >= $total) {
      $csl = "disabled";
      $csl_last = "disabled";
    }
    if(!$start){
      $csl_preview = "disabled";
    }
  }
  //----end
  // $qs = $_SERVER["QUERY_STRING"];
  $qs = $_SERVER["REQUEST_URI"];
  // $link       = $url.$_SERVER["QUERY_STRING"];
  //REQUEST_URI
  $link       = $url.$_SERVER["REQUEST_URI"];
  $ret = '';
  // $ret .= '<h4>Paginação</h4>';
  // $ret .= '<div></div>';
  $ret .= '<div class="pd10">';
  $ret .= '  <a class="btn btn-primary fleft '.$csl_preview.'" href="?start=0&limit='.$limit.'>"><span class="glyphicon glyphicon-fast-backward"></span></a>';
  $ret .= '  <a class="btn btn-primary fleft '.$csl_preview.'" href="?start='.$start_preview.'&limit='.$limit.'"><span class="glyphicon glyphicon-backward"></span></a>';
  $ret .= '  <a class="btn btn-primary fleft '.$csl_last.'" href="?start='.$start_next.'&limit='.$limit.'"><span class="glyphicon glyphicon-forward"></span></a>';
  $ret .= '  <a class="btn btn-primary fleft '.$csl_last.'" href="?start='.$pagina_last.'&limit='.$limit.'"><span class="glyphicon glyphicon-fast-forward"></span></a>';
  $ret .= '  <div class="w20 h30 fleft">  </div>';
  $ret .= '  <a class="btn btn-primary  fleft'.$csl_last.'" href=""><span class=" glyphicon glyphicon-refresh"></span></a>';
  $ret .= '';
  // $ret .= '  <div class="clear"></div>';
  // $ret .= '  <div class="hide_">';
  $ret .= '   <div class="fleft pd10">';
  $ret .= '     Total de registros: '.$total.' ';
  $ret .= '   </div>';
  $ret .= '   <div class="fleft pd10 ">';
  $ret .= '     Páginas : '.$paginas.' ';
  $ret .= '   </div>';
  $ret .= '   <div class="fleft pd10">';
  $ret .= '     Página atual: '.$pagina.' ';
  $ret .= '   </div>';
  $ret .= '   <div class="fleft pd10"> ';
  $ret .= '     Mostrando de: '.$start.' a '.($start + $limit).' ';
  $ret .= '   </div>';
  $ret .= '   <div class="fleft pd10"> ';
  $ret .= '     (registros por páginas: '.$limit.') ';
  $ret .= '   </div>';
  // $ret .= '  </div>';
  $ret .= '</div>';
  return $ret;
/**/
}
add_shortcode("fix_001940_paginacao", "fix_001940_paginacao");






function fix_001940_list_single($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0'
  ), $atts));
  $df['md'] =$md;
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $col      = fix_001940_get_md_col($md);
  $modulo_conf  = fix_001940_get_modulo_conf($md);
  $tabela     = $modulo_conf['tabela'];
  $campo_codigo   = $tabela."_codigo";
  $fields = fix_001940_get_fields($md);
  $data = fix_001940_get_md_rows($md, $fields,$col);
  if($data['msg']) return $data['msg'];
  $_SESSION['md'.$md.'_total'] = $data['total'];
  $ret = '';
  $ret .= '<div id="md'.$md.'ilist" class="pd10" style="width:100%">';
  $ret .= '  <div class="" style="overflow-y:auto">';
  $ret .= '    <table class="table table-condensed" data-total="'.$data['total'].'">';
  $ret .= '    <tbody>';
  for ($i=0; $i < count($data['row']); $i++){
    $ret .= '      <tr class="wpmsc_tr">';
    for ($c=0; $c < count($col); $c++) {  $campo = $col[$c]['dataIndex'];
      $cls = "";
      $ret .= '        <td class="'.$cls.'">'.$data['row'][$i][$campo].'</td>';
    }
    $ret .= '      </tr>';
  }
  $ret .= '    </tbody>';
  $ret .= '  </table>';
  $ret .= '</div>';
  return $ret;
}
add_shortcode("fix_001940_list_single", "fix_001940_list_single");










function fix_001940_list_old($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "manut" => '0',
    "criterio" => '',
    "criterio2" => '',
    "style" => '',
    "class" => '',
    "on_op" => '',
    "title" => '',
    "access" => '',
    "role" => '',
    "un_show" => '',
    "config" => '',
    "join" => '',
    "inner" => '',
    "cnn" => '',
    "die_col" => '',
    "col_replace" => '',
    "die_sql" => '' ,
    "col_url" => '',
    "col_x0" => '',
    "col_xt" => '',
    "col_add" => '',
    "sql_order" => '',
    "sql_dir" => '',
  ), $atts));


  // die("--- $col_x0 ---");




//col_add='depois_de|antes_de,coluna_name,label'
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){ if(!fix_001940_is_role($role)) return '';}
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';
    }
  }
  $cfg = array();
  $busca = fix_001940_get_busca();
  if($busca){
    if(is_numeric($busca)){
      do_shortcode('[fix_001940_buscando]');
      exit;
    }
  }
// ---'.bloginfo('url').'---
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
    }
  
  }
  $df = array();
  
  $df['sql_order'] = $sql_order;
  $df['sql_dir'] = $sql_dir;
  $df['col_add'] = $col_add;
  $df['md'] = $md;
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  
  $col  = fix_001940_get_md_col($md,$cnn,$df);
  // return 'xxx';
  //_die_fix_001940_list
  // print_r($col);
  if($col_replace){
    $resplace = explode(",", $col_replace);
    foreach ($resplace as $keyc => $valuec) {
      $arrray = explode(":", $valuec);
      foreach ($col as $key => $value) {
        if ($value['dataIndex']==$arrray[0]) {
          $col[$key]['dataIndex'] = $arrray[1];
          $col[$key]['filter_type'] = 'string';
        }
      }
    }
  }
  if($die_col){
    echo "<pre>";
    print_r($col);
    echo "<pre>";
    return '';
  }
  if(!count($col)) return '';
  $modulo_conf    = fix_001940_get_modulo_conf($md, $cnn);
  $tabela         = '';//= $modulo_conf['tabela'];//xxxxxxxxxxxxrevisao
  $campo_codigo   = $tabela."_codigo";
  $fields         = fix_001940_get_fields($md, $cnn,$df);
    // echo "<pre>";
    // print_r($fields);
    // echo "<pre>";
    // die('_die_fix_001940_list');
  $df['join'] = $join;
  $df['die_col'] = $die_col;
  $df['col_replace'] = $col_replace;
  $df['die_sql'] = $die_sql;
  $df['inner'] = $inner;
  $criterio = preg_replace("/__cod__/", fix_001940_get_cod() , $criterio);
  $criterio = preg_replace("/__pai__/", fix_001940_get_pai() , $criterio);
  $criterio = preg_replace("/__prefix__/", fix_001940_prefix(false) , $criterio);
  $criterio = preg_replace("/__pessoa_by_user__/", get_user_meta( get_current_user_id(), "pessoa_by_user", true ) , $criterio);
 
  $df['criterio'] = base64_encode($criterio);
  $data = fix_001940_get_md_rows($md, $fields, $col, $df, $cnn);
  
  // echo '<pre>';
  // print_r($data);
  // echo '</pre>';
  // $data = fix_001940_get_md_rows_to_list($md, $fields, $col, $df, $cnn);
  if(isset($data['msg'])){
    if($data['msg']) return $data['msg'];
  }
  $_SESSION['md'.$md.'_total'] = $data['total'];
  $manut = $modulo_conf['show_cp_option'];
  if( $on_op) $manut = false;
  //paginacai -ini
  $ret = "";
  $url = $_SERVER["REDIRECT_URL"].'?';
  $add_class = "wpmsc";
  if(substr($url,1,6)=='xxxwpmsc') {
    $add_class = "wpmsc_link_ajax";
  };
//gambiarra pra consertar  a paginação quando nginxs
  $q = (isset($_GET["q"]) ? sanitize_text_field($_GET["q"]) : '');
  if($q){
    $link       = $q.'?';//$url.$_SERVER["REQUEST_URI"];
  } else {
    $link       = $url.$_SERVER["QUERY_STRING"];
  }
  
  $start      = isset($_GET['start']) ? sanitize_text_field($_GET['start']) : 0;
  $limit      = isset($_GET['limit']) ? sanitize_text_field($_GET['limit']) : $modulo_conf['limit'];//20; //por paginas ou limit
  $total      = $data['total'];//149;//$data['total']
  $supertotal = 0;
  $total2 = $total - $limit;
 
  $rfirst     = fix_001940_add_param($link,'start',"0");//0;//fix_001940_remove_param($link, 'start');//
  $rprevious  = fix_001940_add_param($link,'start',($start-$limit < 0 ? 0 : $start-$limit));//0;//fix_001940_add_param($link,'start',10)
  $rnext      = fix_001940_add_param($link,'start',$start+$limit) ;
  $rlast      = fix_001940_add_param($link,'start',($total2));// $supertotal - $limit;//90;//fix_001940_add_param($link,'start',($supertotal-10))
  $limit_10   = fix_001940_add_param($link,'limit',"10");
  $limit_25   = fix_001940_add_param($link,'limit',"25");
  $limit_50   = fix_001940_add_param($link,'limit',"50");
  $limit_100  = fix_001940_add_param($link,'limit',"100");
  // echo '<hr>';
  // echo '<div style=""></div>';
  // $ret = '<div style=""></div>';
  
  $ret .= $title;
  $ret .= '<div class="'.$md.'_list" style="overflow-y:auto;border:solid 0px gray;">';
  $ret .= '<table style="'.$style.'" class="" >';
  if(($config) && (preg_match("/no_col_title/i", $config))){
  } else{
    $ret .= '<thead>';
    $ret .= '<tr>';
    $ret .= '<th style=""><div class="'.$md.'_mnut">'.$col_xt.'</div></th>';
    for ($i=0; $i < count($col); $i++){
      if($col[$i]['ctr_list'] == 'label'){
        if(($un_show) && (preg_match("/".$col[$i]['dataIndex']."/i", $un_show))){
        } else {
          // if(($un_show) && (preg_match("/".$col[$i]['dataIndex']."/i", $un_show))){
          // $col[$i]['text'] = preg_replace("/_/", " ", $col[$i]['text']);
          $ret .= '<th style="text-align:left;">'.$col[$i]['text'].'</th>';
        }
      }
      if($col[$i]['ctr_list'] == 'radio'){
      	$ret .= '<th style="text-align:left;">'.$col[$i]['text'].'</th>';
      }
    }
    $ret .= '</tr>';
    $ret .= '</thead>';
  }
  $ret .= '<tbody>';

  for ($i=0; $i < count($data['row']); $i++){
    $ret .= '<tr class="'.$md.'_tr" data-codigo='.$data['row'][$i][$md.'_codigo'].'>';
    if ($col_url) {
      $t566_codigo_name = isset($col[0]['codigo_name']) ? $col[0]['codigo_name'] : '';
      if($t566_codigo_name){
        $t566_v_codigo_name = $data['row'][$i][$t566_codigo_name];
        $ok = 0;
       
        if($col_url){
          $col_url = preg_replace("/__tcod__/i", $t566_v_codigo_name, $col_url);
          $col_url = preg_replace("/__pai__/i", fix_001940_get_pai(), $col_url);
          $col_url = preg_replace("/__cod__/i", fix_001940_get_cod(), $col_url);
          $col_url_arr = explode(",", $col_url);
          foreach ($col_url_arr as $ckey => $cvalue) {
            $is_role_true = true;
            // echo "<div>".$cvalue."</div>";
            $col_url_arr_item = explode("|", $cvalue);
            $is_role_in = isset( $col_url_arr_item[2] ) ? $col_url_arr_item[2] : '';
            if($is_role_in){
              $is_role_true = fix_001940_is_role($is_role_in);
            } else {
              $is_role_true = 1;
            }
            if($is_role_true) {
              foreach ($col as $key => $value) {
                if ($value['dataIndex']==$col_url_arr_item[0]) {
                  $tcampo = $value['dataIndex'];
                  $tvalue = $col_url_arr_item[1];
                  $tvalue = preg_replace("/__this__/i", $data['row'][$i][$tcampo], $tvalue);
                  foreach ($col as $ttkey => $ttvalue) {
                    $tttcampo = $ttvalue['dataIndex'];
                    $tttvalue = $data['row'][$i][$tttcampo];
                    if (preg_match("/__".$tttcampo."__/", $tvalue)) {
                      $tvalue = preg_replace("/__".$tttcampo."__/", $data['row'][$i][$tttcampo],$tvalue);
                    }
                  }
                  $trole = isset($col_url_arr_item[2]) ? $col_url_arr_item[2] : "";
                  $data['row'][$i][$tcampo] = $tvalue; 
                }
              }
            }
          }
        }
      }
    }
    


    $ret .= '<td class="'.$md.'_col_x0 '.$md.'_mnum" style="white-space: nowrap;">'.$col_x0.'</td>';

    for ($c=0; $c < count($col); $c++) {  $campo = $col[$c]['dataIndex'];
     
      if(($col[$c]['ctr_list'] == 'label') || ($col[$c]['ctr_list'] == 'radio')) {
        if(($un_show) && (preg_match("/".$campo."/i", $un_show))){
          //$ret .= '<td style="border:1px solid;"></td>';
        }else{
          if(($config) && (preg_match("/no_cel_url/i", $config))){
            $data['row'][$i][$campo] = strip_tags($data['row'][$i][$campo]);//'--=--';
          }
          $ret .= '<td class="'.$col[$c]['dataIndex'].'" style="white-space: nowrap;color:#000000;">'.$data['row'][$i][$campo].'</td>';
          // $ret .= '<td class="irow-sit-" style="color:#000000;">'.$data['row'][$i][$campo].'</td>';
        }
      }
      // if($col[$c]['ctr_list'] == 'radio'){
      	// $ret .= '<td class="'.$col[$c]['dataIndex'].'" style="white-space: nowrap;color:#000000;">--'.$data['row'][$i][$campo].'--</td>';
      // }
    }
    $ret .= '</tr>';
  }
  $ret .= '</tbody>';
  $ret .= '</table>';
  $ret .= '</div>';
  //show paginacao - ini
  if(($config) && (preg_match("/no_count_reg/i", $config))){
  } else {
    $ret .= '<div style="text-align:center"> ';
    $ret .= '<big>'.$total.' registro(s).</big>';
    $ret .= '</div>';
  }
  //show paginacao - end
  // return $ret;
  //show total  - ini
  if(($config) && (preg_match("/no_sum_col/i", $config))){
  } else {
    $limit = 20;
    $q = (isset($_GET["q"]) ? sanitize_text_field($_GET["q"]) : '');
    if($q){
      $link       = $q.'?';//$url.$_SERVER["REQUEST_URI"];
    } else {
      $link       = $url.$_SERVER["QUERY_STRING"];
    }
    
    $start = isset($_GET['start']) ? $_GET['start'] : 0;
    $rnext      = fix_001940_add_param($link,'start',$start+$limit) ;
    $rprevious  = fix_001940_add_param($link,'start',$start-$limit) ;
    $nav_atual = isset($_GET['start']) ? $_GET['start'] : 0;
    $nav_inicio = 0;
    $nav_anterior = $nav_atual - 20;
    $nav_proximo = $nav_atual + 20;
    $nav_ultimo = ($data['total'] -20);
    if ($nav_anterior < 0) {$nav_anterior = 0;}
    if ($nav_proximo > $nav_ultimo ) { $nav_proximo = $nav_ultimo; }
    $url_inicio = fix_001940_add_param($link,'start',$nav_inicio) ;
    $url_anterior  = fix_001940_add_param($link,'start',$nav_anterior) ;
    $url_proximo = fix_001940_add_param($link,'start',$nav_proximo) ;
    $url_ultimo = fix_001940_add_param($link,'start',$nav_ultimo) ;
    
    if($total > $limit){
      $ret .= '<div style="text-align:center"> ';
      $ret .= '<a href="'.$url_inicio.'" >&nbsp;&lt;&lt;&nbsp;</a>';
      $ret .= '<a href="'.$url_anterior.'" >&nbsp;&lt;&nbsp;</a>';
      $ret .= '<small>&nbsp;'.$start.' a '.((($start + $limit) > $total) ? $total : ($start + $limit)).'&nbsp;</small>';
      $ret .= '<a href="'.$url_proximo.'" >&nbsp;&gt;&nbsp;</a>';
      $ret .= '<a href="'.$url_ultimo.'" >&nbsp;&gt;&gt;&nbsp;</a>';
      $ret .= '</div>';
    }
  }
  //show total  - end
  //return $ret;
    if(($config) && (preg_match("/no_paging/i", $config))){
      } else {
    if($total > $limit){
      $ret .= '<div style="text-align:center"> ';
      $ret .= 'limite ';
      $ret .= '<a href="'.$limit_10.'" class="btn btn-link '.$add_class.'">&nbsp;10&nbsp;</a>';
      $ret .= '<a href="'.$limit_25.'" class="btn btn-link '.$add_class.'">&nbsp;25&nbsp;</a>';
      $ret .= '<a href="'.$limit_50.'" class="btn btn-link '.$add_class.'">&nbsp;50&nbsp;</a>';
      $ret .= '<a href="'.$limit_100.'" class="btn btn-link '.$add_class.'">&nbsp;100&nbsp;</a>';
      $ret .= ' por pagina ';
      $ret .= '</div>';
    }
  }
  return $ret;
}
add_shortcode("fix_001940_list_old", "fix_001940_list_old");








function fix_001940_insert($atts, $content = null) {
  extract(shortcode_atts(array(
    "cnn" => '',
    "md" => '0',
    "cod" => '0',
    "target" => '',
    "target_pos_insert" => '?',
    "on_op" => '',
    "access" => '',
    "role" => '',
    "col_fix" => '',
    "insert_add" => '',
    "insert_add_user_meta" => '',
    "insert_add_option" => ''
  ), $atts));
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){ if(!fix_001940_is_role($role)) return '';}
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';
    }
  }
  $target_pos_insert = html_entity_decode($target_pos_insert);
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $target_pos_insert = preg_replace("/__cod__/", fix_001940_get_cod() , $target_pos_insert);
  $target_pos_insert = preg_replace("/__pai__/", fix_001940_get_pai() , $target_pos_insert);
  $ret = '';
  if(!$md) {$ret = "fix_001940_insert - md não especificado";}
  if($ret) {return $ret;exit;}
  $tmp_request = $_REQUEST;
  $fields = '';
  $values = '';
// echo "<br>---$col_fix---<br>";
  if($col_fix){
    $col_fix_arr = explode(',', $col_fix);
    foreach ($col_fix_arr as $key => $value) {
      $t = explode('=', $value);
      $fields .= $t[0];
      $values .= $t[1];
      $values = preg_replace("/__user__/i",  get_current_user_id(), $values);
      $tmp_request[$fields] = $values;
    }
  }
  // print('<pre>');
  // print_r($_REQUEST);
  // print('</pre>');
  // if($col_fix) die();
  $insert = fix_001940_md_insert($md, $tmp_request, $cnn, $insert_add, $insert_add_user_meta, $insert_add_option );
  // $ret = "";
  // $ret .= '';
  // if($target_pos_insert){
  //   echo '<script type="text/javascript">';
  //   echo '    window.location.href = "'.$target_pos_insert.'";';
  //   echo '</script>';
  //   exit;
  // }
  return '';
  // return $ret;
}
add_shortcode("fix_001940_insert", "fix_001940_insert");
function fix_001940_iframe($atts, $content = null){
  extract(shortcode_atts(array(
    "on_op" => '',
    "access" => '',
    "role" => '',
    "url" => '',
    "id" => ''
  ), $atts));
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){ if(!fix_001940_is_role($role)) return '';}
  $ret = '';
  // $ret = '---'.$url.'---';
  $url = preg_replace("/__pai__/", fix_001940_get_pai(), $url);
  $url = preg_replace("/__qs__/", $_SERVER['REQUEST_URI'] , $url);
  
  $ret .= '';
  //style="width:100%;min-height:500px;border:solid 1px #0000; overflow-x:hidden; overflow-y:auto;"
  $ret .= '<iframe class="iiframe" style="overflow:hidden;width:100%;min-height:500px;" id="'.$id.'" src="'.$url.'"  ></iframe>';
  //scrolling="no"
  // $ret .= '<script>(function($) {var t = jQuery("iframe").contents().width();jQuery("'.$id.'").height(t);});</script>';
  // $ret .= '<script>(function($) { 
  //   var t = $("iframe").contents().width();
  //   alert(t);
  //   // console.log(\'t:\'+t); 
  // });</script>';
  ///wpmsc/8201/?pai=__pai__
  // $ret .= "---".$url."---";
  return $ret;
}
add_shortcode("fix_001940_iframe", "fix_001940_iframe");
function fix_001940_duplique($atts, $content = null) {
  extract(shortcode_atts(array(
    "cnn" => '',
    "md" => '0',
    "cod" => '0',
    "target_update" => '',
    "target_insert" => '?op=insert',
    "access" => '',
    "role" => ''
  ), $atts));
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){if(!fix_001940_is_role($role)) return '';}
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $cod = preg_replace("/__cod__/", fix_001940_get_cod() , $cod);
  $target_update = preg_replace("/__cod__/", fix_001940_get_cod() , $target_update);
  $target_update = preg_replace("/__md__/", fix_001940_get_md() , $target_update);
  $target_update = preg_replace("/__pai__/", fix_001940_get_pai() , $target_update);
  $target_insert = preg_replace("/__cod__/", fix_001940_get_cod() , $target_insert);
  $target_insert = preg_replace("/__md__/", fix_001940_get_md() , $target_insert);
  $target_insert = preg_replace("/__pai__/", fix_001940_get_pai() , $target_insert);
  $ret = '';
  if(!$md) {$ret = "fix_001940_duplique - md não especificado";}
  if(!$cod) {$ret = "fix_001940_duplique - cod não especificado";}
  if($ret) {return $ret;exit;}
  $edit = fix_001940_md_edit($md,$cod,$cnn);
  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  if(substr($url,1,6)=='xxxwpmsc') {
    $add_class = "i".$md."update";
    $ret .= '
    <script type="text/javascript">
      jQuery(function(){
        jQuery(".i'.$md.'update").submit(function(e){
          e.preventDefault();
          url = jQuery(this).attr("action");
          // alert(url);
          jQuery.ajax({
            method: "POST",
            url: url,
            data: jQuery(this).serialize()
          })
          .done(function( html ) {
            jQuery("#aba_ctu").load("'.$url.'?op=view&cod='.$cod.'");
          });
          return false;
        })
      });
    </script>
    ';
  };
  $ttop = isset($_REQUEST['op']) ? sanitize_text_field($_REQUEST['op']) : '';
  if($ttop=='duplicar'){
    $ret .= '
    <script type="text/javascript">
    jQuery(function(){
      jQuery("#fmdsubmit").css("visibility","hidden");
      jQuery("#fmdsubmit").remove();
      jQuery("#fmdduplique").css("visibility","visible");
      // alert(333);
    });
    </script>
    ';
  }
  $ret .= '';
  $ret .= ' <form class="form-horizontal '.$add_class.'" action="'.$url.$target_insert.'" method="POST">';
  for ($i=0; $i < count($edit['campo']); $i++) {
    $ret .= ' <div class="form-group pd0" style="margin-bottom:2px;padding-right:10px;" >';
    $ret .= '   <label class="col-sm-3 control-label italico f12">'.$edit['campo'][$i]['fieldLabel'].'</label>';
    $ret .= '   <div class="col-sm-9 bgw colorb" style="min-height:30px">';
    $ret .= '     <input type="text" style="" name="'.$edit['campo'][$i]['name'].'" id="'.$edit['campo'][$i]['name'].'" class="form-control" value="'.$edit['campo'][$i]['value'].'" title="" autocomplete="off">';
    $ret .= '   </div>';
    $ret .= ' </div>';
  }
  $ret .= ' <div class="h20" ></div>';
  $ret .= ' <div class="form-group pd0" style="margin-bottom:2px;padding-right:10px;" >';
  $ret .= '   <div class="col-sm-3"></div>';
  $ret .= '   <button id="fmdduplique" type="submit" name="duplique" class="btn btn-primary" style="">Duplicar</button> ';
  $ret .= ' </div>';
  $ret .= ' </form>';
  return $ret;
}
add_shortcode("fix_001940_duplique", "fix_001940_duplique");
function fix_001940_detalhe($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
  ), $atts));
  $df['md'] =$md;
  $cod = preg_replace("/__cod__/", fix_001940_get_cod() , $cod);
  $cod = preg_replace("/__pai__/", fix_001940_get_pai() , $cod);
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $ret ="";
  if(!$md) {$ret = "fix_001940_detalhe - md não especificado";}
  if(!$cod) {$ret = "fix_001940_detalhe - cod não especificado";}
  if($ret) {return $ret;exit;}
  $view = fix_001940_md_view($md,$cod);
  $ret = "";
  $ret .= '';
  $ret .= ' <dl class="dl-horizontal">';
  for ($i=0; $i < count($view['campo']); $i++) {
    $ret .= '<dt>'.$view['campo'][$i]['fieldLabel'].'</dt><dd>'.$view['campo'][$i]['value'].'</dd>';
  }
  $ret .= ' </dl>';
  return $ret;
}
add_shortcode("fix_001940_detalhe", "fix_001940_detalhe");
function fix_001940_det($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
  ), $atts));
  $df['md'] =$md;
  $cod = preg_replace("/__cod__/", fix_001940_get_cod() , $cod);
  $cod = preg_replace("/__pai__/", fix_001940_get_pai() , $cod);
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $ret = '';
  if(!$md) {$ret = "wpmsc det - md não especificado";}
  if(!$cod) {$ret = "wpmsc det - cod não especificado";}
  if($ret) {return $ret;exit;}
  $view = fix_001940_md_view($md,$cod);
  $ret = "";
  $ret .= '';
  $ret .= ' <form class="form-horizontal" action="" method="POST">';
  for ($i=0; $i < count($view['campo']); $i++) {
    $ret .= ' <div class="form-group">';
    $ret .= '   <div class="gray"><em>'.$view['campo'][$i]['fieldLabel'].'</em></div>';
    $ret .= '    <p class=""><strong>'.$view['campo'][$i]['value'].'</strong></p>';
    $ret .= ' </div>';
  }
  $ret .= ' </form>';
  return $ret;
}
add_shortcode("fix_001940_det", "fix_001940_det");
function fix_001940_delete($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
    "target_pos_delete" => '?',
    "on_op" => '',
    "access" => '',
    "role" => ''
  ), $atts));
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){if(!fix_001940_is_role($role)) return '';}
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';
    }
  }
    $target_pos_delete = preg_replace("/__cod__/", fix_001940_get_cod() , $target_pos_delete);
  $target_pos_delete = preg_replace("/__pai__/", fix_001940_get_pai() , $target_pos_delete);
  $cod = preg_replace("/__cod__/", fix_001940_get_cod() , $cod);
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $delete = fix_001940_md_delete($md,$cod);
  $ret = '';
  $ret = "";
  $ret .= '';
  // if($target_pos_delete){
  //   echo '<script type="text/javascript">';
  //   echo '    window.location.href = "'.html_entity_decode($target_pos_delete).'";';
  //   // echo  'window.location.href = "../md-detalhe/?md=1030&cod=511"';
  //   echo '</script>';
  // }
  return '';
  // return $ret;
}
add_shortcode("fix_001940_delete", "fix_001940_delete");
function fix_001940_deletar($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
    "target_delete" => '?op=delete&cod=__cod__',
    "on_op" => '',
    "access" => '',
    "role" => ''
  ), $atts));
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){if(!fix_001940_is_role($role)) return '';}
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';
    }
  }
  $cod = preg_replace("/__cod__/", fix_001940_get_cod() , $cod);
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $target_delete = preg_replace("/__cod__/", fix_001940_get_cod() , $target_delete);
  $target_delete = preg_replace("/__pai__/", fix_001940_get_pai() , $target_delete);
  $ret = "";
  // $ret .= "<h1 style='color:red;'>DELETAR</h1>";
  $ret .= "<h2 style='text-align:center;'>EXCLUSÃO DE REGISTRO</h2>";
  $ret .= '<div style="text-align:center;">';
  $ret .= '<button id="'.$md.'_btn_confirme_deletar" data-cod="'.$cod.'">CONFIRME A EXCLUSÃO DESTE REGISTRO</button>';
  $ret .= '</div>';
  $ret .= do_shortcode('[fix_001940_view md='.$md.' cod=__cod__]');
  return $ret;
}
add_shortcode("fix_001940_deletar", "fix_001940_deletar");
function fix_001940_crud($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '',
    "op" => '',
    "cod" => '__cod__',
    "pai" => '__pai__',
    "default_op" => 'ilist',
    'title_nnew' => '',
    "access_nnew" => '',
    "access" => '',
    "role" => '',
    "access_manager" => '',
    "target_insert" => '?op=insert',
    "target_pos_insert" => '?',
    "target_edit" => '?op=edit&cod=__cod__',
    "target_update" => '?op=update&cod=__cod__',
    "target_pos_update" => '?op=view&cod=__cod__',
    "target_pos_delete" => '?',
    "target_pos_duplique" => '?',
    "criterio" => '',
    "bar_top" => 1,
    "busca" => 1
  ), $atts));
  if($op=='') {
    $op = fix_001940_get_op();
    if($op=='') $op=$default_op;
  }
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){ if(!fix_001940_is_role($role)) return '';}
  if(!$md) return '--nada--';
  $get_ucod = isset($_GET['ucod']) ? sanitize_text_field($_GET['ucod']) : '';
  $md   = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $op   = preg_replace("/__op__/", fix_001940_get_op() ? fix_001940_get_op() : $default_op , $op);
  $cod  = preg_replace("/__cod__/", fix_001940_get_cod() , $cod);
  $cod  = preg_replace("/__ucod__/", $get_ucod , $cod);
  $pai  = preg_replace("/__pai__/", fix_001940_get_pai() , $pai);
  if($access_manager){
    $se = 0;
    if(($op=='edit') || ($op=='update') || ($op=='novo') || ($op=='nnew') || ($op=='insert') || ($op=='delete') || ($op=='deletar')  || ($op=='duplicar')){
      $se = 1;
    }
    if($se){
        if(!fix_001940_is_access($access_manager)) return '';
    }
  }
  $uur = '';
  if($cod) $uur .= '&cod='.$cod;
  if($pai) $uur .= '&pai='.$pai;
  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  if(substr($url,1,6)=='xxxwpmsc') {
    $add_class = "wpmsc_ajax";
    $ret .= '
    <script type="text/javascript">
      jQuery(function(){
        jQuery(".wpmsc_ajax").on("click",function(e){
          e.preventDefault();
          // alert(jQuery(this).attr("href"));
          jQuery( "#aba_ctu" ).load( jQuery(this).attr("href"));
        });
      });
    </script>
    ';
  };
  if($bar_top==1){
    $ret .= '<div style="text-align:center">';
    $ret .= do_shortcode('[fix_001940_botao class="btn '.$add_class.'" label="RELOAD"      target="" ]');
    $ret .= do_shortcode('[fix_001940_botao class="btn '.$add_class.'" label="LISTAGEM"    target="'.$url.'?" ]');
    $ret .= do_shortcode('[fix_001940_botao class="btn '.$add_class.'" label="NOVO"        target="'.$url.'?op=novo&'.$criterio.'"]');
    $ret .= do_shortcode('[fix_001940_botao class="btn '.$add_class.'" label="EDIT"        target="'.$url.'?op=edit&cod=__cod__&pai=__pai__" on_op="view" ]');
    $ret .= do_shortcode('[fix_001940_botao class="btn '.$add_class.'" label="DELETAR"     target="'.$url.'?op=deletar&cod=__cod__" on_op="view" access=""]');
    $ret .= do_shortcode('[fix_001940_botao class="btn '.$add_class.'" label="DUPLICAR"    target="'.$url.'?op=duplicar&cod=__cod__" on_op="view" access=""]');
    $ret .= '</div>';
  }
  if($busca==1){
    $ret .= '<div style="text-align:center;">';
    $ret .= do_shortcode( '[fix_001940_busca]' );
    $ret .= '</div>';
    $ret .= '<div style="min-height: 1em;"></div>';
  }
  if($op=='ilist')    $ret .= do_shortcode( '[fix_001940_list md='.$md.' access_manager="'.$access_manager.'" criterio="'.$criterio.'"]' );
  if($op=='novo')     $ret .= do_shortcode( '[fix_001940_nnew md='.$md.' target_insert="'.$target_insert.'" title="'.$title_nnew.'" access="'.$access_nnew.'" access_manager="'.$access_manager.'" target_pos_insert="'.$target_pos_insert.'"] ' );
  if($op=='insert')   $ret .= do_shortcode( '[104208 md='.$md.' target_pos_insert="'.$target_pos_insert.$uur.'" access_manager="'.$access_manager.'" ]' );
  if($op=='edit')     $ret .= do_shortcode( '[fix_001940_edit md='.$md.' cod='.$cod.' target_update="'.$target_update.'" access_manager="'.$access_manager.'" ]' );
  if($op=='update')   $ret .= do_shortcode( '[fix_001940_update md='.$md.' cod='.$cod.' target_pos_update="'.$target_pos_update.'" access_manager="'.$access_manager.'" ]' );
  if($op=='view')     $ret .= do_shortcode( '[fix_001940_view md='.$md.' cod='.$cod.' access_manager="'.$access_manager.'" ]' );
  if($op=='det')      $ret .= do_shortcode( '[fix_001940_detalhe md='.$md.' cod='.$cod.' access_manager="'.$access_manager.'" ]' );
  if($op=='delete')   $ret .= do_shortcode( '[fix_001940_delete md='.$md.' cod='.$cod.' target_pos_delete="'.$target_pos_delete.'" access_manager="'.$access_manager.'" ] ' );
  if($op=='duplicar') $ret .= do_shortcode( '[fix_001940_duplique md='.$md.' cod='.$cod.' target_update="'.$target_update.'" access_manager="'.$access_manager.'" ]' );
  if($op=='deletar') {
    $ret = "<h1 style='color:red;'>DELETAR</h1>";
    $ret .= "<h2 style='color:red;'>SOLICITAÇÃO DE EXCLUSÃO DE REGISTRO</h2>";
    $ret .= do_shortcode('[fix_001940_view md='.$md.' cod=__cod__]');
    $ret .= do_shortcode('[fix_001940_botao label="CONFIRMAR EXCLUSÃO" target="?op=delete&cod=__cod__" class="btn btn-danger"]');
  }
  return $ret;
}
add_shortcode("fix_001940_crud", "fix_001940_crud");
function fix_001940_buscando($atts, $content = null) {
  //EH UM CLONE DA FUNCAO DE CIMA (fix_001940_busca_redir) PARA MANTER A COMPATIBILIDADE
  extract(shortcode_atts(array(
    // "tarscm_get_list" => '../listagem/',
    // "target_det" => '../view/'
    "tarscm_get_list" => './',
    "target_det" => './'
  ), $atts));
  $busca = isset($_GET['busca']) ? fix_001940_get_busca() : '';
  $tarscm_get_list = html_entity_decode($target_det);
  $target_det = html_entity_decode($target_det);
  $target_det = preg_replace("/__cod__/", fix_001940_get_cod() , $target_det);
  if(is_numeric($busca)){
    echo '<script type="text/javascript">';
    // echo '    window.location.href = "'.$target_det.'?cod='.$busca.'";';
    echo '    window.location.href = "'.$target_det.'?op=view&cod='.$busca.'";';
    // echo '    window.location.href = "'.$target_det.'";';
    echo '</script>';
    exit;
  }else{
    echo '<script type="text/javascript">';
    echo '    window.location.href = "'.$tarscm_get_list.'?busca='.$busca.'";';
    echo '</script>';
    exit;
  }
}
add_shortcode("fix_001940_buscando", "fix_001940_buscando");
function fix_001940_busca_redir_v2($atts, $content = null) {
  extract(shortcode_atts(array(
    "tarscm_get_list" => '../listagem/',
    "target_det" => '../view/'
  ), $atts));
  $busca = isset($_GET['busca']) ? fix_001940_get_busca() : '';
  $tarscm_get_list = preg_replace("/__site_url__/",site_url() , $tarscm_get_list);
  $target_det = preg_replace("/__site_url__/",site_url() , $target_det);
  // $target =
  if(is_numeric($busca)){
    $target = $target_det;
    // echo '<script type="text/javascript">';
    // echo '    window.location.href = "'.$target_det.'?cod='.$busca.'";';
    // echo '</script>';
    // exit;
  }else{
    $target = $tarscm_get_list;
    // echo '<script type="text/javascript">';
    // echo '    window.location.href = "'.$tarscm_get_list.'?busca='.$busca.'";';
    // echo '</script>';
    // exit;
  }
  $target = preg_replace("/__busca__/",$busca , $target);
  $target = html_entity_decode($target);
  // echo $target;
  echo '<script type="text/javascript">';
  echo '    window.location.href = "'.$target.'";';
  echo '</script>';
  exit;
}
add_shortcode("fix_001940_busca_redir_v2", "fix_001940_busca_redir_v2");
function fix_001940_busca_redir($atts, $content = null) {
  extract(shortcode_atts(array(
    "tarscm_get_list" => '../listagem/',
    "target_det" => '../view/'
  ), $atts));
  $busca = $get_busca;
  $tarscm_get_list = preg_replace("/__site_url__/",site_url() , $tarscm_get_list);
  $target_det = preg_replace("/__site_url__/",site_url() , $target_det);
  if(is_numeric($busca)){
    echo '<script type="text/javascript">';
    // echo '    window.location.href = "'.$target_det.'?cod='.$busca.'";';
    // echo "alert('".$target_det."')";
    echo '</script>';
    exit;
  }else{
    echo '<script type="text/javascript">';
    // echo '    window.location.href = "'.$tarscm_get_list.'?busca='.$busca.'";';
    echo '</script>';
    exit;
  }
}
add_shortcode("fix_001940_busca_redir", "fix_001940_busca_redir");
function fix_001940_busca($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => 0,
    "op" => '',
    "cod" => 0,
    "target" => '',
    "target_det" => '',
    "on_op" => '',
    "access" => '',
    "role" => '',
    "style" => '',
    "class" => '',
    "placeholder" => 'BUSCA'
  ), $atts));
  $target = preg_replace("/__site_url__/",site_url() , $target);
  $target_det = preg_replace("/__site_url__/",site_url() , $target_det);
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){ if(!fix_001940_is_role($role)) return '';}
  $vai = true;
  if($on_op) {
    $vai = false;
    $t_op = fix_001940_get_op() ? fix_001940_get_op() : 'empty';
    if(($on_op=='empty') && ($t_op=='empty')) $vai = true;
  }
  if(!$vai) return '';
  $busca = isset($_GET['busca']) ? fix_001940_get_busca() : '';
  $ret = "";
  $ret .= '<form action="'.$target.'" method="GET" class="'.$class.'" style="'.$style.'">';
  $ret .= '  <input type="text" value="'.$busca.'" name="busca" style="width:100px;" placeholder="'.$placeholder.'" autocomplete="off">';
  $ret .= '  <input type="submit" value="BUSCA">';
  $ret .= '</form>';
  return $ret;
}
add_shortcode("fix_001940_busca", "fix_001940_busca");
function fix_001940_botao($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
    "target" => '',
    "label" => '',
    "janela" => '',//blank
    "class" => '',
    "style" => '',
    "on_op" => '',
    "access" => '',
    "role" => '',
    "rel" => ''
  ), $atts));
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){if(!fix_001940_is_role($role)) return '';}
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
      if(!$get_url_if_op)  return '';
      if($get_url_if_op<>$on_op) return '';
    }
  }
  $target = preg_replace("/__md__/", fix_001940_get_md() , $target);
  $target = preg_replace("/__cod__/", fix_001940_get_cod() , $target);
  $target = preg_replace("/__qs__/",$_SERVER['REQUEST_URI'] , $target);
  $target = preg_replace("/__site_url__/",site_url() , $target);
  $target = preg_replace("/__pai__/", fix_001940_get_pai() , $target);
  $target = preg_replace("/__hoje__/", date('d/m/Y') , $target);
  
  $to_janela = '';
  if($janela) $to_janela = 'target="'.$janela.'"';
  return '<a rel="'.$rel.'" style="'.$style.'" class=" '.$class.'" href="'.$target.'" '.$to_janela.' >'.$label.'</a>'.$content;
}
add_shortcode("fix_001940_botao", "fix_001940_botao");


function fix_001940_mysqli_no_grupo($cod){
  global $wpdb;
  $sql = "select ffn211007_db_host, ffn211007_db_name, ffn211007_db_user, ffn211007_db_pass from ".$GLOBALS['wpdb']->prefix."i0007 where ffn211007_codigo = ".$cod;
  $tb = fix_001940_db_exe($sql);
  if($tb['r']){
    $mysqli = new mysqli($tb['rows'][0]['ffn211007_db_host'], $tb['rows'][0]['ffn211007_db_user'], $tb['rows'][0]['ffn211007_db_pass'], $tb['rows'][0]['ffn211007_db_name']);
    // if (mysqli_connect_errno()) {
    //   return false;
    //   //trigger_error(mysqli_connect_error());
    // }
  }
  // print("<pre>");
  // print_r($tb);
  // print("</pre>");
  
  return $mysqli;
}
function fix_001940_md_insert($md,$values=array(),$cnn,$insert_add){
  global $wpdb;
  // if($insert_add){
  //   echo $insert_add;
  //   die();
  // }
  
  //echo '<pre>';
  //print_r($values);
  //echo '</pre>';
  
  $modulo_conf = fix_001940_get_modulo_conf($md);
  $sql = "select * from ".fix_001940_prefix(true)."fix001941 where fix001941_tabela = '".$md."' and fix001941_ativo = 's' order by fix001941_ordem ";
  $tb = fix_001940_db_exe($sql,'rows');
  $rows = $tb['rows'];
  // echo '<pre>';
  // print_r($rows);
  // echo '</pre>';
  $i=0;
  $campo = array();
  foreach ($rows as $row){
    $vai = fix_001940_select_vai($row['fix001941_ctr_new'],'novo');
    if($vai){
      $name = $row['fix001941_campo'];
      // if(isset($values[$name])){
        $campo[$i]['name']    = $row['fix001941_campo'];
        $campo[$i]['type']    = $row['fix001941_tipo'];
        $campo[$i]['value']   = isset($values[$name]) ? sanitize_text_field($values[$name]) : '';
        // $campo[$i]['value']   = ($_REQUEST[$name]);
        $campo[$i]['xtype']   = strtolower($row['fix001941_ctr_new']);
        $i++;
      // }
    }
  }
  // echo '<pre>';
  // print_r($campo);
  // echo '</pre>';
  $modulo_conf = fix_001940_get_modulo_conf($md);
  $tabela = fix_001940_prefix(true).$md;
  $tabela_cliente = fix_001940_prefix(false).$md;
  // $de_sistema = ($modulo_conf['fix001940_de_sistema']=='s') ? true : false;
  $i_old = $i;
  for ($i=0;$i<$i_old;$i++){
    if(($campo[$i]['xtype']=="checkbox") && ($campo[$i]['type']=='string')){
      if(!$campo[$i]['value']) {
        $campo[$i]['value'] = 'N';
      } else {
        $campo[$i]['value'] = 'S';
      }
    }
    if(($campo[$i]['xtype']=="checkbox") && ($campo[$i]['type']=='int')){
      if(!$campo[$i]['value']) {
        $campo[$i]['value'] = 0;
      } else {
        $campo[$i]['value'] = 1;
      }
    }
    if($campo[$i]['type']=='date'){
      if(!$campo[$i]['value']){
        $campo[$i]['value'] = 'null';
      }else{
        $campo[$i]['value'] = fix_001940_date_br_php($campo[$i]['value']);
        $campo[$i]['value'] = "'".$campo[$i]['value']."'";
      }
    }
    // if($campo[$i]['type']=='blob')    $campo[$i]['value'] = "'".scm_utf8_to_win1252($campo[$i]['value'])."'";
    if($campo[$i]['type']=='blob')    $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
    if(($campo[$i]['type']=='string') || ($campo[$i]['type']=='varchar')){
      $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
      $de_sistema = $modulo_conf['de_sistema'];
    }
    if($campo[$i]['type']=='file'){
      $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
      $de_sistema = $modulo_conf['de_sistema'];
      $filelame = $campo[$i]['name'];
      // echo '---'.ABSPATH.'-----';
      $uploaddir = ABSPATH.'/uploads/';
      // get_home_path() 
      $extensao_t = explode('.', $_FILES[$filelame]['name']); 
      // echo '<pre>';
      // print_r($extensao_t);
      // echo '</pre>';
      $extensao_c = count($extensao_t);
      $extensao_c = $extensao_c -1;
      // echo '<pre>';
      // echo '<h1>extensao_c: '.$extensao_c.'</h1>';
      // echo '</pre>';
      $extensao = $extensao_t[$extensao_c] ;
      $extensao = strtolower($extensao);
      if($extensao){
        $vai = 0;
        if($extensao=='png') $vai = 1;
        if($extensao=='jpg') $vai = 1;
        if(!$vai) {
          echo '<div style="color:red;"><h3>TIPO DE ARQUIVO NÃO PERMITIDO</h3></div>';
          die();
        }
      }
      // echo '<pre>';
      // echo '<h1>extensao: '.$extensao.'</h1>';
      // echo '</pre>';
      // $uploadfile = $uploaddir . basename($_FILES[$filelame]['name']);
      $gera_senha = fix_001940_gera_senha();
      $uploadfile = $uploaddir . $gera_senha.'.'.$extensao ;
      // echo '<h1>uploadfile: '.$uploadfile.'</h1>';
      // echo '<h1>---'.$filelame.'----</h1>';
      // echo '<h1>---'.$_FILES['userfile']['tmp_name'].'----</h1>';
      // echo '<pre>';
      // print_r($_FILES);
      // echo '</pre>';
      $url_foto = '<img src="'.site_url().'/uploads/'.$gera_senha.'.'.$extensao.'">'; //'.$gera_senha.'.'.strtolower($extensao);
      if (move_uploaded_file($_FILES[$filelame]['tmp_name'], $uploadfile)) {
          echo "Arquivo válido e enviado com sucesso.\n";
          $campo[$i]['value'] = "'".$url_foto."'";
          // $url_foto
      } else {
          echo "Nao foi possível fazer o upload do arquivo!\n";
      }
    }
    if($campo[$i]['type']=='int')   {if(!$campo[$i]['value']) $campo[$i]['value'] = 0;}
    if($campo[$i]['type']=='float')   {
      if(!$campo[$i]['value']) $campo[$i]['value'] = 0;
      $campo[$i]['value'] = fix_001940_moeda_br_to_us($campo[$i]['value']);
    }
  }
  $c = $i;
  $insadd_key ='';
  $insadd_value = '';
  $de_sistema = ($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = fix_001940_prefix($de_sistema).$md;
    if($insert_add){
      $insadd = explode(",", $insert_add);
      $insadd_i = 0;
      foreach ($insadd as $key => $value) {
        $insadditem = explode("=", $value);
          $insadd_key .= '';
         $insadd_value .= '';
         $insadd_key .= $insadditem[0];
        $insadd_value .= $insadditem[1];
        $insadd_i++;
      }
    }
  $sql = "insert into ".$tabela_cliente." ";
  $sql_insert = '';
  $sql_values = '';
  for ($i=0;$i<count($campo);$i++){
    if($i > 0){
      $sql_insert .= ",";
      $sql_values .= ",";
    }
    $sql_insert .= $campo[$i]['name'];
    $sql_values .= $campo[$i]['value'];
  }
  if(($modulo_conf['grupalizar']) && ($modulo_conf['conexao'] >=2)){
    $sql_insert .= ", ".$modulo_conf['tabela'].'_id_sysempresa ';
    $sql_insert .= ", ".$modulo_conf['tabela'].'_id_sysusuario ';
    $sql_values .= ", ".get_grupo_id($md);
    $sql_values .= ", ".get_membro_codigo($md);
  }
  $sql_insert .= $insadd_key;
  $sql_values .= $insadd_value;
  $sql .= "(".$sql_insert.")";
  $sql .= " values ";
  $sql .= "(".$sql_values.")";
  $ret = fix_001940_db_data($sql,'insert',$cnn);
  // if($insert_add){
    // echo $sql;
    // die();
  // }
}
function fix_001940_get_modulo_conf($md){
  $sql = "select * from ".fix_001940_prefix(true)."fix001940  where fix001940_codigo = '".$md."' ;";
  $ret = array();
  // $ret['sql']         = $sql;
  // print('<pre>');
  // print($sql);
  // die();
  $tb = fix_001940_db_exe($sql,'rows');
  // $ret['tb'] = $tb;
// print('<pre>');
// echo "---fix_001940_get_modulo_conf---<br>";
// print_r($tb);
// print('</pre>');
  if($tb['r']){
    $ret['retirar_acentos']   = ($tb['rows'][0]['fix001940_retirar_acentos']);
    $ret['caixa_alta']      = ($tb['rows'][0]['fix001940_caixa_alta']);
    $ret['sql_ordem']       = $tb['rows'][0]['fix001940_sql_sort'];
    $ret['sql_dir']       = $tb['rows'][0]['fix001940_sql_dir'];
    $ret['tabela']        = $tb['rows'][0]['fix001940_tabela'];
    $ret['limit']         = ($tb['rows'][0]['fix001940_sql_limit']) ? $tb['rows'][0]['fix001940_sql_limit'] : 20;
    $ret['show_sum']      = ($tb['rows'][0]['fix001940_show_sum']=='S') ? true : false;
    $ret['show_pagin']      = $tb['rows'][0]['fix001940_show_pagin'];
    $ret['show_col_title']    = $tb['rows'][0]['fix001940_show_col_title'];
  }
  return $ret;
}
function fix_001940_get_md_novo($md){
  

  $ret_md_novo = array();
  $campo = array();
  $rules = array();
  $ret_md_novo['campo'] = $campo;
  $ret_md_novo['rules'] = $rules;
  $rows = fix_001940_get_fields($md);
  //echo 'fix_001940_get_md_novo - ini';
  //echo '<pre>';
  //print_r($rows);
  //echo '</pre>';
  //echo 'fix_001940_get_md_novo - end';
  $i=0;
  $r=0;
  if(count($rows)){
    foreach ($rows as $row){
      $vai = fix_001940_select_vai($row['ctr_new'],'novo');
      if($vai){
        $campo[$i]["inputId"] = strtolower($row['name']);
        $campo[$i]["type"] = $row['tipo'];
        $campo[$i]["name"] = $row['name'];
        // $campo[$i]["ctr_new"] = $row['fix001941_ctr_new'];
        $campo[$i]["value"] = '';
        $campo[$i]["cls"] = $row['cls'];
        $campo[$i]["xtype"] = strtolower($row['ctr_new']);
        // $campo[$i]["fieldLabel"]  = strtoupper(($row['label']));
        $campo[$i]["fieldLabel"]  = ($row['label']);
        $campo[$i]['width'] = 550;
        $campo[$i]['placeholde'] = '';//novo


        if($campo[$i]["xtype"]=='textfield') $campo[$i]["type"] = 'text';
        if($row['tipo']=='date') $campo[$i]["type"] = 'date';


        if($campo[$i]["xtype"]=='combobox'){
          $campo[$i]["type"] = 'select';
          $sql2  = "select  ".$row['cmb_codigo'].", ".$row['cmb_descri']." ";
          $sql2 .= "from ".$row['cmb_source']." ";
          $sql2 .= "order by ".$row['cmb_descri']." ";
          $sql2 .= " ;";
          $campo[$i]["sql_combo"] = $sql2;
          $tb2 = fix_001940_db_data($sql2,'rows');
          $rows2 = $tb2['rows'];
          $j = 1;
          $cmb_store = "";
          $c1 = ($row['cmb_codigo']);
          $c2 = ($row['cmb_descri']);
          $campo[$i]['store'][0]['cod'] = '';
          $campo[$i]['store'][0]['value'] = '';
          $campo[$i]['store'][0]['selected'] = 'selected';
          foreach ($rows2 as $row2){
            $campo[$i]['store'][$j]['cod'] = $row2[$c1];
            $campo[$i]['store'][$j]['value'] = ($row2[$c2]);
            $campo[$i]['store'][$j]['selected'] = '';
            $j++;
          }
        }
        //if(!$campo[$i]['black']){
          $name = $campo[$i]["name"];
          $rules[$name]['required'] = true;
          $r++;
        //}
        $i++;
      }
    }
    $ret_md_novo['campo'] = $campo;
    $ret_md_novo['rules'] = $rules;
  }
  return $ret_md_novo;
}
function fix_001940_get_filter_fixo($md){
  return '';
  $erro='';
  $ret = "";
  $sql = "
  select 
    i0003_comparison,
    i0003_type,
    i0003_value,
    i0003_field
  from ".fix_001940_prefix(true)."i0003
  where 
  (
    (i0003_modulo = ".$md.")
    and
    (i0003_ativo = 's')
  )
  ";
  /*
width:450px;
margin: auto;
// padding:10px;
// text-align:center;
  */
  $f=0;
  $filter = array();
  $tb = fix_001940_db_exe($sql,'rows');
  $rows = $tb['rows'];
  foreach ($rows as $row){
    $filter[$f]['data']['type']       = $row['i0003_type'];
    $filter[$f]['data']['value']      = $row['i0003_value'];
    $filter[$f]['field']          = $row['i0003_field'];
    $filter[$f]['data']['comparison']   = $row['i0003_comparison'];
    $f++;
  }
  for ($i=0;$i<$f;$i++){
    if($filter[$i]['data']['value']=='_0_')     $filter[$i]['data']['value'] = 0;
    if($filter[$i]['data']['value']=='__md__')    $filter[$i]['data']['value'] = $md;
    if($filter[$i]['data']['value']=='__usr__')     $filter[$i]['data']['value'] = get_membro_codigo();
    
    if($filter[$i]['data']['value']=='__cx__')    $filter[$i]['data']['value'] = $_SESSION["cx"];//$cx;
    if($filter[$i]['data']['value']=='_dia_')     $filter[$i]['data']['value'] = date("j");
    if($filter[$i]['data']['value']=='_sem_')     $filter[$i]['data']['value'] = date("W");
    if($filter[$i]['data']['value']=='_sem_add1_')  $filter[$i]['data']['value'] = (date("W")+1);
    if($filter[$i]['data']['value']=='_mes_')     $filter[$i]['data']['value'] = date("n");
    if($filter[$i]['data']['value']=='_hoje_')    $filter[$i]['data']['value'] = date("Y-m-d");
    if($filter[$i]['data']['value']=='_uddmp_'){
      $filter[$i]['data']['value'] = date("Y-m-d",mktime (0, 0, 0, (date("m")) , 0, date("Y")));
    }
    if($filter[$i]['data']['value']=='_udm_'){
      $filter[$i]['data']['value'] = date("Y-m-d",mktime (0, 0, 0, (date("m")+1) , 0, date("Y")));
    }
    if($filter[$i]['data']['value']=='_pdm_'){
      $filter[$i]['data']['value'] = date("Y-m-d",mktime (0, 0, 0, (date("m")) , 0, date("Y")));
    }
    if($filter[$i]['data']['value']=='_mes_add1_') {
      $calcula = date("n");
      $calcula++;
      if($calcula==13){
        $calcula = '1';
      }
      $filter[$i]['data']['value'] = $calcula;
    }
    if($filter[$i]['data']['value']=='_ano_')     $filter[$i]['data']['value'] = date("Y");
  }
  
  $qs = '';
  $where = "";
  if (is_array($filter)) {
    for ($i=0;$i<$f;$i++){
      switch($filter[$i]['data']['type']){
        case 'string' : 
          switch ($filter[$i]['data']['comparison']) {
            case 'ig' : $qs .= " AND ".$filter[$i]['field']." = '".$filter[$i]['data']['value']."'"; Break; //igual
            case 'eq' : $qs .= " AND ".$filter[$i]['field']." LIKE '%".$filter[$i]['data']['value']."%'"; Break;//contem
          }
          Break;
        case 'list' :
          if (strstr($filter[$i]['data']['value'],',')){
            $fi = explode(',',$filter[$i]['data']['value']);
            for ($q=0;$q<count($fi);$q++){
              $fi[$q] = "'".$fi[$q]."'";
            }
            $filter[$i]['data']['value'] = implode(',',$fi);
            $qs .= " AND ".$filter[$i]['field']." IN (".$filter[$i]['data']['value'].")";
          }else{
            $qs .= " AND ".$filter[$i]['field']." = '".$filter[$i]['data']['value']."'";
          }
        Break;
        case 'boolean' : 
          if($filter[$i]['data']['value']=='true'){
            $qs .= " AND ".$filter[$i]['field']." = 1"; 
          }
          if($filter[$i]['data']['value']=='false'){
            $qs .= " AND ".$filter[$i]['field']." = 0"; 
          }
        Break;
        case 'numeric' :
          switch ($filter[$i]['data']['comparison']) {
            case 'ne' : $qs .= " AND ".$filter[$i]['field']." != ".$filter[$i]['data']['value']; Break;
            case 'eq' : $qs .= " AND ".$filter[$i]['field']." = ".$filter[$i]['data']['value']; Break;
            case 'lt' : $qs .= " AND ".$filter[$i]['field']." < ".$filter[$i]['data']['value']; Break;
            case 'gt' : $qs .= " AND ".$filter[$i]['field']." > ".$filter[$i]['data']['value']; Break;
            case 'mi' : $qs .= " AND ".$filter[$i]['field']." >= ".$filter[$i]['data']['value']; Break;
          }
        Break;
        case 'date' :
          switch ($filter[$i]['data']['comparison']) {
            case 'ne' : $qs .= " AND ".$filter[$i]['field']." != '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
            case 'eq' : $qs .= " AND ".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
            case 'lt' : $qs .= " AND ".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
            case 'gt' : $qs .= " AND ".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
            case 'ii' : $qs .= " AND ".$filter[$i]['field']." >= '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break; //maior ou igual
            case 'mi' : $qs .= " AND ".$filter[$i]['field']." <= '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break; //menor ou igual
          }
        Break;
      }
    }
    $where .= $qs;
  } 
  return $where;    
}
function fix_001940_get_fields($md, $cnn='', $df=array()){
  $fields = array();
  $sql = "
  select
    fix001941_codigo,
    fix001941_ctr_new,
    fix001941_ctr_list,
    fix001941_campo,
    fix001941_tipo,
    fix001941_tabela,
    fix001941_label
  from ".fix_001940_prefix(true)."fix001941 where fix001941_tabela = '".$md."'  and fix001941_ativo = 's' order by fix001941_ordem";
  $tb = fix_001940_db_exe($sql,'rows');
  $rows = $tb['rows'];
  
  // echo '<pre>';
  // print_r($tb);
  // echo '</pre>';
  // die('_die_fix_001940_get_fields');
  $c = 0;
  for ($i=0;$i<$tb['r'];$i++){
    // $vai = fix_001940_select_vai($rows[$i]['fix001941_ctr_list'],'list');
    $vai = fix_001940_select_vai($rows[$i]['fix001941_ctr_new'],'novo');
    if($vai){
      $fields[$c]['name'] = $tb['rows'][$i]['fix001941_campo'];
      $fields[$c]['type'] = $tb['rows'][$i]['fix001941_tipo'];
      $fields[$c]['ctr_new'] = $tb['rows'][$i]['fix001941_ctr_new'];
      if($fields[$c]['type']=='date'){
        $fields[$c]['dateFormat'] = 'Y-m-d';
      }
      $fields[$c]["tipo"]   = $rows[$i]['fix001941_tipo'];
      $fields[$c]["label"]   = $rows[$i]['fix001941_label'];
      $fields[$c]["url_vai"]    = false;
      if($fields[$c]['url_md']){
        if($fields[$c]['url_op']){
          $fields[$c]["type"]     = 'string';
          $url_op = $fields[$c]['url_op'];
          $url_access = get_access($fields[$c]['url_md']);
          $url_access_op = isset($url_access[$url_op]) ? $url_access[$url_op] : false;
          if($url_access_op) $url_vai = $fields[$c]["url_vai"] = true;
        }
      }
      $c++;
    }
  }
  if(isset($df['col_add'])){
    if($df['col_add']){
      $col_add_a=explode(",", $df['col_add']);
      foreach ($col_add_a as $key => $value) {
        $tmp_field_config = explode(":", $value);
        $fields[$c]["name"] = $tmp_field_config[0];//$value;
        $fields[$c]["type"] = "string";//$tmp_field_config[4];//
        $fields[$c]["ctr_new"] = "numberfield";
        $fields[$c]["url_painel"] = '';
        $fields[$c]["formato"] = '';
        $fields[$c]["url"] = '';
        $fields[$c]["url_md"] = '';
        $fields[$c]["url_op"] = '';
        $fields[$c]["cls"] = '';
        $fields[$c]["tipo"] = "int";//$tmp_field_config[4];//
        $fields[$c]["label"] = "qtrrrd";
        //$fields[$c]["black"] = 1;
        $fields[$c]["url_vai"] = '';
        $c++;
      }
    }
  }
  return $fields;
}
function fix_001940_db_exe($sql,$op='rows',$conn=1){
  $ret = array();
  if($op=='rows'){
    $rr = $GLOBALS['wpdb']->get_results($sql, 'ARRAY_A');
    $ret['rows'] = $GLOBALS['wpdb']->get_results($sql, 'ARRAY_A');
    $rows['total'] = count($rr);
    $ret['r'] = $rows['total'];
    $ret['sql'] = $sql;
    return $ret;
  }
  return $GLOBALS['wpdb']->query($sql);
}
function fix_001940_db_data($sql,$op='rows',$cnn=''){
  $ret = array();
  if ( is_user_logged_in() ) {
    $user_id = get_current_user_id();
    $wpmsc_grupo = get_user_meta($user_id,  'wpmsc_grupo', true );
    if($wpmsc_grupo){
      $grupo_db_host = get_post_meta( $wpmsc_grupo, 'grupo_db_host', true );
      $grupo_db_name = get_post_meta( $wpmsc_grupo, 'grupo_db_name', true );
      $grupo_db_user = get_post_meta( $wpmsc_grupo, 'grupo_db_user', true );
      $grupo_db_pass = get_post_meta( $wpmsc_grupo, 'grupo_db_pass', true );
      $mysqli = new mysqli($grupo_db_host, $grupo_db_user, $grupo_db_pass, $grupo_db_name);
      //echo '<!--'.$sql.' '.$grupo_db_name.'-->';
      $result = mysqli_query($mysqli, $sql);
      if($op=='rows'){
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
          $rows[] = $row;
        }
        $ret['rows']  = $rows;
        $ret['total'] = count($ret['rows']);
        $ret['r']     = $ret['total'];
        $ret['sql']   = $sql;
        return $ret;
      }
    }
  }
  
  if ( is_user_logged_in() ) {
    $user_id = get_current_user_id();
    $wpmsc_user_grupo = get_user_meta($user_id,  'wpmsc_user_grupo', true );
    if ($wpmsc_user_grupo) {
      $mysqli = fix_001940_mysqli_no_grupo($wpmsc_user_grupo);
      $result = mysqli_query($mysqli, $sql);
      if($op=='rows'){
        $rows = array();
        if($result)
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
          $rows[] = $row;
        }
        $ret['rows']  = $rows;
        $ret['total'] = count($ret['rows']);
        $ret['r']     = $ret['total'];
        $ret['sql']   = $sql;
        return $ret;
      }
    }
  }
  
  if($op=='rows'){
    //$row = $wpdb->get_results($sql, 'ARRAY_A');
    $rr = $GLOBALS['wpdb']->get_results($sql, 'ARRAY_A');
    $ret['rows'] = $GLOBALS['wpdb']->get_results($sql, 'ARRAY_A');
    $rows['total'] = count($rr);
    $ret['r'] = $rows['total'];
    $ret['sql'] = $sql;
    return $ret;
  }
  return $GLOBALS['wpdb']->query($sql);
}
function fix_001940_get_param($md){
  return "";
}
function fix_001940_get_start($mds){
  $start = 0;
  return $start;
}
function fix_001940_get_cliterio2($df){
  $criterio2 = isset($df['criterio2']) ? $df['criterio2'] : '';
  return '';
}
function fix_001940_date_br_mysql($data){
  $ex = explode("/", $data);
  return $ex[2].'-'.$ex[1].'-'.$ex[1];
}
function fix_001940_date_mysql_br($data){
  if($data){
    $ex = explode("-", $data);
    return $ex[2].'/'.$ex[1].'/'.$ex[0];
  }
}
function fix_001940_remove_param($querystring, $ParameterName){
  $paramStr = '';
  $queryStr = '';
    if (strpos($querystring, '?') !== false)
        list($queryStr, $paramStr) = explode('?', $querystring);
    else if (strpos($querystring, '=') !== false)
        $paramStr = $querystring;
    else
        $queryStr = $querystring;
    $paramStr = $paramStr ? '&' . $paramStr : '';
    $paramStr = preg_replace ('/&' . $ParameterName . '(\[\])?=[^&]*/', '', $paramStr);
    $paramStr = ltrim($paramStr, '&');
    return $queryStr ? $queryStr . '?' . $paramStr : $paramStr;
}
function fix_001940_md_delete($md,$cod){
  global $wpdb;
  $modulo_conf = fix_001940_get_modulo_conf($md);
  $tabela = $md;
  $tabela_name = fix_001940_prefix(true).$tabela;
  $tabela_cliente = fix_001940_prefix(false).$tabela;
  $tabela_campo = $tabela;
  $sql = "delete from ".$tabela_cliente." where ".$tabela_campo."_codigo = ".$cod.";";
  return fix_001940_db_data($sql,'delete');
}
function fix_001940_md_duplique($md,$cod,$cnn){
  $md_edit = fix_001940_md_edit($md,$cod,$cnn);
  $campos = $md_edit['campo'];
  $values = array();
  for ($i=0; $i < count($campos); $i++) {
    $campo = $campos[$i]['name'];
    $value = $campos[$i]['value'];
    $values[$campo] = $value;
  }
  fix_001940_md_insert($md,$values,$cnn);
  return $values;
}
function fix_001940_moeda_br($valor){
  if(!$valor) $valor = 0;
  return number_format($valor, 2, ',', '.');
  return $valor;
}
function fix_001940_prefix($de_sistema=false){
  return $GLOBALS["wpdb"]->prefix;
}
add_action( 'parse_request', 'fix_001940_combo_ajax');
function fix_001940_combo_ajax( &$wp ) {
  if($wp->request == 'fix_001940_combo_ajax'){
    $tabela = $_GET['tabela'];
    $campo1 = $_GET['campo1'];
    $campo2 = $_GET['campo2'];
    $target1 = $_GET['target1'];
    $target2 = $_GET['target2'];
    $sql = "select $campo1, $campo2 from ".$GLOBALS['wpdb']->prefix."$tabela limit 0,10";
    $tb = fix_001940_db_exe($sql,'rows');
    $ret = '';
    foreach ($tb['rows'] as $key => $value) {
      $ret .= '<li><a href="#" data-campo1="'.$value[$campo1].'" data-campo2="'.$value[$campo2].'" class="fix_001940_combo_ajax_item">'.$value[$campo2].'</a></li>';
    }
    ?>
    <ul style="background: #FFFFFF;">
      <?php echo $ret; ?>
    </ul>
    <?php 
    exit;
  }
}
function fix_001940_is_access($grupo){
  if(is_super_admin()) return true;
  $role = get_user_meta( get_current_user_id(), 'role', true );
  if($role){
    $grupos = explode(',', $grupo);
    foreach ($grupos as $key => $value) {
      if($value==$role) return true;
    }
  }
  return false;
}
function fix_001940_is_role($role){
  // if(is_super_admin()) return true;
  $role = trim($role);
  if(preg_match("/|/", $role)) $t = explode("|", $role);
  if(preg_match("/,/", $role)) $t = explode(",", $role);
  $ret = 0;
  foreach ($t as $key => $value) {
    if(current_user_can( trim($value) ))  $ret = 1;
  }
  return $ret;
}
function fix_001940_get_pai(){
  return sanitize_text_field(isset($_GET['pai']) ? $_GET['pai'] : '');
}
function fix_001940_get_cod(){
   return sanitize_text_field(isset($_GET['cod']) ? $_GET['cod'] : ''); 
}
function fix_001940_get_op(){
  return sanitize_text_field(isset($_GET['op']) ? $_GET['op'] : '');  
}
function fix_001940_get_md(){
 return sanitize_text_field(isset($_GET['md']) ? $_GET['md'] : '');  
}
function fix_001940_get_busca(){
  return sanitize_text_field(isset($_GET['busca']) ? $_GET['busca'] : ''); 
}
function fix_001940_moeda_br_to_us($valor){
  $valor = preg_replace('/,/', '.',$valor);
  return $valor;
}
function fix_001940_date_br_php($data_br){
  if($data_br){
    if(is_array($data_br)){
      $ano = $data_br[3];
      $mes = $data_br[2];
      $dia = $data_br[1];
    }else{
      @list($dia,$mes,$ano) = explode("/", $data_br); 
    }
    $vai = true;
    if(!is_numeric($ano)) $vai = false;
    if(!is_numeric($mes)) $vai = false;
    if(!is_numeric($dia)) $vai = false;
    if($ano=='00') $vai = false;
    if($mes=='00') $vai = false;
    if($dia=='00') $vai = false;
    if($vai){
      if(!checkdate($mes, $dia, $ano)) $vai = false;
    }
    if(strlen($ano)<>4) $vai = false;
    
    if($vai){
      return $ano."-".$mes."-".$dia;
    }else{
      return 'null';
    }
  }else{
    return 'null';
  }
}
function fix_001940_remove_acento($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}
// echo tirarAcentos($string);
function fix_001940__date_mysql_br($data){
  $ex = explode("-", $data);
  if(count($ex)==3){
    list($dia,$mes,$ano) = explode("-", $data);
    return $ano."/".$mes."/".$dia;
  }else{
    return $data;
  }
}
add_shortcode("fix_001940_update", "fix_001940_update");
function fix_001940_update($atts, $content = null) {
  extract(shortcode_atts(array(
    "cnn" => '',
    "md" => '0',
    "cod" => '0',
    "on_op" => '',
    // "target_pos_update" => '?op=view&cod=__cod__',
    "target_pos_update" => '',
    "access" => '',
    "role" => '',
  ), $atts));
  $get_url_if_op = ffn_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';
    }
  }
  if($access){if(!ffn_is_access($access)) return '';}
  if($role){if(!ffn_is_role($role)) return '';}
  $md = preg_replace("/__md__/", ffn_get_md() , $md);
  $cod = preg_replace("/__cod__/", ffn_get_cod() , $cod);
  $cod = preg_replace("/__user__/i",  get_current_user_id(), $cod);
  $target_pos_update = preg_replace("/__cod__/", ffn_get_cod() , $target_pos_update);
  $target_pos_update = preg_replace("/__md__/", ffn_get_md() , $target_pos_update);
  $target_pos_update = preg_replace("/__pai__/", ffn_get_pai() , $target_pos_update);
  $target_pos_update = preg_replace("/__user__/i",  get_current_user_id(), $target_pos_update);
  if(!$md) {$ret = "fix_001940_update - md não especificado";}
  if(!$cod) {$ret = "fix_001940_update - cod não especificado";}
  // if(isset($_POST['duplique'])) {
  //   if(!ffn_md_insert($md, $_REQUEST )) {echo "ERRO AO INSERIR";exit;}
  //     echo '<script type="text/javascript">';
  //     echo '    window.location.href = "?" ;';
  //     echo '</script>';
  //   exit;
  // }
  // $ret = ffn_md_update($md,$cod,$cnn);
  
  if($target_pos_update){
    ?>
    <script type="text/javascript">
      // window.location.href = "<?php echo $target_pos_update ?>";
      alert('11');
    </script>
    <?php
  }
  // if($target_pos_update){
  //   $ret = "";
  //   $url = $_SERVER["REDIRECT_URL"];
  //   $add_class = "wpmsc";
    
  //   if(substr($url,1,6)=='xxxwpmsc') {
  //     $add_class = "i".$md."update";
      
  //     $ret .= '
  //     <script type="text/javascript">
  //       // jQuery(function(){
  //       // alert("'.$url.$target_pos_update.'");
  //       //   jQuery(".i'.$md.'update").submit(function(e){
  //       //     e.preventDefault();
  //       //     url = jQuery(this).attr("action");
  //       //     alert(url);
  //       //     jQuery.ajax({
  //       //       method: "POST",
  //       //       url: url,
  //       //       data: jQuery(this).serialize()
  //       //     })
  //       //   // // alert(jQuery(this).serialize());
  //       //     // .done(function( html ) {
  //       //     //   // jQuery( "#aba_ctu" ).append( html );
  //       //     //   jQuery( "#aba_ctu div" ).remove();
  //       //     //   jQuery( "#aba_ctu" ).html("ok");
  //       //     // });
  //       //     return false;
  //       //   })
  //       // });
  //     </script>
      
  //     ';
      
  //   }else{
      
      
  //     // echo '<script type="text/javascript">';
  //     // echo '    window.location.href = "'.html_entity_decode($url.$target_pos_update).'";';
  //     // echo '</script>';
  //   //}
  // }
  return '';
}
function fix_001940_select_vai($ct,$op){
  if($op=="list"){
    if($ct=='combobox')   return true;
    if($ct=='label')    return true;
    if($ct=='label_user')   return true;
    if($ct=='hidden')   return true;
    if($ct=='textfield')   return true;
    if($ct=='radio')   return true;
    if($ct=='combo_x1')   return true;
    
    
    return false;
  }
  if($op=="view"){
    if($ct=='label')    return true;
    if($ct=='hidden')   return true;
    return false;
  }
  if($op=="novo"){
    if($ct=='textfield')  return true;
    if($ct=='numberfield')  return true;
    if($ct=='datefield')  return true;
    if($ct=='combobox')   return true;
    if($ct=='textarea')   return true;
    if($ct=='htmleditor')   return true;
    if($ct=='ckeditor')   return true;
    if($ct=='radio')    return true;
    if($ct=='multcheckbox') return true;
    if($ct=='checkbox')   return true;
    if($ct=='checkbox')   return true;
    if($ct=='hidden')     return true;
    if($ct=='file')     return true;
    return false;
  }
  if($op=="edit"){
    // if($ct=='Label')     return true;
    if($ct=='textfield')  return true;
    if($ct=='numberfield')  return true;
    if($ct=='datefield')  return true;
    if($ct=='combobox')   return true;
    if($ct=='textarea')   return true;
    if($ct=='checkbox')   return true;
    if($ct=='combo_x1')   return true;
    return false;
  }
  if($op=="editu"){
    if($ct=='textfield')  return true;
    if($ct=='numberfield')  return true;
    if($ct=='datefield')  return true;
    if($ct=='combobox')   return true;
    if($ct=='textarea')   return true;
    if($ct=='checkbox')   return true;
    if($ct=='htmleditor')   return true;
    if($ct=='ckeditor')   return true;
    if($ct=='combo_x1')   return true;
    return false;
  }
  return false;
}
function fix_001940_object_to_array($data){
  if ((! is_array($data)) and (! is_object($data))) return 'xxx'; //$data;
  $result = array();
  $data = (array) $data;
  foreach ($data as $key => $value) {
      if (is_object($value)) $value = (array) $value;
      if (is_array($value)) 
      $result[$key] = fix_001940_object_to_array($value);
      else
          $result[$key] = $value;
  }
  return $result;
}
function fix_001940_md_view($md,$cod,$cnn,$df){
  global $wpdb;
  $ret_md_edit = array();
  $campo = array();
  $rules = array();
  $ret_md_edit['campo'] = $campo;
  $ret_md_edit['rules'] = $rules;
  $modulo_conf = fix_001940_get_modulo_conf($md);
  $sql = "select * from ".fix_001940_prefix(true)."fix001941 where fix001941_tabela = '".$md."' and fix001941_ativo = 's' order by fix001941_ordem";
  $tb = fix_001940_db_exe($sql,'rows');
  $rows = $tb['rows'];
  $items = array();
  $i=0;
  $r=0;
  foreach ($rows as $row){
    $vai = fix_001940_select_vai($row['fix001941_ctr_view'],'view');
    if($vai){
      $campo[$i]["inputId"]   = $row['fix001941_campo'];
      $campo[$i]["type"]      = $row['fix001941_tipo'];
      $campo[$i]["name"]      = $row['fix001941_campo'];
      $campo[$i]["xtype"]     = strtolower($row['fix001941_ctr_edit']);
      $campo[$i]["fieldLabel"]  = (($row['fix001941_label']));
      $campo[$i]['value']     = '';
        $name = $campo[$i]["name"];
        $rules[$name]['required'] = true;
        $r++;
      $i++;
    }
  }
  $tabela = $tb['rows'][0]['fix001941_tabela'];
  $tabela_name = fix_001940_prefix(true).$tb['rows'][0]['fix001941_tabela'];
  $tabela_cliente = fix_001940_prefix(false).$tb['rows'][0]['fix001941_tabela'];
  $tabela_campo = $tb['rows'][0]['fix001941_tabela'];
  $de_sistema = '';//xxxxxxxxxxxxxx($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = fix_001940_prefix($de_sistema).$md;
  $col_replace = $df['col_replace'];
  
  if($col_replace){
    $resplace = explode(",", $col_replace);
    foreach ($resplace as $keyc => $valuec) {
      $arrray = explode(":", $valuec);
      foreach ($campo as $key => $value) {
        if ($value['name']==$arrray[0]) {
          $campo[$key]['name'] = $arrray[1];
          $campo[$key]['type'] = 'string';
        }
      }
    }
  }
  $sql = "select ";
  for ($i=0;$i<count($campo);$i++){
    if($i>0) $sql .= ',';
    $sql .= $campo[$i]["name"];
  }
  $sql .= ' from '.$tabela_cliente." ";
  $sql .= ' '.$df['inner']." ";
  
  
  $sql .= "where ";
  $sql .= $tabela_campo."_codigo = ".$cod." ";
  $tb = fix_001940_db_data($sql,'rows',$cnn);
  
  $r=0;
  for ($i=0;$i<count($campo);$i++){
    $ccampo = ($campo[$i]["name"]);
    $value = isset($tb['rows'][0][$ccampo]) ? $tb['rows'][0][$ccampo] : '';
    $type = $campo[$i]["type"];
    $xtype = $campo[$i]['xtype'];
    if($campo[$i]["xtype"]=='combobox'){
      for ($ii=0;$ii<count($campo[$i]["store"]);$ii++){
        if($campo[$i]["store"][$ii]['cod']==$value){
          $campo[$i]["store"][$ii]['selected'] = 'selected';
        }
      }
    }
    if(($type=='text') || ($type=='string')){
      $value = ($value);
    }
    if($type=='float'){
      $value = fix_001940_moeda_br($value);
    }
    if($type=='date'){
      if($value=='null'){
        $value = '';
      }else{
        $value = fix_001940_date_mysql_br($value);
        // fix_001940_date_mysql_br
      }
    }
    $campo_codigo = $tabela_campo.'_codigo';
    if($ccampo==$campo_codigo){
      $campo[$i]["value"] = str_pad($campo[$i]["value"], 6, "0", STR_PAD_LEFT);
    }
    $campo[$i]["value"] = $value;
  }
  //troca url - ini
  $tabela = $md;
  $tabela_name = fix_001940_prefix(true).$tabela;
  $tabela_cliente = fix_001940_prefix(false).$tabela;
  $tabela_campo = $tabela;
  $campo_codigo = $tabela_campo.'_codigo';
  for ($i=0;$i <  count($campo); $i++){
    if($campo[$i]['name'] == $campo_codigo){
      $codigo = $campo[$i]['value'];
    }
  }
  for ($i=0;$i<count($campo);$i++){
    if($campo[$i]['url']){
      $url_painel = $campo[$i]['url_painel'];
      $vai = 0;
      if($url_painel){
        $vai = wpmsc_role_logic($url_painel);
      }
      if($vai){
        $value = $campo[$i]['url'];
        $value = preg_replace("/__cod__/i",  $codigo, $value);
        $value = preg_replace("/__xxx__/i",  '__yyy__', $value);
        $value = preg_replace("/__this__/i", $campo[$i]["value"], $value);
        $value = preg_replace("/__pai__/i",  fix_001940_get_pai(), $value);
        for ($iii=0;$iii <  count($campo); $iii++){
          $campoiii = strtolower($campo[$iii]["name"]);
          if (preg_match("/__".$campoiii."__/i", $value)) {
            $value = preg_replace("/__".$campoiii."__/i", $campo[$iii]["value"], $value);
          }
        }
        $campo[$i]["value"] = $value;
      }
    }
  }
  //troca url - end
  $ret_md_edit['campo'] = $campo;
  $ret_md_edit['rules'] = $rules;
  return $ret_md_edit;
}
function fix_001940_md_update($md,$cod,$cnn){
  global $wpdb;
  $sql = "select * from ".fix_001940_prefix(true)."fix001941 where ((fix001941_tabela = '".$md."' ) and  ( fix001941_ativo = 's' )) order by fix001941_ordem ";
  $tb = fix_001940_db_exe($sql,'rows');
  $return_update = array();
  $i=0;
  $campo = array();
  $rows = $tb['rows'];
  foreach ($rows as $row){
    $vai = fix_001940_select_vai($row['fix001941_ctr_edit'],'edit');
    if($vai){
      $name = $row['fix001941_campo'];
      $nameU = strtoupper($name);
      $nameL = strtolower($name);
      $vai2 = false;
      $vai2 = isset($_REQUEST[$nameL]) ? true : false;
      if($vai2){
        $campo[$i]['name'] = $row['fix001941_campo'];
        $campo[$i]['type']    = $row['fix001941_tipo'];
        $campo[$i]['value']   = ($_REQUEST[$name]);
        $i++;
      }
    }
  }
  $modulo_conf = fix_001940_get_modulo_conf($md);
  $tabela = $md;
  $tabela_name = fix_001940_prefix(true).$tabela;
  $tabela_cliente = fix_001940_prefix(false).$tabela;
  $tabela_campo = $tabela;
  $i_old = $i;
  for ($i=0;$i<$i_old;$i++){
    // echo '<!--'.$campo[$i]['type'].'-->';
    if($campo[$i]['type']=='string'){

    }
    if($campo[$i]['type']=='date'){
        $campo[$i]['value'] = fix_001940_date_br_php($campo[$i]['value']);
        $campo[$i]['value'] = "'".$campo[$i]['value']."'";
    }
    if($campo[$i]['type']=='blob')    $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
    if(($campo[$i]['type']=='string') || ($campo[$i]['type']=='varchar')){
      $de_sistema = '';
      $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
    }
    if(($campo[$i]['type']=='file')){
      $de_sistema = $modulo_conf['de_sistema'];
      $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
    }
    if($campo[$i]['type']=='int')   {if(!$campo[$i]['value']) $campo[$i]['value'] = 0;}
    if($campo[$i]['type']=='float')   {
      if(!$campo[$i]['value']) $campo[$i]['value'] = 0;
      $campo[$i]['value'] = fix_001940_moeda_br_to_us($campo[$i]['value']);
    }
    if($campo[$i]['type']=='float')   {$campo[$i]['value'] =  fix_001940_moeda_br_to_us($campo[$i]['value']);}
  }
  $return_update['campo'] = $campo;
  $de_sistema = '';//xxxxxxxxxxxxxx($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = fix_001940_prefix($de_sistema).$md;
  $sql = "update ".$tabela_cliente." set ";
  for ($i=0;$i<count($campo);$i++){
    if($i>0) $sql .=", ";
    $sql .= $campo[$i]['name'].' = '.$campo[$i]['value'];
  }
  $sql .= " where ".$tabela_campo."_codigo = ".$cod." ";
  // echo "---".$sql."---";
  $ret = fix_001940_db_data($sql,'update',$cnn);
  // return  $ret;
}
function fix_001940_md_edit($md,$cod,$cnn){
	global $wpdb;
	$ret_md_edit = array();
	$campo = array();
	$rules = array();
	$ret_md_edit['campo'] = $campo;
	$ret_md_edit['rules'] = $rules;
	$modulo_conf = fix_001940_get_modulo_conf($md);
	$sql = "select * from ".fix_001940_prefix(true)."fix001941 where fix001941_tabela = '".$md."' and fix001941_ativo = 's'order by fix001941_ordem";
	$tb = fix_001940_db_exe($sql,'rows');
	$rows = $tb['rows'];
	$items = array();
	$i=0;
	$r=0;
	foreach ($rows as $row){
		$vai = fix_001940_select_vai($row['fix001941_ctr_edit'],'edit');
		if($vai){
			$campo[$i]["id_cp"]     	= $row['fix001941_codigo'];
			$campo[$i]["inputId"]     	= $row['fix001941_campo'];
			$campo[$i]["type"]        	= $row['fix001941_tipo'];
			$campo[$i]["name"]        	= $row['fix001941_campo'];
			$campo[$i]["xtype"]       	= strtolower($row['fix001941_ctr_edit']);
			$campo[$i]["fieldLabel"]  	= preg_replace("/_/", " ", $row['fix001941_label']) ;
			$campo[$i]['value']       	= '';
				$name = $campo[$i]["name"];
				$rules[$name]['required'] = true;
				$r++;
			$i++;
		}
	}
	$tabela = $tb['rows'][0]['fix001941_tabela'];
	$tabela_name = fix_001940_prefix(true).$tb['rows'][0]['fix001941_tabela'];
	$tabela_cliente = fix_001940_prefix(false).$tb['rows'][0]['fix001941_tabela'];
	$tabela_campo = $tb['rows'][0]['fix001941_tabela'];
	$grupo = isset($modulo_conf['grupo']) ? $modulo_conf['grupo'] : '';

	$sql = "select ";
	for ($i=0;$i<count($campo);$i++){
		if($i>0) $sql .= ',';
		$sql .= $campo[$i]["name"];
	}
	$de_sistema = '';
	if (isset($modulo_conf['de_sistema'])) {
		$de_sistema = ($modulo_conf['de_sistema']=='s') ? true : false;
	}//($modulo_conf['de_sistema']=='s') ? true : false;
	$tabela_cliente = fix_001940_prefix($de_sistema).$md;
	$sql .= ' from '.$tabela_cliente." ";
	$sql .= "where ";
	$sql .= $tabela_campo."_codigo = ".$cod." ";
	$tb = fix_001940_db_data($sql,'rows',$cnn);
	$r=0;
	for ($i=0;$i<count($campo);$i++){
		$ccampo = ($campo[$i]["name"]);
		$value = isset($tb['rows'][0][$ccampo]) ? $tb['rows'][0][$ccampo] : '';
		$type = $campo[$i]["type"];
		$xtype = $campo[$i]['xtype'];
		if($campo[$i]["xtype"]=='combobox'){
			for ($ii=0;$ii<count($campo[$i]["store"]);$ii++){
				if($campo[$i]["store"][$ii]['cod']==$value){
					$campo[$i]["store"][$ii]['selected'] = 'selected';
				}
			}
		}
		if(($type=='text') || ($type=='string')){
			$value = ($value);
		}
		if($type=='float'){
			$value = fix_001940_moeda_br($value);
		}
		if($type=='date'){
			if($value=='null'){
				$value = '';
			}else{
				$value = fix_001940_date_mysql_br($value);
			}
		}
		$campo[$i]["value"] = $value;
	}
	$ret_md_edit['campo'] = $campo;
	$ret_md_edit['rules'] = $rules;
	return $ret_md_edit;
}
add_shortcode("fix_001940_list_v2", "fix_001940_list_v2");
function fix_001940_list_v2($atts, $content = null) {
	extract(shortcode_atts(array(
		"md" => '0',
		"manut" => '0',
		"criterio" => '',
		"criterio2" => '',
		"style" => '',
		"class" => '',
		"on_op" => '',
		"title" => '',
		"access" => '',
		"role" => '',
		"un_show" => '',
		"config" => '',
		"join" => '',
		"inner" => '',
		"cnn" => '',
		"die_col" => '',
		"col_replace" => '',
		"die_sql" => '' ,
		"col_url" => '',
		"col_x0" => '',
		"col_add" => '',
		"sql_order" => '',
		"sql_dir" => '',
	), $atts));
  
  	// $ret = '--fix_001940_list_v2--';
   //  return $ret;
  
  	//un_show - ini
  	$col  = fix_001940_get_md_col($md,$cnn,$df=array());
	if ($un_show) {
  		$un_shows = explode(" ", $un_show);
  		for ($i=0; $i < count($col); $i++) { 
  			// echo '<div>$col[$i]["dataIndex"]: '.$col[$i]['dataIndex'].'</div>';
  			if (in_array($col[$i]['dataIndex'], $un_shows)) {
  				$col[$i]['show'] = 0;
  			}
  		}
  	}
  	//un_show - end
	
  	$ret .= '<div class="" style="overflow-y:auto;border:solid 0px gray;">';
  	$ret .= '<table>';
	$ret .= '<thead>';
	$ret .= '<tr>';
    for ($i=0; $i < count($col); $i++){
		$class = ''; 
		$attr = '';
    	if( get_current_user_id()==38 ){
    		$class = 'master_edit';
    		$attr = 'data-edit=/ffn/?md=fix001941&op=edit&cod='.$col[$i]['cd'];
    	}
		if($col[$i]['show']) $ret .= '<th class="'.$class.'" '.$attr.' style="text-align:left;">'.$col[$i]['text'].'</th>';
    }
    $ret .= '</tr>';
    $ret .= '</thead>';
    
    $where = ' where 1 = 1 ';
    if($criterio) {
		$criterio = preg_replace("/__cod__/", fix_001940_get_cod() , $criterio);
		$criterio = preg_replace("/__pai__/", fix_001940_get_pai() , $criterio);
		$criterio = preg_replace("/__prefix__/", fix_001940_prefix(false) , $criterio);
		$criterio = preg_replace("/__user__/", get_current_user_id() , $criterio);
		// $criterio = ' where '.$criterio;
		$where .= ' and ('.$criterio.') '; 
	}
	$busca = isset($_REQUEST['busca']) ? sanitize_text_field($_REQUEST['busca']) : '';
	if($busca){
		$filtro = '';
		foreach ($col as $coll) {
			$col_name = $coll['dataIndex'];
			$col_tipo = $coll['tipo'];
			if($col_tipo=='string') {
				if($filtro) $filtro .= ' OR ';
				$filtro .= " ".$col_name." LIKE '%".$busca."%' ";
			}
		}
		$where .= ' and ('.$filtro.') ';
	}	
    // $sql = "select * from ".$GLOBALS['wpdb']->prefix.$md." where ".$md."_pai = 13";
    $sql = "select * from ".$GLOBALS['wpdb']->prefix.$md." ".$inner."  ".$where." order by ".$md."_codigo desc;";
    // echo '<div>'.$sql.'<div>';
	$tb = fix_001940_db_exe($sql,'rows');
	$rows = $tb['rows'];
	if($col_url) $col_url_e = explode(":", $col_url);
	foreach ($rows as $row) {
	    $ret .= '<tr>';
	    foreach ($col as $coll) {
	    	$campo_name = $coll['dataIndex'];
	    	$campo_value = $row[$campo_name];//$edit['campo'][$i]['id_cp'];
	    	if($col_url){
	    		if ($campo_name==$col_url_e[0]) {
	    			// $row[$campo_name] = 'col_url';
	    			$campo_value = preg_replace("/__this__/i", $campo_value, $col_url_e[1]);
	    			foreach ($col as $colxx) {
	    				$sssss_name = $colxx['dataIndex'];
	    				$sssss_value = $row[$sssss_name];
	    				$campo_value = preg_replace("/__".$sssss_name."__/", $sssss_value, $campo_value);
	    			}
	    			// $campo_value = $col_url_e[1] ;
	    		}
	    	}
	    	if($coll['show']) $ret .= '<td >'.$campo_value.'</td>';
	    }
	    $ret .= '</tr>';
	}
	$ret .= '</table>';  ///////ATENÇÃP
	$ret .= '</div>';
  	return $ret;
  
}
add_shortcode("fix_001940_list_edit_update", "fix_001940_list_edit_update");
function fix_001940_list_edit_update($atts, $content = null){
	extract(shortcode_atts(array(
		"md" => '',
		"target_pos_update" => '',
		"on_op" => ''
	), $atts));
	// $user_id = get_current_user_id();
	// if($user_id<>19) die();
	if(!$md) return 'md ?';
	$get_url_if_op = fix_001940_get_op();
	if($on_op) {
		if($on_op=="empty"){
			if($get_url_if_op) return '';
		}else{
			if(!$get_url_if_op)  return '';
			if($get_url_if_op<>$on_op) return '';
		}
	}
	if($target_pos_update){
		$target_pos_update = preg_replace("/__cod__/", fix_001940_get_cod() , $target_pos_update);
		$target_pos_update = preg_replace("/__md__/", fix_001940_get_md() , $target_pos_update);
		$target_pos_update = preg_replace("/__pai__/", fix_001940_get_pai() , $target_pos_update);
		$target_pos_update = preg_replace("/__user__/", get_current_user_id() , $target_pos_update);
	}
	global $wpdb;
	$op=isset($_GET['op']) ? $_GET['op'] : 'empty';
	// if($op=='list_edit_update') {
	// echo '<pre>_POST';
	// print_r($_POST);
	// echo '</pre>';
	$sql = array();
	foreach ($_POST as $key => $value) {
		foreach ($value as $key2 => $value2) {
			if(!isset($sql[$key2])) $sql[$key2] = '';
			if((isset($sql[$key2])) && ($sql[$key2])) $sql[$key2] .= ', ';
			$sql[$key2] .= $key.'='."'".$value2."'";
		}
	}
	// $sql_x = array();
	$sql_multi = '';
	foreach ($sql as $key => $value) {
		// $sql_x[] = 'update '.$GLOBALS['wpdb']->prefix.$md.' set '.$value. ' where '.$md.'_codigo = '.$key ;
		$sql_multi .= 'update '.$GLOBALS['wpdb']->prefix.$md.' set '.$value. ' where '.$md.'_codigo = '.$key.';'."\n" ;
	}
	// echo '<pre>';
	// print_r($sql_multi);
	// echo '</pre>';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$mysqli->multi_query($sql_multi);
	if($target_pos_update){
		echo '<script type="text/javascript">';
		echo '    window.location.href = "'.$target_pos_update.'"';
		echo '</script>';
	}
	return '';
	$cols = fix_001940_get_md_col($md);
	// echo '<pre>';
	// print_r($cols);
	// echo '</pre>';
    // $i=0;
  	$sql = array();
  	$i = 0;
    foreach ($_POST as $key => $value) {
		// echo '<div>$key:'.$key.' $value: </div>';
		// echo '<pre>';
		// print_r($value);
		// echo '</pre>';
		$sql[$i] = 'update '.$GLOBALS['wpdb']->prefix.$md.' set '.$key.'='.$value[0];
		// $id = array_search($key, $cols);
      // foreach ($cols as $c => $col) {
      //   echo 'col ini<pre>';
      //   print_r($col);
      //   echo '</pre>col end';
      //   if($col['dataIndex']==$key){
      //     echo '<div>$tipo:'.$col['tipo'].'</div>';
      //     if($col['tipo']=='date'){
      //       echo '<div>$value:'.$value[$i].'</div>';
      //       print_r($value);
      //     }
      //   }
      //   # code...
      // }
      // echo '<div>$id:'.$id.'</div>';
      // $c=0;
      foreach ($value as $key2 => $value2) {
      	
      	// $sql[$i] .= ' '.$value2.'= ' ;
        // echo '<div>$key2:'.$key2.' - value2: '.$value2.'</div>';
      //   foreach ($cols as $c => $col) {
      //     if($col['dataIndex']==$key){
      //       // echo '<div>$tipo:'.$col['tipo'].'</div>';
      //       if($col['tipo']=='date'){
      //         $value2 = fix_001940_date_br_mysql($value2);
      //       }
      //     }
      //   }
      //   if(isset($sql[$key2])) $sql[$key2] .=',';
      //   if(isset($sql[$key2])) $sql[$key2] .= $key. "= '".$value2."'";
      //   $c++;
      }
      $i++;
    }
    echo '<pre>';
    print_r($sql);
    echo '</pre>';
    // $sql_updatee = '';
    // $sql_comandos = '';
    // foreach ($sql as $key => $value) {
    //   $sql_updatee = "update wp_".$md." set ".$value." where ".$md."_codigo = ".$key.";\n";
    //   $sql_comandos .= $sql_updatee;
    //   echo '<pre>';
    //   echo '$sql_updatee:'.$sql_updatee;
    //   echo '</pre>';
    // }
    // $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // $mysqli->multi_query($sql_comandos);
    // echo '$sql_updatee:'.$sql_comandos;
    // echo '<a href="https://qiau7uhpf.cloud.fixon.biz/cadastro/">========</a>';
    // if ($target_pos_update) {
    //   echo '<script type="text/javascript">';
    //   // echo '    window.location.href = "'.$target_pos_update.'"';
    //   echo '</script>';
    // } else {
    //   die('$target_pos_update ?');
    // }
  // }
}
function fix_001940_list_edit($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "manut" => '0',
    "criterio" => '',
    "criterio2" => '',
    "style" => '',
    "class" => '',
    "on_op" => '',
    "title" => '',
    "access" => '',
    "role" => '',
    "un_show" => '',
    "config" => '',
    "join" => '',
    "inner" => '',
    "cnn" => '',
    "die_col" => '',
    "col_replace" => '',
    "die_sql" => '' ,
    "col_url" => '',
    "col_x0" => '',
    "col_add" => '',
    "sql_order" => '',
    "sql_dir" => '',
    "target_update" => '',
    "target_ajax_update" => '',
    "cod" => '',
    "botao_proximo" => '',
    "botao_anterior" => '',
    "un_buttom" => ''
  ), $atts));
//col_add='depois_de|antes_de,coluna_name,label'
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){ if(!fix_001940_is_role($role)) return '';}
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';
    }
  }
	if($target_update){
		$target_update = preg_replace("/__cod__/", fix_001940_get_cod() , $target_update);
		$target_update = preg_replace("/__md__/", fix_001940_get_md() , $target_update);
		$target_update = preg_replace("/__pai__/", fix_001940_get_pai() , $target_update);
		$target_update = preg_replace("/__user__/", get_current_user_id() , $target_update);
	}
  if($target_ajax_update){
    $target_ajax_update = preg_replace("/__cod__/", fix_001940_get_cod() , $target_ajax_update);
    $target_ajax_update = preg_replace("/__md__/", fix_001940_get_md() , $target_ajax_update);
    $target_ajax_update = preg_replace("/__pai__/", fix_001940_get_pai() , $target_ajax_update);
    $target_ajax_update = preg_replace("/__user__/", get_current_user_id() , $target_ajax_update);
    ?>
    <script type="text/javascript">
      jQuery(function($){
        $('#<?php echo $md; ?>_list_edit').on("submit",function(e){
          alert('_list_edit');
          return false;
        });
      });
    </script>
    <?php 
  }
  $cfg = array();
  $busca = fix_001940_get_busca();
  if($busca){
    if(is_numeric($busca)){
      do_shortcode('[fix_001940_buscando]');
      exit;
    }
  }
// ---'.bloginfo('url').'---
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
    }
  
  }
  $df = array();
  
  $df['sql_order'] = $sql_order;
  $df['sql_dir'] = $sql_dir;
  $df['col_add'] = $col_add;
  $df['md'] = $md;
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  
  
  $col  = fix_001940_get_md_col($md,$cnn,$df);
  // return 'xxx';
  //_die_fix_001940_list
  // print_r($col);
  if($col_replace){
    $resplace = explode(",", $col_replace);
    foreach ($resplace as $keyc => $valuec) {
      $arrray = explode(":", $valuec);
      foreach ($col as $key => $value) {
        if ($value['dataIndex']==$arrray[0]) {
          $col[$key]['dataIndex'] = $arrray[1];
          $col[$key]['filter_type'] = 'string';
        }
      }
    }
  }
  if($die_col){
    echo "<pre>";
    print_r($col);
    echo "<pre>";
    return '';
  }
  if(!count($col)) return '';
  $modulo_conf    = fix_001940_get_modulo_conf($md, $cnn);
  $tabela         = isset($modulo_conf['tabela']) ? $modulo_conf['tabela'] : '';
  $campo_codigo   = $tabela."_codigo";
  $fields         = fix_001940_get_fields($md, $cnn,$df);
    // echo "<pre>";
    // print_r($fields);
    // echo "<pre>";
    // die('_die_fix_001940_list');
  $df['join'] = $join;
  $df['die_col'] = $die_col;
  $df['col_replace'] = $col_replace;
  $df['die_sql'] = $die_sql;
  $df['inner'] = $inner;
  $criterio = preg_replace("/__cod__/", fix_001940_get_cod() , $criterio);
  $criterio = preg_replace("/__pai__/", fix_001940_get_pai() , $criterio);
  $criterio = preg_replace("/__prefix__/", fix_001940_prefix(false) , $criterio);
  $criterio = preg_replace("/__pessoa_by_user__/", get_user_meta( get_current_user_id(), "pessoa_by_user", true ) , $criterio);
 
  $df['criterio'] = base64_encode($criterio);
  // $data = fix_001940_get_md_rows($md, $fields, $col, $df, $cnn);
  $data = fix_001940_get_md_rows_to_list($md, $fields, $col, $df, $cnn);
  
  if(isset($data['msg'])){
    if($data['msg']) return $data['msg'];
  }
  $_SESSION['md'.$md.'_total'] = $data['total'];
  $manut = isset($modulo_conf['show_cp_option']) ? $modulo_conf['show_cp_option'] : '';
  if( $on_op) $manut = false;
  //paginacai -ini
?>
  <style type="text/css">
    .fix_001940_tr input[type='radio'] {
      margin: 0px 20px 0px 0px;
    }
    .fix_001940_tr label {
      margin: 0px 0px 0px 20px;
    }
</style>
<?php 
  $ret = "";
  $url = isset($_SERVER["REDIRECT_URL"]) ? $_SERVER["REDIRECT_URL"] : '';//.'?';
  $add_class = "wpmsc";
  if(substr($url,1,6)=='xxxwpmsc') {
    $add_class = "wpmsc_link_ajax";
  };
//gambiarra pra consertar  a paginação quando nginxs
  $q = (isset($_GET["q"]) ? sanitize_text_field($_GET["q"]) : '');
  if($q){
    $link       = $q.'?';//$url.$_SERVER["REQUEST_URI"];
  } else {
    $link       = $url.$_SERVER["QUERY_STRING"];
  }
  
  $start      = isset($_GET['start']) ? sanitize_text_field($_GET['start']) : 0;
  $limit      = isset($_GET['limit']) ? sanitize_text_field($_GET['limit']) : (isset($modulo_conf['limit']) ? $modulo_conf['limit'] : '20');//20; //por paginas ou limit
  $total      = $data['total'];//149;//$data['total']
  $supertotal = 0;
  $total2 = $total - $limit;
 
  $rfirst     = fix_001940_add_param($link,'start',"0");//0;//fix_001940_remove_param($link, 'start');//
  $rprevious  = fix_001940_add_param($link,'start',($start-$limit < 0 ? 0 : $start-$limit));//0;//fix_001940_add_param($link,'start',10)
  $rnext      = fix_001940_add_param($link,'start',$start+$limit) ;
  $rlast      = fix_001940_add_param($link,'start',($total2));// $supertotal - $limit;//90;//fix_001940_add_param($link,'start',($supertotal-10))
  $limit_10   = fix_001940_add_param($link,'limit',"10");
  $limit_25   = fix_001940_add_param($link,'limit',"25");
  $limit_50   = fix_001940_add_param($link,'limit',"50");
  $limit_100  = fix_001940_add_param($link,'limit',"100");
  // echo '<hr>';
  // echo '<div style=""></div>';
  $ret = '<div style=""></div>';
  
  $ret .= ' <form class="fix_001940_list_edit" id="'.$md.'_list_edit" action="'.$target_update.'" method="POST">';
  $ret .= $title;
  $ret .= '  <div class="" style="overflow-y:auto;border:solid 0px gray;">';
  $ret .= '<table style="'.$style.'" class="" >';
  if(($config) && (preg_match("/no_col_title/i", $config))){
  } else{
    $ret .= '<thead>';
    $ret .= '<tr>';
    $ret .= '<th style=""></th>';
    $class = ''; 
    $attr = '';
    for ($i=0; $i < count($col); $i++){
      if( get_current_user_id()==38 ){
        $class = 'master_edit';
        // $attr = 'data-edit=/ffn-cp-edit/?cod='.$edit['campo'][$i]['id_cp'];
        $attr = 'data-edit=/ffn/?md=fix001941&op=edit&cod='.$col[$i]['cd'];
      }
      if($col[$i]['ctr_list'] == 'label') {
        if(($un_show) && (preg_match("/".$col[$i]['dataIndex']."/i", $un_show))){
        } else {
          // if(($un_show) && (preg_match("/".$col[$i]['dataIndex']."/i", $un_show))){
          // $col[$i]['text'] = preg_replace("/_/", " ", $col[$i]['text']);
          $ret .= '<th class="'.$class.'" '.$attr.' style="text-align:left;">'.$col[$i]['text'].'</th>';
        }
      }
      if($col[$i]['ctr_list'] == 'textfield') {
        $ret .= '<th class="'.$class.'" '.$attr.' style="text-align:left;">'.$col[$i]['text'].'</th>';
      }
      
      if($col[$i]['ctr_list'] == 'radio') {
        $ret .= '<th class="'.$class.'" '.$attr.' style="text-align:left;">'.$col[$i]['text'].'</th>';
      }
      if($col[$i]['ctr_list'] == 'combo_x1') {
        $ret .= '<th class="'.$class.'" '.$attr.' style="text-align:left;">'.$col[$i]['text'].'</th>';
      }
    }
    $ret .= '</tr>';
    $ret .= '</thead>';
  }
  $ret .= '<tbody>';
  // echo '<pre>';
  // print_r($col);
  // echo '</pre>';
  for ($i=0; $i < count($data['row']); $i++){
    $ret .= '<tr class="fix_001940_tr">';
    if ($col_url) {
// echo "---".$col[0]['codigo_name']."---";
      $t566_codigo_name = isset($col[0]['codigo_name']) ? $col[0]['codigo_name'] : '';
      // echo "<div>--$t566_codigo_name--</div>";
      if($t566_codigo_name){
        $t566_v_codigo_name = $data['row'][$i][$t566_codigo_name];
        // print('<pre>');
        // print_r($data['row'][$i][$t566_codigo_name]); 
        // print('</pre>');
        $ok = 0;
        // if(fix_001940_is_role('master,diretoria')) $ok = 1;
        // if(fix_001940_is_role('diretoria')) $ok = 1;
        if($col_url){
          $col_url = preg_replace("/__tcod__/i", $t566_v_codigo_name, $col_url);
          $col_url = preg_replace("/__pai__/i", fix_001940_get_pai(), $col_url);
          $col_url = preg_replace("/__cod__/i", fix_001940_get_cod(), $col_url);
          $col_url_arr = explode(",", $col_url);
          foreach ($col_url_arr as $ckey => $cvalue) {
            $is_role_true = true;
            // echo "<div>".$cvalue."</div>";
            $col_url_arr_item = explode(":", $cvalue);
            $is_role_in = isset( $col_url_arr_item[2] ) ? $col_url_arr_item[2] : '';
            if($is_role_in){
              $is_role_true = fix_001940_is_role($is_role_in);
            } else {
              $is_role_true = 1;
            }
            // $is_role_true = true;
            // echo "---$is_role_in $is_role_true---<br>";
// echo "=== $cvalue === $is_role_true ===<br>";
            if($is_role_true) {
              foreach ($col as $key => $value) {
                if ($value['dataIndex']==$col_url_arr_item[0]) {
                  $tcampo = $value['dataIndex'];
                  $tvalue = $col_url_arr_item[1];
                  $tvalue = preg_replace("/__this__/i", $data['row'][$i][$tcampo], $tvalue);
                  foreach ($col as $ttkey => $ttvalue) {
                    $tttcampo = $ttvalue['dataIndex'];
                    $tttvalue = $data['row'][$i][$tttcampo];
                    if (preg_match("/__".$tttcampo."__/", $tvalue)) {
                      $tvalue = preg_replace("/__".$tttcampo."__/", $data['row'][$i][$tttcampo],$tvalue);
                    }
                  }
                  $trole = isset($col_url_arr_item[2]) ? $col_url_arr_item[2] : "";
                  $data['row'][$i][$tcampo] = $tvalue; 
                }
              }
            }
          //   foreach ($col as $key => $value) {
          //     $t567 = $col_url_arr_item[1];
          //     $t566_c = isset($col[$i]['dataIndex']) ? $col[$i]['dataIndex'] : '';
          //     if($t566_c){
          //       // echo "<div>---".$col[$i]['dataIndex']."---</div>";
          //       $t566_v = $data['row'][$i][$t566_c];
          //       $t566_codigo_name = $col[$i]['codigo_name'];
          //       $t566_v_codigo_name = $data['row'][$i][$t566_codigo_name];
          //       foreach ($col as $key => $value) {
          //         if (preg_match("/__".$value['codigo_name']."__/", $t567)) {
          //           $tvalue = isset($data['row'][$i][$t567]) ? $data['row'][$i][$t567] : '';
          //           $t567_c = $value['codigo_name'];
          //           $t567_v = strip_tags($data['row'][$i][$t567_c]);
          //           $t567 = preg_replace("/__".$value['codigo_name']."__/", $t567_v,$t567);
          //           if(isset($tcampo)){
          //             $t567 = preg_replace("/__this__/i", $data['row'][$i][$tcampo], $t567);
          //             $t567 = preg_replace("/__pai__/i", fix_001940_get_pai(), $t567);
          //             $data['row'][$i][$tcampo] = $t567;
          //           }
          //         }
          //       }
          //     }
          //   }
          }
        }
      }
    }
    //col_x0
    $ret_col_x0 = '';
    $ret_col_x0_label = '';
    if ($col_x0) {
      $ret_col_x0 = $col_x0;
      $ret_col_x0_label = '...';
      foreach ($col as $key => $value) {
        $x0_campo = $value['dataIndex'];
        $x0_value = $data['row'][$i][$x0_campo];
        $ret_col_x0 = preg_replace("/__".$value['dataIndex']."__/", $x0_value, $ret_col_x0);
        $ret_col_x0 = preg_replace("/__cod__/", fix_001940_get_cod() , $ret_col_x0);
        $ret_col_x0 = preg_replace("/__pai__/", fix_001940_get_pai() , $ret_col_x0);
      }
    }
    $ret .= '   <td class="fix_001940_col_0" data-fix_001940_col_0="'.$ret_col_x0.'" style="white-space: nowrap;">'.$ret_col_x0_label;
    $ret .= isset($col_x0a[$i]) ? $col_x0a[$i] : '';
    $ret .= '</td>';
    for ($c=0; $c < count($col); $c++) {  $campo = $col[$c]['dataIndex'];
     
      // echo '<pre>';
      // print_r ($data['row'][$i]);
      // print_r ($col[$c]);
      // echo '</pre>';
      $codigo_name = $col[$c]['codigo_name'];
      $codigo = $data['row'][$i][$codigo_name];
      if($col[$c]['ctr_list'] == 'label'){
        if(($un_show) && (preg_match("/".$campo."/i", $un_show))){
        }else{
          if(($config) && (preg_match("/no_cel_url/i", $config))){
            $data['row'][$i][$campo] = strip_tags($data['row'][$i][$campo]);//'--=--';
          }
          $ret .= '<td class="'.$col[$c]['dataIndex'].'" style="white-space: nowrap;">'.$data['row'][$i][$campo].'</td>';
        }
      }
      if($col[$c]['ctr_list'] == 'textfield'){
        $ret .= '<td class="'.$col[$c]['dataIndex'].'" style="white-space: nowrap;">';
        $ret .= '<input type="text" name="'.$col[$c]['dataIndex'].'['.$codigo.']" id="'.$col[$c]['dataIndex'].'['.$codigo.']['.$c.']" value="'.$data['row'][$i][$campo].'" title="" autocomplete="off">';
        $ret .= '</td>';
      }
      if($col[$c]['ctr_list'] == 'combo_x1'){
        $ret .= '<td class="'.$col[$c]['dataIndex'].'" style="white-space: nowrap;">';
        if($col[$c]['cmb_tp']=='radio'){
          $cmb_source = $col[$c]['cmb_source'];
          if ($cmb_source) {
            $cmb_source_e = explode("|", $cmb_source);
            $ret .= '<select name="'.$col[$c]['dataIndex'].'['.$codigo.']" id="'.$col[$c]['dataIndex'].'['.$codigo.']">';
            foreach ($cmb_source_e as $key => $value) {
              if($data["row"][$i][$campo]==$value) $selected = 'selected'; else $selected = '';
              $ret .= '<option value="'.$value.'" '.$selected.' >'.$value.'</option> ';
              // $ret .= '<label for="'.$col[$c]['dataIndex'].'['.$codigo.']['.$key.']'.'">'.$value.'</label>';
              // if($data["row"][$i][$campo]==$value) $checked = 'checked'; else $checked = '';
              // $ret .= '<input type="radio" name="'.$col[$c]['dataIndex'].'['.$codigo.']'.'" id="'.$col[$c]['dataIndex'].'['.$codigo.']['.$key.']'.'" value="'.$value.'" '.$checked.' >';
            }
            $ret .= '</select>';
          }
        }
        $ret .= '</td>';
      }
      if($col[$c]['ctr_list'] == 'radio'){

        $ret .= '<td class="'.$col[$c]['dataIndex'].'" style="white-space: nowrap;">';
        // $ret .= '<input type="text" name="'.$col[$c]['dataIndex'].'['.$codigo.']" id="'.$col[$c]['dataIndex'].'['.$codigo.']['.$c.']" value="'.$data['row'][$i][$campo].'" title="" autocomplete="off">';
        // $ret .= "<div>campo: ".$data["row"][$i][$campo]."</div>";
        if($col[$c]['cmb_tp']=='radio'){
          $cmb_source = $col[$c]['cmb_source'];
          if ($cmb_source) {
            $cmb_source_e = explode("|", $cmb_source);
            foreach ($cmb_source_e as $key => $value) {
              $ret .= '<label for="'.$col[$c]['dataIndex'].'['.$codigo.']['.$key.']'.'">'.$value.'</label>';
              if($data["row"][$i][$campo]==$value) $checked = 'checked'; else $checked = '';
              $ret .= '<input type="radio" name="'.$col[$c]['dataIndex'].'['.$codigo.']'.'" id="'.$col[$c]['dataIndex'].'['.$codigo.']['.$key.']'.'" value="'.$value.'" '.$checked.' >';
            }
          }
        }
        // $ret .= '<label for="ffn211026_sexo[0]">xx</label>';
        // $ret .= '<input type="radio" name="" id="" value="xx"  >';
        // $ret .= '<label for="ffn211026_sexo[0]">yy</label>';
        // $ret .= '<input type="radio" name="" id="" value="yy"  >';
        // $ret .= '<label for="ffn211026_sexo[0]">zz</label>';
        // $ret .= '<input type="radio" name="" id="" value="zz"  >';
        $ret .= '</td>';
      }
/*
        <label for="ffn211026_sexo[0]">Masculino</label>
        <input type="radio" name="ffn211026_sexo" id="ffn211026_sexo[0]" value="Masculino" <?php if($rows[0]['ffn211026_sexo']=='Masculino') echo 'checked'; ?> title="" autocomplete="off">        
        <label for="ffn211026_sexo[1]">Feminino</label>
        <input type="radio" name="ffn211026_sexo" id="ffn211026_sexo[1]" value="Feminino" <?php if($rows[0]['ffn211026_sexo']=='Feminino') echo 'checked'; ?> title="" autocomplete="off">
*/
      // echo "----codigo:".$codigo."---";
    }
    $ret .= '</tr>';
  }
  $ret .= '</tbody>';
  $ret .= '</table>';
  $ret .= '</div>';
  if($un_buttom==''){
    if($botao_anterior) {
      $botao_anterior_e = explode(":", $botao_anterior);
      $botao_anterior_save_ajax = isset($botao_anterior_e[2]) ? $botao_anterior_e[2] : '';
      $ret .= '<button type="submit" class="fix_001940_submit_prev" data-url-prev="'.$botao_anterior_e[1].'" data-url-save="'.$botao_anterior_save_ajax.'" data-dados="#'.$md.'_list_edit" >'.$botao_anterior_e[0].'</button> ';
    }
    $ret .= '<button type="submit" >Atualizar</button> ';
    if($botao_proximo) {
      $botao_proximo_e = explode(":", $botao_proximo);
      $botao_proximo_save_ajax = isset($botao_proximo_e[2]) ? $botao_proximo_e[2] : '';
      // print_r($botao_proximo_e);
      $ret .= '<button type="submit" class="fix_001940_submit_next"  
      data-url-next="'.$botao_proximo_e[1].'" 
      data-url-save="'.$botao_proximo_save_ajax.'" 
      data-dados="#'.$md.'_list_edit" >'.$botao_proximo_e[0].'</button> ';
    }
  }
  $ret .= '</form>';
  return $ret;
}
add_shortcode("fix_001940_list_edit", "fix_001940_list_edit");
function fix_001940_get_md_rows_v2($md, $fields, $col, $df=array(),$cnn=""){
  global $wpdb;
  
  $udir = wp_upload_dir();
  $rows = array();
  $sql_ordem = '';
  $modulo_conf = fix_001940_get_modulo_conf($md);
  $grupo = '';//xxxxxxxxxrevisao $modulo_conf['grupo'];
  $user = '';//xxxxxxxxxxxx $modulo_conf['user'];
  $tabela = '';//xxxxxxxxxxxxx$modulo_conf['tabela'];
  $tabela_name = fix_001940_prefix(true).$tabela;
  $tabela_cliente = fix_001940_prefix(false).$tabela;
  $tabela_campo = $tabela;
  $limit = 10;//xxxxxxxxxxxxxx$modulo_conf['limit'] ? $modulo_conf['limit'] : 20 ;
  $sort = array();
  $start = 0;
  $wh = '';
  for ($i=0; $i < count($col); $i++) {
    $campo = $col[$i]['dataIndex'];
    $value = isset($_REQUEST[$campo]) ? sanitize_text_field($_REQUEST[$campo]) : '';
    if($value){
  //     if($col[$i]['filter_type'] == 'date'){
  //       $value = strip_tags($value);
  //       $value = "'".fix_001940_date_mysql_br($value)."'";//'---';//
  //     }
  //     if($col[$i]['filter_type'] == 'string'){
  //       $value = "'".($value)."'";
  //     }
      if($col[$i]['tipo'] == 'int'){
        $value = "'".($value)."'"; 
      }
      $wh .= ' and '.$campo." = ". $value." ";
  //}
  //   $campo_ini = $col[$i]['dataIndex'].'_ini_' ;
  //   $value_ini = isset($_REQUEST[$campo_ini]) ? sanitize_text_field($_REQUEST[$campo_ini]) : '';
  //   if($value_ini){
  //     if($col[$i]['filter_type'] == 'date'){
  //       $value_ini = strip_tags($value_ini);
  //       $value_ini = "'".fix_001940_date_mysql_br($value_ini)."'";//'---';//
  //     }
  //     $wh .= ' and '.$campo." >= ". $value_ini;
  //   }
  //   $campo_end = $col[$i]['dataIndex'].'_end_' ;
  //   $value_end = isset($_REQUEST[$campo_end]) ? sanitize_text_field($_REQUEST[$campo_end]) : '';
  //   if($value_end){
  //     $value_end = strip_tags($value_end);
  //     $value_end = "'".fix_001940_date_mysql_br($value_end)."'";//'---';//
  //     $wh .= ' and '.$campo." <= ". $value_end;
    }
  }
  if(isset($_REQUEST['start']) ? sanitize_text_field($_REQUEST['start']) : 0) $start = $_REQUEST['start'];
  if(isset($_REQUEST['limit']) ? sanitize_text_field($_REQUEST['limit']) : 0) $limit = $_REQUEST['limit'];
  $sort = isset($_REQUEST['sort']) ? sanitize_text_field($_REQUEST['sort']) : '';
  if($sort){
    $sql_ordem = 'order by '.$sort;
  }
  $sql_ordem = 'order by '.$md."_codigo DESC";
  $filters = isset($_REQUEST['filter']) ? sanitize_text_field($_REQUEST['filter']) : null;
  if (is_array($filters)) {
      $encoded = false;
  } else {
      $encoded = true;
      $filters = json_decode($filters);
  }
  // criterio - ini
  $crit_e = array();
  $crit_cp = array();
  $crit_sql = '';
  $i=0;
  $criterio = isset($df['criterio']) ? $df['criterio'] : '';
  if($criterio){
    $criterio = base64_decode($criterio);
    $crit_e = explode("&", $criterio);
    foreach($crit_e as $value){
      $values = explode("=", $value);
      $crit_cp[$i]['campo'] = $values[0];
      $crit_cp[$i]['value'] = '"'.$values[1].'"';
      if($i) $crit_sql .=" and ";
      $operad = "=";
      $crit_sql .= $crit_cp[$i]['campo']." ".$operad." ".$crit_cp[$i]['value'];
      $i++;
    }
    $crit_sql = " AND (".$crit_sql.") ";
  }
  $rows['crit_sql'] = $crit_sql;
  // criterio - end
  $where = ' 0 = 0 ';
  $where .= $wh;
  $where .= $crit_sql;
  $where .= fix_001940_get_filter_fixo($md);
  $where .= fix_001940_get_cliterio2($df);
  $qs = '';
  // -- filters  -- ini
  if (is_array($filters)) {
      for ($i=0;$i<count($filters);$i++){
          $filter = $filters[$i];
          if ($encoded) {
              $field = $filter->field;
              $value = $filter->value;
              $compare = isset($filter->comparison) ? $filter->comparison : null;
              $filterType = $filter->type;
          } else {
              $field = $filter['field'];
              $value = $filter['data']['value'];
              $compare = isset($filter['data']['comparison']) ? $filter['data']['comparison'] : null;
              $filterType = $filter['data']['type'];
          }
          switch($filterType){
              case 'string' : $qs .= " and ".$field." like '%".$value."%'"; Break;
              case 'list' :
                  if (strstr($value,',')){
                      $fi = explode(',',$value);
                      for ($q=0;$q<count($fi);$q++){
                          $fi[$q] = "'".$fi[$q]."'";
                      }
                      $value = implode(',',$fi);
                      $qs .= " and ".$field." in (".$value.")";
                  }else{
                      $qs .= " and ".$field." = '".$value."'";
                  }
              Break;
              case 'boolean' : $qs .= " and ".$field." = ".($value); Break;
              case 'numeric' :
                $value = preg_replace("/__user__/i",  get_membro_codigo($md), $value);
                  switch ($compare) {
                      case 'eq' : $qs .= " and ".$field." = ".$value; Break;
                      case 'lt' : $qs .= " and ".$field." <= ".$value; Break;
                      case 'gt' : $qs .= " and ".$field." >= ".$value; Break;
                  }
              Break;
              case 'date' :
                  switch ($compare) {
                      case 'eq' : $qs .= " and ".$field." = '".date('Y-m-d',strtotime($value))."'"; Break;
                      case 'lt' : $qs .= " and ".$field." <= '".date('Y-m-d',strtotime($value))."'"; Break;
                      case 'gt' : $qs .= " and ".$field." >= '".date('Y-m-d',strtotime($value))."'"; Break;
                  }
              Break;
          }
      }
      $where .= $qs;
  }
  // -- filters  -- end
  // TBARFILTER -- INI
  $tbarFilter = isset($_REQUEST['tbarFilter']) ? sanitize_text_field($_REQUEST['tbarFilter']) : '';
  if($tbarFilter){
    $filtro = '';
    for ($i=0;$i<count($fields);$i++){
      if($fields[$i]['type']=='string'){
        if($filtro) $filtro .= ' OR ';
        $filtro .= " ".$fields[$i]['name']." LIKE '%".$tbarFilter."%' ";
      }
    }
    $where .= ' and ('.$filtro.') ';
  }
  $busca = isset($_REQUEST['busca']) ? sanitize_text_field($_REQUEST['busca']) : '';
  if($busca){
    //SE TA BUSCANDO EM DETERMINADA COLUNA INDICADO PELO "COLUNA:TEXTO" - INI
    $if_busca_col = preg_match("/\:/", $busca);
    if($if_busca_col){
      $tmp0 = explode(":", $busca);
      $tmp_coluna = $tmp0[0];
      $tmp_value = $tmp0[1];
      $tmp_table_prefix = fix_001940_prefix(0);
      $tmp_table = $modulo_conf['tabela'];
      $where .= ' and ('.$tmp_table.'_'.$tmp_coluna.' = "'.$tmp_value.'") ';
      //SE TA BUSCANDO EM DETERMINADA COLUNA INDICADO PELO "COLUNA:TEXTO" - END
    }else{
      $filtro = '';
      for ($i=0;$i<count($fields);$i++){
        if(($fields[$i]['type']=='string') || ($fields[$i]['type']=='blob') || ($fields[$i]['type']=='varchar')){
          if($filtro) $filtro .= ' OR ';
          $filtro .= " ".$fields[$i]['name']." LIKE '%".$busca."%' ";
        }
      }
      $where .= ' and ('.$filtro.') ';
    }
  }
  // TBARFILTER -- END
  // ref_loc -- ini
  $ref_loc = isset($_REQUEST['ref_loc']) ? sanitize_text_field($_REQUEST['ref_loc']) : '';
  if($ref_loc=='undefined') $ref_loc = '';
  if($ref_loc){
    $filtro = '';
    $ff=0;
    for ($i=0;$i<count($fields);$i++){
      if($fields[$i]['type']=='string'){
        if($filtro) $filtro .= ' OR ';
        $filtro .= " ".$fields[$i]['name']." LIKE '%".$ref_loc."%' ";
        $ff++;
      }
    }
    if($ff){
      $where .= ' and ('.$filtro.') ';
    }
  }
  $i = 0;
  
  // if($df["sql_order"]){
  //   $sql_ordem = $df["sql_order"];
  // } else {
  //   if($sql_ordem){ //se vem da url
  //   } else{
  //     if($modulo_conf['sql_ordem']){
  //       $sql_ordem = ' order by '.$modulo_conf['sql_ordem'];
  //       if($modulo_conf['sql_dir']){
  //         $sql_ordem .= ' '.$modulo_conf['sql_dir'];
  //       }
  //     }
  //   }    
  // }
  $sql_ordem = '';
  if($df["sql_order"]){
    $sql_ordem .= " order by ".$df["sql_order"];
  }
  if($df["sql_dir"]){
    $sql_ordem .= " ".$df["sql_dir"];
  }
  $field = '';
  for ($i=0;$i<count($fields);$i++){
    if($i>0) $field .= ',';
    $field .= $fields[$i]["name"];
  }
  $coluna = '';
  $inner = $df['inner'];
  
  for ($i=0;$i<count($col);$i++){
    if($i>0) $coluna .= ',';
    $coluna .= $col[$i]["dataIndex"];
  }
  $de_sistema = '';//xxxxxxxxxxxxxxx($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = fix_001940_prefix($de_sistema).$md;
  $sql  = "";
  $sql .= "select ";
  $sql .= $coluna." ";
  if($df['col_add']){
    // $sql .= ', '.$df['col_add']." ";
  }
  $sql .= "from ".$tabela_cliente." ";
  
  $sql .= $inner." ";
  
  $sql .= " where ";
  $sql .= " ".$where;
  $sql .= $sql_ordem." ";
  $sql .= "limit ".$start.", ".$limit;
  $sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  $sql = preg_replace("/__prefix__/i",  fix_001940_prefix(true), $sql);
  if($df['die_sql']){
    print($sql);
  }
  $tb = fix_001940_db_data($sql,'rows',$cnn);
  $rows['row'] = array();
  $campo_codigo = $tabela_campo.'_codigo';
  if((isset($tb['r'])) && ($tb['r']))
  for ($i=0;$i<$tb['r'];$i++){
    for ($ii=0;$ii<count($fields);$ii++){
      $campo = $col[$ii]['dataIndex'];
      $rows['row'][$i][$campo] = $tb['rows'][$i][($campo)];
      if($fields[$ii]['type']=='string'){
        $rows['row'][$i][$campo] =  strip_tags( $rows['row'][$i][$campo] );
        $rows['row'][$i][$campo] = ($rows['row'][$i][$campo]);//esse resolveu
      }
      if($fields[$ii]['type']=='date'){
        $rows['row'][$i][$campo] = fix_001940_date_mysql_br($rows['row'][$i][$campo]);
        // $rows['row'][$i][$campo] = '11/11/1111';
        //fix_001940_date_mysql_br
      }
      if($fields[$ii]['type']=='blob'){
        $rows['row'][$i][$campo] = ($rows['row'][$i][$campo]);//esse resolveu
      }
      if($col[$ii]['dataIndex']==$campo_codigo){
        $rows['row'][$i][$campo] = str_pad($rows['row'][$i][$campo], 6, "0", STR_PAD_LEFT);
      }
    }
  }
  //---fix_001940_date_mysql_br---
  // echo '<pre>';
  // print_r($rows['row']);
  // echo '</pre>';
  //TROCA URL - INI
  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  if(substr($url,1,6)=='wpmsc') {
    $add_class = "wpmsc_link_ajax";
  };
  $codigo = fix_001940_get_cod();
  if((isset($tb['r'])) && ($tb['r']))
  for ($i=0;$i<$tb['r'];$i++){
    for ($ii=0;$ii<count($fields);$ii++){
      $url_painel = $fields[$ii]['url_painel'];
      $vai = 1;
      if($url_painel){
        $vai = wpmsc_role_logic($url_painel);
      }
      if($vai){
        if($fields[$ii]['url']){
          $campo = $col[$ii]['dataIndex'];
          $value = $rows['row'][$i][$campo];
          $value = $fields[$ii]['url'];
          $campo_codigo = $tabela_campo.'_codigo';
          $value = html_entity_decode($value);
          $value = preg_replace("/__tcod__/i",  strip_tags($rows['row'][$i][$campo_codigo]), $value);
          $value = preg_replace("/__this_cod__/i",  $rows['row'][$i][$campo_codigo], $value);
          $value = preg_replace("/__cod__/i",  $rows['row'][$i][$campo_codigo], $value);
          $value = preg_replace("/__xxx__/i",  '__yyy__', $value);
          $value = preg_replace("/__codigo__/i",  $codigo, $value);
          $value = preg_replace("/__pai__/i",  fix_001940_get_pai(), $value);
          $value = preg_replace("/__this__/i", $rows['row'][$i][$campo], $value);
          $value = preg_replace("/__ucod__/", fix_001940_get_cod() , $value);
          $value = preg_replace("/__site_url__/",site_url() , $value);
          $value = preg_replace("/__upload_dir__/",$udir['baseurl'] , $value);
          $value = preg_replace("/__wpmsc_ajax_url__/",$url , $value);
          $value = preg_replace("/__wpmsc_class_url__/",$add_class , $value);
          $value = preg_replace("/__user__/i",  get_current_user_id(), $value);
          // $value = '--=--';
          $value = html_entity_decode($value);
          $rows['row'][$i][$campo] = $value;
          for ($iii=0;$iii<  count($fields); $iii++){
            $campoiii = strtolower($fields[$iii]["name"]);
            // $campoiii = ($fields[$iii]["name"]);
            if (preg_match("/__".$campoiii."__/i", $value)) {
              $value = preg_replace("/__".$campoiii."__/i",   $rows['row'][$i][$campoiii]   , $value);
            }
          }
          $rows['row'][$i][$campo] = $value;
        }
      }
    }
  }
  //TROCA URL - END
//--- TOTAL
  $sql = "select count(".$col[0]["dataIndex"].") qtd ";
  $sql .= " from ".$tabela_cliente;
  $sql .= " ".$inner;
  $sql .= " where ";
  $sql .= $where;
  $sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  $tb = fix_001940_db_data($sql,'rows',$cnn);
  $rows['total'] = isset($tb['rows'][0]['qtd']) ? $tb['rows'][0]['qtd'] : 0;
  $somas = array();
  for ($ii=0;$ii<count($fields);$ii++){
    $somas[$ii] = '-';
      // $field = strtoupper($fields[$ii]["name"]);
    $field = $fields[$ii]["name"];
  }
  $rows['db_host'] = get_the_author_meta('db_host', get_current_user_id());
  return $rows;
}
function fix_001940_get_md_rows_to_list($md, $fields, $col, $df=array(),$cnn=""){
  global $wpdb;
  
  $udir = wp_upload_dir();
  $rows = array();
  $sql_ordem = '';
  $modulo_conf = fix_001940_get_modulo_conf($md);
  $grupo = isset($modulo_conf['grupo']) ? $modulo_conf['grupo'] : '';
  $user = isset($modulo_conf['user']) ? $modulo_conf['user'] : '';
  $tabela = isset($modulo_conf['tabela']) ? $modulo_conf['tabela'] : '';
  $tabela_name = fix_001940_prefix(true).$tabela;
  $tabela_cliente = fix_001940_prefix(false).$tabela;
  $tabela_campo = $tabela;
  $limit = isset($modulo_conf['limit']) ? $modulo_conf['limit'] : 20 ;
  $sort = array();
  $start = 0;
  $wh = '';
  for ($i=0; $i < count($col); $i++) {
    $campo = $col[$i]['dataIndex'];
    $value = isset($_REQUEST[$campo]) ? sanitize_text_field($_REQUEST[$campo]) : '';
    if($value){
  //     if($col[$i]['filter_type'] == 'date'){
  //       $value = strip_tags($value);
  //       $value = "'".fix_001940_date_mysql_br($value)."'";//'---';//
  //     }
  //     if($col[$i]['filter_type'] == 'string'){
  //       $value = "'".($value)."'";
  //     }
      if($col[$i]['tipo'] == 'int'){
        $value = "'".($value)."'"; 
      }
      $wh .= ' and '.$campo." = ". $value." ";
  //}
  //   $campo_ini = $col[$i]['dataIndex'].'_ini_' ;
  //   $value_ini = isset($_REQUEST[$campo_ini]) ? sanitize_text_field($_REQUEST[$campo_ini]) : '';
  //   if($value_ini){
  //     if($col[$i]['filter_type'] == 'date'){
  //       $value_ini = strip_tags($value_ini);
  //       $value_ini = "'".fix_001940_date_mysql_br($value_ini)."'";//'---';//
  //     }
  //     $wh .= ' and '.$campo." >= ". $value_ini;
  //   }
  //   $campo_end = $col[$i]['dataIndex'].'_end_' ;
  //   $value_end = isset($_REQUEST[$campo_end]) ? sanitize_text_field($_REQUEST[$campo_end]) : '';
  //   if($value_end){
  //     $value_end = strip_tags($value_end);
  //     $value_end = "'".fix_001940_date_mysql_br($value_end)."'";//'---';//
  //     $wh .= ' and '.$campo." <= ". $value_end;
    }
  }
  if(isset($_REQUEST['start']) ? sanitize_text_field($_REQUEST['start']) : 0) $start = $_REQUEST['start'];
  if(isset($_REQUEST['limit']) ? sanitize_text_field($_REQUEST['limit']) : 0) $limit = $_REQUEST['limit'];
  $sort = isset($_REQUEST['sort']) ? sanitize_text_field($_REQUEST['sort']) : '';
  if($sort){
    $sql_ordem = 'order by '.$sort;
  }
  $sql_ordem = 'order by '.$md."_codigo DESC";
  $filters = isset($_REQUEST['filter']) ? sanitize_text_field($_REQUEST['filter']) : null;
  if (is_array($filters)) {
      $encoded = false;
  } else {
      $encoded = true;
      $filters = json_decode($filters);
  }
  // criterio - ini
  $crit_e = array();
  $crit_cp = array();
  $crit_sql = '';
  $i=0;
  $criterio = isset($df['criterio']) ? $df['criterio'] : '';
  if($criterio){
    $criterio = base64_decode($criterio);
    $crit_e = explode("&", $criterio);
    foreach($crit_e as $value){
      $values = explode("=", $value);
      $crit_cp[$i]['campo'] = $values[0];
      $crit_cp[$i]['value'] = '"'.$values[1].'"';
      if($i) $crit_sql .=" and ";
      $operad = "=";
      $crit_sql .= $crit_cp[$i]['campo']." ".$operad." ".$crit_cp[$i]['value'];
      $i++;
    }
    $crit_sql = " AND (".$crit_sql.") ";
  }
  $rows['crit_sql'] = $crit_sql;
  // criterio - end
  $where = ' 0 = 0 ';
  $where .= $wh;
  $where .= $crit_sql;
  $where .= fix_001940_get_filter_fixo($md);
  $where .= fix_001940_get_cliterio2($df);
  $qs = '';
  // -- filters  -- ini
  if (is_array($filters)) {
      for ($i=0;$i<count($filters);$i++){
          $filter = $filters[$i];
          if ($encoded) {
              $field = $filter->field;
              $value = $filter->value;
              $compare = isset($filter->comparison) ? $filter->comparison : null;
              $filterType = $filter->type;
          } else {
              $field = $filter['field'];
              $value = $filter['data']['value'];
              $compare = isset($filter['data']['comparison']) ? $filter['data']['comparison'] : null;
              $filterType = $filter['data']['type'];
          }
          switch($filterType){
              case 'string' : $qs .= " and ".$field." like '%".$value."%'"; Break;
              case 'list' :
                  if (strstr($value,',')){
                      $fi = explode(',',$value);
                      for ($q=0;$q<count($fi);$q++){
                          $fi[$q] = "'".$fi[$q]."'";
                      }
                      $value = implode(',',$fi);
                      $qs .= " and ".$field." in (".$value.")";
                  }else{
                      $qs .= " and ".$field." = '".$value."'";
                  }
              Break;
              case 'boolean' : $qs .= " and ".$field." = ".($value); Break;
              case 'numeric' :
                $value = preg_replace("/__user__/i",  get_membro_codigo($md), $value);
                  switch ($compare) {
                      case 'eq' : $qs .= " and ".$field." = ".$value; Break;
                      case 'lt' : $qs .= " and ".$field." <= ".$value; Break;
                      case 'gt' : $qs .= " and ".$field." >= ".$value; Break;
                  }
              Break;
              case 'date' :
                  switch ($compare) {
                      case 'eq' : $qs .= " and ".$field." = '".date('Y-m-d',strtotime($value))."'"; Break;
                      case 'lt' : $qs .= " and ".$field." <= '".date('Y-m-d',strtotime($value))."'"; Break;
                      case 'gt' : $qs .= " and ".$field." >= '".date('Y-m-d',strtotime($value))."'"; Break;
                  }
              Break;
          }
      }
      $where .= $qs;
  }
  // -- filters  -- end
  // TBARFILTER -- INI
  $tbarFilter = isset($_REQUEST['tbarFilter']) ? sanitize_text_field($_REQUEST['tbarFilter']) : '';
  if($tbarFilter){
    $filtro = '';
    for ($i=0;$i<count($fields);$i++){
      if($fields[$i]['type']=='string'){
        if($filtro) $filtro .= ' OR ';
        $filtro .= " ".$fields[$i]['name']." LIKE '%".$tbarFilter."%' ";
      }
    }
    $where .= ' and ('.$filtro.') ';
  }
  $busca = isset($_REQUEST['busca']) ? sanitize_text_field($_REQUEST['busca']) : '';
  if($busca){
    //SE TA BUSCANDO EM DETERMINADA COLUNA INDICADO PELO "COLUNA:TEXTO" - INI
    $if_busca_col = preg_match("/\:/", $busca);
    if($if_busca_col){
      $tmp0 = explode(":", $busca);
      $tmp_coluna = $tmp0[0];
      $tmp_value = $tmp0[1];
      $tmp_table_prefix = fix_001940_prefix(0);
      $tmp_table = $modulo_conf['tabela'];
      $where .= ' and ('.$tmp_table.'_'.$tmp_coluna.' = "'.$tmp_value.'") ';
      //SE TA BUSCANDO EM DETERMINADA COLUNA INDICADO PELO "COLUNA:TEXTO" - END
    }else{
      $filtro = '';
      for ($i=0;$i<count($fields);$i++){
        if(($fields[$i]['type']=='string') || ($fields[$i]['type']=='blob') || ($fields[$i]['type']=='varchar')){
          if($filtro) $filtro .= ' OR ';
          $filtro .= " ".$fields[$i]['name']." LIKE '%".$busca."%' ";
        }
      }
      $where .= ' and ('.$filtro.') ';
    }
  }
  // TBARFILTER -- END
  // ref_loc -- ini
  $ref_loc = isset($_REQUEST['ref_loc']) ? sanitize_text_field($_REQUEST['ref_loc']) : '';
  if($ref_loc=='undefined') $ref_loc = '';
  if($ref_loc){
    $filtro = '';
    $ff=0;
    for ($i=0;$i<count($fields);$i++){
      if($fields[$i]['type']=='string'){
        if($filtro) $filtro .= ' OR ';
        $filtro .= " ".$fields[$i]['name']." LIKE '%".$ref_loc."%' ";
        $ff++;
      }
    }
    if($ff){
      $where .= ' and ('.$filtro.') ';
    }
  }
  $i = 0;
  
  // if($df["sql_order"]){
  //   $sql_ordem = $df["sql_order"];
  // } else {
  //   if($sql_ordem){ //se vem da url
  //   } else{
  //     if($modulo_conf['sql_ordem']){
  //       $sql_ordem = ' order by '.$modulo_conf['sql_ordem'];
  //       if($modulo_conf['sql_dir']){
  //         $sql_ordem .= ' '.$modulo_conf['sql_dir'];
  //       }
  //     }
  //   }    
  // }
  $sql_ordem = '';
  if($df["sql_order"]){
    $sql_ordem .= " order by ".$df["sql_order"];
  }
  if($df["sql_dir"]){
    $sql_ordem .= " ".$df["sql_dir"];
  }
  $field = '';
  for ($i=0;$i<count($fields);$i++){
    if($i>0) $field .= ',';
    $field .= $fields[$i]["name"];
  }
  $coluna = '';
  $inner = $df['inner'];
  
  for ($i=0;$i<count($col);$i++){
    if($i>0) $coluna .= ',';
    $coluna .= $col[$i]["dataIndex"];
  }
  $de_sistema = isset($modulo_conf['de_sistema']) ? $modulo_conf['de_sistema'] : '';
  $de_sistema = ($de_sistema=='s') ? true : false;
  // ($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = fix_001940_prefix($de_sistema).$md;
  $sql  = "";
  $sql .= "select ";
  $sql .= $coluna." ";
  if($df['col_add']){
    // $sql .= ', '.$df['col_add']." ";
  }
  $sql .= "from ".$tabela_cliente." ";
  
  $sql .= $inner." ";
  
  $sql .= " where ";
  $sql .= " ".$where;
  $sql .= $sql_ordem." ";
  $sql .= "limit ".$start.", ".$limit;
  $sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  $sql = preg_replace("/__prefix__/i",  fix_001940_prefix(true), $sql);
  if($df['die_sql']){
    print($sql);
  }
  $tb = fix_001940_db_data($sql,'rows',$cnn);
  $rows['row'] = array();
  $campo_codigo = $tabela_campo.'_codigo';
  if((isset($tb['r'])) && ($tb['r']))
  for ($i=0;$i<$tb['r'];$i++){
    // for ($ii=0;$ii<count($fields);$ii++){
    for ($ii=0;$ii<count($col);$ii++){
      $campo = $col[$ii]['dataIndex'];
      $rows['row'][$i][$campo] = $tb['rows'][$i][($campo)];
      
      if($col[$ii]['tipo']=='date'){
        $rows['row'][$i][$campo] = fix_001940_date_mysql_br($rows['row'][$i][$campo]);
      }
      // $rows['row'][$i][$campo] .= '-'.$col[$ii]['tipo'];
      // if($fields[$ii]['type']=='string'){
      //   $rows['row'][$i][$campo] =  strip_tags( $rows['row'][$i][$campo] );
      //   $rows['row'][$i][$campo] = ($rows['row'][$i][$campo]);//esse resolveu
      // }
      // if($fields[$ii]['type']=='date'){
      //   // $rows['row'][$i][$campo] = fix_001940_date_mysql_br($rows['row'][$i][$campo]);
      //   // $rows['row'][$i][$campo] = '11/11/1111';
      //   //fix_001940_date_mysql_br
      //   $rows['row'][$i][$campo] = 'x'.$rows['row'][$i][$campo].'x';
      // }
      // if($fields[$ii]['type']=='blob'){
      //   $rows['row'][$i][$campo] = ($rows['row'][$i][$campo]);//esse resolveu
      // }
      // if($col[$ii]['dataIndex']==$campo_codigo){
      //   $rows['row'][$i][$campo] = str_pad($rows['row'][$i][$campo], 6, "0", STR_PAD_LEFT);
      // }
    }
  }
  //---fix_001940_date_mysql_br---
  // echo '<pre>';
  // print_r($rows['row']);
  // print_r($col);
  // echo '</pre>';
  //TROCA URL - INI
  $ret = "";
  $url = isset($_SERVER["REDIRECT_URL"]) ? $_SERVER["REDIRECT_URL"] :'';
  $add_class = "wpmsc";
  if(substr($url,1,6)=='wpmsc') {
    $add_class = "wpmsc_link_ajax";
  };
  $codigo = fix_001940_get_cod();
  if((isset($tb['r'])) && ($tb['r']))
  for ($i=0;$i<$tb['r'];$i++){
    for ($ii=0;$ii<count($fields);$ii++){
      $url_painel = $fields[$ii]['url_painel'];
      $vai = 1;
      if($url_painel){
        $vai = wpmsc_role_logic($url_painel);
      }
      if($vai){
        if($fields[$ii]['url']){
          $campo = $col[$ii]['dataIndex'];
          $value = $rows['row'][$i][$campo];
          $value = $fields[$ii]['url'];
          $campo_codigo = $tabela_campo.'_codigo';
          $value = html_entity_decode($value);
          $value = preg_replace("/__tcod__/i",  strip_tags($rows['row'][$i][$campo_codigo]), $value);
          $value = preg_replace("/__this_cod__/i",  $rows['row'][$i][$campo_codigo], $value);
          $value = preg_replace("/__cod__/i",  $rows['row'][$i][$campo_codigo], $value);
          $value = preg_replace("/__xxx__/i",  '__yyy__', $value);
          $value = preg_replace("/__codigo__/i",  $codigo, $value);
          $value = preg_replace("/__pai__/i",  fix_001940_get_pai(), $value);
          $value = preg_replace("/__this__/i", $rows['row'][$i][$campo], $value);
          $value = preg_replace("/__ucod__/", fix_001940_get_cod() , $value);
          $value = preg_replace("/__site_url__/",site_url() , $value);
          $value = preg_replace("/__upload_dir__/",$udir['baseurl'] , $value);
          $value = preg_replace("/__wpmsc_ajax_url__/",$url , $value);
          $value = preg_replace("/__wpmsc_class_url__/",$add_class , $value);
          $value = preg_replace("/__user__/i",  get_current_user_id(), $value);
          // $value = '--=--';
          $value = html_entity_decode($value);
          $rows['row'][$i][$campo] = $value;
          for ($iii=0;$iii<  count($fields); $iii++){
            $campoiii = strtolower($fields[$iii]["name"]);
            if (preg_match("/__".$campoiii."__/i", $value)) {
              $value = preg_replace("/__".$campoiii."__/i",   $rows['row'][$i][$campoiii]   , $value);
            }
          }
          $rows['row'][$i][$campo] = $value;
        }
      }
    }
  }
  //TROCA URL - END
//--- TOTAL
  $sql = "select count(".$col[0]["dataIndex"].") qtd ";
  $sql .= " from ".$tabela_cliente;
  $sql .= " ".$inner;
  $sql .= " where ";
  $sql .= $where;
  $sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  $tb = fix_001940_db_data($sql,'rows',$cnn);
  $rows['total'] = isset($tb['rows'][0]['qtd']) ? $tb['rows'][0]['qtd'] : 0;
  $somas = array();
  for ($ii=0;$ii<count($fields);$ii++){
    $somas[$ii] = '-';
      // $field = strtoupper($fields[$ii]["name"]);
    $field = $fields[$ii]["name"];
  }
  $rows['db_host'] = get_the_author_meta('db_host', get_current_user_id());
  return $rows;
}

function fix_001940_get_md_rows_old($md, $fields, $col, $df=array(),$cnn=""){
  global $wpdb;
  
  $udir = wp_upload_dir();
  $rows = array();
  $sql_ordem = '';
  $modulo_conf = fix_001940_get_modulo_conf($md);
  $grupo = '';//xxxxxxxxxrevisao $modulo_conf['grupo'];
  $user = '';//xxxxxxxxxxxx $modulo_conf['user'];
  $tabela = '';//xxxxxxxxxxxxx$modulo_conf['tabela'];
  $tabela_name = fix_001940_prefix(true).$tabela;
  $tabela_cliente = fix_001940_prefix(false).$tabela;
  $tabela_campo = $tabela;
  $limit = 20;//xxxxxxxxxxxxxx$modulo_conf['limit'] ? $modulo_conf['limit'] : 20 ;
  $sort = array();
  $start = 0;
  $wh = '';
  for ($i=0; $i < count($col); $i++) {
    $campo = $col[$i]['dataIndex'];
    $value = isset($_REQUEST[$campo]) ? sanitize_text_field($_REQUEST[$campo]) : '';
    if($value){
  //     if($col[$i]['filter_type'] == 'date'){
  //       $value = strip_tags($value);
  //       $value = "'".fix_001940_date_mysql_br($value)."'";//'---';//
  //     }
  //     if($col[$i]['filter_type'] == 'string'){
  //       $value = "'".($value)."'";
  //     }
      if($col[$i]['tipo'] == 'int'){
        $value = "'".($value)."'"; 
      }
      $wh .= ' and '.$campo." = ". $value." ";
  //}
  //   $campo_ini = $col[$i]['dataIndex'].'_ini_' ;
  //   $value_ini = isset($_REQUEST[$campo_ini]) ? sanitize_text_field($_REQUEST[$campo_ini]) : '';
  //   if($value_ini){
  //     if($col[$i]['filter_type'] == 'date'){
  //       $value_ini = strip_tags($value_ini);
  //       $value_ini = "'".fix_001940_date_mysql_br($value_ini)."'";//'---';//
  //     }
  //     $wh .= ' and '.$campo." >= ". $value_ini;
  //   }
  //   $campo_end = $col[$i]['dataIndex'].'_end_' ;
  //   $value_end = isset($_REQUEST[$campo_end]) ? sanitize_text_field($_REQUEST[$campo_end]) : '';
  //   if($value_end){
  //     $value_end = strip_tags($value_end);
  //     $value_end = "'".fix_001940_date_mysql_br($value_end)."'";//'---';//
  //     $wh .= ' and '.$campo." <= ". $value_end;
    }
  }
  if(isset($_REQUEST['start']) ? sanitize_text_field($_REQUEST['start']) : 0) $start = $_REQUEST['start'];
  if(isset($_REQUEST['limit']) ? sanitize_text_field($_REQUEST['limit']) : 0) $limit = $_REQUEST['limit'];
  // $sort = isset($_REQUEST['sort']) ? sanitize_text_field($_REQUEST['sort']) : '';
  // if($sort){
  //   $sql_ordem = 'order by '.$sort;
  // }
  

  $sql_ordem = 'order by '.$md."_codigo DESC";
  $filters = isset($_REQUEST['filter']) ? sanitize_text_field($_REQUEST['filter']) : null;
  if (is_array($filters)) {
      $encoded = false;
  } else {
      $encoded = true;
      $filters = json_decode($filters);
  }
  // criterio - ini
  $crit_e = array();
  $crit_cp = array();
  $crit_sql = '';
  $i=0;
  $criterio = isset($df['criterio']) ? $df['criterio'] : '';
  if($criterio){
    $criterio = base64_decode($criterio);
    $crit_e = explode("&", $criterio);
    foreach($crit_e as $value){
      $values = explode("=", $value);
      $crit_cp[$i]['campo'] = $values[0];
      $crit_cp[$i]['value'] = '"'.$values[1].'"';
      if($i) $crit_sql .=" and ";
      $operad = "=";
      $crit_sql .= $crit_cp[$i]['campo']." ".$operad." ".$crit_cp[$i]['value'];
      $i++;
    }
    $crit_sql = " AND (".$crit_sql.") ";
  }
  $rows['crit_sql'] = $crit_sql;
  // criterio - end
  $where = ' 0 = 0 ';
  $where .= $wh;
  $where .= $crit_sql;
  $where .= fix_001940_get_filter_fixo($md);
  $where .= fix_001940_get_cliterio2($df);
  $qs = '';
  // -- filters  -- ini
  if (is_array($filters)) {
      for ($i=0;$i<count($filters);$i++){
          $filter = $filters[$i];
          if ($encoded) {
              $field = $filter->field;
              $value = $filter->value;
              $compare = isset($filter->comparison) ? $filter->comparison : null;
              $filterType = $filter->type;
          } else {
              $field = $filter['field'];
              $value = $filter['data']['value'];
              $compare = isset($filter['data']['comparison']) ? $filter['data']['comparison'] : null;
              $filterType = $filter['data']['type'];
          }
          switch($filterType){
              case 'string' : $qs .= " and ".$field." like '%".$value."%'"; Break;
              case 'list' :
                  if (strstr($value,',')){
                      $fi = explode(',',$value);
                      for ($q=0;$q<count($fi);$q++){
                          $fi[$q] = "'".$fi[$q]."'";
                      }
                      $value = implode(',',$fi);
                      $qs .= " and ".$field." in (".$value.")";
                  }else{
                      $qs .= " and ".$field." = '".$value."'";
                  }
              Break;
              case 'boolean' : $qs .= " and ".$field." = ".($value); Break;
              case 'numeric' :
                $value = preg_replace("/__user__/i",  get_membro_codigo($md), $value);
                  switch ($compare) {
                      case 'eq' : $qs .= " and ".$field." = ".$value; Break;
                      case 'lt' : $qs .= " and ".$field." <= ".$value; Break;
                      case 'gt' : $qs .= " and ".$field." >= ".$value; Break;
                  }
              Break;
              case 'date' :
                  switch ($compare) {
                      case 'eq' : $qs .= " and ".$field." = '".date('Y-m-d',strtotime($value))."'"; Break;
                      case 'lt' : $qs .= " and ".$field." <= '".date('Y-m-d',strtotime($value))."'"; Break;
                      case 'gt' : $qs .= " and ".$field." >= '".date('Y-m-d',strtotime($value))."'"; Break;
                  }
              Break;
          }
      }
      $where .= $qs;
  }
  // -- filters  -- end
  // TBARFILTER -- INI
  $tbarFilter = isset($_REQUEST['tbarFilter']) ? sanitize_text_field($_REQUEST['tbarFilter']) : '';
  if($tbarFilter){
    $filtro = '';
    for ($i=0;$i<count($fields);$i++){
      if($fields[$i]['type']=='string'){
        if($filtro) $filtro .= ' OR ';
        $filtro .= " ".$fields[$i]['name']." LIKE '%".$tbarFilter."%' ";
      }
    }
    $where .= ' and ('.$filtro.') ';
  }
  $busca = isset($_REQUEST['busca']) ? sanitize_text_field($_REQUEST['busca']) : '';
  if($busca){
    //SE TA BUSCANDO EM DETERMINADA COLUNA INDICADO PELO "COLUNA:TEXTO" - INI
    $if_busca_col = preg_match("/\:/", $busca);
    if($if_busca_col){
      $tmp0 = explode(":", $busca);
      $tmp_coluna = $tmp0[0];
      $tmp_value = $tmp0[1];
      $tmp_table_prefix = fix_001940_prefix(0);
      $tmp_table = $modulo_conf['tabela'];
      $where .= ' and ('.$tmp_table.'_'.$tmp_coluna.' = "'.$tmp_value.'") ';
      //SE TA BUSCANDO EM DETERMINADA COLUNA INDICADO PELO "COLUNA:TEXTO" - END
    }else{
      $filtro = '';
      for ($i=0;$i<count($fields);$i++){
        if(($fields[$i]['type']=='string') || ($fields[$i]['type']=='blob') || ($fields[$i]['type']=='varchar')){
          if($filtro) $filtro .= ' OR ';
          $filtro .= " ".$fields[$i]['name']." LIKE '%".$busca."%' ";
        }
      }
      $where .= ' and ('.$filtro.') ';
    }
  }
  // TBARFILTER -- END
  // ref_loc -- ini
  $ref_loc = isset($_REQUEST['ref_loc']) ? sanitize_text_field($_REQUEST['ref_loc']) : '';
  if($ref_loc=='undefined') $ref_loc = '';
  if($ref_loc){
    $filtro = '';
    $ff=0;
    for ($i=0;$i<count($fields);$i++){
      if($fields[$i]['type']=='string'){
        if($filtro) $filtro .= ' OR ';
        $filtro .= " ".$fields[$i]['name']." LIKE '%".$ref_loc."%' ";
        $ff++;
      }
    }
    if($ff){
      $where .= ' and ('.$filtro.') ';
    }
  }
  $i = 0;
  
  // if($df["sql_order"]){
  //   $sql_ordem = $df["sql_order"];
  // } else {
  //   if($sql_ordem){ //se vem da url
  //   } else{
  //     if($modulo_conf['sql_ordem']){
  //       $sql_ordem = ' order by '.$modulo_conf['sql_ordem'];
  //       if($modulo_conf['sql_dir']){
  //         $sql_ordem .= ' '.$modulo_conf['sql_dir'];
  //       }
  //     }
  //   }    
  // }
  $sql_ordem = '';
  // if($df["sql_order"]){
  //   $sql_ordem .= " order by ".$df["sql_order"];
  // }
  // if($df["sql_dir"]){
  //   $sql_ordem .= " ".$df["sql_dir"];
  // }
  $field = '';
  for ($i=0;$i<count($fields);$i++){
    if($i>0) $field .= ',';
    $field .= $fields[$i]["name"];
  }
  $coluna = '';
  $inner = $df['inner'];
  
  for ($i=0;$i<count($col);$i++){
    if($i>0) $coluna .= ',';
    $coluna .= $col[$i]["dataIndex"];
  }
  $de_sistema = '';//xxxxxxxxxxxxxxx($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = fix_001940_prefix($de_sistema).$md;
  $sql  = "";
  $sql .= "select ";
  $sql .= $coluna." ";
  if($df['col_add']){
    // $sql .= ', '.$df['col_add']." ";
  }
  $sql .= "from ".$tabela_cliente." ";
  
  $sql .= $inner." ";
  
  $sql .= " where ";
  $sql .= " ".$where;
  $sql .= " order by ".$md."_codigo DESC ";
  $sql .= "limit ".$start.", ".$limit;
  $sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  $sql = preg_replace("/__prefix__/i",  fix_001940_prefix(true), $sql);
  if($df['die_sql']){
    print($sql);
  }
  $tb = fix_001940_db_data($sql,'rows',$cnn);
  $rows['row'] = array();
  $campo_codigo = $tabela_campo.'_codigo';
  if((isset($tb['r'])) && ($tb['r']))
  for ($i=0;$i<$tb['r'];$i++){
    for ($ii=0;$ii<count($fields);$ii++){
      $campo = $col[$ii]['dataIndex'];
      $rows['row'][$i][$campo] = $tb['rows'][$i][($campo)];
      if($fields[$ii]['type']=='string'){
        $rows['row'][$i][$campo] =  strip_tags( $rows['row'][$i][$campo] );
        $rows['row'][$i][$campo] = ($rows['row'][$i][$campo]);//esse resolveu
      }
      if($fields[$ii]['type']=='date'){
        $rows['row'][$i][$campo] = fix_001940_date_mysql_br($rows['row'][$i][$campo]);
        // $rows['row'][$i][$campo] = '11/11/1111';
        //fix_001940_date_mysql_br
      }
      if($fields[$ii]['type']=='blob'){
        $rows['row'][$i][$campo] = ($rows['row'][$i][$campo]);//esse resolveu
      }
      if($col[$ii]['dataIndex']==$campo_codigo){
        $rows['row'][$i][$campo] = str_pad($rows['row'][$i][$campo], 6, "0", STR_PAD_LEFT);
      }
    }
  }
  //---fix_001940_date_mysql_br---
  // echo '<pre>';
  // print_r($rows['row']);
  // echo '</pre>';
  //TROCA URL - INI
  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  if(substr($url,1,6)=='wpmsc') {
    $add_class = "wpmsc_link_ajax";
  };
  $codigo = fix_001940_get_cod();
  if((isset($tb['r'])) && ($tb['r']))
  for ($i=0;$i<$tb['r'];$i++){
    for ($ii=0;$ii<count($fields);$ii++){
      $url_painel = $fields[$ii]['url_painel'];
      $vai = 1;
      if($url_painel){
        $vai = wpmsc_role_logic($url_painel);
      }
      if($vai){
        if($fields[$ii]['url']){
          $campo = $col[$ii]['dataIndex'];
          $value = $rows['row'][$i][$campo];
          $value = $fields[$ii]['url'];
          $campo_codigo = $tabela_campo.'_codigo';
          $value = html_entity_decode($value);
          $value = preg_replace("/__tcod__/i",  strip_tags($rows['row'][$i][$campo_codigo]), $value);
          $value = preg_replace("/__this_cod__/i",  $rows['row'][$i][$campo_codigo], $value);
          $value = preg_replace("/__cod__/i",  $rows['row'][$i][$campo_codigo], $value);
          $value = preg_replace("/__xxx__/i",  '__yyy__', $value);
          $value = preg_replace("/__codigo__/i",  $codigo, $value);
          $value = preg_replace("/__pai__/i",  fix_001940_get_pai(), $value);
          $value = preg_replace("/__this__/i", $rows['row'][$i][$campo], $value);
          $value = preg_replace("/__ucod__/", fix_001940_get_cod() , $value);
          $value = preg_replace("/__site_url__/",site_url() , $value);
          $value = preg_replace("/__upload_dir__/",$udir['baseurl'] , $value);
          $value = preg_replace("/__wpmsc_ajax_url__/",$url , $value);
          $value = preg_replace("/__wpmsc_class_url__/",$add_class , $value);
          $value = preg_replace("/__user__/i",  get_current_user_id(), $value);
          // $value = '--=--';
          $value = html_entity_decode($value);
          $rows['row'][$i][$campo] = $value;
          for ($iii=0;$iii<  count($fields); $iii++){
            $campoiii = strtolower($fields[$iii]["name"]);
            if (preg_match("/__".$campoiii."__/i", $value)) {
              $value = preg_replace("/__".$campoiii."__/i",   $rows['row'][$i][$campoiii]   , $value);
            }
          }
          $rows['row'][$i][$campo] = $value;
        }
      }
    }
  }
  //TROCA URL - END
//--- TOTAL
  $sql = "select count(".$col[0]["dataIndex"].") qtd ";
  $sql .= " from ".$tabela_cliente;
  $sql .= " ".$inner;
  $sql .= " where ";
  $sql .= $where;
  $sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  $tb = fix_001940_db_data($sql,'rows',$cnn);
  $rows['total'] = isset($tb['rows'][0]['qtd']) ? $tb['rows'][0]['qtd'] : 0;
  $somas = array();
  for ($ii=0;$ii<count($fields);$ii++){
    $somas[$ii] = '-';
      // $field = strtoupper($fields[$ii]["name"]);
    $field = $fields[$ii]["name"];
  }
  $rows['db_host'] = get_the_author_meta('db_host', get_current_user_id());
  return $rows;
}
function fix_001940_get_md_col($md,$cnn='',$df=array()){
  $sql = "
  select
    fix001941_codigo,
    fix001941_ctr_list,
    fix001941_campo,
    fix001941_tipo,
    fix001941_label,
    fix001941_tabela
  from ".fix_001940_prefix(true)."fix001941
  where fix001941_tabela = '".$md."' 
  order by fix001941_ordem
  ";
  $tb = fix_001940_db_exe($sql,'rows');
  
  // echo "<pre>";
  // print_r($tb);
  // echo "</pre>";
  // return "_die_fix_001940_get_md_col";
  $rows = $tb['rows'];
  // $modulo_conf = fix_001940_get_modulo_conf($md);
  // echo "<pre>";
  // print_r($modulo_conf);
  // echo "</pre>";
  // return "_die_fix_001940_get_md_col";
  $tabela = $md;
  $tabela_name = fix_001940_prefix(true).$tabela;
  $tabela_cliente = fix_001940_prefix(false).$tabela;
  $tabela_campo = $tabela;
  // return "_die_fix_001940_get_md_col";
  $col = array();
  $c=0;
  for ($i=0;$i<$tb['r'];$i++) {
    $vai = fix_001940_select_vai($rows[$i]['fix001941_ctr_list'],'list');
    if($vai){
      $col[$c]["cd"]=$rows[$i]['fix001941_codigo'];
      $col[$c]["codigo_name"]=$tabela.'_codigo';
      $col[$c]["text"]=($rows[$i]['fix001941_label']);
      // $col[$c]["text"] = strtoupper($col[$c]["text"]);
      $col[$c]["dataIndex"]=$rows[$i]['fix001941_campo'];
      if($col[$c]["width"]) $col[$c]["width"] = $col[$c]["width"] * 1.5;
      $col[$c]["filter_type"]=$rows[$i]['fix001941_tipo'];
      $col[$c]["filter"]['type']=$rows[$i]['fix001941_tipo'];
      if($rows[$i]['fix001941_tipo']=='int') {
        $col[$c]["filter_type"] = 'numeric';
        $col[$c]["filter"]['type']  = 'numeric';
      }
      $col[$c]["inner"] = '';
      $col[$c]["ctr_list"] = $rows[$i]['fix001941_ctr_list'];
      $col[$c]["tipo"]=$rows[$i]['fix001941_tipo'];
     
      $c++;
    }
  }
  
//codigo_name:text:
  //xxxxxxxxxxxxxxxxxxxxxxxxxxxx corrigir isso
  // if($df['col_add']){
  //   $col_add_a=explode(",", $df['col_add']);
  //   foreach ($col_add_a as $key => $value) {
  //     $tmp_field_config = explode(":", $value);
  //     $col[$c]['codigo_name'] = $tmp_field_config[0];// $value;
  //     $col[$c]['text'] = $tmp_field_config[2];
  //     $col[$c]['dataIndex'] = $tmp_field_config[0];
  //     $col[$c]['width'] = '';
  //     $col[$c]['hidden'] = '';
  //     $col[$c]['filter_type'] = 'string';//'numeric';//$tmp_field_config[3];//
  //     $col[$c]['filter']['type'] = 'string';//'numeric';//$tmp_field_config[3];//
  //     $col[$c]['inner'] = '';
  //     $col[$c]['ctr_list'] = 'label';
  //     $c++;
  //   }
  // }
  
  // echo "<pre>";
  // print_r($col);
  // echo "</pre>";
  // return "_die_fix_001940_get_md_col";
  return $col;
}
function fix_001940_gera_senha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false){
  $lmin = 'abcdefghijklmnopqrstuvwxyz';
  $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $num = '1234567890';
  $simb = '!@#$%*-';
  $retorno = '';
  $caracteres = '';
  $caracteres .= $lmin;
  if ($maiusculas) $caracteres .= $lmai;
  if ($numeros) $caracteres .= $num;
  if ($simbolos) $caracteres .= $simb;
  $len = strlen($caracteres);
  for ($n = 1; $n <= $tamanho; $n++) {
    $rand = mt_rand(1, $len);
    $retorno .= $caracteres[$rand-1];
  }
  return $retorno;
}



function fix_001940_edit_old($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cnn" => '',
    "cod" => '0',
    "target_update" => '?op=update&cod=__cod__&pai=__pai__',
    "on_op" => '',
    "access" => '',
    "role" => '',
    "un_show" => '',
    "title" => '',
    "botao_proximo" => '',
    "botao_anterior" => '',
    "un_buttom" => ''
  ), $atts));
  if($access){if(!fix_001940_is_access($access)) return '';}
  if($role){ if(!fix_001940_is_role($role)) return '';}
  $get_url_if_op = fix_001940_get_op();
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
      if(!$get_url_if_op)  return '';
      if($get_url_if_op<>$on_op) return '';
    }
  }
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $cod = preg_replace("/__cod__/", fix_001940_get_cod() , $cod);
  $cod = preg_replace("/__user__/i",  get_current_user_id(), $cod);
  $target_update = preg_replace("/__cod__/", fix_001940_get_cod() , $target_update);
  $target_update = preg_replace("/__md__/", fix_001940_get_md() , $target_update);
  $target_update = preg_replace("/__pai__/", fix_001940_get_pai(), $target_update);
  $ret = '';
  if(!$md) {$ret = "fix_001940_edit - md não especificado";}
  if(!$cod) {$ret = "fix_001940_edit - cod não especificado";}
  if($ret) {return $ret;exit;}
  $edit = fix_001940_md_edit($md,$cod,$cnn);
  // echo "<pre>";
  // print_r($edit);
  // echo "</pre>";
  // die("__die__fix_001940_edit");
  $ttop = isset($_REQUEST['op']) ? sanitize_text_field($_REQUEST['op']) : '';
  if($ttop=='duplicar'){
    $ret .= '
    <script type="text/javascript">
    jQuery(function(){
    jQuery("#fmdsubmit").css("visibility","hidden");
    jQuery("#fmdsubmit").remove();
    jQuery("#fmdduplique").css("visibility","visible");
    // alert(333);
    });
    </script>
    ';
  }
  $ret .= '';
  $ret .= $title;
  $ret .= ' <form id="'.$md.'_edit" class="fix_001940_list_edit" action="'.$target_update.'" method="POST">';
  for ($i=0; $i < count($edit['campo']); $i++) {
    if(($un_show) && (preg_match("/".$edit['campo'][$i]['name']."/i", $un_show))){
    } else {
      $width = $edit['campo'][$i]['width'];
      if(!$width) {
        $width = '100%';
      } else {
        $width .= '%';
      }
      $ret .= '<div style="float:left;width:'.$width.';min-height:70px;">';
      $ret .= '<div class="">';
      $class = ''; 
      $attr = '';
      // if(current_user_can('subscriber')) {
      // if(fix_001940_is_role('master')) {
      // $user_id = get_current_user_id();
      if( get_current_user_id()==38 ){
        $class = 'master_edit';
        // $attr = 'data-edit=/ffn-cp-edit/?cod='.$edit['campo'][$i]['id_cp'];
        $attr = 'data-edit=/ffn/?md=fix001941&op=edit&cod='.$edit['campo'][$i]['id_cp'];
      }
      $ret .= '<label class="'.$class.'" '.$attr.'>'.$edit['campo'][$i]['fieldLabel'].'</label>';
      $ret .= '</div>';
      $ret .= '<div style="margin:0px 1px;" class="">';
      if($edit['campo'][$i]['xtype']=='textarea'){
        $ret .= '<textarea style="height:200px;" class="form-control" autocomplete="off" id="'.$edit['campo'][$i]['name'].'" name="'.$edit['campo'][$i]['name'].'" >'.$edit['campo'][$i]['value'].'</textarea>';  
      } elseif ($edit['campo'][$i]['xtype']=='combo_x1'){
        if($edit['campo'][$i]['cmb_tp']=='radio'){
          $cmb_source = $edit['campo'][$i]['cmb_source'];
          if ($cmb_source) {
            $cmb_source_e = explode("|", $cmb_source);
            $ret .= '<select name="'.$edit['campo'][$i]['name'].'" id="'.$edit['campo'][$i]['name'].'" >';
            foreach ($cmb_source_e as $key => $value) {
              if($edit['campo'][$i]['value']==$value) $selected = 'selected'; else $selected = '';
              $ret .= '<option value="'.$value.'" '.$selected.' >'.$value.'</option> ';
            }
            $ret .= '</select>';
          }
        }
        // $ret .= '<input type="text" style="height:30px;width:100%;" name="'.$edit['campo'][$i]['name'].'" id="'.$edit['campo'][$i]['name'].'" class="form-control" value="'.$edit['campo'][$i]['value'].'" title="" autocomplete="off">';  
      }else{
        $ret .= '<input type="text" style="height:30px;width:100%;" name="'.$edit['campo'][$i]['name'].'" id="'.$edit['campo'][$i]['name'].'" class="form-control" value="'.$edit['campo'][$i]['value'].'" title="" autocomplete="off">';  
      }
      $ret .= '</div>';
      $ret .= '<div></div>';
      $ret .= ' </div>';
    }
  }
  // $ret .= ' <div style="height:15px;clean:both;"></div>';
  // $ret .= ' <div style="height:200px;"></div>';
  $ret .= '<div class="" style="" >';
  // $ret .= '<div class=""></div>';
  // $ret .= '<div class="">';
  
  // $ret .= '<button id="fmdduplique" type="submit" name="duplique" class="" style="visibility: hidden;">Duplicar</button> ';
  if($un_buttom==''){
    if($botao_anterior) {
      $botao_anterior_e = explode(":", $botao_anterior);
      $botao_anterior_save_ajax = isset($botao_anterior_e[2]) ? $botao_anterior_e[2] : 'xx';
      // print_r($botao_proximo_e);
      $ret .= '<button type="submit" class="fix_001940_submit_prev"  
      data-url-prev="'.$botao_anterior_e[1].'" 
      data-url-save="'.$botao_anterior_save_ajax.'" 
      data-dados="#'.$md.'_edit">'.$botao_anterior_e[0].'</button> ';
    }
    $ret .= '<button id="fmdsubmit" type="submit" class="">Atualizar</button> ';
    
    if($botao_proximo) {
      $botao_proximo_e = explode(":", $botao_proximo);
      $botao_proximo_save_ajax = isset($botao_proximo_e[2]) ? $botao_proximo_e[2] : 'xx';
      // print_r($botao_proximo_e);
      $ret .= '<button type="submit" class="fix_001940_submit_next"  
      data-url-next="'.$botao_proximo_e[1].'" 
      data-url-save="'.$botao_proximo_save_ajax.'" 
      data-dados="#'.$md.'_edit">'.$botao_proximo_e[0].'</button> ';
    }
  }
  // $ret .= '</div>';
  // $ret .= '<div style=""></div>';
  $ret .= '</div>';
  $ret .= '</form>';
  //$ret .= ' <div style="height:300px;"></div>';
  return $ret;
}
add_shortcode("fix_001940_edit_old", "fix_001940_edit_old");

















function fix_001940_directory_to_array($directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '') {
    $arrayItems = array();
    $skipByExclude = false;
    $handle = opendir($directory);
    if ($handle) {
        while (false !== ($file = readdir($handle))) {
        preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
        if($exclude){
            preg_match($exclude, $file, $skipByExclude);
        }
        if (!$skip && !$skipByExclude) {
            if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
                if($recursive) {
                    $arrayItems = array_merge($arrayItems, fix_001940_object_to_array($directory. DIRECTORY_SEPARATOR . $file, $recursive, $listDirs, $listFiles, $exclude));
                }
                if($listDirs){
                    $file = $directory . DIRECTORY_SEPARATOR . $file;
                    $arrayItems[] = $file;
                }
            } else {
                if($listFiles){
                    $file = $directory . DIRECTORY_SEPARATOR . $file;
                    $arrayItems[] = $file;
                }
            }
        }
    }
    closedir($handle);
    }
    return $arrayItems;
}
function fix_001940_create_fields($tabela) {
  if(!$tabela) {echo 'tabela'; exit;}
  global $wpdb;
  $sql = "SHOW COLUMNS FROM ".$GLOBALS['wpdb']->prefix.$tabela;
  $tb = fix_001940_db_exe($sql,'rows');
  $tabela_len = strlen($tabela);
  $sql_name = "";
  $sql_value = "";
  
  $sql = "delete from ".$GLOBALS['wpdb']->prefix."fix001941 where fix001941_tabela = '".$tabela."';\n";
  $campos = array();
  for ($i=0; $i < $tb['r']; $i++) {
    $tb['rows'][$i]['label']  = $tb['rows'][$i]['Field'];
    $tb['rows'][$i]['tam']  = 10;
    $tb['rows'][$i]['tipo']  = 'string';
    $ctr_new = 'textfield';
    $ctr_edit = 'textfield';
    if(substr($tb['rows'][$i]['Type'], 0, 7) == 'varchar'){
      $tb['rows'][$i]['tipo']  = 'string';
      $tb['rows'][$i]['tam']  = 50;
    }
    if(substr($tb['rows'][$i]['Type'], 0, 5) == 'float'){
      $tb['rows'][$i]['tipo']  = 'float';
      $tb['rows'][$i]['tam']  = 20;
    }
    if(substr($tb['rows'][$i]['Type'], 0, 4) == 'date'){
     $tb['rows'][$i]['tipo']  = 'date'; 
     $tb['rows'][$i]['tam']  = 20;
    }
    if(substr($tb['rows'][$i]['Type'], 0, 3) == 'int'){
     $tb['rows'][$i]['tipo']  = 'int';
     $tb['rows'][$i]['tam']  = 20;
    }
    if(substr($tb['rows'][$i]['Type'], 0, 4) == 'text'){
      $tb['rows'][$i]['tipo']  = 'blob';
      $tb['rows'][$i]['tam']  = 50;
      $ctr_new = 'textarea';
      $ctr_edit = 'textarea';
    }
    $tb['rows'][$i]['fix001941_label'] = preg_replace("/_/", " ", $tb['rows'][$i]['fix001941_label']);
    $sql .= "insert into ".$GLOBALS['wpdb']->prefix."fix001941 (
      fix001941_tabela, 
      fix001941_campo, 
      fix001941_tipo, 
      fix001941_ordem, 
      fix001941_ctr_new, 
      fix001941_ctr_edit, 
      fix001941_ctr_view, 
      fix001941_ctr_list, 
      fix001941_label, 
      fix001941_ativo 
    ) values ( 
      '".$tabela."',
      '".$tb['rows'][$i]['Field']."', 
      '".$tb['rows'][$i]['tipo']."', 
      ".$i.", 
      '".$ctr_new."', 
      '".$ctr_edit."', 
      'label', 
      'label', 
      '".substr($tb['rows'][$i]['Field'], 10) ."', 
      's' 
    ); \n";
  }
//fix001941_
// Ao usar a funcao dbDelta() e necessario carregar este ficheiro
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        // Funcao que cria a tabela na bd e executa as otimizacoes necessarias
        // dbDelta( $sql );
    
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $mysqli->multi_query($sql);
  
  // echo '<pre>';
  // print_r($sql);
  // echo '</pre>';
  // die();
}
add_shortcode("fix_001940_buttom_ajax_save", "fix_001940_buttom_ajax_save");
function fix_001940_buttom_ajax_save($atts, $content = null) {
	extract(shortcode_atts(array(
		"label" => '',
		"form_id_1" => '',
		"form_id_2" => '',
		"target_1" => '',
		"target_2" => '',
		"redirect_pos_save" => ''
	), $atts));
	$ret = '';
	$ret .= '<button class="fix_001940_buttom_ajax_save" type="button" data-target_1="'.$target_1.'" data-target_2="'.$target_2.'" data-form_id_1="'.$form_id_1.'" data-form_id_2="'.$form_id_2.'" data-redirect_pos_save="'.$redirect_pos_save.'" >'.$label.'</button>';
	return $ret;
}
function fix_001940_add_param($querystring, $ParameterName, $ParameterValue){
    $queryStr = null; 
    $paramStr = null;
    if (strpos($querystring, '?') !== false)
        list($queryStr, $paramStr) = explode('?', $querystring);
    else if (strpos($querystring, '=') !== false)
        $paramStr = $querystring;
    else
        $queryStr = $querystring;
    $paramStr = $paramStr ? '&' . $paramStr : '';
    $paramStr = preg_replace ('/&' . $ParameterName . '(\[\])?=[^&]*/', '', $paramStr);
    if(is_array($ParameterValue)) {
        foreach($ParameterValue as $key => $val) {
            $paramStr .= "&" . urlencode($ParameterName) . "[]=" . urlencode($val);
        }
    } else {
        $paramStr .= "&" . urlencode($ParameterName) . "=" . urlencode($ParameterValue);
    }
    $paramStr = ltrim($paramStr, '&');
    return $queryStr ? $queryStr . '?' . $paramStr : $paramStr;
}


$fix001941_new_version = "1.0.0";
$fix001941_version = get_option('fix001941_version');
if(!$fix001941_version) {
  // echo '<h1>---install fix001941_version---</h1>';
  fix001941_create_tables();
  $fix001941_version = $fix001941_new_version;
  update_option('fix001941_version', $fix001941_version);
}


