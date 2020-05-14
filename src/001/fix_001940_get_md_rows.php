<?php

function fix_001940_get_md_rows($md, $fields, $col, $df=array(),$cnn=""){
  global $wpdb;
  //print_r($df);
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
  // $start = 0;
  $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
  $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
  // $start = isset($df['start']) ? $df['start'] : 0;

  //if(isset($_REQUEST['start']) ? sanitize_text_field($_REQUEST['start']) : 0) $start = $_REQUEST['start'];

  // if(isset($_REQUEST['limit']) ? sanitize_text_field($_REQUEST['limit']) : 0) $limit = $_REQUEST['limit'];
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
      // $where .= ' and ('.$tmp_table.'_'.$tmp_coluna.' = "'.$tmp_value.'") ';
      // $where .= ' and ('.$md.'_'.$tmp_coluna.' = "'.$tmp_value.'") ';
      $where .= ' and ( '.$tmp_coluna.' = "'.$tmp_value.'") ';
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
  // $sql .= $coluna." ";
  $sql .= " * ";
  if($df['col_add']){
    $sql .= ', '.$df['col_add']." ";
  }
  $sql .= "from ".$tabela_cliente." ";
  
  $sql .= $inner." ";
  
  $sql .= " where ";
  $sql .= " ".$where;

  $order_by = isset($_REQUEST[$md."_order_by"]) ? $_REQUEST[$md."_order_by"] : $md."_codigo ";
  $sorter_by = isset($_REQUEST[$md."_sorter_by"]) ? $_REQUEST[$md."_sorter_by"] : " DESC ";
  // if($order_by) $order_by = 
  // if()
  // $sql .= " order by ".$md."_codigo DESC ";
  $sql .= " order by ".$order_by." ".$sorter_by;

  $sql .= " limit ".$start.", ".$limit;
  $sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  $sql = preg_replace("/__prefix__/i",  fix_001940_prefix(true), $sql);
  if($df['die_sql']){
    print($sql);
  }
  //echo '<pre>';
  //print_r($sql);
  //echo '</pre>';


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



  $rows['data_sql'] = $tb['rows'];

  //---fix_001940_date_mysql_br---
  // echo '<pre>';
  // print_r($rows);
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

  //echo '<pre>';
  //print_r($sql);
  //echo '</pre>';

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