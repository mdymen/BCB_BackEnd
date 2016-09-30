<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PencaController
 *
 * @author Martin Dymenstein
 */
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/matchs.php';
include APPLICATION_PATH.'/models/result.php';
class PencaController extends Zend_Controller_Action {
    
    public function indexAction() {
        $params = $this->_request->getParams();
        $id_penca = $params['penca'];
        
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        $penca = new Application_Model_Penca();
        $participantes = $penca->load_participantes($id_penca);
        
        $info_penca = $penca->load_penca($id_penca);
        $palpites = $penca->palpites($id_penca, 1, $data['us_id']);
        $rodada = $penca->rodada($info_penca[0]['pn_idchampionship'], $info_penca[0]['ch_atualround']);
        
        $teams = new Application_Model_Teams();
        $teams = $teams->load_penca_limit($info_penca[0]['pn_idchampionship'], 1); 
        
        $this->view->info_penca = $info_penca;
        $this->view->teams = $teams;
        $this->view->participantes = $participantes;
        $this->view->palpites = $palpites;
        $this->view->rodada = $rodada;
        $this->view->penca = $id_penca;
        $this->view->championship = $info_penca[0]['pn_idchampionship'];
        $this->view->is_iscripto = $penca->isIscriptoEmPenca($data['us_id'], $id_penca);
    }
    
    public function listAction() {
        
    }
    
    public function sairpencaAction() {
        $params = $this->_request->getParams();
        
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
           
        $p = new Application_Model_Penca();
        $p->sair_penca($data['us_id'], $params['penca']);
        
        $this->redirect("/penca/index?penca=".$params['penca']);
        
    }
    
    public function proximostimesAction() {
        $params = $this->_request->getParams();
        
        //$proximos = $params['proximos'];
        $champ = $params['champ'];
        $pagina = $params['pagina'];
        
        $teams = new Application_Model_Teams();
        $teams = $teams->load_penca_limit($champ, $pagina); 
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($teams);
    }
    
    public function usuariospencaAction() {
        $params = $this->_request->getParams();
//       print_r($params);
//       die(".");
        $id_penca = $params['penca'];
        
//        print_r($id_penca);
//        die(".");
//        
        $penca = new Application_Model_Penca();
        $usuarios = $penca->load_usuarios($id_penca);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($usuarios);
    }
    
    /*
     * Cuando un usuario se inscribe a una penca se 
     * graban en una tabla todos los partidos para que 
     * la persona pueda escribir los resultados
     * que estima.
     */
    public function inscribirAction() {
        $params = $this->_request->getParams();
        
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        $penca = $params['penca'];
        $championship = $params['championship'];
        
        $matchs_obj = new Application_Model_Matchs();
        $matchs = $matchs_obj->load($championship);
        
//            'up_idpenca' => $params['up_idpenca'],
//            'up_iduser' => $params['up_iduser'],
        
        $p = new Application_Model_Penca();
        $p->save_userpenca(array('up_idpenca' => $penca, 'up_iduser' => $data['us_id']));
        
        for ($i = 0; $i < count($matchs); $i = $i + 1) {
            $matchs_obj->save_penca_match(
                    array(
                        'idmatch' => $matchs[$i]['mt_id'],
                        'idpenca' => $penca,
                        'iduser' => $data['us_id'],
                        'date' => $matchs[$i]['mt_date'],
                        'round' => $matchs[$i]['mt_round']
                    )
            );
        } 
        
        $this->redirect("/penca/index?penca=".$params['penca']);
    }
    
    public function aceitarpalpitesAction() {
        $params = $this->_request->getParams();
        
        $result = new Application_Model_Result();
        
        $n_palpites = $params['count_palpites'];
        for ($i = 0; $i < $n_palpites; $i = $i + 1) {
            $dados = array( 
                'res1' => $params['result1_'.$i],
                'res2' => $params['result2_'.$i],
                'rs_id' => $params['rs_'.$i],
            
            );
            $result->update($dados);
        }
        
        print_r($params);
        die(".");
    }
    
    public function pencasAction() {
        $param = $this->_request->getParams();
       
        $champ = new Application_Model_Championships();
        $pencas = new Application_Model_Penca();
        
        $this->view->championships = $champ->load();
        
        if (!empty($param['championship'])) {
            $this->view->pencas = $pencas->load_pencas_byChamp($param['championship']);
        }
    }
    
    public function pencatimesAction() {
        $params = $this->_request->getParams();
        
        
        $teams = new Application_Model_Teams();
     //   $teams = $teams->load_penca_limit($params[0]['pn_idchampionship'],); 
        
    }
    
    public function meusbaloesAction() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        $pencas = new Application_Model_Penca();

        $penca = $pencas->load_pencas_incripto_usuario($data['us_id']);   
        
        $this->view->pencas = $penca;
    }
    
    public function getrodadaAction() { 
        $params = $this->_request->getParams(); 
        
        $penca = new Application_Model_Penca();

        $rodada = $penca->rodada($params['champ'], $params['round']);
        
                $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($rodada);
        
    }
    
    
    public function proximopalpiteAction() {
        $params = $this->_request->getParams();
        
//        print_r("HOLA");
//        die("..");
   
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        $champ = $params['champ'];
        $round = $params['round'];
        $id_penca = $params['penca'];
        
        $penca = new Application_Model_Penca();
        
        $palpites = $penca->palpites($id_penca, $round, $data['us_id']);
        
         $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($palpites);
    }
    
    public function bolaoAction() {
        $params = $this->_request->getParams();
        
        $rodada_id = $params['rodada'];
        $champ_id = $params['champ'];
        
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        $matchs_obj = new Application_Model_Matchs();
        
        $rodadas = $matchs_obj->load_rodada($champ_id, $rodada_id, $data['us_id']);
        $palpites_da_rodada = $matchs_obj->load_palpites_simples($champ_id, $rodada_id, $data['us_id']);
        
        $teams_obj = new Application_Model_Teams();
        $teams = $teams_obj->load_teams_championship($champ_id); 
        
        for ($i = 0; $i < count($rodadas); $i = $i + 1) {
            $e = false;
            $c = 0;
            for ($j = 0; $j < count($palpites_da_rodada); $j = $j + 1) {
                if ($palpites_da_rodada[$j]['rs_idmatch'] == $rodadas[$i]['mt_id']) {
                  $e = true;  
                }
                $c = $c + 1;
            }
            
            if (!$e) {
                $palpites_da_rodada[$j]['rs_id'] = -1;
                $palpites_da_rodada[$j]['rs_idmatch'] = $rodadas[$c]['mt_id'];
                $palpites_da_rodada[$j]['rs_res1'] = "";
                $palpites_da_rodada[$j]['rs_res2'] = "";
                $palpites_da_rodada[$j]['rs_date'] = "";
                $palpites_da_rodada[$j]['rs_idpenca'] = "";
                $palpites_da_rodada[$j]['rs_iduser'] = "";
                $palpites_da_rodada[$j]['rs_round'] = "";
                $palpites_da_rodada[$j]['rs_result'] = "";
                $palpites_da_rodada[$j]['rs_points'] = "";
                $palpites_da_rodada[$j]['mt_id'] = $rodadas[$c]['mt_id'];
                $palpites_da_rodada[$j]['mt_idteam1'] = $rodadas[$c]['mt_idteam1'];
                $palpites_da_rodada[$j]['mt_date'] = $rodadas[$c]['mt_idteam1'];
                $palpites_da_rodada[$j]['mt_goal1'] = $rodadas[$c]['mt_goal1'];
                $palpites_da_rodada[$j]['mt_goal2'] = $rodadas[$c]['mt_goal2'];
                $palpites_da_rodada[$j]['mt_idchampionship'] = $rodadas[$c]['mt_idchampionship'];
                $palpites_da_rodada[$j]['mt_round'] = $rodadas[$c]['mt_round'];
                $palpites_da_rodada[$j]['mt_played'] = $rodadas[$c]['mt_played'];
                $palpites_da_rodada[$j]['t1nome'] = $rodadas[$c]['t1nome'];
                $palpites_da_rodada[$j]['t2nome'] = $rodadas[$c]['t2nome'];

                               
            }
        }
        
        $this->view->teams = $teams;
        $this->view->rodadas = $rodadas;
        $this->view->palpites = $palpites_da_rodada;
        
//        print_r($palpites_da_rodada);
////        print_r($teams);
//        
//        die(".");
        
    }
    
    public function submeterpalpiteAction() {
        $params = $this->_request->getParams();
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));

        $result1 = $params['result1'];
        $result2 = $params['result2'];
        $user_id = $data['us_id'];
        $match_id = $params['match'];
        $round = $params['round'];
        
        $matchs_obj = new Application_Model_Matchs();     
        $id = $matchs_obj->submeter_result($user_id, $result1, $result2, $match_id, $round);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($id);
    }
    
    public function excluirpalpiteAction() {
        $params = $this->_request->getParams();
        
        $result = $params['result'];
        $matchs_obj = new Application_Model_Matchs();   
        $r = $matchs_obj->result($result);
        $matchs_obj->delete_palpite($result);   
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($r);
                
    }
}
