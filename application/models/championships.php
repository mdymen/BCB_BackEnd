<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of championships
 *
 * @author Martin Dymenstein
 */
class Application_Model_Championships extends Zend_Db_Table_Abstract
{

    protected $_name = 'champsionship';
    
    public function save($params) {
        
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $info = array(
            'ch_nome'=>$params['nome'],
        );       
        $db->insert($this->_name,$info);
    }
        
    
}