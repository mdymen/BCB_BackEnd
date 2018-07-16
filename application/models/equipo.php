<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of teams
 *
 * @author Martin Dymenstein
 */
class Application_Model_Equipo extends Application_Model_Bd_Adapter
{

    protected $_name = 'equipo';
    
    public function save($equipo, $pais, $logo) {
        $db = $this->db;
        $info = array(
            'eq_nome'=>$equipo,
            'eq_idpais' => $pais,
            'eq_logo' => $logo
        );       
        $db->insert("equipo", $info);
        return $db->lastInsertId();
    }

    /**
     * retorna toda la lista de equipos cadastrados por pais
     * @param idPais
     */
    public function loadByPais($idPais) {
        return $this->db->select()->from("equipo")
            ->where("eq_idpais = ?", $idPais)
            ->query()
            ->fetchAll();
    }

    /**
     * carga toda la lista de equipos disponibles en el sistema
     */
    public function load() {
        return $this->db->select()->from("equipo")
            ->joinInner("pais", "ps_id = equipo.eq_idpais")
            ->query()
            ->fetchAll();
    }

    /**
     * carga la info del equipo especificado
     * @param idEquipo
     */
    public function loadById($idEquipo) {
        return $this->db->select()->from("equipo")
            ->where("eq_id = ?", $idEquipo)
            ->query()
            ->fetch();
    }

    /**
     * carga todos los campeonatos del equipo
     * @param idEquipo
     */
    public function loadCampeonatos($idEquipo) {
        return $this->db->select()
            ->from("equipocampeonato")
            ->joinInner("championship", "championship.ch_id = equipocampeonato.ec_idchampionship")
            ->where("ec_idequipo = ?", $idEquipo)
            ->query()
            ->fetchAll();
    }

    /**
     * retorna los 3 partidos ultimos registrados sin importar si fueron jugados o no
     * @param idEquipo
     */
    public function loadNextJogos($idEquipo, $limit) {
        return $this->db->select()
            ->from("match")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship", array("ch_id" => "championship.ch_id","ch_nome" => "championship.ch_nome"))
            ->joinInner(array('t1' => 'equipo'), 't1.eq_id = match.mt_idteam1', array('tm1_id' => 't1.eq_id', 't1nome' => 't1.eq_nome', 'tm1_logo' => 't1.eq_logo'))
            ->joinInner(array('t2' => 'equipo'), 't2.eq_id = match.mt_idteam2', array('tm2_id' => 't2.eq_id', 't2nome' => 't2.eq_nome', 'tm2_logo' => 't2.eq_logo'))
            ->where("match.mt_idteam1 = ?", $idEquipo)
            ->orWhere("match.mt_idteam2 =?", $idEquipo)
            ->order(array('match.mt_id DESC'))
            ->limit($limit,0)
            ->query()
            ->fetchAll();
    }

    /**
     * Retorna todos los equipos del pais
     * @param idPais
     */
    public function gettimes($idPais) {
        return $this->db->select()
            ->from("equipo")
            ->where("eq_idpais = ?", $idPais)
            ->query()
            ->fetchAll();
    }
}