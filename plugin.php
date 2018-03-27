<?php

/*
 * @wordpress-plugin
 *
 * Plugin Name: EDDBK - Developer Mode
 * Description: Activates developer mode for EDD Bookings.
 * Version: 0.0.0-dev
 * Author: RebelCode
 * Text Domain: eddbk
 * Domain Path: /languages/
 * License: GPLv3
 */

use RebelCode\Modular\Finder\ModuleFileFinder;

if (!defined('ABSPATH')) {
    return;
}

define('EDDBK_SAFE_EXCEPTION_HANDLING', false);

add_filter(
    'eddbk_core_module_file_finder',
    function($moduleFinder) {
        $path = EDDBK_DIR . DIRECTORY_SEPARATOR . 'dev-modules';

        if (!file_exists($path)) {
            mkdir($path);
        }

        // Create a module file finder for developer modules
        $devModFinder = new ModuleFileFinder($path);

        // Load each dev module's autoload file if available
        foreach ($devModFinder as $_file) {
            $_path = (string) $_file;
            $_dir = dirname($_path);
            $_autoload = $_dir . '/vendor/autoload.php';
            if (file_exists($_autoload)) {
                require $_autoload;
            }
        }
        reset($devModFinder);

        // Because ModuleFileFinder is not an Iterator, but an IteratorAggregator
        $moduleFinderIterator = new IteratorIterator($moduleFinder);
        $devModFinderIterator = new IteratorIterator($devModFinder);

        // AppendIterator provides iteration over multiple iterators, one after another
        $iterator = new AppendIterator();
        $iterator->append($moduleFinderIterator);
        $iterator->append($devModFinderIterator);

        return $iterator;
    }
);
