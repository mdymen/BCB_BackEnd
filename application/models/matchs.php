<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of matchs
 *
 * @author Martin Dymenstein
 */
class Application_Model_Matchs extends Zend_Db_Table_Abstract
{

    protected $_name = 'matchs';
    
    public function save($params) {
        $info = array(
            'mt_team1'=>$params['team1'],
            'mt_team2'=>$params['team2'],
            'mt_date'=>$params['date'],
            'mt_goal1'=>$params['goal1'],
            'mt_goal2'=>$params['goal2'],
        );       
        $this->insert($info);
    }
        
    
}
