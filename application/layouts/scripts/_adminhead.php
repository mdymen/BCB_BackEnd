<html xmlns="http://www.w3.org/1999/xhtml"><!-- Head --><head>
    <meta charset="utf-8">
    <title>Bobby aulas - Cursos online</title>
     <link rel="shortcut icon" href="<?php echo $this->baseUrl().'/../public/img/icone.png'; ?>" type="image/x-icon">
<?php
    require_once 'breadcrumb.php';
?>

    <meta name="description" content="data tables">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <link rel="shortcut icon" href="<?php echo $this->baseUrl().'/../public/img/icone.png'; ?>" type="image/x-icon">
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/dataTables.bootstrap.css'))?>    
        <a href="_head.php"></a>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/jquery-2.0.3.min.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/skins.min.js')) ?>
        
        <!--Page Related Scripts--> 
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/toastr/toastr.js')) ?>

    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/bootstrap.min.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/slimscroll/jquery.slimscroll.min.js')) ?>
        
    <!--Beyond Scripts-->
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/beyond.min.js')) ?>     
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/dashBoard.js')) ?>  
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/dashboard/alteraPendente.js')) ?>  
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/bootbox/bootbox.js')) ?>  
    
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/dragdropfiles/javascript.js')) ?>  
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/dragdropfiles/jquery.js')) ?>      
    
        
    <!--Basic Styles-->
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/bootstrap.min.css'))?>
    
    <!--<link href="assets/css/bootstrap.min.css" rel="stylesheet">-->
    <link id="bootstrap-rtl-link" href="" rel="stylesheet">
        
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/font-awesome.css'))?>           
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/weather-icons.min.css'))?> 
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/toggleButton.css'))?> 
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/dragdropfiles/style.css'))?>         
    
    <!--<link href="assets/css/font-awesome.min.css" rel="stylesheet"> -->

    <!--Fonts-->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300" rel="stylesheet" type="text/css">
    
   <!--Page Related styles-->
   <!--<link href="assets/css/dataTables.bootstrap.css" rel="stylesheet">-->
   
    <!--Beyond styles-->
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/beyond.min.css'))?>   
    <!--<link id="beyond-link" href="assets/css/beyond.min.css" rel="stylesheet">-->
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/demo.min.css'))?> 
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/typicons.min.css'))?> 
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/animate.min.css'))?> 
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/skins/blue.min.css'))?>
    
    

    <!--<link href="assets/css/demo.min.css" rel="stylesheet">
    <link href="assets/css/typicons.min.css" rel="stylesheet">
    <link href="assets/css/animate.min.css" rel="stylesheet">-->
    <link id="skin-link" href="" rel="stylesheet" type="text/css">

    <!--Skin Script: Place this script in head to load scripts for skins and rtl support-->
    <script async="" src="http://www.google-analytics.com/analytics.js"></script>
    
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/skins.min.js')) ?>
        
    <?php echo $this->headLink()?>     
    <?php echo $this->headScript()?>  
    
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
                            <li>
                                <a title="Usuario" href="<?php echo $this->baseUrl().'/'; ?>">
                                    <i class="icon fa fa-user-md"></i>
                                </a>
                            </li>                            
                            <li>
                                <a title="Cerrar Sessao" href="<?php echo $this->baseUrl().'/auth/logout'; ?>">
                                    <i class="icon fa fa-power-off"></i>
                                </a>
                            </li>                            
                        </ul>
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
                    <!--Databoxes-->
                    <li>
                        <a href="<?php echo $this->baseUrl() . "/admin/"; ?>">
                            <i class="menu-icon glyphicon glyphicon-home"></i>
                            <span class="menu-text">Home </span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="menu-dropdown">
                            <i class="menu-icon fa fa-lock"></i>
                            <span class="menu-text"> Painel administrativo </span>

                            <i class="menu-expand"></i>
                        </a>

                        <ul class="submenu">
                            <li>
                                <a href="<?php echo $this->baseUrl() . "/admin/dashboard"; ?>">
                                    <i class="menu-icon fa fa-user"></i>
                                    <span class="menu-text">Usuarios</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $this->baseUrl() . "/admin/dashboard/creditopendente"; ?>">
                                    <i class="menu-icon fa fa-usd"></i>
                                    <span class="menu-text">Creditos pendentes</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="menu-dropdown">
                            <i class="menu-icon fa fa-lock"></i>
                            <span class="menu-text"> Painel cursos </span>

                            <i class="menu-expand"></i>
                        </a>
                        
                        <ul class="submenu">
                            <li>
                                <a href="<?php echo $this->baseUrl() . "/admin/curso"; ?>">
                                    <i class="menu-icon glyphicon glyphicon-book"></i>
                                    <span class="menu-text">Cursos </span>
                                </a>
                            </li>
                            <!--Widgets-->
                            <li>
                                <a href="<?php echo $this->baseUrl(). "/admin/curso/adicionar"; ?>">
                                    <i class="menu-icon glyphicon glyphicon-edit"></i>
                                    <span class="menu-text">Adicionar curso </span>
                                </a>
                            </li>
                            <!--UI Elements-->
                            <li>
                                <a href="<?php echo $this->baseUrl(). "/admin/slide/adicionar"; ?>">
                                    <i class="menu-icon glyphicon glyphicon-unchecked"></i>
                                    <span class="menu-text">Adicionar slide </span>
                                </a>    
                            </li>
                             <li>
                                <a href="<?php echo $this->baseUrl(). "/admin/perguntas/index"; ?>">
                                    <i class="menu-icon fa fa-question"></i>
                                    <span class="menu-text">Perguntas </span>
                                </a>    
                            </li> 
                            
                        </ul>
                    </li>
                    
                </ul>
                <!-- /Sidebar Menu -->
            </div>
            <!-- /Page Sidebar -->
            <!-- Chat Bar -->

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
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/jquery-2.0.3.min.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/bootstrap.min.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/sparkline/jquery.sparkline.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/sparkline/sparkline-init.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/easypiechart/jquery.easypiechart.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/flot/jquery.flot.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/flot/jquery.flot.resize.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/flot/jquery.flot.pie.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/flot/jquery.flot.tooltip.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/charts/flot/jquery.flot.orderBars.js')) ?>
    <?php echo $this->headScript()?> 

  
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