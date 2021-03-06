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
include APPLICATION_PATH.'/models/users.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH."/helpers/box.php";
include APPLICATION_PATH."/helpers/html.php";
include APPLICATION_PATH."/helpers/translate.php";
class UsuarioController extends Zend_Controller_Action
{
    public function indexAction() {
        $params = $this->_request->getParams();

        $results = new Application_Model_Users();
        
        if (empty($params['usuario'])) {
            $id_user = $this->getIdUser();
        } else {
            $id_user = $params['usuario'];
        }
        
        $r = $results->getPalpitesUsuario($id_user);
        
        $this->view->lostMatches = $r['erros']; //$results->getLostMatches($id_user);
        $this->view->winMatches = $r['acertos']; //$results->getWonMatches($id_user);
        $this->view->playedMatches = $r['palpitados'];//$results->getPlayedMatches($id_user);
        $this->view->totalPoints = $r['pontos']; //$results->getPoints($id_user);
        $this->view->usuario = $id_user;
//        $this->view->position = $results->getPoisition($id_user);
//        
//        print_r($this->view->position);
//        die(".");
    }

    /**
     * Devuelve la informacion de palpites del usuario
     * total de palpites errados, total de palpites acertados, total de palpites 
     * y total de puntos realizados
     * @param usuario
     */
    public function infopalpitesusuarioAction() {
        
        $this->getResponse()
        ->setHeader('Content-Type', 'application/json');
       
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body); 

        $results = new Application_Model_Users();
        
        if (empty($params['usuario'])) {
            $id_user = $this->getIdUser();
        } else {
            $id_user = $params['usuario'];
        }
        
        $r = $results->getPalpitesUsuario($id_user);
        
        $result['erros'] = $r['erros']; //$results->getLostMatches($id_user);
        $result['acertos'] = $r['acertos']; //$results->getWonMatches($id_user);
        $result['palpitados'] = $r['palpitados'];//$results->getPlayedMatches($id_user);
        $result['pontos'] = $r['pontos']; //$results->getPoints($id_user);
        //$result = $id_user;

        $this->_helper->json($result);
       
    } catch (Exception $e) {
        $this->_helper->json($e->getMessage());
    }


    }
    
    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }
    
    public function getPassUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_password'];
    }
    
    public function salvarsenhaAction() {
        $params = $this->_request->getParams();
        
        $senha = $params['senha'];
        $novasenha = $params['novasenha'];
        
        $resposta = "";
        if (strcmp($senha,$this->getPassUser()) == 0) {
            $user = new Application_Model_Users();
            $user->setNovaSenha($novasenha, $this->getIdUser());
            $resposta = 200;
        } else {
            $resposta = 400;
        }
        
         $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($resposta);
    }
    
    public function puntuacaoAction() {
        $params = $this->_request->getParams();
        $us_id = $params['usuario'];
        
        $users = new Application_Model_Users();
        
        $matches = $users->getMatchesWonMatches($us_id);
        
        $this->view->matches = $matches;
        
    }
    public function posicaoglobalAction() {
        $params = $this->_request->getParams();
        
    }
    public function acertadosAction() {
        $params = $this->_request->getParams();
        $us_id = $params['usuario'];
        
        $users = new Application_Model_Users();
        
        $matches = $users->getMatchesWonMatches($us_id);
        
        $this->view->matches = $matches;
    }
    public function erradosAction() {
        $params = $this->_request->getParams();
        $us_id = $params['usuario'];
        
        $users = new Application_Model_Users();
        
        $matches = $users->getMatchesLostMatches($us_id);
        
        $this->view->matches = $matches;
        
    }
    
    public function uploadimageAction() {
        include_once APPLICATION_PATH.'/forms/FotoPerfil.php';
        $form = new Forms_FotoPerfil();
        
        $base = APPLICATION_PATH."/../public/img/perfil/";
        
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData)) {
                $name = $form->getValues();
                $ext = explode('.',$name['imgPerfil']);
                $data = $this->data;
                $new_name = $data['ST_USUARIO_USU'].'.'.$ext[1];
                // success - do something with the uploaded file
                rename($base.$name['imgPerfil'], $base.$new_name);
                $this->redirect('usuario/index');
                    
            }
        }
    }
    
    public function salvaropcoesAction() {
        $params = $this->_request->getParams();
        
        $palpites_publicos = $params['palpitespublicos'];
        $puntuacao_publica = $params['puntuacaopublica'];
        
        $id_user = $this->getIdUser();
        
        $user = new Application_Model_Users();
        
        $user->save_opcoes($id_user, array('us_palppublicos' => $palpites_publicos, 'us_puntpublica' => $puntuacao_publica));
        
         $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($palpites_publicos);
        
    }
    
    public function palpitadosAction() {
        $params = $this->_request->getParams();
        
        $us = $params['usuario'];
        
        $user = new Application_Model_Users();
        
        $this->view->palpitados = $user->historico_palpites($us);
    }
    
    public function cadastrarusuarioAction() {
        $params = $this->_request->getParams();
        
        $username = $params['username'];
        
        $user = new Application_Model_Users();
        $user->registerUsernameFacebook($username, $this->getIdUser());
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }

    /**
     * Guarda la informacion del usuario correspondiente al recibimiento de emails
     * @param params tiene @param res_pal, @param res_rod_pal, @param info_rod_pal
     * @param iduser
     */
    public function emailconfiguracionAction() {
        try {
            $body = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($body);    
            
            $u = new Application_Model_Users();
            $u->emailConfiguracion($params);

            $this->getResponse()
            ->setHeader('Content-Type', 'application/json');
        
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
        
            $this->_helper->json($params);
        } catch (Exception $e) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $this->_helper->json($e);
        }

    }

    public function uploadfileAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        try {
            /*$body = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($body);
        */
            print_r($_FILES['file']);
            die(".");

            $this->_helper->json($this->getRequest()->getContent());
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }
}
