<?php

include APPLICATION_PATH.'/models/users.php';
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    public function registerAction() {
       $params = $this->_request->getParams();
       
       
//       print_r($params);
//       die(".");
//       
       $user = new Application_Model_Users();
       $user->save($params);
    }
    
    public function loginAction() {
       
    }
}

