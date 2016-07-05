<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of penca
 *
 * @author Martin Dymenstein
 */
class Application_Model_Penca extends Zend_Db_Table_Abstract
{

    protected $_name = 'penca';
    
    public function save($params) {
        $info = array(
            'pn_name'=>$params['name'],
            'pn_value'=>$params['value'],
            'pn_iduser'=>$params['iduser'],
        );       
        $this->insert($info);
    }
        
    
}
