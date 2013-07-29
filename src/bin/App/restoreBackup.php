<?php
require_once (realpath(__DIR__) . '/../bootstrap.php');
defined('BACKUP_PATH') || define('BACKUP_PATH',
        realpath(CONFIGURATION_PATH . "/../") . '/backup');

// flush the tmp directory to ensure it's clean.
if (! is_dir(BACKUP_PATH . "/tmp")) {
    mkdir(BACKUP_PATH . "/tmp");
} else {
    exec("rm -rf " . BACKUP_PATH . "/tmp");
    mkdir(BACKUP_PATH . "/tmp");
}
chdir(BACKUP_PATH);
if (! file_exists('backup.tar.gz')) {
    throw new \exception("File: " . BACKUP_PATH . "/backup.tar.gz must exist.");
}

echo "Moving backup.tar.gz into temporary directory.\n";

exec("cp backup.tar.gz tmp/");

echo "extracting contents...";
chdir(BACKUP_PATH . "/tmp");
exec("tar -xzvf backup.tar.gz");

echo "Moving " . SHARE_PATH . " to " . realpath(SHARE_PATH . "/../") .
         "/shared.old\n";
// Backup existing directory (rename to ./shared.old)
if (is_dir(realpath(SHARE_PATH . "/../shared.old"))) {
    exec("rm -rf " . realpath(SHARE_PATH . "/../shared.old"));
}

if (is_dir(SHARE_PATH)) {
    rename(SHARE_PATH, realpath(SHARE_PATH . "/../") . "/shared.old");
}

echo "Copying backed up shared folder into main shared folder.\n";
rename(BACKUP_PATH . "/tmp/shared", SHARE_PATH);

if (is_dir(realpath(CONFIGURATION_PATH . "/../configuration.old"))) {
    exec("rm -rf " . realpath(CONFIGURATION_PATH . "/../configuration.old"));
}

if (is_dir(CONFIGURATION_PATH)) {
    echo "Renaming existing configuration path to configuration.old\n";
    rename(CONFIGURATION_PATH,
            realpath(CONFIGURATION_PATH . "/../") . "/configuration.old");
}
echo "Copying backed up configuration folder into main configuration folder.\n";
rename(BACKUP_PATH . "/tmp/configuration", CONFIGURATION_PATH);

echo "Replacing database.\n";
exec("mysql -h localhost -u dep -pdep dep < dep.sql");

echo "Upgrading Schema as necessary.\n";
echo exec(
        "APPLICATION_ENV=" . APPLICATION_ENV . " php " . APPLICATION_PATH .
                 "/../bin/doctrine.php orm:schema-tool:update --force") . "\n";

echo "Done.\n";