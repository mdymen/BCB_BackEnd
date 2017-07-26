<?php
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/pencas.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH."/helpers/html.php";
include APPLICATION_PATH."/helpers/translate.php";
include APPLICATION_PATH.'/helpers/box.php';
include APPLICATION_PATH.'/helpers/mail.php';
//require_once("phpmailer/class.phpmailer.php");
class MobileController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_id'];
    }
    
    public function getEmailUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_email'];
    }    
    
    public function getUserData() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data;
    }
 
    function logoutAction() {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        $this->_redirect();
    }           
    
    public function loginAction() {
        $params = $this->_request->getParams();
        
        $user = $params["username"];
        $password = $params["password"];
        
        $error = false;
        
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
            $this->_redirect('/index');    
        } else {
            $error = true;
            $this->_redirect('/index?error=yes');
        }

    }
    
    public function celltestingAction() {
     	
        "error";
        
//		$this->getResponse()
//				 ->setHeader('Content-Type', 'application/json');			
//		
//        $this->_helper->layout->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(TRUE);        
//		
//        $this->_helper->json("TESTING"); 
    }    
   
    public function celloginAction() {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        
		//$data = $this->_request->getParams();

		if (empty($data['us']) or empty($data['pass'])) {
			$user = false;
		} else {		
			$u = new Application_Model_Users();
			$user = $u->login($data['us'],$data['pass']);
		}
		
		$this->getResponse()
				 ->setHeader('Content-Type', 'application/json');			
		
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);        
		
        $this->_helper->json($user); 
    }
    
    public function cellgetcampeonatosAction() {
        $c = new Application_Model_Championships();
        
        $champs = $c->getcampeonatos();
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($champs);
    }
    
    public function cellgetcampeonatosabertosAction() {
        $c = new Application_Model_Championships();
        
        $champs = $c->load();
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($champs);
    }    
    
    public function celcadastroAction() {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        
        $user['us_username'] = $data['username'];
        $user['us_password'] = $data['password'];
        $user['us_email'] = $data['email'];
        $user['us_grito'] = $data['grito'];
        
//        print_r($data['niver']);
//        die(".");
        
        $niver = explode("-", $data['niver']);
//        
        $segundo = explode("T", $niver[2]);
        $user['us_dia_niver'] = $segundo[0];
        $user['us_mes_niver'] = $niver[1];
        $user['us_anio_niver'] = $niver[0];
        
        $u = new Application_Model_Users();
        
        $return = 200;
        if ($u->existUserName($user['us_username'])) {
            $return = 401;
        } else {
            $u->save_user($user);
        }
        
        
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($return);
    }
    
    public function celpalpitar() {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        
        $result1 = $data['result1'];
        $result2 = $data['result2'];
        $user_id = $data['us_id'];
        $match_id = $data['match'];
        $round = $data['round'];
        $champ = $data['champ'];
        
        $champs = new Application_Model_Championships();
        $champ_obj = $champs->getChamp($champ);
        
        $temSaldo = $this->verificarsaldo($champ_obj, $user_id);
        if ($temSaldo) {

            
            
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
        } else {
            $result['sucesso'] = 401;
        }
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
        
    }
    
    public function celgetrankingrodadaAction() {
        
    }
    
    public function celgetrankingcampeonatoAction() {
        
    }
    
   
    public function celexcluirpalpiteAction() {
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
        $return['total_campeonato'] = $transaction['tr_res_ch_acumulado'];
        
        
//        print_r($return);
        
        
        $this->login();
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($return);
                
    }
    
    public function login1($usuario, $pass) {
        //$params = $this->_request->getParams();
        
        $user = $usuario;
        $password = $pass;
        
//        print_r($params);
//        die(".");
        
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
        
//        $this->_redirect('/index');
    }
    
    public function celproximojogoslimitAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
        
        $us_id = $params['us_id'];
        
        $limit = 0;
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }
            
        
        $m = new Application_Model_Matchs();
        $matchs = $m->proximos_jogos_offset($us_id, $limit, 12);

        $this->view->matchs = $matchs;
            
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($matchs);      
    }
    
    public function celproximojogosAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
        
        $us_id = $params['us_id'];
        
        $limit = 0;
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }
            
        
        $m = new Application_Model_Matchs();
        $matchs = $m->proximos_jogos($us_id, 12);

        $this->view->matchs = $matchs;
            
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($matchs);      
    }
    
    public function celmeuspalpitesnaojogadosAction() {
        
    }
    
    public function celaddprovisorioAction() {
        
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
            
        $us = $data['us'];
        $pass = $data['pass'];
        
        $u = new Application_Model_Users();
        $u->addprovisorio($us, $pass);
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(1); 
        
    }
    
    public function celloginhashAction() {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
            
        $us = $data['us'];
        $pass = $data['pass'];
        
         $u = new Application_Model_Users();
        $result = $u->login($us, $pass);
        
		$hash = "";
		if (!empty($result)) {
			$hash = md5(uniqid(rand(), TRUE));
		} else {
			$hash = false;
		}
		
		
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($hash); 
    }
 

    private function verificarsaldo($champ, $us_id) {        
        $u = new Application_Model_Users();
        $mycash = $u->getDinheiro($us_id);
        
        $total = floatval($champ['ch_dpalpite']);
        $mycash = $mycash['us_cash'];
        
        $res = false;
        if ($mycash >= $total) {
            $res = true;
        }
        
        return $res;
        
    } 
 
    public function cellsubmeterpalpiteAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);		

        // $storage = new Zend_Auth_Storage_Session();
        // $data = (get_object_vars($storage->read()));

        $result1 = $params['result1'];
        $result2 = $params['result2'];
        $user_id = $params['us_id'];
        $match_id = $params['match'];
        $round = $params['round'];
        $champ = $params['champ'];
        $penca = $params['pn_id'];
//        
//        print_r($round);
//        die(".");
        //Si esta palpitando un partido que no pertenece a una penca
        if (empty($penca)) {
        
            $champs = new Application_Model_Championships();
            $champ_obj = $champs->getChamp($champ);

            $temSaldo = $this->verificarsaldo($champ_obj, $user_id);
            if ($temSaldo) {



                $matchs_obj = new Application_Model_Matchs();     

                            $match_ja_palpitado = $matchs_obj->match_ja_palpitado($match_id, $user_id);

                            if (!$match_ja_palpitado) {

                                    $id = $matchs_obj->submeter_result($user_id, $result1, $result2, $match_id, $round);

                                    $penca = new Application_Model_Penca();
                                    $transaction = $penca->setMatch((-1)*$champ_obj['ch_dpalpite'], $champ_obj['ch_dchamp'], 
                                                    $champ, $user_id, $champ_obj['ch_drodada'], 
                                                    $round, $champ_obj['ch_djogo'], $match_id, 'null');           

                                    $result_obj = new Application_Model_Result();    
                                    $result = $result_obj->getResult($id);

                                    $result['sucesso'] = 200;
                                    $result['total'] = $transaction['tr_res_rd_acumulado'];
                                    $result['total_usuario'] = $transaction['tr_res_us_cash'];
                                    $result['total_match'] = $transaction['tr_res_mt_acumulado'];
                                    $result['total_campeonato'] = $transaction['tr_res_ch_acumulado'];

                //$this->login();
                            } else {

                                    $matchs_obj->alterar_result($user_id, $result1, $result2, $match_id, $round);

                                    $result_obj = new Application_Model_Result();    
                                    $result = $result_obj->getResultByUserMatch($user_id, $match_id);

                                    $result['sucesso'] = 402;
                            }
            } else {
                $result['sucesso'] = 401;
            }
        
        } else {
            $matchs_obj = new Application_Model_Matchs();
            $result_obj = new Application_Model_Result();  
                            
            $match_ja_palpitado = $matchs_obj->match_ja_palpitado_penca($match_id, $user_id, $penca);                        
                       
            if (!$match_ja_palpitado) {
                $dados = array(
                    'rs_idmatch' => $match_id,
                    'rs_idpenca' => $penca,
                    'rs_iduser' => $user_id,
                    'rs_res1' => $result1,
                    'rs_res2' => $result2,
                    'rs_date' => date("Y-m-d H:i:s"),
                    'rs_round' => $round
                );
                
                $id = $matchs_obj->save_penca_match($dados);

                $result = $result_obj->getResult($id);

                $result['sucesso'] = 200;
            } else {
                $dados = array(
                    'rs_res1' => $result1,
                    'rs_res2' => $result2,
                    'rs_date' => date("Y-m-d H:i:s")
                );
                
                $matchs_obj->update_penca_match($dados, $match_id, $user_id, $penca);
                $result = $result_obj->get_result_by_match_user_penca($match_id, $user_id, $penca);
                
                $result['sucesso'] = 402;
            }
        }
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
    } 
	
    public function getpencasdisponiveisAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
        
        $u = new Application_Model_Users();
        $pencas = $u->getPencasDisponiveis($params['iduser']);
        
                $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($pencas);
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
    
    
    
	public function cellbolaopencaAction() {
		
		// $params = $this->_request->getParams();
		
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	  
	
       // print_r($params);
        
        $champ = new Application_Model_Championships();
        $result['championships']= $champ->load();
		
		$id = $params['id'];
                
           
           
           
        $idpenca = $params['idpenca'];
        $p_obj = new Application_Model_Penca();
        
        if ($p_obj->estaAsociadoALaPenca($idpenca, $id)) {
            
            if (!empty($params['champ'])) {                      

             //   $result['teamuserid'] = $this->getTimeUserId();
               // $result['teamusername'] = $this->getTimeUserName();

                $champ_id = $params['champ'];

                $result['champ'] = $champ_id;
                $result['championship'] = $champ->getChamp($champ_id);

                if (empty($params['rodada'])) {
                    $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
                } else {            
                    $rodada_id = $params['rodada'];
                }

    //            print_r("Champ ".$params['champ']);
    //            print_r("Rodada ".$rodada_id);

                $storage = new Zend_Auth_Storage_Session();

                $matchs_obj = new Application_Model_Matchs();
                $rondas = $matchs_obj->getrondas($champ_id);

                $tem_grupo = false;

                if (empty($params['team'])) {
                    $rodada = $matchs_obj->load_rodada_com_palpites_penca($champ_id, $rodada_id, $id, $params['idpenca']);
                    $result['porteam'] = true;
                    $result['porrodada'] = false;
                } else {
                    $result['porteam']  = false;
                    $$result['porrodada'] = true;
                    $team_id = $params['team'];
                    $rodada = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $id);
                }

                $teams_obj = new Application_Model_Teams();
                $teams = $teams_obj->load_teams_championship($champ_id); 

                            $novo_teams = array();


                            if (!empty($teams[0]['tm_grupo'])) {
                                    $tem_grupo = true;
                                    $grupo = $teams[0]['tm_grupo'];
                                    $j = 0;
                                    $k = 0;
                                    for ($i = 0; $i < sizeof($teams); $i = $i + 1) {
                                            if (strcmp($grupo, $teams[$i]['tm_grupo']) != 0) {
                                                    $grupo = $teams[$i]['tm_grupo'];
                                                    $j = $j + 1;
                                            } 
                                            $teams[$i]['tem_grupo'] = true;
                                            $novo_teams[$j]['tem_grupo'] = true;
                                            $novo_teams[$j]['tm_grupo'] = $teams[$i]['tm_grupo'];
                                            // $novo_teams[$j][$teams[$i]['tm_grupo']][$k] = $teams[$i];
                                            $novo_teams[$j]["grupo"][$k] = $teams[$i];
                                            $k = $k + 1;
                                    }
                                    $teams = $novo_teams;
                            } else {
                                    for ($i = 0; $i < sizeof($teams); $i = $i + 1) {
                                            $teams[$i]['tem_grupo'] = false;
                                    }
                            }

                $p = new Application_Model_Penca();
                $us_penca = $p->load_penca_users($params['idpenca']);
                            
                            
                $result['teams'] = $teams;

                //los partidos de la rodada n_rodada
                $result['rodada'] = $rodada;

                //el numero de la rodada activa. La que siguiente inmediata que se va a jugar
                $result['n_rodada'] = $rodada_id;

                //las rodadas del campeonato registradas en el sistema
                $result['rondas'] = $rondas;      

                            $result['tem_grupo'] = $tem_grupo;			
                 
                 $result['usuarios_penca'] = $us_penca;

            }
	
        }
        else {
              
            $result = "300";
                        
        }
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json($result);
    }

    
	
	public function cellbolaoAction() {
		
		// $params = $this->_request->getParams();
		
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	  
	
       // print_r($params);
        
        $champ = new Application_Model_Championships();
        $result['championships']= $champ->load();
		
		$id = $params['id'];
        
        $p_obj = new Application_Model_Penca();

        if (!empty($params['champ'])) {                      
            
         //   $result['teamuserid'] = $this->getTimeUserId();
           // $result['teamusername'] = $this->getTimeUserName();
            
            $champ_id = $params['champ'];

            $result['champ'] = $champ_id;
            $result['championship'] = $champ->getChamp($champ_id);
            
            if (empty($params['rodada'])) {
                $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
            } else {            
                $rodada_id = $params['rodada'];
            }
            
//            print_r("Champ ".$params['champ']);
//            print_r("Rodada ".$rodada_id);

            $storage = new Zend_Auth_Storage_Session();

            $matchs_obj = new Application_Model_Matchs();
            $rondas = $matchs_obj->getrondas($champ_id);

            $tem_grupo = false;
            
            if (empty($params['team'])) {
                $rodada = $matchs_obj->load_rodada_com_palpites($champ_id, $rodada_id, $id);
                $result['porteam'] = true;
                $result['porrodada'] = false;
            } else {
                $result['porteam']  = false;
                $$result['porrodada'] = true;
                $team_id = $params['team'];
                $rodada = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $id);
            }

            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 

			$novo_teams = array();
			
			
			if (!empty($teams[0]['tm_grupo'])) {
				$tem_grupo = true;
				$grupo = $teams[0]['tm_grupo'];
				$j = 0;
				$k = 0;
				for ($i = 0; $i < sizeof($teams); $i = $i + 1) {
					if (strcmp($grupo, $teams[$i]['tm_grupo']) != 0) {
						$grupo = $teams[$i]['tm_grupo'];
						$j = $j + 1;
					} 
					$teams[$i]['tem_grupo'] = true;
					$novo_teams[$j]['tem_grupo'] = true;
					$novo_teams[$j]['tm_grupo'] = $teams[$i]['tm_grupo'];
					// $novo_teams[$j][$teams[$i]['tm_grupo']][$k] = $teams[$i];
					$novo_teams[$j]["grupo"][$k] = $teams[$i];
					$k = $k + 1;
				}
				$teams = $novo_teams;
			} else {
				for ($i = 0; $i < sizeof($teams); $i = $i + 1) {
					$teams[$i]['tem_grupo'] = false;
				}
			}
			
            $result['teams'] = $teams;
            
            //los partidos de la rodada n_rodada
            $result['rodada'] = $rodada;
            
            //el numero de la rodada activa. La que siguiente inmediata que se va a jugar
            $result['n_rodada'] = $rodada_id;
            
            //las rodadas del campeonato registradas en el sistema
            $result['rondas'] = $rondas;      

			$result['tem_grupo'] = $tem_grupo;			
						
        }
		
		$this->getResponse()
			 ->setHeader('Content-Type', 'application/json');
			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			
			$this->_helper->json($result);
    }

	public function cellrankingcampeonatoAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	 
		
		//$params= $this->_request->getParams();
		
		$champ_id = $params['champ'];

		$ranking = new Application_Model_Result();
        $rankings_champ = $ranking->ranking_champ($champ_id);
	
			$this->getResponse()
		 ->setHeader('Content-Type', 'application/json');
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
		$this->_helper->json($rankings_champ);
		
	}
	
	public function cellrankingroundAction() {
		$body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
		
		//$params= $this->_request->getParams();
		
		$champ_id = $params['champ'];
		$round_id = $params['round'];
		
		$ranking = new Application_Model_Result();
        $rankings_round = $ranking->ranking_round($round_id, $champ_id);
	
			$this->getResponse()
		 ->setHeader('Content-Type', 'application/json');
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
		$this->_helper->json($rankings_round);
		
	}	
	
	public function cellmeuspalpitesAction() { 
//        try {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	

				// $params = $this->_request->getParams();
		
    //        print_r($params);
//die(".");            
            $penca = new Application_Model_Penca();
            
           // $id_user = $this->getIdUser();
			
			$id_user = $params['id'];
			
            $champs = $penca->load_championship_with_results($id_user);

            if (!empty($params['champ'])) {
                
//                $result['teamuserid'] = $this->getTimeUserId();
  //              $result['teamusername'] = $this->getTimeUserName();            
                
                $matchs_obj = new Application_Model_Matchs();
                $rondas = $matchs_obj->getrondas($params['champ']);
                $result['rondas'] = $rondas;
                $result['champ'] = $params['champ'];                                

                if (empty($params['rodada'])) {
                    $rodada_id = $penca->getIdPrimeraRodadaDisponivel($params['champ']);
                } else {            
                    $rodada_id = $params['rodada'];
                }
                
                $palpites_da_rodada = $matchs_obj->load_palpites_simples($params['champ'], $rodada_id, $id_user);
                $result['palpites'] = $palpites_da_rodada;
                $result['n_rodada'] = $rodada_id;
                
            }

            
            $result['championships'] = $champs;
//        }
//        catch (Zend_Exception $e) {
//            $config = new Zend_Config_Ini("config.ini");
//            $this->redirect($config->hostpublic);
//        }



		$this->getResponse()
			 ->setHeader('Content-Type', 'application/json');
			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			
			$this->_helper->json($result);
    }
	
	
	 public function cellexcluirpalpiteAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
        
        $result = $params['result'];
        $round = $params['round'];
        $champ = $params['champ'];
        $match_id = $params['match'];  
		$user_id = $params['user_id'];		
        
        $champs = new Application_Model_Championships();
        $champ_obj = $champs->getChamp($champ);
        
        $penca = new Application_Model_Penca();
        $transaction = $penca->setMatch($champ_obj['ch_dpalpite'], (-1)*$champ_obj['ch_dchamp'], 
                $champ, $user_id, (-1)*$champ_obj['ch_drodada'], 
                $round, (-1)*$champ_obj['ch_djogo'], $match_id, $result);
        
        $return = array();        
        $return['total'] = $transaction['tr_res_rd_acumulado'];
        $return['total_usuario'] = $transaction['tr_res_us_cash'];
        $return['total_match'] = $transaction['tr_res_mt_acumulado'];
        $return['total_campeonato'] = $transaction['tr_res_ch_acumulado'];
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($return);
                
    }
	
	
	public function cellteamAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
        
        $team_id = $params['team'];
        $champ = $params['champ'];
		$user_id = $params['us_id'];
        
        $ob_team = new Application_Model_Teams();
        
        $jogos = $ob_team->load_palpites_simples_porteam($champ, $team_id, $user_id);
        
        for ($i = 0; $i < count($jogos); $i = $i + 1) {
            if ($jogos[$i]['tm1_id'] == $team_id) {
                $team = $jogos[$i]['t1nome'];
            }
            if ($jogos[$i]['tm2_id'] == $team_id) {
                $team = $jogos[$i]['t2nome'];
            }
        }
		
		$this->getResponse()
         ->setHeader('Content-Type', 'application/json');        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
		$return['team'] = $team;
		$return['jogos'] = $jogos; 
		
        $this->_helper->json($return);

    }
    
	
	public function cellpalpitesAction() {
		$body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
		
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load();
        
        $teams_obj = new Application_Model_Teams();
        $teams = $teams_obj->load_teams_championship($params['champ']); 
        
        $match = $params['match'];
        
        $m = new Application_Model_Matchs();
        $results = $m->result_matchs($match);
        
        $quanto = $m->get_quantidade_palpites($match);

        for ($i = 0; $i < count($quanto); $i = $i + 1) {
            for ($j = 0; $j < count($results); $j = $j + 1) {
                $quanto[$i]['mt_id'] = $results[$j]['mt_id'];
                $quanto[$i]['mt_idteam1'] = $results[$j]['mt_idteam1'];
                $quanto[$i]['mt_idteam2'] = $results[$j]['mt_idteam2'];
                $quanto[$i]['mt_date'] = $results[$j]['mt_date'];
                $quanto[$i]['mt_goal1'] = $results[$j]['mt_goal1'];
                $quanto[$i]['mt_goal2'] = $results[$j]['mt_goal2'];
                $quanto[$i]['mt_idchampionship'] = $results[$j]['mt_idchampionship'];
                $quanto[$i]['mt_played'] = $results[$j]['mt_played'];
                $quanto[$i]['mt_acumulado'] = $results[$j]['mt_acumulado'];
                $quanto[$i]['mt_idround'] = $results[$j]['mt_idround'];
                $quanto[$i]['rd_round'] = $results[$j]['rd_round'];
            }

        }
        
		$result['results'] = $quanto;
		$result['champ'] = $params['champ'];
		$result['championship'] = $champ->getChamp($params['champ']);
		$result['teams'] = $teams;
		
		$this->getResponse()
         ->setHeader('Content-Type', 'application/json');        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        $this->_helper->json($result);
    }
	
	public function cellrankingusuarioAction() {
     	$body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
		
		$us_id = $params['us_id'];
		
		$r = new Application_Model_Result();
        $rankings = $r->rankings_champ_usuario($us_id);
		 
		$result['ranking'] = $rankings;
		
				$this->getResponse()
         ->setHeader('Content-Type', 'application/json');        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        $this->_helper->json($result);
	}
	
	 public function celltransacoesAction() {
		$body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
        
		
		$us_id = $params['us_id'];
		
        $c = new Application_Model_Championships();
        
        $t = new Application_Model_Transaction();
        $ts_credito = $t->getCampeonato("", $us_id, "", "CREDITO");
         $this->view->tr_credito = $ts_credito;
        if (!empty($params['campeonato'])) {
            
            $campeonato = $params['campeonato'];
            
            $rodada = "";
            if (strcmp($params['rodada'], "Vazio") != 0) {
                $this->view->round = $params['rodada'];
                $rodada = $params['rodada'];
            }
            
            $ts = $t->getCampeonato($campeonato, $us_id, $rodada,"JOGO");            
            
            $result['transactions'] = $ts;
            
            $rs = $c->getrondas($campeonato);          
            
            $result['rondas'] = $rs;
			$result['champ'] = $campeonato;
        }                
        
         //$r = new Application_Model_Result();
         //$rankings = $r->rankings_champ_usuario($us_id);
                
        // $this->view->ranking = $rankings;
        
        $champs = $c->load();
		
		$result['champs'] = $champs;
		//$result['ranking'] = $rankings;
		
				$this->getResponse()
         ->setHeader('Content-Type', 'application/json');        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        $this->_helper->json($result);
    }
	
	
	public function cellplanoAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	
        
        $plano = $params['p'];
        
        $preco = 0;
        $nome_plano = "";
        if ($plano == 1) {
            $preco = "0.50";
            $nome_plano = "Plano 1";
        } else if ($plano == 2) {
            $preco = "21.20";
            $nome_plano = "Plano 2";
        } else if ($plano == 3) {
            $preco = "52.40";
            $nome_plano = "Plano 3";
        } else if ($plano == 4) {
            $preco = "104.40";
            $nome_plano = "Plano 4";
        }
        
        $ch = curl_init();

        $data = array('token'=>'6363C4111D064931A0CC0F0330849143',
            'email'=>'martin@dymenstein.com',
            'currency' => 'BRL',
            'itemId1' => 0001,
            'itemDescription1'=> $nome_plano,
            'itemAmount1' => $preco,
            'itemQuantity1' =>1,
            'itemWeight1' => 1000
            );
 
        curl_setopt($ch, CURLOPT_URL,"https://ws.pagseguro.uol.com.br/v2/checkout");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);
        
//        print_r($server_output);
//        die(".");
        curl_close ($ch);
        
        $return = new SimpleXMLElement($server_output);
        $my_array = (array)$return->code;
        
        $codigo = $my_array[0];
        
				$this->getResponse()
			 ->setHeader('Content-Type', 'application/json');
			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			
			$result['url'] = "https://pagseguro.uol.com.br/v2/checkout/payment.html?code=".$codigo;
			
			$this->_helper->json($result);

    }
    
    public function cellgetcampeonatoAction() {
      	$body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
        
        $c = new Application_Model_Championships();
        
        $rd_id = $params['rd_id'];
        
        $champ = $c->getChampByRound($rd_id);
        
        $m = new Application_Model_Matchs();
        $palpites = $m->getpalpites($params['mt_id']);
        
        $result['palpites'] = $palpites;
        $result['champ'] = $champ;
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json($result);
    } 
    
    public function cellesqueceusenhaAction() {
      	$body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
        
//        
//        $params = $this->_request->getParams();
        $email = $params['email'];
        
        $token = md5(uniqid(rand(), true));
        
        $u = new Application_Model_Users();
        
        $user = $u->load_userbyemail($email);
        
        $result = 400;
        
        if (!empty($user)) {
        
            $u->save_esqueceu($user['us_id'], $email, $token);

            $body = '<a href="http://www.bolaocraquedebola.com.br/public/?trocarsenha='.$token.'">clique neste link para trocar a senha</a>';

            $this->mail($body, $email, "Resetar senha");
            
            $result = 200;
        
        }
                $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json($result);
    }
    
    public function mail($body, $addTo, $subject) {
        $config = array('ssl' => 'ssl',
            'auth' => 'login',
            'username' => 'bolaocraquedebola16@gmail.com',
            'password' => 'E3b3c4f5h5931');

        $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);

        $mail = new Zend_Mail();
        $mail->setBodyHtml($body);
        $mail->setFrom('bolaocraquedebola16@gmail.com');
        $mail->addTo($addTo, $addTo);
        $mail->setSubject($subject);
        $mail->send($transport);
    }
    
    public function cellpalpitesusuarioAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
        
        $user = $params['user'];
        
        $u = new Application_Model_Users();
        $result = $u->getPalpitesUsuario($user);
        
                        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json($result);
        
    }
    
    public function celleditgritoAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);

        $grito = $params['grito'];
        $userId = $params['userId'];
        
        $u = new Application_Model_Users();
        $result = $u->update_grito($userId, $grito);
        
                        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json(200);
    }    
    
    public function celleditsenhaAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);

        $novaSenha = $params['novasenha'];
        $userId = $params['userId'];
        
        $u = new Application_Model_Users();
        $result = $u->update_senha($userId, $novaSenha);
        
                        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json(200);
    }
    
    public function meusboloesAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);       
        
        $userid = $params['userid'];
        
        $u = new Application_Model_Users();
        $boloes = $u->getBoloes($userid);
        
                                $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        if (empty($boloes)) {
            $boloes = false;
        }
        
        
        $this->_helper->json($boloes);
       
    }
    
    public function criarbolaoAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
        
        $iduser = $params['iduser'];
        $nome = $params['nome'];
        $valor = $params['valor'];
        $privado = $params['privado'];
        $idchamp = $params['idchamp'];
        
        $primer = $params['primer'];
        $segundo = $params['segundo'];
        $tercer = $params['tercer'];
        
        $u = new Application_Model_Users();
        $id = $u->criar_bolao($iduser, $nome, $valor, $privado, $idchamp,
                $primer, $segundo, $tercer);
        
        $p = new Application_Model_Penca();
        $p->save_userpenca_inicial(array('up_idpenca' => $id,
            'up_iduser' => $iduser));
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json(200);
    }
    
    
    
    
    public function cellverificardinheiroecadastrarAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
        
        $u = new Application_Model_Users();
        $dinheiro = $u->getDinheiro($params['iduser']);
        
        $custo = $params['custo'];
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $d = $dinheiro['us_cash'];
        
        if ($d >= $custo) {
                        
            $p = new Application_Model_Penca();
            $p->save_userpenca($params['idpenca'],$params['iduser'], $custo);

            $new_cash = round($d, 2) - round($custo, 2);
            
            $u->update_cash($params['iduser'], $new_cash);

            $pencas = $u->getPencasDisponiveis($params['iduser']);
                                
            $boloes = $u->getBoloes($params['iduser']);
            
            $result = array();
            $result['cash'] = $new_cash;
            $result['pencas_disponiveis'] = $pencas;
            $result['boloes'] = $boloes;
            
             $this->_helper->json($result);   
            
        } else {
            $this->_helper->json(false);            
        }

    }
    
    public function cellsairbolaoAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);   
        
        $iduser = $params['iduser'];
        $idpenca = $params['idpenca'];
        
        $p = new Application_Model_Penca();
        $p->remove_penca_usuario($iduser, $idpenca);
        
        $u = new Application_Model_Users();
        $pencas = $u->getPencasDisponiveis($params['iduser']);
        $boloes = $u->getBoloes($params['iduser']);
        
        $result = array();
        $result['boloes'] = $boloes;
        $result['pencas_disponiveis'] = $pencas;
        $result['result'] = 200;
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result); 
    }
    
    public function cellcompartilharbolaoAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body); 
        
        $email = $params['email'];
        $nomepenca = $params['nomepenca'];
        $idpenca = $params['idpenca'];
        $nomeusuarioemisor = $params['nomeusuarioemisor'];
        $nomecampeonato = $params['nomecampeonato'];
        
        $body = "Ola!<br><br> ".$nomeusuarioemisor." convidou voce para participar do Bolao: "
                . "<a href='http://www.bolaocraquedebola.com.br/public/penca/convite/?idpenca=".$idpenca."'>"
                . "".$nomepenca."</a> do campeonato: ".$nomecampeonato."<br><br> Boa sorte!!!";
        
        $this->mail($body, $email, "Convite para participar do Bolao");
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);         
    }
    
    public function uploadAction() {
    
                $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
        
        print_r($params);
        die(".");
        
        //if they DID upload a file...
//        if($_FILES['photo']['name'])
//        {
//                //if no errors...
//                if(!$_FILES['photo']['error'])
//                {
//                        //now is the time to modify the future file name and validate the file
//                        $new_file_name = strtolower($_FILES['photo']['tmp_name']); //rename file
//                        if($_FILES['photo']['size'] > (1024000)) //can't be larger than 1 MB
//                        {
//                                $valid_file = false;
//                                $message = 'Oops!  Your file\'s size is to large.';
//                        }
//
//                        //if the file has passed the test
//                        if($valid_file)
//                        {
//                                //move it to where we want it to be
//                                move_uploaded_file($_FILES['photo']['tmp_name'], 'assets/'.$new_file_name);
//                                $message = 'Congratulations!  Your file was accepted.';
//                        }
//                }
//                //if there is an error...
//                else
//                {
//                        //set that to be the returned message
//                        $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['photo']['error'];
//                }
//        }
//
//        //you get the following information for each file:
//        $_FILES['field_name']['name'];
//        $_FILES['field_name']['size'];
//        $_FILES['field_name']['type'];
//        $_FILES['field_name']['tmp_name'];
        
    }
}

