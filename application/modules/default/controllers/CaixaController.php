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
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/transaction.php';
include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/html.php';
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/helpers/box.php';
class CaixaController extends Zend_Controller_Action
{
    public function indexAction() {
        $params = $this->_request->getParams();
        
        $c = new Application_Model_Championships();
        
        if (!empty($params['campeonato'])) {
            $t = new Application_Model_Transaction();
            
            
            $campeonato = $params['campeonato'];
            
            $rodada = "";
            if (strcmp($params['rodada'], "Vazio") != 0) {
                $this->view->round = $params['rodada'];
                $rodada = $params['rodada'];
            }
            
            $ts = $t->getCampeonato($campeonato, $this->getIdUser(), $rodada);
            $ts_credito = $t->getCampeonato($campeonato, $this->getIdUser(), "", "CREDITO");
            
            $this->view->transactions = $ts;
            $this->view->tr_credito = $ts_credito;
            
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
        $params = $this->_request->getParams();
        
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

        $data = array('token'=>'C75869B3B0FC47E7B3B5B232EC412CD2',
            'email'=>'riquerubim@yahoo.com.br',
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
        
        $this->redirect("https://pagseguro.uol.com.br/v2/checkout/payment.html?code=".$codigo);
    }
    
   public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
        return $data['us_id'];
    }
    
}
