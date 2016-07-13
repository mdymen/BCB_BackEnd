<?php
include APPLICATION_PATH.'/decorators/decorator1.php';
class Form_Championship extends Zend_Form {
        
    function init() {
        
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/penca/public';
        
        $this->setAction($root."/register/addchampionship")->setMethod("post");

        $decorator = new Decorators_Decorator1();
        $id = new Zend_Form_Element_Text('name', array('placeholder' => 'Nome do campeonato', 'icono' => 'fa fa-key'/*, 'col' => 'col-sm-6'*/));
        $id->addDecorator($decorator);
        
        $register = new Zend_Form_Element_Button('Registrar', array('type' => 'submit', 'class' => 'btn btn-blue'));
        
        $this->addElements(array($id, $register));
        
    }
}
