<?php
// wt_news_funcs.php
// 6-29-11 rlb
// contains function wt_build_news() for
// building a returns a news string built from
// rss feeds.  Creates the string from rss feeds
// if health.rss is not found, or if the timestamp
// in the file is over 6 hours old


function wt_build_news($itemLimit)
{
define("UPDATE_HOURS", 2);
$rssFilename = "rss/health.rss";
$urls[] = "http://www.after50health.com/feed";
$urls[] = "http://z.about.com/6/g/nutrition/b/rss2.xml";
$urls[] = "http://rss.msnbc.msn.com/id/13594277/device/rss/rss.xml";
$urls[] = "http://feeds.ezinearticles.com/category/Health-and-Fitness:Weight-Loss.xml";
$urls[] = "http://rssfeeds.usatoday.com/Usatodaycom-Weightloss";
$urls[] = "http://www.weightlossrss.net/?feed=rss2";
$urls[] = "http://rss.msnbc.msn.com/id/3034510/device/rss/rss.xml";
$urls[] = "http://topics.nytimes.com/topics/reference/timestopics/subjects/e/exercise/index.html?rss=1";
$urls[] = "http://www.fitness.com/generated/rss_exercises.xml";
$urls[] = "http://feeds.health.com/health/eating?format=xml";
$urls[] = "http://rss.allrecipes.com/daily.aspx?hubID=84";
$inFile = @file_get_contents($rssFilename);
$writeFlg = false;

$inFile = explode("|**|",$inFile,2);
// rebuild every 2 hours
if (!$inFile || (strtotime("now") - $inFile[0] > (UPDATE_HOURS * 60 * 60)))
{
	$news = "";
	require("magpie/rss_fetch.inc");
	shuffle($urls);
	$itemCnt = 0;
	foreach($urls as $url)
	{
		$rss = @fetch_rss($url);
		if ($rss)
		{
			$titleFlg = true;
			for ($i = 0; $i < min(5,sizeof($rss->items)); $i++)
			{
				$item = $rss->items[$i];
				if (!isset($item['pubdate'])) 
					$pubdate = date("Y-m-d");
				else
				{
					$pDate = explode(" ",trim($item['pubdate']));
					$pubdate = "";
					for ($k=0; $k < 4; $k++)
					{
						if (isset($pDate[$k]))
							$pubdate .= $pDate[$k]." ";
					}
				}
				if (isset($item['title']) && isset($item['description']) && isset($item['link'])
					&& (strtotime("now") - strtotime($pubdate)) <= (8 * 24 * 60 * 60)) 
				{
					if ($titleFlg)
					{
						$news .= "<h3>".$rss->channel['title']."</h3>";
						$titleFlg = false;
					}
					$news .= "<h3>".$item['title']."</h3>";
					$news .= "<p>".$item['description']."</p>";
					//if (isset($item['pubdate'])) $news .= "<p>".$item['pubdate']."</p>";
					$news .= "<p>Full story: <a href='".$item['link']."'>".$item['link']."</a></p>";
					$itemCnt += 1;
					if ($itemCnt >= $itemLimit) break 2;
				}
			}
		}
		else
		{
			$news = $inFile[1];
		}
	}
	$writeFlg = true;
}
else
{
	$news = $inFile[1];
}
$outFile[] = strtotime("now")."|**|";
$outFile[] = $news;
if ($writeFlg) file_put_contents($rssFilename,$outFile);
return $news;
}
?>