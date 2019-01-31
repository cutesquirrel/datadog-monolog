# Logz.io Monolog integration

[![Latest Stable Version](https://poser.pugx.org/inpsyde/logzio-monolog/v/stable)](https://packagist.org/packages/inpsyde/logzio-monolog) 
[![Project Status](http://opensource.box.com/badges/active.svg)](http://opensource.box.com/badges) 
[![License](https://poser.pugx.org/inpsyde/logzio-monolog/license)](https://packagist.org/packages/inpsyde/logzio-monolog)


This package allows you to integrate [datadoghq.com](https://docs.datadoghq.com/api/?lang=bash#logs) into Monolog.

## Installation

Install the latest version with

```
$ composer require cutesquirrel/datadog-monolog
```

## Basic Usage

```php
<?php

use Monolog\Logger;
use cutesquirrel\DatadogMonolog\DatadogHandler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new LogzIoHandler('<your-token>'));

// add records to the log
$log->warning('Foo');
$log->error('Bar');
```

