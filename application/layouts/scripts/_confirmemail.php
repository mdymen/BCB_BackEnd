<?php

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
	<link rel="shortcut icon" href="<?php echo $this->baseUrl().'assets/ico/icone.png'; ?>">
        <link rel="icon" href="assets/ico/icone.png">
	<!-- end: Favicon and Touch Icons -->	
		
</head>

<body>
	
   <div class="container">
		<div class="row">
				
			<!-- start: Main Menu -->
			
			<!-- end: Main Menu -->
						
			<!-- start: Content -->
			<div id="content" class="col-lg-12 col-sm-12 col-xs-12 ">
	<div class=" col-xs-12 col-sm-12 col-lg-6 col-lg-offset-4 callout" style="padding-top:20px">
                                <div class="box" style="background:white !important">
                                    <form action="<?php echo $this->baseUrl(); ?>/index/reenviaremail" method="post">
                                        <div class="header" style="color:white;background-color:#222; padding: 20px">

                                            <b>Confirme seu email!</b>                                            
                                            
                                        </div> 
                                        <br>
                                        <center> <button class="btn btn-primary">Re-enviar e-mal</button> </center>
                                    </form>
                                    </div>
                                    
				</div>		
           
                            



			</div>
			<!-- end: Content -->
				
				</div><!--/row-->		
		
	</div>
	
</body>
