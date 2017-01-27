<?php



        $storage = new Zend_Auth_Storage_Session();
//                print_r($data);
//        die(".");
        $data = "";
        if (!empty($storage)) {
            $x = $storage->read();
            if (!empty($x)) {
                $data = (get_object_vars($x));
            }
        }

//        $storage = new Zend_Auth_Storage_Session();
//        
        if (!empty($data)) {
                  
//        $data = (get_object_vars($storage->read()));
       
   if(!Zend_Registry::isRegistered('translate'))
   {
       $translate = new Zend_Translate(
            array(
                'adapter' => 'array',
                'content' => 'idiomas/'.$data['us_idioma'].'.php',
                'locale'  => $data['us_idioma']
           )
        );

       
//          $translate = new Zend_Translate(
//            array(
//                'adapter' => 'array',
//                'content' => 'idiomas/pt.php',
//                'locale'  => 'pt'
//           )
//        );
//          
        Zend_Registry::set('translate', $translate);

    }
    
        }

