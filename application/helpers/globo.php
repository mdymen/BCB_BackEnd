<?php

class Helpers_Globo {

    public function get($url) {
        $ch = curl_init();
            
        curl_setopt($ch, CURLOPT_URL, $url);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);

        curl_close ($ch); 

        return $server_output;
    }

}