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
class Helpers_Ranking {
    
    public $vwranking;
    public $acumulado;
    public $titulo;
    public $fechado = false;
    
    public $base;
    
    public function ranking() {
        $config = new Zend_Config_Ini('config.ini');
        
        $vwranking = $this->vwranking;
        
        $chevron = "up";
        $box_content = 'style="display:block"';
        if ($this->fechado) {
            $chevron = "down";
            $box_content = 'style="display:none"';
        }
        
        echo '<div class="box">
            <div class="box-header">
                    <h2><i class="fa fa-align-justify"></i><span class="break"></span>'.$this->titulo.'</h2>

                    <div class="box-icon">
                        <span><h2 style="margin-right: 30px !important">'.$this->acumulado.'</h2></span>
                        <a href="form-elements.html#" class="btn-minimize"><i class="fa fa-chevron-'.$chevron.'"></i></a>
                    </div>
            </div>
            <div class="box-content" '.$box_content.'>
                    <table class="table">
                              <thead>
                                      <tr>

                                      </tr>
                              </thead>   
                              <tbody>';  

                                    for ($i = 0; $i < count($vwranking); $i = $i + 1) {

                                        $torneo = "";
                                        $k = $i + 1;
//                                        if (!empty($champion['ch_sec1_ini']) && $k >= $champion['ch_sec1_ini'] && $k <= $champion['ch_sec1_fin']) {
//                                            $torneo = "badge badge-success";
//                                        }
//                                        if (!empty($champion['ch_sec2_ini']) && $k >= $champion['ch_sec2_ini'] && $k <= $champion['ch_sec2_fin']) {
//                                            $torneo = "badge badge-info";
//                                        }
//
//                                        if (!empty($champion['ch_sec3_ini']) && $k >= $champion['ch_sec3_ini'] && $k <= $champion['ch_sec3_fin']) {
//                                            $torneo = "badge badge-important";
//                                        }

                                        echo '<tr>
                                                <td><span class="'.$torneo.'">'.($i+1).'</td>
                                                <td><span class="'.$torneo.'">'.$vwranking[$i]['points'].'</td>
                                                <td>'.$vwranking[$i]['us_username'].'</td>

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
