<?php
// wt_main_news.php
// main content code for news mode of 
// member page in weight tracker
// displays cal section from cal mode, but
// replaces data section with news feed section
// news string was set up in wt_member_mode.php
// using wt_build_news().
// called from wt_member.php

echo '<div id="calendar">';
// main_calendar section of div main - will hold calendar
echo $cal->display(); 
// legend for calendar
echo '
		<div id="cal_legend">
			<span id="goal_legend"></span>&nbsp; Goal&nbsp;&nbsp;&nbsp;
			<span id="weight_legend">Blue-Weight</span>&nbsp;&nbsp;&nbsp;
			<span id="current_legend">Bold-Active Day</span>
		</div><!-- end of cal_legend -->';
echo '
		<div id="mesg">
		<h4>',$mainMesg,'</h4>
		</div><!-- end of mesg -->';
	
echo '
</div> <!-- end of calendar div -->';
echo '<div id="news_data">';
// news section of div main 
echo "<h3>Latest Health News</h3>";
echo "     <div id='div_news'><br>";
echo $news;
echo "     </div><!-- end of div_news -->";
echo "</div><!-- end of news_data -->";


?>