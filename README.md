# File Loader

[![Build Status](https://travis-ci.org/mloberg/FileLoader.svg?branch=master)](https://travis-ci.org/mloberg/FileLoader)
[![Coverage Status](https://coveralls.io/repos/mloberg/FileLoader/badge.svg?branch=master&service=github)](https://coveralls.io/github/mloberg/FileLoader?branch=master)

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
