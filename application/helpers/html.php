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
          return '<table class="table" id="tb_palpites_feitos">
                    <thead>

                            <tr>
                                    <th width="15%"></th>
                                    <th></th>
                            </tr>
                    </thead>   
                    <tbody>
                      <tr>    
                          <td><a href='.$url.'/usuario/puntuacao?usuario='.$user_id.' >Puntuacao</a></td> 
                          <td width="15%" id="td_puntuacao">'.$puntuacoes.'</td>  
                      </tr>   
                      <tr>    
                          <td><a href='.$url.'/usuario/posicaoglobal?usuario='.$user_id.' >Posicao global</a></td> 
                          <td width="15%" id="td_global">'.$posicao.'</td>  
                      </tr>                                                                                                       
                      <tr>
                          <td><a href='.$url.'/usuario/acertados?usuario='.$user_id.' >Palpites acertados...</a></td> 
                          <td width="15%" id="td_acertados">'.$acertados.'</td>                                           
                      </tr>
                      <tr>
                          <td><a href='.$url.'/usuario/errados?usuario='.$user_id.' >Palpites errados...</a></td> 
                          <td width="15%" id="td_errados">'.$errados.'</td>                                           
                      </tr>
                      <tr>
                          <td><a href='.$url.'/usuario/palpitados?usuario='.$user_id.' >Palpitados...</a></td> 
                          <td width="15%" id="td_errados">'.$palpitados.'</td>                                           
                      </tr>                                          

                    </tbody>
           </table> ';
    
    }
    
    public function acertados($matches) {
         $r = '<div class="box">
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
                                                          <th width="15%"></th>
                                                          <th></th>
                                                          <th width="15%"></th>
                                                          <th></th> 
                                                          <th width="15%"></th> 
                                                          <th></th> 
                                                          <th></th> 
                                                  </tr>
                                          </thead>   
                                          <tbody>';
                                              
                                            
                                              $anterior = "";
                                              $r = $r.'<input cant="'.count($matches).'" type="hidden" value="'.count($matches).'" id="count_palpites_feitos" name="count_palpites_feitos">';
                                               
                                              for ($i = 0; $i < count($matches); $i = $i + 1) {
                                                  if ($anterior != $matches[$i]['rs_round']) {
                                                      $anterior = $matches[$i]['rs_round'];
                                                  }  
                                                  if (!empty($matches[$i]['mt_id'])) {
                                                    $st = "";
                                                    if ($matches[$i]['rs_id'] == -1) {
                                                        $st='style="display:none"';
                                                    }
                                                    $r = $r.'<input '.$st.' type="hidden" name="rs_'.$i.'_input" id="rs_'.$i.'" value="'.$matches[$i]['mt_id'].'" />
                                                            <tr name="rs_'.$i.'_dados" '.$st.' pf="'.($i+1).'" id="rstr2'.$matches[$i]['rs_id'].'">
                                                            <td><b>
                                                               '.date("H:i",strtotime($matches[$i]['mt_date']))."hs. ".date("d-m-y",strtotime($matches[$i]['mt_date']) ).'
                                                                </b></td>
                                                            <td width="15%">
                                                                <div class="input-group col-sm-7">
                                                                    <input class="form-control" disabled="true" name="p_result1_'.$i.'" value="'.$matches[$i]['rs_res1'].'" id="p_result1_'.$i.'"  type="text" placeholder="0">
                                                                </div>
                                                            </td>
                                                            <td id="team1'.$i.'">'.$matches[$i]['t1nome'].'</td>
                                                            <td></td>
                                                            <td id="team2'.$i.'">'.$matches[$i]['t2nome'].'</td>
                                                            <td width="15%">     
                                                                <div class="input-group col-sm-7">		
                                                                    <input class="form-control" disabled="true" name="p_result2_'.$i.'" value="'.$matches[$i]['rs_res2'].'" id="p_result2_'.$i.'" type="text" placeholder="0">
                                                                </div>
                                                            </td>  
                                                            <td>
                                                            </td>
                                                                <td><span class="label label-success">'.$matches[$i]['rs_points'].'</span></td>
                                                        </tr>';
                                                    }
                                                }
                                              
                                              
                                              
                                                                           
                                          $r = $r.'</tbody>
                                 </table>  
                               
                            <div class="form-action">
                                <!--<a href="javascript:void(0)" id="btnAceitarPalpites" class="btn btn-success">Aceitar Palpites</a>--> 
                            </div>
                        </div>
                </div>';
                
                return $r;
    }
    
}
