<?php

//ini_set('display_errors', 1);
//echo json_decode(array('result' => 'Success', 'msg'=>'Ciccioooooooo'));
ini_set('safe_mode', 'Off');

require_once 'includes/JsonErrorHandler.php';
session_start();

$_SESSION['Errors']['Errors'] = array();

if (strrpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $namehost = "/horme/";
} else {
    $namehost = "/";
}


if (!defined('_JEXEC')){
define('_JEXEC', 1);
}


 
// Fix magic quotes.
@ini_set('magic_quotes_runtime', 0);
// 
//// Maximise error reporting.
@ini_set('zend.ze1_compatibility_mode', '0');
////error_reporting(E_ALL);
//ini_set('display_errors', 1);
 
/*
 * Ensure that required path constants are defined.
 */
                
if (!defined('JPATH_BASE')){
	define('JPATH_BASE',  $_SERVER['DOCUMENT_ROOT']  . $namehost);
}


if (!defined('JPATH_CONFIGURATION')){
define('JPATH_CONFIGURATION',		JPATH_BASE );
}


if (!defined('JPATH_ADMINISTRATOR')){
	define('JPATH_ADMINISTRATOR',  $_SERVER['DOCUMENT_ROOT']  . $namehost.'/administrator/');
}

if (!defined('JPATH_VM_ADMINISTRATOR')){
	define('JPATH_VM_ADMINISTRATOR',  $_SERVER['DOCUMENT_ROOT']  . $namehost.'/administrator/components/com_virtuemart/');
}

 
if (!defined('JPATH_LIBRARIES')){
define('JPATH_LIBRARIES',		JPATH_BASE . '/libraries');
}
if (!defined('DS')){
define('DS',		'/');
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';
//require_once JPATH_BASE . '/includes/helper.php';
//require_once JPATH_BASE . '/includes/toolbar.php';

// Instantiate the application administrator to make virtuemart work
$app = JFactory::getApplication('administrator');
//echo '<br/> DEBUG webappWO/index.php linea 56 Debug linea 49 Trying to cereate a session token --> '.$token;
// create a form token
//$user = JFactory::getUser(547);
//
//if(strrpos($_SERVER['HTTP_HOST'], 'localhost')!== false) {
$user = JFactory::getUser(580);  // de joomla
//}
$session = JFactory::getSession();
$hash = JApplication::getHash($user->get('id', 0) . $session->getToken(true));
// first set user 
//$b = JUser::getInstance();
// create a token
//$a=JSession::getInstance();
//$token = $a->getToken(true);
//               JRequest::setVar('prova', $_REQUEST['sum1'],'POST');
JRequest::setVar($hash, 1, 'POST');
JRequest::setVar('view', 'product', 'POST');
$GLOBALS['_JREQUEST'][$hash]['SET.POST'] = 1;
$data = JRequest::get('POST');

/*
  Look at the code for the form.php, you'll notice the two form fields, username, password, and you'll see them being sent here too.
 */


//         echo '<br/> webappWO/index.php linea 74 stampo ciò che mi ritorna JRequest'.var_dump($data);
//JRequest::setVar('token', $token, 'SERVER');
//echo '<br> webappWO/index.php linea 77 Trying to cereate an hash with user and session  --> '.$hash;
//if(!JRequest::checkToken()) {
// echo 'webappWO index.php linea 89 <br/>Here<br/>';
//jexit ( '<br>wow' );
//    include_once 'LoginCurl.php';
$credentials = array();
//$credentials['username'] = 'giusgius';
//$credentials['password'] = 'giusgius';
//
//if(strrpos($_SERVER['HTTP_HOST'], 'localhost')!== false) {
$credentials['username'] = 'admin';
$credentials['password'] = 'horme123!!';
//}
//preform the login action
$error = $app->login($credentials);
$user = JFactory::getUser();
// echo 'webappWO index.php linea 89 <br/>Here<br/>';
//}
//echo '<br> webappWO/index.php linea 97 token validato <br/>';
if (!JRequest::checkToken())
    jexit('<br> non sono riuscito a validare token');
//echo '<br> webappWO/index.php linea 97 token validato <br/>';


if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
 $b= VmConfig::set('vmlang','it_it');

//decommentare quanto di seguito';
//$web = MywebappWO::getInstance('MywebappWO');
//
//// Run the application
//$web->execute();
////echo '<br> webappWO/index.php linea 103 vm lang eseguita? <br/>';
//JFactory::$application = $app;

$_SESSION['vmError'] = '';
$_SESSION['vmInfo'] = '';

//echo 'webappWO index.php linea 114 --- Here<br/>';
//$_REQUEST['function'] = 'addProduct';

$file = $_REQUEST['file'];
$functionTodo = $_REQUEST['function'];

//$file = 'http://localhost.dev/ErpMulti2/trunk/PhpCsv/phpcsv/ConfigClasses/Helpers/Virtuemart2/Post.json';
//$file = 'http://easycommercemanager.com/ErpMulti2/trunk/PhpCsv/phpcsv/ConfigClasses/Helpers/Virtuemart2/Post.json';
//$file = 'http://localhost.dev/horme/webappWO/fileTemplates/editPrice.json';
//$file = 'http://localhost.dev/horme/webappWO/fileTemplates/unpublishProduct.json';
//$file = 'http://localhost.dev/horme/webappWO/fileTemplates/addProductWithChildren.json';
$file =   'http://localhost.dev/horme/webappWO/fileTemplates/addProduct.json';
//$file =   'http://localhost.dev/horme/webappWO/fileTemplates/editProductCompletely.json';
//$file =   'http://www.modacalcio.it/webappWO/fileTemplates/addProductWithChildren.json';
$functionTodo = 'addProduct';
//echo '<h1 style="color:red; font-weight:bold">webappWO/index.php linea 130 Attenzione attivato file interno </h1>';

$header_response = get_headers($file, 1);
if (strpos($header_response[0], "404") !== false) {
//    echo ' file Json non trovato con file .'.$file;
    $_SESSION['Errors']['Errors'][] = 'File Json ' . $file . ' con istruzioni non trovato! ' . $file . ' - errorlog di ModaNuovo/webappWO/index.php linea 131';
}




$jsonPost = implode('', file($file));

//echo ' provo con file senza nulla.'.$file;

$dec = json_decode($jsonPost, true);
$err = json_last_error();
if ($err != 0 || empty($dec)) {

    $_SESSION['Errors']['Errors'][] = 'nessun dato con il file ' . $file . ' per json riga 117 ModaNuovo/webappWO/index.php, Dati Json :' . $dec . ' file, Riprovo con il vecchio metodo ';
    $file = 'http://www.easycommercemanager.com/ErpMulti2/trunk/PhpCsv/phpcsv/ConfigClasses/Helpers/Virtuemart2/Post.json';
//        echo ' mi prendo il file di default.'.$file;
    $header_response = get_headers($file, 1);
    if (strpos($header_response[0], "404") !== false) {
        $_SESSION['Errors']['Errors'][] = 'File ' . $file . ' Json con istruzioni non trovato! ' . $file . ' - errorlog di ModaNuovo/webappWO/index.php linea 131';
    }




    $jsonPost = implode('', file($file));



    $dec = json_decode($jsonPost, true);
    $err = json_last_error();

    if ($err != 0) {
        $_SESSION['Errors']['Errors'][] = 'nessun dato anche per json riga ' . $file . ' 117 ModaNuovo/webappWO/index.php, Dati Json :' . $dec . ' Simo Ruvinati ';
    }
}

//            $jsonPost = objectToArray ($dec );
$productIT = $dec['productIT'];
$productIT[$hash] = 1;
$productUK = $dec['productUK'];
$productUK[$hash] = 1;
$productDE = $dec['productDE'];
$productDE[$hash] = 1;
$productFR = $dec['productFR'];
$productFR[$hash] = 1;

if (count($productIT) == 0) {
    $_SESSION['Errors']['Errors'][] = 'productIT è vuoto o non è stato correttamente codificato  ModaNuovo/webappWO/index.php riga 151';
} else {
    $_SESSION['Errors']['Warning'] = $productIT;
}
////                 JRequest::setVar('prova', $_REQUEST['sum1'],'POST');
//	JRequest::setVar($hash, 1, 'POST');
//        $GLOBALS['_JREQUEST'][$hash]['SET.POST']=1;
//        $data = JRequest::get('POST');

/**
 * recomponse array chunked from post basing on it's name
 * @param type $var 
 */
function recomponseJsonDatas($var){
    $arrTmp = array();
    $arrResult= array();
    foreach ($_REQUEST as $key=>$value){
        if(strrpos($key, $var)!==false){
            $arrTmp = json_decode($value);
            $arrResult = array_merge($arrResult,$arrTmp);
        }

        
    }
    return $arrResult;
    
}

function addProduct($productIT, $productUK, $productFR,$productDE, $hash , $_ParentId , $childrenArrayIds){
    global $app;

  if(!class_exists('ProductWebservice')) require('JsonTrial.php');
//		$migrator = new ProductWebservice();
                $a = new ProductWebservice();
                $b= VmConfig::set('vmlang','it_it');
                $_SESSION['Errors']['Errors'] = array();
                $PartsToAdd = $a->createProduct($productIT, $hash, $_ParentId ,$childrenArrayIds);
                
//                echo ' l\'id del prodotto inserito è '.$productVM['virtuemart_product_id'];
//                $mess = JApplication::getMessageQueue();
//
//                var_dump($_SESSION['vmError']);
//                echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~';
//                var_dump($_SESSION['vmInfo']);
                
//                echo $a=JFactory::getMessageQueue();
   
                //pass to english 
//                $app->initialise(array(
//	'language' => 'en-GB'
//));
         foreach ($PartsToAdd as $key => $value){
             if($key=='product_pictures_in_desc'){
                 $productUK ['full_description'] = $value.$productUK ['product_desc'];
                 $productDE ['full_description'] = $value.$productDE ['product_desc'];
                 $productFR ['full_description'] = $value.$productFR ['product_desc'];                 
                 continue;
         }
         
         if($key=='slug') {
             $productUK [$key] = $value.rand(0,50);
             $productDE [$key] = $value.rand(0,50);
             $productFR [$key] = $value.rand(0,50);
         }
             $productUK [$key] = $value;
             $productDE [$key] = $value;
             $productFR [$key] = $value;
//             $productUK [$key] = $value;
         }
         
         
         if(!$PartsToAdd['virtuemart_product_id']) {
             // il prodotto non è stato inserito correttamente
             $_SESSION['Errors']['Errors'][]='Il prodotto non è stato inserito correttamente, nessun virtuemart id ritornato ';
             return false;
         }
                
                $b= VmConfig::set('vmlang','en_gb');
//         define('VMLANG', 'en_gb' );

//                $id=$PartsToAdd['virtuemart_product_id'];
//                $productUK['virtuemart_product_id']=$id;
                
                $result = $a->translationProduct($productUK, 'en_gb');
                
                $b= VmConfig::set('vmlang','de_de');
//         define('VMLANG', 'de_de' );
                
                $result = $a->translationProduct($productDE, 'de_de');
                
                      $b= VmConfig::set('vmlang','fr_fr');
//         define('VMLANG', 'fr_fr' );
                
                $result = $a->translationProduct($productFR ,'fr_fr');
                
//                $b= VmConfig::set('vmlang','de_de');
////                $productDE['virtuemart_product_id']=$id;
//                                $result = $a->translateProduct($productDE);
//                                
//                                
//                $b= VmConfig::set('vmlang','fr_fr');
////                $productFR['virtuemart_product_id']=$id;
//                                $result = $a->translateProduct($productFR);
                                
                                return $PartsToAdd;


}

function editProduct($productIT){
    if(!class_exists('ProductWebservice')) require('JsonTrial.php');
//		$migrator = new ProductWebservice();
                $a = new ProductWebservice();
                $b= VmConfig::set('vmlang','it_it');
                $PartsToAdd = $a->editProduct($productIT);
}




// echo 'applicweb index.php linea 272 --- Here<br/>';
switch(trim($functionTodo)){
    case 'addProduct':
       try{
        $PartsToAdd=addProduct($productIT, $productUK, $productFR,$productDE, $hash);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
        if($PartsToAdd['virtuemart_product_id']){
            $msg = 'Prodotto Inserito correttamente';
        } else {
             $msg = 'Qualcosa fu stuartu ';
             foreach($_SESSION['Errors']['Errors'] as $error) {
                 $msg.= $error;
                 
             }
        }
        ($PartsToAdd['virtuemart_product_id'])? $result = 'Success' : $result = 'Failure';
        
        echo json_encode(array('result'=>$result, 'id'=>$PartsToAdd['virtuemart_product_id'] , 'msg'=>$msg, 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
        break;
        
        case 'editProductCompletely':
       try{
//            $productIT['virtuemart_product_id'] = ;
        $PartsToAdd=addProduct($productIT, $productUK, $productFR,$productDE, $hash);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
//        if($PartsToAdd['virtuemart_product_id']){
//            $msg = 'Prodotto Inserito correttamente';
//        } else {
//             $msg = 'Qualcosa fu stuartu ';
//             foreach($_SESSION['Errors']['Errors'] as $error) { //                 $msg.= $error;  //             } //        }         //        ($PartsToAdd['virtuemart_product_id'])? $result = 'Success' : $result = 'Failure';
        
        if($PartsToAdd['virtuemart_product_id']) {
            echo json_encode(array('result'=>'Success', 'id'=>$PartsToAdd['virtuemart_product_id'] , 'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
        }
        else 
            echo json_encode(array('result'=>'Failure',  'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
       
        break;
        
    case 'CleanGhostProducts':
         if(!class_exists('ProductWebservice')) require('JsonTrial.php');
//		$migrator = new ProductWebservice();
                $a = new ProductWebservice();
        $eventualId = $a ->DetectEventualDuplicate($_REQUEST['GridProductId'], $_REQUEST['language']);
                
        if(! isset($eventualId) || ! $eventualId){
//                   $_SESSION['Errors']['Errors'][]= $msg = 'Non ho trovato ghost products con id:"'. $_REQUEST['GridProductId'] .' e language '.$_REQUEST['language'];
                   echo json_encode(array('result'=>'Success',  'msg'=>$msg, 'PartsToAdd'=>array() , 'Errors'=>$_SESSION['Errors'] ));
                   return;
               } else {
                   if($a ->deleteProduct(array('virtuemart_product_id'=> $eventualId)) ) {
                       
                        if(! isset($eventualId) || ! $eventualId){
         $msg = 'Non ho trovato ghost products con id:"'. $_REQUEST['GridProductId'] .' e language '.$_REQUEST['language'];
                           echo json_encode(array('result'=>'Success',  'msg'=>$msg, 'PartsToAdd'=>array() , 'Errors'=>$_SESSION['Errors'] ));
                           return;
                       }
                   } else {
                       
                       if(!is_bool($eventualId)){
                           if(! ($a ->CleanGhostProducts($eventualId) ) ){
                           $msg = 'Non ho trovato ghost products con id:"'. $_REQUEST['GridProductId'] .' e language '.$_REQUEST['language'];
                            echo json_encode(array('result'=>'Success',  'msg'=>$msg, 'PartsToAdd'=>array() , 'Errors'=>$_SESSION['Errors'] ));
                            return;
                        }
                       } else {
                           echo json_encode(array('result'=>'Success',  'msg'=>'No Ghost Found! con id:"'. $_REQUEST['GridProductId'] .' e language '.$_REQUEST['language'], 'PartsToAdd'=>array() , 'Errors'=>$_SESSION['Errors'] ));
                   return;
                       }
                       // remove it manually from problematic tables
                        
                   }
               }
        
        break;
        
        
//        die();
    case 'editProductCompletelyWithChildren':
        try{
        // edit parent first
        $PartsToAdd=addProduct($dec["Parent"]['productIT'] , 
                            $dec["Parent"]['productUK'], 
                            $dec["Parent"]['productFR'], 
                            $dec["Parent"]['productDE'], $hash, $dec["Parent"]['virtuemart_product_id']);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
       
       $_ParentId = $PartsToAdd['virtuemart_product_id'];
               if(!$_ParentId){
                   $_SESSION['Errors']['Errors'][]='Id del prodotto a null, errore di modifica da valutare ';
                   echo json_encode(array('result'=>'Failure',  'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
                   return;
               }
               
               
       
       //Then cycle all children
               
               $productChildrenNumber = $dec["Children"]["ChidrensKeyArray"];
               $ChildrenPartsToAdd=array(); // continuava a dare l'id del prodotto precedente in caso di errori su inserimento
            foreach($productChildrenNumber as $i){
                
                try{
                    
                    if($dec["Children"][$i]['productIT']['product_in_stock'] ==0 && $dec["Children"][$i]['productIT']['published'] ==0 ){
                        $a = new ProductWebservice();
                        $ChildrenPartsToAdd['virtuemart_product_id']=$dataChUnpls['product']['virtuemart_product_id'] = $dec["Children"][$i]['productIT']['virtuemart_product_id'];
                        if($a->unpublishProduct($dataChUnpls)) $product_published = 0 ;
                           
                    } else {
//                    $PartsToAdd=addProduct($productIT, $productUK, $productFR,$productDE, $hash);
                    $ChildrenPartsToAdd=addProduct($dec["Children"][$i]['productIT'] ,
                            $dec["Children"][$i]['productUK'], 
                            $dec["Children"][$i]['productFR'], 
                            $dec["Children"][$i]['productDE'],                           
                            $hash, $_ParentId , null);
                    $product_published = 1;
                    }
                }catch (Exception $r){
                    $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
               }
               
               if(! ($ChildrenPartsToAdd['virtuemart_product_id']) ){
                   $_SESSION['Errors']['Errors'][]='Id del prodotto figlio "'.$dec["Children"][$i]['productIT']['product_name'].'" e sku:"'.$dec["Children"][$i]['productIT']['product_sku'].'" a null, errore di inserimento da valutare ';
                   echo json_encode(array('result'=>'Failure',  'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
                   return;
               }
               
                $ChildrenArray[$i] = array(
                        "slug" => $dec["Children"][$i]['productIT']['slug'],
                        "product_name" => $dec["Children"][$i]['productIT']['product_name'],
                        "mprices" => array("product_price" => $dec["Children"][$i]['productIT']['product_price']),
                        "product_sku" => $dec["Children"][$i]['productIT']['product_sku'],
                        "pordering" => 0,
                        "published" => $product_published,
                    'virtuemart_product_id'=>$ChildrenPartsToAdd['virtuemart_product_id']
                    );
                $ChildrenPartsToAdd=array(); // continuava a dare l'id del prodotto precedente in caso di errori su inserimento
            }  // all children have been set and I have all ids inside  $_SESSION['ChildrensId'][$i]
             updateLocalChildsData($dec["Parent"]["productIT"]["product_sku"] , $ChildrenArray);
            
//        if($PartsToAdd['virtuemart_product_id']) {
            echo json_encode(array('result'=>'Success', 'ParentId'=>$_ParentId , "ChildrensDetails" => $ChildrenArray, 
                'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
//        }
//        else 
//            echo json_encode(array('result'=>'Failure',  'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
       
        break;
        
        case 'addProductWithChildren':
            
            $ChildrenArray = array();
            $_ParentId ="";
            
             // insert Father
             // 
//            $productIT[$hash] =1;
            
                try{
                    $PartsToAdd=addProduct($dec["Parent"]['productIT'] , 
                            $dec["Parent"]['productUK'], 
                            $dec["Parent"]['productFR'], 
                            $dec["Parent"]['productDE'],                           
                            $hash , null, null);
                }catch (Exception $r){
                    $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
               }
               
               $_ParentId = $PartsToAdd['virtuemart_product_id'];
               if(!$_ParentId){
                   $_SESSION['Errors']['Errors'][]='Id del prodotto a null, errore di inserimento da valutare ';
                   echo json_encode(array('result'=>'Failure',  'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
                   return;
               }
                   
               
//               if(!$_ParentId)
               //I have now father set with generic child variant as attribute.  now insert childrens and gets their ID
               
               $productChildrenNumber = $dec["Children"]["ChidrensKeyArray"];
            foreach($productChildrenNumber as $i){
                
                try{
                    
//                    $PartsToAdd=addProduct($productIT, $productUK, $productFR,$productDE, $hash);
                    $ChildrenPartsToAdd=addProduct($dec["Children"][$i]['productIT'] ,
                            $dec["Children"][$i]['productUK'], 
                            $dec["Children"][$i]['productFR'], 
                            $dec["Children"][$i]['productDE'],                           
                            $hash, $_ParentId , null);
                }catch (Exception $r){
                    $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
                    echo json_encode(array('result'=>'Failure',  'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
                   return;
               }
               
               if(! ($ChildrenPartsToAdd['virtuemart_product_id']) ){
                   $_SESSION['Errors']['Errors'][]='Id del prodotto figlio "'.$dec["Children"][$i]['productIT']['product_name'].'" e sku:"'.$dec["Children"][$i]['productIT']['product_sku'].'" a null, errore di inserimento da valutare ';
                   echo json_encode(array('result'=>'Failure',  'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
                   return;
               }
               
                $ChildrenArray[$i] = array(
                        "slug" => $dec["Children"][$i]['productIT']['slug'],
                        "product_name" => $dec["Children"][$i]['productIT']['product_name'],
                        "mprices" => array("product_price" => $dec["Children"][$i]['productIT']['product_price']),
                        "product_sku" => $dec["Children"][$i]['productIT']['product_sku'],
                        "pordering" => 0,
                        "published" => 1,
                    'virtuemart_product_id'=>$ChildrenPartsToAdd['virtuemart_product_id']
                    );
                
                $ChildrenPartsToAdd=array(); // continuava a dare l'id del prodotto precedente in caso di errori su inserimento
            }  // all children have been set and I have all ids inside  $_SESSION['ChildrensId'][$i]
            
            updateLocalChildsData($dec["Parent"]["productIT"]["product_sku"] , $ChildrenArray);
            // let's edit father
            
//            try{
//                
//                $dec["Parent"]['productIT']['virtuemart_product_id']=$_ParentId;
//                    $PartsToAdd=addProduct($dec["Parent"]['productIT'] , 
//                            $dec["Parent"]['productUK'], 
//                            $dec["Parent"]['productFR'], 
//                            $dec["Children"]['productDE'],                           
//                            $hash, null , $ChildrenArray);
//                }catch (Exception $r){
//                    $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
//               }
//               
//               $_ParentId = $PartsToAdd['virtuemart_product_id'];
            
//           if(count($_SESSION['Errors']['Errors']) ) {
//               var_export($_SESSION['Errors']['Errors']);
//           }
            
            echo json_encode(array('result'=>'Success', 'ParentId'=>$_ParentId , "ChildrensDetails" => $ChildrenArray, 'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
            
           
            break;
        
        
        case 'coupleCategories':
            require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'migrator.php');
        
        $Migr = new Migrator();
        $dets= $Migr->getMigrationProgress('cats');
        var_dump($Migr->getMigrationProgress('cats') );
            break;
        
        case 'migrali':
            if(!class_exists('ProductWebservice')) require('JsonTrial.php');
//		$migrator = new ProductWebservice();
                $a = new ProductWebservice();
                $productVM = $a->migrali();
            break;
            
            case 'RefreshChildInfos':
                
//                echo ' dati per il call'. $dec['keyTorecall'];
                $resultTosend =RecallLocalChildsData($dec['keyTorecall']);
                
                if(isset($resultTosend) && count($resultTosend)>0)
                    echo json_encode(array ('result'=>'Success', 
                                            'msg'=>' Rinfrescati i dati sui figli presenti in virtuemart' , 'ChildrensDetails' =>$resultTosend
                                          ) );
                 else 
                     echo json_encode(array ('result'=>'Failure', 
                                            'msg'=> ' Non esistono dati attualmente sul prodotto in Uso' , 
                         'Errors' => $_SESSION['Errors']['Errors']
                                          ) );
                break;
                
                case 'AddChildId':
                
//                echo ' dati per il call'. $dec['keyTorecall'];
                $OverWrite =  updateLocalChildsData($dec['keyTorecall'] , $dec['NewIdChildArray']);
                    
                    if( $OverWrite ) {
                        $resultTosend =RecallLocalChildsData($dec['keyTorecall']);
                    }
                
                if(isset($resultTosend) && count($resultTosend)>0)
                    echo json_encode(array ('result'=>'Success', 
                                            'msg'=>' Rinfrescati i dati sui figli presenti in virtuemart' , 'ChildrensDetails' =>$resultTosend
                                          ) );
                 else 
                     echo json_encode(array ('result'=>'Failure', 
                                            'msg'=> ' Non esistono dati attualmente sul prodotto in Uso' , 
                         'Errors' => $_SESSION['Errors']['Errors']
                                          ) );
                break;
            
            
            case 'editVariants':
            if(!class_exists('ProductWebservice')) require('JsonTrial.php');
            if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
//		$migrator = new ProductWebservice();
                $a = new ProductWebservice();
                
                try{
        $result = $a->editCartAttributes($dec, $dec["virtuemart_product_id"]);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
        if($result){
            $msg = 'Prodotto Inserito correttamente';
        } else {
             $msg = 'Qualcosa fu stuartu ';
             foreach($_SESSION['Errors']['Errors'] as $error) {
                 $_SESSION['vmInfo']['msg'].= $error;
                 
             }
        }
        
                echo json_encode($_SESSION['vmInfo']);
            break;
            
            case 'editPrice':
            if(!class_exists('ProductWebservice')) require('JsonTrial.php');
            if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
            
              if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
            
            if(is_null($dec['product']['virtuemart_product_id'])){
                 echo json_encode(array('result'=>'Failure', 'msg'=>'no virtuemart id product found, please check json request'));
                 exit(1);
                 
            }
            
//		$migrator = new ProductWebservice();
                $a = new ProductWebservice();
                 try{
        $productVM = $a->editProductPrices($dec, $isChild);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
        if($productVM){
            $msg = 'Prodotto Inserito correttamente';
        } else {
            // viene fatto qui per aggiungere eventuali eccezioni sollevate durante il processo
             $msg = 'Qualcosa fu stuartu ';
             foreach($_SESSION['Errors']['Errors'] as $error) {
                 $_SESSION['vmInfo']['msg'].= $error;
                 
             }
        }
        
        
                
                echo json_encode($_SESSION['vmInfo']);
            break;
            
    case 'DeleteOldItemFromStore':
        if(!class_exists('ProductWebservice')) require('JsonTrial.php');
        
        if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
            
            if(is_null($dec['product']['virtuemart_product_id'])){
                 echo json_encode(array('result'=>'Failure', 'msg'=>'no virtuemart id product found, please check json request'));
                 exit(1);
                 
            }
            
            $a = new ProductWebservice();
                
                try{
//                    echo ' applicwebIndex unpulibhs prouct line 470 sono qui con json uguale a = ';
//                    var_export ($dec);
        $productVM = $a->deleteProduct($dec['product']);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
        
       echo json_encode($_SESSION['vmInfo']);
                 break;
        
        
        case 'DeleteOldItemFromStoreWithChildrens':
        if(!class_exists('ProductWebservice')) require('JsonTrial.php');
        
        if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
            
            if(is_null($dec['product']['Parent']['virtuemart_product_id']) || is_null($dec['product']['Parent']["product_grid_id"])){
                 echo json_encode(array('result'=>'Failure', 'msg'=>'no parent virtuemart id product found, please check json request. virtuemart_product_id:"'.$dec['product']['Parent']['virtuemart_product_id'].' or product_grid_id:"'.$dec['product']['Parent']["product_grid_id"].'" '));
                 exit(1);
                 
            }
            $a = new ProductWebservice();
            
             $childrenToDelete= $a -> GetChildsId ($dec['product']['Parent']);
            
//            $childrenToDelete = RecallLocalChildsData( $dec['product']['Parent']['product_grid_id'].'-Parent-IT' );
//            
//            if(!isset($childrenToDelete)){
//                // if it's not stored inside the local files , try to get the children infos
//                $childrenToDelete = $dec['product']['Children'];
//            }
//             $figli = var_export ( $childrenToDelete );
//             exit(' trovati i seguenti figli '.$figli );
            
            
            
            if(!isset($childrenToDelete)){
                 echo json_encode(array('result'=>'Failure', 'msg'=>'no parent virtuemart id product found, please check json request'));
                 exit(1);
            }
            
            foreach($childrenToDelete as $Key => $childInfo){
                 
                try{
//                    echo ' applicwebIndex unpulibhs prouct line 470 sono qui con json uguale a = ';
//                    var_export ($dec);
                $productVM = $a->deleteProduct(array ( 'virtuemart_product_id' => $childInfo) );
                }catch (Exception $r){
                    $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
                }
            }
            
            
//            Now I cancel the father if there are no errors
                
                try{
//                    echo ' applicwebIndex unpulibhs prouct line 470 sono qui con json uguale a = ';
//                    var_export ($dec);
                   $productVM = $a->deleteProduct($dec['product']['Parent']);
               }catch (Exception $r){
                   $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
               }
        
       echo json_encode($_SESSION['vmInfo']);
            break;
        
        break;
            
            case 'unpublishProduct':
            if(!class_exists('ProductWebservice')) require('JsonTrial.php');
//		$migrator = new ProductWebservice();
           if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
            
            if(is_null($dec['product']['virtuemart_product_id'])){
                 echo json_encode(array('result'=>'Failure', 'msg'=>'no virtuemart id product found, please check json request'));
                 exit(1);
                 
            }
                $a = new ProductWebservice();
                
                try{
//                    echo ' applicwebIndex unpulibhs prouct line 470 sono qui con json uguale a = ';
//                    var_export ($dec);
        $productVM = $a->unpublishProduct($dec);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
        if($productVM){
            $msg = 'Prodotto Spubblicato correttamente';
        } else {
            // viene fatto qui per aggiungere eventuali eccezioni sollevate durante il processo
             $msg = 'Qualcosa fu stuartu ';
             foreach($_SESSION['Errors']['Errors'] as $error) {
                  $_SESSION['vmInfo']['msg'].= $error;
                 
             }
        }
                echo json_encode($_SESSION['vmInfo']);
            break;
            
            case 'UpdateCategories':
                 if(!class_exists('ProductWebservice')) require('JsonTrial.php');
//		$migrator = new ProductWebservice();
           if(!$dec) {
               $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
           }
           
           if(is_null($dec['virtuemart_product_id'])){
                 echo json_encode(array('result'=>'Failure', 'msg'=>'no parent virtuemart_product_id found, please check json request'));
                 exit(1);
                 
            }
            
            $a = new ProductWebservice();
                
                try{

        $productVM = $a->UpdateCategories($dec);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
       
        echo json_encode($_SESSION['vmInfo']);
                break;
            
case 'unpublishProductWithChildren':
            if(!class_exists('ProductWebservice')) require('JsonTrial.php');
//		$migrator = new ProductWebservice();
           if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
            
            if(is_null($dec['Parent']['productIT']['virtuemart_product_id'])){
                 echo json_encode(array('result'=>'Failure', 'msg'=>'no parent virtuemart_product_id found, please check json request'));
                 exit(1);
                 
            }
                $a = new ProductWebservice();
                
                try{
//                    echo ' applicwebIndex unpulibhs prouct line 470 sono qui con json uguale a = ';
//                    var_export ($dec);
                    $data['product']['virtuemart_product_id'] = $dec['Parent']['productIT']['virtuemart_product_id'];
        $productVM = $a->unpublishProduct($data);
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
       
       $productChildrenNumber = $dec["Children"]["ChidrensKeyArray"];
            foreach($productChildrenNumber as $i){
                
                if(!isset( $dec["Children"][$i]['productIT']['virtuemart_product_id']) ) {
                        $_SESSION['Errors']['Errors'][] = ' Non è stato possibile Disattivare la Variante:"'.$i.'" perchè mancava l id';
                        continue;
                    }
                
                try{
                        $data['product']['virtuemart_product_id'] = $dec["Children"][$i]['productIT']['virtuemart_product_id'];
                    $productVM = $a->unpublishProduct($data);
                    
                }catch (Exception $r){
                    $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
                    echo json_encode(array('result'=>'Failure',  'msg'=>$msg.$_SESSION['ResultMsg'], 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
                   return;
               }
               
               
               
                $ChildrenArray[$i] = array(
                        "slug" => $dec["Children"][$i]['productIT']['slug'],
                        "product_name" => $dec["Children"][$i]['productIT']['product_name'],
                        "mprices" => array("product_price" => $dec["Children"][$i]['productIT']['product_price']),
                        "product_sku" => $dec["Children"][$i]['productIT']['product_sku'],
                        "pordering" => 0,
                        "published" => 0,
                    'virtuemart_product_id'=>$ChildrenPartsToAdd['virtuemart_product_id']
                    );
            }  // all children have been set and I have all ids inside  $_SESSION['ChildrensId'][$i]
            
            
        if($productVM){
            $msg = 'Prodotto Parent e children Spubblicati correttamente';
        } else {
            // viene fatto qui per aggiungere eventuali eccezioni sollevate durante il processo
             $msg = 'Qualcosa fu stuartu ';
             
        }
        
        removeLocalChildsData($dec["Parent"]["productIT"]["product_sku"], $ChildrenArray);
        
                echo json_encode(array('result'=>'Success',  'msg'=>$msg, 'PartsToAdd'=>$PartsToAdd , 'Errors'=>$_SESSION['Errors'] ));
            break;
            
            
                case 'resumeProduct':
            if(!class_exists('ProductWebservice')) require('JsonTrial.php');
//		$migrator = new ProductWebservice();
            if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
            
             if(!$dec) {
                $_SESSION['vmInfo']['result'] = 'Error';
                $_SESSION['vmInfo']['msg'] = 'No Json data parsed, check the functions --> '.$functionTodo.' and file-->'.$file ;
                break;
            }
            
            if(is_null($dec['product']['virtuemart_product_id'])){
                 echo json_encode(array('result'=>'Failure', 'msg'=>'no virtuemart id product found, please check json request'));
                 exit(1);
                 
            }
            
                $a = new ProductWebservice();
                 try{
        $result = $a-> resumeProductWithCustom($dec['product']['virtuemart_product_id']) ;
       }catch (Exception $r){
           $_SESSION['Errors']['Errors'][]='Errore Di sistema : ' .$r->getMessage();
       }
        if($result){
            $msg = 'Prodotto Inserito correttamente';
        } else {
             $msg = 'Qualcosa fu stuartu ';
             foreach($_SESSION['Errors']['Errors'] as $error) {
                  $_SESSION['vmInfo']['msg'].= $error;
                 
             }
        }
                
                echo json_encode(array('result' => 'Success', 'categories'=>$categories));
                break;
                
    default:
        echo json_encode(array('result'=>'Failure', 'msg'=>'no function found!'));
                 exit(1);
}

$messages = JFactory::getSession()->get('application.queue');
print_r($messages);

//if ($directly){
//     addProduct($productIT);
//}

function objectToArray( $object )
    {
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        return array_map( 'objectToArray', $object );
    }
    
    function printOnfile($what, $fileRequest){  
       
       if (!is_writable($fileRequest)) {
                $results = chmod($fileRequest, 0777);
            
            }
            
//$dir = substr(strrchr($fileRequest, "/"), 1);            
//         if (!is_writable($dir)) {  // make also the directory writable ?
             // get last directory in $PATH

// $results = @chmod($dir, '0777');
////                $results = @chmod($fileRequest, 0777);
//            
//            }   
            
            if(!$fileReq = fopen($fileRequest, 'w')) {
                throw new Exception ('File non apribile, controllati esistenza o permessi di '.$fileRequest. ' con cartella '.  getcwd()); 
            }
            
            if (!file_exists($fileRequest)) {
//                vmError('File da scrivere non trovato : '.$fileRequest . ' con cartella '.  getcwd()   );
                throw new Exception ('File non trovato. Nome File completo: '.$fileRequest. ' con cartella '.  getcwd()); 
                
                }
                       
                      
            fwrite($fileReq, $what );
            
            fclose($fileReq);
            
            return true;
            
//            $results = @chmod($fileRequest, 0775);
}

function updateLocalChildsData($keyy, $ChildrenArray){
    //Let's save the Children array for future use 
            $filetto= JPATH_BASE.'/webappWO/fileTemplates/LocalSafeInfo/FilesForChildrens.json';
            
            $jsonPostal = implode('', file($filetto));

//echo ' provo con file senza nulla.'.$file;

            $localDatas = json_decode($jsonPostal , true);
            $err = json_last_error();
            if($err !=0 ){
                $_SESSION['Errors']['Errors'][]= 'Problemi con il salvataggio in locale delle info su LocalSafeInfo/FilesForChildrens.json con errore json='.$err;
            }
       
            if( isset($ChildrenArray) && count($ChildrenArray)>0){
                $localDatas [ $keyy] =$ChildrenArray;
                if( printOnfile(json_encode($localDatas), $filetto) ) return true;
            }
            
            return false;
}

function removeLocalChildsData($keyy, $ChildrenArray){
    //Let's save the Children array for future use 
             $filetto= JPATH_BASE.'/webappWO/fileTemplates/LocalSafeInfo/FilesForChildrens.json';
            
            $jsonPostal = implode('', file($filetto));

//echo ' provo con file senza nulla.'.$file;

            $localDatas = json_decode($jsonPostal , true);
            $err = json_last_error();
            if($err !=0 ){
                $_SESSION['Errors']['Errors'][]= 'Problemi con il salvataggio in locale delle info su LocalSafeInfo/FilesForChildrens.json con errore json='.$err;
            }
       
            if( isset($ChildrenArray) && count($ChildrenArray)>0){
                unset($localDatas [ $keyy] );
                if( printOnfile(json_encode($localDatas), $filetto) ) return true;
            }
            
            return false;
}

function RecallLocalChildsData($keyTorecall){
    //Let's save the Children array for future use 
//    /home/giuseppe/homeProj/horme/webappWO/fileTemplates/LocalSafeInfo/FilesForChildrens.json
    $filetto = JPATH_BASE . '/webappWO/fileTemplates/LocalSafeInfo/FilesForChildrens.json';

    $jsonPostal = implode('', file($filetto));

//echo ' provo con file senza nulla.'.$file;

            $localDatas = json_decode($jsonPostal , true);
            $err = json_last_error();
            if($err !=0 ){
                $_SESSION['Errors']['Errors'][]= 'Problemi a richiamare le informazioni errore json='.$err;
            }
            
            return $localDatas [ $keyTorecall ] ;
       
}

