<?php

if (isset($_POST['cid']) && isset($_POST['team']) && isset($_POST['vm']) && isset($_POST['user'])) {
	$hintOpen = Array();
	$hintClose = Array();
	$HO = Array();
	$HC = Array();
	include 'connection.php';
	$cid = stripslashes(htmlspecialchars(htmlentities(trim(filter_var($_POST['cid'], FILTER_SANITIZE_STRING)))));
	$tem = stripslashes(htmlspecialchars(htmlentities(trim(filter_var($_POST['team'], FILTER_SANITIZE_STRING)))));
	$vm = stripslashes(htmlspecialchars(htmlentities(trim(filter_var($_POST['vm'], FILTER_SANITIZE_STRING)))));
	$user = stripslashes(htmlspecialchars(htmlentities(trim(filter_var($_POST['user'], FILTER_SANITIZE_STRING)))));
	$sql = "SELECT * FROM hint WHERE C_ID='$cid' AND TEAM='$tem' AND SYSTEM_NAME='$vm' AND HINT_STATUS='0'";
	$statusCodeRes = mysqli_query($connection, "SELECT * FROM hint WHERE C_ID='$cid' AND TEAM='$tem' AND SYSTEM_NAME='$vm' AND HINT_STATUS='0'");
	$openHint = mysqli_query($connection, "SELECT * FROM hint WHERE C_ID='$cid' AND TEAM='$tem' AND SYSTEM_NAME='$vm' AND HINT_STATUS='1'");
	$statusCode = mysqli_num_rows($statusCodeRes);
	$result = mysqli_query($connection, $sql);
	$num = 0;
	while ($Roww = mysqli_fetch_assoc($openHint)) {
		$hText = $Roww['HINT_TEXT'];
		$hintOpen[] = "$hText";
	}
	if ($statusCode > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$num++;
			$hintstatus = $row['HINT_STATUS'];
			$hintid = $row['HINT_ID'];
			$hinttext = $row['HINT_TEXT'];
			$hinttype = $row['HINT_TYPE'];
			$hintkey = $row['RANDOM'];
			$bigResult = mysqli_query($connection, "SELECT HINT_TYPE FROM hint WHERE C_ID='$cid' AND TEAM='$tem' AND SYSTEM_NAME='$vm' AND HINT_TYPE='big_hint'");
			$norResult = mysqli_query($connection, "SELECT HINT_TYPE FROM hint WHERE C_ID='$cid' AND TEAM='$tem' AND SYSTEM_NAME='$vm' AND HINT_TYPE='normal'");
			$bighint = mysqli_num_rows($bigResult);
			$normalhint = mysqli_num_rows($norResult);
			$totalhint = ($bighint * 2) + $normalhint;
			$singlePay = 200 / $totalhint;
			if ($hinttype == "big_hint") {
				$points = round($singlePay * 2, 0, PHP_ROUND_HALF_DOWN);
			} else {
				$points = round($singlePay, 0, PHP_ROUND_HALF_DOWN);
			}

			//hint 1
			if ($num == 1) {//choose first element
				$int1_sql = "SELECT SCORE,PENALTY FROM scoreboard WHERE TEAM='$tem'";
				$int1_result = mysqli_query($connection, $int1_sql);
				while ($int1_row = mysqli_fetch_assoc($int1_result)) {
					$score = $int1_row['SCORE'];
					$penalty = $int1_row['PENALTY'];
					$updatescore = $score - $points;
					$updatepenalty = $penalty + $points;
					$update_points_sql = mysqli_query($connection, "UPDATE scoreboard SET SCORE='$updatescore',PENALTY='$updatepenalty' WHERE TEAM=$tem");
					if ($update_points_sql) {
						$update_hint_status = mysqli_query($connection, "UPDATE hint SET HINT_STATUS='1' WHERE C_ID='$cid' AND TEAM='$tem' AND SYSTEM_NAME='$vm' AND HINT_ID='$hintid' AND RANDOM='$hintkey'");
						if ($update_hint_status) {
							$act_update = mysqli_query($connection, "UPDATE updater SET SCORE='1',HINT='1',ACTIVITY='1' WHERE TEAM='$tem'");
							if ($act_update) {
								include 'time.php';
								$log_sql = mysqli_query($connection, "INSERT INTO logger (DATE, TEAM, LOG) VALUES ('$fdate','$tem','[$user] Unlocked Hint [$vm] - [$hinttext] - [POINTS : -$points]')");
								if ($log_sql) {
									$hintOpen[] = "$hinttext";
								} else {
									echo "<h4>Hint : Error</h4>";
								}

							} else {
								echo "<h4>Hint : Error</h4>";
							}
						} else {
							echo "<h4>Hint : Error</h4>";
						}
					} else {
						echo "<h4>Hint : Error</h4>";
					}
				}
			} else {
				$hintClose[] = "Hint Locked (- $points)";
			}

		}
	} else {
		$hintClose[] = "No Further Hints";
	}

	$HO = implode("~#~", $hintOpen);
	$HC = implode("~#~", $hintClose);
	echo print_r($HO . "#~#" . $HC, true);

}
?>