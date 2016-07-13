<?php

class Decorators_Textarea extends Zend_Form_Decorator_Abstract {
    
    protected $_format = '<Textarea id="%s" name="%s" rows="%s"  class="form-control" placeholder="%s" type="%s" cols="%s" style="resize: none; %s">%s</Textarea>';
 
    public function render($content)
    {
        $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $id      = htmlentities($element->getId());
        $row     = htmlentities($element->getAttrib('row'));
        $col     = htmlentities($element->getAttrib('col'));
        $type    = htmlentities($element->getAttrib('type'));
        $icono  = htmlentities($element->getAttrib('icono'));
        $value   = htmlentities($element->getValue(), ENT_COMPAT | ENT_HTML401, "UTF-8");
        $rows = htmlentities($element->getAttrib('rows'));
        $cols = $element->getAttrib('cols');
        $style_col = "";

        
        if (empty($rows)) { $rows = 15; }
        if (!empty($cols)) { $style_col = "width:auto;";  }
        
        $placeholder = htmlentities($element->getAttrib("placeholder"));
        
        $markup  = sprintf($this->_format,$id, $name,$rows, $placeholder, $type,$cols, $style_col, $value);

        if ($icono != '') {
            $markup = '<span class="input-icon icon-right">'.$markup.'<i class="'.$icono.'"></i></span>';
        }
        
        $markup = '<div class="form-group">'.$markup.'</div>';
        
        
        
        if ($col != '') {
            $markup = '<div class="'.$col.'">'.$markup.'</div>';
        }
        
        if ($row == 'yes') {
            $markup = '<div class="row">'.$markup.'</div>';
        }

        return $markup;
    }
}
