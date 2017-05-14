<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TeamController
 *
 * @author Martin Dymenstein
 */
class ErrorController extends Zend_Controller_Action
{
    public function errorAction() {
        echo '<div>
            <center>
                <img src="/assets/img/page-not-found.png"  />
            </center>
        </div>';
        die(".");
    }
    
}
