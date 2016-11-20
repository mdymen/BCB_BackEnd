<?php

        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
//        print_r($data->us_username);
//        die(".");
?>  


<!DOCTYPE html>
<html lang="en">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>Bolão Craque de Bola</title>
	<meta name="description" content="Bolão Craque de Bola.">
	<meta name="author" content="Martin Dymenstein">
	<meta name="keyword" content="Bolão, Futebol, Brasileirão, Campeonato">
	<!-- end: Meta -->
	
	<!-- start: Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- end: Mobile Specific -->
	
	<!-- start: CSS -->
        <link href="<?php echo $this->baseUrl("assets/css/bootstrap.min.css"); ?>" rel="stylesheet">
	<link href="<?php echo $this->baseUrl("assets/css/style.min.css"); ?>"  rel="stylesheet">
	<link href="<?php echo $this->baseUrl("assets/css/retina.min.css"); ?>" rel="stylesheet">
	<link href="<?php echo $this->baseUrl("assets/css/print.css"); ?>" rel="stylesheet" type="text/css" media="print"/>
	<!-- end: CSS -->
	
   <?php echo $this->headScript()->appendFile($this->baseUrl('jquery/jquery-1.8.3.js')); ?>
   <?php echo $this->headScript()->appendFile($this->baseUrl('jquery/jquery-ui-1.9.2.custom.js')); ?>
   <?php echo $this->headScript()->appendFile($this->baseUrl('jquery/jquery-ui-1.9.2.custom.min.js')); ?> 
   <?php echo $this->headScript()->appendFile($this->baseUrl('assets/js/own.js')) ?>        
        
	<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		
	  	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<script src="assets/js/respond.min.js"></script>
		
	<![endif]-->
	
	<!-- start: Favicon and Touch Icons -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="assets/ico/apple-touch-icon-57-precomposed.png">
	<link rel="shortcut icon" href="<?php echo $this->baseUrl().'assets/ico/icone.gif'; ?>">
        <link rel="icon" href="assets/ico/icone.gif">
	<!-- end: Favicon and Touch Icons -->	
		
</head>

<body>
		<!-- start: Header -->
	<header class="navbar">
		<div class="container">
			<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".sidebar-nav.nav-collapse">
			      <span class="icon-bar"></span>
			      <span class="icon-bar"></span>
			      <span class="icon-bar"></span>
			</button>
			<a id="main-menu-toggle" class="hidden-xs open"><i class="fa fa-bars"></i></a>		
			<a class="navbar-brand col-md-6 col-sm-6 col-xs-6" href="<?php echo $this->baseUrl(); ?>"><span>Craque de Bola</span></a>
			
			<!-- start: Header Menu -->
			<div class="nav-no-collapse header-nav">
				<ul class="nav navbar-nav pull-right">
                                    
                                    
                                    <li id="li_timecoracao" class="dropdown hidden-xs">
                                    <a id="link_timecoracao" style="padding-right: 75px !important" class="btn dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">							
							<div class="user">
<!--								<span class="hello">Time</span>-->
								<span class="name" id_time="<?php echo $data->us_team; ?>" id="head_timecoracao"><?php echo $data->us_teamname; ?></span>
							</div>
                                                    
						</a>
<!--                                        <ul style="padding-left: -50px;" class="dropdown-menu notifications">
                                            <li class="dropdown-menu-title">
                                                <span>Time do coracao</span>
                                            </li>	
                                            <li>                                          
                                                    <span id="escolher_timecoracao" class="message">New user registration</span>                                            
                                            </li>
                                        </ul>-->
                                    </li>                                    
                                    <li>
                                    <a style="padding-right: 20px !important" class="btn account dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">							
							<div class="user">
								<span class="hello">R$</span>
								<span class="name"><?php echo $data->us_cash; ?></span>
							</div>
                                                    
						</a>
                                    </li>
					<li class="dropdown" >
						<a style="padding-right: 20px !important" class="btn account dropdown-toggle" data-toggle="dropdown" href="2nd-level.html#">							
							<div class="user">
								<span class="hello">Bem-vindo! </span>
								<span class="name"><?php echo $data->us_username; ?></span>
							</div>
                                                    
						</a>
						<ul class="dropdown-menu">
							<li><a href="2nd-level.html#"><i class="fa fa-user"></i> Profile</a></li>
							<li><a href="2nd-level.html#"><i class="fa fa-cog"></i> Settings</a></li>
							<li><a href="2nd-level.html#"><i class="fa fa-envelope"></i> Messages</a></li>
							<li><a href="/penca/public/index/logout"><i class="fa fa-off"></i> Logout</a></li>
						</ul>
					</li>
                                        <li>
                                            <a class="btn" href="<?php echo $this->baseUrl("index/logout"); ?>">
							<i class="fa fa-power-off"></i>
						</a>
                                            
                                        </li>
				</ul>
			</div>
			<!-- end: Header Menu -->
			
		</div>	
	</header>
	<!-- end: Header -->
	
		<div class="container">
		<div class="row">
				
			<!-- start: Main Menu -->
			<div id="sidebar-left" class="col-lg-2 col-sm-1 ">
								
				<div class="sidebar-nav nav-collapse collapse navbar-collapse">
					<ul class="nav main-menu">
                                            <li><a href="<?php echo $this->baseUrl("/"); ?>"><i class="fa fa-home"></i><span class="hidden-sm text"> Home</span></a></li>
                                                <!--<li><a href="index.html"><i class="fa fa-dollar"></i><span class="hidden-sm text"> Caixa</span></a></li>-->	
						<!--<li><a href="<?php echo $this->baseUrl("/penca/pencas"); ?>"><i class="fa fa-dribbble"></i><span class="hidden-sm text"> Baloes</span></a></li>-->	
                                                <!--<li><a href="<?php echo $this->baseUrl("/penca/meusbaloes"); ?>" ><i class="fa fa-globe"></i><span class="hidden-sm text"> Meus Baloes </span></a></li>-->	
                                                <li><a href="<?php echo $this->baseUrl("/penca/meuspalpites"); ?>" ><i class="fa fa-globe"></i><span class="hidden-sm text"> Meus Palpites </span></a></li>	
                                                <li><a href="<?php echo $this->baseUrl("/penca/bolao"); ?>"><i class="fa fa-globe"></i><span class="hidden-sm text"> Palpites </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/usuario"); ?>"><i class="fa fa-user"></i><span class="hidden-sm text">Meu Perfil</span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/campeonatos"); ?>"><i class="fa fa-star-o"></i><span class="hidden-sm text">Campeonatos</span></a></li> 
                                                 <li><a href="<?php echo $this->baseUrl("/penca/ranking"); ?>"><i class="fa fa-star-o"></i><span class="hidden-sm text">Ranking</span></a></li>
                                                 <li><a href="<?php echo $this->baseUrl("/index/logout"); ?>"><i class="fa fa-star-o"></i><span class="hidden-sm text">Logout</span></a></li>
                                                <!--<li><a href="<?php echo $this->baseUrl("/register/penca"); ?>"><i class="fa fa-legal"></i><span class="hidden-sm text"> Criar Balão </span></a></li>-->                                                
                                                
					</ul>
				</div>
									<a href="javascript:void(0)" id="main-menu-min" class="full visible-md visible-lg"><i class="fa fa-angle-double-left"></i></a>
							</div>
			<!-- end: Main Menu -->
						
			<!-- start: Content -->
			<div id="content" class="col-lg-10 col-sm-11 ">
			<ol class="breadcrumb">
			  	<?php 
                                    include("breadcrumb.php");                                
                                    echo breadcrumb();
                                ?>
			</ol>
           
                            <?php echo $this->render('alerts.phtml'); ?>
                            <?php echo $this->layout()->content; ?>
     
					
			</div>
			<!-- end: Content -->
				
				</div><!--/row-->		
		
	</div><!--/container-->
	
	
	<div class="modal fade" id="myModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Modal title</h4>
				</div>
				<div class="modal-body">
					<p>Here settings can be configured...</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div class="clearfix"></div>
	
	<footer>
		
		<div class="row">
			
			<div class="col-sm-5">
			
			</div><!--/.col-->
			
			<div class="col-sm-7 text-right">
				
			</div><!--/.col-->	
			
		</div><!--/.row-->	

	</footer>
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->
             <?php echo $this->headScript()->appendFile($this->baseUrl('assets/js/jquery-2.1.0.min.js')) ?>
	<!--<![endif]-->

	<!--[if IE]>
	
		<script src="assets/js/jquery-1.11.0.min.js"></script>
	
	<![endif]-->

	<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.1.0.min.js'>"+"<"+"/script>");
		</script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script type="text/javascript">
	 	window.jQuery || document.write("<script src='assets/js/jquery-1.11.0.min.js'>"+"<"+"/script>");
		</script>
		
	<![endif]-->
        <?php echo $this->headScript()->appendFile($this->baseUrl('assets/js/jquery-migrate-1.2.1.min.js')) ?>
        <?php echo $this->headScript()->appendFile($this->baseUrl('assets/js/bootstrap.min.js')) ?>







	<!-- page scripts -->
        <?php echo $this->headScript()->appendFile($this->baseUrl('assets/js/jquery-ui-1.10.3.custom.min.js')) ?>
        
	<!-- theme scripts -->
        <?php echo $this->headScript()->appendFile($this->baseUrl('assets/js/custom.min.js')) ?>
        <?php echo $this->headScript()->appendFile($this->baseUrl('assets/js/core.min.js')) ?>
	
	<!-- inline scripts related to this page -->
	
	<!-- end: JavaScript-->
	
</body>
</html>


<script type="text/javascript">
    $(function() {
       $("#link_timecoracao").bind("click", function() {
           if ($("#li_timecoracao").hasClass("open")) {
             $("#li_timecoracao").removeClass("open");
           } else {
             $("#li_timecoracao").addClass("open");
             $.post("/penca/public/index/teams", function(response) {
                
                var s_option = '<select id="" name="" class="form-control">';
                var id_time = $("#head_timecoracao").attr("id_time");
                for (var i = 0; i < response.length; i = i + 1) {
                    var selected = "";
                    if (id_time == response[i].tm_id) {
                        selected = "selected";
                    }
                    s_option = s_option + '<option '+selected+' value="'+response[i].tm_id+'">'+response[i].tm_name+'</option>';
                }     
                s_option = s_option + "</option>";
                $("#escolher_timecoracao").html(s_option);
                 console.log(s_option);
             })
           }
       }) 
    });
</script>    