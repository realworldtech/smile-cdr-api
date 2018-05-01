#!/usr/bin/php

<?php
ini_set("date.timezone", "Australia/Sydney");
ini_set('memory_limit', '2G');


$options = getopt("c");

include 'config_params.php';

function getAccount($usn)
{

  global $soapuser, $soappass, $soapdomain;
  $login = array('login' => $soapuser,'password' => $soappass, 'trace' => 1);
  $wsdl_url= "https://$soapdomain/live/ws/v2/account?wsdl";
  use_soap_error_handler(false);
  try {
    $client = new SOAPClient($wsdl_url,$login);
    //var_dump($client->__getFunctions());
    $account = $client->get(array('usn' => $usn));
  } catch (Exception $e) {
    die("There was an error accessing the account ".$e->getmessage()."\n");
  }

  return $account->Account->Subscriptions;

}

function getRatingPeriods($usn) {
  global $soapuser, $soappass, $soapdomain;
  $login = array('login' => $soapuser,'password' => $soappass, 'trace' => 1);
  $wsdl_url= "https://$soapdomain/live/ws/v2/subscription?wsdl";
  use_soap_error_handler(false);
  try {
    $client = new SOAPClient($wsdl_url,$login);
    $url = $client->getRatingPeriods(
      array(
        'usn' => $usn
        ));
    return $url;
  } catch (Exception $e) {
    // We probably should do something better here.
    // Currently when there is no activity, we are returned a SOAP Fault
    // due to a server bug. Currently we are just silently failing and
    // hoping for the best. Not generally the best course of action.

    echo "====== REQUEST HEADERS =====" . PHP_EOL;
    var_dump($client->__getLastRequestHeaders());
    echo "========= REQUEST ==========" . PHP_EOL;
    var_dump($client->__getLastRequest());
    echo "========= RESPONSE =========" . PHP_EOL;
    var_dump($client->__getLastResponse());
    //echo "Error retrieving stats for service $usn\n";
  }
}

function getRatingsForPeriod($periodID) {
  global $soapuser, $soappass, $soapdomain;
  $login = array('login' => $soapuser,'password' => $soappass, 'trace' => 1);
  $wsdl_url= "https://$soapdomain/live/ws/v2/subscription?wsdl";
  use_soap_error_handler(false);
  try {
    $client = new SOAPClient($wsdl_url,$login);
    $url = $client->getRatingsForPeriod(
      array(
        'ratingPeriodId' => $periodID
        ));
    return $url;
  } catch (Exception $e) {
    // We probably should do something better here.
    // Currently when there is no activity, we are returned a SOAP Fault
    // due to a server bug. Currently we are just silently failing and
    // hoping for the best. Not generally the best course of action.

    /*echo "====== REQUEST HEADERS =====" . PHP_EOL;
    var_dump($client->__getLastRequestHeaders());
    echo "========= REQUEST ==========" . PHP_EOL;
    var_dump($client->__getLastRequest());
    echo "========= RESPONSE =========" . PHP_EOL;
    var_dump($client->__getLastResponse());*/
    //echo "Error retrieving stats for service $usn\n";
  }
}

function getActivityStatementBatch($usn, $batchId) {
  global $soapuser, $soappass, $soapdomain;
  $login = array('login' => $soapuser,'password' => $soappass, 'trace' => 1);
  $wsdl_url= "https://$soapdomain/live/ws/v2/subscription?wsdl";
  use_soap_error_handler(false);
  try {
    $client = new SOAPClient($wsdl_url,$login);
    $url = $client->getActivityStatementBatch(
      array(
        'usn' => $usn,
        'lastBatchId' => $batchId
        ));
    return $url;
  } catch (Exception $e) {
    // We probably should do something better here.
    // Currently when there is no activity, we are returned a SOAP Fault
    // due to a server bug. Currently we are just silently failing and
    // hoping for the best. Not generally the best course of action.

    /*echo "====== REQUEST HEADERS =====" . PHP_EOL;
    var_dump($client->__getLastRequestHeaders());
    echo "========= REQUEST ==========" . PHP_EOL;
    var_dump($client->__getLastRequest());
    echo "========= RESPONSE =========" . PHP_EOL;
    var_dump($client->__getLastResponse());*/
    //echo "Error retrieving stats for service $usn\n";
  }
}

function getActivityStatementRange($usn, $startDate, $endDate) {
  global $soapuser, $soappass, $soapdomain;
  $login = array('login' => $soapuser,'password' => $soappass, 'trace' => 1);
  $wsdl_url= "https://$soapdomain/live/ws/v2/subscription?wsdl";
  use_soap_error_handler(false);
  try {
    $client = new SOAPClient($wsdl_url,$login);
    $url = $client->getActivityStatementRange(
      array(
        'usn' => $usn,
        'startTimestamp' => $startDate->format(DateTime::ATOM),
        'endTimestamp' => $endDate->format(DateTime::ATOM)
        ));
    return $url;
  } catch (Exception $e) {
    // We probably should do something better here.
    // Currently when there is no activity, we are returned a SOAP Fault
    // due to a server bug. Currently we are just silently failing and
    // hoping for the best. Not generally the best course of action.

    echo "====== REQUEST HEADERS =====" . PHP_EOL;
    var_dump($client->__getLastRequestHeaders());
    echo "========= REQUEST ==========" . PHP_EOL;
    var_dump($client->__getLastRequest());
    echo "========= RESPONSE =========" . PHP_EOL;
    var_dump($client->__getLastResponse());
    //echo "Error retrieving stats for service $usn\n";
  }
}

function findPropertyByName($name, $type, &$properties) {
  foreach ($properties->Object->$type as $prop) {
    if ($prop->name == $name) {
       return $prop->_;
    }
  } 
}

//load subscriptinos for users account
$subs = getAccount($masterUSN);
$periods = getRatingPeriods($masterUSN);
//print_r($periods);
$periodList = $periods->RatingPeriodList->RatingPeriodSummary;
$period = $periodList[count($periodList)-1];
if ($period->status == "Open") {
  $period = $periodList[count($periodList)-2];
}
$periodID = $period->ratingPeriodId;

//$startDate = new DateTime('0:00 first day of previous month');
//$endDate = new DateTime('23:59 last day of previous month');
//$dir = $startDate->format("YmdHis").'-'.$endDate->format('YmdHis');
//mkdir($dir);

$periodEnd = $period->ratingPeriodEnd;
$dir = $periodEnd;
mkdir ("output");
$dir = "output/".$dir;
mkdir($dir);


if (isset($options["c"])) {
  $fp = fopen("/src/output/".$periodEnd.".csv", "w");
  $first = true;
  foreach (scandir($dir) as $file) {
    print "Processing $file".PHP_EOL;
    if ('.' === $file) continue;
    if ('..' === $file) continue;
    if ('.DS_Store' === $file) continue;
    $file = file_get_contents($dir."/".$file);
    $lines = explode("\n", $file);
    if (!$first) {
      array_shift($lines);
    }
    $first = false;
    array_pop($lines);
    array_pop($lines);
    fwrite($fp, join("\n", $lines));
    fwrite($fp, "\n");
  }
  exit(0);
}


//iterate t
foreach ($subs->Subscription as $sub) {

  $username = findPropertyByName('username', 'String', $sub->Properties);
  $usn = $sub->USN;
  if (file_exists($dir.'/'.$username.'-'.$usn.'.csv')) continue;
  echo $usn." $sub->ServiceName ($username)".PHP_EOL;
  $periods = getRatingPeriods($usn);
  $periodList = $periods->RatingPeriodList->RatingPeriodSummary;
  if (!is_array($periodList)) continue;
  $period = $periodList[count($periodList)-2];
  $periodID = $period->ratingPeriodId;
  if ($period->invoicingPeriodEnd == False) {
    sleep(1);
    $periods = getRatingPeriods($usn);
    $periodList = $periods->RatingPeriodList->RatingPeriodSummary;
    $period = $periodList[count($periodList)-2];
    $periodID = $period->ratingPeriodId;
  }
  print " -Downloading usage for period ending ".$period->invoicingPeriodEnd." which is period id $periodID".PHP_EOL;
  if (substr($period->invoicingPeriodEnd,0,7)!=substr($periodEnd,0,7)) {
    print " -There is no usage relevant to this service in this download period".PHP_EOL;
    continue;
  }

  //$url = getActivityStatementBatch($usn, 0);
  $urlObj = getRatingsForPeriod($periodID);
  if ($urlObj != NULL) {
      $url = $urlObj->ActivityBatchURL->URL;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERPWD, "$soapuser:$soappass");
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
      $result = curl_exec($ch);
      curl_close($ch);
      $fh = fopen ($dir.'/'.$username.'-'.$usn.'.csv', 'w');
      fwrite($fh, $result);
      fclose($fh);
  } else {
    print " -URL was not provided for this service, can't get usage".PHP_EOL;
  }
}
