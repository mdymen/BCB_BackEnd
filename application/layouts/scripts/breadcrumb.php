<?php

function breadcrumb() {
        
    $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    $url  =   explode("/", $uri);

    $breadcrumb = "";
    
    if (strcmp($url[3], "") == 0) {
        $breadcrumb = "Home ";
    } else {
    
        if (strcmp($url[3], "penca") == 0) {
            $breadcrumb = "Penca / ";
        }
        
        if (!empty($url[4])) {
            if (strcmp($url[4], "meusbaloes") == 0) {
                $breadcrumb = $breadcrumb." Meus Baloes ";
            }

            if (strcmp($url[4], "pencas") == 0) {
                $breadcrumb = $breadcrumb." Buscar Pencas ";
            }

            if (strcmp($url[4], "pencas") == 0) {
                $breadcrumb = $breadcrumb." Buscar Pencas ";
            }
        }
    }
    
//    print_r($url);
//    die(".");
    
    
    return $breadcrumb;
}