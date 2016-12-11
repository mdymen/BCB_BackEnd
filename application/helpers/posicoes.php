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
        
        echo '<div class="box">
            <div class="box-header">
                    <h2><i class="fa fa-align-justify"></i><span class="break"></span>Tabela de Posicoes</h2>

                    <div class="box-icon">
                        <span><h2 style="margin-right: 30px !important" id="ronda_total_palpitado">'.number_format((float)$champion['ch_acumulado'], 2, '.', '').'</h2></span>
                        </div>
            </div>
            <div class="box-content">
                    <table class="table">
                              <thead>
                                      <tr>
                                        <th>Pos</th>
                                        <th>Time</th>
                                        <th>P</th>
                                        <th>J</th>

                                      </tr>
                              </thead>   
                              <tbody>';  

                                    for ($i = 0; $i < count($teams); $i = $i + 1) {

                                        $torneo = "";
                                        $k = $i + 1;
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
                                                <td id="tm_id'.$i.'"><span class="'.$torneo.'">'.($i+1).'</td>
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
