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
    
    public function update($params) {
        
        $db = Zend_Db_Table::getDefaultAdapter(); 

        $info = array(
            'rs_res1'=>$params['res1'],
            'rs_res2'=>$params['res2'],
        );       
        
        $db->update('result',$info, 'rs_id = '.$params['rs_id']);
    }
    
    public function update_puntagem($puntagem, $id_match) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $info = array('rs_points' => $puntagem);
        
        //$db->update('user_penca', $info, 'up_idpenca = '.$penca.' and up_iduser ='.$usuario);
        $db->update('result', $info, 'rs_idmatch = '.$id_match);
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
    
}
