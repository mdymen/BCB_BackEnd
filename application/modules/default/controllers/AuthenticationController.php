<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/helpers/jwt/JWT.php';
include_once(APPLICATION_PATH."/../public/Zend/Controller/Response/Http.php");
class AuthenticationController  extends Zend_Controller_Action {

    public function loginAction() {
        try {
            $x = $this->getRequest()->isOptions();

            if ($x) {
                $res = $this->getRequest();
                $res->headers->set('Content-Type', 'application/json');
                $res->headers->set('Access-Control-Allow-Origin', 'http://example.com');
                $res->headers->set('Access-Control-Allow-Credentials', 'true');
                $res->headers->set('Access-Control-Max-Age', '60');
                $res->headers->set('Access-Control-Allow-Headers', 'AccountKey,x-requested-with, Content-Type, origin, authorization, accept, client-security-token, host, date, cookie, cookie2');
                $res->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

                return $res;
            }

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

                $result["data"]["usuario"] = $login;
                $result["data"]["token"] = $jwt;
                $this->_helper->json($result);

            } 
            

        }
        catch (Exception $e) {
            $response = new Zend_Controller_Response_Http();
            $response->setHttpResponseCode(400);
            $this->setResponse($response);
            
            $result["data"]["erro"] = "Nome de usuario e/ou senha incorretos";
            
            $this->_helper->json($e->getMessage());
        }
    }



}