<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of posicoes
 *
 * @author Martin Dymenstein
 */
class Helpers_Posicoes {
    
    public $teams;
    
    public $champion;
    
    public $champ;
    
    public $base;
    
    public function posicoes() {
        $config = new Zend_Config_Ini('config.ini');
        
        $teams = $this->teams;
        $champ = $this->champ;
        $champion = $this->champion;
        
        $temGrupos = isset($teams[0]['tm_grupo']) ? true : false;
       
        echo '<script type="text/javascript"> 
                 $(function() {
                    $("#vchamp").bind("click", function() {
                        $("#champ_prec_box").show();           
                    });
                    
                    $("#fecharvchamp").bind("click", function() {
                        $("#champ_prec_box").hide();
                    });
                 });   

         </script>';
        
        echo '<div class="box">
            <div class="box-header">
                    <h2><a id="vchamp" href="javascript:void(0)"><i class="fa fa-question-circle"></i></a><span class="break"></span>Tabela de Posicoes</h2>

                    <div class="box-icon">
                        <span><h2 style="margin-right: 30px !important" id="campeonato_total_palpitado">'.number_format((float)$champion['ch_acumulado'], 2, '.', '').'</h2></span>
                        </div>
            </div>
            <div class="box-content">
                <div id="champ_prec_box" style="display:none" class="col-lg-12 col-sm-12 col-xs-12 col-xxs-12 col-xxs-12">
                    <div class="smallstat box">
                        <div class="box-header">
                            <h2><a id="fecharvchamp" href="javascript:void(0)"><i class="fa fa-times"></i></a><span class="break"></span>Info dos palpites</h2>
                        </div>                    
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td width="55%" style="text-align:left">Custo do palpite</td>
                                    <td width="35%">R$ '.$champion['ch_dpalpite'].'</td>
                                </tr>
                                <tr>
                                    <td width="55%" style="text-align:left">para o Campeonato</td>
                                    <td width="35%">R$ '.$champion['ch_dchamp'].'</td>
                                </tr>                                                        
                                <tr>
                                    <td width="55%" style="text-align:left">para a Rodada</td>
                                    <td width="35%">R$ '.$champion['ch_drodada'].'</td>
                                </tr>                    
                                <tr>
                                    <td width="55%" style="text-align:left">para o Jogo</td>
                                    <td width="35%">R$ '.$champion['ch_djogo'].'</td>
                                </tr>                                                        
                            </tbody>
                        </table>    
                    </div>
                </div>            


                    <table class="table">
                              <thead>';
                                    $grupoInicial = "";
                                    if (!$temGrupos) {
                                      echo '<tr>
                                        <th>Pos</th>
                                        <th>Time</th>
                                        <th>P</th>
                                        <th>J</th>
                                      </tr>';
                                      
                                    } 
                              echo '</thead>   
                              <tbody>';  
                                    $k = 0;
                                    for ($i = 0; $i < count($teams); $i = $i + 1) {

                                        $torneo = "";
                                        $k = $k + 1;                                       
//                                        print_r("k ".$k." - ");
                                        if ($temGrupos && strcmp($teams[$i]['tm_grupo'],$grupoInicial) != 0) {
                                          $grupoInicial = $teams[$i]['tm_grupo'];
                                          $k = 1;
                                           echo '<tr>
                                                    <td></td>
                                                    <td>&nbsp;</td>
                                                    <td></td>
                                                    <td></td>

                                            </tr>';                                           
                                            echo '<tr>
                                                    <td></td>
                                                    <td>Grupo '.$grupoInicial.'</td>
                                                    <td></td>
                                                    <td></td>

                                            </tr>';                                            
                                            
                                        }
                                        
                                        if (!empty($champion['ch_sec1_ini']) && $k >= $champion['ch_sec1_ini'] && $k <= $champion['ch_sec1_fin']) {
                                            $torneo = "badge badge-success";
                                        }
                                        if (!empty($champion['ch_sec2_ini']) && $k >= $champion['ch_sec2_ini'] && $k <= $champion['ch_sec2_fin']) {
                                            $torneo = "badge badge-info";
                                        }

                                        if (!empty($champion['ch_sec3_ini']) && $k >= $champion['ch_sec3_ini'] && $k <= $champion['ch_sec3_fin']) {
                                            $torneo = "badge badge-important";
                                        }
                                        
                                        echo '<tr>
                                                <td id="tm_id'.$i.'"><span class="'.$torneo.'">'.($k).'</td>
                                                <td id="tm_name'.$i.'">'.Helpers_Html::getTeamLinkLeft($this->base, $teams[$i]['tm_id'], $teams[$i]['tm_name'], $config->host.$teams[$i]['tm_logo'], $champ).'</td>
                                                <td id="tm_points'.$i.'">'.$teams[$i]['tm_points'].'</td>
                                                <td id="tm_played'.$i.'">'.$teams[$i]['tm_played'].'</td>

                                        </tr>';

                                    }  

                              echo '</tbody>
                     </table>  
                     <div class="pagination pagination-centered">
                      <ul class="pagination" id="ul_positions">



                      </ul>
                    </div>     
            </div>
        </div>';
    }
}
