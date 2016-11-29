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
include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/html.php';
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/helpers/box.php';
class CaixaController extends Zend_Controller_Action
{
    public function indexAction() {
    }
    
    public function planoAction() {
        $params = $this->_request->getParams();
        
        $plano = $params['p'];
        
        $preco = 0;
        $nome_plano = "";
        if ($plano == 1) {
            $preco = 2;
            $nome_plano = "Plano 1";
        } else if ($plano == 2) {
            $preco = 20;
            $nome_plano = "Plano 2";
        } else if ($plano == 3) {
            $preco = 50;
            $nome_plano = "Plano 3";
        } else if ($plano == 4) {
            $preco = 100;
            $nome_plano = "Plano 4";
        }
        
        $ch = curl_init();

        $data = array('token'=>'2CAADB949E5B4D0CB091FD530357A86B',
            'email'=>'martin@dymenstein.com',
            'currency' => 'BRL',
            'itemId1' => 0001,
            'itemDescription1'=> $nome_plano,
            'itemAmount1' => $preco.'.00',
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
        
        $return = new SimpleXMLElement($server_output);
        $my_array = (array)$return->code;
        
        $codigo = $my_array[0];
 
        $this->redirect("https://pagseguro.uol.com.br/v2/checkout/payment.html?code=".$codigo);
    }
    
}
