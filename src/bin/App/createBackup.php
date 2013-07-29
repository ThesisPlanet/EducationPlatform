<?php
require_once (realpath(__DIR__) . '/../bootstrap.php');
defined('BACKUP_PATH') ||
         define('BACKUP_PATH', realpath(CONFIGURATION_PATH) . '/../backup');
// flush the tmp directory to ensure it's clean.
if (! is_dir(BACKUP_PATH . "/tmp")) {
    mkdir(BACKUP_PATH . "/tmp");
} else {
    exec("rm -rf " . BACKUP_PATH . "/tmp");
    mkdir(BACKUP_PATH . "/tmp");
}

echo "dumping database to " . BACKUP_PATH . "/tmp/dep.sql\n";
chdir(BACKUP_PATH . "/tmp");
exec("mysqldump dep --user=dep --password=dep > dep.sql");

echo "Copying shared folder content (course images, temporary files, etc) to" .
         BACKUP_PATH . "/tmp/shared/*\n";
exec("cp -R " . SHARE_PATH . " " . BACKUP_PATH . "/tmp/");
echo "Copying configuration data to" . BACKUP_PATH . "/tmp/configuration/*\n";
exec("cp -R " . CONFIGURATION_PATH . " " . BACKUP_PATH . "/tmp");

echo "Creating tar file of backup data and placing in " . BACKUP_PATH .
         "/backup.tar.gz\n";
exec("tar -czvf ../backup.tar.gz .");
echo "Deleting tmp directory\n";
exec("rm -rf " . BACKUP_PATH . "/tmp");
echo "Platform backed now backed up. Please execute restoreBackup.php in order to restore the configuration.\n";