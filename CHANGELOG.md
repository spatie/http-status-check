# Changelog

All notable changes to `spatie/http-status-check` will be documented in this file.

## 3.3.0 - 2020-12-01

- Add support for PHP 8

## 3.2.0 - 2020-03-18

- follow and log redirects as multiple responses (#66)

## 3.1.4 - 2020-02-18

- fix for overwriting a file

## 3.1.3 - 2020-02-16

- use response status code if available (#59)

## 3.1.2 - 2019-11-19

- allow symfony 5 components

## 3.1.1 - 2018-05-22

- Add an extra null check when a request fails to determine the message.

## 3.1.0 - 2018-05-09

- Update crawler to `^4.1.0`.
- Add `--ignore-robots` option.

## 3.0.0 - 2017-12-24

- PHP 7.1 required
- update `spatie/crawler` to `^3.0`

## 2.5.0 - 2017-12-22
- added support for Symfony 4

## 2.4.0 - 2017-10-16
- added some command line arguments

## 2.3.0 - 2017-02-01
- add `timout` option

## 2.2.1 - 2017-02-17
- fix add `dont-crawl-external-urls` option

## 2.2.0
- add `dont-crawl-external-urls` option

## 2.1.1
- append urls to log file instead of overwriting entire file

## 2.1.0
- added an option to write an output log file

## 2.0.0
- improve speed by crawling links concurrently
- show on which url a broken link was found

## 1.0.2
- add support for Symfony 3

## 1.0.1
- Lower requirements to php 5.5


## 1.0.0
- First release
