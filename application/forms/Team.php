<?php
include APPLICATION_PATH.'/decorators/decorator1.php';
include APPLICATION_PATH.'/decorators/Combobox.php';
class Form_Team extends Zend_Form {
        
    function init() {
        
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/penca/public';
        
        $this->setAction($root."/register/addteam")->setMethod("post");

        $decorator = new Decorators_Decorator1();
        $id = new Zend_Form_Element_Text('tm_name', array('placeholder' => 'Nome do time', 'icono' => 'fa fa-key'/*, 'col' => 'col-sm-6'*/));
        $id->addDecorator($decorator);
        
        $decorator3 = new Decorators_Combobox(); 
        $champs = new Zend_Form_Element_Select('tm_idchampionship', array('col' => 'col-sm-3')); 
 
        $champ = new Models_Championship();
        $champ = $champ->load();
        
        $champs->addMultiOptions($champ);
        $champs->addDecorator($decorator3);
        
        $register = new Zend_Form_Element_Button('Registrar', array('type' => 'submit', 'class' => 'btn btn-blue'));
        
        $this->addElements(array($id, $champs, $register));
        
    }
}
