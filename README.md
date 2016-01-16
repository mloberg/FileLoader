# File Loader

[![Latest Stable Version](https://poser.pugx.org/mlo/file-loader/v/stable)](https://packagist.org/packages/mlo/file-loader)
[![License](https://poser.pugx.org/mlo/file-loader/license)](https://packagist.org/packages/mlo/file-loader)
[![Build Status](https://travis-ci.org/mloberg/FileLoader.svg?branch=master)](https://travis-ci.org/mloberg/FileLoader)
[![Coverage Status](https://coveralls.io/repos/mloberg/FileLoader/badge.svg?branch=master&service=github)](https://coveralls.io/github/mloberg/FileLoader?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mloberg/FileLoader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mloberg/FileLoader/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9a09cc58-37a3-414e-a0a4-f2433b65c014/mini.png)](https://insight.sensiolabs.com/projects/9a09cc58-37a3-414e-a0a4-f2433b65c014)

FileLoader allows you to load files from a collection of directories and then
caches the results for faster access later.

## Installation

    composer require mlo/file-loader

## Requirements

The following PHP versions are supported.

* PHP 5.4
* PHP 5.5
* PHP 5.6
* PHP 7.0
* HHVM

The following versions of Symfony components are supported.

* 2.3
* 2.7
* 2.8
* 3.0

## Overview

```php
$loader = new \Mlo\FileLoader\FileLoader('var/cache/loader', ['app/config']);

$values = $loader->load('config.yml');
```

## Supported File Types

* YAML (.yml/.yaml)
* JSON (.json)
* INI (.ini)
