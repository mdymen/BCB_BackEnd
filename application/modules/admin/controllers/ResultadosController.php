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
        
        $ganadores = $result->getResultsGanadoresPencas($matchid);
        for ($i = 0; $i < count($ganadores); $i = $i + 1) {
            $result->update_penca_puntuation($ganadores[$i]['rs_iduser'], $ganadores[$i]['rs_idpenca']);
        }

        
        $r = $result->calcularmoney($matchid, $res1, $res2, $team1, $team2, $champ);
       
        //calcular puntuacion de la penca

        
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

