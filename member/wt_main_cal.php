<?php
// wt_main_cal.php
// 5-22-11 rlb
// main content code for member page in calendar mode
// diaplays calendar and tabbed info for selected day
// calendar must have been set up in wt_member_mode.php
// so that the css can be placed in the <HTML><HEAD> section

echo '<div id="calendar">';
// main_calendar section of div main - will hold calendar
echo $cal->display(); 
// legend for calendar
echo '
		<div id="cal_legend">
			<span id="goal_legend"></span>&nbsp; Goal&nbsp;&nbsp;&nbsp;
			<span id="weight_legend">Blue-Weight</span>&nbsp;&nbsp;&nbsp;
			<span id="current_legend">Bold-Active Day</span>
		</div>';
echo '
		<div id="mesg">
		<h4>',$mainMesg,'</h4>
		</div>';
	
echo '
</div> <!-- end of graphic div -->
<div id="data">';
// main_data section of div main - will usually hold tabbed weight/food/exercise data
require("member/wt_tab_links_cal.php");
echo '		<div id="tab_content">';
require("member/wt_tab_data_cal.php");
echo '		</div>
</div>';
?>