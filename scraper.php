<?
// This is a template for a PHP scraper on morph.io (https://morph.io)
// including some code snippets below that you should find helpful

require 'scraperwiki.php';
require 'scraperwiki/simple_html_dom.php';

//
// // Read in a page
$baseurl="http://72.21.92.20";
$html = scraperwiki::scrape($baseurl . "/category/wdc/woodcraft.aspx?sort=priceD");

//
// // Find something on the page using css selectors
$dom = new simple_html_dom();
$dom->load($html);

// parse the categories and save to database
// database columns:
//   Category name
//   path
//   URL
//   
function getCategories($d){
  foreach ($dom->find('div[class=S2refinementsContainer]')->children() as $div) {
    $data = (
      trim(strstr($div->children(1)->plaintext,"(",true)),
      ,
      $baseurl . $div->children(1)->href
      );
  }
}
// print_r($dom->find("table.list"));
//
// // Write out to the sqlite database using scraperwiki library
// scraperwiki::save_sqlite(array('name'), array('name' => 'susan', 'occupation' => 'software developer'));
//
// // An arbitrary query against the database
// scraperwiki::select("* from data where 'name'='peter'")

// You don't have to do things with the ScraperWiki library.
// You can use whatever libraries you want: https://morph.io/documentation/php
// All that matters is that your final data is written to an SQLite database
// called "data.sqlite" in the current working directory which has at least a table
// called "data".
?>
