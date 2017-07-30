<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author Martin Dymenstein
 */
class Helpers_Html {
    
    public function puntuacoes($puntuacoes, $posicao, $acertados, $errados, $palpitados,$user_id, $url) {
                $t = Zend_Registry::get('translate');  
        
        return '<table class="table" id="tb_palpites_feitos">
                    <thead>

                            <tr>
                                    <th width="15%"></th>
                                    <th></th>
                            </tr>
                    </thead>   
                    <tbody>
                      <tr>    
                          <td><a href='.$url.'/usuario/puntuacao?usuario='.$user_id.' >'.$t->_('puntuacao').'</a></td> 
                          <td width="15%" id="td_puntuacao">'.$puntuacoes.'</td>  
                      </tr>                                                                                                      
                      <tr>
                          <td><a href='.$url.'/usuario/acertados?usuario='.$user_id.' >'.$t->_('acertos').'</a></td> 
                          <td width="15%" id="td_acertados">'.$acertados.'</td>                                           
                      </tr>
                      <tr>
                          <td><a href='.$url.'/usuario/errados?usuario='.$user_id.' >'.$t->_('erros').'</a></td> 
                          <td width="15%" id="td_errados">'.$errados.'</td>                                           
                      </tr>
                      <tr>
                          <td><a href='.$url.'/usuario/palpitados?usuario='.$user_id.' >'.$t->_('palpitados').'</a></td> 
                          <td width="15%" id="td_errados">'.$palpitados.'</td>                                           
                      </tr>                                          

                    </tbody>
           </table> ';
    
    }
    
    public function erros($matches, $baseUrl) {
        $config = new Zend_Config_Ini("config.ini");
        
         $r = '
        
             <div class="row">
             <div class="col-md-9">
                    <div class="box">
                        <div class="box-header">
                                <h2><i class="fa fa-align-justify"></i><span class="break"></span>Erros</h2>
                                <div class="box-icon">
<!--                                        <a href="table.html#" class="btn-setting"><i class="fa fa-wrench"></i></a>
                                        <a href="table.html#" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
                                        <a href="table.html#" class="btn-close"><i class="fa fa-times"></i></a>-->
                                </div>
                        </div>
                        <div class="box-content">
                                <table class="table" id="tb_palpites_feitos">
                                          <thead>
                                                  <tr>
                                                          <th></th>
                                                          <th style="width:2%"></th>
                                                          <th></th>
                                                          <th style="width:2%"></th>                                                           
                                                          <th></th>                                                          
                                                          <th></th> 
                                                          <th></th> 
                                                  </tr>
                                          </thead>   
                                          <tbody>';
                                              
                                            
                                              $anterior = "";
                                              $r = $r.'<input cant="'.count($matches).'" type="hidden" value="'.count($matches).'" id="count_palpites_feitos" name="count_palpites_feitos">';
                                               $total = 0;
                                              for ($i = 0; $i < count($matches); $i = $i + 1) {
                                                  $total = $total + $matches[$i]['rs_points'];
                                                  if ($anterior != $matches[$i]['rs_round']) {
                                                      $anterior = $matches[$i]['rs_round'];
                                                  }  
                                                  if (!empty($matches[$i]['mt_id'])) {
                                            
                                                    $r = $r.'<tr>                                                            
                                                            <td style="text-align:right">'.Helpers_Html::getTeamLinkRight($baseUrl, $matches[$i]['tm1_id'], $matches[$i]['t1nome'], $config->host.$matches[$i]['tm1_logo'], $matches[$i]['mt_idchampionship']).'</td>
                                                            <td style="text-align:right"><span class="label label-danger">'.$matches[$i]['mt_goal1'].'</span></td>
                                                            <td class="col-sm-3 col-xs-3">
                                                                <div class="row">
                                                                    <div class="col-xs-6 col-sm-6 col-lg-6">
                                                                        <input style="text-align:center" class="form-control" disabled="true" value="'.$matches[$i]['rs_res1'].'" type="text">
                                                                    </div>                                                            
                                                                    <div class="col-xs-6 col-sm-6 col-lg-6">		
                                                                        <input style="text-align:center" class="form-control" disabled="true" value="'.$matches[$i]['rs_res2'].'" type="text">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td style="text-align:left"><span class="label label-danger">'.$matches[$i]['mt_goal2'].'</span></td>
                                                            <td style="text-align:left">'.Helpers_Html::getTeamLinkLeft($baseUrl, $matches[$i]['tm2_id'], $matches[$i]['t2nome'], $config->host.$matches[$i]['tm2_logo'], $matches[$i]['mt_idchampionship']).'</td>
                                                            <td><b>'.Helpers_Data::day($matches[$i]['mt_date']).'</b></td>
                                                            <td></td>
                                                        </tr>
                                                        ';
                                                    }
                                                }
                                              
                                              
                                              
                                                                           
                                          $r = $r.'<tr><td><span class="label label-info">Total: '.$total.' puntos</span></td></tr></tbody>
                                 </table>  
                               
                            <div class="form-action">
                                <!--<a href="javascript:void(0)" id="btnAceitarPalpites" class="btn btn-success">Aceitar Palpites</a>--> 
                            </div>
                        </div>
                </div></div></div>';
                
                return $r;
    }
    
    public function getrow($base, $tm_id, $tm_nome, $tm_logo, $mt_championship, $rs_res, $goal, $show_final_result, $palpites_ou_result, $disabled_input, $result_input) {
        $result =  $rs_res;
        if ($palpites_ou_result) {
            $result = $goal;
        }
        
        $disabled = "disabled";
        if (!$disabled_input) {
            $disabled = "";
        }
        
//        print_r("base ".$base);
        $r = '<table><tr>
            <td width="55%" style="text-align:left">'.Helpers_Html::getTeamLinkLeft($base, $tm_id, $tm_nome, $tm_logo, $mt_championship).'</td>';           
        
            if ($show_final_result) {
                $r = $r.'<td width="10%" style="text-align:right"><span class="label label-success">'.$goal.'</span></td>';
            }        
            
            $r = $r.'<td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10">                    
                    <input id="'.$result_input.'" style="text-align:center" class="form-control" '.$disabled.' value="'.$goal.'" type="text">
                </div>
                </div>
            </td>
            </tr>
        </table>';
        
        return $r;
        
    }
    
    public function titulo($titulo){
        return '<table width="100%"><tr><td style="text-align:center"><b>'.$titulo.'</b></td></tr></table>';
    }
    
   public static function _titulo($titulo){
        return '<table width="100%"><tr><td style="text-align:center"><b>'.$titulo.'</b></td></tr></table>';
    }
    
    /*
     * $matches -> array de los partidos
     * $baseUrl -> $this->baseUrl()
     * $show_final_result -> boolean que indica si muestra o no el resultado final de los partidos
     * $tamanho_box -> el tamanio de las cajas
     * $palpites_ou_goal -> indica si el atributo para mostrar es el resultado final del partido o el palpite
     *      true = palpite, false = goal
     * $disabled_input -> si el input text esta cerrado o no
     * $mas_info -> si el campo de mas informacion esta abierto o cerrado.
     * $mostras_solo_los_palpitados -> true si muestra solo los que fueron palpitados
     * $mostras_solo_los__no_palpitados -> true si muestra solo los que no fueron palpitados
     * $mostrar_rodada -> si muestra el numero de la rodada en el titulo, true -> si muestra.
     */
    public function box($matches, $baseUrl, $show_final_result, $tamanho_box, $palpites_ou_goal, $disabled_input, $mas_info, $mostras_solo_los_palpitados, $mostras_solo_los__no_palpitados, $mostrar_rodada) {
        $config = new Zend_Config_Ini("config.ini");
        $anterior = "";
        
        $mas_info_display = "display_none";
        if ($mas_info) {
            $mas_info_display = "";
        }
        
//        print_r($matches);
        
        for($i = 0; $i < count($matches); $i = $i + 1) {     
            $st = 'style=""';
            if ($mostras_solo_los_palpitados) {
                if (empty($matches[$i]['rs_id'])) {
                    $st = 'style="display:none"';
                }   
            }
            
            if ($mostras_solo_los__no_palpitados) {
                $st = 'style=""';
                if (!empty($matches[$i]['rs_id'])) {
                    $st = 'style="display:none"';
                }
            }
            
            
                $id = rand(0,1000);
                echo '<div '.$st.' id="fila_'.$matches[$i]['mt_id'].'" class="'.$tamanho_box.'">
                        <div class="smallstat box">';
                            
                            if ($mostrar_rodada) {
                                echo $this->titulo("Rodada ".$matches[$i]['mt_round']);
                            }
                            echo $this->titulo(Helpers_Data::day($matches[$i]['mt_date'])).'
                            '.$this->getrow($baseUrl, $matches[$i]['tm1_id'], $matches[$i]['t1nome'], $config->host.$matches[$i]['tm1_logo'], $matches[$i]['mt_idchampionship'], $matches[$i]['rs_res1'], $matches[$i]['mt_goal1'], $show_final_result, $palpites_ou_goal, $disabled_input, "result1p_".$matches[$i]['mt_id']).'
                            '.$this->getrow($baseUrl, $matches[$i]['tm2_id'], $matches[$i]['t2nome'], $config->host.$matches[$i]['tm2_logo'], $matches[$i]['mt_idchampionship'], $matches[$i]['rs_res2'], $matches[$i]['mt_goal2'], $show_final_result, $palpites_ou_goal, $disabled_input, "result2p_".$matches[$i]['mt_id']);                        

                            if (!$mas_info) {    
                                echo '<a href="javascript:void(0)" id_opcoes="'.$id.'" class="more box_palpite">
                                    <span>Mais opcoes...</span>
                                    <i class="fa fa-chevron-right"></i>
                                </a>';
                            }

                            echo '<div id="'.$id.'" style="margin: 15px 0 0 0; '.$mas_info_display.'">

                                <button data="'.$matches[$i]['mt_date'].'" match="'.$matches[$i]['mt_id'].'" class="btn btn-xs btn-success palpite">
                                    <i style="padding: 6px 0 !important; font-size: 10px !important; margin-right: 0px !important; width: 15px !important"  class="fa fa-check"></i></button>

                            </div>    
                        </div>
                </div>';
        }
        
    }
    
    public function acertados($matches, $baseUrl) {
        $config = new Zend_Config_Ini("config.ini");
        
         $r = '
        
             <div class="row">
             <div class="col-md-9">
                    <div class="box">
                        <div class="box-header">
                                <h2><i class="fa fa-align-justify"></i><span class="break"></span>Acertados</h2>
                                <div class="box-icon">
<!--                                        <a href="table.html#" class="btn-setting"><i class="fa fa-wrench"></i></a>
                                        <a href="table.html#" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
                                        <a href="table.html#" class="btn-close"><i class="fa fa-times"></i></a>-->
                                </div>
                        </div>
                        <div class="box-content">
                                <table class="table" id="tb_palpites_feitos">
                                          <thead>
                                                  <tr>
                                                          <th></th>
                                                          <th style="width:2%"></th>
                                                          <th></th>
                                                          <th style="width:2%"></th>                                                           
                                                          <th></th>                                                          
                                                          <th></th> 
                                                          <th></th> 
                                                  </tr>
                                          </thead>   
                                          <tbody>';
                                              
                                            
                                              $anterior = "";
                                              $r = $r.'<input cant="'.count($matches).'" type="hidden" value="'.count($matches).'" id="count_palpites_feitos" name="count_palpites_feitos">';
                                               $total = 0;
                                              for ($i = 0; $i < count($matches); $i = $i + 1) {
                                                  $total = $total + $matches[$i]['rs_points'];
                                                  if ($anterior != $matches[$i]['rs_round']) {
                                                      $anterior = $matches[$i]['rs_round'];
                                                  }  
                                                  if (!empty($matches[$i]['mt_id'])) {
                                                    $st = "";
                                                    if ($matches[$i]['rs_id'] == -1) {
                                                        $st='style="display:none"';
                                                    }
                                                    $r = $r.'<tr>                                                            
                                                            <td style="text-align:right">'.Helpers_Html::getTeamLinkRight($baseUrl, $matches[$i]['tm1_id'], $matches[$i]['t1nome'], $config->host.$matches[$i]['tm1_logo'], $matches[$i]['mt_idchampionship']).'</td>
                                                            <td style="text-align:right"><span class="label label-success">'.$matches[$i]['mt_goal1'].'</span></td>
                                                            <td class="col-sm-3 col-xs-3">
                                                                <div class="row">
                                                                    <div class="col-xs-6 col-sm-6 col-lg-6">
                                                                        <input style="text-align:center" class="form-control" disabled="true" value="'.$matches[$i]['rs_res1'].'" type="text">
                                                                    </div>                                                            
                                                                    <div class="col-xs-6 col-sm-6 col-lg-6">		
                                                                        <input style="text-align:center" class="form-control" disabled="true" value="'.$matches[$i]['rs_res2'].'" type="text">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td style="text-align:left"><span class="label label-success">'.$matches[$i]['mt_goal2'].'</span></td>
                                                            <td style="text-align:left">'.Helpers_Html::getTeamLinkLeft($baseUrl, $matches[$i]['tm2_id'], $matches[$i]['t2nome'], $config->host.$matches[$i]['tm2_logo'], $matches[$i]['mt_idchampionship']).'</td>
                                                            <td><b>'.Helpers_Data::day($matches[$i]['mt_date']).'</b></td>
                                                            <td><span class="label label-success">'.$matches[$i]['rs_points'].'</span></td>
                                                        </tr>
                                                        ';
                                                    }
                                                }
                                              
                                              
                                              
                                                                           
                                          $r = $r.'<tr><td><span class="label label-info">Total: '.$total.' puntos</span></td></tr></tbody>
                                 </table>  
                               
                            <div class="form-action">
                                <!--<a href="javascript:void(0)" id="btnAceitarPalpites" class="btn btn-success">Aceitar Palpites</a>--> 
                            </div>
                        </div>
                </div></div></div>';
                
                return $r;
    }
    
    public static function getTeamLinkRight($baseUrl, $id_team, $nome_team, $logo_team, $champ) {
        return '<a href="'.$baseUrl."/team/team?team=".$id_team."&champ=".$champ.'">'.$nome_team.'</a><img width="25px;" height="21px;" src="'.$logo_team.'" />';
    }
    
    public static function getTeamLinkLeft($baseUrl, $id_team, $nome_team, $logo_team, $champ) {
//        print_r($baseUrl."-".$id_team."-".$nome_team."-".$logo_team."-".$champ);
        return '<img width="25px;" height="21px;" src="'.$logo_team.'" /><a href="'.$baseUrl."/team/team?team=".$id_team."&champ=".$champ.'">'.$nome_team.'</a>';
    }
    
    
    
    public function tabelas($matches, $baseUrl) {
        
//        print_r($matches);
        
        $config = new Zend_Config_Ini("config.ini");
        
         $r = '
        
             <div class="row">
             <div class="col-md-9">
                    <div class="box">
                        <div class="box-header">
                                <h2><i class="fa fa-align-justify"></i><span class="break"></span>Palpitados</h2>
                                <div class="box-icon">
<!--                                        <a href="table.html#" class="btn-setting"><i class="fa fa-wrench"></i></a>
                                        <a href="table.html#" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
                                        <a href="table.html#" class="btn-close"><i class="fa fa-times"></i></a>-->
                                </div>
                        </div>
                        <div class="box-content">
                                <table class="table" id="tb_palpites_feitos">
                                          <thead>
                                                  <tr>
                                                          <th></th>
                                                          <th style="width:2%"></th>
                                                          <th></th>
                                                          <th style="width:2%"></th>                                                           
                                                          <th></th>                                                          
                                                          <th></th> 
                                                          <th></th> 
                                                  </tr>
                                          </thead>   
                                          <tbody>';
                                              
                                            
                                              $anterior = "";
                                              $r = $r.'<input cant="'.count($matches).'" type="hidden" value="'.count($matches).'" id="count_palpites_feitos" name="count_palpites_feitos">';
                                               $total = 0;
                                              for ($i = 0; $i < count($matches); $i = $i + 1) {
                                                  $total = $total + $matches[$i]['rs_points'];
                                                  if ($anterior != $matches[$i]['rs_round']) {
                                                      $anterior = $matches[$i]['rs_round'];
                                                  }  
                                                  
                                                  
                                                  $label = "danger";
                                                  if ($matches[$i]['rs_result'] == 1) {
                                                      $label = "success";
                                                  }
                                                  
                                                  
                                                  
                                                  
                                                  if (!empty($matches[$i]['mt_id'])) {
                                            
                                                    $r = $r.'<tr>                                                            
                                                            <td style="text-align:right">'.Helpers_Html::getTeamLinkRight($baseUrl, $matches[$i]['tm1_id'], $matches[$i]['t1nome'], $config->host.$matches[$i]['tm1_logo'], $matches[$i]['mt_idchampionship']).'</td>
                                                            <td style="text-align:right"><span class="label label-'.$label.'">'.$matches[$i]['mt_goal1'].'</span></td>
                                                            <td class="col-sm-3 col-xs-3">
                                                                <div class="row">
                                                                    <div class="col-xs-6 col-sm-6 col-lg-6">
                                                                        <input style="text-align:center" class="form-control" disabled="true" value="'.$matches[$i]['rs_res1'].'" type="text">
                                                                    </div>                                                            
                                                                    <div class="col-xs-6 col-sm-6 col-lg-6">		
                                                                        <input style="text-align:center" class="form-control" disabled="true" value="'.$matches[$i]['rs_res2'].'" type="text">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td style="text-align:left"><span class="label label-'.$label.'">'.$matches[$i]['mt_goal2'].'</span></td>
                                                            <td style="text-align:left">'.Helpers_Html::getTeamLinkLeft($baseUrl, $matches[$i]['tm2_id'], $matches[$i]['t2nome'], $config->host.$matches[$i]['tm2_logo'], $matches[$i]['mt_idchampionship']).'</td>
                                                            <td><b>'.Helpers_Data::day($matches[$i]['mt_date']).'</b></td>
                                                            <td></td>
                                                        </tr>
                                                        ';
                                                    }
                                                }
                                              
                                              
                                              
                                                                           
                                          $r = $r.'<tr><td><span class="label label-info">Total: '.$total.' puntos</span></td></tr></tbody>
                                 </table>  
                               
                            <div class="form-action">
                                <!--<a href="javascript:void(0)" id="btnAceitarPalpites" class="btn btn-success">Aceitar Palpites</a>--> 
                            </div>
                        </div>
                </div></div></div>';
                
                return $r;
    }
    
    
}
