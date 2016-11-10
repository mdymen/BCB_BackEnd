<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Perfil
 *
 * @author Martin Dymenstein
 */
class Forms_FotoPerfil extends Zend_Form{
    
    function init() {
        $root = 'http'. '://' . $_SERVER['HTTP_HOST'] . '/penca/public';
        
        $this->setAction($root."/usuario/uploadimage")->setMethod("post");

        $this->setAttrib('enctype', 'multipart/form-data');       
        //$this->setAttrib('style', 'display: none');
        
        $file = new Zend_Form_Element_File('imgPerfil', array('class' => 'btn btn-blue col-xs-7 col-sm-12'));
        $file->setDestination(APPLICATION_PATH);
        $file->removeDecorator('Label');
        
        $register = new Zend_Form_Element_Button('Aceitar', array('type' => 'submit', 'class' => 'btn btn-success '));
        $register->removeDecorator('DtDdWrapper');
        
        $this->addElement($file);
        $this->addElement($register);
    }
        
}
