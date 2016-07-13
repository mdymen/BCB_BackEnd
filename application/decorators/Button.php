<?php

class Decorators_Button extends Zend_Form_Decorator_Abstract {
    
    protected $_format = '<button name="%s" id="%s" type="%s" class="%s" style="margin-right: 10px">%s</button>';

    public function render($content)
    {
        $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $id      = htmlentities($element->getId());
        $value   = htmlentities($element->getValue());
        $class   = htmlentities($element->getAttrib('class'));
        $type = htmlentities($element->getAttrib('type'));
        
        if ($type == '') {
            $type = "submit";
        }
        
        $markup  = sprintf($this->_format, $name, $id, $type, $class, $value);
        return $markup;
    }
}
