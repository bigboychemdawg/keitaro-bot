<?php

//создаем домен
function createZone($domainName){

$cloudflareEmail = $GLOBALS['cloudflareEmail'];
$cloudflareApikey = $GLOBALS['cloudflareApikey'];
$cloudflareAccountId = $GLOBALS['cloudflareAccountId'];

$payload = json_encode(array("name"=>$domainName, "jump_start"=>true, "organization"=>array("id"=>$cloudflareAccountId)));

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>$payload,
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Email: '.$cloudflareEmail,
    'X-Auth-Key: '.$cloudflareApikey,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);

$decoded_json = json_decode($response);
$id = $decoded_json->result->id;
return $id;
}

//включаем HTTPS
function alwaysUseHTTPS($id){

$cloudflareEmail = $GLOBALS['cloudflareEmail'];
$cloudflareApikey = $GLOBALS['cloudflareApikey'];

$payload = json_encode(array("value"=>"on"));

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/'.$id.'/settings/always_use_https',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS =>$payload,
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Email: '.$cloudflareEmail,
    'X-Auth-Key: '.$cloudflareApikey,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
}


//устанавлием FULL SSL
function changeSslSettings($id){

$cloudflareEmail = $GLOBALS['cloudflareEmail'];
$cloudflareApikey = $GLOBALS['cloudflareApikey'];

$payload = json_encode(array("value"=>"full"));

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/'.$id.'/settings/ssl',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS =>$payload,
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Email: '.$cloudflareEmail,
    'X-Auth-Key: '.$cloudflareApikey,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
}


//включаем Brotil
function changeBrotilSetting($id){

$cloudflareEmail = $GLOBALS['cloudflareEmail'];
$cloudflareApikey = $GLOBALS['cloudflareApikey'];

$payload = json_encode(array("value"=>"on"));

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/'.$id.'/settings/brotli',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS =>$payload,
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Email: '.$cloudflareEmail,
    'X-Auth-Key: '.$cloudflareApikey,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
}


//включаем автоматическое переключение на HTTPS
function automaticHttpsRewrite($id){

$cloudflareEmail = $GLOBALS['cloudflareEmail'];
$cloudflareApikey = $GLOBALS['cloudflareApikey'];

$payload = json_encode(array("value"=>"on"));

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/'.$id.'/settings/automatic_https_rewrites',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS =>$payload,
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Email: '.$cloudflareEmail,
    'X-Auth-Key: '.$cloudflareApikey,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
}


//Включаем minify для CSS, HTML, JS
function minify($id){

$cloudflareEmail = $GLOBALS['cloudflareEmail'];
$cloudflareApikey = $GLOBALS['cloudflareApikey'];

$payload = json_encode(array("value"=>array("css"=>"on","html"=>"on","js"=>"on")));

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/'.$id.'/settings/minify',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS =>$payload,
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Email: '.$cloudflareEmail,
    'X-Auth-Key: '.$cloudflareApikey,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
}


//прибиваем домен к трекеру
function changeDnsRecord($id, $domainName){

$cloudflareEmail = $GLOBALS['cloudflareEmail'];
$cloudflareApikey = $GLOBALS['cloudflareApikey'];
$keitaroIP = $GLOBALS['keitaroIP'];

$payload = json_encode(array("type"=>"A","name"=>"@","content"=>$keitaroIP,"ttl"=>120,"proxied"=>true));

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/'.$id.'/dns_records',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>$payload,
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Email: '.$cloudflareEmail,
    'X-Auth-Key: '.$cloudflareApikey,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
array_push($GLOBALS['cloudflareDomains'], $domainName);
}
