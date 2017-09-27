<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';

include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/translate.php';
class Admin_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $params = $this->_request->getParams();
        
        $penca = new Application_Model_Championships();
        
        $champs = $penca->load();
        
        if (!empty($params['champ'])) {            
            $t_obj = new Application_Model_Teams();
            $teams = $t_obj->load_teams_para_jogo($params['champ']);
            
            $c = new Application_Model_Championships();
            
            $this->view->rondas = $c->getrondas($params['champ']);        
            
//            print_r($this->view->rondas);
            
            $this->view->teams = $teams;
            $this->view->champ = $params['champ'];
        }
        
        $this->view->championships = $champs;
    }
    
    public function registerAction() {}

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
    public function salvarjogoAction() {
        $params = $this->_request->getParams();
        
        $ronda = $params['ronda'];
        $date = $params['date'];
        $hora = $params['hora'];
        $team1 = $params['team1'];
        $team2 = $params['team2'];
        $champ = $params['champ'];  
        
        $helper = new Helpers_Data();
        $date = $helper->for_save($date);
        
        $info = array(
            'round' => $ronda, 
            'team1' => $team1,
            'team2' => $team2,
            'date' => $date.' '.$hora,
            'championship' => $champ);
        
        $m = new Application_Model_Matchs();
        $m->save($info);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }
    
    
    public function usuariosAction() {
        
        $u = new Application_Model_Users();
        
        $users = $u->users();
        
        $this->view->users = $users;
        
    }
    
        public function usuarios2Action() {
        
        $u = new Application_Model_Users();
        
        $users = $u->users();
        
        $this->view->users = $users;
        
    }
    
    public function usuariospalpitaronAction() {
        
        $u = new Application_Model_Users();
        
        $users = $u->userspalpitaron();
        
        $this->view->users = $users;
        
    }
    
    public function usuariospalpitaronjogoAction() {
        
        $u = new Application_Model_Users();
        
        $users = $u->usersPalpitaronJogo();
        
        $this->view->users = $users;
        
    }    
    
    public function usuariosganaramjogoAction() {
        $params = $this->_request->getParams();        
        
        $u = new Application_Model_Users();
        
        $users = $u->getUsuariosPalpitaronJogo($params['idmatch']);
        $users_palpitaran = $u->usersPalpitaronJogo($params['idmatch']);
        
        $this->view->users = $users;
        $this->view->users_palpitaran = $users_palpitaran;
        
    }
    
    
	
    public function adicionargranaAction() {
            $u = new Application_Model_Users();
            $users = $u->users();

            $this->view->users = $users;
    }   
    
    public function adicionargranapostAction() {
        $params = $this->_request->getParams();
        
        
       // print_r($params);
       // die(".");
        
        $u = new Application_Model_Users();
        $user = $u->load_user($params['usuario']);
        
//            print_r($user);
//        die(".");
        
        $cash = $user['us_cash'] + $params['valor'];
        
        $u->adicionesgrana($user['us_id'], $params['valor']);
        $u->update_cash($user['us_id'], $cash);
        
        $this->redirect("admin/index/adicionargrana");
    }
    
    public function emailsAction() {
        $params = $this->_request->getParams();
        
        $u = new Application_Model_Users();
        $emails = $u->getEmailsUsuario();
        
        $this->view->emails = $emails;

    }
    
    public function testemailresultadosAction() {
        
      $params = $this->_request->getParams();
        $champ = $params['champ'];
        $ronda = $params['rodada'];

        $m_obj = new Application_Model_Matchs();
        $matchs = $m_obj->load_matchs_byrodada($champ, $ronda);
        $matchs = $m_obj->setDatas($matchs);

        $users = $m_obj->getusuarios_do_campeonato($champ);
        
                 $emails = array();
        
        $e = 0;
        for ($i = 0; $i < count($users); $i = $i + 1) {
            
            if (!empty($users[$i]['us_email'])) {
                $emails[$e] = $users[$i]['us_email'];
                $e = $e + 1;
            }
            
        }
        
//        print_r($emails);
//        die(".");
//        
//
//        
//        $emails[0] = "msn@dymenstein.com";
//        $emails[1] = "martin@dymenstein.com";

//        print_r(count($matchs));
//        die(".");
        
        
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
      <tbody><tr>
        <td align="left" valign="top" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><a href="http://www.bolaocraquedebola.com.br/public" TARGET="_blank"><img src="http://www.bolaocraquedebola.com.br/public/assets/img/banneremail.jpg" width="583" height="118"></a></td>
      </tr>
      <tr>
        <td align="left" valign="top" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody><tr>
            <td width="35" align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody><tr>
                <td align="center" valign="top">
                	<div style="color:#245da5; font-family:Times New Roman, Times, serif; font-size:48px;">'.$matchs[0]['ch_nome'].'</div>
        <div style="font-family: Verdana, Geneva, sans-serif; color:#898989; font-size:12px;">Rodada: '.$matchs[0]['rd_round'].'</div></td>
              </tr>
              <tr>
                <td align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#525252;">
                
                <div style="color:#3482ad; font-size:19px;">
                
</div>
                <br>

<div class="box">
                        <div class="box-header">
                            <h2><i class="fa fa-align-justify"></i><span class="break"></span>Resultados</h2></div><div class="box-content">
                    <div class="row">
					
					
					<div style="" id="958" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box">';
	
            for ($j = 0; $j < count($matchs); $j = $j + 1) {
      
                $match = $matchs[$j];
                
                $x = $x. '<table width="100%">
                    <tbody>
                            <tr>
                                    <td style="text-align:center;width:5%"><b><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br/'.$match['t1logo'].'"></b></td>
                                    <td style="text-align:center;width:25%"><b>'.$match['t1nome'].'</b></td>
                                    <td style="text-align:center;width:10%"><b>'.$match['mt_goal1'].'</b></td>
                                    <td style="text-align:center;width:5%"><b>x</b></td>
                                    <td style="text-align:center;width:10%"><b>'.$match['mt_goal2'].'</b></td>
                                    <td style="text-align:center;width:25%"><b>'.$match['t2nome'].'</b></td>			
                                    <td style="text-align:center;width:5%"><b><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//'.$match['t2logo'].'"></b></td>			
                            </tr>
                    </tbody>
                </table>';
                               
                }
            

                                                        
$x = $x. '
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
            <td height="50" valign="middle" style="color:#FFFFFF; font-size:11px; font-family:Arial, Helvetica, sans-serif;"><a href="http://www.bolaocraquedebola.com.br/public" TARGET="_BLANK"><b>Bolão Craque de Bola</b></a><br>
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


            $this->mail($x, $emails[$i], "Rodada ".$matchs[0]['rd_round'].' '.$matchs[0]['ch_nome']);
        }
    }
    
    public function testmailAction() {
        
        $emails = array();
        
        $emails[0] = "msn@dymenstein.com";
        $emails[1] = "martin@dymenstein.com";

        
        
        
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
      <tbody><tr>
        <td align="left" valign="top" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><img src="http://www.bolaocraquedebola.com.br/public/assets/img/banneremail.jpg" width="583" height="118"></td>
      </tr>
      <tr>
        <td align="left" valign="top" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody><tr>
            <td width="35" align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody><tr>
                <td align="center" valign="top">
                	<div style="color:#245da5; font-family:Times New Roman, Times, serif; font-size:48px;">Brasileirão 2017</div>
                  <div style="font-family: Verdana, Geneva, sans-serif; color:#898989; font-size:12px;">Rodada 24</div></td>
              </tr>
              <tr>
                <td align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#525252;">
                
                <div style="color:#3482ad; font-size:19px;">
                
</div>
                <br>

<div class="box">
                        <div class="box-header">
                            <h2><i class="fa fa-align-justify"></i><span class="break"></span>Resultados</h2></div><div class="box-content">
                    <div class="row">
					
					
					<div style="" id="958" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box">
						<table width="100%">
	<tbody>
		<tr>
			<td style="text-align:center"><b><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/gremio.png"></b></td>
			<td style="text-align:center"><b>Gremio</b></td>
			<td style="text-align:center"><b>2</b></td>
			<td style="text-align:center"><b>x</b></td>
			<td style="text-align:center"><b>1</b></td>
			<td style="text-align:center"><b>Sport Recife</b></td>			
			<td style="text-align:center"><b><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/sportrecife.png"></b></td>			
		</tr>
	</tbody>
</table>						
<table width="100%">
	<tbody>
		<tr>
			<td style="text-align:center"><b><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/gremio.png"></b></td>
			<td style="text-align:center"><b>Gremio</b></td>
			<td style="text-align:center"><b>2</b></td>
			<td style="text-align:center"><b>x</b></td>
			<td style="text-align:center"><b>1</b></td>
			<td style="text-align:center"><b>Sport Recife</b></td>			
			<td style="text-align:center"><b><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/sportrecife.png"></b></td>			
		</tr>
	</tbody>
</table>
                        </div>
                </div>
				
				
			<div class="form-action">                              
                    </div>
                    </div>
                </div>
        </div>

<br>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td width="13%"><b><img src="images/tweet.gif" alt="" width="24" height="23"> <img src="images/facebook.gif" alt="" width="24" height="23"></b></td>
			<td width="87%" style="font-size:11px; color:#525252; font-family:Arial, Helvetica, sans-serif;"><b><br></b></td>
		</tr>
	</tbody>
</table>

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
            <td height="50" valign="middle" style="color:#FFFFFF; font-size:11px; font-family:Arial, Helvetica, sans-serif;"><b>Company Address:</b><br>
123 James Street,  Suite100, Long Beach CA, 90000, (000) 123  4567 </td>
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
            $this->mail($x, $emails[$i], "Resultados ");
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

