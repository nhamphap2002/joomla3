<?php

$var = $_REQUEST['sum1'] ;
$var2 = $_REQUEST['sum2'] ;
echo '<h1>'.$var+$var2.'</h1>';
foreach ($_REQUEST as $key=>$value){
    print $key .'=>'.$value.'<br/>';
}
//die();


