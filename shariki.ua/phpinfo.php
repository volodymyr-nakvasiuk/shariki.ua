<?php
	$hacker = true;
	
	if (isset($_GET['dev'])){
		if ($_GET['dev']=='on'){ 
			$hacker = false;
		}
	}
	
	if ($hacker){
		echo "<center><h1>You are Hacker!!!</h1><h2>Access denied.</h2></center>";
	}
	else {
		phpinfo();
	}
?>