<?php

function breadcrumb() {
        
    $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    $url  =   explode("/", $uri);

    $nivel1 = $url[3];

    if (!empty($url[4])) {
        $nivel2 = explode("?", $url[4]);    
        $nivel2 = $nivel2[0];
    } else {
        $nivel2 = "index";
    }
    
    $array = array();
    
    if (empty($url[4]) && empty($url[3])) { 
        $nivel1 = 'in';
        $nivel2 = 'in';
        $array[$nivel1][$nivel2] = 'Tela inicial';
    }

    $array['index']['index'] = 'Tela inicial';
    $array['usuario']['index'] = 'Minha conta';
    $array['curso']['cursos'] = 'Cursos';
    $array['credito']['index'] = utf8_encode('Créditos');
    $array['credito'][''] = utf8_encode('Créditos');
    $array['perguntas']['index'] = 'Minhas perguntas';
    $array['perguntas'][''] = 'Minhas perguntas';
    $array['curso']['novo'] = 'Criar novo curso';
    $array['curso']['cursoslideedit'] = utf8_encode('Edição do curso');
    $array['curso']['ficha'] = 'Ficha';    
    $array['curso']['vercurso'] = 'Tela principal do curso';
    $array['curso']['meuscursos'] = 'Meus cursos';
    
//    echo '<div class="header-title">
//                <h1>';
//                    echo $array[$nivel1][$nivel2];
//                echo '</h1>
//            </div>';  
}