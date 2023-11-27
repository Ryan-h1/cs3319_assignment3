<?php
/**
 * @author 67
 * This file contains configuration information for the application.
 */

// Base URL
define('BASE_URL', 'http://cs3319.gaul.csd.uwo.ca/vm098/a3bug/');
//const BASE_URL = 'http://localhost:63342/cs3319_assignment3/';

// Paths relative to the root of your application directory
const COMPONENTS_PATH = __DIR__ . '/components/';
const DATA_ACCESS_PATH = __DIR__ . '/data-access/';
const STYLES_PATH = __DIR__ . '/styles/';

// Database configuration
const DB_SERVER = 'localhost';
const DB_USERNAME = 'root';
const DB_PASSWORD = 'cs3319';
const DB_NAME = 'assign2db';

// Default values
const DEFAULT_TA_IMAGE = 'https://christopherscottedwards.com/wp-content/uploads/2018/07/Generic-Profile.jpg';
