<?php

include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/championships.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/matchs.php';

class RegisterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    public function championshipAction() {

    }
    
    public function addchampionshipAction() {
        $params = $this->_request->getParams();
        
        $championship = new Application_Model_Championships();
        $championship->save($params);
        
        $this->redirect("/register/championship");
    }
    
    public function teamAction() {
        
    }
    
    public function addteamAction() {
        $params = $this->_request->getParams();
        
        $championship = new Application_Model_Teams();
        $championship->save($params);
        
        $this->redirect("/register/team");   
    }        
    
    public function pencaAction() {
        $championship = new Application_Model_Championships();
        $this->view->championships = $championship->load();
    }
    
    public function addpencaAction() {
        $params = $this->_request->getParams();
        
//        print_r($params);
//        die(".");
        
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();

        $params['pn_iduser'] = $data->us_id;
        
        $penca = new Application_Model_Penca();
        $penca->save($params);
        
        $params['up_idpenca'] = $params['tm_idchampionship'];
        $params['up_iduser'] = $data->us_id;
        
        $penca->save_userpenca($params);
        
        $this->redirect("/register/penca");
    }
    
    public function matchAction() {
     
        $params = $this->_request->getParams();
        
        $team = new Application_Model_Teams();
        $teams1 = $team->load($params['championship']);
        $teams2 = $teams1;
        $championship = $params['championship'];
        
        
        $this->view->team1 = $teams1;
        $this->view->team2 = $teams2;
        $this->view->championship = $championship;
      
    }
    
    public function addmatchAction() {
        $params = $this->_request->getParams();
        
        $match = new Application_Model_Matchs();
        $j = 0;
        for ($i = 0; $i < count($params); $i = $i + 1) {
            $id1 = $params['tm_idchampionship1'.$j];
            $id2 = $params['tm_idchampionship2'.$j];
            
            $match->save(array(
                'team1' => $id1,
                'team2' => $id2,
                'date' => date('d-n-y'),
                'championship' => $params['championship'],
                'round' => 1
            ));
            $j = $j + 1;
        }
        
        $this->redirect("/register/match");
    }
    
    public function addteamsAction() {
  
        
    }
    
    public function addteamspostAction() {
        $params = $this->_request->getParams();
        
        $champion = $params['tm_idchampionship'];
        
        $teams = $params['tm_name'];
        $teams = explode(",", $teams);
        
        $team_save = new Application_Model_Teams();
                    
        for ($i = 0; $i < count($teams); $i = $i + 1) {
            $obj_team = array( 
                'tm_name'=> $teams[$i], 
                'tm_idchampionship' => $champion
            );
            
            $team_save->save($obj_team);
        }
        
        print_r($teams);
        die(".");
    }
  
    
    public function rodadaAction() {
        $params = $this->_request->getParams();
        
        $championship = $params['championship'];
        $rodada = $params['rodada'];
        
        $match = new Application_Model_Matchs();
        $matchs = $match->load_rodada($championship, $rodada);
        
        $this->view->matchs = $matchs;
    }
    
    /*
     * [count_palpites] => 10 numero de palpites ex 10
     * [mt_x] => 41 ID del match
     * [result1_x] => 1 
     * [result2_x] => 2   resultados
     */
    public function rodadaaddAction() {
        $params = $this->_request->getParams();
        
        $count_palpites = $params['count_palpites'];
        
        $result = new Application_Model_Result();
        
        $matchs = new Application_Model_Matchs();
        
        $match = array();
        $m_res1 = array();
        $m_res2 = array();
        //Actualiza el tanteador de una rodada
        for ($i = 0; $i < count($count_palpites); $i = $i + 1) {
            $id_match = $params['mt_'.$i];
            $res1 = $params['result1_'.$i];
            $res2 = $params['result2_'.$i];
            
            $match[$i] = $id_match;
            $m_res1[$i] = $res1;
            $m_res2[$i] = $res2;
            
            $result->update_resultado($id_match, $res1, $res2);
            
            /* Retorna los RESULT con ID MATCH 
             *  [rs_id] => 11 [rs_idmatch] => 41 [rs_res1] => 9 [rs_res2] => 2 
                [rs_date] => 2004-09-16 00:00:00 [rs_idpenca] => 1 [rs_iduser] => 1 
                [rs_round] => 1 [rs_result] => [rs_points] => 0 ) )
             */
            $match = $matchs->load_resultados_palpitados($id_match);
            
            for ($j = 0; $j < count($match); $j = $j + 1) {
                $puntagem = $this->puntuacao($match[$i], $res1, $res2);
                $result->update_puntagem($puntagem, $match[$j]['rs_idmatch']);
            }
        }
        
        $this->redirect("/register/rodada?championship=1&rodada=1");
    }
    
    private function puntuacao($match, $res1, $res2) {
        $v1 = $match['rs_res1'];
        $v2 = $match['rs_res2'];
        
        if ($v1 == $res1 && $v2 == $res2) {
            return 5;
        }
        
        $ganador_visitante = $v1 < $v2;
        $ganador_visitante_palpite = $res1 < $res2;        
        
        if ($ganador_visitante && $ganador_visitante_palpite) {
            return 1;
        }
        
        $empate1 = $res1 == $res2;
        $empate2 = $v1 == $v2;
        if ($empate1 && $empate2) {
            return 1;
        }
        
        $perdedor_visitante = $v1 > $v2;
        $perdedor_visitante_palpite = $res1 > $res2;
        
        if ($perdedor_visitante && $perdedor_visitante_palpite) {
            return 1;
        }
        
        return 0;
    }
}

