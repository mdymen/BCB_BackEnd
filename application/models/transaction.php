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
    
}
