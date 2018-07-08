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
class Application_Model_Pais extends Application_Model_Bd_Adapter
{

    protected $_name = 'pais';
    
    public function save($pais) {
        $db = $this->db;
        $info = array(
            'ps_nome'=>$pais
        );       
        $db->insert("pais", $info);
        return $db->lastInsertId();
    }

    public function load() {
        return $this->db->select()
            ->from("pais")
            ->query()
            ->fetchAll();
    }

}