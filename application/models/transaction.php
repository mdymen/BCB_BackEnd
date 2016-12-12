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
class Application_Model_Transaction extends Zend_Db_Table_Abstract
{
    protected $_name = 'transaction';
    
    public function save_transaction($valortransaccion, $valorcampeonato, $iduser, $valorrodada, $valorjogo,  $idcampeonato, $idmatch ) {
        
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $data = array(
            'tr_valortransaccion' => $valortransaccion,
            'tr_valorcampeonato' => $valorcampeonato,
            'tr_iduser' => $iduser,
            'tr_valorrodada' => $valorrodada,
            'tr_valorjogo' => $valorjogo,
            'tr_idcampeonato' => $idcampeonato,
            'tr_idmatch' => $idmatch);
        
        $db->insert("transaction",$data);
        
    }
    
    public function getCampeonato($id_champ) {
        $db = Zend_Db_table::getDefaultAdapter();
        
        $result = $db->select()->from($this->_name)
                ->where("tr_idcampeonato = ?", $id_champ);
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    
    public function getRondaCampeonato($id_champ, $rodada) {
        $db = Zend_Db_table::getDefaultAdapter();
        
        $result = $db->select()->from($this->_name)
                ->innerJoin("match","match.mt_id = transaction.tr_idmatch")
                ->where("tr_idcampeonato = ?", $id_champ)
                ->where("match.mt_round = ?", $rodada);
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function getUsuario($us_id) {
        $db = Zend_Db_table::getDefaultAdapter();
        
        $result = $db->select()->from($this->_name)
                ->where("tr_iduser = ?", $us_id);
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function getCampeonatoRodadaUsuario($camp, $rodada, $us) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $result = $db->select()->from("transaction")
                ->innerJoin("match", "match.mt_id = transaction.tr_idmatch")
                ->where("match.mt_round = ?", $rodada)
                ->where("tr_idcampeonato = ?", $camp)
                ->where("transaction.tr_iduser = ?",$us);
        

        $return = $result->query()->fetchAll();
        
        return $return;        
    }
    
}
