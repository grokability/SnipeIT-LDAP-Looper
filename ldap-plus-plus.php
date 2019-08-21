#!/usr/bin/php
<?php
/*
 * ldap-plus-plus.php
 *
 * [Step 1]
 *   Create a single column list of Base-Bind-DNs,
 *      'ldap-basedn-list.txt'
 *
 * [Step 2]
 *    For each basedn,
 *     update Snipe-IT DB's settings.ldap_basedn value,
 *          then run LDAP Sync via the command line.
 *
 * [Step 3]
 *    Post-Sync: update each user's phone, department, etc.
 *       (Some fields may be overwritten on stamp.)
 *
 * [Step 4]
 *    Disable all user logins for all non-IT/non-helpdesk staff.
 */


/*
 * [Step 1]
 *    'ldap-basedn-list.txt'
 */

$ldap_list = 'ldap-basedn-list.txt';
$ldap_sync_cmd = 'php artisan snipeit:ldap-sync --summary';
$snipeit_backup_cmd = 'php artisan snipeit:backup';

(!file_exists($ldap_list))
    ? die("ERROR: $ldap_list not found in this directory! ")
    : print "$ldap_list present!";

# Read the Snipe-IT Server Configuration from the .env file:

echo "Backup a Snipe-IT installation, then loop through a list /
of base_dns named ldap-basedn-list.txt.  Update the Snipe-IT sttings tables /
with each one, then run a ldap-sync per setting injection!\n";

$backup_results = `$snipeit_backup_cmd`;
echo "\nBackup Results = $backup_results\n";

$source_env = shell_exec('. ./.env;echo $DB_DATABASE');
echo "\nSource .env results: $source_env\n";

$db_name = `. ./.env;echo \$DB_DATABASE`;
$db_user = `. ./.env;echo \$DB_USERNAME`;
$db_pass = `. ./.env;echo \$DB_PASSWORD`;

$mysqlCLI_AUTH =  "mysql -u" .trim($db_user) .  " -p" . trim($db_pass) . " " . trim($db_name) . " -e ";
$mysql_SELECT = " 'select ldap_basedn from settings where id = 1;'";
$select_sql = $mysqlCLI_AUTH . $mysql_SELECT;

echo 'MySQL SELECT Statement: ' . $select_sql . "\n";
$exe_results = shell_exec($select_sql);
echo "\nbase_dn_setting_results: {$exe_results}";

$basedn_list = file_get_contents($ldap_list);

/*
 * [Step 2]
 *    For each basedn,
 *     update Snipe-IT DB's settings.ldap_basedn value,
 *          then run LDAP Sync via the command line.
 */

foreach (explode("\n", $basedn_list) as $basedn) {

    $mysql_UPDATE = " \"update settings set ldap_basedn = '" . trim($basedn) . "' where id = 1\"";
    $update_sql = $mysqlCLI_AUTH . $mysql_UPDATE;
    
    echo "\nMYSQL UPDATE Statement: \n$update_sql\n";
    $update_results = shell_exec($update_sql);
    echo "\nbase_dn update results: \n{$update_results}";
    $exe_results = shell_exec($select_sql);
    echo "\nbase_dn_setting_results: \n{$exe_results}";

    $sync_results = shell_exec($ldap_sync_cmd);
    echo "\nSync Results = \n$sync_results \n";


}

/*
 * [Step 3]
 *    Post-Sync: update each user's phone, department, etc.
 *       (Some fields may be overwritten on stamp.)
 *
 * [Step 4]
 *    Disable all user logins for all non-IT/non-helpdesk staff.
 */