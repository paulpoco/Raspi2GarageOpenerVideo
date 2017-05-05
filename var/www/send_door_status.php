<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
exec ( "gpio read 2", $status ); //read the magnetic switch
if ( $status[0] == 0 ) {
	$new_data = "Door Closed";
} else {
	$new_data = "Door Open";
}
//Delete this section if you are not using a motion sensor of gpio read 3
exec ( "gpio read 3", $motion );
if ( $motion[0] == 0 ) {
        $new_data2 = "No Motion";
} else {
        $new_data2 = "Motion Detected";
}
//Delete this section if you are not using a motion sensor of gpio read 3
//remove $new_data2 if you are not using a motion sensor of gpio read 3
echo "data:<h1><mark><strong>[$new_data]&#9743;&#9743;&#9743;&#9743;&#9743;[$new_data2]</strong></mark></h1>\n\n";
ob_flush();
?>
