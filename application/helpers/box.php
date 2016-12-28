<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Helpers_Box { 
    
    public $matches;
    
    public $base;
    
    //boolean que indica si muestra o no el resultado final de los partidos
    public $show_final_result;
    
    //el tamanio de las cajas
    public $tamanho_box;
    
    //indica si el atributo para mostrar es el resultado final del partido o el palpite
    //      true = palpite, false = goal
    public $palpites_goal;
    
    //si el input text esta cerrado o no
    public $disabled_input;
    
    //si el campo de mas informacion esta abierto o cerrado.
    public $mas_info;
    
    //true si muestra los box solo los que fueron palpitados
    public $show_solo_palpitados;
    
    //true si muestra los box solo los que no fueron palpitados
    public $show_solo_nopalpitados;
    
    //si muestra el numero de la rodada en el titulo, true -> si muestra.
    public $show_titulo_rodada = false;
    
    public $btn_palpitar = false;
    
    public $btn_excluir = false;
    
    public $btn_cantidad = false;
    
    public $btn_palpitar_sem_ter_jugado = false;
    
    public $acumulado = true;
    
    public $id_result_input1;
    
    public $id_result_input2;
    
    public $id_box;
    
    public $id_html_team;
    
    public $show_titulo_campeonato = false;
    
    public $show_jugados = true;
    
    public $link_campeonato = "/campeonatos/index";
    
    public $link_ronda = "/campeonatos/index";
    
    public $btn_minimize = false;
    
    public $display_boxcontent = true;
    
    public $titulo_bigbox = "";
    
    public $ganadores = false;
    
    public $infoescrita = false;
    
    public $infoescrita_msg = "";
    
    public $show_total_palpites_desse_marcador = false;
    
    public $show_data_quadrado = true;

    public function getrow($base, $tm_id, $tm_nome, $tm_logo, $mt_championship, $rs_res, $goal, $palpites_ou_result, $disabled_input, $result_input, $mt_id, $played, $ganou) {
    
        
        $result =  $goal;
        if ($palpites_ou_result) {
            $result = $rs_res;
        }
        
        $disabled = "disabled";
        if (!$disabled_input) {
            $disabled = "";
        }
        
        $r = '<table><tr>
            <td width="55%" style="text-align:left"><span id="'.$this->id_html_team.$tm_id.'">'.Helpers_Html::getTeamLinkLeft($base, $tm_id, $tm_nome, $tm_logo, $mt_championship).'</span></td>';                   
             
            if ($this->show_final_result) {
                
                if ($played) {
                    $res = "label-success";
                    if ($ganou == 0) {
                        $res = "label-danger";
                    }
                    $r = $r.'<td width="10%" style="text-align:right"><span class="label '.$res.'">'.$goal.'</span></td>';
                }
            }        
            
            $r = $r.'<td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="'.$result_input.'" style="text-align:center" class="form-control numeros_input" '.$disabled.' value="'.$result.'" type="text">
                </div>
                </div>
            </td>
            </tr>
        </table>';
        
        return $r;
        
    }
    
    public function titulo($titulo, $link){
        return '<table width="100%"><tr><td style="text-align:center"><b><a href="'.$link.'">'.$titulo.'</a></b></td></tr></table>';
    }
    
    public function big_box() {
        
                      $display_boxcontent = "";
                      $chevron = "up";
                      if (!$this->display_boxcontent) { 
                          $display_boxcontent = 'style="display:none"';
                          $chevron = "down";
                      }
                      
                      $matches = $this->matches;
        
         echo '<div class="box">
                        <div class="box-header">
                            <h2><i class="fa fa-align-justify"></i><span class="break"></span>'.$this->titulo_bigbox.'</h2>';
                                if ($this->btn_minimize) {
                                    echo '<div class="box-icon">
                                        <span><h2 style="margin-right: 30px !important" id="ronda_total_palpitado">'.number_format((float)$matches[0]['rd_acumulado'], 2, '.', '').'</h2></span>
                                       <a href="form-elements.html#" class="btn-minimize"><i class="fa fa-chevron-'.$chevron.'"></i></a>
                                    </div>';
                                }
                        echo '</div>';
                        

                        
                echo '<div class="box-content" '.$display_boxcontent.'>
                    <div class="row">';

                        echo $this->box();

                    echo '<div class="form-action">                              
                    </div>
                    </div>
                </div>
        </div>';
         
    }
    
    public function box() {
        $matches = $this->matches;
        
        $config = new Zend_Config_Ini("config.ini");
        $anterior = "";
        
        $mas_info_display = "";
        if ($this->mas_info) {
            
            $mas_info_display = "display:none";
        }
        
        for($i = 0; $i < count($matches); $i = $i + 1) {   
            $habilitado = Helpers_Data::antesDeHoras($matches[$i]['mt_date']);
            if (!$habilitado) {
                $this->infoescrita = true;
                $this->infoescrita_msg = "Encerrado";
            }   else {
                $this->infoescrita = false;
                $this->infoescrita_msg = "";
            }                                                                                         
            
            if (!$this->show_jugados && $matches[$i]['mt_played']) {
                //si no se pueden mostrar los jugados y este partido fue jugado 
                //entonces no muestra nada

            } else {
            $st = 'style=""';
            if ($this->show_solo_palpitados) {
                if (empty($matches[$i]['rs_id'])) {
                    $st = 'style="display:none"';
                }   
            }
            
            if ($this->show_solo_nopalpitados) {
                $st = 'style=""';
                if (!empty($matches[$i]['rs_id'])) {
                    $st = 'style="display:none"';
                }
            }
            
                $id = rand(0,1000);
                echo '<div '.$st.' id="'.$this->id_box.$matches[$i]['mt_id'].'" class="'.$this->tamanho_box.'">
                        <div class="smallstat box">';

                            if ($this->show_titulo_campeonato) {
                                echo $this->titulo($matches[$i]['ch_nome'], $this->base.$this->link_campeonato."?champ=".$matches[$i]['ch_id']);
                            }
                
                            if ($this->show_titulo_rodada) {
                                echo $this->titulo("Rodada ".$matches[$i]['rd_round'], $this->base.$this->link_ronda."?rodada=".$matches[$i]['mt_idround']."&champ=".$matches[$i]['ch_id']);
                            }                          
                            
                            if (!isset($matches[$i]['rs_res1'])) {
                                $matches[$i]['rs_res1'] = "";
                                $matches[$i]['rs_res2'] = "";
                                $matches[$i]['rs_result'] = "";
                                
                            }

                            if ($this->show_data_quadrado) {
                                echo Helpers_Html::_titulo(Helpers_Data::day($matches[$i]['mt_date']));
                            }
                            echo $this->getrow($this->base, $matches[$i]['tm1_id'], $matches[$i]['t1nome'], $config->host.$matches[$i]['tm1_logo'], $matches[$i]['mt_idchampionship'], $matches[$i]['rs_res1'], $matches[$i]['mt_goal1'], $this->palpites_goal, $this->disabled_input, $this->id_result_input1.$matches[$i]['mt_id'], $matches[$i]['mt_id'], $matches[$i]['mt_played'], $matches[$i]['rs_result']);
                            echo $this->getrow($this->base, $matches[$i]['tm2_id'], $matches[$i]['t2nome'], $config->host.$matches[$i]['tm2_logo'], $matches[$i]['mt_idchampionship'], $matches[$i]['rs_res2'], $matches[$i]['mt_goal2'], $this->palpites_goal, $this->disabled_input, $this->id_result_input2.$matches[$i]['mt_id'], $matches[$i]['mt_id'], $matches[$i]['mt_played'], $matches[$i]['rs_result']);                        

                            if ($this->mas_info) {    
                                echo '<a href="javascript:void(0)" id_opcoes="'.$id.'" class="more box_palpite">
                                    <span>Mais opcoes...</span>
                                    <i id="icone_'.$id.'" class="fa fa-chevron-right"></i>
                                </a>';
                            }

                            echo '<div id="'.$id.'" style="margin: 15px 0 0 0; '.$mas_info_display.'">';
                                   
                            
                            if ($this->show_total_palpites_desse_marcador) {
                                $palpitados = "palpites";
                                if ($matches[$i]['quantidade'] == 1) {
                                    $palpitados = "palpite";
                                }
                                    echo '<span class="" style="margin-right:20px">'.$matches[$i]['quantidade'].' '.$palpitados.'</span>';
                            }
                            
                            if ($this->infoescrita) {                                
                                  echo '<span class="label label-important" style="margin-right:70px">'.$this->infoescrita_msg.'</span>';                                
                            }
                            
                            if ($this->acumulado) {
                                echo '<span style="padding-right:10px" class="ac_'.$matches[$i]['mt_id'].'">'.$matches[$i]['mt_acumulado'].'</span>';
                            }         

                               
                               if (!$matches[$i]['mt_played']) {                                                                                                                                                        
                                   
                                   if ($habilitado) {
                                        if ($this->btn_palpitar) {
                                             echo '<button team1="'.$matches[$i]['tm1_id'].'" team2="'.$matches[$i]['tm2_id'].'" data="'.$matches[$i]['mt_date'].'" match="'.$matches[$i]['mt_id'].'" class="btn btn-xs btn-success palpite">
                                             <i style="padding: 6px 0 !important; font-size: 10px !important; margin-right: 0px !important; width: 15px !important"  class="fa fa-check"></i></button>';
                                         } 
                                   }
                                   
                                    if ($this->btn_excluir) {                                        
                                        if ($habilitado) {    
                                          echo '<button id="btn_excluir_'.$matches[$i]['mt_id'].'" title="Excluir palpite" data="'.$matches[$i]['mt_date'].'" match="'.$matches[$i]['mt_id'].'" result="'.$matches[$i]['rs_id'].'"  class="btn btn-xs btn-danger excluir">'
                                                  . '<i style="padding: 6px 0 !important; font-size: 10px !important; margin-right: 0px !important; width: 15px !important" class="fa fa-trash-o "></i></button>';
                                        }

                                    }
                                    if ($this->btn_cantidad) {
                                        echo  '<a title="'.$matches[$i]['cantidad'].' Palpites" href="'.$this->base."/penca/palpites?match=".$matches[$i]['mt_id']."&champ=".$matches[$i]['mt_idchampionship'].'" class="btn btn-xs btn-warning">'
                                                  . '<i style="padding: 6px 0 !important; font-size: 10px !important; margin-right: 0px !important; width: 15px !important" class="fa fa-globe"></i></i></a>';

                                    }
                                    
                                    

                               } else {
                                    
                                    if ($this->ganadores) {
                                        echo '<button match="'.$matches[$i]['mt_id'].'" class="btn btn-xs btn-warning ganadores">
                                        <i style="padding: 6px 0 !important; font-size: 10px !important; margin-right: 0px !important; width: 15px !important"  class="fa fa-trophy"></i></button>';
                                    }   
                               }
                               
                                if ($this->btn_palpitar_sem_ter_jugado) {
                                    echo  '<a title="'.$matches[$i]['cantidad'].' Palpites" href="'.$this->base."/penca/palpites?match=".$matches[$i]['mt_id']."&champ=".$matches[$i]['mt_idchampionship'].'" class="btn btn-xs btn-warning">'
                                    . '<i style="padding: 6px 0 !important; font-size: 10px !important; margin-right: 0px !important; width: 15px !important" class="fa fa-globe"></i></i></a>';
                                } 
                                
                               
                               echo '<div id="dvInfo_'.$matches[$i]['mt_id'].'" style="display:none">'                                      
                                . '</div>';
                                        
                            echo '</div>    
                        </div>
                </div>';
            }
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
    

}