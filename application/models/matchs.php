<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of matchs
 *
 * @author Martin Dymenstein
 */
class Application_Model_Matchs extends Zend_Db_Table_Abstract
{

    protected $_name = 'match';
    
    public function save($params) {
        $info = array(
            'mt_idteam1'=>$params['team1'],
            'mt_idteam2'=>$params['team2'],
            'mt_date'=>$params['date'],
            'mt_idchampionship' => $params['championship'],
            'mt_round' => $params['round']
        );       
        $this->insert($info);
    }
        
    public function load($championship) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")
                ->where("mt_idchampionship = ?", $championship)
                ->query()
                ->fetchAll();
        
        return $result;
        
    }
    
    public function load_rodada($championship, $rodada) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")
                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name'))
                ->joinLeft("result", "match.mt_id = result.rs_idmatch")
                ->where("match.mt_idchampionship = ?", $championship)
                ->where("match.mt_round = ?", $rodada)
                ->query()
                ->fetchAll();
        
        return $result;
        
    }
    
    public function save_penca_match($dados) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
//        print_r($dados);
//        die(".");
        
        $d = array(
            'rs_idmatch' => $dados['idmatch'],
            'rs_idpenca' => $dados['idpenca'],
            'rs_iduser' => $dados['iduser'],
            'rs_res1' => 0,
            'rs_res2' => 0,
            'rs_date' => $dados['date'],
            'rs_round' => $dados['round']
        );
        
        $db->insert("result", $d);
    }
    
    public function submeter_result($user_id, $result1, $result2, $match_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $dados = array(
            'rs_idmatch' => $match_id,
            'rs_res1' => $result1,
            'rs_res2' => $result2,
            'rs_iduser' => $user_id
        );
        
        $db->insert("result", $dados);
        
    }
    
    public function load_resultados_palpitados($match) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result")
                ->where("rs_idmatch = ?", $match)
                ->query()
                ->fetchAll();
        
        return $result;
    }
}
