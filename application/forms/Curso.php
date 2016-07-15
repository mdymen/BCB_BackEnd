<?php

include APPLICATION_PATH.'/decorators/decorator1.php';
class Form_Curso extends Zend_Form {
        
    function init() {
        
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/bobby/public';
        
        $this->setAction($root."/curso/addcurso")->setMethod("post");

        $decorator = new Decorators_Decorator1();
        $id = new Zend_Form_Element_Text('ST_IDENT_CR', array('placeholder' => 'Identificador do curso', 'icono' => 'fa fa-key'/*, 'col' => 'col-sm-6'*/));
        $id->addDecorator($decorator);
        
        $decorator1 = new Decorators_Decorator1();        
        $nome = new Zend_Form_Element_Text('ST_NOME_CR"', array('placeholder' => 'Nome do curso'));
        $nome->addDecorator($decorator1);
        
        $decorator2 = new Decorators_Decorator1();  
        $custo = new Zend_Form_Element_Text('VL_CUSTO_CR', array('placeholder' => 'Custo', 'icono' => 'fa fa-dollar')); 
        $custo->addDecorator($decorator2);
        
        $decorator3 = new Decorators_Decorator1(); 
        $des = new Zend_Form_Element_Textarea('ST_DESCR_CR', array('placeholder' => 'Descripcion'));
        $des->addDecorator($decorator3);    
        
        $register = new Zend_Form_Element_Button('Registrar', array('type' => 'submit', 'class' => 'btn btn-blue'));
        
        $this->addElements(array($id, $nome, $custo, $des,$register));
        
    }
}
