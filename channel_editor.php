<html>
<head>TV Guide Channel Editor</head>
<body>
<p>Please note, this editor processes per-line entries, not per-page.</p>
<?php

include_once("db.php");

$qry=mysql_query("SELECT id FROM channels LIMIT 1");

if(mysql_errno()>0 OR mysql_num_rows($qry)==0) {die("You've never downloaded the channels, or the last channel run wiped out the database and didn't complete.");}

switch(strtolower($_POST['action'])) {
  case 'update':
    $qry=mysql_query("SELECT channel_number FROM channel_map WHERE channel_id='" . mysql_real_escape_string($_POST['channel_id']) . "'");
    if(mysql_errno()>0 or mysql_num_rows($qry)==0) {
      $sql="INSERT INTO channel_map (channel_id, channel_number, have_access, is_favourite) VALUES ('" . mysql_real_escape_string($_POST['channel_id']) . "','" . mysql_real_escape_string($_POST['channel_number']) . "', '" . getBoolValue($_POST['have_access']) . "', '" . getBoolValue($_POST['is_favourite']) . "')";
    } else {
      if($_POST['channel_number']=="") {$sql="DELETE FROM channel_map WHERE channel_id='" . mysql_real_escape_string($_POST['channel_id']) . "'";} else {
        $sql="UPDATE channel_map SET channel_number='" . mysql_real_escape_string($_POST['channel_number']) . "', have_access='" . getBoolValue($_POST['have_access']) . "', is_favourite='" . getBoolValue($_POST['is_favourite']) . "' WHERE channel_id='" . mysql_real_escape_string($_POST['channel_id']) . "'";
      }
    }
    mysql_query($sql);
    break;
  default:
}

$page="channel_editor.php";

if(isset($_REQUEST['ha'])) {
  $channels=getQueryAsArray("SELECT channels.id as id, channels.name as name FROM channels, channel_map WHERE channel_map.have_access=1 AND channel_map.channel_id=channels.id ORDER BY channels.name", "id");
  $mode1=$page;
  $mode1text="normal";
  $mode2=$page . "?noha";
  $mode2text="channels you <b>don't</b> have";
  $page.="?ha";
} elseif(isset($_REQUEST['noha'])) {
  $channels=getQueryAsArray("SELECT channels.id as id, channels.name as name FROM channels, channel_map WHERE channel_map.have_access=0 AND channel_map.channel_id=channels.id ORDER BY channels.name", "id");
  $mode1=$page;
  $mode1text="normal";
  $mode2=$page . "?ha";
  $mode2text="channels you have";
  $page.="?noha";
} else {
  $channels=getQueryAsArray("SELECT id, name FROM channels ORDER BY name", "id");
  $mode1=$page . "?ha";
  $mode1text="channels you have";
  $mode2=$page . "?noha";
  $mode2text="channels you <b>don't</b> have";
}
$channel_map=getQueryAsArray("SELECT channel_id, channel_number, have_access, is_favourite FROM channel_map", "channel_id");

echo "<p><a href='$mode1'>Change to '$mode1text' mode</a> | <a href='$mode2'>Change to '$mode2text' mode</a></p>";

if(count($channels)>0) {
  echo "<table>
  <tr>
    <th>Channel Name</th>
    <th>Channel Number</th>
    <th>Have Access</th>
    <th>Is a Favourite</th>
  </tr>";
  foreach($channels as $channel_id=>$channel_data) {
    echo "
<form action='$page#$channel_id' method='post'>
  <input type='hidden' name='action' value='update'>
  <input type='hidden' name='channel_id' value='$channel_id'>
  <tr>
    <td><a name='$channel_id'>{$channel_data['name']}</a></td>
    <td>
      <input type='text' name='channel_number' size='3' value='{$channel_map[$channel_id]['channel_number']}'>
      <input type='submit' style='display:none;'>
    </td>
    <td><input type='checkbox' name='have_access'" . showChecked($channel_map[$channel_id]['have_access'], TRUE) . "></td>
    <td><input type='checkbox' name='is_favourite'" . showChecked($channel_map[$channel_id]['is_favourite']) . "></td>
  </tr>
</form>";
  }
  echo "</table>";
}

function getBoolValue($value) {
  if($value=="on") {return 1;} else {return 0;}
}
function showChecked($value, $default=FALSE) {
  if($value=="1" OR ($default==TRUE AND $value=='')) {return " checked";} else {return "";}
}
?>
