<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<html xmlns="http://www.w3.org/1999/xhtml">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"></meta>
    <head>
<!--<script type="text/javascript" src="jquery-1.5.2.js"></script>-->


<script type="text/javascript" src="../templates/gk_esport_old/js/jquery-1.8/js/jquery-1.7.1.min.js"></script>

<script>
    var complete = false;
    
/* 
when the submit button is clicked we will hide the message div. get the input provided by user and start processing. 
*/  
$(document).ready(function(){  

      /* 
         url : "http://localhost.dev/Ariprova/trunk/PhpCsv/phpcsv/lib/datagrid/funzioniPerChiamateAjax.php?function=eliminaInventoryVariant&allAuctions=true&idOggetto=191&idVariant=298&eliminareAncheLaVariant=true&arrayDelleAuctionDaEliminare=[%22auctionDaEliminare286%22,%22auctionDaEliminare287%22,%22auctionDaEliminare288%22,%22auctionDaEliminare289%22]", 
      */ 
      $.ajax( 
        { 
          url : "http://localhost.dev/phpQuery/getJson.php", 
          datatype:"json", 
          success: function(data){
              alert ('success');
              $('#ciao').html(data);
          },
          error: function(data){
             alert ('error');
              $('#ciao').html(data);
          },
//          success: function(data){
//              $('#errors').html('ok combà');
//              complete = true;
////            alert(data)
//            
//          },
          complete:function(data){ 
              alert ( 'completato');
              
              } 
        } ); 

    /* 
    call the updateStatus() function every 3 second to update progress bar value. 
    */ 
      t = setTimeout("updateStatus()", 1000); 
    }); 

</script> </head>
    <div id="ciao"></div>
