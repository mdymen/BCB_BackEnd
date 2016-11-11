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
        
    public function setTeamCoracao($id, $name, $us_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $db->update("user", array("us_team" => $id, "us_teamname" => $name), "us_id = ".$us_id);
    }
    
    public function setNovaSenha($senha, $us_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->update("user", array("us_password" => $senha), "us_id = ".$us_id);
    }
    
    public function getWonMatches($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("count(*) as wonmatches"))
               ->where("rs_iduser = ?",$id_user)
               ->where("rs_date < " + date("m-d-Y"))
               ->where("rs_points = 1")
                ->orWhere("rs_points = 5")
                ->query()
                ->fetch();
        
        return $result['wonmatches'];
    }    
    
    public function getLostMatches($us_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("count(*) as losts"))
               ->where("rs_iduser = ?",$us_id)
                ->where("rs_date < " + date("m-d-Y"))
               ->where("rs_points = 0")
                ->query()
                ->fetch();
        
        return $result['losts'];        
    }
    
    public function getMatchesLostMatches($us_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")
                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name'))
                ->joinRight("result", "match.mt_id = result.rs_idmatch")
                ->where("rs_iduser = ?",$us_id)
                ->where("rs_points = 0")
                ->query()
                ->fetchAll();
        
        return $result;  

    }
    
    public function getMatchesWonMatches($us_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")
                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name', 'tm1_logo' => 't1.tm_logo', 'tm1_id' => 't1.tm_id'))
                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name', 'tm2_logo' => 't2.tm_logo', 'tm2_id' => 't2.tm_id'))
                ->joinRight("result", "match.mt_id = result.rs_idmatch")
                ->where("rs_iduser = ?",$us_id)
                ->where("rs_points = 5")
                ->orWhere("rs_points = 1")
                ->query()
                ->fetchAll();
        
        return $result;  

    }
    
    public function getPlayedMatches($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("count(*) as played"))
               ->where("rs_iduser = ?",$id_user)
                ->where("rs_date < " + date("m-d-Y"))
                ->where("rs_result is not null")
                ->query()
                ->fetch();
        
        return $result['played'];        
    }
    
    public function getPoints($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("sum(rs_points) as points"))
                ->where("rs_iduser = ?",$id_user)
                ->where("rs_date < " + date("m-d-Y"))
                ->query()
                ->fetch();
        
        return $result['points'];
    }
    
    public function posicao_por_campeonato($id_user) {
//        $db = Zend_Db_Table::getDefaultAdapter();
//        
//        $db->select()->
    }
    
    public function getPoisition($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $index = "@us_".$id_user;
        
        $db->query("SET ".$index."=0");
       
        $sql = "select ".$index.":=".$index." + 1 as position, sum(rs_points) as points, rs_iduser"
                . " from result where rs_iduser = ".$id_user." group by rs_iduser"
                . " order by rs_points DESC";

//        $result = $db->select()->from("result",array($index.":=".$index." + 1 as position", 
//            "sum(rs_points) as points","rs_iduser"))
//                ->where("rs_iduser = ?",$id_user)
//                ->order("rs_points DESC")
//                ->group("rs_iduser")
//                ->query()
//                ->fetch();
        
        $result = $db->query($sql)->fetch();
        
        return $result['position'];
    }
    
    public function save_opcoes($id_user, $array_opcoes) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        print_r($array_opcoes);
        die(",");
        
        $db->update("user", $array_opcoes, "us_id = ".$id_user);
    }
    
    public function historico_palpites($us) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")  
            ->joinInner("result", "result.rs_idmatch = match.mt_id")    
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' => 't1.tm_id', 'tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' => 't2.tm_id', 'tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'))
            ->where('result.rs_iduser = ?', $us)
            ->order('mt_date')
            ->query()
            ->fetchAll();
        
        return $result;
    }
    
}