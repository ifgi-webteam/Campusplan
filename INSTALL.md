Campusplan Installation
============

Requirements
------------

* Apache Webserver
* PHP 5.1
    * curl enabled
* [lessc](http://lesscss.org)
* [Cloudmade](http://cloudmade.com/) routing API key

Installation
------------
1. Clone the repository into your webserver htdocs
2. Add your Cloudmade API key to keys.php
3. Verify that folders **cache**, **routes** and **tiles** can be read and written by your HTTP server user

Working with the CSS
------------
The base CSS is made with [Bootstrap 2.3.2](http://getbootstrap.com/2.3.2/).

After making changes to either **campusplan.less** or **campusplan-responsive.less**, you need to recompile the files with **lessc** into proper CSS files:

```
cd campusplan\less
lessc -yui-compress campusplan.less ../css/campusplan.css
lessc -yui-compress campusplan-responsive.less ../css/campusplan-responsive.css
```