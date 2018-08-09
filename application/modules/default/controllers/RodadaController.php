<?php
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
//include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/modules/default/controllers/BolaoController.php';
class RodadaController extends BolaoController
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {     
        
       $penca = new Application_Model_Championships();
        
       $champs = $penca->load();
       
       $this->view->championships = $champs;
       
       
    }
    
    /**
     * Salva la rodada
     * 
     * @param champ
     * @param rodada
     * @param suma
     * @param cambio
     */
    public function postAction() {
        try {

            $body = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($body);
            
            $this->info("[CREAR RODADA] creando rodada con los siguientes datos: ".print_r($params, true));
            
            if ($this->getRequest()->isPost()) {  
                $penca = new Application_Model_Championships();
                $penca->salvar_rodada($params['champ'], $params['rodada'], !empty($params['suma']) ? true : false, $params['cambio']);
                $this->info("[CREAR RODADA] Rodada creada con éxito. ");
                
            }
            
            $this->_helper->json($params);

        } catch (Exception $e) {
            $this->error("[CREAR RODADA] Error: ".$e->getMessage());
            $this->_helper->json($e->getMessage());
        }
    }
    
    public function rodadaatualAction() { 
       $params = $this->_request->getParams(); 
        
       $penca = new Application_Model_Championships();
        
       $champs = $penca->load();
       
       $this->view->championships = $champs;
       
       if (!empty($params['champ'])) {
           
           $this->view->rondas = $penca->getrondas($params['champ']);
            $this->view->champ = $params['champ'];    
       } 
    }
    
    public function setrodadaatualAction() {
        $params = $this->_request->getParams();
        
//        print_r($params);
//        die(".");
        
        $penca = new Application_Model_Championships();
        
        $penca->setRondaAtual($params['champ_selected'], $params['ronda']);
        
        $this->render("rodadaatual");
    }

    /**
     * Seta la rodada como actual en el campeonato
     * 
     * @param champ_selected
     * @param ronda
     */
    public function setrodadaAction() {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
         
        $campeonato = new Application_Model_Championships();        
        $campeonato->setRondaAtual($params['champ_selected'], $params['ronda']);
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }
    
}

