PHP - JasperReports integration with JavaBridge
====

[![Build Status](https://travis-ci.com/alaczi/phpjasper.svg?branch=master)](https://travis-ci.com/alaczi/phpjasper)
[![Coverage Status](https://coveralls.io/repos/github/alaczi/phpjasper/badge.svg?branch=master)](https://coveralls.io/github/alaczi/phpjasper?branch=master)
[![Latest Stable Version](http://poser.pugx.org/polarbear/phpjasper/v)](https://packagist.org/packages/polarbear/phpjasper)
[![Total Downloads](http://poser.pugx.org/polarbear/phpjasper/downloads)](https://packagist.org/packages/polarbear/phpjasper)
[![Latest Unstable Version](http://poser.pugx.org/polarbear/phpjasper/v/unstable)](https://packagist.org/packages/polarbear/phpjasper)
[![License](http://poser.pugx.org/polarbear/phpjasper/license)](https://packagist.org/packages/polarbear/phpjasper)

This component is to run JasperReports reports in PHP with using JavaBridge.

## Why?

JasperReports is a great tool to generate reports with different output formats (for example: PDF, DOC, XLS),
and it comes with a wysiwyg editor, JasperSoft Studio.

## Requirements
To get this work, you have to install JavaBridge and add required .jars from JasperReports.

- JavaBridge: https://sourceforge.net/projects/php-java-bridge
- JasperReports: https://sourceforge.net/projects/jasperreports
- JasperSoft Studio: https://sourceforge.net/projects/jasperstudio

There is a script in the Resources/script dir to easily start the standalone JavaBridge server with the jar files included in the classpath

## Install

Clone the project
```BASH
    git clone https://github.com/polarbearhandler/phpjasper.git
```

Using composer
```YML
    composer require polarbear/phpjasper
```

For JavaBridge include you should set `allow_url_include` to `On` in php.ini

## Limitations

Currently, the JavaBridge should run on the same machine.
It's recommended to run the JavaBridge with the same user as your webserver (www-data)

This library was tested with JasperReports 6.17.0

## Test and usage

Just run the tests
Check the test suite for an example how to pass datasource/parameters to the report

```BASH
    phpunit src/
```
