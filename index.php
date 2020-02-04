<?php
$dbhost = "obfiscated";
$dbuser = "obfiscated";
$dbpass = "obfiscated";
$dbname = "obfiscated";

//connect to db
$connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

//helper function to quickly make a query
function query($query) 
{
	global $connect;
	$result = mysqli_query($connect, $query);
	return $result;
	
}

//issues a query and returns a single value
function getSingle($query)
{
	$result = query($query);
	$row = mysqli_fetch_row($result);
	return $row[0];
}

//handle input when user hits 'tweet' button
if(isset($_REQUEST['tweet'])) 
{
	$tweet = $_REQUEST['tweet'];
	$ip = mysqli_real_escape_string($connect, $_SERVER['REMOTE_ADDR']);
	$uid = getSingle("select uid from twitUsers where ip = '".$ip."'");
	
	//create user id if it does not exist
	if(!$uid)
	{
		query("insert into twitUsers(ip) values ('$ip')");
	}
	$date = Date("Y-m-d H:i:s");
	query("insert into twitTweets(uid, post, date) values ('$uid', '$tweet', '$date')");
	echo "$tweet, $ip";
}

//this is the tweet input field (for users making tweets).
echo '
<form method="POST" action=index.php>
<textarea name=tweet></textarea>
<input type=submit value="Tweet">
</form>
';

$result = query("select * from twitTweets order by date desc");
echo "<table>";
while ($row = mysqli_fetch_assoc($result)) 
{
	$uid = $row['uid'];
	$post = htmlspecialchars($row['post']);
	$date = $row['date'];
	echo "
	<tr><td>$uid</td><td>$post</td><td>$date</td></tr>
	";
}
echo "</table>";