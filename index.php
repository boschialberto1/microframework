<?php

/**
 * Index file for microframework.
 *
 * This file is the entry point for the application.
 * It initializes the framework.
 *
 * @package alberto\microframework
 * @author Alberto <boschi.alberto1@gmail.com>
 * @license Proprietary
 */

const ROOT_PATH = __DIR__;
const SRC_PATH = ROOT_PATH . '/src/';
const CONFIG_PATH = SRC_PATH . DIRECTORY_SEPARATOR . 'Config/';
const VENDOR_PATH = ROOT_PATH . '/vendor/';


// Set the environment (development, testing, production)
const APP_ENVIRONMENT = 'development';


require_once SRC_PATH . '/Core/bootstrap.php';