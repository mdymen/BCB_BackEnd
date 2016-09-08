<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of penca
 *
 * @author Martin Dymenstein
 */
class Application_Model_Penca extends Zend_Db_Table_Abstract
{

    protected $_name = 'penca';
    
    public function save($params) {
        $info = array(
            'pn_name'=>$params['pn_name'],
            'pn_value'=>$params['pn_value'],
            'pn_iduser'=>$params['pn_iduser'],
            'pn_idchampionship' =>$params['tm_idchampionship'],
            'pn_justfriends' => $params['pn_justfriends'],
            'pn_password' => $params['pn_password']
        );       
        
        $this->insert($info);
    }
        
    public function save_userpenca($params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $info = array(
            'up_idpenca' => $params['up_idpenca'],
            'up_iduser' => $params['up_iduser'],
        );
        
        $db->insert("user_penca", $info);
    }
    
    public function load_penca__puntagem_usuario($id_user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        return $db->select()->from("user_penca")
                ->joinInner("penca", "user_penca.up_idpenca = penca.pn_id")
                ->joinInner("championship", "penca.pn_idchampionship = championship.ch_id")
                ->where("up_iduser = ?", $id_user)
                 ->query()->fetchAll();
    }
    
    public function load_penca_users($penca) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        return $db->select()->from("user_penca")
                ->joinInner("user","user_penca.up_iduser = user.us_id")
                ->where("up_idpenca = ?", $penca)
                ->order("up_puntagem desc")
                ->query()->fetchAll();
    }
    
    public function load_pencas() {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $return = $db->select()->from("penca")
                ->joinInner("championship", "penca.pn_idchampionship = championship.ch_id")
                ->query()->fetchAll();
        
        return $return;
    }
    
    public function load_pencas_usuario($id_usuario) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $return = $db->select()->from("penca") 
                ->joinInner("championship", "penca.pn_idchampionship = championship.ch_id")
                ->where("pn_iduser = ?", $id_usuario)
                ->query()
                ->fetchAll();
        
        return $return;
    }
    
    public function load_participantes($penca) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from('user_penca', array('count(*) as participantes'))
                ->where('user_penca.up_idpenca = ?', $penca)->query()->fetchAll();
        
        return $result;
    }
    
    public function load_penca($penca) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from('penca')
                ->joinInner('championship','penca.pn_idchampionship = championship.ch_id')
                ->joinInner('user','penca.pn_iduser = user.us_id')
                ->where('penca.pn_id = ?', $penca)
                ->query()
                ->fetchAll();
        
        return $result;

    }
    
    public function load_usuarios($id_penca) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("user_penca")
                ->joinInner('user', 'user_penca.up_iduser = user.us_id')
                ->where('user_penca.up_idpenca = ?', $id_penca)
                ->query()
                ->fetchAll();

        return $result;
    }
    
    public function palpites($penca, $round, $usuario) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("result")
                ->joinInner('match','result.rs_idmatch = match.mt_id')
                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
                ->where('result.rs_idpenca = ?', $penca)
                ->where('result.rs_round = ?', $round)
                ->where('result.rs_iduser = ?', $usuario)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function rodada($idcham, $round) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("match")
                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
                ->where('mt_round = ?', $round)
                ->where('mt_idchampionship = ?', $idcham)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function load_pencas_byChamp($id_championship) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("penca")
                ->joinInner("championship", 'championship.ch_id = penca.pn_idchampionship')
                ->where("championship.ch_id = ?", $id_championship)
                ->query()
                ->fetchAll();
        
        return $result;
    }

}