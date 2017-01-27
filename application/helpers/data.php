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
    
   public static function formats($date) {
        $d = date_create($date);
        return date_format($d,'d/m/y');
    }
    
    public function today() {
        return new Date('d/m/Y H:i');
    }
    
    public function for_save($date) {
        $res = explode("/",$date);
        return "20".$res[2]."-".$res[1]."-".$res[0];
    }
    
    public static function antesDeHoras($date) {
		
        $config = new Zend_Config_Ini("config.ini");
        $diferenciaMinutos = $config->diferenciaminutos;
        $minutosParaFechar = $config->minutosparafechar;
        
        $totalDif = $diferenciaMinutos;//$minutosParaFechar;
        $dataHoraServer = date("Y-m-d H:i:s", strtotime($totalDif.' minutes', strtotime(date("Y-m-d H:i:s"))));
        
        $jogo = $date;
        
//        print_r($jogo);
//        print_r("<br>");
//        print_r($dataHoraServer);
        
		//print_r($diferenciaMinutos);
		//print_r($dataHoraServer."  -  ".$jogo);
		//print_r("----");
		
		
        if( strtotime($dataHoraServer)<=strtotime($jogo) )
        {        
            return true;
        }
        else
        {
            return false;
        }       
    }
    
    public static function paradia($dia) {
                $t = Zend_Registry::get('translate');
        
        $dia = str_replace("Thursday", $t->_("quinta"), $dia);
        $dia = str_replace("Friday", $t->_("sexta"), $dia);
        $dia = str_replace("Monday", $t->_("segunda"), $dia);
        $dia = str_replace("Tuesday", $t->_("terca"), $dia);
        $dia = str_replace("Wednesday", $t->_("quarta"), $dia);
        $dia = str_replace("Saturday", $t->_("sabado"), $dia);
        $dia = str_replace("Sunday", $t->_("domingo"), $dia);
        
        return $dia;
    }
    
    public static function parames($dia) {
        $t = Zend_Registry::get('translate');
        
        
        $dia = str_replace("January", "de ".$t->_("janeiro"), $dia);
        $dia = str_replace("February", "de Fevreiro", $dia);
        $dia = str_replace("March", "de Março", $dia);
        $dia = str_replace("April", "de Abril", $dia);
        $dia = str_replace("May", "de Maio", $dia);
        $dia = str_replace("June", "de Junho", $dia);
        $dia = str_replace("July", "de Julho", $dia);
        $dia = str_replace("August", "de Agosto", $dia);
        $dia = str_replace("September", "de Setembro", $dia);
        $dia = str_replace("October", "de Otubro", $dia);
        $dia = str_replace("November", "de Novembro", $dia);
        $dia = str_replace("December", "de Dezembro", $dia);
        
        return $dia;
    }
    
    public static function day($date) {
        $time = strtotime($date);
        $newformat = date("l d M. H:i",$time)."hs";
        $newformat = Helpers_Data::paradia($newformat);
        
        //print_r($newformat);
        return $newformat;
    }
    
        public static function day_semhour($date) {
        $time = strtotime($date);
        $newformat = date("l d F",$time);
        $newformat = Helpers_Data::paradia($newformat);
        $newformat = Helpers_Data::parames($newformat);
        
        //print_r($newformat);
        return $newformat;
    }
    
}
