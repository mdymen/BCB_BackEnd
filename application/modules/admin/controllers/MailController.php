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
				<a href="http://www.bolaocraquedebola.com.br" TARGET="_blank"><img src="http://www.bolaocraquedebola.com.br/public/assets/img/crioubolao.jpg"></a>
				
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
				<a href="http://www.bolaocraquedebola.com.br" TARGET="_blank"><img src="http://www.bolaocraquedebola.com.br/public/assets/img/creditodisponivel.jpg"></a>
				
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

    /**
     * Me devuelve la lista de usuarios que palpito un partido determinado
     * @param mt_id
     */
    public function palpitaronpartidoAction() {
        $this->getResponse()
        ->setHeader('Content-Type', 'application/json');
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            $body = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($body);        

            $users = new Application_Model_Users();
            $usuarios = $users->getUsuariosPalpitaronJogo($params['mt_id']);

            $this->_helper->json($usuarios);

        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }

    }

    /**
     * Envia email con el resultado del palpite
     * @param mt_id
     */
    public function emailparapalpitadoresAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);        

        $users = new Application_Model_Users();
        $usuarios = $users->getUsuariosPalpitaronJogo($params['mt_id']);

        //verifica el resultado del partido y envia un email
        //avisando si acerto o erro el palpite
        for ($i = 0; $i < count($usuarios); $i = $i + 1) {
            $palpite = $usuarios[$i];
            
            if ($palpite['mt_goal1'] === $palpite['rs_res1'] 
                && $palpite['mt_goal2'] === $palpite['rs_res2']) {
                    $this->enviarPalpiteAcertado($palpite);
            } else {
                $this->enviarPalpiteErrado($palpite);
            }

            
        }
        
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(TRUE);
       
       $this->_helper->json($usuarios);

    }

    function enviarPalpiteAcertado($palpite) {
        
         $html = 
         '<html xmlns="http://www.w3.org/1999/xhtml"><head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title>Untitled Document</title>
            </head>
            <body>
                Ola '.$palpite['us_username'].'<br></br>
                Parabens!! você acertou no palpite do jogo '.$palpite['t1nome']." - ".$palpite['t2nome'].'<br>
                Ingresse na plataforma do bolão para ver o ranking.
                <br><br>
                Bolão Craque de Bola
                <a href="http://www.bolaocraquedebola.com.br" TARGET="_BLANK">http://www.bolaocraquedebola.com.br</a>
            </body>
        </html>';

        $titulo = "Palpite acertado: ".$palpite['t1nome']." - ".$palpite['t2nome'].": ".$palpite['mt_goal1']." - ".$palpite['mt_goal2'];

        $this->mail($html, $palpite['us_email'], $titulo);

    }

    function enviarPalpiteErrado($palpite) {
        $html = 
        '<html xmlns="http://www.w3.org/1999/xhtml"><head>
           <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
           <title>Untitled Document</title>
           </head>
           <body>
               Ola '.$palpite['us_username'].'<br></br>
               Infelizmente você errou no palpite do jogo '.$palpite['t1nome']." - ".$palpite['t2nome'].'<br>
               Ingresse na plataforma do bolão para ver o ranking.
               <br><br>
               Bolão Craque de Bola
               <a href="http://www.bolaocraquedebola.com.br" TARGET="_BLANK">http://www.bolaocraquedebola.com.br</a>
           </body>
       </html>';

       $titulo = "Palpite errado: ".$palpite['t1nome']." - ".$palpite['t2nome'].": ".$palpite['mt_goal1']." - ".$palpite['mt_goal2'];

       $this->mail($html, $palpite['us_email'], $titulo);
    }


    /**
     * Devuelve el email de todos los usuarios que palpitaron ese campeonato
     * @param champ
     */
    function usuariosPalpitaronCampeonato($champ) {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);   

        $c = new Application_Model_Championships();
        $result = $c->getUsuariosQuePalpitaron($champ);

        return $result;
    }
    

    /**
     * Envia email do ranking para todos os usuarios que palpitaron
     * @param champ
     */
    function emailrankingAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);    


        $c = new Application_Model_Championships();
        $campeonato = $c->getChamp($params['champ']);
        $ranking = $c->ranking($params['champ']);

        $emails = $this->usuariosPalpitaronCampeonato($params['champ']);

        $this->enviarRanking($campeonato, $ranking, $emails);

        $result['campeonato'] = $campeonato;
        $result['ranking'] = $ranking;

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
    }

    function enviarRanking($campeonato, $ranking, $emails) {
        $html = '<html xmlns="http://www.w3.org/1999/xhtml"><head>
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
              <tbody><tr>
                <td align="left" valign="top" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><a href="http://www.bolaocraquedebola.com.br" TARGET="_blank"><img src="http://www.bolaocraquedebola.com.br/public/assets/img/banneremail.jpg" width="583" height="118"></a></td>
              </tr>
              <tr>
                <td align="left" valign="top" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tbody><tr>
                    <td width="35" align="left" valign="top">&nbsp;</td>
                    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tbody><tr>
                        <td align="center" valign="top">
                            <div style="color:#245da5; font-family:Times New Roman, Times, serif; font-size:48px;">'.$campeonato['ch_nome'].'</div>
                            <div style="font-family: Verdana, Geneva, sans-serif; color:#898989; font-size:12px;">'.date('d-m-Y H:i').'</div>
                      </tr>
                      <tr>
                        <td align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#525252;">
                        
                        <div style="color:#3482ad; font-size:19px;">
                        
        </div>
                        <br>
        
        <div class="box">
                                <div class="box-header">
                                    <h2><i class="fa fa-align-justify"></i><span class="break"></span>Ranking</h2></div><div class="box-content">
                            <div class="row">
                            
                            
                            <div style="" id="958" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                                <div class="smallstat box">';
            
                    for ($j = 0; $j < count($ranking); $j = $j + 1) {
              
                        $r = $ranking[$j];
                        
                        $html = $html. '<table width="100%">
                            <tbody>
                                    <tr>
                                            <td style="text-align:center;width:10%"><b>'.($j+1).'</b></td>
                                            <td style="text-align:center;width:10%"><b>'.$r['points'].'</b></td>
                                            <td style="width:80%"><b>'.$r['rk_username'].'</b></td>
                                    </tr>
                            </tbody>
                        </table>';
                                       
                        }
                    
        
                                                                
                        $html = $html. '
                                </div>
                        </div>
                        
                        
                    <div class="form-action">                              
                            </div>
                            </div>
                        </div>
                </div>
        
        <br>
        
        
            </td>
                      </tr>
                      <tr>
                        <td align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#525252;">&nbsp;</td>
                      </tr>
                    </tbody></table></td>
                    <td width="35" align="left" valign="top">&nbsp;</td>
                  </tr>
                </tbody></table></td>
              </tr>
              <tr>
                <td align="left" valign="top" bgcolor="#3d90bd" style="background-color:#3d90bd;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tbody><tr>
                    <td width="35">&nbsp;</td>
                    <td height="50" valign="middle" style="color:#FFFFFF; font-size:11px; font-family:Arial, Helvetica, sans-serif;"><a href="http://www.bolaocraquedebola.com.br" TARGET="_BLANK"><b>Bolão Craque de Bola</b></a><br>
                    <a href="https://goo.gl/ds1vGn" TARGET="_BLANK">App Android</a></td>
                    <td width="35">&nbsp;</td>
                  </tr>
                </tbody></table></td>
              </tr>
          </tbody></table>
            <br>
            <br></td>
          </tr>
        </tbody></table>
        
        </div>
        
        
        
        </body></html>';

        for ($i = 0; $i < count($emails); $i = $i + 1) {
            $this->mail($html, $emails[$i], "Ranking: ".$campeonato['ch_nome']);
        }
    }
}

