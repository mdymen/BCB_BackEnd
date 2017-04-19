<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
include APPLICATION_PATH.'/models/championships.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH.'/helpers/translate.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
class Admin_ResultadosController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $params = $this->_request->getParams();
        
        $c = new Application_Model_Championships();
        if (!empty($params['champ'])) {
            $this->view->rounds = $c->getrondas($params['champ']);
            $this->view->champ = $params['champ'];
            
            if (!empty($params['ronda'])) {
                
//                print_r($params);
//                die(".");
                
                $ronda = $params['ronda'];
                $champ = $params['champ'];
                $m_obj = new Application_Model_Matchs();
                $matchs = $m_obj->load_matchs_byrodada($champ, $ronda);
                $matchs = $m_obj->setDatas($matchs);
                $this->view->matchs = $matchs;      
                $this->view->ronda = $ronda;
            }
        }
        
        
        $this->view->championships = $c->load();
        $this->view->champ = $params['champ'];
    }
    
    public function registerAction() {}

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
    public function salvarjogoAction() {
       
    }
    
    public function setresultadoAction() {
        $params = $this->_request->getParams();
        
        $matchid = $params['match'];
        $res1 = $params['res1'];
        $res2 = $params['res2'];
        $team1 = $params['team1'];
        $team2 = $params['team2'];
        $champ = $params['champ'];
        
        
        
        $result = new Application_Model_Result();   
        $r = $result->calcularmoney($matchid, $res1, $res2, $team1, $team2, $champ);
       
//        $result->update_resultado($matchid, $res1, $res2);
//        
//        $teams = new Application_Model_Teams();
//        
//        if ($res1 > $res2) {
//            $teams->sum_points($team1, 3);
//            $teams->sum_match($team2);
//        }
//        if ($res1 < $res2) {            
//            $teams->sum_match($team1);
//            $teams->sum_points($team2, 3);
//        }
//
//        if ($res1 == $res2) {
//            $teams->sum_points($team1, 1);
//            $teams->sum_points($team2, 1);
//        }
//        
//        /* Retorna los RESULT con ID MATCH 
//         *  [rs_id] => 11 [rs_idmatch] => 41 [rs_res1] => 9 [rs_res2] => 2 
//            [rs_date] => 2004-09-16 00:00:00 [rs_idpenca] => 1 [rs_iduser] => 1 
//            [rs_round] => 1 [rs_result] => [rs_points] => 0 ) )
//         */
//        $matchs = new Application_Model_Matchs();
//        $match = $matchs->load_resultados_palpitados($matchid);
//
//        $x = "";                
//        
//        for ($j = 0; $j < count($match); $j = $j + 1) {
//            $puntagem = $this->puntuacao($match[$j], $res1, $res2);
//            $x = $puntagem;
//            $result->update_puntagem($puntagem, $match[$j]['rs_id']);
//        }
//        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }
    
    private function puntuacao($match, $res1, $res2) {
        $v1 = $match['rs_res1'];
        $v2 = $match['rs_res2'];
        
           print_r("afuera ".$v1);
        
        if ($v1 == $res1 && $v2 == $res2) {
                    print_r("adentro ".$v1);
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
        
        print_r("retorna 0");
//        die(".");
        
        return 0;
    }
    
    function fecharrodadaAction() {
        $params = $this->_request->getParams();
        
        $ch = new Application_Model_Championships();
        
        $rodada = $params['rodada'];
        $champ = $params['champ'];
        
        $ch->setAtualRound($champ, $rodada);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
        
    }
    
}

