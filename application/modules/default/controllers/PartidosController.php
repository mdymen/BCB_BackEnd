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
class PartidosController extends Zend_Controller_Action
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

            for ($i = 0; $i < count($resultados); $i = $i + 1) {
                $resultado = $resultados[$i];

                $match = array(
                    "mt_id" => $resultado['mt_id'],
                    "mt_goal1" => $resultado['mt_goal1'],
                    "mt_goal2" => $resultado['mt_goal2'],
                    "mt_idteam1" => $resultado['mt_idteam1'],
                    "mt_idteam2" => $resultado['mt_idteam2'],
                    "mt_idchampionship" => $resultado['mt_idchampionship'],
                    "mt_played" => $resultado['mt_played'],
                    "mt_date" => $resultado['mt_date'],
                    "mt_idround" => $resultado['mt_idround']
                );

                $idMatch = $m->save($match);

                //Si el partido está marcado como NO JUGADO pero se están pasando los goles
                //quiere decir que se tiene que procesar como NUEVO PARTIDO JUGADO
                if (strcmp($resultado['mt_played'], "0") == 0 
                    && strcmp($resultado['played'], "1") == 0 
                    && !is_null($resultado['mt_goal1']) 
                    && !is_null($resultado['mt_goal2'])) {                       

                    //verifica el resultado del partido y setea los puntos
                    //y setea al partido como jugado
                    $r->verificarGanadores($resultado['mt_idteam1'], $resultado['mt_idteam2'], $resultado['mt_goal1'], $resultado['mt_goal2'], $idMatch);
                    
                    //verifica los usuarios ganadores y setea los puntos
                    $this->usuariosGanadores($resultado['mt_goal1'], $resultado['mt_goal2'], $idMatch);

                }
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

}
