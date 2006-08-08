<?php
/*===========================
  Author:  Marek /Flash/ Wywial
  Email:   onjin@onjin.net
  Program: import / export Jabber roster
  Licence: BSD
  ===========================*/

$IMPORT=0;
$ADDED=0;
require_once('class.jabber.php');
?>
<?php print '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <title>Jabber - roster import/export</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		   
	</head>

<body>
<div id="main">
<?php
if(
	isset($_POST['login']) && $_POST['login'] != ''
	&& isset($_POST['haslo']) && $_POST['haslo'] != ''
	) {
	print '<div id="log">';
	print '<h3>dziennik [log]</h3>';

	// params
	$JABBER = new Jabber;
	$JABBER->server     = $_POST['server'];
	$JABBER->port       = 5222;
	$JABBER->username   = $_POST['login'];
	$JABBER->password   = $_POST['haslo'];
	$JABBER->resource   = 'JabUtil';
	$JABBER->enable_logging = 0;
	$JABBER->log_filename   = 'log.log';
	print 'łączenie [Connecting]<br/>';
	flush();

	// connecting to server
	$JABBER->Connect() or die('Nie można połączyć się z serwerem. <a href="import.php">wróć³Ä</a> [Unable connect to server. <a href="import.php">back</a>]');

	// logging as valid user
	print "Loguję jako $_POST[login]@$_POST[server] [Logging as $_POST[login]@$_POST[server] ]<br/>";
	$JABBER->SendAuth() or die('Nie można autoryzować Twojego konta. <a href="import.php">wróć</c> [Access denied. <a href="import.php">back</a><br/>');
	flush();

	// converting chars
	#$kont = iconv('cp1250', 'utf-8', $_POST['kontakty']);
	if($_POST['kontakty']) {
		$kont = $_POST['kontakty'];
	
	// process contacts
	$kontakty = split("\n", $kont);
	foreach($kontakty as $kontakt) {

		$kontakt = split(';', $kontakt);
		$jid = $kontakt[0];
		$name = $kontakt[1];
		$subscription = $kontakt[2];
		$group = $kontakt[3];
		print "Dodaję: $name &lt;$jid&gt; $group ";
		print "[Adding: $name &lt;$jid&gt; $group]<br/>";
		//$JABBER->RosterAddUser($jid , $group , $name );

		// creating XML command 
		$xml = '<item name="'.$name.'" jid="'.$jid.'">';
		if($group != '') $xml .= '<group>'.$group.'</group>';
		$xml .= '</item>';

		// send XML command
		$JABBER->SendIq($_POST['login'].'@'.$_POST['server'], 'set', $group, 'jabber:iq:roster', $xml);

		// Subscribe contact
		$JABBER->Subscribe($jid);
		flush();
		$ADDED = 1;
	}
	} else {

	// get roster
	print 'pobieram kontakty [importing contacts]<br/>';
	$JABBER->RosterUpdate();
	}

	// disconnect from server
	print 'wylogowywuję [logout]<br/>';
	$JABBER->Disconnect() ;
	print '<hr/>';
	print '</div>';
	$IMPORT=1;
}
if(!$ADDED) {
if($IMPORT) {
	print '<h2>dodanie kontaktów do listy na serwerze [adding contats to roster]</h2>';
} else {
	print '<h2>Import kontaktów z serwera [importing roster from server]</h2>';
}
?>
<form name="konwert" method="post">
Nazwa servera Jabber [Jabber server]:<br/>
<input name="server" type="text"/><br/>
Nazwa użytkownika Jabber [Jabber user]:<br/>
<input name="login" type="text"/><br/>
Hasło użytkownika Jabber [user password]:<br/>
<input name="haslo" type="password"/><br/>
<?php
if($JABBER->roster) {

 print "# jid ; name ; subscription ; group\n<br/>";
 print '<textarea cols="80" rows="20" name="kontakty">';
 foreach($JABBER->roster as  $user) {
	 print $user['jid'].';';
	 print $user['name'].';';
	 print $user['subscription'].';';
	 print $user['group'];
	 print "\n";
 }
 print '</textarea><hr/>';
}
if($IMPORT) {
	print '<input type="submit" class="submit" value="wyślij kontakty na serwer [add contacts to roster]"/><br/>';
} else {
	print '<input type="submit" class="submit" value="pobierz kontakty [get roster]"/><br/>';
}
?>
</form>
<?php } // if(!$ADDED) ?>

</div>
<p class="footer">
Author: Marek /Flash/ Wywiał &lt;onjin@onjin.net&gt; jid:onjinx@chrome.pl source: <a href="import.php.txt">import.php</a> | main <a href="import.php">import.php</a></p>
</body>
</html>
