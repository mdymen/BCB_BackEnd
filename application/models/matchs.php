<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of matchs
 *
 * @author Martin Dymenstein
 */
//include APPLICATION_PATH."/helpers/data.php";

class Application_Model_Matchs extends Application_Model_Bd_Adapter
{

    protected $_name = 'match';
    
    public function save($params) {
        
        $info = array(
            'mt_idteam1'=>$params['team1'],
            'mt_idteam2'=>$params['team2'],
            'mt_date'=>$params['date'],
            'mt_idchampionship' => $params['championship'],
            'mt_idround' => $params['round']
        );       
        
        $db = $this->db;       
                  
        $db->insert("match",$info);        
    }
    
    public function update_acumulado_match($match_id, $valor) {
        $db = $this->db;
        
        $result = $db->select()->from("match", array("mt_acumulado"))
                ->where("mt_id = ?", $match_id)->query()->fetch();
        
        $din = $result['mt_acumulado'];
        
        $din = floatval($valor) + $din;
        
        $db->update("match", array("mt_acumulado" => $din), "mt_id = ".$match_id);
        
        return $din;
    }
        
    public function load($championship) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
                ->where("mt_idchampionship = ?", $championship)
                ->query()
                ->fetchAll();
        
        return $result;
        
    }
    
    public function load_palpites_simples($championship, $rodada, $usuario) {
         $db = $this->db;
        
        $result = $db->select()->from("vwmatchsresult")
                ->where("rs_iduser= ?", $usuario)
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_idround = ?", $rodada)
                ->order(array('mt_idround ASC','mt_date ASC'));
        
//        print_r($result->__toString());
//        die(".");
        
        $return = $result
                ->query()
                ->fetchAll();
        
        
        return $return;       
    }
    
//    public function load_palpites_simples($championship, $rodada, $usuario) {
//         $db = Zend_Db_Table::getDefaultAdapter();
//        
//        $result = $db->select()->from("match")
//                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name'))
//                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name'))
//                ->joinRight("result", "match.mt_id = result.rs_idmatch")
//                ->where("match.mt_idchampionship = ?", $championship)
//                ->where("match.mt_round = ?", $rodada)
//                //->where("result.rs_id <> '' " )
//                ->query()
//                ->fetchAll();
//        
//        
//        return $result;       
//    }
    
    public function load_matchs($championship, $rodada) {
        $db = $this->db;
        
        $result = $db->select()->from("vwmatchsteams")
                ->distinct()
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_idround = ?", $rodada)
                ->order(array('mt_date ASC'));
        
//        print_r($result->__toString());
        
        $return = $result->query()
                ->fetchAll();

        return $return;
    }
    
    public function load_rodada($championship, $rodada) {
        $db = $this->db;
        
        $result = $db->select()->from("vwmatchsresult")
                ->distinct()
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_round = ?", $rodada)
                ->order(array('mt_date ASC'));
        
//        print_r($result->__toString());
        
        $return = $result->query()
                ->fetchAll();

        return $return;
        
    }
    
    public function proximos_jogos($us_id, $limit) { 
        
        $db = $this->db;
        
        $sql = "SELECT vwpalpites.*, `match`.*, t1.tm_id as tm1_id, t1.tm_name as t1nome, t1.tm_logo as tm1_logo,  `t1`.*, t2.tm_id as tm2_id, 
            t2.tm_name as t2nome, t2.tm_logo as tm2_logo,
            `t2`.*, r.*, round.*,c.* FROM `match` 
  INNER JOIN `team` AS `t1` ON t1.tm_id = match.mt_idteam1 
  INNER JOIN `team` AS `t2` ON t2.tm_id = match.mt_idteam2   
  INNER JOIN championship c ON c.ch_id = mt_idchampionship
  LEFT JOIN vwpalpites ON vwpalpites.rs_idmatch = match.mt_id
  LEFT JOIN round ON round.rd_id = match.mt_idround
  LEFT JOIN (select * from result where rs_iduser = ".$us_id.") r ON r.rs_idmatch = match.mt_id WHERE mt_played <> 1 ORDER BY `mt_date` ASC limit ".$limit;
        
//        print_r($sql);
        
        $result = $db->query($sql)->fetchAll();
        
        return $result;
        
    }
    
   
    
    public function load_rodada_com_palpites($championship, $rodada, $usuario) {
        $db = $this->db;
        
        $sql = "SELECT match.mt_idchampionship as ch_id, vwpalpites.*, `match`.*, t1.tm_id as tm1_id, t1.tm_name as t1nome, t1.tm_logo as tm1_logo,  `t1`.*, t2.tm_id as tm2_id, 
            t2.tm_name as t2nome, t2.tm_logo as tm2_logo,
            `t2`.*, r.*, round.*  FROM `match` 
  INNER JOIN `team` AS `t1` ON t1.tm_id = match.mt_idteam1 
  INNER JOIN `team` AS `t2` ON t2.tm_id = match.mt_idteam2   
  LEFT JOIN vwpalpites ON vwpalpites.rs_idmatch = match.mt_id
  LEFT JOIN round ON round.rd_id = match.mt_idround
  LEFT JOIN (select * from result where rs_iduser = ".$usuario.") r ON r.rs_idmatch = match.mt_id 
  WHERE (match.mt_idchampionship = '".$championship."') AND (mt_idround = '".$rodada."') ORDER BY `mt_date` ASC";
        
//        print_r($sql);
        
        $result = $db->query($sql)->fetchAll();
//        
//        
//        
//        print_r("SELECT `match`.*, t1.tm_id as tm1_id, t1.tm_nome as t1nome, t1.tm_logo as tm1_logo,  `t1`.*, `t2`.*, r.*  FROM `match` 
//  INNER JOIN `team` AS `t1` ON t1.tm_id = match.mt_idteam1 
//  INNER JOIN `team` AS `t2` ON t2.tm_id = match.mt_idteam2   
//  LEFT JOIN (select * from result where rs_iduser = ".$usuario.") r ON r.rs_idmatch = match.mt_id 
//  WHERE (match.mt_idchampionship = '".$championship."') AND (mt_round = '".$rodada."') ORDER BY `mt_date` ASC");
        
//        $result = $db->select()->from("result")
//                ->where("rs_iduser = ?", $usuario)
//                ->query();
//        
//        $result1 = $db->select()->from("match")
//                ->joinInner(array('t1' => 'team'),"t1.tm_id = match.mt_idteam1")
//                ->joinInner(array('t2' => 'team'),"t2.tm_id = match.mt_idteam2")
//                ->
//        
//        $result = $db->select()->from("match")
//                ->joinInner(array('t1' => 'team'),"t1.tm_id = match.mt_idteam1")
//                ->joinInner(array('t2' => 'team'),"t2.tm_id = match.mt_idteam2")
//                ->joinRight("result", "match.mt_id = result.rs_idmatch")
//                ->where("match.mt_idchampionship = ?", $championship)
//                ->where("mt_round = ?", $rodada)
//                ->where("rs_iduser = ?",$usuario)
//                ->orWhere("rs_iduser = null")
//                ->order(array('mt_date ASC'))
//                ->__toString();
////                ->query()->fetchAll();
        
//        print_r($result);
//        die(".");
        
        //        ->fetchAll();
//        
//                ->where("match.mt_round = ?", $rodada)
//        
//        $result = $db->select()->from("vwmatchsresult")
//                ->joinLeft("user", "user.us_id = vwmatchsresult.rs_iduser AND user.us_id = ".$usuario)
//                ->where("mt_idchampionship = ?", $championship)
//                ->where("mt_round = ?", $rodada)
//               // ->where("rs_iduser is null OR rs_iduser = ".$usuario)
//                ->order(array('mt_date ASC'))
//                ->query()
//                ->fetchAll();

//        print_r($result);
//        die("..");
        return $result;
        
    }
    
    public function load_rodada_porteam($championship, $team, $usuario) {
        $db = $this->db;
        
        $result = $db->select()->from("vwmatchsresult")
//                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name'))
//                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name'))
//                ->joinLeft("result", "match.mt_id = result.rs_idmatch and result.rs_iduser = ".$usuario)
//                ->where("match.mt_idchampionship = ?", $championship)
//                ->where("match.mt_idteam1 = ?", $team)
//                ->orWhere("match.mt_idteam2 = ?",$team)
                ->where("rs_iduser = ? ",$usuario)
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_idteam1 = ?", $team)
                ->orWhere("mt_idteam2 = ?",$team)
                //->where("result.rs_id is null " )
                //->where("result.rs_iduser = ?", $usuario)
                ->query()
                ->fetchAll();
        
        return $result;
        
    }    
    
    public function load_porteam($championship, $team, $usuario) {
        $db = $this->db;
        
        $result = $db->select()->from("vwmatchsresult")
                ->where("rs_iduser = ?", $usuario)
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_idteam1 = ?", $team)
                ->orWhere("mt_idteam2 = ?", $team)
//                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name'))
//                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name'))
//                ->joinInner("result", "match.mt_id = result.rs_idmatch and result.rs_iduser =".$usuario)
//                ->where("match.mt_idchampionship = ?", $championship)
//                ->where("match.mt_idteam1 = ?", $team)
//                ->orWhere("match.mt_idteam2 = ?", $team)
                 ->query()
                ->fetchAll();
        
//        print_r($result->__toString());
//        die(".");
               
        
        return $result;
        
    }
    
    public function save_penca_match($dados) {
        $db = $this->db;
        
//        print_r($dados);
//        die(".");
        
        $d = array(
            'rs_idmatch' => $dados['idmatch'],
            'rs_idpenca' => $dados['idpenca'],
            'rs_iduser' => $dados['iduser'],
            'rs_res1' => 0,
            'rs_res2' => 0,
            'rs_date' => $dados['date'],
            'rs_round' => $dados['round']
        );
        
        $db->insert("result", $d);
    }
    
    public function submeter_result($user_id, $result1, $result2, $match_id, $round) {
        $db = $this->db;
        
        $dados = array(
            'rs_idmatch' => $match_id,
            'rs_res1' => $result1,
            'rs_res2' => $result2,
            'rs_iduser' => $user_id,
            'rs_round' => $round
                
        );
        
        $db->insert("result", $dados);
        return $db->lastInsertId();
    }
	
	public function alterar_result($user_id, $result1, $result2, $match_id, $round) {
		$db = $this->db;
		
		$dados = array();
		
		$db->update("result", array(
			'rs_res1' => $result1,
            'rs_res2' => $result2,
            'rs_round' => $round), "rs_iduser = ".$user_id." and rs_idmatch = ".$match_id);
		
		
	}
    
    public function load_resultados_palpitados($match) {
        $db = $this->db;
        
        $result = $db->select()->from("result")
                ->where("rs_idmatch = ?", $match)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function delete_palpite($result) {
        $db = $this->db;
        
        $db->delete("result", "rs_id = ".$result);
    }
    
    public function result($id) {
        $db = $this->db;
        
        $result = $db->select()->from("result")
                ->joinInner('match','result.rs_idmatch = match.mt_id')
                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
                ->where('result.rs_id = ?', $id)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function rondas($champ) {
        $db = $this->db;
        $result = $db->select()->from('match', array('count(distinct mt_round) as rounds'))
                ->where("mt_idchampionship", $champ)
                ->query()
                ->fetch();

        return $result;
        
    }
    
    public function getrondas($champ) {
        $db = $this->db;
        
        $result = $db->select()->from("match", 'mt_idround')
                ->distinct()
                ->joinInner('round','match.mt_idround = round.rd_id')
                ->where("mt_idchampionship = ?", $champ)
                ->order("mt_idround")
                ->query()
                ->fetchAll();
        
        return $result;
                        
    }
    
    public function getusuarios_do_campeonato($champ) {
        $db = $this->db;
        
        $return = $db->select()->from("vwmatchsresult","")
                ->distinct()
                ->joinInner("user","user.us_id = vwmatchsresult.rs_iduser",array("us_id","us_username"))
                ->where("vwmatchsresult.mt_idchampionship = ?", $champ);
        
        $result = $return->query()->fetchAll();
        
        return $result;
    }
    
    public function load_matchs_byrodada($champ, $rodada) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))
            ->where('mt_idchampionship = ?', $champ)
            ->where("mt_idround = ?",$rodada)
            ->query()
            ->fetchAll();
        
        return $result;
        
    }
    
    public function setDatas($matchs) {
        $data = new Helpers_Data();
        for ($i = 0; $i < count($matchs); $i = $i + 1) {
            $matchs[$i]['mt_date'] = $data->format($matchs[$i]['mt_date']);
        }
        return $matchs;
    }
    
    public function load_all_matchs($champ) {  
        $db = $this->db;
        
        $result = $db->select()->from("match")  
            ->joinInner("round", "round.rd_id = match.mt_idround")
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'))
            ->where('mt_idchampionship = ?', $champ)
            ->order('mt_idround','mt_date')
            ->query()
            ->fetchAll();
        
        return $result;
    }
    
    public function result_matchs($match) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
                ->joinInner("round", "match.mt_idround = round.rd_id")
                ->where("mt_id = ?", $match)
                ->query()->fetchAll();
        
        return $result;
    }
    
    public function getpalpites($match) {
        $db = $this->db;
        
        $result = $db->select()->from("vwpalpites")
                ->where("rs_idmatch = ?", $match);
        
        return $result->query()->fetch();
    }
    
    public function get_quantidade_palpites($match) {
        $db = $this->db;
        
        $result = $db->select()->from("result",array("count(*) as quantidade", "rs_res1","rs_res2", "rs_idmatch","rs_result"))
                ->joinInner("match","match.mt_id = result.rs_idmatch",array("mt_idteam1", "mt_idteam2"))
                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' =>'t1.tm_id', 'tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' =>'t2.tm_id','tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'))
                ->joinInner("round", "match.mt_idround = round.rd_id")
                ->where("rs_idmatch = ?",$match)
                ->group(array("rs_res1", "rs_res2", "rs_idmatch","mt_idteam1", "mt_idteam2","rs_result"));
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function load_match($id_match) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
                ->joinInner("round","round.rd_id = match.mt_idround")
                ->joinInner("championship", "championship.ch_id = round.rd_idchampionship")
                ->where("match.mt_id = ?", $id_match);
        
        $return = $result->query()->fetch();
        
        return $return;
    }
    
    public function atualizar_match($params) {
        $info = array(
            'mt_idteam1' => $params['mt_idteam1'],
            'mt_idteam2' => $params['mt_idteam2'],
            'mt_date' => $params['mt_date'],
            'mt_idround' => $params['mt_idround']
        );
        
        $db = $this->db;
        
        $db->update("match", $info, "mt_id = ".$params['mt_id']);
        
    }
	
	public function match_ja_palpitado($id_match, $id_user) {
		$db = $this->db;
		
		$result = $db->select()->from("result")
			->where("rs_idmatch = ?", $id_match)
			->where("rs_iduser = ?", $id_user);
			
		$return = $result->query()->fetch();
		
		if (empty($return)) {
			return false;
		}
		return true;
	}
    
    public function load_by_date($date_ini, $date_fim) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
                ->joinInner("round", "match.mt_idround = round.rd_id")
                ->joinInner("championship", "championship.ch_id = round.rd_idchampionship")
                 ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' =>'t1.tm_id', 'tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' =>'t2.tm_id','tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'));
        
        if (!empty($date_ini)){
            $result = $result->where("match.mt_date >= ?", $date_ini);
        }
        
        if (!empty($date_fim)) {
            $result = $result->where("match.mt_date <= ?", $date_fim);
        }        
        
        $return = $result->query()->fetchAll();
        
        return $return;
                
    }
   
    
}
