<html xmlns="http://www.w3.org/1999/xhtml"><!-- Head --><head>
    <meta charset="utf-8">
    <title>Bobby aulas - Cursos online</title>
    
    
        
<?php
    require_once 'breadcrumb.php';
    $storage = new Zend_Auth_Storage_Session();
    $data = (get_object_vars($storage->read()));
    
    $data['ID_USUARIO_USU'] = $data['ID_ID_USU'];

    $perguntas = new Models_Perguntas();
    $respostas = $perguntas->perguntasUsuarioNaoLidas($data);
    
    $count = count($respostas);
?>
    <meta name="description" content="data tables">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">-->
    <link rel="shortcut icon" href="<?php echo $this->baseUrl().'/../public/img/icone.png'; ?>" type="image/x-icon">
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/dataTables.bootstrap.css'))?>    
        <a href="_head.php"></a>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/jquery-2.0.3.min.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/angular.min.js')) ?>
           
        
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/toastr/toastr.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/dashBoard.js')) ?>  

    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/bootstrap.min.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/slimscroll/jquery.slimscroll.min.js')) ?>
           
   <?php $this->headScript()->appendFile($this->baseUrl('jquery/jquery-1.8.3.js')) ?>
   <?php $this->headScript()->appendFile($this->baseUrl('jquery/jquery-ui-1.9.2.custom.js')) ?>
   <?php $this->headScript()->appendFile($this->baseUrl('jquery/jquery-ui-1.9.2.custom.min.js')) ?>   
   
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/flot/flot-init.js')) ?>   
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/flot/jquery.flot.js')) ?> 
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/flot/jquery.flot.min.js')) ?>   

 
<?php //$this->headScript()->appendFile($this->baseUrl('assets/js/datetime/moment.js'))      
// $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/daterangepicker.js'))      

// $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/bootstrap-timepicker.js'))  
// $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/bootstrap-timepicker.min.js'))  

// $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/bootstrap-datepicker.js'))   
// $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/moment.min.js')) ?>  

        <?php 
 $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/moment.js'));      
 $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/daterangepicker.js'));      
 $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/bootstrap-timepicker.js'));  
 $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/bootstrap-timepicker.min.js'));  
 $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/bootstrap-datepicker.js'));   
 $this->headScript()->appendFile($this->baseUrl('assets/js/datetime/moment.min.js')); 
?>  
        
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/beyond.min.js')) ?>     
        

        
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/bootstrap.min.css'))?>
    
   <link id="bootstrap-rtl-link" href="" rel="stylesheet">
        
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/font-awesome.css'))?>           
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/weather-icons.min.css'))?>           
    
  
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300" rel="stylesheet" type="text/css">
    
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/beyond.min.css'))?>   
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/demo.min.css'))?> 
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/typicons.min.css'))?> 
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/animate.min.css'))?> 
    <link id="skin-link" href="" rel="stylesheet" type="text/css">

    <script async="" src="http://www.google-analytics.com/analytics.js"></script>
    
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/skins.min.js')) ?>
        
    <?php echo $this->headLink()?>     
    <?php echo $this->headScript()?>  
    <?php include_once APPLICATION_PATH.'/decorators/Textarea.php'; ?>
</head>
<!-- /Head -->
<!-- Body -->
<body>
    <!-- Loading Container -->
    <div class="loading-container loading-inactive">
        <div class="loader"></div>
    </div>
    <!--  /Loading Container -->
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-inner">
            <div class="navbar-container">
                <!-- Navbar Barnd -->
                <div class="navbar-header pull-left">
                    <a href="#" class="navbar-brand">
                        <small>
                            <!--<img src="assets/img/logo.png" alt=""> -->
                        </small>
                    </a>
                </div>
                <!-- /Navbar Barnd -->
                <!-- Sidebar Collapse -->
                <div class="sidebar-collapse" id="sidebar-collapse">
                    <i class="collapse-icon fa fa-bars"></i>
                </div>
                <!-- /Sidebar Collapse -->
                <!-- Account Area and Settings --->
                <div class="navbar-header pull-right">
                    <div class="navbar-account">
                        
                        <ul class="account-area">
                            <li><?php 
                                if ($data['ST_CONFIRMADO_USU'] != '') { 
                                    include_once APPLICATION_PATH.'/Elementoshtml/alerts.php'; 
                                    $root = 'http'. '://' . $_SERVER['HTTP_HOST'] . '/aulas/public/auth/reenviarmail?conf='.$data['ST_CONFIRMADO_USU'].'&email='.$data['ST_EMAIL_USU'];
                                    echo Elementoshtml_Alerts::warning('Vocë precisa confirmar seu e-mail. <a href="'.$root.'">Re-enviar email</a>');                          
                                } 
                                ?></li>
                            <?php
                                if ($data['FL_ADMIN_USU']) {  ?>
                                    <li>
                                        <a title="Admin" href="<?php echo $this->baseUrl().'/admin'; ?>">
                                            <i class="icon fa fa-user-md"></i>
                                        </a>
                                    </li>
                            <?php    }
                            ?>
                            <li>
                                <a class="dropdown-toggle" data-toggle="dropdown" title="Sugestoes" block="0" href="" id="lnkSugestao">
                                            <i class="icon fa fa-comment-o"></i>
                                        </a>
                                  <ul class="pull-right dropdown-menu dropdown-tasks dropdown-arrow " id="ulSugestao">
                                    <li class="dropdown-header bordered-darkorange">
                                        <i class="fa fa-star"></i>
                                        Suas sugoestoes sao importantes...
                                    </li>
                                        <center>
                                        <?php
                                            include_once APPLICATION_PATH.'/forms/sugestoes.php';
                                            $form = new Forms_Sugestoes();
                                            echo $form;

                                        ?>
                                        </center>
                                </ul>
                            </li>
                            
                            

                            <li>
                                <a class="dropdown-toggle" data-toggle="dropdown" title="Novas respostas" href="#">
                                    <i class="icon fa fa-tasks"></i>
                                    <span class="badge"><?php echo $count; ?></span>
                                </a>
                                <!--Tasks Dropdown-->
                                <ul class="pull-right dropdown-menu dropdown-tasks dropdown-arrow ">
                                    <li class="dropdown-header bordered-darkorange">
                                        <i class="fa fa-tasks"></i>
                                        <?php 
                                        $resp = "Resposta";
                                        $nova = "nova";
                                        if ($count != 1) {
                                            $resp .= "s";
                                            $nova .= "s";
                                        }
                                        echo $count.' '.$resp.' '.$nova;
                                                
                                                ?>
                                    </li>
                                    <?php
                                        foreach ($respostas as $resposta) {
                                            echo '<li> <div class="clearfix">
                                                <span class="pull-left"><a href="'.$this->baseUrl().'/perguntas/index">'.$resposta['ST_NOME_CR'].'</a></span>
                                                <span class="pull-right">'.$resposta['DT_UTIMOMOV_PER'].'</span>
                                            </div> </li>';
                                        }
                                    
                                    ?>
<!--
                                    <li>
                                        <a href="#">
                                            <div class="clearfix">
                                                <span class="pull-left">Account Creation</span>
                                                <span class="pull-right">65%</span>
                                            </div>

                                            <div class="progress progress-xs">
                                                <div style="width:65%" class="progress-bar"></div>
                                            </div>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#">
                                            <div class="clearfix">
                                                <span class="pull-left">Profile Data</span>
                                                <span class="pull-right">35%</span>
                                            </div>

                                            <div class="progress progress-xs">
                                                <div style="width:35%" class="progress-bar progress-bar-success"></div>
                                            </div>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#">
                                            <div class="clearfix">
                                                <span class="pull-left">Updating Resume</span>
                                                <span class="pull-right">75%</span>
                                            </div>

                                            <div class="progress progress-xs">
                                                <div style="width:75%" class="progress-bar progress-bar-darkorange"></div>
                                            </div>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#">
                                            <div class="clearfix">
                                                <span class="pull-left">Adding Contacts</span>
                                                <span class="pull-right">10%</span>
                                            </div>

                                            <div class="progress progress-xs">
                                                <div style="width:10%" class="progress-bar progress-bar-warning"></div>
                                            </div>
                                        </a>
                                    </li>-->

<!--                                    <li class="dropdown-footer">
                                        <a href="#">
                                            See All Tasks
                                        </a>
                                        <button class="btn btn-xs btn-default shiny darkorange icon-only pull-right"><i class="fa fa-check"></i></button>
                                    </li>-->
                                </ul>
                                <!--/Tasks Dropdown-->
                            </li>
<!--                            <li>
                                <a class="wave in" id="chat-link" title="Chat" href="#">
                                    <i class="icon glyphicon glyphicon-comment"></i>
                                </a>
                            </li>-->
                            <li>
                                
                                <?php
                                    $storage = new Zend_Auth_Storage_Session();
                                    $data = get_object_vars($storage->read());
                                ?>
                               
                                <a href="<?php echo $this->baseUrl().'/usuario/index' ?>" class="login-area">
                                    <div class="avatar" title="Minha conta">
                                        <img src="<?php 
                                        
                                            $file = APPLICATION_PATH."/../public/img/perfil/".$data['ST_USUARIO_USU'].'.jpg';
                                          if (is_file($file)) {
                   
                                                echo $this->baseUrl().'/img/perfil/'.$data['ST_USUARIO_USU'].'.jpg';
                                            } else {
                                                echo $this->baseUrl().'/img/perfil.jpg'; 
                                                
                                            }
                                        
                                        ?>">
                                    </div>
                                    <section>
                                        <h2><span class="profile"><span><?php echo $data['ST_USUARIO_USU']; ?></span></span></h2>
                                    </section>
                                </a>
                                <!--Login Area Dropdown-->
                                <ul class="pull-right dropdown-menu dropdown-arrow dropdown-login-area">
<!--                                    <li class="username"><a><?php echo $data['ST_USUARIO_USU']; ?></a></li>
                                    <li class="email"><a><?php echo $data['ST_EMAIL_USU']; ?></a></li>-->
                                    <!--Avatar Area-->
<!--                                    <li>
                                        <div class="avatar-area">
                                            <img src="<?php echo $this->baseUrl().'/../public/img/perfil.jpg'; ?>" class="avatar">
                                            <span class="caption">Change Photo</span>
                                        </div>
                                    </li>-->
                                    <!--Avatar Area-->
<!--                                    <li class="edit">
                                        <a href="profile.html" class="pull-left">Profile</a>
                                        <a href="#" class="pull-right">Setting</a>
                                    </li>-->
                                    <!--Theme Selector Area-->
<!--                                    <li class="theme-area">
                                        <ul class="colorpicker" id="skin-changer">
                                            <li><a class="colorpick-btn" href="#" style="background-color:#5DB2FF;" rel="assets/css/skins/blue.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#2dc3e8;" rel="assets/css/skins/azure.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#03B3B2;" rel="assets/css/skins/teal.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#53a93f;" rel="assets/css/skins/green.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#FF8F32;" rel="assets/css/skins/orange.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#cc324b;" rel="assets/css/skins/pink.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#AC193D;" rel="assets/css/skins/darkred.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#8C0095;" rel="assets/css/skins/purple.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#0072C6;" rel="assets/css/skins/darkblue.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#585858;" rel="assets/css/skins/gray.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#474544;" rel="assets/css/skins/black.min.css"></a></li>
                                            <li><a class="colorpick-btn" href="#" style="background-color:#001940;" rel="assets/css/skins/deepblue.min.css"></a></li>
                                        </ul>
                                    </li>-->
                                    <!--/Theme Selector Area-->
<!--                                    <li class="dropdown-footer">
                                        <a href="<?php echo $this->baseUrl().'/auth/logout'; ?>">
                                            Cerrar sessao
                                        </a>
                                    </li>-->
                                </ul>
                                <!--/Login Area Dropdown-->
                            </li>
                            <!-- /Account Area -->
                            <!--Note: notice that setting div must start right after account area list.
                            no space must be between these elements-->
                            <!-- Settings -->
                              <li>
                                        <a title="Cerrar sessao" href="<?php echo $this->baseUrl().'/auth/logout'; ?>">
                                            <i class="icon fa fa-power-off"></i>
                                        </a>
                                    </li>
                        </ul><div class="setting">
                            <a id="btn-setting" title="Setting" href="#">
                                <i class="icon glyphicon glyphicon-cog"></i>
                            </a>
                        </div><div class="setting-container">
                            <label>
                                <input type="checkbox" id="checkbox_fixednavbar">
                                <span class="text">Fixed Navbar</span>
                            </label>
                            <label>
                                <input type="checkbox" id="checkbox_fixedsidebar">
                                <span class="text">Fixed SideBar</span>
                            </label>
                            <label>
                                <input type="checkbox" id="checkbox_fixedbreadcrumbs">
                                <span class="text">Fixed BreadCrumbs</span>
                            </label>
                            <label>
                                <input type="checkbox" id="checkbox_fixedheader">
                                <span class="text">Fixed Header</span>
                            </label>
                        </div>
                        <!-- Settings -->
                    </div>
                </div>
                <!-- /Account Area and Settings -->
            </div>
        </div>
    </div>
    <!-- /Navbar -->
    <!-- Main Container -->
    <div class="main-container container-fluid">
        <!-- Page Container -->
        <div class="page-container">
            <!-- Page Sidebar -->
            <div class="page-sidebar" id="sidebar">
                <!-- Page Sidebar Header-->
                <div class="sidebar-header-wrapper">
                    <input type="text" class="searchinput">
                    <i class="searchicon fa fa-search"></i>
                    <div class="searchhelper">Search Reports, Charts, Emails or Notifications</div>
                </div>
                <!-- /Page Sidebar Header -->
                <!-- Sidebar Menu -->
                <ul class="nav sidebar-menu">
                    <li>
                        <a href="<?php echo $this->baseUrl().'/index/index'; ?>">
                            <i class="menu-icon glyphicon glyphicon-home"></i>
                            <span class="menu-text"> Home </span>
                        </a>
                    </li> 
                    <li>
                        <a href="<?php echo $this->baseUrl().'/usuario/index'; ?>">
                            <i class="menu-icon glyphicon glyphicon-user"></i>
                            <span class="menu-text"> Minha conta </span>
                        </a>
                    </li>                    
                    <li>
                        <a href="<?php echo $this->baseUrl().'/curso/cursos'; ?>">
                            <i class="menu-icon glyphicon glyphicon-book"></i>
                            <span class="menu-text"> Cursos </span>
                        </a>
                    </li>                    
                    <!--Dashboard-->

                    <li>
                        <a href="<?php echo $this->url(array('controller'=>'credito','action'=>'index'))?>">
                            <i class="menu-icon glyphicon fa fa-dollar"></i>
                            <span class="menu-text"> Credito </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $this->baseUrl().'/perguntas/index'; ?>">
                            <i class="menu-icon glyphicon glyphicon-question-sign"></i>
                            <span class="menu-text"> Perguntas </span>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class=""></i>
                            <span class="menu-text"> &nbsp; </span>
                        </a>
                    </li>    
                    <li>
                        <a href="<?php echo $this->baseUrl().'/curso/novo'; ?>">
                            <i class="menu-icon glyphicon glyphicon-file"></i>
                            <span class="menu-text"> Novo curso </span>
                        </a>
                    </li>    
                    <li>
                        <a href="<?php echo $this->baseUrl().'/curso/meuscursos'; ?>">
                            <i class="menu-icon glyphicon glyphicon-book"></i>
                            <span class="menu-text"> Meus cursos </span>
                        </a>
                    </li>    
                </ul>
                <!-- /Sidebar Menu -->
            </div>
            <!-- /Page Sidebar -->
            <!-- Chat Bar -->
            <div id="chatbar" class="page-chatbar">
                <div class="chatbar-contacts">
                    <div class="contacts-search">
                        <input type="text" class="searchinput" placeholder="Search Contacts">
                        <i class="searchicon fa fa-search"></i>
                        <div class="searchhelper">Search Your Contacts and Chat History</div>
                    </div>
<!--                    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 581px;"><ul class="contacts-list" style="overflow: hidden; width: auto; height: 581px;">
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/divyia.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">Divyia Philips</div>
                                <div class="contact-status">
                                    <div class="online"></div>
                                    <div class="status">online</div>
                                </div>
                                <div class="last-chat-time">
                                    last week
                                </div>
                            </div>
                        </li>
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/Nicolai-Larson.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">Adam Johnson</div>
                                <div class="contact-status">
                                    <div class="offline"></div>
                                    <div class="status">left 4 mins ago</div>
                                </div>
                                <div class="last-chat-time">
                                    today
                                </div>
                            </div>
                        </li>
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/John-Smith.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">John Smith</div>
                                <div class="contact-status">
                                    <div class="online"></div>
                                    <div class="status">online</div>
                                </div>
                                <div class="last-chat-time">
                                    1:57 am
                                </div>
                            </div>
                        </li>
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/Osvaldus-Valutis.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">Osvaldus Valutis</div>
                                <div class="contact-status">
                                    <div class="online"></div>
                                    <div class="status">online</div>
                                </div>
                                <div class="last-chat-time">
                                    today
                                </div>
                            </div>
                        </li>
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/Javi-Jimenez.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">Javi Jimenez</div>
                                <div class="contact-status">
                                    <div class="online"></div>
                                    <div class="status">online</div>
                                </div>
                                <div class="last-chat-time">
                                    today
                                </div>
                            </div>
                        </li>
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/Stephanie-Walter.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">Stephanie Walter</div>
                                <div class="contact-status">
                                    <div class="online"></div>
                                    <div class="status">online</div>
                                </div>
                                <div class="last-chat-time">
                                    yesterday
                                </div>
                            </div>
                        </li>
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/Sergey-Azovskiy.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">Sergey Azovskiy</div>
                                <div class="contact-status">
                                    <div class="offline"></div>
                                    <div class="status">offline since oct 24</div>
                                </div>
                                <div class="last-chat-time">
                                    22 oct
                                </div>
                            </div>
                        </li>
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/Lee-Munroe.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">Lee Munroe</div>
                                <div class="contact-status">
                                    <div class="online"></div>
                                    <div class="status">online</div>
                                </div>
                                <div class="last-chat-time">
                                    today
                                </div>
                            </div>
                        </li>
                        <li class="contact">
                            <div class="contact-avatar">
                                <img src="assets/img/avatars/divyia.jpg">
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">Divyia Philips</div>
                                <div class="contact-status">
                                    <div class="online"></div>
                                    <div class="status">online</div>
                                </div>
                                <div class="last-chat-time">
                                    last week
                                </div>
                            </div>
                        </li>
                    </ul><div class="slimScrollBar" style="width: 4px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; left: 1px; background: rgb(45, 195, 232);"></div><div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; left: 1px; background: rgb(51, 51, 51);"></div></div>-->
                </div>
<!--                <div class="chatbar-messages" style="display: none;">
                    <div class="messages-contact">
                        <div class="contact-avatar">
                            <img src="assets/img/avatars/divyia.jpg">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Divyia Philips</div>
                            <div class="contact-status">
                                <div class="online"></div>
                                <div class="status">online</div>
                            </div>
                            <div class="last-chat-time">
                                a moment ago
                            </div>
                            <div class="back">
                                <i class="fa fa-arrow-circle-left"></i>
                            </div>
                        </div>
                    </div>
                    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 417px;"><ul class="messages-list" style="overflow: hidden; width: auto; height: 417px;">
                        <li class="message">
                            <div class="message-info">
                                <div class="bullet"></div>
                                <div class="contact-name">Me</div>
                                <div class="message-time">10:14 AM, Today</div>
                            </div>
                            <div class="message-body">
                                Hi, Hope all is good. Are we meeting today?
                            </div>
                        </li>
                        <li class="message reply">
                            <div class="message-info">
                                <div class="bullet"></div>
                                <div class="contact-name">Divyia</div>
                                <div class="message-time">10:15 AM, Today</div>
                            </div>
                            <div class="message-body">
                                Hi, Hope all is good. Are we meeting today?
                            </div>
                        </li>
                        <li class="message">
                            <div class="message-info">
                                <div class="bullet"></div>
                                <div class="contact-name">Me</div>
                                <div class="message-time">10:14 AM, Today</div>
                            </div>
                            <div class="message-body">
                                Hi, Hope all is good. Are we meeting today?
                            </div>
                        </li>
                        <li class="message reply">
                            <div class="message-info">
                                <div class="bullet"></div>
                                <div class="contact-name">Divyia</div>
                                <div class="message-time">10:15 AM, Today</div>
                            </div>
                            <div class="message-body">
                                Hi, Hope all is good. Are we meeting today?
                            </div>
                        </li>
                        <li class="message">
                            <div class="message-info">
                                <div class="bullet"></div>
                                <div class="contact-name">Me</div>
                                <div class="message-time">10:14 AM, Today</div>
                            </div>
                            <div class="message-body">
                                Hi, Hope all is good. Are we meeting today?
                            </div>
                        </li>
                        <li class="message reply">
                            <div class="message-info">
                                <div class="bullet"></div>
                                <div class="contact-name">Divyia</div>
                                <div class="message-time">10:15 AM, Today</div>
                            </div>
                            <div class="message-body">
                                Hi, Hope all is good. Are we meeting today?
                            </div>
                        </li>
                    </ul><div class="slimScrollBar" style="width: 4px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; left: 1px; background: rgb(45, 195, 232);"></div><div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; left: 1px; background: rgb(51, 51, 51);"></div></div>
                    <div class="send-message">
                        <span class="input-icon icon-right">
                            <textarea rows="4" class="form-control" placeholder="Type your message"></textarea>
                            <i class="fa fa-camera themeprimary"></i>
                        </span>
                    </div>
                </div>-->
            </div>
            <!-- /Chat Bar -->
            <!-- Page Content -->
            <div class="page-content">
                <!-- Page Breadcrumb -->
               <!-- <div class="page-breadcrumbs">
                    <ul class="breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="#">Home</a>
                        </li>
                        <li>
                            <a href="#">UI Elements</a>
                        </li>
                        <li class="active">Alerts</li>
                    </ul>
                </div>-->
                <!-- /Page Breadcrumb -->
                <!-- Page Header -->
                <div class="page-header position-relative"><?php
                    breadcrumb();
                            ?>
                    <!--Header Buttons-->
                    <div class="header-buttons">
                        <a class="sidebar-toggler" href="#">
                            <i class="fa fa-arrows-h"></i>
                        </a>
                        <a class="refresh" id="refresh-toggler" href="">
                            <i class="glyphicon glyphicon-refresh"></i>
                        </a>
                        <a class="fullscreen" id="fullscreen-toggler" href="#">
                            <i class="glyphicon glyphicon-fullscreen"></i>
                        </a>
                    </div>
                    <!--Header Buttons End-->
                </div>
                <!-- /Page Header -->
                <!-- Page Body -->
                <div class="page-body">
                   
                    <?php  echo $this->layout()->content; ?>
                </div>
                <!-- /Page Body -->
            </div>
            <!-- /Page Content -->
        </div>
        <!-- /Page Container -->
        <!-- Main Container -->

    </div>

    <!--Basic Scripts-->

    <!--Google Analytics::Demo Only-->
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date(); a = s.createElement(o),
            m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'http://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-52103994-1', 'auto');
        ga('send', 'pageview');

    </script>

<!--  /Body -->

</body></html>

<script type="text/javascript">
    
    $(function() {
       $('#lnkSugestao').bind('click', function() {
           var block = $(this).attr('block');
           if (block === '0') {
               $(this).attr('block', 1);
               $('#ulSugestao').attr('style','display: block');
           } else {
               $(this).attr('block', 0);
               $('#ulSugestao').attr('style','display: none');
           }
           
       });
    });
    
</script>