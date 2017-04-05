<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bolão Craque de Bola">
    <meta name="author" content="Martin Dymenstein">
    <link rel="shortcut icon" href="assets/ico/icone.gif">
        <link rel="icon" href="assets/ico/icone.gif">

    <title> Bolão Craque de Bola </title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/icomoon.css">
    <link href="assets/css/animate-custom.css" rel="stylesheet">
    
<!--    <link href="public/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="publicassets/css/style.min.css"  rel="stylesheet">-->

<script type="text/css">
    
    <?php
        $error = $_GET['error'];
        $msg = "";
        if (isset($error)) {
            
            if (strcmp($error, "t") == 0) {
                $msg = "Deve aceitar os termos";
            }
            
            elseif (strcmp($error, "u") == 0) {
                $msg = "Nome de usuario existente";
            }
            elseif (strcmp($error, "s") == 0) {
                $msg = "Verifique a senha e a confirmação";
            }
            
        }
    
    ?>

/****** LOGIN MODAL ******/
.loginmodal-container {
  padding: 30px;
  max-width: 350px;
  width: 100% !important;
  background-color: #F7F7F7;
  margin: 0 auto;
  border-radius: 2px;
  box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
  overflow: hidden;
  font-family: roboto;
}

.loginmodal-container h1 {
  text-align: center;
  font-size: 1.8em;
  font-family: roboto;
}

.loginmodal-container input[type=submit] {
  width: 100%;
  display: block;
  margin-bottom: 10px;
  position: relative;
}

.loginmodal-container input[type=text], input[type=password] {
  height: 44px;
  font-size: 16px;
  width: 100%;
  margin-bottom: 10px;
  -webkit-appearance: none;
  background: #fff;
  border: 1px solid #d9d9d9;
  border-top: 1px solid #c0c0c0;
  /* border-radius: 2px; */
  padding: 0 8px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.loginmodal-container input[type=text]:hover, input[type=password]:hover {
  border: 1px solid #b9b9b9;
  border-top: 1px solid #a0a0a0;
  -moz-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
  -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

.loginmodal {
  text-align: center;
  font-size: 14px;
  font-family: 'Arial', sans-serif;
  font-weight: 700;
  height: 36px;
  padding: 0 8px;
/* border-radius: 3px; */
/* -webkit-user-select: none;
  user-select: none; */
}

.loginmodal-submit {
  /* border: 1px solid #3079ed; */
  border: 0px;
  color: #fff;
  text-shadow: 0 1px rgba(0,0,0,0.1); 
  background-color: #4d90fe;
  padding: 17px 0px;
  font-family: roboto;
  font-size: 14px;
  /* background-image: -webkit-gradient(linear, 0 0, 0 100%,   from(#4d90fe), to(#4787ed)); */
}

.loginmodal-submit:hover {
  /* border: 1px solid #2f5bb7; */
  border: 0px;
  text-shadow: 0 1px rgba(0,0,0,0.3);
  background-color: #357ae8;
  /* background-image: -webkit-gradient(linear, 0 0, 0 100%,   from(#4d90fe), to(#357ae8)); */
}

.loginmodal-container a {
  text-decoration: none;
  color: #666;
  font-weight: 400;
  text-align: center;
  display: inline-block;
  opacity: 0.6;
  transition: opacity ease 0.5s;
} 

.login-help{
  font-size: 12px;
}

</script>

    
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,700' rel='stylesheet' type='text/css'>
    
    
    <script src="assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/modernizr.custom.js"></script>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
      <script src="assets/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body data-spy="scroll" data-offset="0" data-target="#navbar-main">
  
  
  	<div id="navbar-main">
      <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="" style="font-size:30px; color:#3498db;">
                  <img src="http://www.bolaocraquedebola.com.br/assets/img/iconelogo.png" width="28px" height="28px" style="background: none !important"/>
              </span>
          </button>
                    <a class="navbar-brand hidden-xs hidden-sm" href="#home"><span style="font-size:18px; color:#3498db;">
                            <img src="http://www.bolaocraquedebola.com.br/assets/img/iconelogo.png" width="32px" height="32px" style="background: none !important"/>
                </span></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="#home" class="smoothScroll">Home</a></li>
			<li> <a href="public" class="smoothScroll"> Login</a></li>
                        <li> <a href="comofunciona.html" class="smoothScroll"> Como funciona</a></li>
                        <li> <a href="contato.html" class="smoothScroll"> Contato</a></li>
			<!--<li> <a href="#services" class="smoothScroll"> Services</a></li>-->
			<!--<li> <a href="#team" class="smoothScroll"> Team</a></li>-->
			<!--<li> <a href="#portfolio" class="smoothScroll"> Portfolio</a></li>-->
			<!--<li> <a href="#blog" class="smoothScroll"> Blog</a></li>-->
			<!--<li> <a href="#contact" class="smoothScroll"> Contact</a></li>-->
        </div><!--/.nav-collapse -->
      </div>
    </div>
    </div>

  
  
		<!-- ==== HEADERWRAP ==== -->
	    <div id="headerwrap" id="home" name="home">
			<header class="clearfix">
                      
                            <!DOCTYPE html>
<html>
<body>

                            <div class=" col-xs-12 col-sm-12 col-lg-4 col-lg-offset-1 callout" style="padding-top:20px">
                                <div class="box" style="background:white !important">
                                    <div class="header" style="color:white;background-color:#222; padding: 20px">
                                        <b>Cadastre-se Agora!</b>
					</div>
					<form id="novo_usuario" class="form-horizontal" role="form" action="public/index/register" style="padding: 40px 0 30px 30px">
                                            <div><span id="erro" style="display:yes; color:red"><?php echo $msg; ?></span></div>  
                                                <div class="form-group">
						    <div class="col-xs-10 col-sm-10 col-lg-10">
						      <input type="text" class="form-control" name="username" placeholder="Usuario">
						    </div>
						  </div>
						  <div class="form-group">
						    <div class="col-xs-10 col-sm-10 col-lg-10">
						      <input type="password" class="form-control" name="password" placeholder="Senha">
						    </div>
						  </div>
                                                    <div class="form-group">
						    <div class="col-xs-10 col-sm-10 col-lg-10">
						      <input type="password" class="form-control" name="confpassword" placeholder="Confirmação">
						    </div>
						  </div>
                                                    <div class="form-group">
						    <div class="col-xs-10 col-sm-10 col-lg-10">
                                                        <input type="checkbox" id="termos" name="termos"> <a href="http://www.bolaocraquedebola.com.br/termos.pdf" TARGET="_BLANK">Aceito os termos de uso.</a>
						    </div>
						  </div>                                            
						  <div class="form-group">
						    <div class="col-xs-10 col-sm-10 col-lg-10">
						      <button type="submit" id="btnCadastro" class="btn btn-success">Cadastra-se</button>
						    </div>
						  </div>
                                                    <div class="form-group">
						    <div class="col-xs-10 col-sm-10 col-lg-10" style="text-align:left">
                                                        <span><a href="public/?login">Já possui uma conta? Faça Login</a></span>
						    </div>
						  </div>                                              
					   </form>
                                    </div>
				</div>
</body>
</html>

                            
                        </div>
	  		</header>	    
	    </div><!-- /headerwrap -->

		<!-- ==== GREYWRAP ==== -->
		<div id="greywrap">
			<div class="row">
				<div class="col-lg-4 callout">
					<span class="icon icon-user-plus"></span>
					<h2>Bolão com os Amigos!</h2>
					<p>Crie seu próprio bolão personalizado para curtir com a rapaziada, compartilhar com os amigos e se divertir. </p>
				</div><!-- col-lg-4 -->
					
				<div class="col-lg-4 callout">
					<span class="icon icon-star"></span>
					<h2>Ranking e Histórico</h2>
					<p>Tenha acesso ao Ranking do Bolão em que esta participando; veja todos os históricos de palpites e de quem está palpitando. </p>
				</div><!-- col-lg-4 -->	
				
				<div class="col-lg-4 callout">
					<span class="icon icon-trophy"></span>
					<h2>Time do coração</h2>
					<p>Faça palpites no seu time do coração, com uma pagina exclusiva para seu time. </p>
				</div><!-- col-lg-4 -->	
			</div><!-- row -->
		</div><!-- greywrap -->
		

		<!-- ==== SECTION DIVIDER4 ==== -->
		<section class="section-divider textdivider divider4">
			<div class="container">
				<h1>Participe dos Bolões do Futebol Brasileiro com seus palpites e ganhe prêmios! Faça o cadastro e compartilhe com seus amigos!.</h1>
			</div><!-- container -->
		</section><!-- section -->
		
		
		<div class="container" id="contact" name="contact">
			<div class="row">
			<br>
				<h1 class="centered">OBRIGADO PELA VISITA</h1>
				<hr>
				<br>
				<br>
				<div class="col-lg-4">
					<h3>Informação de Contato</h3>
					<p><br/>
						<span class="icon icon-envelop"></span> <a href="#"> info@bolaocraquedebola.com.br</a> <br/>
						<span class="icon icon-twitter"></span> <a href="https://twitter.com/BolaoCraqueBola"> @BolaoCraqueBola </a> <br/>
						<span class="icon icon-facebook"></span> <a href="https://www.facebook.com/bolaocraquedebolaoficial/"> Bolão Craque de Bola </a> <br/>
					</p>
				</div><!-- col -->
				
				<div class="col-lg-4">
					<a class="twitter-timeline" data-width="280" data-height="250" href="https://twitter.com/BolaoCraqueBola">Bolão Craque de Bola</a> <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
				</div><!-- col -->
				
				<div class="col-lg-4">
					<h3>Ajude-nos a melhorar!</h3>
					<p>Estamos trabalhando para melhor comodidade ao usuario acessar a plataforma, ajude-nos informando seu feedback para que possamos estar sempre melhorando. Toda ajuda é sempre Bem vinda!</p>
				</div><!-- col -->

			</div><!-- row -->
		
		</div><!-- container -->

		<div id="footerwrap">
			<div class="container">
				<h4>Created by Bolão Craque de Bola</h4>
			</div>
		</div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
		

	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/retina.js"></script>
	<script type="text/javascript" src="assets/js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="assets/js/smoothscroll.js"></script>
	<script type="text/javascript" src="assets/js/jquery-func.js"></script>
  </body>
</html>


<script type="text/javascript">
//
//    $(function() {
//       $("#btnCadastro").bind("click", function() {
//            var usuario = $("#usuario").val();
//            var password = $("#password").val();
//            if (usuario === "" || password === "") {
//                $("#erro").html("Nome de usuario ou senha incorretos");
//                $("#erro").show();
//            } else {
//                if ($("#termos").is(":checked")) {
//                    $.post("public/index/podecadastrarusuario", {usuario:usuario}, function(response) {
////                        console.log(response);
//                        if (response) {
//                            $("#novo_usuario").submit();
//                        } else {
//                            $("#erro").html("O nome de usuario jà esta sendo utilizado");
//                            $("#erro").show();            
//                        }
//                    });
//                    
//                } else {
//                    $("#erro").html("Deve aceitar os termos");
//                    $("#erro").show();    
//                }
//            }
//       });
//    });
    
    
</script>


<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-90398320-1', 'auto');
  ga('send', 'pageview');

</script>