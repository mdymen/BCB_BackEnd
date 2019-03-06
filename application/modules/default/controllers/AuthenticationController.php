<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/helpers/jwt/JWT.php';
include_once(APPLICATION_PATH."/../public/Zend/Controller/Response/Http.php");
class AuthenticationController  extends Zend_Controller_Action {
    
    public function loginAction() {

        try {
            $body = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($body);

            $usuario = $params['usuario'];
            $senha = $params['senha'];                        

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $u = new Application_Model_Users();
            $login = $u->login($usuario, $senha);

            if (!empty($login)) {
                
                $key = "fka7kum1";
                $token = array(
                    "id"=> $login['us_id'],
                    "username" => $login['us_username']
                );
                
                $jwt = JWT::encode($token, $key);

                $result["data"]["token"] = $jwt;
                $this->_helper->json($result);

            } 
            
            throw new Exception();

        }
        catch (Exception $e) {
            $response = new Zend_Controller_Response_Http();
            $response->setHttpResponseCode(400);
            $this->setResponse($response);
            
            $result["data"]["erro"] = "Nome de usuario e/ou senha incorretos";
            
            $this->_helper->json($result);
        }
    }



}