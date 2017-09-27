<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';

include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/translate.php';
class Admin_MailController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $opcoes = array();
        
        $opcoes[0] = "Ainda tem saldo";
        $opcoes[1] = "Donos de Boloes";
        //$opcoes[2] = "";
        
        $this->view->opcoes = $opcoes;
    }
    
    public function enviaremailAction() {
        $params = $this->_request->getParams();
        
        $op = $params['opcao'];
        
        $u = new Application_Model_Users();
        if ($op == 0) {
            $this->email_creditosdisponiveis($u->email_quemtemsaldo());                        
        }
        if ($op == 1) {
            $this->email_donosboloes($u->email_donosboloes());
        }
        
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }
    
    

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
    public function email_donosboloes($users) {

        
        $emails = array();
        
        $e = 0;
        for ($i = 0; $i < count($users); $i = $i + 1) {
            
            if (!empty($users[$i]['us_email'])) {
                $emails[$e] = $users[$i]['us_email'];
                $e = $e + 1;
            }
            
        }
        
        for ($i = 0; $i < count($emails); $i = $i + 1) {
            $x = '<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
</head>
<body>

<div style="width:100%;" align="center">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr>
    <td align="center" valign="top" style="background-color:#53636e;" bgcolor="#53636e;">
    
    <br>
    <br>
    <table width="583" border="0" cellspacing="0" cellpadding="0">
      <tbody>
				<a href="http://www.bolaocraquedebola.com.br/public" TARGET="_blank"><img src="http://www.bolaocraquedebola.com.br/public/assets/img/crioubolao.jpg"></a>
				
				</td>

  </tbody></table>
    <br>
    <br></td>
  </tr>
</tbody></table>

</div>



</body></html>';


            $this->mail($x, $emails[$i], "Convide seus amigos");
        }        
    }
    
    public function email_creditosdisponiveis($users) {

        
        $emails = array();
        
        $e = 0;
        for ($i = 0; $i < count($users); $i = $i + 1) {
            
            if (!empty($users[$i]['us_email'])) {
                $emails[$e] = $users[$i]['us_email'];
                $e = $e + 1;
            }
            
        }
        
        for ($i = 0; $i < count($emails); $i = $i + 1) {
            $x = '<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
</head>
<body>

<div style="width:100%;" align="center">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr>
    <td align="center" valign="top" style="background-color:#53636e;" bgcolor="#53636e;">
    
    <br>
    <br>
    <table width="583" border="0" cellspacing="0" cellpadding="0">
      <tbody>
				<a href="http://www.bolaocraquedebola.com.br/public" TARGET="_blank"><img src="http://www.bolaocraquedebola.com.br/public/assets/img/creditodisponivel.jpg"></a>
				
				</td>

  </tbody></table>
    <br>
    <br></td>
  </tr>
</tbody></table>

</div>



</body></html>';


            $this->mail($x, $emails[$i], "Crédito Disponível");
        }
    }
    
    public function mail($body, $email, $subject) {
        $config = array('ssl' => 'ssl',
            'auth' => 'login',
            'username' => 'bolaocraquedebola16@gmail.com',
            'password' => 'Ebcfh94785',
            'encoding' => 'UTF-8',
            'charset' => 'UTF-8');

        $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($body);
        $mail->setFrom('bolaocraquedebola16@gmail.com', 'Bolão Craque de Bola');
        
        $mail->addTo($email, $email);
        

        $mail->setSubject($subject);
        $mail->send($transport);
    }
    
    public function usuariosquefizerambolaoAction() {
        
        $p = new Application_Model_Penca();
        $us = $p->load_usuarios_donos_bolao();
        
        $this->view->users = $us;
        
    }
}

