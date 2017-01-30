<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paginacao
 *
 * @author Martin Dymenstein
 */
class Helpers_Paginacao {
    
    public $teams;
    
    public $link_click_rodada;
    
    public $championship_id;
    
    public $teamuserid;
    
    public $teamusername;
    
    public $rodada_atual_id;
    
    public $rondas;
    
    public $base;
        
    public function paginacao_aux() {
                $t = Zend_Registry::get('translate');
        $teamuserid = $this->teamuserid;        
        $teams_coracao = $this->teams;
                 echo '<div class="box" id="teamcoracao_pick" style="display:none">
                        <div class="box-header">
                            <h2><i class="fa fa-align-justify"></i><span class="break"></span>'.$t->_('time.do.coracao').'</h2>
                            <div class="box-icon">
                        </div>
                </div>
                <div class="box-content">
                    <div class="row">

                        <div class="box-content">
                <div class="pagination pagination-centered" style="margin: 0px !important">
                    <form class="col-lg-4 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6" style="padding-left: 0px">
                        <div class="control-group form-group"><div>
                                <div class="editable-input" style="position: relative;">
                                    <div class="form-group">
                                    <div class="controls">
                                                    <div class="input-group date">
                                                            <span class="input-group-addon">
                                                                <i class="fa  fa-search"></i>
                                                            </span>
                                                            <div class="controls">
                                                                <input type="hidden" value="<?php echo $champ; ?>" name="rodada" />
                                                            <select id="team_coracao" name="team_coracao" class="form-control">';
                                                                         

                                                                for ($i = 0; $i < count($teams_coracao); $i = $i + 1) {
                                                                    $selected="";
                                                                    if ($teamuserid == $teams_coracao[$i]['tm_id']) {
                                                                        $selected = "selected";
                                                                    }
                                                                    echo '<option '.$selected.' value="'.$teams_coracao[$i]['tm_id'].'">'.$teams_coracao[$i]['tm_name'].'</option>';
                                                                }                                                        
                                                           
                                                            echo '</select>
                                                        </div>
                                                    </div>	
                                              </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                <div class="controls">
                                    <div>
                                        <button id="aceitar_teamusername" type="button" class="btn btn-primary btn-sm editable-submit">
                                            <i class="glyphicon glyphicon-ok"></i>
                                        </button>
                                        <button type="button" id="btn_cancel_teamcoracao" class="btn btn-default btn-sm editable-cancel">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </button>
                                    </div>
                                </div>
                                    </div>
                            </div>
                            <div class="editable-error-block help-block" style="display: none;">

                            </div>

                        </div>
                    </form>
                </div>

            </div>    
                 
                        

                    <div class="form-action">                              
                    </div>
                    </div>
                </div>
        </div>';
    }
    
    public function paginacao() {
        $champ = $this->championship_id;
        $rondas = $this->rondas;
        $teamuserid = $this->teamuserid;
        $teamusername = $this->teamusername;
        
        echo '<input type="hidden" value="'.$champ.'" id="champ">';
        echo '<div class="box">
            <div class="box-content">
                <div class="pagination pagination-centered" style="margin: 0px !important">
                    <ul class="pagination">

                          <li><a id="teamusername" href="javascript:void(0)"><i class="fa fa-edit"></i></a></li><li><a href="'.$this->base.'/penca/bolao?team='.$teamuserid.'&champ='.$champ.'" data-type="text" data-pk="1" data-original-title="Enter username" id="teamcoracao_nome" class="editable editable-click">'.$teamusername.'</a></li>';
                                for ($i = 0; $i < count($rondas); $i = $i + 1) {
                                    $r = $i + 1;
                                    $active = "";
                                    if ($rondas[$i]['mt_idround'] == $this->rodada_atual_id ) { 
                                        $active = 'class="active"';                                
                                    } 
                                    echo '<li '.$active.'><a href="'.$this->base.$this->link_click_rodada.'?rodada='.$rondas[$i]['mt_idround'].'&champ='.$champ.'">'.$rondas[$i]['rd_round'].'</a></li>';
                                }

                            
                    echo '</ul>
                </div>';
                    
                        $this->paginacao_aux();
                    
            echo '</div>
            
        </div>';

    }
}
