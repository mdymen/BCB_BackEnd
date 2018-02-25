<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of result
 *
 * @author Martin Dymenstein
 */

class Application_Model_Result extends Application_Model_Bd_Adapter
{
    protected $_name = 'result';
    
    public function update(array $params, $where) {
        
        $db = $this->db; 

        $info = array(
            'rs_res1'=>$params['res1'],
            'rs_res2'=>$params['res2'],
        );       
        
        $db->update('result',$info, 'rs_id = '.$params['rs_id']);
    }
    
    public function update_puntagem($puntagem, $rs_id) {
        $db = $this->db;
        
        $res = 0;
        if ($puntagem != 0) {
            $res = 1;
        }
        
//        print_r("res ".$puntagem);
//        die(".");
        
        $info = array('rs_points' => $puntagem, 'rs_result' => $res);
        
        //$db->update('user_penca', $info, 'up_idpenca = '.$penca.' and up_iduser ='.$usuario);
        $db->update('result', $info, 'rs_id = '.$rs_id);
    }
    
    public function update_resultado($id_match, $res1, $res2) {
        $db = $this->db;
        
        $dados = array(
            'mt_goal1' => $res1, 
            'mt_goal2' => $res2,
            'mt_played' => true
            );
        
        $db->update('match', $dados,'mt_id = '.$id_match);
    }
    
    public function getResult($id) {
        $db = $this->db;
        
        return $db->select()->from("result")
                ->where("rs_id = ?", $id)
                ->query()
                ->fetch();  
    }
	
	public function getResultByUserMatch($user_id, $match_id) {
        $db = $this->db;
        
        return $db->select()->from("result")
                ->where("rs_iduser = ?", $user_id)
				->where("rs_idmatch = ?", $match_id)
                ->query()
                ->fetch();  
    }
    
    public function get_result_by_match_user_penca($match_id, $user_id, $penca) {
        $db = $this->db;
        
        return $db->select()->from("result")
                ->where("rs_iduser = ?", $user_id)
                ->where("rs_idmatch = ?", $match_id)
                ->where("rs_idpenca = ?", $penca)
                ->query()
                ->fetch();  
    }
    
    public function palpites_em_acao($us_id) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("count(rs_id) as cantidad"))
                ->where("rs_result is null")
                ->where("rs_iduser = ?", $us_id)
                ->query()
                ->fetchAll();

        return $result;        
    }
    
    public function points($us_id) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("sum(rs_points) as pontos"))
                ->where("rs_iduser = ?", $us_id)
                ->query()
                ->fetchAll();

        return $result;         
    }
    
    public function palpites_em_acao_group($us_id, $ordem) {
        $db = $this->db;
        
        $o = "";
        if (!empty($ordem)) {
            if ($ordem == 1) {
                $o = "match.mt_idchampionship";
            } else if ($ordem == 2) {
                $o = "match.mt_date";
            } else if ($ordem == 3) {
                $o = "match.mt_round";
            } else {
                $o = "match.mt_idchampionship";
            }
        }
        
//        $result = $db->select()->from("result")
//                ->joinInner("match", "match.mt_id = result.rs_idmatch")
//                ->joinInner("championship", "championship.ch_id = match.mt_idchampionship")
//                ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name', 'tm1_logo' => 't1.tm_logo'))
//                ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name', 'tm2_logo' => 't2.tm_logo'))
//                ->where("rs_result is null")
//                ->where("rs_iduser = ?", $us_id)
            $result = $db->select()->from("vwmatchsresult")
                ->joinInner("vwpalpites","vwpalpites.rs_idmatch = vwmatchsresult.mt_id")
                ->where("rs_result is null")
                ->where("mt_played = 0")    
                ->where("rs_iduser = ?", $us_id)
                ->order($o)
                ->query()
                ->fetchAll();
                
//            print_r($result);
            
            
//                 print_r($result->__toString());
//        die(".");
             
        return $result;
        
    }
    
    public function puntuacao($id_user) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("sum(rs_points) as pontos"))
                ->where("rs_iduser = ?", $id_user)
                ->query()
                ->fetchAll();
        
        return $result;
    }
    
    public function ganados($id_user) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("count(rs_points) as ganados"))
                ->where("rs_iduser = ?", $id_user)
                ->where("rs_result = 0")
                ->query()
                ->fetchAll();
        
        return $result;
    }    
    
    public function perdidos($id_user) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("count(rs_points) as perdidos"))
                ->where("rs_iduser = ?", $id_user)
                ->where("rs_result = 0")
                ->query()
                ->fetchAll();
        
        return $result;        
    }
    
    public function ranking_round($round, $championship) {
        $db = $this->db;
        
        $result = $db->select()->from("vwranking_round")
                ->where("mt_idround = ?", $round)
                ->where("mt_idchampionship = ?", $championship)
                ->order("points DESC");
        
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function ranking_champ($championship) {
        $db = $this->db;
        
        $result = $db->select()->from("vwranking_championship")
                ->where("mt_idchampionship = ?", $championship)
                ->order("points DESC");

        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function rankings_champ_usuario($usuario) {
        $db = $this->db;
        
        $sql ="call rankings_championships(". $usuario .")";       
        
        $result = $db->query($sql)->fetchAll();
        
        return $result;
    }
    
    public function ganadores_match($match) {
        $db = $this->db;
        
        $result = $db->select()->from("vwmatchsresult", array('vwmatchsresult.mt_id', 'vwmatchsresult.rs_points'))
                ->joinInner("user", "user.us_id = vwmatchsresult.rs_iduser", array('user.us_id','user.us_username'))
                ->where("mt_id = ?", $match)
                ->where("rs_points = 5");
                
        $return = $result->query()->fetchAll();
        
        return $return;
    }
    
    public function getresultsbychamp($champ)  {
        $db = $this->db;
        
        $result = $db->select()->from("vwmatchsresult")
                ->joinInner("user","user.us_id = rs_iduser")                
                ->where("mt_idchampionship = ?", $champ)
                ->order("user.us_id")
                ->order("mt_idround")
                ->query()
                ->fetchAll();
        
        return $result;
        
    }
    
    public function getResultsGanadoresPencas($idmatch) {
        $db = $this->db;
        
        return $db->select()->from("result")
                ->where("rs_result = 1")
                ->where("rs_idmatch = ?", $idmatch)
                ->where("rs_idpenca <> 0")
                ->query()->fetchAll();
    }
    
    public function update_penca_puntuation($iduser, $idpenca) {
        $db = $this->db;
        
        $result = $db->select()->from("user_penca")
                ->where("up_iduser = ?", $iduser)
                ->where("up_idpenca = ?", $idpenca)
                ->query()->fetch();
        
        $puntagem = $result["up_puntagem"] + 5;
        
        $db->update("user_penca", 
                array("up_puntagem" => $puntagem), 
                "up_iduser = ".$iduser." AND up_idpenca = ".$idpenca);
    }
    
    public function calcularmoney($match, $res1, $res2, $s_id_team1, $s_id_team2, $champ) {
        $db = $this->db;
        
        $sql = "call jogo_terminado(".$match.", ".$res1.", ".$res2.", ".$s_id_team1.", ".$s_id_team2.", ".$champ.")";
        
//        return $sql;
        
        //die(".");
        
        $db->query($sql)->fetch();
    }

    /**
     * Verifica los resultados del partido y coloca los puntos correspondientes
     * y actualiza los partidos jugados
     * @param id_team1
     * @param id_team2
     * @param res1
     * @param res2
     */
    public function verificarGanadores($id_team1, $id_team2, $res1, $res2) {
        if ($res1 > $res2) {
            $this->sumarPuntosGanador($id_team1);
            $this->sumarPartidoJugado($id_team2);
        }

        if ($res1 == $res2) {
            $this->sumarEmpate($id_team1);
            $this->sumarEmpate($id_team2);
        }

        if ($res2 > $res1) {
            $this->sumarPuntosGanador($id_team2);
            $this->sumarPartidoJugado($id_team1);
        }
    }

    /**
     * Se le suma 3 puntos al equipo ganador y mas un partido jugado.
     * @param $id_team es el ID de un equipo para sumarle 3 puntos
     */
    public function sumarPuntosGanador($id_team) {
        $db = $this->db;

        $return = $db->select()->from("team")
            ->where("tm_id = ?", $id_team)
            ->query()
            ->fetch();

        $return['tm_points'] = $return['tm_points'] + 3;
        $return['tm_played'] = $return['tm_played'] + 1;
        
        $db->update("team", 
            array(
                'tm_points' => $return['tm_points'],
                'tm_played' => $return['tm_played']
            ), 
            "tm_id = ".$id_team);
    }

    /**
     * Suma un partido jugado
     * @param $id_team
     */
    public function sumarPartidoJugado($id_team) {
        $db = $this->db;

        $return = $db->select()->from("team")
            ->where("tm_id = ?", $id_team)
            ->query()
            ->fetch();

        $return['tm_played'] = $return['tm_played'] + 1;            

        $db->update("team", 
            array(
                'tm_played' => $return['tm_played']
            ), 
            "tm_id = ".$id_team);        
    }

    /**
     *  Suma 1 punto a un equipo y 1 partido mas jugado
     * @param $id_team1
     */
    public function sumarEmpate($id_team) {
        $db = $this->db;

        $return = $db->select()->from("team")
            ->where("tm_id = ?", $id_team)
            ->query()
            ->fetch();

        $return['tm_points'] = $return['tm_points'] + 1;
        $return['tm_played'] = $return['tm_played'] + 1;
        
        $db->update("team", 
            array(
                'tm_points' => $return['tm_points'],
                'tm_played' => $return['tm_played']
            ), 
            "tm_id = ".$id_team);
    }

    /**
     * $jogo es un de match con los resultados para palpitar.
     * Crea la tupla del palpite si ya no fue creada o altera la tupla 
     * que ya previamente fue palpitada.
     * 
     * @param $jogo es el partido
     * @param $usuario es el id del usuario que palpito
     */
    public function palpitar($jogo, $usuario) {
        
        $db = $this->db;

        $dados = array(
            'rs_idmatch' => $jogo['mt_id'],
            'rs_res1' => $jogo['rs_res1'],
            'rs_res2' => $jogo['rs_res2'],
            'rs_iduser' => $usuario,
            'rs_round' => $jogo['rd_id'],
            'rs_date' => date("Y-m-d H:i:s")
        );

        $result = $db->select()->from("result")
            ->where("rs_idmatch = ?",$jogo['mt_id'])
            ->where("rs_iduser = ?", $usuario)
            ->query()->fetch();

        //significa que el partido nunca fue palpitado
        if (empty($result)) {
            $db->insert("result", $dados);
        } else {
            $where = "rs_iduser = ".$result["rs_iduser"]." AND rs_idmatch = ".$result["rs_idmatch"];
            $db->update("result",  $dados, $where);
        }
    }
}
