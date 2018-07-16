<?php

/**
 * 
 */
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH."/helpers/html.php";
include APPLICATION_PATH."/helpers/translate.php";
include APPLICATION_PATH.'/helpers/box.php';
include APPLICATION_PATH.'/helpers/mail.php';
class EquiposController extends Zend_Controller_Action
{

    /**
     * GET
     * Info del equipo
     * @param idEquipo
     */
    public function getAction() {
        try {
            $param = $this->getRequest()->getParams();
            $idEquipo = $param['idEquipo'];

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $e = new Application_Model_Equipo();
            $result['body']['equipo'] = $e->loadById($idEquipo);
            $result['body']['campeonatos'] = $e->loadCampeonatos($idEquipo);
            $result['body']['jogos'] = $e->loadNextJogos($idEquipo, 3);
            
            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * GET
     * Retorna la tabla de posicion del campeonato.
     * La lista de equipos ordenada por posicion y/o grupo
     * 
     * @param idCampeonato
     */
    public function getbycampeonatoAction() {
        try {
            $body = $this->getRequest()->getParams();
            $champ_id = $body['idCampeonato'];

            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 

            $novo_teams = array();

            if (!empty($teams[0]['ec_grupo'])) {
                $tem_grupo = true;
                $grupo = $teams[0]['ec_grupo'];
                $j = 0;
                $k = 0;
                for ($i = 0; $i < sizeof($teams); $i = $i + 1) {
                    if (strcmp($grupo, $teams[$i]['ec_grupo']) != 0) {
                        $grupo = $teams[$i]['ec_grupo'];
                        $j = $j + 1;
                        $k = 0;
                    } 
                    $teams[$i]['tem_grupo'] = true;
                    $novo_teams[$j]['tem_grupo'] = true;
                    $novo_teams[$j]['ec_grupo'] = $teams[$i]['ec_grupo'];
                    $novo_teams[$j]["grupo"][$k] = $teams[$i];
                    $k = $k + 1;
                }
                $teams = $novo_teams;
            } else {
                for ($i = 0; $i < sizeof($teams); $i = $i + 1) {
                    $teams[$i]['tem_grupo'] = false;
                }
            }

            $result['body']['teams'] = $teams;
        
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            
            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * 
     * @param idEquipo
     * @param limite
     */
    public function getpartidosAction() {
        try {
            $param = $this->getRequest()->getParams();
            $idEquipo = $param['idEquipo'];
            $limite = $param['limite'];

            $e = new Application_Model_Equipo();
            $result['body']['jogos'] = $e->loadNextJogos($idEquipo, $limite);

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            
            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * GET
     * Retorna todos los equipos del pais 
     * @param idPais
     */
    public function gettimesAction() {
        try {
            $body = $this->getRequest()->getParams();
            $idPais = $body['idPais'];

            $e = new Application_Model_Equipo();
            $result['body'] = $e->gettimes($idPais);

            $this->_helper->json($result);

        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

}