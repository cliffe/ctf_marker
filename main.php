<?php 
session_start();
include 'template/connection.php';
if(!isset($_SESSION['USERNAME']) || !isset($_SESSION['TEAM']) || !isset($_SESSION['TYPE'])){
	header('location:index.php');
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimal-ui" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link href="https://fonts.googleapis.com/css?family=Iceland|Orbitron" rel="stylesheet"> 	
		<link href="css/secgen.css" rel="stylesheet" type="text/css">
		<link href="css/map.css" rel="stylesheet" type="text/css">
	<script>
		var user = '<?php echo $_SESSION['USERNAME'];?>';
		var tm1 = '<?php echo $_SESSION['TEAM'];?>';
	</script>
	<script src="js/dialog.js"></script>
	<script src="js/divcheck.js"></script>
	<script src="js/jquery_form.js"></script>
	<style>
		.modal-content{
			width:30%;
			height:auto;
		}
				
		::-webkit-scrollbar {
		  width: 6px;
		  height: 6px;
		}
		::-webkit-scrollbar-button {
		  width: 0px;
		  height: 0px;
		}
		::-webkit-scrollbar-thumb {
		  background: #ceb342;
		  border: 0px none #ffffff;
		  border-radius: 50px;
		}
		::-webkit-scrollbar-thumb:hover {
		  background: #faf434;
		}
		::-webkit-scrollbar-thumb:active {
		  background: #000000;
		}
		::-webkit-scrollbar-track {
		  background: #abd17d;
		  border: 0px none #ffffff;
		  border-radius: 90px;
		}
		::-webkit-scrollbar-track:hover {
		  background: #41a428;
		}
		::-webkit-scrollbar-track:active {
		  background: #ffff00;
		}
		::-webkit-scrollbar-corner {
		  background: transparent;
		}
		.hintok{
			color:green;
		}
		
		.hintclose{
			color:red;
		}

	</style>
</head>
<body id="main" style="background:url('images/bgadmin.png');">


		<div id="wrapper">
		
		<!-- Left Panel--->
		<div class="floating_panel left_panel">
			<h1>Submit Flags</h1>
			<input type="text" id="flag-check" placeholder="Enter Flag and Press Enter" />
			<script src="js/loginpopup.js"></script>
			<div class="status">
				<marquee><p id="flag-status-p">&ensp;</p></marquee>
			</div>
		</div>
		
		<!-- Right Panel-->
		<div class="floating_panel right_panel" id="right_panel">
			<?php include 'template/announce.php';?>
		</div>
		
		<div class="dropdown">
			<button class="dropbtn">View Teams</button>
			<div class="dropdown-content">
	<?php
		include 'template/connection.php';
		$center_panel_sql = "SELECT * FROM team";
		$center_panel_result = mysqli_query($connection, $center_panel_sql);
		while($center_row = mysqli_fetch_assoc($center_panel_result)){
			$team = $center_row['TEAMNAME'];
			$teamno = $center_row['TEAM'];
			if(isset($_SESSION['TEAM'])){
				$sess_team = $_SESSION['TEAM'];
				if($teamno == $sess_team){
					echo "<a href='main.php?team=$sess_team'>$team</a>";
				}else{
					echo "<a href='main.php?team=$teamno'>$team</a>";
				}
			}else{
				
			}		
		}
	?>
			</div>
		</div>

		<div class="systems">
		<?php
		include 'template/connection.php';
		if(isset($_GET['team'])){
			$getTeam= preg_replace('[^0-9]', '', urldecode(stripslashes(htmlspecialchars(htmlentities(trim($_GET['team']))))));
			$initTeamResult = mysqli_query($connection, "SELECT DISTINCT TEAM FROM secgenflag WHERE TEAM='".$getTeam."'");
			if(mysqli_num_rows($initTeamResult) != 0){
				if(mysqli_num_rows($initTeamResult) == 1){
					$initTeam = $getTeam;
				}else{
					header('location:main.php?team='.$_SESSION['TEAM']);
				}
			}else{
				header('location:html/initialise.php');	
			}	
					
		}else{
			header('location:main.php?team='.$_SESSION['TEAM']);
		}
		$ssql = "SELECT DISTINCT VM, IP FROM secgenflag WHERE TEAM='$initTeam'";
		$sresult = mysqli_query($connection, $ssql);
		$ii = 0;
			while($srow = mysqli_fetch_assoc($sresult)){
				$vm = $srow['VM'];
				$ip = $srow['IP'];
				$ii+=1;
				
			?>
			
			<div class="grouper">
				<div class="grouper_heading">
					<p class="vm"><?php echo $vm; ?></p>
					<p class="ip"><?php echo $ip; ?></p>
				</div>
				<div class="grouper_map" id="<?php echo "grouperId".$ii; ?>">
					<?php
						include 'template/connection.php';
						$sChooseMapCountSql = "SELECT FLAG_POINTS FROM secgenflag WHERE VM='$vm' AND TEAM='$initTeam'";
						$sChooseMapCountResult = mysqli_query($connection, $sChooseMapCountSql);
						$sChooseMapCount = mysqli_num_rows($sChooseMapCountResult);
						if($sChooseMapCount < 10){
							$sSelectMapDistinct = "SELECT DISTINCT W, H FROM secgen WHERE C_NO='$sChooseMapCount'";
							$sSelectMapDistinctResult = mysqli_query($connection, $sSelectMapDistinct);
							while($sSelectMRow = mysqli_fetch_assoc($sSelectMapDistinctResult)){
								$w = $sSelectMRow['W'];
								$h = $sSelectMRow['H'];
								?>
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.2" viewBox="0 0 <?php echo $w." ".$h?>">
							<g>
								<?php
	
								$sSelectMapSql = "SELECT * FROM secgen WHERE C_NO='$sChooseMapCount'";
								$sSelectMapResult = mysqli_query($connection, $sSelectMapSql);
								while($sChooseRow = mysqli_fetch_assoc($sSelectMapResult)){
									$sC_ID = $sChooseRow['C_ID'];
									$sC_COUNTRY= $sChooseRow['C_COUNTRY'];
									$sC_TITLE = $sChooseRow['C_TITLE'];
									$sC_D = $sChooseRow['C_D'];
									$secgenQuery = mysqli_query($connection, "SELECT STATUS FROM secgenflag WHERE C_ID='$sC_ID' AND TEAM='$initTeam' AND VM='$vm'");
									while($secgenStatusRow = mysqli_fetch_assoc($secgenQuery)){
										$secgenStatus = $secgenStatusRow['STATUS'];		
										$session_team = $_SESSION['TEAM'];								
										if($secgenStatus == 0){
											if($session_team == $initTeam){
												echo "<path id='$sC_ID' title='$sC_COUNTRY' d='$sC_D' onclick='Alert.menu(\"$sC_ID\",\"$vm\",\"$session_team\");'/>";
											}else{
												echo "<path id='$sC_ID' title='$sC_COUNTRY' d='$sC_D'/>";
											}
											
										}else{
											echo "<path fill='#abd17d' id='$sC_ID' title='$sC_COUNTRY' d='$sC_D'/>";										
										}	
									}	
								}
								
								?>
							</g>
						 </svg>
								<?php	
								}	
								mysqli_close($connection);	 																
																
						}else{
							echo "<p class='map_init' id='tssst'>Map Not Initialised</p>";
						}
					?>	
				</div>
			</div>
			<?php
			}
		?>
		</div>
	</div>
<!--Dialog Code -->
<!-- Left Menu -->
<div id="info_menu" onclick="openNav()">
<span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;</span>
</div>
<div id="mySidenav" class="sidenav">
  	<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  	<?php
  	if(isset($_SESSION['USERNAME']) && isset($_SESSION['TEAM']))
  	{
  	?>
    <a href="template/logout.php" id="main_logout">Logout</a>
	<div class="scores side_item">
		<div class="side_heading">
			<h1>Score Board</h1>
		</div>
		<span id="div1_inner_body_1"><?php include 'template/userscoreboard.php'; ?></span>
		<span id="div1_inner_body_2"><?php include 'template/teamscoreboard.php'; ?></span>
	</div>
	<div class="logs side_item">
		<div class="side_heading">
			<h1>Team Activity</h1>
		</div>
		<div class="team_logs" id="team_logs_scroll">
			<div id="div2_inner">
				<div id="div2_inner_border">
					<?php include 'template/viewlog.php'; ?>
				</div>	
			</div>
		</div>
	</div>
	<div class="chat side_item">
		<div class="side_heading">
			<h1>Team Chat</h1>
		</div>
		<div class="chat_history" id="chat_history_scroll">
			<div id="div3_inner">
				<div id="div3_inner_chat_history">
					<?php include 'template/viewchat.php'; ?>
				</div>	
			</div>
		</div>	
		<div class="chat_input">
			<input id="div3_chat_input" type="text" placeholder="Enter Message and Press Enter" />
		</div>
	</div>
	<?php
	}
	?>
</div>

<div id="noooo"></div>

<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h3 id="dialog-id" class="modal_info"></h3>
      <h3 id="dialog-title" class="modal_info"></h3>
    </div>
    <div class="modal-body">
		<div id="moBody">
			<h3 class="hintclose">Please wait...</h3>
		</div>
	   <div id="dialog_flag_button">
	   		<button type="fsubmit" id="fsubmit">Get Hint</button>
	   </div></br>
	   <div id="moBodyLocked">
	   	
	   </div>
    </div>
    <div class="modal-footer">
      <h3 id="flag_hint">--</h3>
    </div>
  </div>

</div>
<script src="js/dialog.js"></script>
<script>
	function alert(){
	var modal = document.getElementById('myModal');
	
	var span = document.getElementsByClassName("close")[0];
	
	this.menu = function(cid,vm,teams) {
	    modal.style.display = "block";
	    document.getElementById('dialog-title').innerHTML = vm;
	    document.getElementById('dialog-id').innerHTML = cid;
	    //-------------------------------------------------------------------	
	    if(checkSessionStorage() != "undefined"){
    		if (sessionStorage.getItem(cid+"-"+vm) == null){     	
				$.ajax({
					method: "POST",
					url: "template/viewhint.php",
					data: {cids: cid,team:teams,vms:vm},
					success: function(status){
						sessionStorage.setItem(cid+"-"+vm,status);	
						insertHint(status);			
					}	
				});
			}else{
				var status = sessionStorage.getItem(cid+"-"+vm);	
				insertHint(status);	
			}
	    }else{
	    	$.ajax({
				method: "POST",
				url: "template/viewhint.php",
				data: {cids: cid,team:teams,vms:vm},
				success: function(status){
					insertHint(status);					
				}	
			});
	    }	
	    
	};
	
	function insertHint(value){
		var status = value;	
		$('#moBody').empty();
		$('#moBodyLocked').empty();
		var OCSplit = status.split("#~#");
		for(var i=0; i<OCSplit.length;i++){
			var split = OCSplit[i].split("~#~");
			var cn = 0;
			for(var e=0; e<split.length;e++){
				if(i == OCSplit.length-1){		
					if(e == 0){
						var str = split[0];
						if(str == ""){
							document.getElementById("fsubmit").innerHTML = "No Further Hints";
						}else{
							var res = str.replace("Hint Locked","");
							document.getElementById("fsubmit").innerHTML = "Unlock Hint "+res;
						}

					}
					var addh3 = document.createElement("h3");
					var text = document.createTextNode(split[e]);
					addh3.appendChild(text);				
					addh3.setAttribute("class","hintclose");
					document.getElementById("moBodyLocked").appendChild(addh3);		
				}else{
					cn++;
					var addh3 = document.createElement("h3");
					if(split[e] == ""){
						var text = document.createTextNode(split[e]);	
					}else{
						var text = document.createTextNode(cn+") "+split[e]);	
					}
					addh3.appendChild(text);
					addh3.setAttribute("class","hintok");
					document.getElementById("moBody").appendChild(addh3);	
				}			
					
			}
		}
	}
	
	function checkSessionStorage(){
   		return window.sessionStorage;
	}

	span.onclick = function() {
	    modal.style.display = "none";
    	var text = document.getElementById('flag_hint').innerText;
	    document.getElementById('flag_hint').innerHTML = " ";
	};
	
	window.onclick = function(event) {
	    if (event.target == modal) {
	        modal.style.display = "none";
	        var text = document.getElementById('flag_hint').innerText;
	        document.getElementById('flag_hint').innerHTML = " ";
	    }
	};
}

var Alert = new alert();
</script>
<script src="js/main.js"></script>
<script src="noti/notify.js"></script>
<script src="noti/notify.min.js"></script>
</body>
</html>	