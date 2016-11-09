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
        
        if (strcmp($url[3], "team") == 0) {
            $breadcrumb = "Times / ";
        }
        
        if (strcmp($url[3], "campeonatos") == 0) {
            $breadcrumb = "Campeonatos / ";
        }
        
        

        if (!empty($url[4])) {
            
            $url_final  =   explode("?", $url[4]);
            
            if (strcmp($url_final[0], "meusbaloes") == 0) {
                $breadcrumb = $breadcrumb." Meus Baloes ";
            }

            if (strcmp($url[4], "pencas") == 0) {
                $breadcrumb = $breadcrumb." Buscar Pencas ";
            }

            if (strcmp($url[4], "pencas") == 0) {
                $breadcrumb = $breadcrumb." Buscar Pencas ";
            }
            
            if (strcmp($url_final[0], "team") == 0) {
                $breadcrumb = $breadcrumb." Nome do time ";
            }
        }
    }
    
//    print_r($url);
//    die(".");
    
    
    return $breadcrumb;
}