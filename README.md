# Check the statuscode of all links on a website

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/http-status-check.svg?style=flat-square)](https://packagist.org/packages/spatie/http-status-check)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/http-status-check.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/http-status-check)
[![StyleCI](https://styleci.io/repos/44727732/shield?branch=master)](https://styleci.io/repos/44727732)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/http-status-check.svg?style=flat-square)](https://packagist.org/packages/spatie/http-status-check)

This repository provides a tool to check the http statuscode of every link on a given website.

## Installation

Via Composer

``` bash
composer global require spatie/http-status-check
```

## Usage

This tool will scan all links on a given site.

```bash
http-status-check scan https://example.com
```

It outputs a line per link found.
 
![screenshot](https://raw.githubusercontent.com/spatie/http-status-check/gh-pages/images/screenshot.png)
 
 When the crawl is finished a summary will be shown.

By default it uses 10 concurrent connections to speed up the crawling process. You can change that number passing a different value to the `concurrency`-option.

```bash
http-status-check scan https://example.com --concurrency=20
```

You can also write all urls that gave a non-2xx or non-3xx response to a file:

```bash
http-status-check scan https://example.com --output=log.txt
```

When the crawler finds a link to an external site it will by default crawl that link as well. If you don't want the crawler to crawl such external urls use the `--dont-crawl-external-links` option

```bash
http-status-check scan https://example.com --dont-crawl-external-links
```

By default, requests timeout after 10 seconds. You can change this by passing the number of seconds to the `timeout`-option.

```bash
http-status-check scan https://example.com --timeout=30
```

By default, the crawler will respect robots data. You can ignore them though with the `--ignore-robots` option.

```bash
http-status-check scan https://example.com --ignore-robots
```

## Testing

To run the tests you'll have to start the included node based server first in a separate terminal window.

```bash
cd tests/server
./start_server.sh
```

With the server running, you can start testing.
```bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
