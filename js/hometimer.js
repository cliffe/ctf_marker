var x = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
    var countDownDate = new Date(document.getElementById("sttimer").value).getTime();
    // Find the distance between now an the count down date
    var distance = countDownDate - now;
    
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    // Output the result in an element with id="demo"
    document.getElementById("ttimer").innerHTML = (days<10?'0':' ')+days + " <span class='timer_words'>days</span> " + (hours<10?'0':' ')+hours + " <span class='timer_words'>hrs</span> "
    + (minutes<10?'0':' ')+minutes + " <span class='timer_words'>min</span> " + (seconds<10?'0':' ')+seconds + " <span class='timer_words'>sec</span> ";
    
    // If the count down is over, write some text 
    if (distance < 0) {
        clearInterval(x);
        document.getElementById("ttimer").innerHTML = "GAME STARTED";
    }
}, 1000);