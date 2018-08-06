<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of championships
 *
 * @author Martin Dymenstein
 */


class Application_Model_Rodada extends Application_Model_Bd_Adapter
{

    protected $_name = 'round';

    public function loadById($idRodada) {
        return $this->db->select()->from($this->_name)
            ->where($this->_name.".rd_id = ?", $idRodada)
            ->query()
            ->fetch();
    }

}