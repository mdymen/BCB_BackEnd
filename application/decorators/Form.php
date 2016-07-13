<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NewUser
 *
 * @author Dell
 */
class Decorators_Form extends Zend_Form_Decorator_Abstract {
                                                           
    protected $_format = '<form>%elements</form>';
 
    public function render($content)
    {
        $titulo = htmlentities($this->getElement()->getAttrib("titulo"));
        $id = htmlentities($this->getElement()->getAttrib("id"));
        
        
        
        $action = htmlentities($this->getElement()->getAttrib("action"));
        $method = htmlentities($this->getElement()->getAttrib("method"));
        $enctype = htmlentities($this->getElement()->getAttrib("enctype"));
        $classes = $this->getElement()->getAttrib("class");
        $icone = $this->getElement()->getAttrib('icone');
        $widget_btns = $this->getElement()->getAttrib('widget_btns');
        $width = $this->getElement()->getAttrib('width');
        
        $enctyestring= '';
        $id_ = '';
        $width_ = '';
        
        if ($width != '') {
            $width_ = 'style="width:'.$width.'"';
        }
        
        if ($id != '') {
            $id_ = 'id = "'.$id.'"';
        }
        
        $widget_buttons = '';
        if ($widget_btns != '') {
            $widget_buttons = '<div class="widget-buttons">
                                        <a href="#" data-toggle="collapse">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                        <a href="#" data-toggle="dispose">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>';
        }
        
        
        if ($enctype != ""){
            $enctyestring = 'enctype = multipart/form-data';
        }
        
        if ($classes == "") {
            $classes = "col-lg-6 col-sm-6 col-xs-12"; 
        }
        
        $icone_class = '';
        if ($icone != '') {
            $icone_class = '<i class="'.$icone.'"></i>';
        }
        
        
        $v = '<div class="'.$classes.'" '.$width_.'>
            <div class="widget">
                <div class="widget-header bordered-bottom bordered-blue">
                    <span class="widget-caption" id="tituloForm">'.$icone_class.' '.$titulo.'</span>
                        '.$widget_buttons.'
                </div>
                <div class="widget-body">
                    <div>';
        
        $form = $v."<form $id_ ".$enctyestring." action=".$action." method=".$method." >";
        $elements = $this->getElement()->getElements();
        
        foreach ($elements as $element) {
            $form .= $element->__toString();
        }
        $form .= "</form></div></div></div></div>";
        return $form;
    }
       /* $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = htmlentities($element->getLabel());
        $id      = htmlentities($element->getId());
        $placeholder = htmlentities($element->getAttrib("placeholder"));
 
        $markup  = sprintf($this->_format, $name, $label, $id, $name, $placeholder);
        return $markup;*
    }*/
}
