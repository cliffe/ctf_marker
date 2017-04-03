		$(document).ready(function(){
			$(".map__image").draggable();
		});
		//Chat
		$(document).ready(function(){
   			$("#chat").click(function(){
        		$("#info_chat").slideToggle("slow");
    		});
		});
		
		$(document).ready(function(){
			$('#fsubmit').click(function(){
				gethint();
			});
		});
		var gethint = function(){
			var coun_id = $('#dialog-id').text();
			var system = $('#dialog-title').text();
			$.ajax({
				method: "POST",
				url: "template/gethint.php",
				data: {cid: coun_id, team: tm1, vm: system,user:user},
				success: function(status){		
					if(checkSessionStorage() != "undefined"){
						sessionStorage.setItem(coun_id+"-"+system,status);	
					}	
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
			});
		};	
		//chat
		$(document).ready(function(){
			$('#div3_chat_input').keypress(function(event){
				var key = (event.keyCode ? event.keyCode : event.which);
				if(key == 13){
					var chat = $('#div3_chat_input').val();
					$.ajax({
						method: "POST",
						url: "template/chat.php",
						async:false,
						data: {team: tm1, chat: chat, user: user},
						success: function(status){
							$('#div3_chat_input').val('');
						}
					});
				}
			});
		});	
		
		function checkSessionStorage(){
			return window.sessionStorage;
		}