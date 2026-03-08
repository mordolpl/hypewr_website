<?php
// includes/db.php - helper wrappers around PDO connection

require_once __DIR__ . '/../config.php';

function db(): PDO {
    return getDbConnection();
}
