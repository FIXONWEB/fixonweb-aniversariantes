<?php 

function fix_001940_nnew($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
    "restrito" => 's',
    "target_insert" => '?op=insert&pai=__pai__',
    "label_submit" => 'Salvar',
    "title" => '',
    "access" => '',
    "role" => '',
    "on_op" => '',
    "un_show" => '',
    "class" => '',
    "add_class" => '',
    "access_manager" => '',
    "add_cp_class" => '', // add_cp_class="fix_001940_000000_nome:fix_001940_class_busca_nome:__site_url__/ajax_busca_nome/"
    "combo_ajax" => ''
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
  $df['md'] =$md;
  $md = preg_replace("/__md__/", fix_001940_get_md() , $md);
  $target_insert = preg_replace("/__md__/", fix_001940_get_md() , $target_insert);
  $target_insert = preg_replace("/__cod__/", fix_001940_get_cod() , $target_insert);
  $target_insert = preg_replace("/__pai__/", fix_001940_get_pai() , $target_insert);
  $ret = '';
  if(!$md) {$ret = "nnew - md não especificado";}
  if($ret) {return $ret;exit;}
  $nnew = fix_001940_get_md_novo($md);
  ob_start();
  ?>
  <style type="text/css">
    #<?php echo $df['md'] ?>_nnew {
      display: grid;
      grid-template-columns: 1fr 1fr;  
    }
    #<?php echo $df['md'] ?>_nnew label {
      text-align: right;
      padding: 2px;
      text-transform: uppercase;
    }
    #<?php echo $df['md'] ?>_nnew input {
      border: 1px solid black;
      margin: 2px;
    }

    @media (max-width: 600px) {
      #<?php echo $df['md'] ?>_nnew {
        grid-template-columns: 1fr;   
      }
      #<?php echo $df['md'] ?>_nnew label {
        text-align: left;
        padding: 0px;
      }
    }
  </style>
  <div style="border:0px solid gray;padding:10px;">
    <form id="<?php echo $df['md'] ?>_nnew" enctype="multipart/form-data" class="<?php echo $add_class ?>" action="<?php echo $target_insert ?>" method="POST" style="padding:0px;margin:0px;">
      <?php foreach ($nnew['campo'] as $key => $value) { ?>
        <?php $t_campo = $value['name']; ?>
        <?php $t_value = isset($_REQUEST[$t_campo]) ? sanitize_text_field($_REQUEST[$t_campo]) : ''; ?>
        <?php $is_show = !preg_match("/".$t_campo." /", $un_show) ?>
        <?php $value['fieldLabel'] = preg_replace("/_/", " ", $value['fieldLabel']) ?>
        <?php if($is_show) { ?>
          <label for="<?php echo $value['name'] ?>"><?php echo $value['fieldLabel'] ?></label>
          <?php if($value['type']=='blob'){ ?>
            <textarea class="form--control" autocomplete="off" id="<?php echo $value['name'] ?>" name="<?php echo $value['name'] ?>" ><?php echo $t_value ?></textarea> 
          <?php } else { ?>
            <input type="text" style="min-width:100%;" name="<?php echo $value['name'] ?>" id="<?php echo $value['name'] ?>" class="" value="<?php echo $t_value ?>" title="" autocomplete="off" placeholder="<?php echo $value['fieldLabel'] ?>">
          <?php } ?>
        <?php }  ?>
      <?php }  ?>
      <div></div>
      <button type="submit" class="" style=""><?php echo $label_submit ?></button>
    </form>
  </div>
  <?php
  return ob_get_clean();
}
add_shortcode("fix_001940_nnew", "fix_001940_nnew");




function fix_001940_edit($atts, $content = null) {
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
	ob_start();
	?>
	<style type="text/css">
		#<?php echo $md ?>_edit {
			display: grid;
			grid-template-columns: 1fr 1fr;  
		}
		#<?php echo $md ?>_edit label {
			text-align: right;
			padding: 5px;
      text-transform: uppercase;
		}
		@media (max-width: 600px) {
			#<?php echo $md ?>_edit {
				grid-template-columns: 1fr;   
			}
			#<?php echo $md ?>_edit label {
				text-align: left;
				padding: 0px;
			}
		}
	</style>
	<form id="<?php echo $md ?>_edit" class="fix_001940_edit" action="<?php echo $target_update ?>" method="POST">
		<?php foreach ($edit['campo'] as $key => $value) { ?>
	        <?php $t_campo = $value['name']; ?>
    	    <?php $t_value = isset($_REQUEST[$t_campo]) ? sanitize_text_field($_REQUEST[$t_campo]) : ''; ?>
        	<?php $is_show = !preg_match("/".$t_campo." /", $un_show) ?>
        	<?php if($is_show) { ?>
				<label for="<?php echo $value['name'] ?>" ><?php echo $value['fieldLabel'] ?></label>
				<?php if($value['xtype']=='textarea'){ ?>
					<textarea id="<?php echo $value['name'] ?>" name="<?php echo $value['name'] ?>" ><?php echo $value['value'] ?></textarea>
				<?php } else { ?>
					<input type="text" name="<?php echo $value['name'] ?>" id="<?php echo $value['name'] ?>" value="<?php echo $value['value'] ?>" title="" autocomplete="off">
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<div></div>
		<button type="submit" class="">Atualizar</button>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode("fix_001940_edit", "fix_001940_edit");


function fix_001940_list($atts, $content = null) {
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
    "no_title" => '',
    "no_thead" => '',
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


  if(!count($col)) return '';

  $modulo_conf    = fix_001940_get_modulo_conf($md, $cnn);
  $tabela         = '';//= $modulo_conf['tabela'];//xxxxxxxxxxxxrevisao
  $campo_codigo   = $tabela."_codigo";
  $fields         = fix_001940_get_fields($md, $cnn,$df);

  $df['join'] = $join;
  $df['die_col'] = $die_col;

  


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
  $df['col_replace'] = $col_replace;



  $df['die_sql'] = $die_sql;
  $df['inner'] = $inner;
  // INNER JOIN tabela2 ON tabela1.coluna=tabela2.coluna
  $criterio = preg_replace("/__cod__/", fix_001940_get_cod() , $criterio);
  $criterio = preg_replace("/__pai__/", fix_001940_get_pai() , $criterio);
  $criterio = preg_replace("/__prefix__/", fix_001940_prefix(false) , $criterio);
  $criterio = preg_replace("/__pessoa_by_user__/", get_user_meta( get_current_user_id(), "pessoa_by_user", true ) , $criterio);
 
  $df['criterio'] = base64_encode($criterio);

  //---------------------------------
  $start      = isset($_GET['start']) ? sanitize_text_field($_GET['start']) : 0;
  //$limit      = isset($_GET['limit']) ? sanitize_text_field($_GET['limit']) : $modulo_conf['limit'];
  $limit      = isset($_GET['limit']) ? sanitize_text_field($_GET['limit']) : 10;
 
  $pag = isset($_GET['pag']) ? $_GET['pag'] : 1;
  $start = ($pag * $limit) - $limit;
  $df['start'] = $start;

  //---------------------------------
  $data = fix_001940_get_md_rows($md, $fields, $col, $df, $cnn);
  $total = $data['total'];//149;//$data['total']
  $supertotal = 0;
  $total2 = $total - $limit;


  
  if(isset($data['msg'])){
    if($data['msg']) return $data['msg'];
  }
  $_SESSION['md'.$md.'_total'] = $data['total'];
  $manut = $modulo_conf['show_cp_option'];
  if( $on_op) $manut = false;
  //paginacai -ini
  $ret = "";
  $url = $_SERVER["REDIRECT_URL"].'?';

  


  /*
  //-----
  $limit    = isset($_GET['limit']) ? $_GET['limit'] : 10;
  $start    = isset($_GET['start']) ? $_GET['start'] : '0';
  $pag      = isset($_GET['pag']) ? $_GET['pag'] : 1;
  $paginas  = 0;

  //-----
  */

  //return $start;
    global $wpdb;
    $sql = "select * from ".$wpdb->prefix."fix001940 where fix001940_tabela = '".$md."' order by fix001940_tabela";
    $tb = fix_001940_db_exe($sql,'rows');
    $fix001940_tabela = $tb['rows'][0]['fix001940_tabela'];
    $fix001940_descri = $tb['rows'][0]['fix001940_descri'];
    $modulo_descri = $fix001940_tabela;
    if($fix001940_descri) $modulo_descri = $fix001940_descri;
    //echo '<pre>';
    //print_r($tb);
    //echo '</pre>';

  	ob_start();
  	// echo '<pre>';
   //  print_r($col);
   //  echo '</pre>';
    ?>
    <style type="text/css">
          #<?php echo $md ?>_list_th_mask {
            position: fixed;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 9990;
          }
          #<?php echo $md ?>_list_th_dv {
            position: absolute;
            left: 0px;
            margin-left: 0px;
            top: 0px;
            background-color: white;
            width: 600px;
            min-height: 30px;
            border: 1px solid gray;
            z-index: 9991;

            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            border-radius: 10px;
            padding: 5px 10px;

            -moz-box-shadow: 5px 5px 10px gra;
            -webkit-box-shadow: 5px 5px 10px black;
            box-shadow: 5px 5px 10px black;

          }

    </style>
    <script type="text/javascript">
      jQuery(function($){
        $('.<?php echo $md ?>_list_xxx th').on("click",function(e){
          var campo = $(this).attr('data-campo');
          var tipo = $(this).attr('data-tipo');
          var md = $(this).attr('data-md');
          console.log('th: '+campo);
          console.log('th: '+tipo);
          console.log('th: '+md);

              $('body').append('<div id="<?php echo $md ?>_list_th_mask"></div>');
              $('body').append('<div id="<?php echo $md ?>_list_th_dv">abrindo...</div>');
              $('#<?php echo $md ?>_list_th_dv').load('<?php echo site_url() ?>/fix_001940_list_mnu_th/?campo='+campo+'&tipo='+tipo+'&md='+md);
              $('#<?php echo $md ?>_list_th_mask').on('click',function(e){
                $('#<?php echo $md ?>_list_th_mask').remove();
                $('#<?php echo $md ?>_list_th_dv').remove();
              });

              $('#<?php echo $md ?>_list_th_dv').css('left',mousex+'px');
              $('#<?php echo $md ?>_list_th_dv').css('top',mousey+'px');
























        });
      });
    </script>
  	<div class="<?php echo $md ?>_list" data-total="<?php echo $total ?>" data-pag="<?php echo $pag ?>" data-busca="<?php echo $busca ?>" style="overflow-y:auto;">
  		<table style="min-width: 100%;"id="">


        <?php if(!$no_thead) {?>
  			<thead>
  				<?php if($col_xt) {?>
  					<th style="padding: 4px;margin: 4px;line-height: 1;border: 1px solid black;background-color: #d8d8d8;"><div class="<?php echo $md ?>_mnut"><?php echo $col_xt ?></div></th>
  				<?php } ?>
  				<?php foreach ($col as $key => $value2) { ?>
  					<?php if($value2['ctr_list'] == 'label'){ ?>
  						<?php $is_show = !preg_match("/".$value2['dataIndex']." /i", $un_show) ?>
  						<?php if($is_show) { ?>
                <?php $value2['text'] = preg_replace("/_/", " ",$value2['text']) ?>
                <?php $value2['text'] = strtoupper($value2['text']) ?>
							<th class="th" data-campo="<?php echo $value2['dataIndex'] ?>" data-tipo="<?php echo $value2['tipo'] ?>" data-md="<?php echo $md ?>" style="text-align:left;white-space: nowrap;font-size: 10px;padding: 4px;margin: 4px;line-height: 1;border: 1px solid black;background-color: #d8d8d8; "><?php echo $value2['text'] ?></th>
						<?php } ?>
					<?php } ?>
  				<?php } ?>
  			</thead>
        <?php } ?>


  			<tbody>
          <?php $i = 0; ?>


          <?php //echo "kkkk". count($data['data_sql'][0])."kkkk"; ?>

  				<?php foreach ($data['row'] as $key => $value) { ?>
            <?php $key0 = $key ?>
            <?php $i++; ?>
	  				<tr style="line-height: 0;" class="<?php echo $md ?>_tr" data-codigo="<?php echo $value[$md.'_codigo'] ?>">
	  					<?php if($col_x0) { ?>
	  						<td data-cod="<?php echo $value[$md.'_codigo'] ?>" class="<?php echo $md ?>_col_x0 <?php echo $md ?>_mnum" style="white-space: nowrap;border: 1px solid black;background-color: #d8d8d8;"><?php echo $i ?></td>
	  					<?php } ?>		
  						<?php foreach ($col as $key => $value2) { ?>
  							<?php $campo = $value2['dataIndex'] ?>
  							<?php if($value2['ctr_list'] == 'label'){ ?>
  								<?php $is_show = !preg_match("/".$value2['dataIndex']." /i", $un_show) ?>
  								<?php if($is_show) { ?>


                    <?php 
                      if($col_url) {
                        $col_url_e = explode(",", $col_url);
                        if($campo==$col_url_e[0]){
                          $col_url_e[1] = preg_replace("/__this__/i", $value[$campo], $col_url_e[1]);

                          foreach ($col as $key => $value3){
                            $t_campo2 = $value3['dataIndex'];
                            $t_value_2 = $value[$t_campo2];
                            $col_url_e[1] = preg_replace("/__".$value3['dataIndex']."__/i", $t_value_2, $col_url_e[1]);
                            // $col_url_e[1] = preg_replace("/__fix158381_cadastro__/i", $data['data_sql'][$key0]['fix158381_cadastro'] , $col_url_e[1]);
                            //https://prosocio.appsaas.com.br/vbfjohqw/fix438026/detalhes/?cod=__fix158381_cadastro__
                            //$col_url_e[1] = preg_replace("/__fix811804_codigo__/i", "xx-xx", $col_url_e[1]);
                          }
                          $value[$campo] = $col_url_e[1];




                        }
                      }
                      //if ($col_url) {
                      //  $t566_codigo_name = isset($col[0]['codigo_name']) ? $col[0]['codigo_name'] : '';
                      //  if($t566_codigo_name){
                      //    $t566_v_codigo_name = $data['row'][$i][$t566_codigo_name];
                      //    $ok = 0;

                      //    if($col_url){
                      //      $col_url = preg_replace("/__tcod__/i", $t566_v_codigo_name, $col_url);
                      //      $col_url = preg_replace("/__pai__/i", fix_001940_get_pai(), $col_url);
                      //      $col_url = preg_replace("/__cod__/i", fix_001940_get_cod(), $col_url);
                      //      $col_url_arr = explode(",", $col_url);
                      //      foreach ($col_url_arr as $ckey => $cvalue) {
                      //        $is_role_true = true;
                      //        // echo "<div>".$cvalue."</div>";
                      //        $col_url_arr_item = explode("|", $cvalue);
                      //        $is_role_in = isset( $col_url_arr_item[2] ) ? $col_url_arr_item[2] : '';
                      //        if($is_role_in){
                      //          $is_role_true = fix_001940_is_role($is_role_in);
                      //        } else {
                      //          $is_role_true = 1;
                      //        }
                      //        if($is_role_true) {
                      //          foreach ($col as $key => $value) {
                      //            if ($value['dataIndex']==$col_url_arr_item[0]) {
                      //              $tcampo = $value['dataIndex'];
                      //              $tvalue = $col_url_arr_item[1];
                      //              $tvalue = preg_replace("/__this__/i", $data['row'][$i][$tcampo], $tvalue);
                      //              foreach ($col as $ttkey => $ttvalue) {
                      //                $tttcampo = $ttvalue['dataIndex'];
                      //                $tttvalue = $data['row'][$i][$tttcampo];
                      //                if (preg_match("/__".$tttcampo."__/", $tvalue)) {
                      //                  $tvalue = preg_replace("/__".$tttcampo."__/", $data['row'][$i][$tttcampo],$tvalue);
                      //                }
                      //              }
                      //              $trole = isset($col_url_arr_item[2]) ? $col_url_arr_item[2] : "";
                      //              $data['row'][$i][$tcampo] = $tvalue; 
                      //            }
                      //          }
                      //        }
                      //      }
                      //    }
                      //  }
                      //}
                     ?>
                     <?php //$value[$campo] = strtoupper($value[$campo]) ?>
  									<td class="<?php echo $campo ?>" data-tipo="<?php echo $value2['tipo'] ?>" style="white-space: nowrap;line-height: 0;border: 1px solid black;padding: 10px 4px;margin: 0px;"><?php echo $value[$campo] ?></td>
  								<?php } ?>
  							<?php } ?>
  						<?php } ?>
  					</tr>
  				<?php } ?>
  			</tbody>	
  		</table>
  	</div>
  	<?php





    return ob_get_clean();



  $limit    = isset($_GET['limit']) ? $_GET['limit'] : 10;
  $start    = isset($_GET['start']) ? $_GET['start'] : '0';
  $pag      = isset($_GET['pag']) ? $_GET['pag'] : 1;
  $paginas  = 0;

  if($total) {
    $paginas = (int) ($total / $limit);
    $paginas++; // tem ciencia aqui :( ;
    // echo '<div>paginas: '.$paginas.'</div>';
  }


  if($pag) {
    $start = (int) (($pag * $limit) - $limit);
  }

  $page_primeira    = 1;
  $page_anterior2   = $pag - 3;
  $page_anterior1   = $pag - 2;
  $page_anterior    = $pag - 1;
  $page_seguinte    = $pag + 1;
  $page_seguinte1   = $pag + 2;
  $page_seguinte2   = $pag + 3;
  $page_ultima    = $paginas;

  $url_page_anterior = '';
  $url_page_primeira = '';
  $url_page_anterior = '';
  $url_page_anterior2 = '';
  $url_page_anterior1 = '';
  $url_page_seguinte = '';
  $url_page_seguinte1 = '';
  $url_page_seguinte2 = '';


  ?>
  <style type="text/css">
  .fix002011_pag {
    text-align: center;
    background-color:#ededed;
    border-radius: 8px;
    color: gray;
    margin-top: 20px;
    margin-bottom: 20px;
  }
  .fix002011_pag ul {
    list-style: none;
    margin: 0;
    padding-left: 0;
    position: relative;
  }
  .fix002011_pag li {
    display: flex;
    justify-content: center;
    flex-direction: column;
    display: inline;
  }
  
    
    
    


  .fix002011_pag a {
    color: gray;
  }

  .fix002011_pag_box {
    /*display: grid;*/
    /*grid-template-columns:  1fr 1fr 5fr 1fr 1fr;*/

  }
  @media (max-width: 600px) {

    .fix002011_pag a {
      min-width: 20px !important;
    }
    .fix002011_pag_box .page-numbers a {
      font-size: 60% !important;
    }
    .page-numbers.intervalo {
      display: none;
    }
  }
  .page-numbers.current {
    font-size: 30px !important;
  }
  </style>
  <?php

  $query_string = $_GET;

  //url_page_primeira
  $query_string_t = $query_string;
  $query_string_t['pag'] = '1';
  $t_param = http_build_query($query_string_t);
  $url_page_primeira  = '?'.$t_param;
  // url_page_primeira - end

  // url_page_anterior - end
  $query_string_t = $query_string;
  $query_string_t['pag'] = $page_anterior;
  $t_param = http_build_query($query_string_t);
  $url_page_anterior  = '?'.$t_param;
  // url_page_anterior - end

  // url_page_ultima - ini
  $query_string_t = $query_string;
  $query_string_t['pag'] = $page_ultima;
  $t_param = http_build_query($query_string_t);
  $url_page_ultima  = '?'.$t_param;
  // url_page_ultima - end

  // page_anterior1 - ini
  $page_anterior1 = $pag - 2;
  if(!$page_anterior1) $page_anterior1 = 1;
  $query_string_t = $query_string;
  $query_string_t['pag'] = $page_anterior1;
  $t_param = http_build_query($query_string_t);
  $url_page_anterior1   = '?'.$t_param;
  // page_anterior1 - end

  // page_anterior2 - ini
  $page_anterior2 = $pag - 3;
  if(!$page_anterior2) $page_anterior = 1;
  $query_string_t = $query_string;
  $query_string_t['pag'] = $page_anterior2;
  $t_param = http_build_query($query_string_t);
  $url_page_anterior2   = '?'.$t_param;
  // page_anterior2 - end

  // url_page_seguinte - ini
  $query_string_t = $query_string;
  $query_string_t['pag'] = $page_seguinte;
  $t_param = http_build_query($query_string_t);
  $url_page_seguinte  = '?'.$t_param;
  // url_page_seguinte - end

  // url_page_seguinte1 - end
  $page_seguinte1   = $pag + 2;
  if($page_seguinte1 > $paginas) $page_seguinte1 = $paginas;
  $query_string_t = $query_string;
  $query_string_t['pag'] = $page_seguinte1;
  $t_param = http_build_query($query_string_t);
  $url_page_seguinte1   = '?'.$t_param;
  // url_page_seguinte1 - end


  // page_seguinte2 - ini
  $page_seguinte2   = $pag + 3;
  if($page_seguinte2 > $paginas) $page_seguinte2 = $paginas;
  $query_string_t = $query_string;
  $query_string_t['pag'] = $page_seguinte2;
  $t_param = http_build_query($query_string_t);
  $url_page_seguinte2   = '?'.$t_param;
  // page_seguinte2 - end


  echo '<pre style="display:none;">';
  echo 'page_primeira: '.$page_primeira."\n";
  echo 'page_anterior2: '.$page_anterior2."\n";
  echo 'page_anterior1: '.$page_anterior1."\n";
  echo 'page_anterior: '.$page_anterior."\n";
  echo 'pag: '.$pag."\n";
  echo 'page_seguinte: '.$page_seguinte."\n";
  echo 'page_seguinte1: '.$page_seguinte1."\n";
  echo 'page_seguinte2: '.$page_seguinte2."\n";
  echo 'page_ultima: '.$page_ultima."\n";
  echo '</pre>';

  ?>
  <div>Total de registros encontrados: <?php echo $total ?>. Filtro usado: <?php echo $busca ?>.</div>
  <?php 

  if($total > $limit){
    ?>

    <div class="fix002011_pag oceanwp-pagination clr">
      <ul class="fix002011_pag_box page-numbers" style="text-align: center;">
        <li <?php if($page_primeira == $pag ) echo "style=display:none;"?> ><a class="prev page-numbers <?php echo $md ?>_page_prev " href="<?php echo $url_page_anterior ?>" data-page_go=<?php echo $page_anterior ?> >ANTERIOR<i class="fa fa-angle-left"></i></a></li>
        <li <?php if($page_primeira == $pag ) echo "style=display:none;"?> ><a class="page-numbers " href="<?php echo $url_page_primeira ?>" data-page_go=<?php echo $page_ultima ?> ><?php echo ($page_primeira) ?></a></li>
        <li <?php if($page_primeira == $pag ) echo "style=display:none;"?> ><span class="page-numbers dots">…</span></li>
        <li <?php if($page_anterior2 <= 1 ) echo "style=display:none;"?> ><a class="page-numbers intervalo" href="<?php echo $url_page_anterior2 ?>" data-page_go=<?php echo $page_anterior2 ?>><?php echo ($page_anterior2) ?></a></li>
        <li <?php if($page_anterior1 <= 1 ) echo "style=display:none;"?> ><a class="page-numbers intervalo" href="<?php echo $url_page_anterior1 ?>" data-page_go=<?php echo $page_anterior1 ?> ><?php echo ($page_anterior1) ?></a></li>
        <li <?php if($page_anterior <= 1 ) echo "style=display:none;"?> ><a class="page-numbers intervalo" href="<?php echo $url_page_anterior ?>" data-page_go=<?php echo $page_anterior ?>><?php echo $page_anterior ?></a></li>
        <li><span class="page-numbers current"><?php echo $pag ?></span></li>
        <li <?php if($page_seguinte >= $paginas ) echo "style=display:none;"?> ><a class="page-numbers intervalo" href="<?php echo $url_page_seguinte ?>" data-page_go=<?php echo $page_seguinte ?>><?php echo $page_seguinte ?></a></li>
        <li <?php if($page_seguinte1 >= $paginas ) echo "style=display:none;"?> ><a class="page-numbers intervalo" href="<?php echo $url_page_seguinte1 ?>" data-page_go=<?php echo $page_seguinte1 ?>><?php echo $page_seguinte1 ?></a></li>
        <li <?php if($page_seguinte2 >= $paginas ) echo "style=display:none;"?> ><a class="page-numbers intervalo" href="<?php echo $url_page_seguinte2 ?>" data-page_go=<?php echo $page_seguinte2 ?> ><?php echo $page_seguinte2 ?></a></li>
        <li <?php if($page_ultima == $pag ) echo "style=display:none;"?> ><span class="page-numbers dots">…</span></li>
        <li <?php if($page_ultima == $pag ) echo "style=display:none;"?> ><a class="page-numbers " href="<?php echo $url_page_ultima ?>" data-page_go=<?php echo $page_ultima ?> ><?php echo $page_ultima ?></a></li>
        <li <?php if($page_seguinte >= $paginas ) echo "style=display:none;"?> ><a class="next page-numbers <?php echo $md ?>_page_next " data-page_go=<?php echo $page_seguinte ?> href="<?php echo $url_page_seguinte ?>" >PROXIMA<i class="fa fa-angle-right"></i></a></li>
      </ul>
    </div>
    <script type="text/javascript">
      jQuery(function($){
        $('.page-numbers').on('click',function(e){
          e.preventDefault();
          var page_go = $(this).attr('data-page_go');
          console.log('page_go: '+page_go);
        })
      });
      //next page-numbers fix811804_page_next 
    </script>
    <?php
  }

	return ob_get_clean();






}
add_shortcode("fix_001940_list", "fix_001940_list");

add_shortcode("fix_001940_paged", "fix_001940_paged");
function fix_001940_paged($atts, $content = null) {
  extract(shortcode_atts(array(
    "div" => ''
  ), $atts));

}



//--request
add_action( 'parse_request', 'fix001940_parse_request');
function fix001940_parse_request( &$wp ) {
  if($wp->request == 'fix_001940_list_mnu_th'){
    
    $value = isset($_GET['value']) ? $_GET['value'] : '';
    $campo = isset($_GET['campo']) ? $_GET['campo'] : '';
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
    $md = isset($_GET['md']) ? $_GET['md'] : '';

    $order_by_asc = $md."_order_by=".$campo."&".$md."_sorter_by=ASC";
    $order_by_desc = $md."_order_by=".$campo."&".$md."_sorter_by=DESC";
    ?>
      <div style="display: grid;grid-template-columns: 1fr 1fr;">
        <div>
        
          <div><a href="?<?php echo $order_by_asc ?>">ASCENDENTE</a></div>
          <div><a href="?<?php echo $order_by_desc ?>">DESCENTENTE</a></div>
          <div><a id="fix001940_modulo_edit" data-md="<?php echo $md ?>" href="#" >MODULO CONFIG</a></div>
          <div><a id="fix001940_campos_edit" data-md="<?php echo $md ?>" href="#" >CAMPOS CONFIG</a></div>
          <div>
            <script type="text/javascript">
              jQuery(function($){
                $('.<?php echo $md ?>_form_filter').on('submit',function(e){
                  e.preventDefault();
                  // console.log('_form_filter');

                  var busca = '?busca='+$('.<?php echo $md ?>_filter_cp').val()+':'+$('.<?php echo $md ?>_filter_value').val();
                  // console.log(busca);
                  window.location.href = busca;
                });
                $('#fix001940_modulo_edit').on("click",function(e){
                  e.preventDefault();
                  
                  $('#<?php echo $modulo ?>_mnum_mask').remove();
                  $('#<?php echo $modulo ?>_mnum_dv').remove();

                  $('body').append('<div id="fix001940_modulo_edit_mask"></div>');
                  $('body').append('<div id="fix001940_modulo_edit_dv">abrindo...</div>');
                  $('#fix001940_modulo_edit_dv').load('<?php echo site_url() ?>/fix001940_modulo_edit_/?campo='+campo+'&tipo='+tipo+'&md='+md);
                  $('#fix001940_modulo_edit_mask').on('click',function(e){
                    $('#fix001940_modulo_edit_mask').remove();
                    $('#fix001940_modulo_edit_dv').remove();
                  });

                  $('#fix001940_modulo_edit_dv').css('left',mousex+'px');
                  $('fix001940_modulo_edit_dv').css('top',mousey+'px');




                });
                $('#fix001940_campos_edit').on("click",function(e){
                  e.preventDefault();
                });

              });
            </script>


            <style type="text/css">
                  #fix001940_modulo_edit_mask {
                    position: fixed;
                    top: 0px;
                    left: 0px;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                    z-index: 9990;
                  }
                  #fix001940_modulo_edit_dv {
                    position: absolute;
                    left: 0px;
                    margin-left: 0px;
                    top: 0px;
                    background-color: white;
                    width: 600px;
                    min-height: 30px;
                    border: 1px solid gray;
                    z-index: 9991;

                    -moz-border-radius: 10px;
                    -webkit-border-radius: 10px;
                    border-radius: 10px;
                    padding: 5px 10px;

                    -moz-box-shadow: 5px 5px 10px gra;
                    -webkit-box-shadow: 5px 5px 10px black;
                    box-shadow: 5px 5px 10px black;

                  }

            </style>

            <form class="<?php echo $md ?>_form_filter" action="" method="GET">
              <input type="text" name="<?php echo $md ?>_filter_value" class="<?php echo $md ?>_filter_value" style="border:1px solid gray;">
              <input type="hidden" name="<?php echo $md ?>_filter_cp" value="<?php echo $campo ?>" class="<?php echo $md ?>_filter_cp" >
            </form>
          </div>
        </div>  
        <div>
          <?php 
          global $wpdb;
          $sql = "select fix001941_campo, fix001941_label from ".$wpdb->prefix."fix001941 where fix001941_tabela='".$md."'";
          $tb = fix_001940_db_exe($sql,'rows');
          foreach ($tb['rows'] as $row) {
            ?>
            <div><input type="checkbox" nome="show_field" value="<?php echo $row['fix001941_campo'] ?>"> <?php echo $row['fix001941_label'] ?></div>
            <?php
          }
          ?>
        </div>
      </div>
    <?php
    /*
fix438027_nivel
sql_order
sql_dir
    */
    exit;
  }
}