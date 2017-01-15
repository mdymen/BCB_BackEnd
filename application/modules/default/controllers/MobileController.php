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
        
        $this->login1($data['us'], $data['pass']);
        
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json("loginok"); 
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
    
}

