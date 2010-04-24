<?php	
	$Include_Dir = $_SERVER["DOCUMENT_ROOT"]. "/includes/";
	$containing_dir = basename(dirname(__FILE__));
	require_once($Include_Dir. "concertreservations_dbopen.php");
	require_once($Include_Dir. "magpierss/rss_fetch.inc"); 
	define('MAGPIE_CACHE_AGE', 604800);
	function unurlize($string){
		$string=str_replace("--","%%",$string); 
		$string=str_replace("-"," ",$string); 
		$string=str_replace("%%","-",$string); 
		$string=urldecode($string);
		return $string;
	}
	function trackingUrl($string){
		$string = str_replace('/','-',$string);
		$string = str_replace(' ','_',$string);
		$string = urlencode($string);
		return $string;
	}
	function sanitize($value){
	   $value = trim($value);
	   if(get_magic_quotes_gpc())
	   {
		  $value = stripslashes($value);
	   }
	   if(!is_numeric($value)) // only need to do this part for strings
	   {
		  $text = @mysql_real_escape_string($value);
		  if($text === FALSE)  // we must not be connected to mysql, so....
		  {
			 $text = mysql_escape_string($value);
		  }
		  $value = "$text";
	   }
	   return($value);
	}
	$q = $_POST['query'];
	$q = sanitize($q);
	if ($q == "''") { $q=str_replace("'","",$q);} 
	$get_genres_rss=mysql_query("SELECT DISTINCT category from adlinks where type='rss' and active=TRUE order by category");
	$get_genres_nonrss=mysql_query("SELECT DISTINCT category from adlinks where active=TRUE and type<>'rss' and category<>'MLB' and category<>'Review' and type<>'embed' and category<>'' order by category");
	$genres_rss = array();
	$genres_nonrss = array();
		while($genres_row_rss=mysql_fetch_row($get_genres_rss)) { 
			$genres_rss[]=$genres_row_rss[0];
		}
		while($genres_row_nonrss=mysql_fetch_row($get_genres_nonrss)) { 
			$genres_nonrss[]=$genres_row_nonrss[0];
		}
	//$genres_rss[]="All";
	$genres = array_merge($genres_rss, $genres_nonrss);
	if (empty($urlVars) && $q==""){$artist="";$genre="";$pagenumber="";$other="";} // if there are no url variables, we manually set them to empty vars
	else{ // there are url vars, so we'll set the vars that exist and set the ones that don't exist to empty
		if (array_key_exists('artist',$urlVars)){ $artisturl=$urlVars['artist']; $artist=unurlize($artisturl); } else { $artist=""; }
		if (array_key_exists('genre',$urlVars)){ $genreurl=$urlVars['genre']; $genre=unurlize($genreurl); } else { $genre=""; }
		if (array_key_exists('other',$urlVars)){ $other=$urlVars['other']; } else { $other=""; }
		if (array_key_exists('affiliate',$urlVars)){ $affiliate=$urlVars['affiliate']; } else { $affiliate=""; }
		if (array_key_exists(':number1',$urlVars)){ $pagenumber=$urlVars[':number1']; }
		// after all that, i still don't know if some of these are unset... so:
		if (!isset($artist)) { $artist=""; }
		if (!isset($genre)) { $genre=""; }
		if (!isset($pagenumber)) { $pagenumber="1"; }
		if (!isset($other)) { $other=""; }
		if (!isset($affiliate)) { $affiliate=""; }
	}
	$landscapebanner_query = "SELECT click,img,alt,name,w,h,affiliate FROM adlinks WHERE active=TRUE and type = 'banner' and w > h and w > 600 ORDER BY RAND() LIMIT 1";
	$reviewbanner_query = "SELECT click,img,alt,name,w,h,affiliate FROM adlinks WHERE active=TRUE and type = 'banner' and category = 'review' and w > h and affiliate = '$affiliate' ORDER BY RAND() LIMIT 1";
	$reviewbanner468_query = "SELECT click,img,alt,name,w,h,affiliate FROM adlinks WHERE active=TRUE and type = 'banner' and category = 'review' and w = 468 and affiliate = '$affiliate' ORDER BY RAND() LIMIT 1";

	///////////// METADATA ////////////
	if (count($urlVars)==0 or $other<>""){ // This is a text page - either no urlVars at all or we are on an $other page (like reviews)
		if (count($urlVars)==0) {
		$sql_txt = mysql_query("SELECT * FROM reviews WHERE active = TRUE and affiliate = 'concertreservations.com' LIMIT 1") or die (mysql_error());
		}
		else {
		$sql_txt = mysql_query("SELECT * FROM reviews WHERE active = TRUE and affiliate = '$affiliate' LIMIT 1") or die (mysql_error());
		}
		while($row_txt = mysql_fetch_array($sql_txt)){
			$pagename = $row_txt['pagename'];
			$title = $row_txt['title']; // get website title from database
			$description = $row_txt['description']; // get description from database
			$keywords = $row_txt['keywords']; // get keywords from database
			$body1 = $row_txt['body1'];
			$body2 = $row_txt['body2'];
			$body3 = $row_txt['body3'];
			$body4 = $row_txt['body4'];
			$thumb = "/images/reviews/" . $row_txt['thumb'];
			$click = $row_txt['click'];
			$trackimg = $row_txt['trackimg'];
			$alt = $row_txt['alt'];
			$trackUrl = "homepage_image";
			$type = "review";
		}
		//$title = strtok($title,'|');
	}
	if ($q <> "" || $q <> null) { // if query isn't blank or null...
		$text_q = str_replace("'","",$q);
		$text_q = stripslashes($text_q);
		$title = $text_q . " search | ConcertReservations.com";
		$description = "Keyword search for $text_q";
		$keywords = $text_q;
		$keywords = strtolower($keywords);
		$keywords = str_replace("the ","",$keywords);
		$keywords = str_replace("a ","",$keywords);
		$keywords = str_replace("and ","",$keywords);
		$keywords = str_replace("at ","",$keywords);
		$keywords = str_replace("is ","",$keywords);
		$keywords = str_replace("in ","",$keywords);
		$keywords = str_replace("on ","",$keywords);
		$keywords = str_replace(" ",",",$keywords);
	}
	if (in_array($genre, $genres) && $artist==""){ // if genre is acceptable and artist is blank, set up metadata
		$title = $genre . " tickets | ConcertReservations.com";
		$description = "Buy " . $genre . " tickets online at ConcertReservations.com";
		$keywords = $description;
		$keywords = strtolower($keywords);
		$keywords = str_replace("the","",$keywords);
		$keywords = str_replace("a","",$keywords);
		$keywords = str_replace("and","",$keywords);
		$keywords = str_replace("at","",$keywords);
		$keywords = str_replace("is","",$keywords);
		$keywords = str_replace("in","",$keywords);
		$keywords = str_replace("on","",$keywords);
		$keywords = str_replace(" ",",",$keywords);
		}
	$resultsperpage_rss = 15; // number of results per page for RSS feed pages (they have ticket detail)
	$resultsperpage_nonrss = 100; // number of results per page for text link pages (tons to sift through)
	if (in_array($genre, $genres_rss)) { $resultsperpage = $resultsperpage_rss; } 
	if (in_array($genre, $genres_nonrss)) { $resultsperpage = $resultsperpage_nonrss; }
	if (in_array($genre, $genres) && $genre <> "All" && $artist=="" && $q=="") { // If genre acceptable and isn't all, pagenums set up here 
		include($Include_Dir. "PagedResults.php");
		$sql_rss_genre_browse = mysql_query("SELECT name FROM adlinks WHERE active=TRUE and category = '$genre' order by name") or die (mysql_error());
		$sql_rss_artist_count = mysql_query("SELECT count(id) as num FROM adlinks WHERE active=TRUE and category = '$genre'") or die (mysql_error());
		$numartistsarray = mysql_fetch_array($sql_rss_artist_count);
		$numartists = $numartistsarray['num'];
		/* Start collecting pagination info so we can use it in the title - so we instantiate the paging class */
		$Paging = new PagedResults();
		$Paging->TotalResults = $numartists; // tell the pager how many results we have
		$Paging->ResultsPerPage = $resultsperpage; // choose how many results per page, values here override the PagedResults() defaults
		$Paging->LinksPerPage = 20; // same
		$Paging->PageVarName = "pagenumber"; // same
		if (isset($pagenumber)) { // if we have a pagenumber set (from the URL) set that to be the current page in the pager array
			$Paging->CurrentPage = $pagenumber;
		}
		$InfoArray = $Paging->InfoArray(); // creates the infoarray
		//print("<pre>"); print_r($InfoArray); print("</pre>"); // uncomment to echo the infoarray to see what the pager knows about the page
		$start_offset = $InfoArray["START_OFFSET"];
		$mysql_limit = $InfoArray["MYSQL_LIMIT2"];
		$totalpages = $InfoArray['TOTAL_PAGES'];
		
		// here we're creating the multi-page navigation element $pagenav as a list
		$pagenav = "<div class='pagenav'>\n";
		/* Print our some info like "Displaying page 1 of 49" */
		$pagenav = $pagenav. "<p>Displaying page " . $InfoArray["CURRENT_PAGE"] . " of " . $InfoArray["TOTAL_PAGES"] . "<br/>\n";
		$pagenav = $pagenav. "Displaying results " . $InfoArray["START_OFFSET"] . " - " . $InfoArray["END_OFFSET"] . " of " . $InfoArray["TOTAL_RESULTS"] . "</p>\n";
		$pagenav = $pagenav. "<div class='pagenav-interface'>";
		/* Print our first link */
		if($InfoArray["CURRENT_PAGE"]!= 1) {
			$pagenav = $pagenav. "<ul class='pagenav'>\n<li class='pagenav'><a href='/$genreurl/' title='&#124;&lt; first'><img src='/images/arrow-first.png' alt='&#124;&lt; first'></a></li>\n";
		} else {
			$pagenav = $pagenav. "<ul class='pagenav'>\n<li class='pagenav'><img src='/images/arrow-first-bw.png' alt='&#124;&lt; first disabled'></li>";
		}

		/* Print out our prev link */
		if($InfoArray["PREV_PAGE"]) {
			$pagenav = $pagenav. "<li class='pagenav'><a href='/$genreurl/" . $InfoArray["PREV_PAGE"] . "/' title='&lt; previous'><img src='/images/arrow-previous.png' alt='&lt; previous'></a></li>\n";
		} else {
			$pagenav = $pagenav. "<li class='pagenav'><img src='/images/arrow-previous-bw.png' alt='&lt; previous disabled'></li>\n";
		}

		/* Example of how to print our number links! */
		for($i=0; $i<count($InfoArray["PAGE_NUMBERS"]); $i++) {
			if($InfoArray["CURRENT_PAGE"] == $InfoArray["PAGE_NUMBERS"][$i]) {
				$pagenav = $pagenav. "<li class='pagenav'>" . $InfoArray["PAGE_NUMBERS"][$i] . "</li>\n";
			} else {
				$pagenav = $pagenav. "<li class='pagenav'><a href='/$genreurl/" . $InfoArray["PAGE_NUMBERS"][$i] . "/'>" . $InfoArray["PAGE_NUMBERS"][$i] . "</a></li>\n";
			}
		}

		/* Print out our next link */
		if($InfoArray["NEXT_PAGE"]) {
			$pagenav = $pagenav. "<li class='pagenav'><a href='/$genreurl/" . $InfoArray["NEXT_PAGE"] . "/' title='next &gt;'><img src='/images/arrow-next.png' alt='next &gt;'></a></li>\n";
		} else {
			$pagenav = $pagenav. "<li class='pagenav'><img src='/images/arrow-next-bw.png' alt='next &gt; disabled'></li>\n";
		}

		/* Print our last link */
		if($InfoArray["CURRENT_PAGE"]!= $InfoArray["TOTAL_PAGES"]) {
			$pagenav = $pagenav. "<li class='pagenav'><a href='/$genreurl/" . $InfoArray["TOTAL_PAGES"] . "/' title='last &gt;&#124;'><img src='/images/arrow-last.png' alt='last &gt;&#124;'></a></li>\n";
		} else {
			$pagenav = $pagenav. "<li class='pagenav'><img src='/images/arrow-last-bw.png' alt='last &gt;&#124; disabled'></li>\n";
		}
		$pagenav = $pagenav. "</ul></div></div>\n";
	}

	if ($artist<>""){ // Artist is searched so metadata comes from the RSS feed, so we have to set up the RSS feed now... 
		$sql_rss = mysql_query("SELECT name,click FROM adlinks WHERE active=TRUE and name = '$artist' and type='rss'") or die (mysql_error());
		$sql_rss_trackinginfo = mysql_query("SELECT affiliate,type FROM adlinks WHERE active=TRUE and name = '$artist' and type='rss'") or die (mysql_error());
		$rss_result = mysql_fetch_array($sql_rss);
		$rss_trackinginfo = mysql_fetch_array($sql_rss_trackinginfo);
		$affiliate = $rss_trackinginfo[0];
		$type = $rss_trackinginfo[1];
		$rssurl = $rss_result['click'];
		$rss = fetch_rss($rssurl);
		$title = $rss->channel['title'];
		$description = "Buy $artist tickets at ConcertReservations.com";
		$keywords=str_replace("the ",'',strtolower($description));
		$keywords=str_replace("a ",'',$keywords);
		$keywords=str_replace("and ",'',$keywords);
		$keywords=str_replace("\"",'',$keywords);
		if (empty($rss->items)){ // if there aren't any tickets in the feed we 302 redirect people to ticketcity's page directly
			$redir = $rss->channel['link'];
			header("Location: $redir");
			exit();
		}
if (empty($rss->items)){echo "<a href='" . $rss->channel['link'] ."'>" . $rss->channel['description'] . "</a>";}
	}
	///////////// END METADATA ////////////
	
	$h1 = trim(strtok($title,'|'));
	$pagetype = "";
	if (count($urlVars)==0) { $pagetype = "home"; } // if there are no url variables, show homepage (4x paragraphs text) 
	if ($other=="Reviews") { $pagetype = "review"; }  // if it's a review page, show review page (same code, 4x paragraphs text but it's reviews this time and has banners woo)
	include($Include_Dir. "showBanner.php");
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php // metatags
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
	echo "<title>$title</title>\n";
	echo "<meta name='title' content='$title' />\n";
	echo "<meta name='description' content='$description' />\n";
	echo "<meta name='keywords' content='$keywords' />\n";
?>
<meta name="google-site-verification" content="UtnLa9iX8_OYOtDD4poQaOfgpluxWGswN6pRWxTydUg" />
<link rel="stylesheet" href="/concertreservations.css" type="text/css" media='screen' />
</head>

<body>

<div id='content'>
<!-- header begins -->
<div id="header">
	<div id="logo">
		<h1><a href="/">Concert Reservations</a></h1>
		<h2><a href="/">All the concerts you want, all the providers you trust.</a></h2>
	</div>
</div>

<!-- header ends -->
<div id='main'>
<?php 
		include($Include_Dir. "concertreservations_menu.php"); // include left hand menu
		include($Include_Dir. "google_ad_160.php"); // include right hand google ads
		if ($pagetype == 'review') { 
			showBanner($reviewbanner468_query,"topban");
		}
		echo "<div id='right'>\n"; // begin middle (well, right hand) content pane
		echo "<h1 align='center'>$h1</h1>\n";
	if ($pagetype=="home" && $q==""){ // This is the homepage (no search or urlVars)  
		echo "<p>" . $body1 . "</p>\n\n<p>" . $body2 . "</p>\n\n<p>" . $body3 . "</p>\n\n<p>" . $body4 . "</p>\n"; 	
	}	
	
	if ($pagetype=="review" && $q==""){ // This is the homepage (no search or urlVars) or the Reviews page 
		$thumb_title = $pagename . " homepage";
		echo "<p>" . $body1 . "</p><div id='reviewthumb'><a href='$click' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\"><img src='$thumb' alt='$alt' title='$thumb_title'></a>\n\n";
		if ($trackimg<>"" && isset($trackimg)) { // if there is a tracking image in the db for the link used in the thumbnail, show it here as a 1x1
			echo "<img src='$trackimg' width=1 height=1>";
			}
		echo "</div>\n\n<p>" . $body2 . "</p>\n\n<p>" . $body3 . "</p>\n\n<p>" . $body4 . "</p>\n"; 	
	}
	if (in_array($genre, $genres) && $artist==""){ // We have genre, but no artist - big search time
		if ($genre=="All") { // This is a SINGLE page listing all artists
			$genre_all = "SELECT category,name FROM adlinks where active=TRUE and type='rss' order by name";
			$sql_genre_all = mysql_query($genre_all) or die (mysql_error());
			echo "<div id='list'><ul>";
			while ($all = mysql_fetch_array($sql_genre_all)){
				$g_all = $all[0];
				$a_all = $all[1];
				$artistinternalurl = "/" . urlize($g_all) . "/" . urlize($a_all) . "/";
				echo "<li>";
				echo "<a href='$artistinternalurl'>$a_all - $g_all</a>";
				echo "</li>\n";
			}
			echo "</ul></div>\n\n";
		}
		if ($genre=="NFL") { // This is the nfl page
			$nfl_all = "SELECT name,img,click,w,h,alt,affiliate,type FROM adlinks where active=TRUE and category='NFL' and affiliate='ticketmaster' order by name";
			$sql_nfl_all = mysql_query($nfl_all) or die (mysql_error());
			echo "<div class='nfl'>\n";
			while ($nfl = mysql_fetch_array($sql_nfl_all)){
				$nfl_name = $nfl[0];
				$nfl_img = $nfl[1];
				if ($nfl_img == "http://media.ticketmaster.com/en-us/dbimages/10468a.jpg") { // goddamn 49ers logo is always broken, if it's the broke ass img, use mine instead
					$nfl_img = "/images/San-Francisco-49ers-Logo.gif";
				}
				$nfl_click = $nfl[2];
				$nfl_w = $nfl[3];
				$nfl_h = $nfl[4];
				$nfl_w = 160;
				$nfl_alt = $nfl[0];
				$affiliate = $nfl[6];
				$type = $nfl[7];
				$trackUrl = trackingUrl($nfl_name);
				echo "<span id='nflteam'>\n";
				echo "<a href='$nfl_click' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\">\n<img src='$nfl_img' \nalt='$nfl_alt'></a>\n<br><a href='$nfl_click' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\">$nfl_name</a>\n";	
				echo "</span>\n";
			}
			echo "</div>\n";	
		}
		if (in_array($genre, $genres_rss) && $genre !== "All") { // This is a multiple page listing of RSS tickets in $genres_rss
			echo $pagenav . "<p><div id='list'>"; 
			$rss_genre_browse_paged = "SELECT name,click,affiliate,type FROM adlinks WHERE active=TRUE and category = '$genre' and type='rss' order by name limit $start_offset, $mysql_limit";
			$sql_rss_genre_browse_paged = mysql_query($rss_genre_browse_paged) or die (mysql_error());
				while ($genrebrowsepaged = mysql_fetch_array($sql_rss_genre_browse_paged)){
					$artistbrowse = $genrebrowsepaged[0];
					$rssurl = $genrebrowsepaged[1];
					$affiliate = $genrebrowsepaged[2];
					$type = $genrebrowsepaged[3];
					$artistinternalurl = "/" . urlize($genre) . "/" . urlize($artistbrowse) . "/";
					$rss = fetch_rss($rssurl);
					echo "<h3>";
					if (empty($rss->items)){ 
						$trackUrl = "NoTicketsAvailable";
						echo "<a href='$artistinternalurl' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\">$artistbrowse</a>";
					}
					else {
						echo "<a href='$artistinternalurl'>$artistbrowse</a>";
					}
					echo "</h3>\n";
					echo "<ul>\n";
					foreach ($rss->items as $item) { // loops over each item in the RSS feed
						$href = $item['link'];
						$itemtitle = $item['title'];
						$itemdescription = $item['description'];
						$trackUrl = trackingUrl($itemtitle);
						echo "<li><a href='$href' title='$itemtitle' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\">$itemdescription</a></li>\n";
					}
					echo "</ul>";						
				} 
				echo "</div>";	
			echo $pagenav;
			echo "</div></p>\n\n";
		}
		if (in_array($genre, $genres_nonrss) && $genre !== "NFL") { // This is a multiple page listing of genre stuff, non-RSS feeds - text links
			echo $pagenav . "<p><div id='list'>"; 
			$nonrss_genre_browse_paged = "SELECT name,click,affiliate,type FROM adlinks WHERE active=TRUE and category = '$genre' order by name limit $start_offset, $mysql_limit";
			$sql_nonrss_genre_browse_paged = mysql_query($nonrss_genre_browse_paged) or die (mysql_error());
			echo "<ul id='text'>";
				while ($nonrssbrowsepaged = mysql_fetch_array($sql_nonrss_genre_browse_paged)){
					$event = $nonrssbrowsepaged[0];
					$click = $nonrssbrowsepaged[1];
					$affiliate = $nonrssbrowsepaged[2];
					$type = $nonrssbrowsepaged[3];
					$trackUrl = trackingUrl($event);
					echo "<li id='text'>";
					echo "<a href='$click' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\">$event</a>";
					echo "</li>\n";	
				} 
			echo "</ul></div>";	
			echo $pagenav;
			echo "</div></p>\n\n";
		}
	
	}
	if ($artist<>"") {    // We have an artist defined, so we'll show only that artist
		if (empty($rss->items)){echo "<a href='" . $rss->channel['link'] ."'>" . $rss->channel['description'] . "</a>";}
		else {
			echo "<div id='list'><ul>";
				foreach ($rss->items as $item) {
					$href = $item['link'];
					$itemtitle = $item['title'];
					$itemdescription = $item['description'];
					$trackUrl = trackingUrl($itemtitle);
					echo "<li><a href='$href' title='$itemtitle' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\">$itemdescription</a></li>";
				}
			echo "</ul></div>";
		}
	}	
	if ($q<>"") { // query is not blank, so time to display search results... 
	$query = trim($q);
	$query_word_array = split(" ",$query);	# Break the string into an array of words
	$query_words_to_remove_from_search = array(); // array("A","The","An","In","On","I");
	$query_word_array = array_diff($query_word_array,$query_words_to_remove_from_search);
	$sql = "select type, affiliate, name, category, click FROM adlinks where active=TRUE and MATCH(name) AGAINST('"; 
	while(list($key,$val)=each($query_word_array))
		{
			if($val<>" " and strlen($val) > 0)
			{	
				# A long edit required here. Can't be helped. Watch for typos!
				$sql = $sql . "$val,";
			}
		}
	$sql=substr($sql,0,(strLen($sql)-1)) . "')"; // remove last comma, add closing parentheses 
	# This is the part of the statment that will be attached to the end of the $sql.

/*

	$sql_end = " order by relevance DESC limit 500";
	# echo "<pre> dump of query word array "; var_dump($query_word_array);	echo "</pre>";  # Some test code should it be required (remove the  leading #)
	$sql_middle = '(';			# Need to start by opening the brackets, which are closed after the while loop
	# Now let us generate the middle part of the sql.  We will cycle once through this loop for each word searched on
		while(list($key,$val)=each($query_word_array))
		{
			if($val<>" " and strlen($val) > 0)
			{	
				# A long edit required here. Can't be helped. Watch for typos!
				$sql_middle .= "name like '%$val%' or "; # Note the required space after the last 'or'
				//$sql_middle .= "name like '%$val%' or category like '%$val%' or "; # Note the required space after the last 'or'
			}
		}
	$sql_middle=substr($sql_middle,0,(strLen($sql_middle)-3));		# This will remove the last 'or' from the string.
	$sql = $sql.$sql_middle.")".$sql_end;							# Lets stick it all together.  Including that trailing bracket.

*/

	//echo $sql;
	$search_query = mysql_query($sql);
	echo "<div id='list'><br /><ul id='text'>";
		while ($search_array = mysql_fetch_array($search_query)){
			// type, affiliate, name, category, click
			$type = $search_array[0];
			$affiliate = $search_array[1];
			$name = $search_array[2];
			$category = $search_array[3];
			$click = $search_array[4];
			$trackUrl = trackingUrl($event);
			if ($type == "rss") {
				$rss = fetch_rss($click);				
				//if (empty($rss->items)){echo "<a href='" . $rss->channel['link'] ."'>" . $rss->channel['description'] . "</a>";}
				$artistinternalurl = "/" . urlize($category) . "/" . urlize($name) . "/";
				echo "<li>";
				echo "<a href='$artistinternalurl'>$name</a>";
				echo "</li>\n";
				if (!empty($rss->items)) {
				echo "<ul>";
					foreach ($rss->items as $item) {
						$href = $item['link'];
						$itemtitle = $item['title'];
						$itemdescription = $item['description'];
						$trackUrl = trackingUrl($itemtitle);
						echo "<li><a href='$href' title='$itemtitle' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\">$itemdescription</a></li>";
					}
				echo "</ul>";
				}
			}
			else {
				echo "<li id='text'>";
				echo "<a href='$click' onClick=\"javascript: pageTracker._trackPageview('/outgoing/$affiliate/$type/$trackUrl');\">$name</a>";
				echo "</li>\n";
			}			
		} 
	echo "</ul></div>";
	}
echo "</div>\n\n"; // end right hand content pane, end centerbox span

 ?>
</div></div>		
<?php 
if ($pagetype == 'review') 
	{ showBanner($reviewbanner_query,"ban"); } 
else { 
	showBanner($landscapebanner_query,"ban"); 
	} ?>
<div id='footer'>
&nbsp;
</div>
<?php // EOP stuff like GA code and dbclose
include($Include_Dir. "concertreservations_ga.php"); include($Include_Dir. "concertreservations_dbclose.php"); 
//echo " <!-- "; print_r(get_defined_vars()); echo " --> ";
?>
</body>
</html>