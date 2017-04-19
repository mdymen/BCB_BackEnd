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


class Application_Model_Championships extends Application_Model_Bd_Adapter
{

    protected $_name = 'championship';
    
    public function __construct() {
        parent::__construct(); 
    }
    
    public function save($params) {
        
        $db = $this->db;     
        
        $db->insert($this->_name,$params);
    }
    
	public function getcampeonatos() { 
	    $db = $this->db;
        
        $select = $db->select()->from("championship");
        
        $championships = $select->query()->fetchAll();
        
        return $championships;
	}
	
    public function load_encerrados() {
        
//        $db = Zend_Db_Table::getDefaultAdapter();
        
        $db = $this->db;
        
        $table = $this->_name;
        
        $select = $db->select($table)->from($table)->where("ch_ativo = ?", 0);
        
        $query = $select->query();
        
        $championships = $query->fetchAll();
        
        return $championships;
    }        
        
    public function load() {
        
//        $db = Zend_Db_Table::getDefaultAdapter();
        
        $db = $this->db;
        
        $table = $this->_name;
        
        $select = $db->select($table)->from($table)->where("ch_ativo = ?", 1);
        
        $query = $select->query();
        
        $championships = $query->fetchAll();
        
        return $championships;
    }

    public function ranking($champ) {
        $db = $this->db;
        
        $result = $db->select()->from("ranking")
                ->where("rk_idchamp = ?", $champ)
                ->query()
                ->fetchAll();
                
        
        return $result;
    }
    
    public function setAtualRound($champ, $round) {
        $db = $this->db;
        
        $return = $db->select()->from("vwranking_round", array('max(points) as points'))
                ->where("mt_idchampionship = ?", $champ)
                ->where("rd_id = ?", $round);
        
        $return = $return->query()
                ->fetch();
        
        $points = $return['points'];
        
        $ganadores = $db->select()->from("vwranking_round")
                ->where("mt_idchampionship = ?", $champ)
                ->where("rd_id = ?", $round)
                ->where("points = ?", $points)->query()->fetchAll();
        
        
        $round_aux = $db->select()->from("round")->where("rd_id = ?",$round)->query()->fetch();
        $rd_acumulado = $round_aux['rd_acumulado'];
        
        $total_por_jogador = $rd_acumulado/count($ganadores);
  
        
        for ($i = 0; $i < count($ganadores); $i = $i + 1) {
            $user = $db->select()->from("user", array("us_cash"))->where("us_id = ?", $ganadores[$i]['us_id'])->query()->fetch();
      
            
            $user['us_cash'] = $user['us_cash'] + $total_por_jogador;
            $db->update("user", array('us_cash' => $user['us_cash']), 'us_id = '.$ganadores[$i]['us_id']);
            
            
            $transaction = array('tr_valortransaccion' => $total_por_jogador, 'tr_valorcampeonato' => 0, 'tr_iduser' => $ganadores[$i]['us_id']
                        , 'tr_idcampeonato' => $champ, 'tr_res_us_cash' => $user['us_cash'], 'tr_tipo' =>'CREDITO','tr_date' => date("Y-m-d H:i:s"), 
                        'tr_motivo' => 'RODADA', 'tr_idrodada' => $round);
            $db->insert("transaction", $transaction);
        }
        
        $r = $db->select()->from("round")->where("rd_idchampionship = ?", $champ)->order(array("rd_id ASC"))->query()->fetchAll();
        $prox_round_id = "";
        for ($i = 0; $i < count($r); $i = $i + 1) {
            if (strcmp($r[$i]['rd_id'],$round) == 0) {
                if (isset($r[$i+1]['rd_id'])) {
                    $prox_round_id = $r[$i+1]['rd_round'];
                }
            }
        }
        if (isset($prox_round_id)) {
            $db->update("championship", array('ch_atualround' => $prox_round_id), "ch_id = ".$champ);
        }
    }
    
    public function getChampByRound($rd_id) {
        $db = $this->db;
        $result = $db->select()->from("round")
                ->joinInner('championship', 'round.rd_idchampionship = championship.ch_id')
                ->where("round.rd_id = ?", $rd_id)
                ->query()
                ->fetch();
        
        return $result;
        
    }    
    
    public function getChamp($id) {
        $db = $this->db;
        $result = $db->select()->from("championship")
                ->where("ch_id = ?", $id)
                ->query()
                ->fetch();
        
        return $result;
        
    }
    
    public function getMatchs($champ) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
                ->where("mt_idchampionship = ?", $champ);
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function getTeams($champ) {
        $db = $this->db;
        
        $result = $db->select()->from("team")
                ->where("tm_idchampionship = ?", $champ);
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function getrondas($champ) {
        $db = $this->db;
        
        $result = $db->select()->from("round")
                ->where("rd_idchampionship = ?", $champ)
                ->order("rd_round")
                ->query()
                ->fetchAll();
        
        return $result;
                        
    }
    
    public function salvar_rodada($id_champ, $nome_rodada) {
        $db = $this->db;
        
        $db->insert("round",array("rd_idchampionship" => $id_champ, "rd_round" => $nome_rodada));
    }
    
    
    public function setRondaAtual($id_champ, $id_ronda) {
        $db = $this->db;
        
//        print_r($id_champ);
//        print_r($id_ronda);
//        die(".");
        
        $db->update("championship", array("ch_atualround" => $id_ronda), "ch_id = ".$id_champ);
    }
}