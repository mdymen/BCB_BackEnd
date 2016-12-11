<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of result
 *
 * @author Martin Dymenstein
 */
class Application_Model_Result extends Zend_Db_Table_Abstract
{
    protected $_name = 'result';
    
    public function update(array $params, $where) {
        
        $db = Zend_Db_Table::getDefaultAdapter(); 

        $info = array(
            'rs_res1'=>$params['res1'],
            'rs_res2'=>$params['res2'],
        );       
        
        $db->update('result',$info, 'rs_id = '.$params['rs_id']);
    }
    
    public function update_puntagem($puntagem, $rs_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $res = 0;
        if ($puntagem != 0) {
            $res = 1;
        }
        
//        print_r("res ".$puntagem);
//        die(".");
        
        $info = array('rs_points' => $puntagem, 'rs_result' => $res);
        
        //$db->update('user_penca', $info, 'up_idpenca = '.$penca.' and up_iduser ='.$usuario);
        $db->update('result', $info, 'rs_id = '.$rs_id);
    }
    
    public function update_resultado($id_match, $res1, $res2) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $dados = array(
            'mt_goal1' => $res1, 
            'mt_goal2' => $res2,
            'mt_played' => true
            );
        
        $db->update('match', $dados,'mt_id = '.$id_match);
    }
    
    public function getResult($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        return $db->select()->from("result")
                ->where("rs_id = ?", $id)
                ->query()
                ->fetch();  
    }
    
    public function palpites_em_acao($us_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("count(rs_id) as cantidad"))
                ->where("rs_result is null")
                ->where("rs_iduser = ?", $us_id)
                ->query()
                ->fetchAll();

        return $result;        
    }
    
    public function points($us_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("sum(rs_points) as pontos"))
                ->where("rs_iduser = ?", $us_id)
                ->query()
                ->fetchAll();

        return $result;         
    }
    
    public function palpites_em_acao_group($us_id, $ordem) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $o = "";
        if (!empty($ordem)) {
            if ($ordem == 1) {
                $o = "match.mt_idchampionship";
            } else if ($ordem == 2) {
                $o = "match.mt_date";
            } else if ($ordem == 3) {
                $o = "match.mt_round";
            } else {
                $o = "match.mt_idchampionship";
            }
        }
        
//        $result = $db->select()->from("result")
//                ->joinInner("match", "match.mt_id = result.rs_idmatch")
//                ->joinInner("championship", "championship.ch_id = match.mt_idchampionship")
//                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name', 'tm1_logo' => 't1.tm_logo'))
//                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name', 'tm2_logo' => 't2.tm_logo'))
//                ->where("rs_result is null")
//                ->where("rs_iduser = ?", $us_id)
            $result = $db->select()->from("vwmatchsresult")
                ->joinInner("vwpalpites","vwpalpites.rs_idmatch = vwmatchsresult.mt_id")
                ->where("rs_result is null")
                ->where("mt_played = 0")    
                ->where("rs_iduser = ?", $us_id)
                ->order($o)
                ->query()
                ->fetchAll();
                
//            print_r($result);
            
            
//                 print_r($result->__toString());
//        die(".");
             
        return $result;
        
    }
    
    public function puntuacao($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("sum(rs_points) as pontos"))
                ->where("rs_iduser = ?", $id_user)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function ganados($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("count(rs_points) as ganados"))
                ->where("rs_iduser = ?", $id_user)
                ->where("rs_result = 0")
                ->query()
                ->fetchAll();
        
        return $result;
    }    
    
    public function perdidos($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("count(rs_points) as perdidos"))
                ->where("rs_iduser = ?", $id_user)
                ->where("rs_result = 0")
                ->query()
                ->fetchAll();
        
        return $result;        
    }
    
    public function ranking_round($round, $championship) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("vwranking_round")
                ->where("mt_round = ?", $round)
                ->where("mt_idchampionship = ?", $championship)
                ->order("points DESC");
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function ranking_champ($championship) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("vwranking_championship")
                ->where("mt_idchampionship = ?", $championship)
                ->order("points DESC");
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
}
