<?php
include('../vendor/autoload.php');
include('credentials.php');
include('namecheap.php');
include('cloudflare.php');
include('keitaro.php');

use Telegram\Bot\Api;
$bot_token = $_GET['token'];
$telegram = new Api($bot_token);

require "rb-mysql.php";
R::setup('mysql:host=localhost;dbname={replace}','root', '{replace}');
if(!R::testConnection()) die('No DB connection!');

$mem_var = new Memcached();
$mem_var->addServer("127.0.0.1", 11211);

$result = $telegram -> getWebhookUpdates();
$chat_id = -1001178974387;
$text = $result["message"]["text"];
$username = $result["message"]["from"]["username"];
$currentChat = $result["message"]["chat"]["id"];

if ($currentChat != $chat_id) {
  $telegram->sendMessage(['chat_id' => $currentChat, 'parse_mode' => 'HTML', 'text' => "Здесь базар неуместен"]);
  die();
}

$step1 = $mem_var->get("step1");
$step2 = $mem_var->get("step2");
$step3 = $mem_var->get("step3");
$step4 = $mem_var->get("step4");
$step5 = $mem_var->get("step5");
$step6 = $mem_var->get("step6");
$step7 = $mem_var->get("step7");

if ($text == "/create@evgeniy_killer_bot") {
  $mem_var->set("step1", "true");
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Для кого суетимся?"]); 
}
if ($step1 == true) {
  $mem_var->set("storedTargetUsername", $text);
  $mem_var->set("step2", "true");
  $mem_var->delete("step1");
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Сколько шт делаем?"]);
} 
if ($step2 == true) {
  $mem_var->set("storedQuantity", $text);
  $mem_var->delete("step2");
  $mem_var->set("step3", "true");
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Источник?"]);
} 
if ($step3 == true) {
  $mem_var->set("storedTrafficsource", $text);
  $mem_var->delete("step3");
  $mem_var->set("step4", "true");
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "ID группы кампаний?"]);
}
if ($step4 == true) {
  $mem_var->set("storedBuyerId", $text);
  $mem_var->delete("step4");
  $mem_var->set("step5", "true");
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "ID офера?"]);
}
if ($step5 == true) {
  $mem_var->set("storedOfferId", $text);
  $mem_var->delete("step5");
  $mem_var->set("step6", "true");
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "ID лендинга?"]);
}
if ($step6 == true) {
  $mem_var->set("storedLandingId", $text);
  $mem_var->delete("step6");
  $mem_var->set("step7", "true");
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Гео?"]);
}
if ($step7 == true) {
  $mem_var->set("geo", $text);
  $mem_var->delete("step7");
  $quantity = intval($mem_var->get("storedQuantity"));
  $targetUsername = $mem_var->get("storedTargetUsername");
  $trafficSource = $mem_var->get("storedTrafficsource");
  $groupId = $mem_var->get("storedBuyerId");
  $offerId = $mem_var->get("storedOfferId");
  $landingId = $mem_var->get("storedLandingId");
  $geo = $mem_var->get("geo");

//проверяем баланс перед покупкой
 if ((checkBalance()) < ($quantity * $domainPrice)) {
    $message = "Not enough balance for this operation";
    $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Денег нет, но вы держитесь (пополните счет Namecheap)"]);
    die($message);
 }

$telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Добро, щас докурю и всё сделаем по красоте"]);
$domains = array();
$boughtDomains = array();
$cloudflareDomains = array();
$storedDomains = array();
$keitaroDomains = array();

//сообщаем телеграму что всё ок и продолжаем выполнение скрипта
ob_start();
$size = ob_get_length();
header("Content-Encoding: none");
header("Content-Length: {$size}");
header("Connection: close");
ob_end_flush();
@ob_flush();
flush();

//генерируем доменные имена и проверяем их на доступность
 do {
 checkDomainEligibility();
 } while ((count($GLOBALS['domains'])) < $GLOBALS['quantity']);


//регистрируем домены и меняем NSы
  if ((count($GLOBALS['domains'])) == $GLOBALS['quantity']) {
     print_r("\nDomains are ready to buy!\n");
     $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Метнулся за доменами..."]);
     foreach ($GLOBALS['domains'] as $domainName) {
      registerDomain($domainName);
      changeNameservers($domainName);
     }
  }

//загружаем домены в Cloudflare
  if ((count($GLOBALS['boughtDomains'])) == $GLOBALS['quantity']) {
    print_r("\nDomains are bought and nameservers are successfully changed\n\n");    foreach ($GLOBALS['boughtDomains'] as $domainName) {
     if ((count($GLOBALS['cloudflareDomains'])) == 0) {
       echo "\n\nStart adding domains to Cloudflare...\n";
      $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Прокидываю их через Cloudflare..."]);     }
      $id = createZone($domainName);
      alwaysUseHTTPS($id);
      changeSslSettings($id);
      changeBrotilSetting($id);
      automaticHttpsRewrite($id);
      minify($id);
      changeDnsRecord($id, $domainName);
    }
  }

//создаем кампании кейтаро
 if ((count($GLOBALS['cloudflareDomains'])) == $GLOBALS['quantity']) {
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Создаю кампании кейтаро..."]);
  foreach ($GLOBALS['cloudflareDomains'] as $domainName) {
    $campaignId = createCampaign($geo, $trafficSource, $domainName, $groupId);
    createStream($campaignId, $landingId, $offerId);
    $domainId = createSimpleDomain($domainName, $campaignId);
    updateSimpleDomainStatus($domainId);
    array_push($GLOBALS['keitaroDomains'], $domainName);
  }
 }

//записываем покупки в БД
 if ((count($GLOBALS['keitaroDomains'])) == $GLOBALS['quantity']) {
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Записываю в личное дело..."]);
  foreach ($GLOBALS['keitaroDomains'] as $domainName) {
    $domainsStorage = R::dispense('domains');
    $domainsStorage->name = $domainName;
    $domainsStorage->who_bought =  $mem_var->get("storedTargetUsername");
    $domainsStorage->when_bought = date("Y-m-d");
    $domainsStorage->when_expire = date("Y-m-d", strtotime("+1 year"));
    if ($trafficSource == "FB") {
      $domainsStorage->used_fb = true;
    } elseif ($trafficSource == "TT") {
      $domainsStorage->used_tt = true;
    }
    R::store($domainsStorage);
    array_push($GLOBALS['storedDomains'], $domainName);
  }
 }

 if ((count($GLOBALS['storedDomains'])) == $GLOBALS['quantity']) {
  $mem_var->delete("storedQuantity");
  $mem_var->delete("storedTargetUsername");
  $mem_var->delete("storedTrafficsource");
  $mem_var->delete("storedBuyerId");
  $mem_var->delete("storedOfferId");
  $mem_var->delete("storedLandingId");
  $mem_var->delete("geo");
  $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Ссыло4ки готовы, прогрузятся минут через 20-30: \n".implode("\n", $GLOBALS['domains'])."\n\nС тебя поляна!"]);
  die();
}
}