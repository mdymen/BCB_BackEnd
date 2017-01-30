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
        
        
        $dia = str_replace("January","de ".$t->_("January"), $dia);
        $dia = str_replace("February","de ".$t->_("February"), $dia);
        $dia = str_replace("March","de ".$t->_("March"), $dia);
        $dia = str_replace("April","de ".$t->_("April"), $dia);
        $dia = str_replace("May","de ".$t->_("May"), $dia);
        $dia = str_replace("June","de ".$t->_("June"), $dia);
        $dia = str_replace("July","de ".$t->_("July"), $dia);
        $dia = str_replace("August","de ".$t->_("August"), $dia);
        $dia = str_replace("September","de ".$t->_("September"), $dia);
        $dia = str_replace("October","de ".$t->_("October"), $dia);
        $dia = str_replace("November","de ".$t->_("November"), $dia);
        $dia = str_replace("December","de ".$t->_("December"), $dia);
        
        return $dia;
    }
    
        public static function paramesred($dia) {
        $t = Zend_Registry::get('translate');
        
        
        $dia = str_replace("Jan", "de ".$t->_("Jan"), $dia);
        $dia = str_replace("Feb", "de ".$t->_("Feb"), $dia);
        $dia = str_replace("Mar", "de ".$t->_("Mar"), $dia);
        $dia = str_replace("Apr", "de ".$t->_("Apr"), $dia);
        $dia = str_replace("May", "de ".$t->_("May"), $dia);
        $dia = str_replace("Jun", "de ".$t->_("Jun"), $dia);
        $dia = str_replace("Jul", "de ".$t->_("Jul"), $dia);
        $dia = str_replace("Aug", "de ".$t->_("Aug"), $dia);
        $dia = str_replace("Sep", "de ".$t->_("Sep"), $dia);
        $dia = str_replace("Oct", "de ".$t->_("Oct"), $dia);
        $dia = str_replace("Nov", "de ".$t->_("Nov"), $dia);
        $dia = str_replace("Dec", "de ".$t->_("Dec"), $dia);
        
        return $dia;
    }
    
    public static function day($date) {
        $time = strtotime($date);
        $newformat = date("l d M. H:i",$time)."hs";
        $newformat = Helpers_Data::paradia($newformat);
        $newformat = Helpers_Data::paramesred($newformat);
        
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
