<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of usuario
 *
 * @author Martin Dymenstein
 */
class Application_Model_Users extends Zend_Db_Table_Abstract
{

    protected $_name = 'user';
    
    public function save($params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $info = array(
            'us_username'=>$params['username'],
            'us_password' => $params['password'],
        );  
        
        $db->insert($this->_name, $info);
    }
    
    public function user_penca($penca) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from('user_penca')
                ->joinInner('user', 'user_penca.up_iduser = user.us_id')
                ->where('user_penca.up_idpenca = ?', $penca)
                ->query()
                ->fetchAll();
        
        return $result;
    }
        
    
}