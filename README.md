osu!card
========

osu!card is my own [osu!api](https://github.com/peppy/osu-api/wiki) powered signature made in 6 hours out of boredom when the api went public. I'm releasing this as I'm working on the next version of the card.

This signature makes use of [osu!APIlib](https://github.com/Repflez/osu-API-lib) for fetching and caching osu! data. Data is cached at osu!APIlib defaults.

Setting up
==========

* Extract the `osu` folder on a folder that is not publicly accessible (out of your public_html or disabled via .htaccess/nginx config).
* Extract the `public/osu.php` file on any folder on your website. To configure the file, see [the readme in osu!APIlib](https://github.com/Repflez/osu-API-lib/blob/master/README.md).
* On `$apiPath` make it point to the `osu` folder you extracted before.
* To see if everything works, call your `public/osu.php` folder with your browser. You should see an error that "something went wrong" if everything works.

Usage
=====

The file just redirects to another file, which accept their own parameters. See the `osu/mode` folder for details.

To see the raw osu!api data, append `&debug=very` to the query in `public/osu.php` on your browser.