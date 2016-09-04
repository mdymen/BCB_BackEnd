<?php

include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/championships.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/matchs.php';

class RegisterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    public function championshipAction() {

    }
    
    public function addchampionshipAction() {
        $params = $this->_request->getParams();
        
        $championship = new Application_Model_Championships();
        $championship->save($params);
        
        $this->redirect("/register/championship");
    }
    
    public function teamAction() {
        
    }
    
    public function addteamAction() {
        $params = $this->_request->getParams();
        
        $championship = new Application_Model_Teams();
        $championship->save($params);
        
        $this->redirect("/register/team");   
    }        
    
    public function pencaAction() {
        $championship = new Application_Model_Championships();
        $this->view->championships = $championship->load();
    }
    
    public function addpencaAction() {
        $params = $this->_request->getParams();
        
//        print_r($params);
//        die(".");
        
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();

        $params['pn_iduser'] = $data->us_id;
        
        $penca = new Application_Model_Penca();
        $penca->save($params);
        
        $params['up_idpenca'] = $params['tm_idchampionship'];
        $params['up_iduser'] = $data->us_id;
        
        $penca->save_userpenca($params);
        
        $this->redirect("/register/penca");
    }
    
    public function matchAction() {
     
        $params = $this->_request->getParams();
        
        $team = new Application_Model_Teams();
        $teams1 = $team->load($params['championship']);
        $teams2 = $teams1;
        $championship = $params['championship'];
        
        
        $this->view->team1 = $teams1;
        $this->view->team2 = $teams2;
        $this->view->championship = $championship;
      
    }
    
    public function addmatchAction() {
        $params = $this->_request->getParams();
        
        $match = new Application_Model_Matchs();
        $j = 0;
        for ($i = 0; $i < count($params); $i = $i + 1) {
            $id1 = $params['tm_idchampionship1'.$j];
            $id2 = $params['tm_idchampionship2'.$j];
            
            $match->save(array(
                'team1' => $id1,
                'team2' => $id2,
                'date' => date('d-n-y'),
                'championship' => $params['championship'],
                'round' => 1
            ));
            $j = $j + 1;
        }
        
        $this->redirect("/register/match");
    }
    
    public function addteamsAction() {
  
        
    }
    
    public function addteamspostAction() {
        $params = $this->_request->getParams();
        
        $champion = $params['tm_idchampionship'];
        
        $teams = $params['tm_name'];
        $teams = explode(",", $teams);
        
        $team_save = new Application_Model_Teams();
                    
        for ($i = 0; $i < count($teams); $i = $i + 1) {
            $obj_team = array( 
                'tm_name'=> $teams[$i], 
                'tm_idchampionship' => $champion
            );
            
            $team_save->save($obj_team);
        }
        
        print_r($teams);
        die(".");
    }
  
}

