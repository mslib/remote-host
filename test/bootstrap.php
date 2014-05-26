<?php
/**
 * PHPUnit bootstrap file
 *
 * PHP version 5
 *
 * @author "Marco Spallanzani" <mslib.code@gmail.com>
 */
// Vendor dir
$vendorDir = realpath(__DIR__.'/../../../../vendor/');

// Getting loader from composer autoload
$loader = require $vendorDir . '/autoload.php';

// Registering msl tests namespace
$baseDir = dirname($vendorDir);
$loader->set('Msl\\Tests', array($vendorDir . '/mslib/remote-host/test'));
