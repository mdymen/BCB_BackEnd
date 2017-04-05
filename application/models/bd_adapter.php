<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
class Application_Model_Bd_Adapter extends Zend_Db_Table_Abstract
{
//    public $bd_ = array(
//    'database' => 'penca',
//    'username' => 'root',
//    'password' => ''
//    );
    
    public $db;
    
    public function __construct() {
//         Zend_Db_Table::setDefaultAdapter($this->bd_);
        
        $bd = $this->getDb();
        
        if (!empty($bd)) { 
            $this->db = new Zend_Db_Adapter_Pdo_Mysql(array(
                'host'     => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname'   => $bd,
                'charset' => 'UTF8'
            ));
            
            /*           $this->db = new Zend_Db_Adapter_Pdo_Mysql(array(
                'host'     => 'localhost',
                'username' => 'wi061609_penca',
                'password' => 'wi27fekoRE',
                'dbname'   => $bd,
                'charset' => 'UTF8'
            ));*/
            
        } else {
            $this->db = Zend_Db_Table::getDefaultAdapter();
        }

    }
    
    public function getDb() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_base'];
    }
    
}

