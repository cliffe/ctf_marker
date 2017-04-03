var capture = 0;
setInterval(function () {
	checkS();
}, 1000);	

function chatscroll() {
	$("#chat_history_scroll").animate({ scrollTop: $("#chat_history_scroll")[0].scrollHeight}, 1000);

}

function actiscroll(){
  $("#team_logs_scroll").animate({ scrollTop: $("#team_logs_scroll")[0].scrollHeight}, 1000);
}
//onload scroll to bottom 
chatscroll();
actiscroll();

$(document).ready(function(){
	$('#flag-check').keypress(function(event){
		var key = (event.keyCode ? event.keyCode : event.which);
		if(key == 13){
			flagCheck();
		};
	});
});

var flagCheck = function(){
	var fg = $('#flag-check').val();
	$.ajax({
		method: "POST",
		url: "template/flagcheck.php",
		data: {fg: fg, tm: tm1,username:user},
		success: function(status){
			$('#flag-status-p').html(status);
			$('#flag-check').val('');											
		}	
	});
};
var checkS = function(){
	$.ajax({
		method: "POST",
		url: "template/sidestatus.php",
		data: {status:"all", team:tm1, username:user},
		success: function(status){
			var statC = status.split(";");
			var ln = statC.length;
			for(var i=0; i < ln; i++){
				var ch = statC[i];
				if(ch == "CHAT"){
					$("#div3_inner_chat_history").load("template/viewchat.php").click(chatscroll());
					$.ajax({
						method: "POST",
						url: "template/SideStatusUpdate.php",
						data: {update:"CHAT", team:tm1, username:user},
						success: function(status){
							if(status == "Success"){
								$("#div3_inner_chat_history").click(chatscroll());
						 	 	$.notify("New Message Received",{position:"bottom center", className:"success"});
							}else{
								$.notify("Report Admin [ERROR 001]",{position:"bottom center", className:"error"});
							}
						}	
					});
				}
				else if(ch == "ACTIVITY"){
					$("#div2_inner_border").load("template/viewlog.php").click(actiscroll());
					$.ajax({
						method: "POST",
						url: "template/SideStatusUpdate.php",
						data: {update:"ACTIVITY", team:tm1, username:user},
						success: function(status){
							  if(status == "Success"){
								  $("#div2_inner_border").click(actiscroll());	
								  $.notify("New Activity Update",{position:"bottom center", className:"info"});
							  }else if(status == "Failed"){
								  $.notify("Report Admin [ERROR 002]",{position:"bottom center", className:"error"});
							  }  
						}	
					});
				}
				else if(ch == "SCORE"){
					$("#div1_inner_body_1").load("template/userscoreboard.php");
					$("#div1_inner_body_2").load("template/teamscoreboard.php");
					$.ajax({
						method: "POST",
						url: "template/SideStatusUpdate.php",
						data: {update:"SCORE", team:tm1, username:user},
						success: function(status){
							if(status == "Success"){
						  		$.notify("Score Board Updated",{position:"bottom center", className:"info"});		
						  	}else{
								  $.notify("Report Admin [ERROR 003]",{position:"bottom center", className:"error"});
						    }	
						}	
					});
				}
				else if(ch == "ANNOUNCE"){
					$("#right_panel").load("template/announce.php");
					$.ajax({
						method: "POST",
						url: "template/SideStatusUpdate.php",
						data: {update:"ANNOUNCE", team:tm1, username:user},
						success: function(status){
							if(status == "Success"){
						  		$.notify("New Announcement",{position:"bottom center", className:"warn"});	
						  	}else{
								  $.notify("Report Admin [ERROR 004]",{position:"bottom center", className:"error"});
						    }
						}	
					});
				}
				else if(ch == "FLAG"){
					$.ajax({
						method: "POST",
						url: "template/SideStatusUpdate.php",
						data: {update:"FLAG", team:tm1, username:user},
						success: function(status){
							if(status == "Success"){
								$.notify("Flag Captured",{position:"bottom center", className:"success"});
								setTimeout(function(){
		 							location.reload();
		 						}, 3000);							
						  			
						  	}else{
								  $.notify("Report Admin [ERROR 005]",{position:"bottom center", className:"error"});
						    }	
						}	
					});
				}
				else if(ch == "TIME"){
					$("#right_panel").load("template/announce.php");
					$.ajax({
						method: "POST",
						url: "template/SideStatusUpdate.php",
						data: {update:"TIME", team:tm1, username:user},
						success: function(status){
							if(status == "Success"){
						  		$.notify("Timer Updated",{position:"bottom center", className:"success"});	
						  	}else{
								  $.notify("Report Admin [ERROR 006]",{position:"bottom center", className:"error"});
						    }	
						}	
					});
				}
				else if(ch == "HINT"){
					$.ajax({
						method: "POST",
						url: "template/SideStatusUpdate.php",
						data: {update:"HINT", team:tm1, username:user},
						success: function(status){
							if(status == "Success"){
						  		$.notify("HINT Disclosed",{position:"bottom center", className:"success"});	
						  	}else if(status == "Failed"){
								  $.notify("Report Admin [ERROR 007]",{position:"bottom center", className:"error"});
						    }else if(status == "all"){
						    	$.notify("Bonus : Hints Unlocked",{position:"bottom center", className:"success"});
						    	sessionStorage.clear();
						    }else{
						    	$.notify("HINT Disclosed",{position:"bottom center", className:"success"});	
						    	sessionStorage.removeItem(status);
						    }
						}	
					});
				}
				
			}				
		}	
	});
};
