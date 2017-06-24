=== CoinVis ===
Contributors: <a href="paulweekswright.com">Paul Weeks Wright</a>, Demo at <a href="alpual.com>alpual.com</a>
Donate link:
Tags:
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 0.1

Adds shortcodes for generating cryptocurrency coin tables

== Description ==

Adds shortcodes for generating cryptocurrency coin tables

== Installation ==
Install manually as a wordpress plugin.  It will need to be in a .zip file.
Zip up the coinvis directory, which should contain all of the files including this readme.

This pulls data from the coinmarketcap api when the page loads.  To get new data, reload page.  More than 1 request per
5 min is bad form, and might cause coinmarketcap to block you.  Please see their api guidelines to get an api key
for more frequent requests.

<a href="https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation" target="_blank">See more here
https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation</a>

Shortcodes:

[coin-vis] gets all data for bitcoin (default)
[coin-vis coin='ethereum'] gets all data for ethereum
[coin-vis datapoint='price_USD'] gets specifically the data for bitcoin value in usd

datapoint values accepted according to <a href="https://coinmarketcap.com/api" target="_blank">coinmarketcap api</a>

name
symbol
rank
price_usd
price_btc
24h_volume_usd
market_cap_usd
available_supply
total_supply
percent_change_1h
percent_change_24h
percent_change_7d
last_updated

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 0.1 =
- Initial Revision
