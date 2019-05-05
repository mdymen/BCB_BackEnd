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

    /**
     * Retorna cual es la rodada activa actual para el campeonato
     * @param idChampionship id del campeonato
     */
    public function rodadaActiva($idChampionship) {
        return $this->db->select()->from("round", "min(rd_id) as rd_id")
            ->where("rd_idchampionship = ?", $idChampionship)
            ->where("rd_finalizado = 0")
            ->query()
            ->fetch();
    }

    /**
     * Retorna la ultima rodada finalizada
     * @param idChampionship id del campeonato
     */
    public function ultimaRodadaFinalizada($idChampionship) {
        return $this->db->select()->from("round", "max(rd_id) as rd_id")
            ->where("rd_idchampionship = ?", $idChampionship)
            ->where("rd_finalizado = 1")
            ->query()
            ->fetch();
    }
    

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

    /**
     * @param rodada es el numero de la rodada no el ID
     * @param campeonato es el id del campeonato
     */
    public function rodada($rodada, $campeonato) {

        return $this->db->select()->from("round")
            ->where("rd_round = ?", $rodada)
            ->where("rd_idchampionship = ?", $campeonato)
            ->query()
            ->fetch();
    }

    public function save($rodada) {
        $this->db->insert("round", $rodada);
        return $this->loadById($this->db->lastInsertId());
    }

    public function actualizar($rodada) {
        $this->db->update("round", $rodada, "rd_id = ".$rodada["rd_id"]);
    }


}