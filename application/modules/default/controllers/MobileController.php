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
   
    public function celloginAction() {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        
		$u = new Application_Model_Users();
		$user = $u->login($data['us'],$data['pass']);
		// if (!empty($user)) {
		
			// $this->login1($data['us'], $data['pass']);
			
			
			// $storage = new Zend_Auth_Storage_Session();
			// $data = (get_object_vars($storage->read()));
		// } else {
			// $data = false;
		// }
		
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
    
    public function celcadastroAction() {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        
        $user['us_username'] = $data['username'];
        $user['us_password'] = $data['password'];
        $user['us_email'] = $data['email'];
 
        $u = new Application_Model_Users();
        $u->save_user($user);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
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
        
        $temSaldo = $this->verificarsaldo($champ_obj);
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
    
    public function celproximojogosAction() {
       // $params = $this->_request->getParams();
        
        $us_id = 1;//$params['us_id'];
        
        $m = new Application_Model_Matchs();
        $matchs = $m->proximos_jogos($us_id);

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
 

    private function verificarsaldo($champ) {        
        $u = new Application_Model_Users();
        $mycash = $u->getDinheiro($this->getIdUser());
        
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

        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));

        $result1 = $params['result1'];
        $result2 = $params['result2'];
        $user_id = $data['us_id'];
        $match_id = $params['match'];
        $round = $params['round'];
        $champ = $params['champ'];
//        
//        print_r($round);
//        die(".");
        
        $champs = new Application_Model_Championships();
        $champ_obj = $champs->getChamp($champ);
        
        $temSaldo = $this->verificarsaldo($champ_obj);
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
  
            //$this->login();
   
        } else {
            $result['sucesso'] = 401;
        }
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
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
	
	public function cellbolaoAction() {
		
		// $params = $this->_request->getParams();
		
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	  
	
       // print_r($params);
        
        $champ = new Application_Model_Championships();
        $result['championships']= $champ->load();
        
        $p_obj = new Application_Model_Penca();

        if (!empty($params['champ'])) {                      
            
            $result['teamuserid'] = $this->getTimeUserId();
            $result['teamusername'] = $this->getTimeUserName();
            
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
            $data = (get_object_vars($storage->read()));

            $matchs_obj = new Application_Model_Matchs();
            $rondas = $matchs_obj->getrondas($champ_id);

            
            
            if (empty($params['team'])) {
                $rodada = $matchs_obj->load_rodada_com_palpites($champ_id, $rodada_id, $data['us_id']);
                $result['porteam'] = true;
                $result['porrodada'] = false;
            } else {
                $result['porteam']  = false;
                $$result['porrodada'] = true;
                $team_id = $params['team'];
                $rodada = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $data['us_id']);
            }

            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 

            $result['teams'] = $teams;
            
            //los partidos de la rodada n_rodada
            $result['rodada'] = $rodada;
            
            //el numero de la rodada activa. La que siguiente inmediata que se va a jugar
            $result['n_rodada'] = $rodada_id;
            
            //las rodadas del campeonato registradas en el sistema
            $result['rondas'] = $rondas;           
						
        }
		
		$this->getResponse()
			 ->setHeader('Content-Type', 'application/json');
			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
			
			$this->_helper->json($result);
    }
	
	
	public function cellmeuspalpitesAction() { 
//        try {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);	

//            print_r($params);
            
            $penca = new Application_Model_Penca();
            
            $id_user = $this->getIdUser();
            $champs = $penca->load_championship_with_results($id_user);

            if (!empty($params['champ'])) {
                
                $result['teamuserid'] = $this->getTimeUserId();
                $result['teamusername'] = $this->getTimeUserName();            
                
                $matchs_obj = new Application_Model_Matchs();
                $rondas = $matchs_obj->getrondas($params['champ']);
                $result['rondas'] = $rondas;
                $result['champ'] = $params['champ'];                                

                if (empty($params['rodada'])) {
                    $rodada_id = $penca->getIdPrimeraRodadaDisponivel($params['champ']);
                } else {            
                    $rodada_id = $params['rodada'];
                }
                
                $palpites_da_rodada = $matchs_obj->load_palpites_simples($params['champ'], $rodada_id, $this->getIdUser());
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
	
	
	
	
	
}

