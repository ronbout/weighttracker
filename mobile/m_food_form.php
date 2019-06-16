<?php
// m_food_form.php
// 2-4-11 rlb
// html form for searching food items


?>
<div id="div_search_form">
	<form name="search_form" action="m_food_lookup.php" method="post">
	<input id="search_words" type="text" name="search_words" placeholder="Enter food keywords" value="<?php echo $search_words;?>">
	<input id="search_button" type="image" name="search_but" src="search.png" alt="search">
	</form>
</div><!-- end of div_search_form -->