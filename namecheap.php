<?php 

//проверяем баланс на аккаунте
function checkBalance() {

$url = $GLOBALS['url'];
$namecheapUsername = $GLOBALS['namecheapUsername'];
$namecheapApikey = $GLOBALS['namecheapApikey'];
$namecheapClientIp = $GLOBALS['namecheapClientIp'];

  $curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $url.'?ApiUser='.$namecheapUsername.'&ApiKey='.$namecheapApikey.'&UserName='.$namecheapUsername.'&Command=namecheap.users.getBalances&ClientIp='.$namecheapClientIp,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_HTTPHEADER => array(
    'Content-Length: 0'
  ),
));

$response = curl_exec($curl);
$xml = simplexml_load_string($response);
$balance = intval(($xml->CommandResponse->UserGetBalancesResult)['AvailableBalance']);
return $balance;
}

//генератор доменных имен
function createDomainName(){
    if ((count($GLOBALS['domains'])) == 0) {
      echo "\n\nStart generating domain names...\n";
    }
    $txt_file = file_get_contents('words.txt');
    $words = explode("\n", $txt_file);
    $domainName = trim($words[rand(0, 20000)]).'-'.trim($words[rand(0, 20000)]).'.click';
    echo $domainName." generated\n";
    return $domainName; 
}

//проверка домена на доступность
function checkDomainEligibilityRequest($domainName){

$url = $GLOBALS['url'];
$namecheapUsername = $GLOBALS['namecheapUsername'];
$namecheapApikey = $GLOBALS['namecheapApikey'];
$namecheapClientIp = $GLOBALS['namecheapClientIp'];

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url.'?ApiUser='.$namecheapUsername.'&ApiKey='.$namecheapApikey.'&UserName='.$namecheapUsername.'&Command=namecheap.domains.check&ClientIp='.$namecheapClientIp.'&DomainList='.$domainName,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_HTTPHEADER => array(
    'Content-Length: 0'
  ),
));
$response = curl_exec($curl);
curl_close($curl);

$xml = simplexml_load_string($response);
$domain = (($xml->CommandResponse->DomainCheckResult)['Domain']);
$status = (($xml->CommandResponse->DomainCheckResult)['Available']);
$premium = (($xml->CommandResponse->DomainCheckResult)['IsPremiumName']);

if ($status == 'true' and $premium == 'false'){return 'ok';}
else {echo "error: \n".print_r($response)."\n\n"; return 'false';}
}

//формируем массив доменов для покупки
function checkDomainEligibility(){  
    $domainName = createDomainName();
    if (checkDomainEligibilityRequest($domainName) == "ok") {
        array_push($GLOBALS['domains'], $domainName);
    } else {checkDomainEligibility();}
}

//регистрируем домен
function registerDomain($domainName){

$url = $GLOBALS['url'];
$namecheapUsername = $GLOBALS['namecheapUsername'];
$namecheapApikey = $GLOBALS['namecheapApikey'];
$namecheapClientIp = $GLOBALS['namecheapClientIp'];
$domainPrice = $GLOBALS['domainPrice'];

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url.'?ApiUser='.$namecheapUsername.'&ApiKey='.$namecheapApikey.'&UserName='.$namecheapUsername.'&Command=namecheap.domains.create&ClientIp='.$namecheapClientIp.'&DomainName='.$domainName.'&Years=1&AuxBillingFirstName={replace}&AuxBillingLastName={replace}&AuxBillingAddress1={replace}&AuxBillingStateProvince={replace}&AuxBillingPostalCode={replace}&AuxBillingCountry={replace}&AuxBillingPhone={replace}&AuxBillingEmailAddress={replace}&AuxBillingCity={replace}&TechFirstName={replace}&TechLastName={replace}&TechAddress1={replace}&TechStateProvince={replace}&TechPostalCode={replace}&TechCountry={replace}&TechPhone={replace}&TechEmailAddress={replace}&TechCity={replace}&AdminFirstName={replace}&AdminLastName={replace}&AdminAddress1={replace}&AdminStateProvince={replace}&AdminPostalCode={replace}&AdminCountry={replace}&AdminPhone={replace}&AdminEmailAddress={replace}&AdminCity={replace}&RegistrantFirstName={replace}&RegistrantLastName={replace}&RegistrantAddress1={replace}&RegistrantStateProvince={replace}&RegistrantPostalCode={replace}&RegistrantCountry={replace}&RegistrantPhone={replace}&RegistrantEmailAddress={replace}&RegistrantCity={replace}&AddFreeWhoisguard=yes&WGEnabled=yes&GenerateAdminOrderRefId=False&IsPremiumDomain=False',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_HTTPHEADER => array(
    'Content-Length: 0'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
$xml = simplexml_load_string($response);
$domain = (($xml->CommandResponse->DomainCreateResult)['Domain']);
$status = (($xml->CommandResponse->DomainCreateResult)['Registered']);
$amount = round((($xml->CommandResponse->DomainCreateResult)['ChargedAmount']), 2);

if (($domain == $domainName) and ($status == true) and ($amount == $domainPrice)) {
    array_push($GLOBALS['boughtDomains'], $domainName);
    return 'ok';
} else {echo "error"; return 'false';}
}


//меняем NSы
function changeNameservers($domainName){

$url = $GLOBALS['url'];
$namecheapUsername = $GLOBALS['namecheapUsername'];
$namecheapApikey = $GLOBALS['namecheapApikey'];
$namecheapClientIp = $GLOBALS['namecheapClientIp'];
$separatedDomain = explode(".", $domainName);

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url.'?ApiUser='.$namecheapUsername.'&ApiKey='.$namecheapApikey.'&UserName='.$namecheapUsername.'&Command=namecheap.domains.dns.setCustom&ClientIp='.$namecheapClientIp.'&SLD='.$separatedDomain[0].'&TLD='.$separatedDomain[1].'&NameServers={replace},{replace}',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_HTTPHEADER => array(
    'Content-Length: 0'
  ),
));

$response = curl_exec($curl);
curl_close($curl);

$xml = simplexml_load_string($response);
$domain = (($xml->CommandResponse->DomainDNSSetCustomResult)['Domain']);
$status = (($xml->CommandResponse->DomainDNSSetCustomResult)['Updated']);

if (($domain == $domainName) and ($status == true)) {
    return 'ok';
} else {print_r($response); return 'false';}
}