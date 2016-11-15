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
include APPLICATION_PATH."/helpers/data.php";
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
    
    public function load_palpites_simples($championship, $rodada, $usuario) {
         $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("vwmatchsresult")
                ->where("rs_iduser= ?", $usuario)
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_round = ?", $rodada)
                ->order(array('mt_date ASC'))
                ->query()
                ->fetchAll();
        
        
        return $result;       
    }
    
//    public function load_palpites_simples($championship, $rodada, $usuario) {
//         $db = Zend_Db_Table::getDefaultAdapter();
//        
//        $result = $db->select()->from("match")
//                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name'))
//                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name'))
//                ->joinRight("result", "match.mt_id = result.rs_idmatch")
//                ->where("match.mt_idchampionship = ?", $championship)
//                ->where("match.mt_round = ?", $rodada)
//                //->where("result.rs_id <> '' " )
//                ->query()
//                ->fetchAll();
//        
//        
//        return $result;       
//    }
    
    public function load_rodada($championship, $rodada) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("vwmatchsresult")
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_round = ?", $rodada)
                ->order(array('mt_date ASC'))
                ->query()
                ->fetchAll();

        return $result;
        
    }
    
    public function load_rodada_com_palpites($championship, $rodada, $usuario) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("vwmatchsresult")
                ->joinLeft("user", "user.us_id = vwmatchsresult.rs_iduser AND user.us_id = ".$usuario)
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_round = ?", $rodada)
                ->order(array('mt_date ASC'))
                ->query()
                ->fetchAll();

        return $result;
        
    }
    
    public function load_rodada_porteam($championship, $team, $usuario) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("vwmatchsresult")
//                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name'))
//                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name'))
//                ->joinLeft("result", "match.mt_id = result.rs_idmatch and result.rs_iduser = ".$usuario)
//                ->where("match.mt_idchampionship = ?", $championship)
//                ->where("match.mt_idteam1 = ?", $team)
//                ->orWhere("match.mt_idteam2 = ?",$team)
                ->where("rs_iduser = ? ",$usuario)
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_idteam1 = ?", $team)
                ->orWhere("mt_idteam2 = ?",$team)
                //->where("result.rs_id is null " )
                //->where("result.rs_iduser = ?", $usuario)
                ->query()
                ->fetchAll();
        
        return $result;
        
    }    
    
    public function load_porteam($championship, $team, $usuario) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("vwmatchsresult")
                ->where("rs_iduser = ?", $usuario)
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_idteam1 = ?", $team)
                ->orWhere("mt_idteam2 = ?", $team)
//                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name'))
//                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name'))
//                ->joinInner("result", "match.mt_id = result.rs_idmatch and result.rs_iduser =".$usuario)
//                ->where("match.mt_idchampionship = ?", $championship)
//                ->where("match.mt_idteam1 = ?", $team)
//                ->orWhere("match.mt_idteam2 = ?", $team)
                 ->query()
                ->fetchAll();
        
//        print_r($result->__toString());
//        die(".");
               
        
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
    
    public function submeter_result($user_id, $result1, $result2, $match_id, $round) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $dados = array(
            'rs_idmatch' => $match_id,
            'rs_res1' => $result1,
            'rs_res2' => $result2,
            'rs_iduser' => $user_id,
            'rs_round' => $round
                
        );
        
        $db->insert("result", $dados);
        return $db->lastInsertId();
    }
    
    public function load_resultados_palpitados($match) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result")
                ->where("rs_idmatch = ?", $match)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function delete_palpite($result) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $db->delete("result", "rs_id = ".$result);
    }
    
    public function result($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result")
                ->joinInner('match','result.rs_idmatch = match.mt_id')
                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
                ->where('result.rs_id = ?', $id)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function rondas($champ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $db->select()->from('match', array('count(distinct mt_round) as rounds'))
                ->where("mt_idchampionship", $champ)
                ->query()
                ->fetch();

        return $result;
        
    }
    
    public function getrondas($champ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match", 'mt_round')
                ->distinct()
                ->where("mt_idchampionship = ?", $champ)
                ->order("mt_round")
                ->query()
                ->fetchAll();
        
        return $result;
                        
    }
    
    public function load_matchs_byrodada($champ, $rodada) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
            ->where('mt_idchampionship = ?', $champ)
            ->where("mt_round = ?",$rodada)
            ->query()
            ->fetchAll();
        
        return $result;
        
    }
    
    public function setDatas($matchs) {
        $data = new Helpers_Data();
        for ($i = 0; $i < count($matchs); $i = $i + 1) {
            $matchs[$i]['mt_date'] = $data->format($matchs[$i]['mt_date']);
        }
        return $matchs;
    }
    
    public function load_all_matchs($champ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")  
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'))
            ->where('mt_idchampionship = ?', $champ)
            ->order('mt_round','mt_date')
            ->query()
            ->fetchAll();
        
        return $result;
    }
    
}
