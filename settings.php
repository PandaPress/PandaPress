<?php

use Panda\Services;

$services = Services::init();

global $pandadb;
global $logger;

$pandadb = $services->db;
$logger = $services->logger;
