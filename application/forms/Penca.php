<?php
include APPLICATION_PATH.'/decorators/decorator1.php';
include APPLICATION_PATH.'/decorators/Combobox.php';
//include APPLICATION_PATH.'/models/championships.php';
class Form_Penca extends Zend_Form {
        
    function init() {
        
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/penca/public';
        
        $this->setAction($root."/register/addpenca")->setMethod("post");

        $decorator = new Decorators_Decorator1();
        $id = new Zend_Form_Element_Text('pn_name', array('placeholder' => 'Nome da penca', 'icono' => 'fa fa-key'/*, 'col' => 'col-sm-6'*/));
        $id->addDecorator($decorator);
        
        $decorator1 = new Decorators_Decorator1();
        $valor = new Zend_Form_Element_Text('pn_value', array('placeholder' => 'Valor', 'icono' => 'fa fa-key'/*, 'col' => 'col-sm-6'*/));
        $valor->addDecorator($decorator1);
        
        $decorator3 = new Decorators_Combobox(); 
        $champs = new Zend_Form_Element_Select('tm_idchampionship', array('col' => 'col-sm-3')); 
 
        $champ = new Application_Model_Championships();
        $champ = $champ->load();
        
        $champs->addMultiOptions($champ);
        $champs->addDecorator($decorator3);
        
        $register = new Zend_Form_Element_Button('Registrar', array('type' => 'submit', 'class' => 'btn btn-blue'));
        
        $this->addElements(array($id, $valor, $champs, $register));
        
    }
}
