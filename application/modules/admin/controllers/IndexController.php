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
        <td align="left" valign="top" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><img src="images/header.jpg" width="583" height="118"></td>
      </tr>
      <tr>
        <td align="left" valign="top" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody><tr>
            <td width="35" align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody><tr>
                <td align="center" valign="top">
                	<div style="color:#245da5; font-family:Times New Roman, Times, serif; font-size:48px;">Newsletter Title</div>
                  <div style="font-family: Verdana, Geneva, sans-serif; color:#898989; font-size:12px;">Month Day, Year</div></td>
              </tr>
              <tr>
                <td align="left" valign="top"><img src="images/pic1.jpg" width="512" height="296" vspace="10"></td>
              </tr>
              <tr>
                <td align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#525252;">
                
                <div style="color:#3482ad; font-size:19px;">
                

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum magna enim, volutpat venenatis eros.</div>
                <br>

<div class="box">
                        <div class="box-header">
                            <h2><i class="fa fa-align-justify"></i><span class="break"></span>Próximos Jogos</h2></div><div class="box-content">
                    <div class="row"><div style="" id="958" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box"><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?champ=14">Brasileirão 2017</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?rodada=153&amp;champ=14">Rodada 22</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b>Sabado 26 de Ago. 21:00hs</b></td></tr></tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="292"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/gremio.png"><a href="/public/team/team?team=292&amp;champ=14">Grêmio</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="958" style="text-align:center" class="form-control numeros_input" value="" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="290"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/sportrecife.png"><a href="/public/team/team?team=290&amp;champ=14">Sport Recife</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="958" style="text-align:center" class="form-control numeros_input" value="" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><div id="725" style="margin: 15px 0 0 0; "><span class="label label-important" style="margin-right:70px">Encerrado</span><span style="padding-right:10px" class="ac_958">0.00</span><div id="dvInfo_958" style="display:none"></div></div>    
                        </div>
                </div><div style="" id="959" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box"><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?champ=14">Brasileirão 2017</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?rodada=153&amp;champ=14">Rodada 22</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b>Domingo 27 de Ago. 11:00hs</b></td></tr></tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="289"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/avai.png"><a href="/public/team/team?team=289&amp;champ=14">Avaí</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="959" style="text-align:center" class="form-control numeros_input" value="" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="307"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/Chapecoense.png"><a href="/public/team/team?team=307&amp;champ=14">Chapecoense</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="959" style="text-align:center" class="form-control numeros_input" value="" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><div id="512" style="margin: 15px 0 0 0; "><span class="label label-important" style="margin-right:70px">Encerrado</span><span style="padding-right:10px" class="ac_959">0.00</span><div id="dvInfo_959" style="display:none"></div></div>    
                        </div>
                </div><div style="" id="931" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box"><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?champ=16">Campeonato Uruguayo Clausura 2017</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?rodada=160&amp;champ=16">Rodada 2</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b>Domingo 27 de Ago. 15:30hs</b></td></tr></tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="351"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/danubio.gif"><a href="/public/team/team?team=351&amp;champ=16">Danubio</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="931" style="text-align:center" class="form-control numeros_input" value="1" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="352"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/nacional.gif"><a href="/public/team/team?team=352&amp;champ=16">Nacional</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="931" style="text-align:center" class="form-control numeros_input" value="2" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><div id="482" style="margin: 15px 0 0 0; "><a href="/public/penca/bolao?rodada=160&amp;champ=16"><span class="label label-success" style="margin-right:70px">Palpitar</span></a><span style="padding-right:10px" class="ac_931">0.00</span><div id="dvInfo_931" style="display:none"></div></div>    
                        </div>
                </div><div style="" id="931" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box"><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?champ=16">Campeonato Uruguayo Clausura 2017</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?rodada=160&amp;champ=16">Rodada 2</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b>Domingo 27 de Ago. 15:30hs</b></td></tr></tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="351"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/danubio.gif"><a href="/public/team/team?team=351&amp;champ=16">Danubio</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="931" style="text-align:center" class="form-control numeros_input" value="1" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="352"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/nacional.gif"><a href="/public/team/team?team=352&amp;champ=16">Nacional</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="931" style="text-align:center" class="form-control numeros_input" value="2" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><div id="43" style="margin: 15px 0 0 0; "><a href="/public/penca/bolao?rodada=160&amp;champ=16"><span class="label label-success" style="margin-right:70px">Palpitar</span></a><span style="padding-right:10px" class="ac_931">0.00</span><div id="dvInfo_931" style="display:none"></div></div>    
                        </div>
                </div><div style="" id="933" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box"><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?champ=16">Campeonato Uruguayo Clausura 2017</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?rodada=160&amp;champ=16">Rodada 2</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b>Domingo 27 de Ago. 15:30hs</b></td></tr></tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="346"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/racing.gif"><a href="/public/team/team?team=346&amp;champ=16">Racing</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="933" style="text-align:center" class="form-control numeros_input" value="1" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="348"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/tanquesisley.gif"><a href="/public/team/team?team=348&amp;champ=16">El Tanque Sisley</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="933" style="text-align:center" class="form-control numeros_input" value="1" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><div id="50" style="margin: 15px 0 0 0; "><a href="/public/penca/bolao?rodada=160&amp;champ=16"><span class="label label-success" style="margin-right:70px">Palpitar</span></a><span style="padding-right:10px" class="ac_933">0.00</span><div id="dvInfo_933" style="display:none"></div></div>    
                        </div>
                </div><div style="" id="935" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box"><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?champ=16">Campeonato Uruguayo Clausura 2017</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?rodada=160&amp;champ=16">Rodada 2</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b>Domingo 27 de Ago. 15:30hs</b></td></tr></tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="345"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/plazacolonia.gif"><a href="/public/team/team?team=345&amp;champ=16">Plaza Colonia</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="935" style="text-align:center" class="form-control numeros_input" value="1" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="350"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/wanderers.gif"><a href="/public/team/team?team=350&amp;champ=16">Wanderers</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="935" style="text-align:center" class="form-control numeros_input" value="2" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><div id="158" style="margin: 15px 0 0 0; "><a href="/public/penca/bolao?rodada=160&amp;champ=16"><span class="label label-success" style="margin-right:70px">Palpitar</span></a><span style="padding-right:10px" class="ac_935">0.00</span><div id="dvInfo_935" style="display:none"></div></div>    
                        </div>
                </div><div style="" id="936" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box"><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?champ=16">Campeonato Uruguayo Clausura 2017</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?rodada=160&amp;champ=16">Rodada 2</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b>Domingo 27 de Ago. 15:30hs</b></td></tr></tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="355"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/sudamerica.gif"><a href="/public/team/team?team=355&amp;champ=16">Sud América</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="936" style="text-align:center" class="form-control numeros_input" value="2" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="358"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/campeonatouruguayo2017/rampla.gif"><a href="/public/team/team?team=358&amp;champ=16">Rampla Jrs</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="936" style="text-align:center" class="form-control numeros_input" value="1" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><div id="355" style="margin: 15px 0 0 0; "><a href="/public/penca/bolao?rodada=160&amp;champ=16"><span class="label label-success" style="margin-right:70px">Palpitar</span></a><span style="padding-right:10px" class="ac_936">0.00</span><div id="dvInfo_936" style="display:none"></div></div>    
                        </div>
                </div><div style="" id="960" class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12 col-xxs-6">
                        <div class="smallstat box"><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?champ=14">Brasileirão 2017</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b><a href="/public/campeonatos/index?rodada=153&amp;champ=14">Rodada 22</a></b></td></tr></tbody></table><table width="100%"><tbody><tr><td style="text-align:center"><b>Domingo 27 de Ago. 16:00hs</b></td></tr></tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="304"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/Bahia.png"><a href="/public/team/team?team=304&amp;champ=14">Bahia</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="960" style="text-align:center" class="form-control numeros_input" value="" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><table><tbody><tr>
            <td width="55%" style="text-align:left"><span id="293"><img width="25px;" height="21px;" src="http://www.bolaocraquedebola.com.br//assets/img/brasileirao2017/Botafogo.png"><a href="/public/team/team?team=293&amp;champ=14">Botafogo</a></span></td><td width="35%">
                <div class="row">
                <div class="col-xs-10 col-sm-10 col-lg-10 numeros">                    
                    <input id="960" style="text-align:center" class="form-control numeros_input" value="" type="text">
                </div>
                </div>
            </td>
            </tr>
        </tbody></table><div id="974" style="margin: 15px 0 0 0; "><a href="/public/penca/bolao?rodada=153&amp;champ=14"><span class="label label-success" style="margin-right:70px">Palpitar</span></a><span style="padding-right:10px" class="ac_960">0.00</span><div id="dvInfo_960" style="display:none"></div></div>    
                        </div>
                </div><div class="form-action">                              
                    </div>
                    </div>
                </div>
        </div>

<br>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody><tr>
    <td width="13%"><b><img src="images/tweet.gif" alt="" width="24" height="23"> <img src="images/facebook.gif" alt="" width="24" height="23"></b></td>
    <td width="87%" style="font-size:11px; color:#525252; font-family:Arial, Helvetica, sans-serif;"><b>Hours: Mon-Fri 9:30-5:30, Sat. 9:30-3:00, Sun. Closed <br>
      Customer Support: support@companyname.com</b></td>
  </tr>
</tbody></table></td>
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
            $this->mail($x, $emails[$i], "testando");
        }

    }
    
    public function mail($body, $email, $subject) {
        $config = array('ssl' => 'ssl',
            'auth' => 'login',
            'username' => 'bolaocraquedebola16@gmail.com',
            'password' => 'E3b3c4f5h5931',
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
}

