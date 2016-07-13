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

    protected $_name = 'championship';
    
    public function save($params) {
        
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $info = array(
            'ch_nome'=>$params['name'],
        );       
        
        $db->insert($this->_name,$info);
    }
    
    public function load() {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $table = $this->_name;
        
        $select = $db->select($table)->from($table);
        
        $query = $select->query();
        
        $championships = $query->fetchAll();
        
        return $championships;
    }

}