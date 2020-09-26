<?php
if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && strpos($_SERVER['HTTP_USER_AGENT'], 'Bot') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'bot') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'ELB-HealthChecker') === false)
{
	$db = new mysqli("localhost", "my_user", "my_password", "tracking") or die("");

	$ref = (strlen($_SERVER['HTTP_REFERER']) > 29) ? substr($_SERVER['HTTP_REFERER'], 0, 27) . '...' : $_SERVER['HTTP_REFERER'];
	$url = (strlen(strtok($_SERVER["REQUEST_URI"], '?')) > 29) ? substr(strtok($_SERVER["REQUEST_URI"], '?'), 0, 27) . '...' : strtok($_SERVER["REQUEST_URI"], '?');
	$query = (strlen($_SERVER['QUERY_STRING']) > 29) ? substr($_SERVER['QUERY_STRING'], 0, 27) . '...' : $_SERVER['QUERY_STRING'];
	$date = date("Y-m-d H:i:s");

	$browser = "question";
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) { $browser = "internet-explorer"; }
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false) { $browser = "opera"; }
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false) { $browser = "chrome"; }
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) { $browser = "firefox"; }
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false) { $browser = "safari"; }
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') !== false) { $browser = "edge"; }
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false) { $browser = "mobile"; }

	if($stmt = $db->prepare("INSERT INTO log (date, ip, url, query, ref, browser) VALUES (?, ?, ?, ?, ?, ?)"))
	{
		$stmt->bind_param("ssssss", $date, $_SERVER['REMOTE_ADDR'], $url, $query, $ref, $browser);
		$stmt->execute();
		$stmt->close();
	}

	$db -> close();
}
?>
