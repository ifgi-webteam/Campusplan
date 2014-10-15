# WWU Campus Plan App 

This is a web app version of a the **Campus Plan App** developed by the [University of Münster](http://www.uni-muenster.de/). The goal of this web app is to subsequently replace the existing native versions for [iOS](http://itunes.apple.com/de/app/wwu-campus-plan/id474030032?mt=8) and [Android](https://play.google.com/store/apps/details?id=ifgi.android) in order to reduce the maintenance effort to one code base.

![Screenshot](screenshot.png)

## Installation

### Requirements

* Apache Webserver
	* mod_rewrite enabled
* PHP 5.1+
* bower 

### Installation

1. Clone the repository into your webserver htdocs
2. Run ```bower update``` to install the JS libraries

### Todo v2

#### Features
- [ ] Hauptseite
  - [x] Kacheln
  - [ ] Navbar
  	- [x] Farbwechsel
- [ ] Mensen 
  - [ ] DB API
  	- [x] Anfragen
  	- [ ] Caching
- [ ] Uni A-Z
  - [ ] DB API
- [ ] Karte 
  - [ ] DB API
- [ ] Fachbereiche 
  - [ ] DB API
- [ ] Favoriten 
- [ ] Hörsäle 
  - [ ] DB API
- [ ] Info 
- [ ] ULB-Katalog 
- [ ] Wohnheime
  - [ ] DB API