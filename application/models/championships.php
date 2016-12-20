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
        
        $db->insert($this->_name,$params);
    }
    
    public function load() {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $table = $this->_name;
        
        $select = $db->select($table)->from($table);
        
        $query = $select->query();
        
        $championships = $query->fetchAll();
        
        return $championships;
    }

    public function ranking($champ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("ranking")
                ->where("rk_idchamp = ?", $champ)
                ->query()
                ->fetchAll();
                
        
        return $result;
    }
    
    public function setAtualRound($champ, $round) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $return = $db->select()->from("vwranking_round", array('max(points) as points'))
                ->where("mt_idchampionship = ?", $champ)
                ->where("rd_id = ?", $round)
                ->query()
                ->fetch();
        
        $points = $return['points'];
        
        $ganadores = $db->select()->from("vwranking_round")
                ->where("mt_idchampionship = ?", $champ)
                ->where("rd_id = ?", $round)
                ->where("points = ?", $points)->query()->fetchAll();
        
        
        $round = $db->select()->from("round")->where("rd_id = ?",$round)->query()->fetch();
        $rd_acumulado = $round['rd_acumulado'];
        
        $total_por_jogador = $rd_acumulado/count($ganadores);
        
//        for ($i = 0; $i < count($ganadores); $i = $i + 1) {
//            $user = $db->select()->from("user", "us_cash")->where("us_id = ?", $ganadores[$i]['us_ud'])->query()->fetch();
//            $user['us_cash'] = $user['us_cash'] + $total_por_jogador;
//            $db->update("user", $bind)
//        }
        
        $db->update("championship", array('ch_atualround' => $round), "ch_id = ".$champ);
    }
    
    public function getChamp($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $db->select()->from("championship")
                ->where("ch_id = ?", $id)
                ->query()
                ->fetch();
        
        return $result;
        
    }
    
    public function getrondas($champ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("round")
                ->where("rd_idchampionship = ?", $champ)
                ->order("rd_round")
                ->query()
                ->fetchAll();
        
        return $result;
                        
    }
    
}