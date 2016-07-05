<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of teams
 *
 * @author Martin Dymenstein
 */
class Application_Model_Team extends Zend_Db_Table_Abstract
{

    protected $_name = 'teams';
    
    public function save($params) {
        $info = array(
            'tm_name'=>$params['name'],
        );       
        $this->insert($info);
    }
        
    
}
