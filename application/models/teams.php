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
class Application_Model_Teams extends Zend_Db_Table_Abstract
{

    protected $_name = 'team';
    
    public function save($params) {
        $info = array(
            'tm_name'=>$params['tm_name'],
            'tm_idchampionship' =>$params['tm_idchampionship'],
        );       
        $this->insert($info);
    }
    
    public function load($championship) {
        

        $db = Zend_Db_Table::getDefaultAdapter();
        
        $teams = $db->select()->from("team")
          ->where("tm_idchampionship = ?",$championship)
          ->query()->fetchAll();

        return $teams;
    }
        
    public function load_penca_limit($championship, $limit) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from('team')
                ->where('team.tm_idchampionship = ?', $championship)
                ->limit(4, ($limit-1)*4)
                ->order("team.tm_points DESC")    
                ->query()
                ->fetchAll();

        return $result;        
    }
    
    public function load_teams_championship($champ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from('team')
                ->where('team.tm_idchampionship = ?', $champ)
                ->order("team.tm_points DESC")    
                ->query()
                ->fetchAll();

        return $result;  
    }
}
