<?php
        if(isset($_GET['trigger']) && $_GET['trigger'] == 1) {
                error_reporting(E_ALL);
                exec('gpio write 0 0'); // Output GPIO 0 as 0  replace with what you use
                usleep(1000000);
                exec('gpio write 0 1'); // Output GPIO 0 as 1  replace with what you use
        }
?>
<html>
<div id="serverData">Here is where the server sent data will appear</div>
<script type="text/javascript">
//functions here
//check for browser support
if(typeof(EventSource)!=="undefined") {
        //create an object, passing it the name and location of the server side script
        var eSource = new EventSource("send_door_status.php");
        //detect message receipt
        eSource.onmessage = function(event) {
                //write the received data to the page
                document.getElementById("serverData").innerHTML = event.data;
        };
}
else {
        document.getElementById("serverData").innerHTML="Whoops! Your browser doesn't receive server-sent events.";
}
</script>
        <head>
		<link rel="apple-touch-icon" href="apple-touch-icon-iphone.png" />
                <link rel="apple-touch-icon" sizes="72x72" href="apple-touch-icon-ipad.png" />
                <link rel="apple-touch-icon" sizes="114x114" href="apple-touch-icon-iphone-retina-display.png" />
                <link rel="stylesheet" href="/css/style.css" type="text/css">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
                <script type="text/javascript" src="/js/script.js"></script>
        </head>
        <body>
                <!--Replace RaspiIP with your IP-->
                <img style="-webkit-user-select: none" src="http://RaspiIP:8081/" width="980" height="735"> 
                <div class='awrap'>
              <a href='/?trigger=1'></a>
                </div>
        </body>
</html>
