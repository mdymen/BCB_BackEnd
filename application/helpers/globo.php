<?php

class Helpers_Globo {

    public function get($url) {

        $ch = curl_init($url);

      //  $ch = curl_init('https://api.globoesporte.globo.com/tabela/4b20b911-f174-4958-9be8-4033dc74f970/fase/primeira-fase-campeonato-paulista-2019/rodada/1/jogos/');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            
    //    curl_setopt($ch, CURLOPT_URL, "https://api.globoesporte.globo.com/tabela/4b20b911-f174-4958-9be8-4033dc74f970/fase/primeira-fase-campeonato-paulista-2019/rodada/1/jogos/");
    
     //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
    //    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $server_output = curl_exec ($ch);

    //    $redirectURL = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL );

        curl_close ($ch); 

     //   print_r($server_output);
     //   die(".");

        return $server_output;
    }

}