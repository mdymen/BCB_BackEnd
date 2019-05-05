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
class Application_Model_Teams extends Application_Model_Bd_Adapter
{

    protected $_name = 'team';
    
    public function save($params) {
        $db = $this->db;
        $info = array(
            'tm_name'=>$params['tm_name'],
            'tm_idchampionship' =>$params['tm_idchampionship'],
            'tm_logo' => $params['tm_logo'],
            'tm_grupo' => $params['tm_grupo'],
            'tm_points' => 0,
            'tm_played' => 0
        );       
//        print_r($info);
        $db->insert("team", $info);
    }
    
    public function load($championship) {
        

        $db = $this->db;
        
        $teams = $db->select()->from("team")
          ->where("tm_idchampionship = ?",$championship)
          ->query()->fetchAll();

        return $teams;
    }
        
    public function load_penca_limit($championship, $limit) {
        $db = $this->db;
        
        $result = $db->select()->from('team')
                ->where('team.tm_idchampionship = ?', $championship)
                ->limit(4, ($limit-1)*4)
                ->order("team.tm_points DESC")    
                ->query()
                ->fetchAll();

        return $result;        
    }
    
    public function load_teams_championship($champ) {
        $db = $this->db;
        
        $result = $db->select()->from('equipo')
                ->joinInner("equipocampeonato", "equipocampeonato.ec_idequipo = equipo.eq_id")
                ->where('equipocampeonato.ec_idchampionship = ?', $champ)
                ->order(array("equipocampeonato.ec_grupo", "equipocampeonato.ec_pontos DESC"))
                ->query()
                ->fetchAll();
                
        return $result;  
    }
    
    public function load_teams_para_jogo($champ) {
        $db = $this->db;
        
        $result = $db->select()->from('team')
                ->where('team.tm_idchampionship = ?', $champ)
                ->order(array("team.tm_name ASC"))    
                ->query()
                ->fetchAll();

        return $result;  
    }
    
    public function sum_points($team, $points) {
        $db = $this->db;
        
        $result = $db->select()->from("team", array('tm_points','tm_played'))
                ->where("tm_id = ?", $team)->query()->fetch();
        
        $db->update("team", array('tm_points' => $result['tm_points'] + $points, 'tm_played' => $result['tm_played'] + 1), "tm_id = ".$team);
        
    }
    
    public function sum_match($team) {
        $db = $this->db;
        
        $result = $db->select()->from("team", array('tm_played'))
                ->where("team.tm_id = ?", $team)->query()->fetch();
        
        $db->update("team", array('tm_played' => $result['tm_played'] + 1), "tm_id = ".$team);
    }
	
    public function load_palpites_simples_porteam($championship, $team_id, $usuario) {
         $db = $this->db;
        
        $result = $db->select()->from("vwmatchsresult")
                ->where("rs_iduser= ".$usuario." or rs_iduser is NULL ")
                ->where("mt_idchampionship = ?", $championship)
                ->where("tm1_id = ".$team_id." or tm2_id = ".$team_id)
                ->order(array('mt_idround ASC','mt_date ASC'));
        
       // print_r($result->__toString());
        // die(".");
        
        $return = $result
                ->query()
                ->fetchAll();
        
        
        return $return;       
    }	
    
    public function getJogosTeam($team, $champ) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
            ->joinInner("round", "match.mt_idround = round.rd_id")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship and championship.ch_id = ".$champ)
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' => 't1.tm_id', 't1nome' => 't1.tm_name', 'tm1_logo' => 't1.tm_logo'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' => 't2.tm_id', 't2nome' => 't2.tm_name', 'tm2_logo' => 't2.tm_logo'))
            ->where("t1.tm_id = ?", $team)
            ->orWhere("t2.tm_id = ?", $team)
            ->order(array("mt_idround ASC"))    
            ->query()
            ->fetchAll();
        
        return $result;
    }

    /**
     * Retorna la cantidad de partidos jugados del equipo
     * y del campeonato especificado
     * @param idEquipo 
     * @param idCampeonato
     */
    public function partidosJugados($idEquipo, $idCampeonato) {
        return $this->db->select()->from("match","count(*) as jugados")
            ->where("match.mt_idchampionship = ?", $idCampeonato)
            ->where("match.mt_played = 1")
            ->where("match.mt_idteam1 = ".$idEquipo." or match.mt_idteam2 = ".$idEquipo)
            ->query()
            ->fetch();
    }
}
