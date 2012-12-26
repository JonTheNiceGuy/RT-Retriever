<?php

if(!isset($db)) {
  $db=array(
    'hostname'=>'localhost',
    'username'=>'root',
    'password'=>'',
    'database'=>'radiotimes'
  );
}

$get_all_channels=FALSE; 
// If this is set to TRUE then it will permit us to see ALL channels. For most people, it's not necessary.

if(!isset($mail_user)) {$mail_user="Example Mail Recipient <no-one@example.com>";}

mysql_connect($db['hostname'], $db['username'], $db['password']) or die("You can't authenticate against the database server");
mysql_select_db($db['database']) or die("You have authenticated against the database server, but you can't access the database tables");

function d_mysql_query($sql) {
  global $debug;
  if($debug!=FALSE) {echo "SQL: $sql\r\n";}
  $qry=mysql_query($sql);
  if(mysql_errno()==0) {
    // It's OK!
  } else {
    if($debug!=FALSE) {if(mysql_errno()>0) {echo "Error: " . mysql_error() . "\r\n";} else {echo "No rows returned!\r\n";}}
  }
  return $qry;
}

function getQueryAsArray($sql, $uid=FALSE, $debug=FALSE) {
  global $debug;
  if($debug!=FALSE) {echo "SQL: $sql\r\n";}
  $qry=mysql_query($sql);
  if(mysql_errno()==0 AND mysql_num_rows($qry)>0) {
    while($data=mysql_fetch_array($qry)) {
      if($uid!=FALSE and array_key_exists($uid, $data)) {
        $return[$data[$uid]]=$data;
      } else {
        $return[]=$data;
      }
    }
  } else {
    if($debug!=FALSE) {if(mysql_errno()>0) {echo "Error: " . mysql_error() . "\r\n";} else {echo "No rows returned!\r\n";}}
    $return=FALSE;
  }
  return($return);
}

?>
