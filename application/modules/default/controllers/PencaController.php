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
include APPLICATION_PATH.'/models/transaction.php';
include APPLICATION_PATH.'/helpers/html.php';
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/helpers/box.php';
include APPLICATION_PATH.'/helpers/posicoes.php';
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
    
    public function palpitesAction() {
        $params = $this->_request->getParams();
        
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load();
        
        $teams_obj = new Application_Model_Teams();
        $teams = $teams_obj->load_teams_championship($params['champ']); 

        $this->view->teams = $teams;
        
        $match = $params['match'];
        
        $m = new Application_Model_Matchs();
        $results = $m->result_matchs($match);
        
        $quanto = $m->get_quantidade_palpites($match);
        
//        print_r("results");
//        print_r($results);
        

        
        for ($i = 0; $i < count($quanto); $i = $i + 1) {
            for ($j = 0; $j < count($results); $j = $j + 1) {
//                $results[$i]['rs_id'] = "x";
//                $results[$i]['rs_res1'] = $quanto[$j]['rs_res1'];
//                $results[$i]['rs_res2'] = $quanto[$j]['rs_res2'];
//                $results[$i]['rs_idmatch'] = $quanto[$j]['rs_idmatch'];
//
//                $results[$i]['tm1_id'] = $quanto[$j]['tm1_id'];
//                $results[$i]['tm1_logo'] = $quanto[$j]['tm1_logo'];
//                $results[$i]['t1nome'] = $quanto[$j]['t1nome'];
//
//                $results[$i]['tm2_id'] = $quanto[$j]['tm2_id'];
//                $results[$i]['tm2_logo'] = $quanto[$j]['tm2_logo'];
//                $results[$i]['t2nome'] = $quanto[$j]['t2nome'];
//
//                $results[$i]['rs_result'] = "";
//                $results[$i]['quantidade'] = $quanto[$i]['quantidade'];
                $quanto[$i]['mt_id'] = $results[$j]['mt_id'];
                $quanto[$i]['mt_idteam1'] = $results[$j]['mt_idteam1'];
                $quanto[$i]['mt_idteam2'] = $results[$j]['mt_idteam2'];
                $quanto[$i]['mt_date'] = $results[$j]['mt_date'];
                $quanto[$i]['mt_goal1'] = $results[$j]['mt_goal1'];
                $quanto[$i]['mt_goal2'] = $results[$j]['mt_goal2'];
                $quanto[$i]['mt_idchampionship'] = $results[$j]['mt_idchampionship'];
                $quanto[$i]['mt_round'] = $results[$j]['mt_round'];
                $quanto[$i]['mt_played'] = $results[$j]['mt_played'];
                $quanto[$i]['mt_acumulado'] = $results[$j]['mt_acumulado'];
                $quanto[$i]['mt_idround'] = $results[$j]['mt_idround'];
            }

        }
        
////        print_r($results);
//        print_r("<br> quanto");
//        print_r($quanto);
        
        $this->view->results = $quanto;
        $this->view->champ = $params['champ'];
        $this->view->championship = $champ->getChamp($params['champ']);
        
    }
    
    public function bolaoAction() {
        $params = $this->_request->getParams();
                    
//        print_r($params);
        
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
           

            $storage = new Zend_Auth_Storage_Session();
            $data = (get_object_vars($storage->read()));

            $matchs_obj = new Application_Model_Matchs();
            $rondas = $matchs_obj->getrondas($champ_id);

            
            
            if (empty($params['team'])) {
                $rodada = $matchs_obj->load_rodada_com_palpites($champ_id, $rodada_id, $data['us_id']);
                $this->view->porteam = true;
                $this->view->porrodada = false;
            } else {
                $this->view->porteam = false;
                $this->view->porrodada = true;
                $team_id = $params['team'];
                $rodada = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $data['us_id']);
            }

            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 
            
            $this->view->teams = $teams;
            
            //los partidos de la rodada n_rodada
            $this->view->rodada = $rodada;
            
            //el numero de la rodada activa. La que siguiente inmediata que se va a jugar
            $this->view->n_rodada = $rodada_id;
            
            //las rodadas del campeonato registradas en el sistema
            $this->view->rondas = $rondas;          
        }
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
        $champ = $params['champ'];
        
        $temSaldo = $this->verificarsaldo();
        if ($temSaldo) {

            $champs = new Application_Model_Championships();
            $champ_obj = $champs->getChamp($champ);
            
            $matchs_obj = new Application_Model_Matchs();     
            $id = $matchs_obj->submeter_result($user_id, $result1, $result2, $match_id, $round);
            
            $penca = new Application_Model_Penca();
            $transaction = $penca->setMatch((-1)*$champ_obj['ch_dpalpite'], $champ_obj['ch_dchamp'], 
                    $champ, $this->getIdUser(), $champ_obj['ch_drodada'], 
                    $round, $champ_obj['ch_djogo'], $match_id, 'null');           
   
            $result_obj = new Application_Model_Result();    
            $result = $result_obj->getResult($id);

            $result['sucesso'] = 200;
            $result['total'] = $transaction['tr_res_rd_acumulado'];
            $result['total_usuario'] = $transaction['tr_res_us_cash'];
            $result['total_match'] = $transaction['tr_res_mt_acumulado'];
            $result['total_campeonato'] = $transaction['tr_res_ch_acumulado'];
  
            $this->login();
   
        } else {
            $result['sucesso'] = 401;
        }
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
    }
    
    public function excluirpalpiteAction() {
        $params = $this->_request->getParams();
        
        $result = $params['result'];
        $round = $params['round'];
        $champ = $params['champ'];
        $match_id = $params['match'];      
        
        $champs = new Application_Model_Championships();
        $champ_obj = $champs->getChamp($champ);
        
        $penca = new Application_Model_Penca();
        $transaction = $penca->setMatch($champ_obj['ch_dpalpite'], (-1)*$champ_obj['ch_dchamp'], 
                $champ, $this->getIdUser(), (-1)*$champ_obj['ch_drodada'], 
                $round, (-1)*$champ_obj['ch_djogo'], $match_id, $result);

        $return = array();        
        $return['total'] = $transaction['tr_res_rd_acumulado'];
        $return['total_usuario'] = $transaction['tr_res_us_cash'];
        $return['total_match'] = $transaction['tr_res_mt_acumulado'];
        $result['total_campeonato'] = $transaction['tr_res_ch_acumulado'];
        
        $this->login();
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($return);
                
    }
    
    public function meuspalpitesAction() { 
        try {
            $params = $this->_request->getParams();

//            print_r($params);
            
            $penca = new Application_Model_Penca();
            
            $id_user = $this->getIdUser();
            $champs = $penca->load_championship_with_results($id_user);

            if (!empty($params['champ'])) {
                
                $this->view->teamuserid = $this->getTimeUserId();
                $this->view->teamusername = $this->getTimeUserName();            
                
                $matchs_obj = new Application_Model_Matchs();
                $rondas = $matchs_obj->getrondas($params['champ']);
                $this->view->rondas = $rondas;
                $this->view->champ = $params['champ'];                                

                if (empty($params['rodada'])) {
                    $rodada_id = $penca->getIdPrimeraRodadaDisponivel($params['champ']);
                } else {            
                    $rodada_id = $params['rodada'];
                }
                
                $palpites_da_rodada = $matchs_obj->load_palpites_simples($params['champ'], $rodada_id, $this->getIdUser());
                $this->view->palpites = $palpites_da_rodada;
                $this->view->n_rodada = $rodada_id;
                
            }

            
            $this->view->championships = $champs;
        }
        catch (Zend_Exception $e) {
            $config = new Zend_Config_Ini("config.ini");
            $this->redirect($config->hostpublic);
        }
    }
    
    public function getpalpitesAction() {
        $params = $this->_request->getParams();
//        
        $champ_id = $params['champ'];
        $rodada_id = $params['ronda'];
//        
//        $penca = new Application_Model_Penca();
//        $results = $penca->getpalpites($this->getIdUser());
        
        $d_helper = new Helpers_Data();
        
        $matchs_obj = new Application_Model_Matchs();
        $palpites_da_rodada = $matchs_obj->load_palpites_simples($champ_id, $rodada_id, $this->getIdUser());
//        
        for ($i = 0; $i < count($palpites_da_rodada); $i = $i + 1) {
            $palpites_da_rodada[$i]['mt_date'] = $d_helper->day($palpites_da_rodada[$i]['mt_date']);
        }
//        
//        print_r($palpites_da_rodada);
//        die(".");
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($palpites_da_rodada);
    }
    
    
    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_id'];
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
    
    public function testAction() {
        
        $penca = new Application_Model_Penca();
        $results = $penca->primera_rodada_disponible($this->getIdUser());
        
        print_r($results);
        die(".");
        
    }
    
    public function login() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        
        $user = $data["us_username"];
        $password = $data["us_password"];
        
        $users = new Application_Model_Users();
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'user');
        $authAdapter->setIdentityColumn('us_username')
                    ->setCredentialColumn('us_password');
        $authAdapter->setIdentity($user)
                    ->setCredential($password);

        $result = $auth->authenticate($authAdapter);
        
        if ($result->isValid()) {         
            $storage = new Zend_Auth_Storage_Session();
            $storage->write($authAdapter->getResultRowObject());
            
        }

    }
    
    private function setTimeCoracaoStorage($id, $name) {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        $data['us_team'] = $id;
        $data['us_teamname'] = $name;
//      
       // $storage->write(array('us_team' => $id, 'us_teamname' => $name));
//        Zend_Auth::getInstance()->getStorage()->write(
//        Zend_Auth::getInstance()->getStorage()->read(),
//        array('us_team' => $id, 'us_teamname' => $name));
//        
        $auth = Zend_Auth::getInstance();
        $auth->setStorage($data);
        
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        print_r($data);
        die(".");
        
    }
    
    public function edittimecoracaoAction() {
        $params = $this->_request->getParams();
        
        $id = $params['idteam'];
        $name = $params['nameteam'];
        
        $user = new Application_Model_Users();
        $user->setTeamCoracao($id, $name, $this->getIdUser());
        
        $this->login();
        
       // $this->setTimeCoracaoStorage($id, $name);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }
    
    public function rankingAction() {
        $params = $this->_request->getParams();
        
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load();
        
        
        if (!empty($params['championship'])) {
            
            $ranking = $champ->ranking($params['championship']);
            $this->view->ranking = $ranking;
            $this->view->champ = $params['championship'];
        }

    }
    
    private function verificarsaldo() {        
        $u = new Application_Model_Users();
        $mycash = $u->getDinheiro($this->getIdUser());
        
        $total = floatval(2.5);
        
        $res = false;
        if ($mycash >= $total) {
            $res = true;
        }
        
        return $res;
        
    }

    /*
     * Envia por JSON a lista de ganadores de un determinado partido
     */
    public function ganadoresjogoAction() {
        $params = $this->_request->getParams();
        
        $jogo = $params['match_id'];
        
        $r = new Application_Model_Result();
        $ganadores = $r->ganadores_match($jogo);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($ganadores);
    }
}
