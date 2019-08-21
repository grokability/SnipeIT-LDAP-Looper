# SnipeIT-LDAP-Looper

A drop-in tool to assist IT Managers who administer Enterprise AD/LDAP Directories in performing 'bulk-ldap' operations.  Specifically, across many base-bind-DNs.

See: [Snipe-IT LDAP Documentation](https://snipe-it.readme.io/docs/ldap-sync-login)

1. Create a single column list of Base-Bind-DNs, `ldap-basedn-list.txt`
2. Drop both `ldap-basedn-list.txt` and `ldap-plus-plus.php` into your Snipe-IT installation.
2. `php ldap-plus-plus.php`

_TODO: consider this a POC towards_ `php artisan snipeit:ldap-feed [ldap-basedn-list.txt]`

Also to consider: post-sync operations, such as importing User information to Snipe-IT.Users via the API, or disabling accounts for all non-IT/Asset Management personel.
 
#### Implementation Notes

```
[Step 1]
  Create a single column list of Base-Bind-DNs,
     'ldap-basedn-list.txt'
 *
[Step 2]
   For each basedn,
    update Snipe-IT DB's settings.ldap_basedn value,
         then run LDAP Sync via the command line.
 *
[Step 3]
   Post-Sync: update each user's phone, department, etc.
      (Some fields may be overwritten on stamp.)
 *
[Step 4]
   Disable all user logins for all non-IT/non-helpdesk staff.
```

*Attention: Prior to machine-gunning users into your Snipe-IT database, this script will try to backup your snipe-it environment before running - however if there is something preventing `php artisan snipeit:backup` from working, you may be going into dangerous territory without a net!*  _Abandon hope, all ye who use this script willy-nilly!_