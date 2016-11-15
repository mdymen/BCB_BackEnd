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
include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH."/helpers/data.php";
//include APPLICATION_PATH."/helpers/html.php";
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
        
        $this->view->lostMatches = $results->getLostMatches($id_user);
        $this->view->winMatches = $results->getWonMatches($id_user);
        $this->view->playedMatches = $results->getPlayedMatches($id_user);
        $this->view->totalPoints = $results->getPoints($id_user);
        $this->view->usuario = $id_user;
//        $this->view->position = $results->getPoisition($id_user);
//        
//        print_r($this->view->position);
//        die(".");
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
}
