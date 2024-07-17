<?php

use Panda\Services;

$services = Services::init();

global $pandadb;

$pandadb = $services->db;
