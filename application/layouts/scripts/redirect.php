<meta name="description" content="data tables">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">-->
    <link rel="shortcut icon" href="<?php echo $this->baseUrl().'/../public/img/icone.png'; ?>" type="image/x-icon">
    <?php $this->headLink()->appendStylesheet($this->baseUrl('assets/css/dataTables.bootstrap.css'))?>    
        <a href="_head.php"></a>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/jquery-2.0.3.min.js')) ?>
        
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/toastr/toastr.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/dashBoard.js')) ?>  

    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/bootstrap.min.js')) ?>
    <?php $this->headScript()->appendFile($this->baseUrl('assets/js/slimscroll/jquery.slimscroll.min.js')) ?>
           
   <?php $this->headScript()->appendFile($this->baseUrl('jquery/jquery-1.8.3.js')) ?>
   <?php $this->headScript()->appendFile($this->baseUrl('jquery/jquery-ui-1.9.2.custom.js')) ?>
   <?php $this->headScript()->appendFile($this->baseUrl('jquery/jquery-ui-1.9.2.custom.min.js')) ?>      
 
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

<?php

?>
    
    <div id="divDir" style="display:none"><?php echo $this->baseUrl('/'); ?></div>    
    
<script type="text/javascript">

    $(function() {
       window.location.href = $('#divDir').html()+'?returnUrl='+window.location; 
    });

</script>