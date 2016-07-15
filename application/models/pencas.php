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
            'pn_name'=>$params['pn_name'],
            'pn_value'=>$params['pn_value'],
            'pn_iduser'=>$params['pn_iduser'],
            'pn_idchampionship' =>$params['tm_idchampionship'],
        );       
        
        $this->insert($info);
    }
        
    public function save_userpenca($params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $info = array(
            'up_idpenca' => $params['up_idpenca'],
            'up_iduser' => $params['up_iduser'],
        );
        
        $db->insert("user_penca", $info);
    }
}
