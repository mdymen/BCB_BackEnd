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

    public function ranking($idRodada) {
        $sql  = "SELECT SUM( rs_points ) AS suma, rs_iduser, rs_round, us_username
        FROM result
        INNER JOIN user ON result.rs_iduser = user.us_id WHERE rs_round = ".$idRodada."
        GROUP BY rs_iduser
        ORDER BY suma DESC";

        $result = $this->db->query($sql)->fetchAll();

        return $result;
    }

}