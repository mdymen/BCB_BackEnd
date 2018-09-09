<?php

include APPLICATION_PATH.'/modules/default/controllers/BolaoController.php';
include APPLICATION_PATH.'/models/bd_adapter.php';
/*include APPLICATION_PATH.'/cloudinary/Cloudinary.php';
include APPLICATION_PATH.'/cloudinary/Uploader.php';
include APPLICATION_PATH.'/cloudinary/Api.php';
include APPLICATION_PATH.'/cloudinary/Error.php';*/

//include APPLICATION_PATH.'/facebook/Facebook.php';
//include APPLICATION_PATH.'/facebook/Facebook/FileUpload/FacebookFile.php';
class FacebookController extends BolaoController {

  /*  public function test4Action() {
        try {
        $img = APPLICATION_PATH."/../public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";

        $fb = new Facebook(array(
            'app_id' => '321383738230464',
          //  'app_secret' => '{app-secret}',
            'default_graph_version' => 'v3.1'
            // . . .
        ));

        $x = $fb->fileToUpload($img);

        $data['message'] = "xx";
        $data['source'] = $x;

              $return = $fb->post('321383738230464/feed', $data);

              $this->_helper->json($return);
            }
            catch (Exception $e) {
                $this->_helper->json($e->getMessage());
            }
    }*/

    public function test5Action() {

        try {
            $post_url = "https://api.cloudinary.com/v1_1/dmmwki3kn/image/upload";
            
            
            $data = array(
                "file" => "http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png",
                "api_key" => "579438751795697", 
                "api_secret" => "-FeGTRvlXoDOE-16aNWd7eV-Im0"
            );
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $post_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            curl_close($ch);
            
            $this->_helper->json($return);
   /*     $images = APPLICATION_PATH."/../public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";

        Cloudinary::config(array( 
            "cloud_name" => "dmmwki3kn", 
            "api_key" => "579438751795697", 
            "api_secret" => "-FeGTRvlXoDOE-16aNWd7eV-Im0" 
          ));

          $return = Uploader::upload($images);

          $this->_helper->json($return);*/

        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    public function testAction() {

        try {
 
        //    $data['images'] = APPLICATION_PATH."/../public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
        $data['url'] = "http://www.dymenstein.com/publicidad/j0IoCI7aRLCZenqO.php";
        $data['image'] = "http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
       
        $data['picture'] = "http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
        $data['icon'] = "http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
      //  $data['link'] = "http://www.bolaocraquedebola.com.br";
         //  $data['url'] = "http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
           $data['message'] = "Your message";
            $data['caption'] = "Caption";
            $data['description'] = "Description";
            $data['access_token'] = "EAAEp17DZCrAMBALn19QAIOsUWmpsFwu369NdYpk7woK6luDGfspAY9pUv4GPd1AjHENZAtP1S2sw0ZC4USWo5C3q5gXHCzfyW7YTP8N00UUQW1oybGa2XmoigCpoXfoUISrg0agPZB8QZBuznRJCe91LsYS5LLkPdWRxSgrsTblEQpPxtO4pZA";
            
            $post_url = 'https://graph.facebook.com/321383738230464/feed';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $post_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            curl_close($ch);
            
            $this->_helper->json($return);
        }
        catch(Exception $e) {
            $this->_helper->json($e->getMessage());
        }

    }
    public function test3Action() {
        $headers = array("Content-Type:multipart/form-data");
        $file= APPLICATION_PATH."/../public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
        try {
            $headers = array("Content-Type:multipart/form-data");
            $data[basename($file)] = '@'.realpath($file);//"http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
          //  $data['source'] = APPLICATION_PATH."/../public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
            $data['fileUpload'] = true;
            $date['message'] = "mes";
            //$data['image'] =  '@' . realpath($file);
     //   $data['picture'] = "http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
      //  $data['link'] = "http://www.bolaocraquedebola.com.br";
         //  $data['url'] = "http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";
        //   $data['message'] = "Your message";
            $data['access_token'] = "EAAEp17DZCrAMBACfgBCcKJr7X8ZAcAB8fJKUx7jXXqUHT5Wbo3CzccwXm07yUPzLVFheFM0pZCwYee6ugf8JX9lMBQtDJvALZB8ZBe3iH2VueYgM2Mn7NMSSti8Ej43mPNZBgCMFPyZC0GgEJ95TT3h8pRxAsM1BS8QlyHVsk22uQZDZD";
        //    $data['access_token'] = "EAAEp17DZCrAMBAN790xs7nXT138sdZAoATAdTxMSwPsr1LtmGxLM6wLofgmxdeXJUf6QIjB2c6rFpUKw6YtcHTwvSZAuPZCxLBkg68PKJ9dIXcAMixIf3tsekKOThPizXcJuixVDlKb7gIAjRuNRoc522ZBPW3lcA3bQEyeeiKakZAPXaAampDkpUrxRIjzZBxoPoAAZAlP6pgZDZD";
            $post_url = 'https://graph.facebook.com/321383738230464/feed';

    //        $data['source'] = new CurlFile($file, 'image/png', 'filename.png');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $post_url);
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            curl_close($ch);
            
            $this->_helper->json($return);
        }
        catch(Exception $e) {
            $this->_helper->json($e->getMessage());
        }

    }
    public function test2Action() {
        $file= APPLICATION_PATH."/../public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";

        $headers = array("Content-Type:multipart/form-data");

        $args = array(
            'message' => 'Photo from application',
            //'url' => 'http://www.dymenstein.com/public/assets/img/propaganda/j0IoCI7aRLCZenqO.png',
            'image' =>  '@' . realpath($file),
            'access_token' => 'EAAEp17DZCrAMBALn19QAIOsUWmpsFwu369NdYpk7woK6luDGfspAY9pUv4GPd1AjHENZAtP1S2sw0ZC4USWo5C3q5gXHCzfyW7YTP8N00UUQW1oybGa2XmoigCpoXfoUISrg0agPZB8QZBuznRJCe91LsYS5LLkPdWRxSgrsTblEQpPxtO4pZA'
                       // 'image' => file_get_contents($file)
           );


     /*   $args = array(
        'message' => 'Photo from application',
        );
        $args[basename($file)] = '@' . realpath($file);*/
        $ch = curl_init();
        $url = 'https://graph.facebook.com/321383738230464/photos';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $data = curl_exec($ch);
        //returns the photo id
        print_r(json_decode($data,true));
    }

    public function test1Action() {
        $url = 'http://rest7.com/html_to_image';
        $data = json_decode(file_get_contents('http://api.rest7.com/v1/html_to_image.php?url=' . $url . '&format=png'));

        if (@$data->success !== 1)
        {
            die('Failed');
        }
        $image = file_get_contents($data->file);
        file_put_contents('rendered_page.png', $image);
    }

    


    function imageAction()
    {
        //Please input your restful API key here.
        $apikey = "adca13bc35fae614";
        $api_url = "http://api.page2images.com/restfullink";

        global $apikey, $api_url;
        // URL can be those formats: http://www.google.com https://google.com google.com and www.google.com
        // But free rate plan does not support SSL link.
        $url = "http://www.google.com";
        $device = 6; // 0 - iPhone4, 1 - iPhone5, 2 - Android, 3 - WinPhone, 4 - iPad, 5 - Android Pad, 6 - Desktop
        $loop_flag = TRUE;
        $timeout = 120; // timeout after 120 seconds
        set_time_limit($timeout+10);
        $start_time = time();
        $timeout_flag = false;

        while ($loop_flag) {
            // We need call the API until we get the screenshot or error message
            try {
                $para = array(
                    "p2i_url" => $url,
                    "p2i_key" => $apikey,
                    "p2i_device" => $device
                );
                // connect page2images server
                $response = $this->connect($api_url, $para);

                if (empty($response)) {
                    $loop_flag = FALSE;
                    // something error
                    echo "something error";
                    break;
                } else {
                    $json_data = json_decode($response);
                    if (empty($json_data->status)) {
                        $loop_flag = FALSE;
                        // api error
                        break;
                    }
                }
                switch ($json_data->status) {
                    case "error":
                        // do something to handle error
                        $loop_flag = FALSE;
                        echo $json_data->errno . " " . $json_data->msg;
                        break;
                    case "finished":
                        // do something with finished. For example, show this image
                        echo "<img src='$json_data->image_url'>";
                        // Or you can download the image from our server
                        $loop_flag = FALSE;
                        break;
                    case "processing":
                    default:
                        if ((time() - $start_time) > $timeout) {
                            $loop_flag = false;
                            $timeout_flag = true; // set the timeout flag. You can handle it later.
                        } else {
                            sleep(3); // This only work on windows.
                        }
                        break;
                }
            } catch (Exception $e) {
                // Do whatever you think is right to handle the exception.
                $loop_flag = FALSE;
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
        }

        if ($timeout_flag) {
            // handle the timeout event here
            echo "Error: Timeout after $timeout seconds.";
        }
        die("");
    }

    // curl to connect server
    function connect($url, $para)
    {
        if (empty($para)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($para));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    function call_p2i_with_callback()
    {
        //Please input your restful API key here.
        $apikey = "adca13bc35fae614";
        $api_url = "http://api.page2images.com/restfullink";
        // 0 - iPhone4, 1 - iPhone5, 2 - Android, 3 - WinPhone, 4 - iPad, 5 - Android Pad, 6 - Desktop
        $device = 6;

        // you can pass us any parameters you like. We will pass it back.
        // Please make sure http://your_server_domain/api_callback can handle our call
        $callback_url = "http://www.bolaocraquedebola.com.br/public?image_id=".uniqid("");
        $para = array(
                    "p2i_url" => $api_url,
                    "p2i_key" => $apikey,
                    "p2i_device" => $device,
                    "p2i_callback" => $callback_url
                );
        $response = connect($api_url, $para);

        if (empty($response)) {
            // Do whatever you think is right to handle the exception.
        } else {
            $json_data = json_decode($response);
            if (empty($json_data->status)) {
                // api error do something
                echo "api error";
            }else
            {
                //do anything
                echo $json_data->status;
            }
        }

    }

    function pathAction() {
        print_r( APPLICATION_PATH."/../");
        die(".");
    }

    // This function demo how to handle the callback request
    function apicallbackAction()
    {

        try {
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);

            $nome = explode('/', $data['image_url']);
            $imagen = $nome[count($nome) - 1];

            $destino = APPLICATION_PATH."/../public/assets/img/propaganda/".$imagen;

            $nome_sem_ponto = explode(".", $imagen);

            copy($data['image_url'], $destino);

            $html = '<head>
                <meta property="og:url" content="https://goo.gl/8T3gxq" />
                <meta property="og:type" content="article" />
                <meta property="og:title" content="Resultados" />
                <meta property="og:description" content="Resultados da rodada x" />
                <meta property="og:image" content="http://www.dymenstein.com/public/assets/img/propaganda/'.$imagen.'" /> </head>';

            file_put_contents ( APPLICATION_PATH."/../publicidad/".$nome_sem_ponto[0].".php" , $html);

            $result['status'] = 200;

            $this->_helper->json($result);

        }
        catch (Exception $e) {

        }
    }

    public function getimageAction() {
        

        $url = "http://api.page2images.com/restfullink?p2i_url=www.bolaocraquedebola.com.br/partidos/1&p2i_key=adca13bc35fae614";

        $args = array(
            "p2i_callback" => "http://www.dymenstein.com/public/facebook/apicallback"
        );

        $ch = curl_init();
            
        curl_setopt($ch, CURLOPT_URL, $url);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        
        $server_output = curl_exec ($ch);

        curl_close ($ch); 

        print_r($server_output);
        die(".");

        return $server_output;
    } 


    public function saveimageAction() {

        $destino = APPLICATION_PATH."/../public/assets/img/propaganda/j0IoCI7aRLCZenqO.png";

        copy('http://api.page2images.com/ccimages/9e/b8/j0IoCI7aRLCZenqO.png', $destino);

        die(".");
        
    }

    public function postAction() {

        try {

            $p = new Application_Model_Posts();
            $posts = $p->get();

            for ($i = 0; $i < count($posts); $i = $i + 1) {
                $return = $this->curl($posts[$i]);
            }

        /*    $newTime = strtotime('+5 minutes');
            $hora = date('H:i', $newTime);
            print_r($hora);
            die(".");*/

            $this->_helper->json($return);
        }       
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * GET
     * @param tag
     * @param idCampeonato
     */
    public function manualpostAction() {

        try {
            $body = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($body);

            $p = new Application_Model_Posts();
            $post = $p->getByTagAndCampeonato($params['tag'],$params['idCampeonato']);
            $return = $this->curl($post);
        
            $this->_helper->json($return);
        }       
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    public function curl($post) {

        $data = array(
            "url" => $post['ps_url']."/".$post['ps_idchampionship']."/".uniqid(""),
            "idPost" => $post['ps_id']
        );

        $ch = curl_init();
            
        curl_setopt($ch, CURLOPT_URL, "http://www.bolaocraquedebola.com.br/public/send.php");
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        
        $server_output = curl_exec ($ch);
        
        curl_close ($ch); 
        
        return $server_output;
    }

    public function getpostAction() {
        try {
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);

            $p = new Application_Model_Posts();
            $posts = $p->getPost($data['idPost']);

            $this->_helper->json($posts);

        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

}

/*

$ch = curl_init();
            
curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

curl_close ($ch); 

return $server_output;*/