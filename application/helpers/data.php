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
    
}
