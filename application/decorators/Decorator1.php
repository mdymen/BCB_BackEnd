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
class Decorators_Decorator1 extends Zend_Form_Decorator_Abstract {
    
    
    public function render($content)
    {
        
        
        $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $id      = htmlentities($element->getId());
        $label   = $element->getAttrib('nomeCampo');
        $row     = htmlentities($element->getAttrib('row'));
        $col     = htmlentities($element->getAttrib('col'));
        $type    = htmlentities($element->getAttrib('type'));
        $icono  = htmlentities($element->getAttrib('icono'));
        $disabled = $element->getAttrib('disabled');
        $value   = htmlentities($element->getValue());
        $style = htmlentities($element->getAttrib('style'));
        
        if ($disabled == 'disabled') { $disabled = 'disabled="disabled"';}
        $_format = '<label for="%s">%s</label><input id="%s" name="%s"  class="form-control" '.$disabled.' placeholder="%s" type="%s" value="%s"/>';

        $placeholder = htmlentities($element->getAttrib("placeholder"));
        $markup  = sprintf($_format,$name,$label,$id, $name, $placeholder, $type, $value);

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
