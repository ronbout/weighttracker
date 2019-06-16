<?php
// wt_home_page.php
// 5-29-11 rlb
// home page of weight tracker program
// this will be what users see if they have not
// logged in to the program
// will be calendar, news feed, and message box

// build news and graph data
$news = wt_build_news(5);
$member = new Member;
$type = "home";
$graph = buildGraph($member, $type);
$mainMesg = 
"	<h3>Benefits of Registering</h3>
	<ul>
		<li>Track your weight with graphs and charts</li>
		<li>Set your goals and watch your progress</li>
		<li>Monitor your food and caloric intake</li>
		<li>Record your daily exercise output</li>
		<li>It's all free!</li>
	</ul>
";
echo '<div id="home_main">';
echo '
		<h2>Weight Tracker</h2>
		<h2>The free fitness tracking site</h2>
		<div id="home_mesg"><br>
		',$mainMesg,'
		</div>
     <div id="home_graph">
		<img src="member/wt_graph.php" usemap="#graph">
		<map name="graph">';
	$graph->mapHTML();

	
echo '
    </div><!-- end of home_graph -->
</div> <!-- end of home_main div -->';
echo '<div id="news_data">';
// news section of div main 
echo "<h3>Latest Health News</h3>";
echo "     <div id='div_news'><br>";
echo $news;
echo "     </div><!-- end of div_news -->";
echo "</div><!-- end of news_data -->";
?>