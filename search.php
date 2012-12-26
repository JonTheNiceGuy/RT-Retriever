<?php
date_default_timezone_set("Europe/London");

$keys=array('is_premier', 'is_film', 'is_repeat', 'is_subtitled', 'is_in_widescreen', 'is_new_series', 'is_BSL_signed', 'is_black_and_white', 'is_radio_times_choice');
$genres=array('-ga'=>'Animation', '-gb'=>'Business', '-gc'=>'Children', '-gd'=>'Comedy', '-ge'=>'Consumer', '-gf'=>'Cookery', '-gg'=>'Current affairs', '-gh'=>'Documentary', '-gi'=>'Drama', '-gj'=>'Education', '-gk'=>'Entertainment', '-gl'=>'Environment', '-gm'=>'Film', '-gn'=>'Game show', '-go'=>'Gardening', '-gp'=>'Health', '-gq'=>'Interests', '-gr'=>'Music and Arts', '-gs'=>'News and Current Affairs', '-gt'=>'Religion', '-gu'=>'Science', '-gv'=>'Sitcom', '-gw'=>'Soap', '-gx'=>'Sport', '-gy'=>'Talk show', '-gz'=>'Transport', '-g1'=>'Travel', '-g2'=>'Youth', '-g9'=>'No Genre', '-g0'=>'TBA');

if(is_http()) {echo "<html>
<head><title>Search the TVGuide</title></head>
<body>
<form action='#' method='post'>
  <div name='period'><span name='SectionTitle'>Period</span>
    <input id='-24' type='radio' name='period' value='-24' ";
  if($_POST['period']=='-24') {echo 'checked="checked"';}
  echo " /> <label for='-24'>Show 24 hours of data</label>
    <input id='-7' type='radio' name='period' value='-7' ";
  if($_POST['period']=='-7') {echo 'checked="checked"';}
  echo " /> <label for='-7'>Show 7 days of data</label>
    <input id='-14' type='radio' name='period' value='' ";
  if($_POST['period']!='-24' AND $_POST['period']!='-7') {echo 'checked="checked"';}
  echo " /> <label for='-14'>Show all available data</label>
    
    <input id='-zeroutc' type='checkbox' name='-zeroutc' value='TRUE' ";
  if($_POST['-zeroutc']==TRUE) {echo 'checked="checked"';}
  echo " /> <label for='-zeroutc'>Start date at 0000 UTC after today</label>
  </div>
  <div name='increment'><span name='SectionTitle'>Increment start date (days)</span>";
  for($key=0; $key<14; $key++) {
    if($key==0) {
      echo "    <input id='inc+' type='radio' name='inc' value='' ";
      if($_POST['inc']=='') {echo 'checked="checked"';}
      echo " /> <label for='inc+$key'>0</label>\r\n";
    } else {
      echo "    <input id='inc+$inc' type='radio' name='inc' value='+{$key}' ";
      if($_POST['inc']=="+{$key}") {echo 'checked="checked"';}
      echo " /> <label for='inc+$key'>+{$key}</label>\r\n";
    }
  }
echo "
  </div>
  <div name='channels'><span name='SectionTitle'>Channels</span>
    <input id='-only' type='radio' name='channels' value='-only' ";
if($_POST['channels']=='-only') {echo 'checked="checked"';} 
echo " /> <label for='-only'>Show only favourite channels</label>
    <input id='-mine' type='radio' name='channels' value='' ";
if($_POST['channels']=='') {echo 'checked="checked"';} 
echo " /> <label for='-mine'>Show just channels I have</label>";
if($get_all_channels!=FALSE) {echo "
    <input id='-all' type='radio' name='channels' value='-all' ";
if($_POST['channels']=='-all') {echo 'checked="checked"';}
echo " /> <label for='-all'>Show all Radio Times channels</label>";
}
echo "
  </div>
  <div name='genre'><span name='SectionTitle'>Genres</span>";
  foreach($genres as $key=>$value) {
    echo "    <input id='genre$key' type='checkbox' name='$key' value='TRUE' ";
    if($_POST[$key]==TRUE) {echo 'checked="checked"';}
    echo "/> <label for='genre$key'>$value</label>\r\n";
  }
echo "
    <input id='-premier' type='checkbox' name='-premier' value='TRUE' ";
if($_POST['-premier']==TRUE) {echo 'checked="checked"';}
echo "/> <label for='-premier'>Premiers and starts of series only</label>
    <input id='-rtchoice' type='checkbox' name='-rtchoice' value='TRUE' ";
if($_POST['-rtchoice']==TRUE) {echo 'checked="checked"';}
echo "/> <label for='-rtchoice'>Only Radio Times Choices</label>
  </div>
  <div name='submit'><input type='submit' /></div>
</form>
";
}

include_once("db.php");

$qry=mysql_query("SELECT id FROM channels LIMIT 1");

if(mysql_errno()>0 OR mysql_num_rows($qry)==0) {die("You've never downloaded the channels, or the last channel run wiped out the database and didn't complete.");}

$inc='now';

if($argc>1) {
  foreach($argv as $key=>$arg) {
    if($key!=0) {
      switch($arg) {
	case '-h':
	case '--help':
	  help();
	  exit(0);
        case '+1':
        case '+2':
        case '+3':
        case '+4':
        case '+5':
        case '+6':
        case '+7':
        case '+8':
        case '+9':
        case '+10':
        case '+11':
        case '+12':
        case '+13':
          $inc="$arg days";
          break;
        case '-m':
        case '--mail':
          $mail=TRUE;
          break;
        case '-r':
	case '--rtchoice':
	  $rtonly=TRUE;
	  break;
	case '-l':
	case '--list':
	  $list_only=TRUE;
	  break;
	case '-f':
	case '--film':
	  $film_only=TRUE;
	  break;
	case '-p':
	case '--premier':
	  $premier_only=TRUE;
	  break;
	case '-o':
        case '-only':
	case '--only':
	  $only=TRUE;
	  break;
	case '-a':
        case '-all':
	case '--all':
	  $all=TRUE;
	  break;
	case '-c':
	case '--cast':
	  $cast=TRUE;
	  break;
	case '-24':
	  $in_next_24=TRUE;
	  break;
	case '-z':
	case '--zeroutc':
	  $starting_tomorrow=TRUE;
	  break;
	case '-7':
	  $next_7_days=TRUE;
	  break;
        case '-ga':
        case '-gb':
        case '-gc':
        case '-gd':
        case '-ge':
        case '-gf':
        case '-gg':
        case '-gh':
        case '-gi':
        case '-gj':
        case '-gk':
        case '-gl':
        case '-gm':
        case '-gn':
        case '-go':
        case '-gp':
        case '-gq':
        case '-gr':
        case '-gs':
        case '-gt':
        case '-gu':
        case '-gv':
        case '-gw':
        case '-gx':
        case '-gy':
        case '-gz':
        case '-g1':
        case '-g2':
        case '-g9':
        case '-g0':
          $set_genre[]=$arg;
	  break;
	default:
	  $search_string=$arg;
      }
    }
  }
} elseif(count($_REQUEST)>0) {
  foreach($_REQUEST as $key=>$arg) {
    if($key=="inc" or $key=="period") {
      switch($arg) {
	case '+1':
	case '+2':
	case '+3':
	case '+4':
	case '+5':
	case '+6':
	case '+7':
	case '+8':
	case '+9':
	case '+10':
	case '+11':
	case '+12':
	case '+13':
	  $inc="$arg days";
	  break;
	case '-7':
	  $next_7_days=TRUE;
	  break;
	case '-24':
	  $in_next_24=TRUE;
	  break;

      }
    } elseif($arg=="TRUE") {
      switch($key) {
	case '-rtchoice':
	  $rtonly=TRUE;
	  break;
	case '-list':
	  $list_only=TRUE;
	  break;
	case '-film':
	  $film_only=TRUE;
	  break;
	case '-premier':
	  $premier_only=TRUE;
	  break;
	case '-only':
	  $only=TRUE;
	  break;
	case '-all':
	  $all=TRUE;
	  break;
	case '-cast':
	  $cast=TRUE;
	  break;
	case '-zeroutc':
	  $starting_tomorrow=TRUE;
	  break;
        case '-ga':
        case '-gb':
        case '-gc':
        case '-gd':
        case '-ge':
        case '-gf':
        case '-gg':
        case '-gh':
        case '-gi':
        case '-gj':
        case '-gk':
        case '-gl':
        case '-gm':
        case '-gn':
        case '-go':
        case '-gp':
        case '-gq':
        case '-gr':
        case '-gs':
        case '-gt':
        case '-gu':
        case '-gv':
        case '-gw':
        case '-gx':
        case '-gy':
        case '-gz':
        case '-g1':
        case '-g2':
        case '-g9':
        case '-g0':
	  $set_genre[]=$key;
	  break;
      }
    } elseif($_POST['channels']=='-only') {
      $only=TRUE;
    } elseif($_POST['channels']=='-all') {
      $all=TRUE;
    } else {
      $search_string=$arg;
    }
  }
} else {
  if(!is_http()) {help();} else {output("</body></html>"); exit();}
}

function help() {
  global $mail_user;
  output("tvguide Search Options:

-g[x] -g[x]       Genre Selection (mix and match from below)
-ga Animation, -gb Business, -gc Children, -gd Comedy, -ge Consumer, 
-gf Cookery, -gg Current affairs, -gh Documentary, -gi Drama, -gj Education, 
-gk Entertainment, -gl Environment, -gm Film, -gn Game show, -go Gardening,
-gp Health, -gq Interests, -gr Music and Arts, -gs News and Current Affairs, 
-gt Religion, -gu Science, -gv Sitcom, -gw Soap, -gx Sport, -gy Talk show, 
-gz Transport, -g1 Travel, -g2 Youth, -g9 No Genre, -g0 TBA

-l  | --list      List all matching criteria only (no details)
-f  | --film      List only films
-p  | --premier   List only if this is a new series or premier
-a  | --all       List all channels (default will show only channels you have)
-o  | --only	  List only the channels you selected as 'Favourites'
-r  | --rtchoice  List only the programs classed as 'Radio Times Choice'
-c  | --cast      Cast listing contains search string, not program title
-z  | --zeroutc   Start the periods below from midnight tonight
-24               List only programs being shown in the next 24 hours
-7	  	  List only programs being shown in the next 7 days
+[1-13]           Increment the start date by this number of days (e.g. +3)
-m  | --mail      Send as a mail to $mail_user
                  (if this says no-one@example.com - edit db.php)
");
exit(0);
}

$where='';
$keys=array('is_premier', 'is_film', 'is_repeat', 'is_subtitled', 'is_in_widescreen', 'is_new_series', 'is_BSL_signed', 'is_black_and_white', 'is_radio_times_choice');
$genres=array('-ga'=>'Animation', '-gb'=>'Business', '-gc'=>'Children', '-gd'=>'Comedy', '-ge'=>'Consumer', '-gf'=>'Cookery', '-gg'=>'Current affairs', '-gh'=>'Documentary', '-gi'=>'Drama', '-gj'=>'Education', '-gk'=>'Entertainment', '-gl'=>'Environment', '-gm'=>'Film', '-gn'=>'Game show', '-go'=>'Gardening', '-gp'=>'Health', '-gq'=>'Interests', '-gr'=>'Music and Arts', '-gs'=>'News and Current Affairs', '-gt'=>'Religion', '-gu'=>'Science', '-gv'=>'Sitcom', '-gw'=>'Soap', '-gx'=>'Sport', '-gy'=>'Talk show', '-gz'=>'Transport', '-g1'=>'Travel', '-g2'=>'Youth', '-g9'=>'No Genre', '-g0'=>'TBA');

$basetime=strtotime($inc);

if($search_string!='' and $cast!=TRUE) {$where.="listings.show_name LIKE '%$search_string%' AND ";}
if($search_string!='' and $cast==TRUE) {$where.="listings.cast LIKE '%$search_string%' AND "; $keys[]='cast';}
if($film_only==TRUE and $premier_only==TRUE) {
  $where.="(listings.is_film='true' OR listings.is_premier='true' OR listings.is_new_series='true') AND ";
} else {
  if($film_only==TRUE) {$where.="listings.is_film='true' AND ";}
  if($premier_only==TRUE) {$where.="(listings.is_premier='true' OR listings.is_new_series='true') AND ";}
}
if($rtonly==TRUE) {$where.=" listings.is_radio_times_choice='true' AND ";}
if($all!=TRUE) {$where.="channel_map.have_access=1 AND ";}
if($only==TRUE) {$where.="channel_map.is_favourite=1 AND ";}
if($starting_tomorrow==TRUE) {
  $totime='';
  if($in_next_24==TRUE) {$totime=date("Y-m-d", strtotime("+48 hours", $basetime)) . " 00:00:00";}
  if($next_7_days==TRUE) {$where.="listings.time_start<'" . date("Y-m-d", strtotime("+8 days", $basetime)) . " 00:00:00' AND ";}

  $fromtime=date("Y-m-d", strtotime("+24 hours", $basetime)) . " 00:00:00";
  $where.="listings.time_start>'$fromtime' AND ";
  if($totime!='') {$where.="listings.time_start<'$totime' AND ";}
} else {
  $totime='';
  if($in_next_24==TRUE) {$totime=date("Y-m-d H:i:s", strtotime("+24 hours", $basetime));}
  if($next_7_days==TRUE) {$totime=date("Y-m-d H:i:s", strtotime("+7 days", $basetime));}

  $fromtime=date("Y-m-d H:i:s", strtotime($inc));
  $where.="listings.time_start>'$fromtime' AND ";
  if($totime!='') {$where.="listings.time_start<'$totime' AND ";}
}
if(is_array($set_genre)) {
  $where.='(';
  foreach($set_genre as $counter=>$key) {
    if($counter>0) {$where.=" OR ";}
    $where.="listings.genre='{$genres[$key]}'";
  }
  $where.=') AND ';
}

if($list_only==TRUE) {
  if($cast!=TRUE) {
    $shows=getQueryAsArray("SELECT listings.show_name FROM listings, channel_map WHERE $where listings.channel_id=channel_map.channel_id GROUP BY listings.show_name");
    if($shows!=FALSE) {foreach($shows as $show) {echo "Show: {$show['show_name']}\r\n";}}
  } else {
    if($search_string!='') {$where="WHERE person LIKE '%$search_string%'";} else {$where='';}
    $shows=getQueryAsArray("SELECT person FROM people $where GROUP BY person");
    if($shows!=FALSE) {foreach($shows as $show) {echo "Person: {$show['person']}\r\n";}}
  }
} else {
  $result='';
  $show=getQueryAsArray("SELECT channels.*, listings.*, channel_map.* FROM listings, channels, channel_map WHERE $where channels.id=listings.channel_id AND channel_map.channel_id=listings.channel_id GROUP BY listings.summary, listings.time_start ORDER BY listings.time_start, channel_map.channel_number");
  if($show!=FALSE) {
    foreach($show as $show_entry) {
      $result.="'{$show_entry['show_name']}'";
      if($show_entry['episode_name']!='') {$result.=":'{$show_entry['episode_name']}'";}
      $result.=" ";
      if($show_entry['release_year']!=0) {$result.="({$show_entry['release_year']}) ";}
      $result.="on {$show_entry['name']} (Ch: {$show_entry['channel_number']}) from {$show_entry['time_start']} - " . date("H:i:s", strtotime($show_entry['time_end'])) . "\r\n";
      $setkey=FALSE;
      foreach($keys as $key) {if($show_entry[$key]=='true') {$result.='| ' . substr(str_replace("_", " ", $key),3) . ' '; $setkey=TRUE;}}
      if($show_entry['rating_age']!='') {$result.="| Rated: {$show_entry['rating_age']} "; $setkey=TRUE;}
      if($show_entry['rating_stars']!='') {$result.="| Radio Times Rating: {$show_entry['rating_stars']}* "; $setkey=TRUE;}
      if($show_entry['genre']!='') {$result.="| Genre: {$show_entry['genre']} "; $setkey=TRUE;}
      if($setkey==TRUE) {$result.="|\r\n";}
      if($show_entry['summary']!='') {$result.="Summary: {$show_entry['summary']}\r\n";}
      $result.="--------------------------\r\n";
    }
  }
  if($totime!='') {$subject="- $totime";}
  if($mail==TRUE) {mail($mail_user, "TV Guide for $fromtime $subject", $result);} else {output($result);}
}

function is_http() {if(isset($_SERVER['REQUEST_METHOD'])) {return TRUE;} else {return FALSE;}}
function output($string) {if(is_http()) {echo nl2br($string);} else {echo $string;}}

?>
