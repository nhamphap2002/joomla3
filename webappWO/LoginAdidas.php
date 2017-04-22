<?php
$uname = '4701013129';
$upswd = '13129BAR';
$url = "https://portal.adidas-group.com/irj/portal/light";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE );
curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE );
curl_setopt($ch, CURLOPT_COOKIEJAR, realpath('./cookie.txt'));
curl_setopt($ch, CURLOPT_COOKIEFILE, realpath('./cookie.txt'));
curl_setopt($ch, CURLOPT_HEADER, TRUE );
$ret = curl_exec($ch);
if (!preg_match('/name="([a-zA-z0-9]{32})"/', $ret, $spoof)) {
    preg_match("/name='([a-zA-z0-9]{32})'/", $ret, $spoof);
}

// POST fields
$postfields = array();
$postfields['j_user'] = urlencode($uname);
$postfields['j_password'] = urlencode($upswd);
//$postfields['option'] = 'com_user';
//$postfields['task'] = 'login';
//$postfields[$spoof[1]] = '1';
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
$ret = curl_exec($ch);

// Get logged in cookie and pass it to the browser
preg_match('/^Set-Cookie: (.*?);/m', $ret, $m);
$cookie=explode('=',$m[1]);
setcookie($cookie[0], $cookie[1]);
//$postfields = array();
//$postfields['option'] = 'com_virtuemart';
//$postfields[$spoof[1]] = '1';
//curl_setopt($ch, CURLOPT_URL, $postfields);
//header('Location: https://portal.adidas-group.com/irj/portal/light?NavigationTarget=navurl://e0cdfba59e90ae1663449f15f2c8882e');
curl_setopt($ch, CURLOPT_URL,'https://portal.adidas-group.com/irj/portal/light?NavigationTarget=navurl://e0cdfba59e90ae1663449f15f2c8882e');
$ret = curl_exec($ch);
echo $ret;
//?>