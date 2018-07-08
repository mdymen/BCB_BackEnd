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
}