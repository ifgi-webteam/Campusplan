# WWU Campusplan App 

## About

This is a web app version of a the **Campusplan App** developed by the [University of Münster](http://www.uni-muenster.de/). The goal of this web app is to subsequently replace the existing native versions for [iOS](http://itunes.apple.com/de/app/wwu-campus-plan/id474030032?mt=8) and [Android](https://play.google.com/store/apps/details?id=ifgi.android) in order to reduce the maintenance effort to one code base.

![Screenshot](screenshot.png)

Campusplan is built with AngularJS. University data, such as buildings and lecture halls, is queried and processed from the SPARQL endpoint provided by [University of Münster's Open Data initiative](http://lodum.de/) (LODUM). Canteen data is [parsed](https://github.com/chk1/mensaparser) from Studierendenwerk Münster, weather data provided by [forecast.io](http://forecast.io/). Routing and map tiles provided by Mapquest Open.

## Installation

### Requirements

* webserver like Apache or Nginx 
* PHP 5.x
* [bower](http://bower.io/)

### Installation

1. Clone the repository into your webserver htdocs
2. Configure your webserver to redirect requests to `index.html` ([Nginx configuration example](https://gist.github.com/chk1/d8149378fcea2cf72778); for Apache it is enough to enable mod_rewrite and then use the .htaccess file provided)
2. Run ```bower update``` to install the Javascript and CSS dependencies
3. Create the directory ```api/cache``` and give your webserver read/write access: ```chmod www-data:www-data api/cache/```

## Attributions

* Map icons `img/awesomemarkers-*.png` derived from the [Awesome Markers](https://github.com/lvoogdt/Leaflet.awesome-markers) project (MIT license)
* Navigation & menu icons from [Iconmonstr](http://iconmonstr.com/) (Creative Commons license) and [The Noun Project](http://thenounproject.com/) (Public Domain)
* [Weather icons](http://erikflowers.github.io/weather-icons/) (`fonts/weathericons*`) by Lukas Bischoff & Erik Flowers (SIL Open Font License 1.1 & MIT License)
* [Signika font](http://www.google.com/fonts/specimen/Signika) by Anna Giedryś (SIL Open Font License 1.1)