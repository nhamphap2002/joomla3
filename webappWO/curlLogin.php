<?php

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// example of how to use basic selector to retrieve HTML contents
        include('simpleHTMLscarpe/simple_html_dom.php');
//
//// get DOM from URL or file
$html = file_get_html('http://localhost.dev/modacalcioBackup/jupgrade/administrator/index.php');
$a = $html->find('input[type=hidden]');

// find all link
foreach ($a as $key => $e) {
    if ($key == count($a)-2)
        $c = $e->attr['value'];
    
    if ($key + 1 == count($a))
        $b = $e->attr['name'];
}
echo $b;

////// find all image
////foreach($html->find('img') as $e)
////    echo $e->src . '<br>';
////
////// find all image with full tag
////foreach($html->find('img') as $e)
////    echo $e->outertext . '<br>';
////
////// find all div tags with id=gbar
////foreach($html->find('div#gbar') as $e)
////    echo $e->innertext . '<br>';
////
////// find all span tags with class=gb1
////foreach($html->find('span.gb1') as $e)
////    echo $e->outertext . '<br>';
////
////// find all td tags with attribite align=center
////foreach($html->find('td[align=center]') as $e)
////    echo $e->innertext . '<br>';
////    
////// extract text from table
////echo $html->find('td[align="center"]', 1)->plaintext.'<br><hr>';
//
//// extract text from HTML
//echo $html->plaintext;

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $uname = "admin";
$upswd = "superciccio";
$url = "http://localhost.dev/ModaNuovo/administrator/index.php";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_COOKIEJAR, './cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, './cookie.txt');
curl_setopt($ch, CURLOPT_HEADER, FALSE);
//$ret = curl_exec($ch);
//echo $ret;
//
//
//if (!preg_match('/name="([a-zA-z0-9]{32})"/', $ret, $spoof)) {
//    preg_match("/name='([a-zA-z0-9]{32})'/", $ret, $spoof);
//}

echo ' <br/>the token is '.$b .' and return value is '.$c.'<br/>';

// POST fields
$postfields = array();
$postfields['username'] = urlencode($uname);
$postfields['passwd'] = urlencode($upswd);
$postfields['lang'] = '';
$postfields['return'] = $c;
$postfields['option'] = 'com_login';
$postfields['task'] = 'login';
$postfields[$b] = '1';
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
$fileContents = curl_exec($ch);

// $ch = curl_init();
//    curl_setopt ($ch, CURLOPT_URL, 'http://localhost.dev/ModaNuovo/administrator/index.php');
//    curl_setopt ($ch,CURLOPT_POST, 1);
//    curl_setopt ($ch, CURLOPT_MAXREDIRS, 5);
//    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
//    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt ($ch, CURLOPT_POSTFIELDS, "username=admin&passwd=superciccio&task=login&option=com_login&$b=1");
//    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
//    
//    curl_setopt ($ch, CURLOPT_COOKIEJAR, "cookie.txt");
// 
//      // Here we `think` that it worked, so continue.
//      $fileContents = curl_exec($ch);

echo $fileContents;
// This page can _ONLY_ be accessed when the _SESSION_ cookie is sent back to the server and the user is logged in.
// 
// Get logged in cookie and pass it to the browser
        preg_match('/^Set-Cookie: (.*?);/m', $fileContents, $m);
        $cookie = explode('=', $m[1]);
        setcookie($cookie[0], $cookie[1], 3600*24,'/');
        header("location:  http://localhost.dev/ModaNuovo/administrator/index.php");

//curl_setopt($ch, CURLOPT_URL, 'http://localhost.dev/ModaNuovo/administrator/index.php?option=com_virtuemart');
// We want to keep this one.
//$fileContents = curl_exec($ch);


//echo $fileContents;
?>
