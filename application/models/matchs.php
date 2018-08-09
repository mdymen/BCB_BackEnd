<?php
/**
 * Es el modelo con todas las responsabilidades
 * de la tabla de partidos (match) del sistema
 *
 * @author Martin Dymenstein
 */
class Application_Model_Matchs extends Application_Model_Bd_Adapter
{

    protected $_name = 'match';
    
    /**
     * Actualiza el partido con los datos,
     * si el partido no existe, entonces lo crea.
     * @param mt_id Es el id del partido. Si es nulo entonces el partido es creado como nuevo
     * @param mt_idteam1
     * @param mt_idteam2
     * @param mt_date
     * @param mt_idchampionship
     * @param mt_goal1 Si es nulo entonces el partido aun no fue jugado
     * @param mt_goal2 Si es nulo entonces el partido aun no fue jugado
     * @param mt_played 0 si el partido no fue jugado. 1 si el partido ya fue jugado
     * 
     * @return id del partido
     */
    public function save($match) {
        
        $db = $this->db;       

        if(empty($match['mt_id'])) {
            $db->insert("match", $match);
            return $db->lastInsertId();
        } else {
            $db->update("match", $match, "mt_id = ".$match['mt_id']);
            return $match['mt_id'];
        }
                  
        
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
                ->where("rs_idpenca = 0")
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
    
    public function palpites_usuario($us_id, $limit) { 
        
        $db = $this->db;
        
        $sql = "SELECT `match`.*, t1.tm_id as tm1_id, t1.tm_name as t1nome, t1.tm_logo as tm1_logo,  `t1`.*, t2.tm_id as tm2_id, 
            t2.tm_name as t2nome, t2.tm_logo as tm2_logo,
            `t2`.*, r.*, round.*,c.* FROM `match` 
  INNER JOIN `team` AS `t1` ON t1.tm_id = match.mt_idteam1 
  INNER JOIN `team` AS `t2` ON t2.tm_id = match.mt_idteam2   
  INNER JOIN championship c ON c.ch_id = mt_idchampionship
  INNER JOIN round ON round.rd_id = match.mt_idround
  INNER JOIN (select * from result where rs_iduser = ".$us_id.") r ON r.rs_idmatch = match.mt_id ORDER BY `mt_id` DESC limit ".$limit;
        

        
//        print_r($sql);
        
        $result = $db->query($sql)->fetchAll();
        
        return $result;
        
    }

    public function proximos_jogos_offset($us_id, $offset, $limit) { 
        
        $db = $this->db;
        
        $sql = "SELECT vwpalpites.*, `match`.*, t1.tm_id as tm1_id, t1.tm_name as t1nome, t1.tm_logo as tm1_logo,  `t1`.*, t2.tm_id as tm2_id, 
            t2.tm_name as t2nome, t2.tm_logo as tm2_logo,
            `t2`.*, r.*, round.*,c.* FROM `match` 
  INNER JOIN `team` AS `t1` ON t1.tm_id = match.mt_idteam1 
  INNER JOIN `team` AS `t2` ON t2.tm_id = match.mt_idteam2   
  INNER JOIN championship c ON c.ch_id = mt_idchampionship
  LEFT JOIN vwpalpites ON vwpalpites.rs_idmatch = match.mt_id
  LEFT JOIN round ON round.rd_id = match.mt_idround
  LEFT JOIN (select * from result where rs_iduser = ".$us_id.") r ON r.rs_idmatch = match.mt_id WHERE mt_played <> 1 ORDER BY `mt_date` ASC limit ".$offset.",".$limit;
//                print_r($sql);
//        die(".");
//        print_r($sql);
        
        $result = $db->query($sql)->fetchAll();
        
        return $result;
        
    }    
   
    public function load_rodada_com_palpites_penca($championship, $rodada, $usuario, $idpenca) {
        $db = $this->db;
        
        $sql = "SELECT match.mt_idchampionship as ch_id, vwpalpites.*, `match`.*, t1.tm_id as tm1_id, t1.tm_name as t1nome, t1.tm_logo as tm1_logo,  `t1`.*, t2.tm_id as tm2_id, 
            t2.tm_name as t2nome, t2.tm_logo as tm2_logo,
            `t2`.*, r.*, round.*  FROM `match` 
  INNER JOIN `team` AS `t1` ON t1.tm_id = match.mt_idteam1 
  INNER JOIN `team` AS `t2` ON t2.tm_id = match.mt_idteam2   
  LEFT JOIN vwpalpites ON vwpalpites.rs_idmatch = match.mt_id
  LEFT JOIN round ON round.rd_id = match.mt_idround  
  LEFT JOIN (select * from result where rs_iduser = ".$usuario." and rs_idpenca = ".$idpenca.") r ON r.rs_idmatch = match.mt_id 
  WHERE (match.mt_idchampionship = '".$championship."') AND (mt_idround = '".$rodada."') ORDER BY `mt_date` ASC";
        
        $result = $db->query($sql)->fetchAll();

        return $result;
        
    }    

    /**
     * Devuelve toda la informacion de la rodada y del usuario relacionado con la rodada
     * @param idrodada
     * @param iduser
     */
    public function getRodada($idrodada, $iduser) {
        $db = $this->db;

        $sql = "SELECT * FROM round LEFT JOIN 
            (SELECT * FROM rodadausuario WHERE rodadausuario.ru_iduser = ".$iduser.") ru
            ON ru.ru_idrodada = round.rd_id WHERE round.rd_id = ".$idrodada;

        return $db->query($sql)->fetchAll();

        
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
  LEFT JOIN (select * from result where rs_iduser = ".$usuario." AND rs_idpenca = 0) r ON r.rs_idmatch = match.mt_id 
  WHERE  (match.mt_idchampionship = '".$championship."') AND (mt_idround = '".$rodada."') ORDER BY `mt_date` ASC";
        
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
                ->where("rs_iduser = ? ",$usuario)
                ->where("rs_idpenca = 0")
                ->where("mt_idchampionship = ?", $championship)
                ->where("mt_idteam1 = ?", $team)
                ->orWhere("mt_idteam2 = ?",$team)
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
                 ->query()
                ->fetchAll();
        return $result;
        
    }
    
    public function save_penca_match($dados) {
        $db = $this->db;
        $d = array(
            'rs_idmatch' => $dados['rs_idmatch'],
            'rs_idpenca' => $dados['rs_idpenca'],
            'rs_iduser' => $dados['rs_iduser'],
            'rs_res1' => $dados['rs_res1'],
            'rs_res2' => $dados['rs_res2'],
            'rs_date' => $dados['rs_date'],
            'rs_round' => $dados['rs_round']
        );
        
        $db->insert("result", $d);
        
        return $db->lastInsertId();
    }
    
    public function update_penca_match($dados, $rs_idmatch, $rs_iduser, $rs_idpenca ) {
        $db = $this->db;
        
        $db->update("result", $dados, "rs_idmatch = ".$rs_idmatch.""
                . " and rs_iduser = ".$rs_iduser." and rs_idpenca = ".$rs_idpenca);
    }
    
    public function submeter_result($user_id, $result1, $result2, $match_id, $round) {
        $db = $this->db;
        
        $dados = array(
            'rs_idmatch' => $match_id,
            'rs_res1' => $result1,
            'rs_res2' => $result2,
            'rs_iduser' => $user_id,
            'rs_round' => $round,
             'rs_date' => date("Y-m-d H:i:s")
                
        );
        
        $db->insert("result", $dados);
        return $db->lastInsertId();
    }
	
	public function teste($teste) {
		$db = $this->db;
		
		$db->insert("teste", array('teste' => $teste));
	}
	
	public function alterar_result($user_id, $result1, $result2, $match_id, $round) {
		$db = $this->db;
		
		$dados = array();
		
		$db->update("result", array(
			'rs_res1' => $result1,
                    'rs_date' => date("Y-m-d H:i:s"),
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
                ->joinInner("user","user.us_id = vwmatchsresult.rs_iduser",array("us_id","us_username","us_email"))
                ->where("vwmatchsresult.mt_idchampionship = ?", $champ);
        
        $result = $return->query()->fetchAll();
        
        return $result;
    }
    
    public function load_matchs_byrodada($champ, $rodada) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name', 't1logo' => 't1.tm_logo'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name', 't2logo' => 't2.tm_logo'))
            ->joinInner('round', 'round.rd_id = match.mt_idround')
            ->joinInner('championship', 'championship.ch_id = match.mt_idchampionship')
            ->where('mt_idchampionship = ?', $champ)
            ->where("mt_idround = ?",$rodada)
            ->query()
            ->fetchAll();
        
        return $result;
        
    }
    
    public function load_matchs_byrodada2($champ, $rodada) {
        $db = $this->db;
        
        $result = $db->select()->from("match")
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name', 't1logo' => 't1.tm_logo'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name', 't2logo' => 't2.tm_logo'))
            ->joinInner('round', 'round.rd_id = match.mt_idround')
            ->joinInner('championship', 'championship.ch_id = match.mt_idchampionship')
            ->where('mt_idchampionship = ?', $champ)
            ->where("mt_idround = ?",$rodada)
                ->order('mt_date')
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
    
    	public function match_ja_palpitado_penca($id_match, $id_user, $penca) {
		$db = $this->db;
		
		$result = $db->select()->from("result")
			->where("rs_idmatch = ?", $id_match)
			->where("rs_iduser = ?", $id_user)
                        ->where("rs_idpenca = ?", $penca);
			
		$return = $result->query()->fetch();
		
		if (empty($return)) {
			return false;
		}
		return true;
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

    /**
     * Devuelve la cantidad de partidos no jugados
     * @param cantidad
     */
    public function partidosNoJugados($cantidad) {
        $db = $this->db;

        return $db->select()->from("match")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship")
            ->joinInner("round","round.rd_id = match.mt_idround")
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' =>'t1.tm_id', 'tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' =>'t2.tm_id','tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'))   
            ->where("championship.ch_ativo = 1")
            ->where("match.mt_played = 0")
            ->order("match.mt_date ASC")
            ->limit($cantidad,0)->query()->fetchAll();
    }

        /**
     * Devuelve la cantidad de partidos no jugados
     * @param cantidad
     */
    public function partidos($cantidad, $campeonato) {
        $db = $this->db;

        return $db->select()->from("match")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship")
            ->joinInner("round","round.rd_id = match.mt_idround")
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' =>'t1.tm_id', 'tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' =>'t2.tm_id','tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'))   
            ->where("championship.ch_ativo = 1")
            ->where("match.mt_played = 0")
            ->where("championship.ch_id = ?", $campeonato)
            ->order("match.mt_date ASC")
            ->limit($cantidad,0)->query()->fetchAll();
    }

    /**
     * Retorna informacion de la rodada con los palpites del usuario
     * de un campeonato especificado
     * @param championship
     * @param rodada
     * @param usuario
     */
    public function loadRodadaPalpitada($championship, $rodada, $usuario) {
        $db = $this->db;
        
        $sql = "
            SELECT match.mt_idchampionship as ch_id, vwpalpites.*, `match`.*, t1.eq_id as tm1_id, t1.eq_nome as t1nome, t1.eq_logo as tm1_logo,  
            `t1`.*, t2.eq_id as tm2_id, 
            t2.eq_nome as t2nome, t2.eq_logo as tm2_logo,
            `t2`.*, r.*, round.*  
            FROM `match` 
            INNER JOIN `equipo` AS `t1` ON t1.eq_id = match.mt_idteam1 
            INNER JOIN `equipo` AS `t2` ON t2.eq_id = match.mt_idteam2   
            LEFT JOIN vwpalpites ON vwpalpites.rs_idmatch = match.mt_id
            LEFT JOIN round ON round.rd_id = match.mt_idround
            LEFT JOIN (select * from result where rs_iduser = ".$usuario." AND rs_idpenca = 0) r ON r.rs_idmatch = match.mt_id 
            WHERE  (match.mt_idchampionship = '".$championship."') AND (mt_idround = '".$rodada."') ORDER BY `mt_date` ASC";
        
        $result = $db->query($sql)->fetchAll();
        return $result;
        
    }

    /**
     * Devuelve los partidos del campeonato y de la rodada especificada
     * 
     * @param idCampeonato
     * @param idRodada
     */
    public function get($idCampeonato, $idRodada) {
        $db = $this->db;

        return $this->db->select()
            ->from("match")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship", array("ch_id" => "championship.ch_id","ch_nome" => "championship.ch_nome"))
            ->joinInner(array('t1' => 'equipo'), 't1.eq_id = match.mt_idteam1', array('tm1_id' => 't1.eq_id', 't1nome' => 't1.eq_nome', 'tm1_logo' => 't1.eq_logo', 'tm1_sigla' => 't1.eq_sigla'))
            ->joinInner(array('t2' => 'equipo'), 't2.eq_id = match.mt_idteam2', array('tm2_id' => 't2.eq_id', 't2nome' => 't2.eq_nome', 'tm2_logo' => 't2.eq_logo', 'tm2_sigla' => 't2.eq_sigla'))
            ->where("championship.ch_id = ?", $idCampeonato)
            ->where("match.mt_idround = ?", $idRodada)
            ->query()
            ->fetchAll();
    }

    /**
     * Retorna la url para buscar los partidos del campeonato
     * @param idCampeonato
     */
    public function getGlobo($idCampeonato) {
        return $this->db->select()
            ->from("urlcampeonatos")
            ->where("dr_idchampionship = ?", $idCampeonato)
            ->query()
            ->fetch();
    }

    /**
     * Devuelve el partido especificado
     * 
     * @param idMatch
     */
    public function getPartido($idMatch) {
        $db = $this->db;

        return $this->db->select()
            ->from("match")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship", array("ch_id" => "championship.ch_id","ch_nome" => "championship.ch_nome"))
            ->joinInner("round", "round.rd_id = match.mt_idround")
            ->joinInner(array('t1' => 'equipo'), 't1.eq_id = match.mt_idteam1', array('tm1_id' => 't1.eq_id', 't1nome' => 't1.eq_nome', 'tm1_logo' => 't1.eq_logo', 'tm1_sigla' => 't1.eq_sigla'))
            ->joinInner(array('t2' => 'equipo'), 't2.eq_id = match.mt_idteam2', array('tm2_id' => 't2.eq_id', 't2nome' => 't2.eq_nome', 'tm2_logo' => 't2.eq_logo', 'tm2_sigla' => 't2.eq_sigla'))
            ->where("match.mt_id = ?", $idMatch)
            ->query()
            ->fetch();
    }

    /**
     * Retorna todos los partidos marcados como NO JUGADOS antes 
     * que la fecha pasada por parametro
     * @param data
     */
    public function loadPartidosNoJugados($data) {
        $db = $this->db;

        return $this->db->select()
            ->from("match")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship", array("ch_id" => "championship.ch_id","ch_nome" => "championship.ch_nome","ch_atualround" => "championship.ch_atualround"))
            ->joinInner("round", "round.rd_id = match.mt_idround")
            ->joinInner(array('t1' => 'equipo'), 't1.eq_id = match.mt_idteam1', array('tm1_id' => 't1.eq_id', 't1nome' => 't1.eq_nome', 'tm1_logo' => 't1.eq_logo', 'tm1_sigla' => 't1.eq_sigla'))
            ->joinInner(array('t2' => 'equipo'), 't2.eq_id = match.mt_idteam2', array('tm2_id' => 't2.eq_id', 't2nome' => 't2.eq_nome', 'tm2_logo' => 't2.eq_logo', 'tm2_sigla' => 't2.eq_sigla'))
            ->where("match.mt_date < ?", $data)
            ->where("match.mt_played = 0")
            ->query()
            ->fetchAll();
    }

    /**
     * Retorna los proximos partidos a jugarse
     */
    public function games() {
        $hoy = date('Y-m-d');

        $db = $this->db;

        return $this->db->select()
            ->from("match")
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship", array("ch_id" => "championship.ch_id","ch_nome" => "championship.ch_nome","ch_atualround" => "championship.ch_atualround"))
            ->joinInner("round", "round.rd_id = match.mt_idround")
            ->joinInner(array('t1' => 'equipo'), 't1.eq_id = match.mt_idteam1', array('tm1_id' => 't1.eq_id', 't1nome' => 't1.eq_nome', 'tm1_logo' => 't1.eq_logo', 'tm1_sigla' => 't1.eq_sigla'))
            ->joinInner(array('t2' => 'equipo'), 't2.eq_id = match.mt_idteam2', array('tm2_id' => 't2.eq_id', 't2nome' => 't2.eq_nome', 'tm2_logo' => 't2.eq_logo', 'tm2_sigla' => 't2.eq_sigla'))
            ->where("match.mt_date >= ?", $hoy)
            ->where("match.mt_played = 0")
            ->order("match.mt_date ASC")
            ->limit(12)
            ->query()
            ->fetchAll();
    }

    /**
     * Retorna el partido con las siglas de los equipos pasados por parametros
     * y la rodada y del campeonato
     * @param equipo1
     * @param equipo2
     * @param idCampeonato
     * @param idRodada
     */
    public function loadBySiglaAndCampeoantoAndRodada($equipo1, $equipo2, $idCampeonato, $idRodada) {
        return $this->db->select()
            ->from("match", array("match.mt_id"))
            ->joinInner(array('t1' => 'equipo'),'t1.eq_id = match.mt_idteam1', array())
            ->joinInner(array('t2' => 'equipo'),'t2.eq_id = match.mt_idteam2', array())
            ->where("match.mt_idchampionship = ?", $idCampeonato)
            ->where("match.mt_idround =?", $idRodada)
            ->where("t1.eq_sigla = ?", $equipo1)
            ->where("t2.eq_sigla = ?", $equipo2)
            ->query()->fetch();
    }
    
}