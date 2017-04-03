<?php
if (!isset($_SESSION)) {
	session_start();
}
if (isset($_SESSION['TEAM'])) {
	include 'connection.php';
	$viewlog_team = $_SESSION['TEAM'];
	$viewlog_sql = "SELECT * FROM (SELECT * FROM logger WHERE TEAM=$viewlog_team ORDER BY DATE DESC LIMIT 10) logger ORDER BY DATE ASC";
	$viewlog_result = mysqli_query($connection, $viewlog_sql);
	while ($viewlog_row = mysqli_fetch_assoc($viewlog_result)) {
		$viewlog_log = $viewlog_row['LOG'];
		echo "<p>=> $viewlog_log</p>";
	}
} else {
	//Display NULL Redirect
}
?>