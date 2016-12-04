<?php

include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH."/helpers/html.php";
include APPLICATION_PATH."/helpers/translate.php";
include APPLICATION_PATH.'/helpers/box.php';
include APPLICATION_PATH.'/helpers/mail.php';
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function testAction() {
        
        print_r($config->host);
        die(".");
    }
    
    public function indexAction() {
        try {
            $params = $this->_request->getParams();
            //$this->view->error = $params['error'];
            
            $ordem = "";
            if (!empty($params['ordem'])) {
                $ordem = $params['ordem'];
            }

            $storage = new Zend_Auth_Storage_Session();
            $data = (get_object_vars($storage->read()));

            $result = new Application_Model_Result();
            $em_acao_group = $result->palpites_em_acao_group($data['us_id'], $ordem);

            $config = new Zend_Config_Ini('config.ini');

            $h_date = new Helpers_Data();
            $this->view->palpites = $em_acao_group;

        }
        catch (Exception $e) {
//             $config = new Zend_Config_Ini("config.ini");
//            $this->redirect("/index/logout");
        }
    }
    
    public function puntuacaoAction() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
//        $result = new Application_Model_Result();
        $results = new Application_Model_Users();
        
        $resultados = array();
        
        $id_user = $data['us_id'];
        
        $resultados['perdidos'] = $results->getLostMatches($id_user);
        $resultados['ganados'] = $results->getWonMatches($id_user);
        $resultados['jogados'] = $results->getPlayedMatches($id_user);
        $resultados['pontos'] = $results->getPoints($id_user);
        $resultados['position'] = $results->getPoisition($id_user);
//        $ganados = $result->ganados($data['us_id']);
//        $perdidos = $result->perdidos($data['us_id']);
//        $puntuacao = $result->puntuacao($data['us_id']);
//        
//        $resultados = array($ganados[0], $perdidos[0], $puntuacao[0]);
//        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($resultados);
        
    }
    
//    public function registerAction() {
//       $params = $this->_request->getParams();
//       
//       $user = new Application_Model_Users();
//       $usuario = $user->load_user($params['username']);
//       
//       
////       if (empty($usuario)) {
////            $user->save($params);
////            //$this->login1($params['username'], $params['password']);
////       } else {
////           
////       }
//       $this->redirect("../public/?register");
//    }
    
    public function registerAction() {
        $params = $this->_request->getParams();
       
        $user = new Application_Model_Users();
       
        $usuario = $user->save_provisorio($params);
       
        $this->redirect("../public/?register&id=".$usuario);
       
    }
    
    public function podecadastrarusuarioAction() {
        $usuario = $this->_request->getParam("usuario");
        
        $u = new Application_Model_Users();
        $return = $u->load_user($usuario);
        
        $result = false;
        if (empty($return)) {
            $result = true;
        } 
        
        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json($result);
    }
    
    public function testpagamentoAction() {
        $ch = curl_init();

        $data = array('response'=>$params['g-recaptcha-response'],
            'secret'=>'6Lfs7QwUAAAAAHd5nUoanvbwefoZoW3IPt-6QVR5');
        curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);
        curl_close ($ch);        
        $resp = json_decode($server_output, true);
        
    }
    
    public function regulamentoAction() {
        
    }
    
    public function registercompleteAction() {
        $params = $this->_request->getParams();
     
//        print_r($params);
//        die(".");
        
        $ch = curl_init();

        $data = array('response'=>$params['g-recaptcha-response'],
            'secret'=>'6Lfs7QwUAAAAAHd5nUoanvbwefoZoW3IPt-6QVR5');
        curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);
        curl_close ($ch);        
        $resp = json_decode($server_output, true);
        
        $usuario = $params['usuario'];
        $userlinked = $params['userlinked'];
        
        
        $nome = $params["nome"];
        $email = $params["email"];
        $cpf = $params["cpf"];
        $cpf = ereg_replace('[^0-9]', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        
        
        $senha = $params["password"];
        $cep = $params["cep"];
        $telefone = $params["telefone"];
        $ano = $params['ano'];
        $mes = $params['mes'];
        $dia = $params['dia'];       
        
        $data = array(
            'us_password' => $senha,
            'us_nome' => $nome,
            'us_cpf' => $cpf,
            'us_email' => $email,
            'us_telefone' => $telefone,
            'us_anio_niver' => $ano,
            'us_mes_niver' => $mes,
            'us_dia_niver' => $dia
        );

        $u = new Application_Model_Users();
        
        $server = $this->view->serverUrl();
        if (strcmp($server, "http://localhost") == 0) {
            $server = $server."/penca";
        }
        $server = $server."/public/index";
        $data['us_codverificacion'] = rand();
//        print_r($server);
//        die(".");
        
        if (!empty($usuario) && empty($userlinked)) {                   
            $result = $u->cancomplete($usuario, $senha);

            $username = $result['prov_username'];                     
            $data['us_username'] = $username;
            
            $res = false;
            if (!empty($result)) {                            
               $u->save_user($data);
               $this->login1($result['prov_username'], $senha);
               $res = true;
            } 
            $this->mail($data);
            $this->redirect($server);

        } 
        else if (!empty($userlinked) && empty($usuario)) {
            
            $data['us_username'] = $params['nomeusuario'];
            $lastIdUser = $u->save_user($data);
            
            $u->adicionarPorLinkReferencia($userlinked, $lastIdUser);
            
            $usuario_linked = $u->load_userbyid($userlinked);
            $v = $usuario_linked['us_cash'] + 1;
            $u->update_cash($usuario_linked['us_id'], $v);
                     
            $this->login1($data['us_username'], $senha);
            $this->mail($data);
            $this->redirect($server);
        }
        else {
            
        }
       
    }
    
    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_id'];
    }
    
    function gerarlinkreferenciaAction() { 
        
        $user_id = $this->getIdUser();
        
        $ch = curl_init();
        
        //$link = $this->view->serverUrl().$this->view->url()."?user=".$user_id;
        
        $server = $this->view->serverUrl();
        if (strcmp($server, "http://localhost") == 0) {
            $server = $server."/penca";
        }
        $server = $server."/public/?linkreferencia&userlinked=".$user_id;
        
        $data = array('longUrl'=>  $server);
        $data = json_encode($data);  
        
        curl_setopt($ch, CURLOPT_URL,"https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyBxHbn3Y8XdBQqk7j-ZPzJRdrnG_fmpZ-o");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data))                                                                       
        );                                                                                                                   

        
        $server_output = curl_exec ($ch);

        curl_close ($ch);        
        
        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $result = json_decode($server_output);
        
        $this->_helper->json($result->id);
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
    
    public function registerteamsAction() {
        
    }   
    
    public function registerteamsformAction() {
        $params = $this->_request->getParams();
        
        print_r($params["teams"]);
        die(".");
        
        $teams = explode(' ', $params["teams"]);
        print_r($teams);
        die(".");
    }
    
    public function teamsAction() {
        
        $teams = new Application_Model_Teams();
        $ts = $teams->load_teams_championship(1);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($ts);
    }
    
   
    public function loginfaceAction() {
        $params = $this->_request->getParams();
        
        $us = new Application_Model_Users();
        $result = $us->isUserRegistered($params['facebookid']);
        
        if (empty($result)) {
            $us->facebookUserSave($params['facebookid']);
        }
           
        if (!empty($params['facebookid'])) {        
            $this->login1($result['us_username'], $result['us_password']);
        }
//        $this->_request->setParams(array("username" => $params['facebookid'], "password" => $params['facebookid']));
        //$this->forward("login", "index", "default",array("username" => $params['facebookid'], "password" => $params['facebookid']));
//           $this->_helper->redirector("login", "index", "default", array("username" => $params['facebookid'], "password" => $params['facebookid']));
       // $this->redirect("/index/login",array("username" => $params['facebookid'], "password" => $params['facebookid']));

        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
        
        
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
    
    public function addfacebookuserAction() {
//        $this->render("addfacebookuser")
    }
    
    public function validaemailAction() {
        $email = $this->_request->getParam("email");
        $u = new Application_Model_Users();
        $return = $u->isEmailUsed($email);
        
        $result = false;
        if (empty($return)) {
            $result = true;
        }
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
    }
    
    public function validacpfAction() {
        $cpf = $this->_request->getParam("cpf");
        
        $cpf = preg_replace('/\D/', "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        
//        print_r($cpf);
//        die("-");
        
        $u = new Application_Model_Users();
        $result = $u->isCpfUsed($cpf);
        
        if (empty($result)) {
            // Verifica se um número foi informado
            if(empty($cpf)) {
                $result = false;
            }

            // Elimina possivel mascara
            

            // Verifica se o numero de digitos informados é igual a 11 
            if (strlen($cpf) != 11) {
                $result =  false;
            }
            // Verifica se nenhuma das sequências invalidas abaixo 
            // foi digitada. Caso afirmativo, retorna falso
            else if ($cpf == '00000000000' || 
                $cpf == '11111111111' || 
                $cpf == '22222222222' || 
                $cpf == '33333333333' || 
                $cpf == '44444444444' || 
                $cpf == '55555555555' || 
                $cpf == '66666666666' || 
                $cpf == '77777777777' || 
                $cpf == '88888888888' || 
                $cpf == '99999999999') {
                $result =  false;
             // Calcula os digitos verificadores para verificar se o
             // CPF é válido
             } else {   

                for ($t = 9; $t < 11; $t++) {

                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf{$c} * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf{$c} != $d) {
                        $result =  false;
                    }
                }

                $result =  true;
            }
        
        } else {
            $result = false;
        }
        
         $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
    }
    
    public function usuarioexisteAction() {
        $param = $this->_request->getParam("usuario");
        
        $u = new Application_Model_Users();
        $result = $u->isUsersName($param);
        
        $existe = true;
        if (empty($result)) {
            $existe = false;
        }
          
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($existe);        
        
    }
    
    public function reenviaremailAction() {
        $params = $this->_request->getParams();
        
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        
        $this->mail($data);
        
        
        $this->redirect("/index");

    }
    
    public function mail($data) {
        $mail = Helpers_Mail::getInstance();
        $mail->addTo('<'.$data['us_email'].'>');
        $mail->setSubject('Confirme seu email');
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/penca/public'."/?confmail=".$data['us_codverificacion'];
        $string = 'Confirme seu email: <a href="'.$root.'" > Faça clique.</a>';
        $mail->setBodyHtml($string);
        $mail->setFrom('bolaocraquedebola16@gmail.com', 'Bolão Craque de Bola');
        $mail->send();
    }
    
    public function confirmaremailAction() {
        $params = $this->_request->getParams();
        
//        print_r($params);
//        die(".");
        
        $u = new Application_Model_Users();
        $user = $u->user_bycod($params['confmail']);
        
        if (!empty($user)) {
            $u->confirmaremail($user['us_id']);
            
            $this->login1($user['us_username'], $user[us_password]);
            
        }
        $this->redirect("/index");
    }
}

