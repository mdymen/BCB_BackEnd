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
class Application_Model_Usuarios extends Zend_Db_Table_Abstract
{

    protected $_name = 'usuarios';
    
    public function save($params) {
        $info = array(
            'us_user'=>$params['username'],
            'us_password' => $params['password'],
        );       
        $this->insert($info);
    }
        
    
}