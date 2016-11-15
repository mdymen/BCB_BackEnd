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
            'tm_logo' => $params['tm_logo'],
            'tm_points' => 0,
            'tm_played' => 0
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
    
    public function sum_points($team, $points) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("team", array('tm_points','tm_played'))
                ->where("tm_id = ?", $team)->query()->fetch();
        
        $db->update("team", array('tm_points' => $result['tm_points'] + $points, 'tm_played' => $result['tm_played'] + 1), "tm_id = ".$team);
        
    }
    
    public function sum_match($team) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("team", array('tm_played'))
                ->where("team.tm_id = ?", $team)->query()->fetch();
        
        $db->update("team", array('tm_played' => $result['tm_played'] + 1), "tm_id = ".$team);
    }
    
    public function getJogosTeam($team, $champ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship and championship.ch_id = ".$champ)
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' => 't1.tm_id', 't1nome' => 't1.tm_name', 'tm1_logo' => 't1.tm_logo'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' => 't2.tm_id', 't2nome' => 't2.tm_name', 'tm2_logo' => 't2.tm_logo'))
            ->where("t1.tm_id = ?", $team)
            ->orWhere("t2.tm_id = ?", $team)
            ->order("mt_round DESC")    
            ->query()
            ->fetchAll();
        
        return $result;
    }
}
