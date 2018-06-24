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
        $root = 'http'. '://' . $_SERVER['HTTP_HOST'] . '/public';

        $this->setAction($root."/usuario/uploadimage")->setMethod("post");

        $this->setAttrib('enctype', 'multipart/form-data');       
        
        $file = new Zend_Form_Element_File('imgPerfil', array('class' => 'btn btn-blue col-xs-7 col-sm-12', 'style' => 'padding: 0px'));
        $file->setDestination(APPLICATION_PATH."/../public/assets/img/perfil");
        $file->setLabel('Avatar');
        $file->addDecorator('HtmlTag', array(
            'tag' => 'dd',
            'style' => 'margin-left:-0px'
        ));
        $file->setAttrib('style', 'display:none');
        
        $register = new Zend_Form_Element_Button('Aceitar', array('type' => 'submit', 'class' => 'btn btn-success '));
        $register->removeDecorator('DtDdWrapper');

        $id = new Zend_Form_Element_Hidden('id');
        
        $this->addElement($file);
        $this->addElement($register);
        $this->addElement($id);
    }
        
}
