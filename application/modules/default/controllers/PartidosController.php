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
include APPLICATION_PATH.'/helpers/partidos.php';
include APPLICATION_PATH.'/modules/default/controllers/BolaoController.php';
class PartidosController extends BolaoController
{
    /**
     * GET
     * Retorna todos los partidos del campeonato de la rodada especificada
     * @param idCampeonato
     * @param idRodada
     */
    public function getAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $body = $this->getRequest()->getParams();

            $idCampeonato = $body['idCampeonato'];
            $idRodada = $body['idRodada'];

            $c = new Application_Model_Matchs();

            $result['body'] = $c->get($idCampeonato, $idRodada);

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }        
    }

     /**
     * Envia los resultados de una rodada para procesar.
     * @param partidos, donde es una lista de resultados con 
     * 
     * -------
     * PARTIDO
     * -------
     * @param mt_id 
     * @param mt_goal1
     * @param mt_goal2
     * @param mt_idteam1
     * @param mt_idteam2
     * @param mt_idchampionship
     * @param mt_played
     * @param mt_date
     * @param mt_idround
     */
    public function putAction() {
        try {
            $body = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($body);

            $resultados = $params['partidos'];

            $m = new Application_Model_Matchs();        
            $r = new Application_Model_Result();  

            $p = new Helpers_Partidos();

            for ($i = 0; $i < count($resultados); $i = $i + 1) {
                $resultado = $resultados[$i];

                $p->save($resultado, ($resultado['played'])); 

            }
                    
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
        
        
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());

        }
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

    /**
     * Retorna los ultimos 12 partidos jugados
     */
    public function ultimosjugadosAction() {
        try {
            $c = new Application_Model_Matchs();

            $partidos = $c->ultimosjugados();
            $result = array();

            $campeonatos = array();

            //Agrupa los partidos por campeonato y agrega el id del campeonato en
            //un array
            for ($i = 0; $i < count($partidos); $i = $i + 1) {
                $idCampeonato = $partidos[$i]['ch_nome'];
                $cantidad = count($result[$idCampeonato]);

                $result[$idCampeonato][$cantidad] = $partidos[$i]; 

                if (!in_array($idCampeonato, $campeonatos)) {
                    $campeonatos[]=$idCampeonato;
                }
            }

            //Saca el id del campeonato y deja un array con los array 
            //de los partidos
            $retorno = array();
            for ($i = 0; $i < count($campeonatos); $i = $i + 1) {
                $retorno[$i] = $result[$campeonatos[$i]];
              //  $retorno[$i]['nome'] = $campeonatos[$i];
            }

            $res['body'] = $retorno;

            $this->_helper->json($res);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }    
    }

    /**
     * GET
     * Devuelve una lista de 5 partidos de hoy, de ayer y de maniana
     */
    public function getproximosAction() {
        try {
            $m = new Application_Model_Matchs();

            $result['body']['hoy'] = $m->hoy();
            $result['body']['datahoy'] = date('Y-m-d');

            $result['body']['maniana'] = $m->manana();
            $result['body']['datamaniana'] = date('Y-m-d', strtotime('+1 day', strtotime(date("r"))));

            $result['body']['ayer'] = $m->ayer();
            $result['body']['dataayer'] = date('Y-m-d', strtotime('-1 day', strtotime(date("r"))));

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * GET
     * Devuelve los resultados o proximos partidos 
     * @param idCampeonato
     * @param tag especifica si es de ayer, hoy, o resultados, o partidos futuros.
     */
    public function getpostAction() {
        try {
            $params = $this->getRequest()->getParams();

            $m = new Application_Model_Matchs();

            $tag = $params['tag'];
            $campeonato = $params['idCampeonato'];

            $date = $this->getDateFromTag($tag);

            if (!empty($campeonato)) {
                $result['body']['partidos'] = $m->jogosByCampeonatoAndDate($date, $campeonato);
            } else {
                $result['body']['partidos'] = $m->jogosByDate($date);
            }

            $result['body']['post'] = $m->getTitulo($campeonato,$params['tag']);

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * Retorna la fecha dependiendo de si se quiere de hoy, maniana o ayer
     */
    private function getDateFromTag($tag) {
        if (strcmp($tag, "H") == 0) {
            return date("Y-m-d");
        }

        if (strcmp($tag, "A") == 0) {
            return date('Y-m-d', strtotime('-1 day', strtotime(date("r"))));
        }
        
        if (strcmp($tag, "M") == 0) {
            return date('Y-m-d', strtotime('+1 day', strtotime(date("r"))));
        }
        
    }

    public function getjogosbycampeonatoanddateAction() {
        try {
            $params = $this->getRequest()->getParams();

            $date = date('Y-m-d');            

            $m = new Application_Model_Matchs();

            $result['body'] = $m->jogosByCampeonatoAndDate($date, $params['idCampeonato']);
            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }        
    }

    /**
     * GET
     * devuelve los ultimos palpites del sistema, 15 ultimos
     */
    public function getultimospalpitesAction() {
        try {
            $r = new Application_Model_Result();
            $result['body'] = $r->getUltimosPalpites();

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());   
        }
    }

    /**
     * GET
     * devuelve todos los partidos de una fecha determinada
     */
    public function bydateAction() {
        try {

            $params = $this->getRequest()->getParams();
            $date = $params['date'];

            $m = new Application_Model_Matchs();

            $result['body'] = $m->jogosByDate($date);

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage()); 
        }
    }

    public function posttesteAction() {
        try {

            $r = new Application_Model_Result();
            $result['body'] = $r->testepost();

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage()); 
        }
    }


    /**
     * Palpita toda la rodada.
     * Envia todos los partidos de una rododa con sus resultados.
     * Crea la tupla del nuevo partido o actualiza si ya fue palpitado el partido.
     * 
     * En formato de array recibe
     * @param partidos son todos los palpites realizados
     */
	public function palpitarAction() {
        try {

            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);

            $jogos = $data['partidos'];
            $usuario = $this->getId();

            $results = new Application_Model_Result();
            for ($i = 0; $i < count($jogos); $i = $i + 1) {
                $jogo = $jogos[$i];

                $jogo['rs_res1'] = strcmp($jogo['rs_res1'],"") == 0 ? null : $jogo['rs_res1'];
                $jogo['rs_res2'] = strcmp($jogo['rs_res2'],"") == 0 ? null : $jogo['rs_res2'];        

                $results->palpitar($jogo, $usuario);
            }

            $this->_helper->json(200);
        
        } catch(Zend_Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }
    
}
