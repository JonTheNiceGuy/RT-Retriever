<?php
date_default_timezone_set("Europe/London");

if(is_http()) {echo "<html>
<head>Now and Next from TVGuide</head>
<body><p>This information is available for <a href='now_and_next.php?h'>the channels you have access to</a>, <a href='now_and_next.php?f'>favourite channels only</a> (default) and <a href='no_and_next.php?n'>channels you don't have access to</a>.</p>";
}

output("This may be a time-consuming request. Your information is loading. Please wait.\r\n\r\n");

include_once("db.php");

$qry=mysql_query("SELECT id FROM channels LIMIT 1");

if(mysql_errno()>0 OR mysql_num_rows($qry)==0) {die("You've never downloaded the channels, or the last channel run wiped out the database and didn't complete.");}

$time_now=date("Y-m-d H:i:s", strtotime("now"));

$sql=" AND m.is_favourite=1";

if($argc>1) {
  switch($argv[1]) {
    case "-a":
    case "--access":
      $sql=" AND m.have_access=1";
      break;
    case "-n":
    case "--noaccess":
      $sql=" AND m.have_access=0";
      break;
    case "-h":
    case "--help":
      echo "php -q now_and_next.php [-a | -f | -n]
-a | --access     Show all channels you have access to
-f | --favourites Show only your favourite channels (default)
-n | --noaccess   Show only channels you don't have access to
";
      exit(0);
    case "-f":
    case "--favourites":
    default:
      $sql=" AND m.is_favourite=1";
      break;
  }
}

if(isset($_REQUEST['h'])) {$sql=" AND m.have_access=1";}
if(isset($_REQUEST['f'])) {$sql=" AND m.is_favourite=1";}
if(isset($_REQUEST['n'])) {$sql=" AND m.have_access=0";}

$channels=getQueryAsArray("SELECT c.id AS id, c.name AS name, m.channel_number AS number, m.is_favourite AS favourite FROM channels AS c, channel_map AS m WHERE m.channel_id=c.id $sql ORDER BY m.channel_number, c.name", "id");

$keys=array('is_premier', 'is_film', 'is_repeat', 'is_subtitled', 'is_in_widescreen', 'is_new_series', 'is_BSL_signed', 'is_black_and_white', 'is_radio_times_choice');

$getkeys='show_name, release_year, time_start, time_end, rating_age, rating_stars, summary';
foreach($keys as $key) {$getkeys.=", $key";}

foreach($channels as $channel_id=>$channel_data) {
  $on_now=getQueryAsArray("SELECT $getkeys FROM listings WHERE time_end>'$time_now' AND channel_id='$channel_id' GROUP BY time_start ORDER BY time_start ASC limit 2");
  if($on_now!=FALSE) {
    output("On {$channel_data['name']} (Ch: {$channel_data['number']})\r\n");
    foreach($on_now as $count=>$show_entry) {
      if(is_http()) {echo "<b>";} else {echo "'";}
      output("{$show_entry['show_name']}");
      if(is_http()) {echo "</b>";} else {echo "'";}
      if($show_entry['release_year']!=0) {output(" ({$show_entry['release_year']})");}
      output(" between " . date("H:i", strtotime($show_entry['time_start'])) . " and " . date("H:i", strtotime($show_entry['time_end'])) . "\r\n");
      $setkey=FALSE;
      foreach($keys as $key) {if($show_entry[$key]=='true') {output('| ' . substr(str_replace("_", " ", $key),3) . ' '); $setkey=TRUE;}}
      if($show_entry['rating_age']!='') {output("| Rated: {$show_entry['rating_age']} "); $setkey=TRUE;}
      if($show_entry['rating_stars']!='') {output("| Radio Times Rating: {$show_entry['rating_stars']}* "); $setkey=TRUE;}
      if($setkey==TRUE) {output("|\r\n");}
//      if($count==0) {output("\r\n");}
    }
  }
  if(is_http()) {echo "<hr>";} else {output("--------------------\r\n");}
}

function is_http() {if(isset($_SERVER['REQUEST_METHOD'])) {return TRUE;} else {return FALSE;}}
function output($string) {if(is_http()) {echo nl2br($string);} else {echo $string;}}

?>
