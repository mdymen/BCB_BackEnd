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

class Application_Model_Transaction extends Application_Model_Bd_Adapter
{
    protected $_name = 'transaction';
    
    public function save_transaction($valortransaccion, $valorcampeonato, $iduser, $valorrodada, $valorjogo,  $idcampeonato, $idmatch ) {
        
        $db = $this->db;
        
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
    
    public function getCampeonato($id_champ, $usuario, $rodada, $tipo = null) {
        $db = $this->db;
        
        $return = $db->select()->from("vwmatchsteams")
                ->distinct()
                ->joinRight("transaction","vwmatchsteams.mt_id = transaction.tr_idmatch")
                ->joinInner("championship","championship.ch_id = transaction.tr_idcampeonato")
                ->joinInner("user","user.us_id = transaction.tr_iduser");
        
        
        if (!empty($id_champ)) {
            $return = $return->where("transaction.tr_idcampeonato = ?", $id_champ);
        }
        
        if (!empty($usuario)) {
            $return = $return->where("transaction.tr_iduser = ?", $usuario);
        }
        if (!empty($rodada)) {
            $return = $return->where("vwmatchsteams.mt_idround = ?", $rodada);
        }
        
        if (isset($tipo)) {
            $return = $return->where("transaction.tr_tipo = ?", $tipo);
        }
        
        $return = $return->order("championship.ch_id")
                ->order("vwmatchsteams.mt_idround")
                ->order("transaction.tr_id");
        
//        print_r($return->__toString()."  - - --");
        
        
        $result = $return->query()->fetchAll();
        
        return $result;
    }
    
    
    public function getRondaCampeonato($id_champ, $rodada) {
        $db = $this->db;
        
        $result = $db->select()->from($this->_name)
                ->innerJoin("match","match.mt_id = transaction.tr_idmatch")
                ->where("tr_idcampeonato = ?", $id_champ)
                ->where("match.mt_round = ?", $rodada);
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function getUsuario($us_id) {
        $db = $this->db;
        
        $result = $db->select()->from($this->_name)
                ->where("tr_iduser = ?", $us_id);
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function getCampeonatoRodadaUsuario($camp, $rodada, $us) {
        $db = $this->db;
        
        $result = $db->select()->from("transaction")
                ->innerJoin("match", "match.mt_id = transaction.tr_idmatch")
                ->where("match.mt_round = ?", $rodada)
                ->where("tr_idcampeonato = ?", $camp)
                ->where("transaction.tr_iduser = ?",$us);
        

        $return = $result->query()->fetchAll();
        
        return $return;        
    }
    
}
