<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TeamController
 *
 * @author Martin Dymenstein
 */
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/transaction.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/html.php';
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/helpers/box.php';
class CaixaController extends Zend_Controller_Action
{
    public function indexAction() {

        
    }
    
    public function transacoesAction() {
        $params = $this->_request->getParams();
        
        $c = new Application_Model_Championships();
        
        $t = new Application_Model_Transaction();
        $ts_credito = $t->getCampeonato("", $this->getIdUser(), "", "CREDITO");
         $this->view->tr_credito = $ts_credito;
        if (!empty($params['campeonato'])) {
            
            $campeonato = $params['campeonato'];
            
            $rodada = "";
            if (strcmp($params['rodada'], "Vazio") != 0) {
                $this->view->round = $params['rodada'];
                $rodada = $params['rodada'];
            }
            
            $ts = $t->getCampeonato($campeonato, $this->getIdUser(), $rodada,"JOGO");            
            
            $this->view->transactions = $ts;
           
            
            $rs = $c->getrondas($campeonato);          
            
            $this->view->rondas = $rs;
            
            $this->view->champ = $campeonato;
        }                
        
        $r = new Application_Model_Result();
        $rankings = $r->rankings_champ_usuario($this->getIdUser());
                
        $this->view->ranking = $rankings;
        
        $champs = $c->load();
        $this->view->champs = $champs;
    }
    
    public function planoAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);   
        
        $plano = $params['p'];
        
        $preco = 0;
        $nome_plano = "";
        if ($plano == 1) {
            $preco = "10.80";
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

        $data = array('token'=>'C6109FC822BD4E78845AE99356D37D9A',
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
		
        curl_close ($ch);
		
		//print_r($server_output);
		//die(".");
        
        $return = new SimpleXMLElement($server_output);
        $my_array = (array)$return->code;
        
        $codigo = $my_array[0];
		
		$u = new Application_Model_Users();
		$u->add_pagseguro_ini($params['user'], $codigo, $params['email'], $nome_plano);

        $this->getResponse()
        ->setHeader('Content-Type', 'application/json');
       
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(TRUE);
       
       $this->_helper->json($codigo);
    }


    public function testarcaixaxAction() {
        try {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);   
        
        $plano = 1;
        
        $preco = 0;
        $nome_plano = "";
        if ($plano == 1) {
            $preco = "10.80";
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

        $data = array('token'=>'5FCC6C3FA3694BFDB7DF5C2534A65562',
            'email'=>'martin@dymenstein.com',
            'currency' => 'BRL',
            'itemId1' => 0001,
            'itemDescription1'=> $nome_plano,
            'itemAmount1' => $preco,
            'itemQuantity1' =>1,
            'itemWeight1' => 1000
            );
 
 
 
        curl_setopt($ch, CURLOPT_URL,"https://ws.sandbox.pagseguro.uol.com.br/v2/checkout");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);
		
        curl_close ($ch);
	
        $return = new SimpleXMLElement($server_output);
        $my_array = (array)$return->code;
        
        $codigo = $my_array[0];
		
		$u = new Application_Model_Users();
		$u->add_pagseguro_ini($params['user'], $codigo, $params['email'], $nome_plano);

        $this->getResponse()
        ->setHeader('Content-Type', 'application/json');
       
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(TRUE);
       
       $this->_helper->json($codigo);

        }
        catch (Exception $e) {
            $this->getResponse()
            ->setHeader('Content-Type', 'application/json');
           
           $this->_helper->layout->disableLayout();
           $this->_helper->viewRenderer->setNoRender(TRUE);
           
           $this->_helper->json($e->getMessage());
        }
    }

    
   public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_id'];
    }
    
   public function getUserEmail() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_email'];
    }
	
	public function notificacaoAction() {
		$params = $this->_request->getParams();
		
		$notificationCode = $params['notificationCode'];
		
		$ch = curl_init();

        //$data = array('token'=>'6363C4111D064931A0CC0F0330849143',
		$data = array('token'=>'C6109FC822BD4E78845AE99356D37D9A',
            'email'=>'martin@dymenstein.com'
            );
 
        curl_setopt($ch, CURLOPT_URL,"https://ws.pagseguro.uol.com.br/v3/transactions/notifications/".$notificationCode."?token=C6109FC822BD4E78845AE99356D37D9A&email=martin@dymenstein.com");
    //    curl_setopt($ch, CURLOPT_POST, 1);
    //    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);
        
        curl_close ($ch);
        
        $return = new SimpleXMLElement($server_output);
        $my_array = (array)$return->code;
		
		$retorno = (array)$return;

		$dados = (array)$retorno['sender'];
		$email = (array)$dados['email'];
		
		$status = (array)$retorno['status'];
		
		$items = (array)$retorno['items'];
		$item = (array)$items['item'];
		$plano = (array)$item['description'];
		
		$r = new Application_Model_Users();
		
		//para acreditar pagamento;
		
		$pg_foipago = 0;
		
		if ($status[0] == 3) {
			
			$us = $r->getPagSeguroByEmail($email[0], $plano[0]);
			
			print_r($us);
			
			$usuario = $r->getUserByEmail($email[0]);
			
			if ($plano[0] == "Plano 1") {
				$usuario['us_cash'] = $usuario['us_cash'] + 10;
			}
			if ($plano[0] == "Plano 2") {
				$usuario['us_cash'] = $usuario['us_cash'] + 20;
			}
			if ($plano[0] == "Plano 3") {
				$usuario['us_cash'] = $usuario['us_cash'] + 51;
			}
			if ($plano[0] == "Plano 4") {
				$usuario['us_cash'] = $usuario['us_cash'] + 102;
			}
			
			$pg_foipago = 1;
            
            if (!empty($usuario['us_id'])) {
                $r->update_cash($usuario['us_id'], $usuario['us_cash']);
            }
		}
		
		
		$r->update_pagseguro($email[0], $notificationCode, $status[0], $plano[0], $my_array[0], $pg_foipago  );
		
		        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($return);
		
    }
    
    public function notificacaotesteAction() {
		$params = $this->_request->getParams();
		
		$notificationCode = $params['notificationCode'];
		
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/".$notificationCode."?token=5FCC6C3FA3694BFDB7DF5C2534A65562&email=martin@dymenstein.com");      
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);

        curl_close ($ch);
        
        $return = new SimpleXMLElement($server_output);       

        $my_array = (array)$return->code;

        $retorno = (array)$return;		

		$dados = (array)$retorno['sender'];
		$email = (array)$dados['email'];
		
		$status = (array)$retorno['status'];
		
		$items = (array)$retorno['items'];
		$item = (array)$items['item'];
		$plano = (array)$item['description'];
		
		$r = new Application_Model_Users();
		
		//para acreditar pagamento;
		
		$pg_foipago = 0;
		
		if ($status[0] == 3) {
			
			$us = $r->getPagSeguroByEmail($email[0], $plano[0]);
			
			$usuario = $r->getUserByEmail($email[0]);
			
			if ($plano[0] == "Plano 1") {
				$usuario['us_cash'] = $usuario['us_cash'] + 10;
			}
			if ($plano[0] == "Plano 2") {
				$usuario['us_cash'] = $usuario['us_cash'] + 20;
			}
			if ($plano[0] == "Plano 3") {
				$usuario['us_cash'] = $usuario['us_cash'] + 51;
			}
			if ($plano[0] == "Plano 4") {
				$usuario['us_cash'] = $usuario['us_cash'] + 102;
			}
			
			$pg_foipago = 1;        
            if (!empty($usuario['us_id'])) {
                $r->update_cash($usuario['us_id'], $usuario['us_cash']);
            }
            

		}		    
		
		$r->update_pagseguro($email[0], $notificationCode, $status[0], $plano[0], $my_array[0], $pg_foipago  );

		        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($return);
		
	}
}
