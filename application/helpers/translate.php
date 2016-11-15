<?php

   if(!Zend_Registry::isRegistered('translate'))
   {
       $translate = new Zend_Translate(
            array(
                'adapter' => 'array',
                'content' => 'idiomas/pt.php',
                'locale'  => 'pt'
           )
        );

        Zend_Registry::set('translate', $translate);

    }

