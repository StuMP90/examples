# Examples

Just some examples, experiments and references for local use.

## Mar 2022
* wordle hints - A Chrome extension linked to wordle2. It reads the gamestate, sends it to wordle2 (like an api) and wordle2 returns a list of suggested words or the starting word. The word selection is a little more comprehensive than wordle2's own interface. (Using PHP 7.4, under Chrome and Valet on Mac.)

* wordleart - Experiments generating words within word grids in a wordle style with valid words. One version prints dot matrix style letters within letters, the other converts black and white images into a grid and then populates it. (Using PHP 7.4, under Valet on Mac.)

* flightslive2 - A reworked version of flightslive, but with Google Maps and an API_to_KML translation script this time. Some work arounds were needed due to Google's timeout and file size limits, along with Cloudflare's cache. The main benefit of this version was being able to use Google Earth's non-rotateable, but rotated, icons to indicate aircraft headings. (Updated to generate and cache KMZ files.) As with the original, all the data sources are free and keyless, which places some limitations on the data that I can use. (Using PHP 7.4, on a standard CentOS/Plesk server as Google only accept "public" KML files.)

* wordle2 - I found out today that all of wordle's dictionary words - both allowed guesses and answers (in chronological order... DOH!!!) - are in the page source code. So I replaced the dictionary in the finder. I also added positional analysis to the best word lists. Of course I now know the currenly planned answers for the next few years...

* flightslive - An OpenStreetMap map with, live, OpenSky aircraft markers. Russian, Belarusian and Chinese aircraft are meant to be colour coded (red, amber, yellow). However, the ICAO transponder code only appears to signify the aircraft origin rather than the current operator, e.g. a UK owned aircraft leased and operated by a Russian airline will show as UK. If I can find a keyless API to lookup the current operator I may update this. (Using PHP 7.4, under Valet on Mac.)

## Jan/Feb 2022
* wordle - A word finder for Wordle. UK, USA and combined dictionaries. Just a simple interface, originally built on a Saturday ready for Sunday's word. Although, 9 times out of 10 I can find the word without help. (2022-02-21 Added analysis of best "finding" words. Will see how it works out.) (Using PHP 7.4, MySQL and Bootstrap, under Valet on Mac.)

* api_to_elastic - Fetching API data and saving it to elasticsearch, then retrieving and graphing the data. Using Covid data API's since there are a few of those around right now. (Using PHP 7.4, Elasticsearch, Bootstrap and Google Charts, under Valet on Mac.)

## 2020/2021:
* mybusmap - A very simple, quick'n'dirty, single-page "app" to display the real-time locations of a few local buses. It uses the https://www.bus-data.dft.gov.uk/ API for bus location data and https://www.ordnancesurvey.co.uk/ for maps. There is no cache as the data has to be live to be useful. On mobiles it uses the phone's current location to centre the map. (Using PHP 7.4, under Valet on Mac for development then a standard CentOS/Plesk server for live use.)
