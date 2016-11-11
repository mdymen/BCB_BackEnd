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
                          <td><a href='.$url.'/usuario/posicaoglobal?usuario='.$user_id.' >Posicao</a></td> 
                          <td width="15%" id="td_global">'.$posicao.'</td>  
                      </tr>                                                                                                       
                      <tr>
                          <td><a href='.$url.'/usuario/acertados?usuario='.$user_id.' >Acertos</a></td> 
                          <td width="15%" id="td_acertados">'.$acertados.'</td>                                           
                      </tr>
                      <tr>
                          <td><a href='.$url.'/usuario/errados?usuario='.$user_id.' >Erros</a></td> 
                          <td width="15%" id="td_errados">'.$errados.'</td>                                           
                      </tr>
                      <tr>
                          <td><a href='.$url.'/usuario/palpitados?usuario='.$user_id.' >Palpitados</a></td> 
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
                                                    $st = "";
                                                    if ($matches[$i]['rs_id'] == -1) {
                                                        $st='style="display:none"';
                                                    }
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
        return '<a href="'.$baseUrl."/team/team?team=".$id_team."&champ=".$champ.'">'.$nome_team.'</a><img width="28px;" height="21px;" src="'.$logo_team.'" />';
    }
    
    public static function getTeamLinkLeft($baseUrl, $id_team, $nome_team, $logo_team, $champ) {
        return '<img width="28px;" height="21px;" src="'.$logo_team.'" /><a href="'.$baseUrl."/team/team?team=".$id_team."&champ=".$champ.'">'.$nome_team.'</a>';
    }
    
}
