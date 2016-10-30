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
               ->where("rs_points = 1")
                ->orWhere("rs_points = 5")
                ->query()
                ->fetch();
        
        return $result['wonmatches'];
    }
    
    public function getLostMatches($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("count(*) as lostmatches"))
               ->where("rs_iduser = ?",$id_user)
               ->where("rs_points = 0")
                ->where("rs_result is null")
                ->query()
                ->fetch();
        
            return $result['lostmatches'];

    }
    
    public function getPlayedMatches($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("count(*) as played"))
               ->where("rs_iduser = ?",$id_user)
                ->where("rs_result = null")
                ->query()
                ->fetch();
        
        return $result['played'];        
    }
    
    public function getPoints($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result", array("sum(rs_points) as points"))
                ->where("rs_iduser = ?",$id_user)
                ->query()
                ->fetch();
        
        return $result['points'];
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
    
}