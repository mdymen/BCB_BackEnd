<?php

   if(!Zend_Registry::isRegistered('translate'))
   {
//       $translate = new Zend_Translate(
//            array(
//                'adapter' => 'array',
//                'content' => 'idiomas/'.$data['us_idioma'].'.php',
//                'locale'  => $data['us_idioma']
//           )
//        );

       
          $translate = new Zend_Translate(
            array(
                'adapter' => 'array',
                'content' => 'idiomas/'.$data['us_idioma'].'.php',
                'locale'  => $data['us_idioma']
           )
        );
          
        Zend_Registry::set('translate', $translate);

    }

        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
//        print_r($data->us_username);
//        die(".");
        
        $t = Zend_Registry::get('translate');

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
	<link rel="shortcut icon" href="<?php echo $this->baseUrl().'/assets/ico/icone.png'; ?>">
        <link rel="icon" href="/assets/ico/icone.png">
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
			<div id="search" class="col-sm-4 col-xs-8 col-lg-3" style="background: none !important; margin-top:9px !important">
                            <a href="<?php echo $this->baseUrl()."/index/regulamento"; ?>" style="color:white ">Regulamento</a>
			</div>
			<!-- start: Header Menu -->
			<div class="nav-no-collapse header-nav">
				<ul class="nav navbar-nav pull-right">
                                    <li><a href="<?php echo $this->baseUrl("index/idioma?i=pt"); ?>">Portugues</a></li>
                                    <li><a href="<?php echo $this->baseUrl("index/idioma?i=es"); ?>">Español</a></li>
                                    <li>
                                    <a style="padding-right: 20px !important" class="btn account dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">							
							<div class="user">
								<span>Base:</span>
								<?php echo $data->us_base; ?>
							</div>
                                                    
						</a>
                                    </li>                                      
                                    <li>
                                    <a style="padding-right: 20px !important" class="btn account dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">							
							<div class="user">
								<span class="hello">R$</span>
								<span class="name" id="cash_usuario"><?php echo $data->us_cash; ?></span>
							</div>
                                                    
						</a>
                                    </li>                                    
					<li class="dropdown" >
						<a style="padding-right: 20px !important" class="btn account dropdown-toggle" data-toggle="dropdown" href="2nd-level.html#">							
							<div class="user">
								<span class="hello"><?php echo $t->_('bem.vindo'); ?></span>
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
                                                <li><a href="<?php echo $this->baseUrl("/penca/meuspalpites"); ?>" ><i class="ttt fa fa-globe"></i><span class="hidden-sm text"> <?php echo $t->_('meus.palpites'); ?> </span></a></li>	
                                                <li><a href="<?php echo $this->baseUrl("/penca/bolao"); ?>"><i class="fa fa-globe"></i><span class="hidden-sm text"><?php echo $t->_('palpites'); ?>  </span></a></li>
 <li>
							<a class="dropmenu"><i class="fa fa-users"></i><span class="hidden-sm text">Bolões</span> <span class="chevron closed"></span></a>
							<ul style="display: none;">
							<li><a class="submenu" href="<?php echo $this->baseUrl("/penca/meusbaloes"); ?>"><i class="fa fa-sign-in"></i><span class="hidden-sm text">Meus Bolões</span></a></li>	
                                                            <li><a class="submenu" href="<?php echo $this->baseUrl("/penca/boloesdisponiveis"); ?>"><i class="fa  fa-search"></i><span class="hidden-sm text">Bolões Disponiveis</span></a></li>						 		
                                                                <li><a class="submenu" href="<?php echo $this->baseUrl("/penca/criar"); ?>"><i class="fa fa-money"></i><span class="hidden-sm text">Criar Bolão</span></a></li>
							</ul>
						
						</li>                                                
                                                <li><a href="<?php echo $this->baseUrl("/usuario"); ?>"><i class="fa fa-user"></i><span class="test hidden-sm text"><?php echo $t->_('meu.perfil'); ?> </span></a></li>
<!--                                                <li><a href="<?php echo $this->baseUrl("/campeonatos"); ?>"><i class="fa fa-star-o"></i><span class="hidden-sm text">Campeonatos</span></a></li> 
                                                -->
                                                <li>
							<a class="dropmenu" href="<?php echo $this->baseUrl("/campeonatos"); ?>"><i class="fa fa-star-o"></i><span class="hidden-sm text"> <?php echo $t->_('campeonatos'); ?> </span> <span class="chevron closed"></span></a>
							<ul style="display: none;">
								<li><a class="submenu" href="<?php echo $this->baseUrl("/campeonatos"); ?>"><i class="fa fa-chevron-right"></i><span class="hidden-sm text"><?php echo $t->_('em.andamento'); ?> </span></a></li>
								<li><a class="submenu" href="<?php echo $this->baseUrl("/penca/encerrados"); ?>"><i class="fa fa-chevron-right"></i><span class="hidden-sm text"><?php echo $t->_('encerrados'); ?> </span></a></li>
							</ul>
						
						</li>
                                                
                                                <li><a href="<?php echo $this->baseUrl("/penca/ranking"); ?>"><i class="fa fa-star-o"></i><span class="hidden-sm text">Ranking</span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/caixa/index"); ?>"><i class="fa fa-dollar"></i><span class="hidden-sm text"><?php echo $t->_('caixa'); ?> </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/caixa/transacoes"); ?>"><i class="fa fa-dollar"></i><span class="hidden-sm text"><?php echo $t->_('transacoes'); ?></span></a></li>
                                                <!--<li><a href="<?php echo $this->baseUrl("/register/penca"); ?>"><i class="fa fa-legal"></i><span class="hidden-sm text"> Criar Balão </span></a></li>-->                                                
                                                <li><a href="<?php echo $this->baseUrl("/admin/campeonato"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text">Add Campeonato</span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/campeonato/cerrar"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text">Fechar Campeonato</span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/time"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Add Time </span></a></li>                                                
                                                <li><a href="<?php echo $this->baseUrl("/admin/rodada"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Add Rodada </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/rodada/rodadaatual"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Set Rodada Atual </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/jogos/jogospordata"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text">Jogos por data</span></a></li>                                                
                                                <li><a href="<?php echo $this->baseUrl("/admin"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text">Add Jogo</span></a></li>                                                
                                                <li><a href="<?php echo $this->baseUrl("/admin/jogos"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Jogos </span></a></li>
                                                <!--<li><a href="<?php echo $this->baseUrl("/admin/team"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Admin Teams </span></a></li>-->
                                                <li><a href="<?php echo $this->baseUrl("/admin/transacoes"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Transações </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/campeonato/palpites"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Palpites </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/resultados"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Resultados </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/index/usuarios"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Usuarios </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/index/adicionargrana"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Adicionar Grana </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/index/emails"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Emails </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/penca/selecionarbase"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Selecionar base </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/admin/campeonato/backup"); ?>"><i class="fa fa-male"></i><span class="hidden-sm text"> Backup </span></a></li>
                                                <li><a href="<?php echo $this->baseUrl("/index/logout"); ?>"><i class="fa fa-power-off"></i><span class="hidden-sm text">Logout</span></a></li>
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
                            <h5>Campeonatos</h5>                                                            
                            <?php $c = new Application_Model_Championships();
                            $champs = $c->load();
                            
                            for ($i = 0; $i < count($champs); $i++) {
                                echo '<h5><a href="'.$this->baseUrl("penca/bolao?champ=".$champs[$i]['ch_id']).'">'.$champs[$i]['ch_nome'].'</a></h5>';                                                            
                            }
                            
                            ?>
                            
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