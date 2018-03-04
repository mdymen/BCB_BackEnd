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

    /**
     * Pasando @param champ carga las rodadas del campeonato.
     * Luego pasando @param ronda, carga las rodadas del campeonato.
     * Pasando @param champ y @param ronda 
     * @return resultado['rounds'];
     * @return resultado['champ'];
     * @return resultado['championships'];
     * @return resultado['matchs'];
     * @return resultado['ronda'];
     */
    public function cargarpartidosAction()
    {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	  
        
        $c = new Application_Model_Championships();
        if (!empty($params['champ'])) {
            $resultado['rounds'] = $c->getrondas($params['champ']);
            
            if (!empty($params['ronda'])) {

                $ronda = $params['ronda'];
                $champ = $params['champ'];
                $m_obj = new Application_Model_Matchs();
                $matchs = $m_obj->load_matchs_byrodada($champ, $ronda);
                $matchs = $m_obj->setDatas($matchs);
                $resultado['matchs'] = $matchs;      
                $resultado['ronda'] = $ronda;
            }
        }
        
        
        $resultado['championships'] = $c->load();
        $resultado['champ'] = $params['champ'];

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json');
       
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(TRUE);
       
       $this->_helper->json($resultado);
    }
    

    /**
     * Envia los resultados de una rodada para procesar.
     * @param results, donde es una lista de resultados con 
     * los siguientes parametros @param match, @param res1, @param res2,
     * @param team1, @param team2, @param champ, @param played
     */
    public function grabarresultadosAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);

        $resultados = $params['results'];

        $result = new Application_Model_Result();  
        for ($i = 0; $i < count($resultados); $i = $i + 1) {
            $resultado = $resultados[$i];

            $matchid = $resultado['match'];
            $res1 = $resultado['res1'];
            $res2 = $resultado['res2'];
            $team1 = $resultado['team1'];
            $team2 = $resultado['team2'];
            $champ = $resultado['champ'];   
            $played = $resultado['played'];                      
            
            
            $ganadores = $result->getResultsGanadoresPencas($matchid);

            
            if ($played == 0) {

                //verifica el resultado del partido y setea los puntos
                //y setea al partido como jugado
                $result->verificarGanadores($team1, $team2, $res1, $res2, $matchid);
                
                //verifica los usuarios ganadores y setea los puntos
                $this->usuariosGanadores($res1, $res2, $matchid);

                //seta el resultado al partido
                $result->setResultado($matchid, $res1, $res2);

            }
        }
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }

    /**
     * para teste
     */
    private function usuariosganadoresAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);

        $res1 = $params['res1'];
        $res2 = $params['res2'];
        $idmatch = $params['idmatch'];

        $result = new Application_Model_Result();
        $ganadores = $result->obtenerUsuariosGanadores($res1, $res2, $idmatch);

        for ($i =0; $i < count($ganadores); $i = $i + 1) {
            $result->puntosAlGanador($ganadores[$i]['rs_iduser'], $idmatch);
        }

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($ganadores);
    }

    /**
     * Busca los ganadores del partido y luego les coloca 5 puntos.
     * @param res1
     * @param res2
     * @param idmatch
     */
    private function usuariosGanadores($res1, $res2, $idmatch) {
        $result = new Application_Model_Result();
        $ganadores = $result->obtenerUsuariosGanadores($res1, $res2, $idmatch);
        for ($i = 0; $i < count($ganadores); $i = $i + 1) {
            $result->puntosAlGanador($ganadores[$i]['rs_iduser'], $idmatch);
        }
    }
}

