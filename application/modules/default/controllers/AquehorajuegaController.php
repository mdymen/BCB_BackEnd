<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
class AquehorajuegaController  extends Zend_Controller_Action {


    
    function testAction() {

        try {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $ch = curl_init("https://api.globoesporte.globo.com/tabela/d1a37fa4-e948-43a6-ba53-ab24ab3a45b1/fase/fase-unica-seriea-2019/rodada/2/jogos/");
    
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
      curl_setopt($ch, CURLOPT_TIMEOUT, 60);
              
              $server_output = curl_exec ($ch);
      
              curl_close ($ch);       

            $this->_helper->json(json_decode($server_output));

        }
        catch (Exception $e) {
            print_r($e->getMessage());
            die(".");
        }
    }

        public function html_to_obj($html) {
        $dom = new DOMDocument();
        $dom->loadHTML($html);

        return $this->element_to_obj($dom->documentElement);
    }

    public function element_to_obj($element) {
        $obj = array( "tag" => $element->tagName );
        foreach ($element->attributes as $attribute) {
            $obj[$attribute->name] = $attribute->value;
        }
        foreach ($element->childNodes as $subElement) {
            if ($subElement->nodeType == XML_TEXT_NODE) {
                $obj["html"] = $subElement->wholeText;
            }
            else {
                $obj["children"][] = $this->element_to_obj($subElement);
            }
        }
        return $obj;
    }

    function recursive_array_search($needle, $haystack, $currentKey = '') {
        foreach($haystack as $key=>$value) {
            if (is_array($value)) {
                $nextKey = $this->recursive_array_search($needle,$value, $currentKey . '[' . $key . ']');
                if ($nextKey) {
                    return $nextKey;
                }
            }
            else if($value==$needle) {
                return $currentKey;
            }
        }
        return false;
    }

    function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    function Even($array) 
{ 
    if (strcmp($array,"html") == 0) {
        return true;
    }
    return false;
} 
  
}