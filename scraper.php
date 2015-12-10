<?
// This is a template for a PHP scraper on morph.io (https://morph.io)
// including some code snippets below that you should find helpful

require 'scraperwiki.php';
require 'scraperwiki/simple_html_dom.php';

//
// // Read in a page
$baseurl="http://gp1.adn.edgecastcdn.net";
$html = scraperwiki::scrape($baseurl . "/category/wdc/woodcraft.aspx?sort=priceD");

getCategories($html);
//
// // Find something on the page using css selectors
// $dom = new simple_html_dom();
// $dom->load($html);

// parse the categories and save to database
// database columns:
//   Category name
//   path
//   URL
//   
function getCategories($u){
  $d = new simple_html_dom();
  $d->load(scraperwiki::scrape($u));
  if ($d->find('div[id=ctl00_cphContent_gsaCatFacetContainer]')->find('div[class=S2refinementsContainer]')) {
    $breadcrumb = $d->find('div[id=breadcrumb]');
    foreach ($breadcrumb->children() as $crumb) {
      $path .= trim($crumb->innertext) . "/";
    }
    $path .= trim(strrchr($breadcrumb->innertext,">"),"> ");
    foreach ($d->find('div[id=ctl00_cphContent_gsaCatFacetContainer]')->find('div[class=S2refinementsContainer]')->children() as $div) {
      $data = (
        trim(strstr($div->children(1)->innertext,"(",true)),
        $path,
        $baseurl . $div->children(1)->href
        );
      scraperwiki::save_sqlite(array('Name', 'Path', 'URL'), $data, 'Categories');
      getCategories($baseurl . $div->children(1)->href);
    }
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
