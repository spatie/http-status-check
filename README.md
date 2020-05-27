# Check the HTTP status code of all links on a website

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/http-status-check.svg?style=flat-square)](https://packagist.org/packages/spatie/http-status-check)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/http-status-check.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/http-status-check)
[![StyleCI](https://styleci.io/repos/44727732/shield?branch=master)](https://styleci.io/repos/44727732)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/http-status-check.svg?style=flat-square)](https://packagist.org/packages/spatie/http-status-check)

This repository provides a tool to check the HTTP status code of every link on a given website.

## Support us

Learn how to create a package like this one, by watching our premium video course:

[![Laravel Package training](https://spatie.be/github/package-training.jpg)](https://laravelpackage.training)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

This package can be installed via Composer:

``` bash
composer global require spatie/http-status-check
```

## Usage

This tool will scan all links on a given website:

```bash
http-status-check scan https://example.com
```

It outputs a line per link found:
 
![screenshot](https://raw.githubusercontent.com/spatie/http-status-check/gh-pages/images/screenshot.png)
 
When the crawling process is finished a summary will be shown.

By default the crawler uses 10 concurrent connections to speed up the crawling process. You can change that number by passing a different value to the `--concurrency` option:

```bash
http-status-check scan https://example.com --concurrency=20
```

You can also write all urls that gave a non-2xx or non-3xx response to a file:

```bash
http-status-check scan https://example.com --output=log.txt
```

When the crawler finds a link to an external website it will by default crawl that link as well. If you don't want the crawler to crawl such external urls use the `--dont-crawl-external-links` option:

```bash
http-status-check scan https://example.com --dont-crawl-external-links
```

By default, requests timeout after 10 seconds. You can change this by passing the number of seconds to the `--timeout` option:

```bash
http-status-check scan https://example.com --timeout=30
```

By default, the crawler will respect robots data. You can ignore them though with the `--ignore-robots` option:

```bash
http-status-check scan https://example.com --ignore-robots
```

## Testing

To run the tests, first make sure you have [Node.js](https://nodejs.org/) installed. Then start the included node based server in a separate terminal window:

```bash
cd tests/server
./start_server.sh
```

With the server running, you can start testing:

```bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
