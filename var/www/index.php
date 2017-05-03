<?php
        if(isset($_GET['trigger']) && $_GET['trigger'] == 1) {
                error_reporting(E_ALL);
                exec('gpio write 0 0'); // Output GPIO 0 as 0  replace with what you use
                usleep(1000000);
                exec('gpio write 0 1'); // Output GPIO 0 as 1  replace with what you use
        }
exec ( "gpio read 2", $status ); // Read GPIO 2   replace with what you use

if ( $status[0] == 0 ) {
?>
<h1><mark>Door Closed</mark></h1>
<?php
} else {
?>
<h1><mark>Door Open</mark></h1><p>
<?php
}
?>

<!DOCTYPE html>
<html>
        <head>
                <title>Garage Opener</title>
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
