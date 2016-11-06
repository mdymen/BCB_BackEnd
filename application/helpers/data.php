<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author Martin Dymenstein
 */
class Helpers_Data {
    
    public function format($date) {
        $d = date_create($date);
        return date_format($d,'d/m/y H:i')."hs";
    }
    
    public function today() {
        return new Date('d/m/Y H:i');
    }
    
    public function for_save($date) {
        $res = explode("/",$date);
        return "20".$res[2]."-".$res[1]."-".$res[0];
    }
    
    public static function paradia($dia) {
        $dia = str_replace("Thursday", "Quinta", $dia);
        $dia = str_replace("Friday", "Sexta", $dia);
        $dia = str_replace("Monday", "Segunda", $dia);
        $dia = str_replace("Tuesday", "Terca", $dia);
        $dia = str_replace("Wednesday", "Quarta", $dia);
        $dia = str_replace("Saturday", "Sabado", $dia);
        $dia = str_replace("Sunday", "Domingo", $dia);
        
        return $dia;
    }
    
    public static function parames($mes) {

    }
    
    public static function day($date) {
        $time = strtotime($date);
        $newformat = date("l d M. H:i",$time)."hs";
        $newformat = Helpers_Data::paradia($newformat);
        
        //print_r($newformat);
        return $newformat;
    }
    
}
