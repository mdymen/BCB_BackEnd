<?php

include APPLICATION_PATH.'/decorators/decorator1.php';
include APPLICATION_PATH.'/decorators/textarea.php';
include APPLICATION_PATH.'/decorators/button.php';

class Forms_Curso_Editar extends Zend_Form{
 function init() {
        
        $root = ('http') . '://' . $_SERVER['HTTP_HOST'] . '/aulas/public/admin';
        
        $this->setAction($root."/curso/editar")->setMethod("post");

        $this->setAttrib('enctype', 'multipart/form-data');
        
        $idhidden = new Zend_Form_Element_Hidden('ID_ID_CR');
        
        $decorator = new Decorators_Decorator1();
        $id = new Zend_Form_Element_Text('ST_IDENT_CR', array('placeholder' => 'Identificador do curso', 'icono' => 'fa fa-key', 'col' => 'col-sm-6'));
        $id->addDecorator($decorator);
        
        $decorator1 = new Decorators_Decorator1();        
        $nome = new Zend_Form_Element_Text('ST_NOME_CR"', array('placeholder' => 'Nome do curso'));
        $nome->addDecorator($decorator1);
        
        $decorator8 = new Decorators_Decorator1();        
        $subtitulo = new Zend_Form_Element_Text('ST_SUBTITULO_CR"', array('placeholder' => 'subtitulo'));
        $subtitulo->addDecorator($decorator8);
        
        $decorator2 = new Decorators_Decorator1();  
        $custo = new Zend_Form_Element_Text('VL_CUSTO_CR', array('placeholder' => 'Custo', 'icono' => 'fa fa-dollar', 'col' => 'col-sm-4')); 
        $custo->addDecorator($decorator2);
        
        $decorator7 = new Decorators_Textarea(); 
        $minides = new Zend_Form_Element_Textarea('ST_MINIDESCR_CR', array('placeholder' => 'Mini descricao', 'rows' => 5));
        $minides->addDecorator($decorator7);    
        
        $decorator3 = new Decorators_Textarea(); 
        $des = new Zend_Form_Element_Textarea('ST_DESCR_CR', array('placeholder' => 'Descripcion', 'rows' => 5));
        $des->addDecorator($decorator3);    
        
        $decorator4 = new Decorators_Textarea(); 
        $conteudo = new Zend_Form_Element_Textarea('ST_CONTEUDO_CR', array('placeholder' => 'Conteudo. Separe o conteudo usando ";"', 'rows' => 5));
        $conteudo->addDecorator($decorator4);  

        $decorator5 = new Decorators_Textarea(); 
        $objetivo = new Zend_Form_Element_Textarea('ST_OBJETIVO_CR', array('placeholder' => 'Objetivo', 'rows' => 5));
        $objetivo->addDecorator($decorator5);          

        $decorator6 = new Decorators_Textarea(); 
        $caract = new Zend_Form_Element_Textarea('ST_CARACT_CR', array('placeholder' => 'Caracteristicas. Separe as caracteristicas usando ";"', 'rows' => 5));
        $caract->addDecorator($decorator6);         
        
//        $file = new Zend_Form_Element_File('ST_IMAGEM_CR');
//        $file->setDestination(APPLICATION_PATH."/uploads");
        
        $buttondec = new Decorators_Button();
        $register = new Zend_Form_Element_Button('Salvar', array('type' => 'submit', 'class' => 'btn btn-blue', 'value' => 'Salvar'));
        $register->addDecorator($buttondec);
        
        $buttondec1 = new Decorators_Button();
        $excluir = new Zend_Form_Element_Button('Excluir', array('type' => 'submit', 'class' => 'btn btn-red', 'value'=> 'Excluir'));
        $excluir->addDecorator($buttondec1);
        
        $this->addElements(array($idhidden, $id, $custo, $nome, $subtitulo, $minides, $des, $objetivo, $conteudo, $caract, $register, $excluir));
        
    }
}
