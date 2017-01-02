<?php

function breadcrumb() {
        
    $config = new Zend_Config_Ini("config.ini");
    
    $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    $url  =   explode("/", $uri);

    $breadcrumb = "";
    
    if (strcmp($url[3], "") == 0) {
        $breadcrumb = "Home ";
    } else {
    
        if (strcmp($url[3], "penca") == 0) {
            $breadcrumb = "Bolao ";
        }
        
        if (strcmp($url[3], "team") == 0) {
            $breadcrumb = "Times ";
        }
        
        if (strcmp($url[3], "campeonatos") == 0) {
            $breadcrumb = "Campeonatos ";
        }
        
        if (strcmp($url[3], "usuario") == 0) {
            $breadcrumb = "Meu Perfil ";
        }
        
        if (strcmp($url[3], "caixa") == 0) {
            $breadcrumb = '<a href="'.$config->hostpublic.'/caixa"> Caixa </a>';
        }
        
        

        if (!empty($url[4])) {
            
            $url_final  =   explode("?", $url[4]);
            
//            print_r($url_final);
//            die(".");
            
            
            
            if (strcmp($url_final[0], "transacoes") == 0) {
                $breadcrumb = $breadcrumb.' / <a href="'.$config->hostpublic.'/caixa/transacoes"> Transações </a>';
            }
            
            if (strcmp($url_final[0], "meuspalpites") == 0) {
                $breadcrumb = $breadcrumb.' / <a href="'.$config->hostpublic.'/penca/meuspalpites"> Meus Palpites </a>';
            }
            
            if (strcmp($url_final[0], "bolao") == 0) {
                $breadcrumb = $breadcrumb.' / <a href="'.$config->hostpublic.'/penca/bolao"> Palpitar </a>';
            }
            
            if (strcmp($url_final[0], "meusbaloes") == 0) {
                $breadcrumb = $breadcrumb." Meus Baloes ";
            }

            if (strcmp($url[4], "pencas") == 0) {
                $breadcrumb = $breadcrumb." Buscar Pencas ";
            }

            if (strcmp($url[4], "pencas") == 0) {
                $breadcrumb = $breadcrumb." Buscar Pencas ";
            }
            
//            if (strcmp($url_final[0], "team") == 0) {
//                $breadcrumb = $breadcrumb." Nome do time ";
//            }
        }
    }
    
//    print_r($url);
//    die(".");
    
    
    return $breadcrumb;
}