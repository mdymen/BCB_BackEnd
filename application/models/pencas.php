<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of penca
 *
 * @author Martin Dymenstein
 */

class Application_Model_Penca extends Application_Model_Bd_Adapter
{

    protected $_name = 'penca';
    
    public function save($params) {
        $info = array(
            'pn_name'=>$params['pn_name'],
            'pn_value'=>$params['pn_value'],
            'pn_iduser'=>$params['pn_iduser'],
            'pn_idchampionship' =>$params['tm_idchampionship'],
            'pn_justfriends' => $params['pn_justfriends'],
            'pn_password' => $params['pn_password']
        );       
        
        $this->insert($info);
    }
        
    public function save_userpenca($params) {
        $db = $this->db;
        
        $info = array(
            'up_idpenca' => $params['up_idpenca'],
            'up_iduser' => $params['up_iduser'],
        );
        
        $db->insert("user_penca", $info);
    }
    
    public function load_penca__puntagem_usuario($id_user) {
        $db = $this->db;
        
        return $db->select()->from("user_penca")
                ->joinInner("penca", "user_penca.up_idpenca = penca.pn_id")
                ->joinInner("championship", "penca.pn_idchampionship = championship.ch_id")
                ->where("up_iduser = ?", $id_user)
                 ->query()->fetchAll();
    }
    
    public function load_penca_users($penca) {
        $db = $this->db;
        
        return $db->select()->from("user_penca")
                ->joinInner("user","user_penca.up_iduser = user.us_id")
                ->where("up_idpenca = ?", $penca)
                ->order("up_puntagem desc")
                ->query()->fetchAll();
    }
    
    public function load_pencas() {
        $db = $this->db;
        
        $return = $db->select()->from("penca")
                ->joinInner("championship", "penca.pn_idchampionship = championship.ch_id")
                ->query()->fetchAll();
        
        return $return;
    }
    
    public function load_pencas_usuario($id_usuario) {
        $db = $this->db;

        $return = $db->select()->from("penca") 
                ->joinInner("championship", "penca.pn_idchampionship = championship.ch_id")
                ->where("pn_iduser = ?", $id_usuario);
              //  ->query();
        

//                ->fetchAll();
//        
        return $return;
    }
    
    public function load_participantes($penca) {
        $db = $this->db;
        
        $result = $db->select()->from('user_penca', array('count(*) as participantes'))
                ->where('user_penca.up_idpenca = ?', $penca)->query()->fetchAll();
        
        return $result;
    }
    
    public function load_penca($penca) {
        $db = $this->db;
        
        $result = $db->select()->from('penca')
                ->joinInner('championship','penca.pn_idchampionship = championship.ch_id')
                ->joinInner('user','penca.pn_iduser = user.us_id')
                ->where('penca.pn_id = ?', $penca)
                ->query()
                ->fetchAll();
        
        return $result;

    }
    
    public function update_cash_usuario($id_us, $valor) {
        $db = $this->db;
        
        $result = $db->select()->from("user", array("us_cash"))
                ->where("us_id = ?", $id_us)->query()->fetch();
        
        $din = $result['us_cash'];
        
        $din = floatval($valor) + $din;
        
        $db->update("user", array("us_cash" => $din), "us_id = ".$id_us);
        
        return $din;
    }
    
    public function load_championship_with_results($id_user) {
        $db = $this->db;
        
        $result = $db->select()->distinct()->from("result", array('championship.ch_nome', 'championship.ch_id'))
                ->joinInner('match','match.mt_id = result.rs_idmatch',"")
                ->joinInner('championship', 'match.mt_idchampionship = championship.ch_id',"")
                ->where('result.rs_iduser = ?',$id_user)
                ->query()
                ->fetchAll();
        
        return $result;
        
    }
    
    public function load_usuarios($id_penca) {
        $db = $this->db;
        
        $result = $db->select()->from("user_penca")
                ->joinInner('user', 'user_penca.up_iduser = user.us_id')
                ->where('user_penca.up_idpenca = ?', $id_penca)
                ->query()
                ->fetchAll();

        return $result;
    }
    
    public function palpites($penca, $round, $usuario) {
        $db = $this->db;
        
        $result = $db->select()->from("result")
                ->joinInner('match','result.rs_idmatch = match.mt_id')
                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
                ->where('result.rs_idpenca = ?', $penca)
                ->where('result.rs_round = ?', $round)
                ->where('result.rs_iduser = ?', $usuario)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function rodada($idcham, $round) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
                ->where('mt_round = ?', $round)
                ->where('mt_idchampionship = ?', $idcham)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function load_pencas_byChamp($id_championship) {
        $db = $this->db;
        
        $result = $db->select()->from("penca")
                ->joinInner("championship", 'championship.ch_id = penca.pn_idchampionship')
                ->where("championship.ch_id = ?", $id_championship)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function load_pencas_incripto_usuario($usuario) {
        $db = $this->db;
        
        $result = $db->select()->from("user_penca")
            ->joinInner("penca", 'user_penca.up_idpenca = penca.pn_id')
             ->joinInner("championship", 'championship.ch_id = penca.pn_idchampionship')
            ->where("user_penca.up_iduser = ?", $usuario)
                ->query()
                ->fetchAll();
        
        return $result;
                    
    }
    
    public function sair_penca($usuario, $penca) {
        $db = $this->db;
        
        $db->delete("user_penca",'up_iduser = '.$usuario.' and up_idpenca = '.$penca);
        $db->delete("result", 'rs_iduser = '.$usuario.' and rs_idpenca = '.$penca);
    }
    
    public function isIscriptoEmPenca($user, $penca) {
        $db = $this->db;
        
        $result = $db->select()->from("user_penca")
                ->where("up_idpenca = ? ", $penca)
                ->where("up_iduser = ? ", $user)
                ->query()
                ->fetch();
        return !empty($result);
    }
    
    public function getpalpites($user) {
        $db = $this->db;
        
        $result = $db->select()->from("result")
                ->joinInner('match','result.rs_idmatch = match.mt_id')
                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
                ->where('result.rs_iduser = ?', $user)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function primera_rodada_disponible($champ) {
        $db = $this->db;
        
        $sql = "select * from penca.match where mt_played is false and mt_round in (select min(mt_round) "
                . "as mt_round from "
                . "penca.match  where mt_played is false) and mt_idchampionship = ".$champ;
 
        $stmt = $db->query($sql);
         
        return $stmt;
    }
    
    public function getIdPrimeraRodadaDisponivel($champ) {
        $db = $this->db;
        
//        $sql_before = $db->select()->from("match",array("min(mt_round) as mt_round"))
//                ->where("mt_played is false")
//                ->where("mt_idchampionship = ?",$champ)
//                ->query()
//                ->fetchAll();
//        
//        $sql = $db->select()->from("match", array("match.mt_round"))
//                ->distinct()
//                ->where("mt_played is false")
//                ->where("mt_round IN(?)", $sql_before);
        
//        $sql = "select distinct mt_round from penca.match where mt_played is false and mt_round in "
//                . "(select min(mt_round) as mt_round from penca.match  where mt_played is false) "
//                . "and mt_idchampionship = ".$champ;
        
//        $stmt = $db->query($sql)->fetchAll();
//        
//        $stmt = $stmt[0]['mt_round'];
        
        $result = $db->select()->from("championship", array("ch_atualround as round"))
                ->where("ch_id = ?", $champ);
        
//        print_r($result->__toString());
//        die(".");
        
        $return = $result->query()->fetch();
 
        return $return['round'];
    }
    
    public function update_acumulado_rodada($rodada, $campeonato, $dinhero) {
        $db = $this->db;
        $db->beginTransaction();
        try {
            $d = $db->select()
                    ->from("round",array("rd_acumulado"))
                    ->where("rd_round = ?", $rodada)
                    ->where("rd_idchampionship = ?", $campeonato)
                    ->query()
                    ->fetch();
            
            $d = $d['rd_acumulado'];
            
//            print_r($d);
//            die(",");
            
            
            $d = floatval($d) + $dinhero;
            
//            print_r($d);
//            die(".");
            
            $db->update("round", array("rd_acumulado" => $d), "rd_round = ".$rodada." and rd_idchampionship = ".$campeonato);
                        
            $db->commit();
            
            //$d = number_format((float)$d, 2, '.', '');
            
            return $d;
            
        } catch (Exception $ex) {
            $db->rollBack();
            
        }
    }
    
    public function setMatch($dpalpite, $dchamp, $s_ch_id, $s_us_id, $drodada, $rd_id, $djogo, $s_mt_id, $rs_id) {
        
        $db = $this->db;
        
        $sql = "call update_palpites(".$dpalpite.",".$dchamp.",".$s_ch_id.",".$s_us_id.",".$drodada.",".$rd_id.", ".$djogo.",".$s_mt_id.", ".$rs_id.")";
        
//        print_r($sql);
//        die(".");
//        
        
        $result = $db->query($sql)->fetch();
        
        return $result;
    }
    

}