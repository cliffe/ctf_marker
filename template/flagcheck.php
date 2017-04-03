<?php

if(isset($_POST['tm']) && isset($_POST['fg']) && isset($_POST['username'])){
     $flag = $_POST['fg'];
	 $team = $_POST['tm'];
	 $user = $_POST['username'];
	
	 include 'connection.php';	
	 if(!empty($flag)){
	 	//time check
		 	$state = mysqli_query($connection, "SELECT value FROM options WHERE name='END_TIME'");
			foreach(mysqli_fetch_assoc($state) as $time){
				$gametime = $time;
			}
			$timezone = 'Europe/London'; 
			$date = new DateTime('now', new DateTimeZone($timezone));
			$localtime = $date->format('Y-m-d H:i:s');
			if(strtotime($gametime)>strtotime($localtime)){
			 	$fg = htmlspecialchars(htmlentities(trim(filter_var($flag,FILTER_SANITIZE_STRING))));
				$result = mysqli_query($connection,"SELECT * FROM secgenflag WHERE FLAG='$fg' AND TEAM='$team'");
				$num = mysqli_num_rows($result);
				include 'time.php';
				if($num == 1)
				{
				 //-----------------------------------------------
				  while($row = mysqli_fetch_assoc($result)){
					  	 $ans = $row['FLAG'];
						 //$point = $row['FLAG_POINTS'];
						 $cid = $row['C_ID'];
						 $stat = $row['STATUS'];
						 $vm = $row['VM'];
						 if($stat == 0){
							 if($fg == $ans){
							 	//update flag status
							 	$suc = 1;
							 	$update_status_sql = mysqli_query($connection,"UPDATE secgenflag SET STATUS='$suc' WHERE FLAG='$fg' AND TEAM='$team'");
								if($update_status_sql){
									$sSelectStatus = "SELECT HINT_ID, HINT_STATUS, HINT_TYPE, HINT_TEXT FROM hint WHERE C_ID='$cid' AND TEAM='$team' AND SYSTEM_NAME='$vm'";
									$sSelectStatusResult = mysqli_query($connection, $sSelectStatus);
									$bigResult = mysqli_query($connection, "SELECT HINT_TYPE FROM hint WHERE C_ID='$cid' AND TEAM='$team' AND SYSTEM_NAME='$vm' AND HINT_TYPE='big_hint'");
									$norResult = mysqli_query($connection, "SELECT HINT_TYPE FROM hint WHERE C_ID='$cid' AND TEAM='$team' AND SYSTEM_NAME='$vm' AND HINT_TYPE='normal'");
									$bighint = mysqli_num_rows($bigResult);
									$normalhint = mysqli_num_rows($norResult);
									$totalhint = ($bighint * 2)+$normalhint;
									$singlePay = 200 / $totalhint;
									
									$calPoints = mysqli_query($connection, "SELECT HINT_TYPE FROM hint WHERE C_ID='$cid' AND SYSTEM_NAME='$vm' AND TEAM='$team' AND HINT_STATUS='1'");
									$points = 0;
									while($cod = mysqli_fetch_assoc($calPoints)){
										if($cod['HINT_TYPE'] == "big_hint"){
											$points+=($singlePay * 2);
										}else if($cod['HINT_TYPE'] == "normal"){
											$points+= $singlePay;
										}
									}
									$finalPoints = 250 - round($points,0,PHP_ROUND_HALF_DOWN);
									$flag_marker_scb_sql = mysqli_query($connection,"SELECT SCORE FROM scoreboard WHERE TEAM='$team'");
									while($flag_marker_scb_row = mysqli_fetch_assoc($flag_marker_scb_sql)){
										$flag_scoreboard_points = $flag_marker_scb_row['SCORE'];
										$final_grade = $flag_scoreboard_points + $finalPoints;
										$update_points_sql = mysqli_query($connection,"UPDATE scoreboard SET SCORE='$final_grade' WHERE TEAM=$team");
										if($update_points_sql){
											$log_sql = mysqli_query($connection, "INSERT INTO logger (DATE, TEAM, LOG) VALUES ('$fdate','$team','[$user][$vm] Captured the Flag - [$flag] - [POINTS : $finalPoints]')");
											if($log_sql){								
												$revealHint = mysqli_query($connection, "SELECT HINT_ID FROM hint WHERE TEAM='$team' AND SYSTEM_NAME='$vm' AND C_ID='$cid'");
												$iN = 0;
												if(mysqli_num_rows($revealHint) > 0){
													while($hID = mysqli_fetch_assoc($revealHint)){
														$id = $hID['HINT_ID'];
														mysqli_query($connection, "UPDATE hint SET HINT_STATUS='1' WHERE HINT_ID='$id' AND TEAM='$team'");
														if(mysqli_affected_rows($connection) > 1){
															$iN++;
														}
													}
												}
												$act_update = mysqli_query($connection, "UPDATE updater SET ACTIVITY='1', FLAG='1',HINT='1',HINT_UPDATE='all' WHERE TEAM='$team'");
												if($act_update){
													$bonus_sql = mysqli_query($connection, "INSERT INTO logger (DATE, TEAM, LOG) VALUES ('$fdate','$team','[BONUS] Unlocked [$iN]Hints for other challenges')");
													if($bonus_sql){
														echo "<p style='color:#d4ff00;'>Your key is Correct</p>"; 
													
														$flag_log_query = mysqli_query($connection, $log_sql);
														if(!$flag_log_query){
															$log_sql_failed = "INSERT INTO report (DATE,LOG) VALUES ('$fdate','Flag check logger failed to log for team $team')";
															$flag_log_query_failed = mysqli_query($connection, $log_sql_failed);
														}
														mysqli_close($connection);
													}else{
														echo "<p style='color:orange;'>Technical Error [2002]</p>";
													}
		
												}else{
													echo "<p style='color:orange;'>Technical Error [2001]</p>";
												}
		
											}
										}
									} 
								}else{
									$log_sql_error = "INSERT INTO report (DATE,  LOG) VALUES ('$fdate','Flag Status Update Failed - $cid')";
									mysqli_query($connection, $log_sql_error);
								}
							 	//end
							 }else{
								echo "<p style='color:orange;'>Your key is Incorrent</p>"; 
								$log_sql = mysqli_query($connection,"INSERT INTO logger (DATE, TEAM, LOG) VALUES ('$fdate','$team','Incorrect Flag Entered - [$fg]')");
								mysqli_query($connection, "UPDATE updater SET ACTIVITY='1' WHERE TEAM='$team'");
							 } 
						}else{
							echo "<p style='color:orange;'>Flag already captured</p>";
						}	
				  }
				 
				 //-----------------------------------------------
				}else{
					echo "<p style='color:orange;'>Incorrect Flag</p>";
					mysqli_query($connection,"INSERT INTO logger (DATE, TEAM, LOG) VALUES ('$fdate','$team','Incorrect Flag Entered - [$fg]')");
					mysqli_query($connection, "UPDATE updater SET ACTIVITY='1' WHERE TEAM='$team'");
				}
		//time check end 
		}else{
			echo "<p style='color:orange;'>Game Paused</p>";
		}	
	 }else{
	 	echo "<p style='color:orange;'>Your string is empty</p>";
	 }
	
}

?>