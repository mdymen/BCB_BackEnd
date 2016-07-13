<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Row
 *
 * @author Dell
 */
class Decorators_Combobox extends Zend_Form_Decorator_Abstract {
    
 //   protected $_format = '<input id="%s" name="%s"  class="form-control" placeholder="%s" type="%s" value="%s"/>';
    public function render($content)
    {
        $_format = '<select name="ID_CURSO_CR" id="Curso">';

        $value = ($this->getElement()->getValue());

        $element = $this->getElement();
        $options = $element->options;

        $i = 0;
        $select = '';
        foreach ($options as $e) {
            if ($value == $e['ID_ID_CR']) {
                $select = "selected";
            }
            $_format .= '<option value="'.$e['ID_ID_CR'].'" '.$select.'>'.$e['ST_IDENT_CR'].'</option>';
            $select = '';
       }
            
       $_format .= '</select>';
        
        $name    = htmlentities($element->getFullyQualifiedName());
        $id      = htmlentities($element->getId());
        $row     = htmlentities($element->getAttrib('row'));
        $col     = htmlentities($element->getAttrib('col'));
        $type    = htmlentities($element->getAttrib('type'));
        $icono  = htmlentities($element->getAttrib('icono'));
        $value   = htmlentities($element->getValue());
        
        
        $placeholder = htmlentities($element->getAttrib("placeholder"));
        
        $markup  = sprintf($_format,$id, $name, $placeholder, $type, $value);

        if ($icono != '') {
            $markup = '<span class="input-icon icon-right">'.$markup.'<i class="'.$icono.'"></i></span>';
        }
        
        $markup = '<div class="form-group">'.$markup.'</div>';
        
        
        
        if ($col != '') {
            $markup = '<div class="'.$col.'" style="padding-left: 0px !important">'.$markup.'</div>';
        }
        
        if ($row == 'yes') {
            $markup = '<div class="row">'.$markup.'</div>';
        }
        
        return $markup;
    }
}
