<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChampionshipController
 *
 * @author Martin Dymenstein
 */
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/helpers/html.php';
include APPLICATION_PATH.'/helpers/box.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/helpers/paginacao.php';
include APPLICATION_PATH.'/helpers/posicoes.php';
include APPLICATION_PATH.'/helpers/ranking.php';
class CampeonatosController extends Zend_Controller_Action
{
    public function indexAction() {
        $params = $this->_request->getParams();
                    
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load();
        
        $p_obj = new Application_Model_Penca();
        
        if (!empty($params['champ'])) {  
        
            $this->view->teamuserid = $this->getTimeUserId();
            $this->view->teamusername = $this->getTimeUserName();
            
            $champ_id = $params['champ'];

            $this->view->champ = $champ_id;
            $this->view->championship = $champ->getChamp($champ_id);
            
            if (empty($params['rodada'])) {
                $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
            } else {            
                $rodada_id = $params['rodada'];
            }
            
            $this->view->rodada = $rodada_id;

            $storage = new Zend_Auth_Storage_Session();
            $data = (get_object_vars($storage->read()));

            $matchs_obj = new Application_Model_Matchs();
            $rondas = $matchs_obj->getrondas($champ_id);

            $p_obj = new Application_Model_Penca();
            
            if (empty($params['team'])) {
                $rodadas = $matchs_obj->load_rodada_com_palpites($champ_id, $rodada_id, $data['us_id']);
                $this->view->porteam = true;
                $this->view->porrodada = false;
            } else {
                $this->view->porteam = false;
                $this->view->porrodada = true;
                $team_id = $params['team'];
                $rodadas = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $data['us_id']);
            }

            
            
            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 
            
            $ranking = new Application_Model_Result();
            $rankings = $ranking->ranking_round($rodada_id, $champ_id);
            $rankings_champ = $ranking->ranking_champ($champ_id);
            
            $this->view->teams = $teams;
            $this->view->rodadas = $rodadas;
            $this->view->n_rodada = $rodada_id;
            $this->view->rondas = $rondas;   
            $this->view->rankings = $rankings;
            $this->view->ranking_champ = $rankings_champ;
        }
    }
    
    public function encerradosAction() {
        $params = $this->_request->getParams();
                    
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load_encerrados();
        
        $p_obj = new Application_Model_Penca();
        
        if (!empty($params['champ'])) {  
        
            $this->view->teamuserid = $this->getTimeUserId();
            $this->view->teamusername = $this->getTimeUserName();
            
            $champ_id = $params['champ'];

            $this->view->champ = $champ_id;
            $this->view->championship = $champ->getChamp($champ_id);
            
            if (empty($params['rodada'])) {
                $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
            } else {            
                $rodada_id = $params['rodada'];
            }
            
            $this->view->rodada = $rodada_id;

            $storage = new Zend_Auth_Storage_Session();
            $data = (get_object_vars($storage->read()));

            $matchs_obj = new Application_Model_Matchs();
            $rondas = $matchs_obj->getrondas($champ_id);

            $p_obj = new Application_Model_Penca();
            
            if (empty($params['team'])) {
                $rodadas = $matchs_obj->load_matchs($champ_id, $rodada_id, $data['us_id']);
                $this->view->porteam = true;
                $this->view->porrodada = false;
            } else {
                $this->view->porteam = false;
                $this->view->porrodada = true;
                $team_id = $params['team'];
                $rodadas = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $data['us_id']);
            }

            
            
            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 
            
            $ranking = new Application_Model_Result();
            $rankings = $ranking->ranking_round($rodada_id, $champ_id);
            $rankings_champ = $ranking->ranking_champ($champ_id);
            
            $this->view->teams = $teams;
            $this->view->rodadas = $rodadas;
            $this->view->n_rodada = $rodada_id;
            $this->view->rondas = $rondas;   
            $this->view->rankings = $rankings;
            $this->view->ranking_champ = $rankings_champ;
        }
    }    
    
    public function getTimeUserId() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        
        return $data['us_team'];
    }
    
    public function getTimeUserName() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        
        return $data['us_teamname'];
    }


    /**
     * GET
     * Retorna todos los campeonatos activos
     */
    public function getAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $champ = new Application_Model_Championships();
        $result['body'] = $champ->load();

        $this->_helper->json($result);

    }

    /**
     * POST
     * Lista de equipos-campeonato para ser grabados
     * como asociados.
     * Adiciona los equipos al campeonato
     * @param equipos
     */
    public function saveAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);

            $equipos = $data['equipos'];

            for ($i = 0; $i < count($equipos); $i = $i + 1) {
                $equipo = $equipos[$i];
                unset($equipo['nome']);

                $c = new Application_Model_Championships();


                $c->saveEquipoCampeonato($equipo);

            }

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * GET
     * Retorna todos los equipos cadastrados en el campeonato
     * @param idCampeonato
     */
    public function getbycampeonatoAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $body = $this->getRequest()->getParams();
            $idCampeonato = $body['idCampeonato'];

            $c = new Application_Model_Championships();

            $result['body'] = $c->loadByCampeonato($idCampeonato);

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * GET
     * Retorna todas las rodadas del campeonato
     * @param idCampeonato
     */
    public function getrodadasAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $body = $this->getRequest()->getParams();
            $idCampeonato = $body['idCampeonato'];

            $c = new Application_Model_Championships();
            $result['body'] = $c->loadRodadasByCampeonato($idCampeonato);

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }


    /**
     * GET
     * Retorna todos los partidos de la rodada especificada y del campeonato especificado
     * @param idUsuario
     * @param idCampeonato
     * @param idRodada
     */
    public function getpartidosAction() {
       
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            
            $params = $this->getRequest()->getParams();
        
            
            $champ = new Application_Model_Championships();
            $result['championships']= $champ->load();
            
            $id = $params['idUsuario'];
            
            $p_obj = new Application_Model_Penca();

            if (!empty($params['idCampeonato'])) {                      
                
                $champ_id = $params['idCampeonato'];

                $ranking = new Application_Model_Result();
                $result['rankings_champ'] = $ranking->ranking_champ($champ_id);
                
                $result['idCampeonato'] = $champ_id;
                $result['championship'] = $champ->getChamp($champ_id);
                
                if (!is_numeric($params['idRodada'])) {
                    $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
                } else {            
                    $rodada_id = $params['idRodada'];
                }

                $storage = new Zend_Auth_Storage_Session();

                $matchs_obj = new Application_Model_Matchs();
                $rondas = $matchs_obj->getrondas($champ_id);

                $tem_grupo = false;
                
                if (empty($params['team'])) {
                    $rodada = $matchs_obj->loadRodadaPalpitada($champ_id, $rodada_id, $id);
                    $result['porteam'] = true;
                    $result['porrodada'] = false;
                } else {
                    $result['porteam']  = false;
                    $$result['porrodada'] = true;
                    $team_id = $params['team'];
                    $rodada = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $id);
                }
                
                $rodadaAtual = $matchs_obj->getRodada($rodada_id, $id);

                $result['teams'] = $teams;
                
                //los partidos de la rodada n_rodada
                $result['rodada'] = $rodada;
                
                //el numero de la rodada activa. La que siguiente inmediata que se va a jugar
                $result['n_rodada'] = $rodada_id;

                //toda la informacion de la rodada actual
                $result['rodadaAtual'] = $rodadaAtual;            

                //las rodadas del campeonato registradas en el sistema
                $result['rondas'] = $rondas;      		

                $result['status'] = 200;
                            
            }
                
            $res['body'] = $result;

            $this->_helper->json($res);
            
        }
        catch (Exception $e) {
            $result['status'] = 400;
            $result['error'] = $e->getMessage();
            $this->_helper->json($result);
        }
    }

    /**
     * Retorna todos los campeonatos abiertos
     * 
     * @return ch_id
     * @return ch_acumulado
     * @return ch_nome
     * @return ch_logocampeonato
     * @return ch_id
     */
    public function getbasicAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $champ = new Application_Model_Championships();
            $result['body']= $champ->get();

            $this->_helper->json($result);

        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    public function htmlToArray($html) {

        $object = $this->html_to_obj($html);

        $lista = $object['children'][0]['children'];

        $partidos = array();
        $result = array();

        for ($i = 0; $i < count($lista); $i = $i + 1) {

            $partidos[$i] = $this->algoritmoPartidoJugado($lista[$i]);

            if ($partidos[$i]['equipo1']['nome'] == null) {
                $partidos[$i] = $this->algoritmoPartidoNoJugado($lista[$i]);
            }

        }

        $result['body'] = $partidos;

        return $result;


    }

    function algoritmoPartidoJugado($partido) {
        $partidos = $partido['children'][0]['children'][2]['children'][1]['children'];

        $result['data'] = $partido['children'][0]['children'][1]['content'];
        $result['hora'] = $partido['children'][0]['children'][2]['children'][0]['html'];
        $result['equipo1']['nome'] = $partidos[0]['children'][1]['html'];
        $result['equipo1']['resultado'] = $partidos[1]['children'][0]['html'];
        $result['equipo2']['resultado'] = $partidos[1]['children'][2]['html'];
        $result['equipo2']['nome'] = $partidos[2]['children'][2]['html'];
        $result['played'] = strcmp($partido['children'][0]['children'][2]['children'][2]['html'], "veja como foi") == 0 ? 1 : 0 ;

        return $result;
    }

    function algoritmoPartidoNoJugado($partido) {
        $result['data'] = $partido['children'][0]['children'][1]['content'];
        $result['hora'] = $partido['children'][0]['children'][2]['html'];
        $result['equipo1']['nome'] = $partido['children'][0]['children'][3]['children'][0]['children'][1]['html'];
        $result['equipo2']['nome'] = $partido['children'][0]['children'][3]['children'][2]['children'][2]['html'];
        $result['equipo1']['resultado'] = null;
        $result['equipo2']['resultado'] = null;
        $result['played'] = 0;

        return $result;

    }

    public function getjsongloboAction() {
        error_reporting(E_ERROR | E_PARSE);

        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $data['dir']);
        
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $server_output = curl_exec ($ch);

            curl_close ($ch);        

            $result = $this->htmlToArray($server_output);

            $this->_helper->json($result);

        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    public function getjsonhtmlgloboAction() {
        error_reporting(E_ERROR | E_PARSE);

        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $data['dir']);
        
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $server_output = curl_exec ($ch);

            curl_close ($ch);        

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $result = $this->html_to_obj($server_output);

            print_r($result);
            die(".");

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }        
    }

    public function html_to_obj($html) {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        return $this->element_to_obj($dom->documentElement);
    }

    public function element_to_obj($element) {
        $obj = array( "tag" => $element->tagName );
        foreach ($element->attributes as $attribute) {
            $obj[$attribute->name] = $attribute->value;
        }
        foreach ($element->childNodes as $subElement) {
            if ($subElement->nodeType == XML_TEXT_NODE) {
                $obj["html"] = $subElement->wholeText;
            }
            else {
                $obj["children"][] = $this->element_to_obj($subElement);
            }
        }
        return $obj;
    }

    /**
     * POST
     * Busca los resultados en la globo y retorna todos los partidos cadastrados con los resultados 
     * encontrados en la globo     
     * @param rodada es lo que se va a utilizar para sustituir algo referente a la rodada en la url de request
     * @param idCampeonato id campeonato del bolao 
     * @param idRodada id de la rodada perteneciente al bolao
     */
    function globoAction() { 
        error_reporting(E_ERROR | E_PARSE);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);

            $existsRodada = true;

            //No existe rodada entonces hay que crearla
            if (empty($data['idRodada'])) {
                $existsRodada = false;
                $c = new Application_Model_Championships();
                $data['idRodada'] = $c->salvar_rodada($data['idCampeonato'], $data['rodada']);
            }


            $c = new Application_Model_Matchs();
            $urlcampeonatos = $c->getGlobo($data['idCampeonato']);

            $urlcampeonatos['dr_url'] = str_replace("###",$data['rodada'], $urlcampeonatos['dr_url']);
            
            $server_output = $this->getGlobo($urlcampeonatos['dr_url']); curl_exec ($ch);
              
            $res =  $this->htmlToArray($server_output);

            $partidosGlobo = $res['body'];

            if (!$existsRodada) {
                $partidosBolao = array();
                for ($i = 0; $i < count($partidosGlobo); $i = $i + 1) {

                    $pGlobo = $partidosGlobo[$i];

                    $e = new Application_Model_Equipo();                    
                    $equipo1 = $e->getBySigla($pGlobo['equipo1']['nome']);
                    $equipo2 = $e->getBySigla($pGlobo['equipo2']['nome']);
                
                    $partido = array();
                    $partido['tm1_id'] = $equipo1['eq_id'];
                    $partido['tm2_id'] = $equipo2['eq_id'];

                    $partido['t1nome'] = $equipo1['eq_nome'];
                    $partido['t2nome'] = $equipo2['eq_nome'];

                    $partido['tm1_logo'] = $equipo1['eq_logo'];
                    $partido['tm2_logo'] = $equipo2['eq_logo'];

                    $partido['tm1_sigla'] = $equipo1['eq_sigla'];
                    $partido['tm2_sigla'] = $equipo2['eq_sigla'];

                    $partido['mt_date'] = $pGlobo['data'].$pGlobo['hora'];
                    $partido['mt_goal1'] = $pGlobo['equipo1']['resultado'];
                    $partido['mt_goal2'] = $pGlobo['equipo2']['resultado'];;
                    $partido['mt_idchampionship'] = $data['idCampeonato'];
                    $partido['mt_played'] = 0;
                    $partido['mt_acumulado'] = 0;
                    $partido['mt_idround'] = $data['idRodada'];

                    $partido['mt_idteam1'] = $equipo1['eq_id'];
                    $partido['mt_idteam2'] = $equipo2['eq_id'];

                    $partido['mt_goal2'] = $pGlobo['equipo2']['resultado'];;
                    $partido['ch_id'] = $data['idCampeonato'];

                    $partido['played'] = $pGlobo['played'];

                    $partidosBolao[$i] = $partido;


                }
            } else {
                $partidosBolao = $this->getPartidos($data['idCampeonato'], $data['idRodada']);

                for ($i = 0; $i < count($partidosGlobo); $i = $i + 1) {
                    for ($j = 0; $j < count($partidosBolao); $j = $j + 1) {
                        $pGlobo = $partidosGlobo[$i];
                        $pBolao = $partidosBolao[$j];
    
                        if (strcmp($pGlobo['equipo1']['nome'], $pBolao['tm1_sigla']) == 0 
                            && strcmp($pGlobo['equipo2']['nome'], $pBolao['tm2_sigla']) == 0) {
    
                            $partidosBolao[$j]['mt_goal1'] = $pGlobo['equipo1']['resultado'];
                            $partidosBolao[$j]['mt_goal2'] = $pGlobo['equipo2']['resultado'];
                            $partidosBolao[$j]['played'] = $pGlobo['played'];
                        }
                    }
                }
            }
            $result['body'] = $partidosBolao;

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }

    }

    public function getPartidos($idCampeonato, $idRodada) {
        try {
            $c = new Application_Model_Matchs();

            return $c->get($idCampeonato, $idRodada);
        }
        catch (Exception $e) {

        }        
    }

    function getGlobo($url) {
        $ch = curl_init();
            
        curl_setopt($ch, CURLOPT_URL, $url);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);

        curl_close ($ch); 

        return $server_output;
    }

}
