<?php

$product=array("vmlang"=>"it-IT",
"published"=>"1",
"product_sku"=>"ITALIANO - immaginiEbay/maglie/trainingSilver1112/ MG-152 V12998 1332353734",
"product_name"=>"ITALIANO - Giuseppe inserimento prodotto",
"slug"=>"ITALIANO - felpa-allenamento-training-sweatshirt-adidas-liverpool-stagione",
"product_url"=>"",
"virtuemart_manufacturer_id"=>"0",
"layout"=>"0",
"product_special"=>"0",
"product_price"=>"26.66667",
"product_currency"=>"47",
    'product_thumb_image' => 'http://www.modacalcio.com/immaginiEbay/maglie/manchesterCity/PoloAzzurra1112/front-PoloCityAzzurra-moda.jpg',
    'product_full_image' => 'http://www.modacalcio.com/immaginiEbay/maglie/manchesterCity/PoloAzzurra1112/front-PoloCityAzzurra-gall.jpg',
 "basePrice"=>"26.67",
"product_price_incl_tax"=>"26.67",
"product_tax_id"=>"0",
"product_discount_id"=>"0",
"product_override_price"=>"0.00000",
"intnotes"=>"",
"product_s_desc"=>"ITALIANO - Saldi e sconti su Felpa Allenamento Training Sweatshirt della casa produttrice Adidas stagione 2011 12.<br>",
"product_desc"=>"ITALIANO - <p>Saldi e sconti su Felpa Allenamento Training Sweatshirt della casa produttrice Adidas stagione 2011 12.<br />versione maniche lunghe Colore principale : Grigio con particolari Silver e rossi.<br />Il prodotto non ha tasche, Collo Alto con mezza zip. stagione 2011 12<br />100% poliestere, mezzo tempo Made in China.<br />Il prodotto ha una vestibilitÃ  normale Loghi Adidas cucito, stemma Liverpool applicato.<br />Tessuto mezzo tempo non imbottito ottimo per allenamento. Pronta disponibiltÃ , Spedizione in 24/48 h<br /><br /><br />Per la scelta delle taglie controllate le misure sottostanti<br /> // <img src=\"http://www.modacalcio.com/immaginiEbay/taglie/adidasFelpaCappuccio.jpg\" border=\"0\" alt=\"Tabella taglie e misure Felpa Allenamento Training Sweatshirt Adidas Liverpool stagione 2011 12.\" /><br /> Codice Fornitore: V12998<br /> Codice univoco Modacalcio: immaginiEbay/maglie/trainingSilver1112/<br /> Codice ERP Interno: MG-152</p>",
"customtitle"=>"",
"metadesc"=>"",
"metakey"=>"",
"metarobot"=>"",
"metaauthor"=>"",
"product_in_stock"=>"20",
"product_ordered"=>"0",
"low_stock_notification"=>"0",
"min_order_level"=>"0",
"max_order_level"=>"0",
"product_available_date_text"=>"03/21/12",
"product_available_date"=>"2012-03-21 18:15:34",
"product_availability"=>"24h.gif",
"image"=>"24h.gif",
"product_length"=>"0.0000",
"product_lwh_uom"=>"IN",
"product_width"=>"0.0000",
"product_height"=>"0.0000",
"product_weight"=>"0.4500",
"product_weight_uom"=>"KG",
"product_unit"=>"pezz",
"product_packaging"=>"0",
"product_box"=>"0",
"searchMedia"=>"37959544_l.jpg_product_product :: 24",
"virtuemart_media_id"  => array ("23" => 0,"24"=>  1 ),
"virtuemart_media_ordering"  => array ( "23" => 23, "24"=>24),
    "media_published"=>"1",
"file_title"=>"",
"file_description"=>"",
"file_meta"=>"",
//"file_url"=>"images/stories/virtuemart/product/",
//"file_url_thumb"=>"",
"media_roles"=>"file_is_displayable",
"media_action"=>"0",
"file_is_product_image"=>"1",
"active_media_id"=>"0",
"option"=>"com_virtuemart",
"save_customfields"=>"1",
"search"=>"",
    "attribute" => "Taglia,S,M,L",
"pictures"=> "http://www.modacalcio.com/immaginiEbay/maglie/barcelona/homeRS1112//det_Barca1112_02.jpg**http://www.modacalcio.com/immaginiEbay/maglie/barcelona/homeRS1112//det-bar1112_08.jpg**http://www.modacalcio.com/immaginiEbay/maglie/barcelona/homeRS1112//det-bar1112_06-Messi.jpg",
"task"=>"apply",
"boxchecked"=>"0",
"controller"=>"product",
"view"=>"product",
"bd6bd516abe76b4f174ae023a45e22c0"=>"1",
"product_parent_id"=>0 );

$prod = array_chunk($product, 2);

include 'simpleHTMLscarpe/simple_html_dom.php';
$fileContents = file_get_html('http://localhost.dev/ModaNuovo/webapp/index.php');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost.dev/ModaNuovo/webapp/index.php');
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
// POST fields
//$tot= 4;
//foreach($product as $key=>$value){
//    $newArr[]=$value;
//    $postfields['addProduct-datas'.$key] =  json_encode($value );
//    
//}

/**
 *array(1) { 'function' => string(10) "addProduct" } 
DEBUG line 672 phpQuery/phpquery.php -- provo la stampa di data string(53) "GET: http://localhost.dev/ModaNuovo/webapp/index.php\n" string(621) "Options:
array (\n  \'url\' => \'http://localhost.dev/ModaNuovo/webapp/index.php\',\n  \'global\' => true,\n  \'type\' => \'GET\',\n  \'timeout\' => NULL,\n  \'contentType\' => \'application/x-www-form-urlencoded\',\n  \'processData\' => true,\n  \'data\' => \n  array (\n    \'function\' => \'addProduct\',\n  ),\n  \'username\' => NULL,\n  \'password\' => NULL,\n  \'accepts\' => \n  array (\n    \'xml\' => \'application/xml, text/xml\',\n    \'html\' => \'text/html\',\n    \'script\' => \'text/javascri"...
string(16) "Status: 200 / OK"
string(375) "GET /ModaNuovo/webapp/index.php HTTP/1.1\r\nHost: localhost.dev\r\nConnection: close\r\nAccept-encoding: gzip, deflate\r\nCookie: e1317b23c50f9eb9a0e9acf35314f501=u6kj80motmaieqp1app3280sg2;\r\nUser-Agent: Mozilla/5.0 (X11; U; Linux x86; en-US; rv:1.9.0.5) Gecko/2008122010 Firefox/3.0.5\r\nAccept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\nAccept-Language: en-us,en;q=0.5\r\nAccept:  * / *\r\n\r\n"
array(8) {
  'Date' =>
  string(29) "Wed, 18 Apr 2012 16:43:02 GMT"
  'Server' =>
  string(22) "Apache/2.2.20 (Ubuntu)"
  'X-powered-by' =>
  string(30) "PHP/5.4.0-1build1~ppa1~oneiric"
  'Vary' =>
  string(15) "Accept-Encoding"
  'Content-encoding' =>
  string(4) "gzip"
  'Content-length' =>
  string(4) "1218"
  'Connection' =>
  string(5) "close"
  'Content-type' =>
  string(9) "text/html"
} 
 */

//$postfields['username'] = urlencode($uname);
$postfields['passwd'] = urlencode(md5('superciccio'));
//$postfields['lang'] = '';
//$postfields['return'] = $c;
$postfields['function'] = 'addProduct';
$postfields['task'] = 'login';
$postfields[$b] = '1';
$postfields['addProduct-datas'.$key] = json_encode($product );
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
$fileContents = curl_exec($ch);
echo 'La response del server <br/>';
echo $fileContents;
?>
