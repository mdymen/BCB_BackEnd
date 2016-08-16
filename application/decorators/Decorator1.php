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

//    <div class="controls">
//                        <div class="input-group col-sm-4">
//                          <span class="input-group-addon"><i class="fa fa-male"></i></span>
//                          <input type="text" id="ssn" class="form-control">
//                        </div>
//                        <span class="help-block col-sm-8">ex. 999-99-9999</span>
//                  </div>

        $placeholder = htmlentities($element->getAttrib("placeholder"));
        $input  = sprintf($_format,$name,$label,$id, $name, $placeholder, $type, $value);
        
        $markup = '';
        if ($icono != '') {
            $markup = '<span class="input-group-addon"><i class="'.$icono.'"></i></span>'.$markup;
        }
        
        $markup = '<div class="form-group"><div class="controls"><div class="input-group col-sm-4"> '.$markup.$input.'</div></div></div>';
        
        
        
        if ($col != '') {
            $markup = '<div class="'.$col.'" style="padding-left: 0px !important">'.$markup.'</div>';
            
        }
        
        if ($row == 'yes') {
            $markup = '<div class="row">'.$markup.'</div>';
        }
        

        
        

        
        
        return $markup;
    }
}
