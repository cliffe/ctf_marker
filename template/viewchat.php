<?php
	include 'connection.php'; 
	if(!isset($_SESSION)){
		session_start();
	}
	echo "<table>";
	if(isset($_SESSION['TEAM'])){
		$chatlog_team = $_SESSION['TEAM'];
		$chatlog_sql = "SELECT * FROM (SELECT * FROM `chat` WHERE TEAM='$chatlog_team' ORDER BY DATE DESC LIMIT 10) chat ORDER BY DATE ASC";
		$chatlog_result = mysqli_query($connection, $chatlog_sql);
		while($chatlog_row = mysqli_fetch_assoc($chatlog_result)){
			$chatlog_user = $chatlog_row['USERNAME'];
			$chatlog_log = $chatlog_row['CHAT'];
			echo "<tr><td class='cha_user'>$chatlog_user</td><td class='cha_val'>$chatlog_log</td></tr>";
		}
		echo "</table>";
	}else{
		//header('location:../main.php');
		//Display NULL Redirect
	}
?>