

[daloRADIUS_Feature_Management]: https://cloud.githubusercontent.com/assets/316371/7444436/48d887e4-f18b-11e4-855d-264dc6d881e1.jpg
[daloRADIUS_Feature_Accounting]: https://cloud.githubusercontent.com/assets/316371/7488564/9338bf0c-f3d4-11e4-977b-48227eb5c2b5.jpg
[daloRADIUS_Book]: https://cloud.githubusercontent.com/assets/316371/7488439/e3c9bd4c-f3d2-11e4-9d88-9f57098752e0.jpg

# About
![daloRadius_Logo](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img1.png?raw=true "MarineGEO logo")

[daloRADIUS](http://www.daloradius.com) is an advanced RADIUS web management application aimed at managing hotspots and general-purpose ISP deployments. It features user management, graphical reporting, accounting, billing engine and integrates with GoogleMaps for geo-locating.

daloRADIUS is written in PHP and JavaScript and utilizes a database abstraction
layer which means that it supports many database systems, among them the popular
MySQL, PostgreSQL, Sqlite, MsSQL, and many others.

It is based on a [FreeRADIUS](http://www.freeradius.org) deployment with a database server serving as the backend.
Among other features it implements ACLs, GoogleMaps integration for locating
hotspots/access points visually and many more features.

## Contributors
Thanks goes to these wonderful people :

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore -->
<table><tr><td align="center"><a href="https://github.com/liran_tal"><img src="https://avatars1.githubusercontent.com/u/316371?v=4" width="100px;" alt="Liran Tal"/><br /><sub><b>Liran Tal</b></sub></a><br /></td><td align="center"><a href="https://github.com/MiguelVis"><img src="https://avatars0.githubusercontent.com/u/4165032?s=460&v=4" width="100px;" alt="MiguelVis"/><br /><sub><b>MiguelVis</b></sub></a><br /></td><td align="center"><a href="https://github.com/screwloose8"><img src="https://avatars0.githubusercontent.com/u/18901582?s=460&v=4" width="100px;" alt="screwloose83"/><br /><sub><b>screwloose83</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/AxeyGabriel"><img src="https://avatars1.githubusercontent.com/u/6699637?s=460&v=4" width="100px;" alt="Axey Gabriel Müller Endres
"/><br /><sub><b>screwloose83</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/zanix"><img src="https://avatars2.githubusercontent.com/u/1580378?s=460&v=4" width="100px;" alt="Joshua Clark"/><br /><sub><b>Joshua Clark</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/theFra985"><img src="https://avatars2.githubusercontent.com/u/16063131?s=460&v=4" width="100px;" alt="Francesco Cattoni"/><br /><sub><b>Francesco Cattoni</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/Tantawi"><img src="https://avatars2.githubusercontent.com/u/1369523?s=460&v=4" width="100px;" alt="Mohamed Eltantawi"/><br /><sub><b>Mohamed Eltantawi</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/Seazonx"><img src="https://avatars1.githubusercontent.com/u/41646287?s=460&v=4" width="100px;" alt="Seazon"/><br /><sub><b>Seazon</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/reigelgallarde"><img src="https://avatars3.githubusercontent.com/u/10612336?s=400&v=4" width="100px;" alt="Reigel Gallarde"/><br /><sub><b>Reigel Gallarde</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/jomaxro"><img src="https://avatars0.githubusercontent.com/u/15638256?s=400&v=4" width="100px;" alt="Joshua Rosenfeld"/><br /><sub><b>Joshua Rosenfeld</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/seanmavley"><img src="https://avatars2.githubusercontent.com/u/5289083?s=400&v=4" width="100px;" alt="Nkansah Rexford"/><br /><sub><b>Nkansah Rexford</b></sub></a><br /></td>
	<td align="center"><a href="https://github.com/dennisdegreef"><img src="https://avatars0.githubusercontent.com/u/361905?s=400&v=4" width="100px;" alt="Dennis de Greef"/><br /><sub><b>Dennis de Greef</b></sub></a><br /></td>
	</tr></table>
<!-- ALL-CONTRIBUTORS-LIST:END -->

## Edited flaviojunior1995
<a href="https://github.com/flaviojunior1995"><img src="https://avatars.githubusercontent.com/u/53404989?v=4" width="100px;" alt="Flavio Junior"/><br /><sub><b>Flavio Junior</b></sub></a>

# Requirements
 * Apache
 * PHP v8.2 or higher
 * MySQL v15.1 or higher

More details about installation and requirements can be found if needed on the (maybe very old) files:

 * [INSTALL.md](https://github.com/flaviojunior1995/daloradius/blob/master/INSTALL.md)
 * FAQS

# Documentation

You can find some documentation in the `doc` directory.

# Features
## Management
### User

    * List Users
    * Create New User
    * Create New User - Quick add ( For IT Team )
    * Edit User
    * Search User
    * Remove User

![daloRadius_Users](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img2.png?raw=true)

### NAS

    * List NAS
    * Create New NAS
    * Edit NAS
    * Remove NAS
  
  ![daloRadius_NAS](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img3.png?raw=true)

### User-Groups

    * List, Create New, Edit and Delete User-Groups Mapping
      usergroup table in radius database
    * List, Create New, Edit and Delete Group-Reply and Group-Check Settings
      radgroupreply and radgroupcheck tables in radius database for managing group-wide attributes

![daloRadius_User-Group](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img4.png?raw=true)

### Profiles

    * Create New Profile
    * List Profile
    * Edit Profile
    * Duplicate Profile
    * Remove Profile

![daloRadius_Profiles](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img5.png?raw=true)

### Attributes

    * Add New Vendor Attibute
    * List Attributes for Vendor
    * Edit Vendor's Attribute
    * Search Attribute
    * Remove Vendor's Attibute
    * Import Vendor Dictionary

![daloRadius_Attributes](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img6.png?raw=true)

## Reports
### Basic Reporting

    * Online Users
      View Online users, users that are connected to the system from all NASes at a current
      point in time.
    * Last Connection Attempts
      View last connection attempts and their status - whether they were rejected or successful
    * Search Users
      Search for Users - similar to the functionality in User Management page
    * Top Users
      View a report of the Top Users based on their Bandwidth consumption or Time usage
      
![daloRadius_Reports](https://github.com/flaviojunior1995/daloradius/blob/master/assents/im7.png?raw=true)

### Logs Reporting

    * daloRADIUS Log
      daloRADIUS keeps a log file for all the actions it performs itself (viewing pages,
      form actions like deleting users, creating new hotspots, queries submission as in
      performing user accounting and more)
    * RADIUS Server Log
      Provides monitoring of the freeradius server logfile
    * System Log
      Provides monitoring of the system log, being syslog or messages, depends.
    * Boot Log
      Provides monitoring of the boot/kernel log (dmesg)
      
![daloRadius_Logs](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img8.png?raw=true)

### Status Reporting

    * Server Status
      Provides detailed information on the server daloRADIUS is deployed.
      Information such as CPU utilization, uptime, memory, disks information, and more.
    * Services Status
      Provides information whether the freeradius server is running along with the database
      server (mysql, postgresql, or others)

![daloRadius_Status](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img9.png?raw=true)

## Accounting
### Users Accounting By

    * Username
    * IP Address
    * NAS IP Address
    * Date (From/To)
    * Display of All Accounting records
      the entire content of the radacct table in the radius database
    * Display of Active Accounting records
      performed by an algorithm implemented by daloRADIUS itself to calculate if
      an account has expired or not based on it's Max-All-Session attribute or Expiration attribute
	* Custom Accounting Query

![daloRadius_Accounting](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img10.png?raw=true)

## Graphs

### Users Graphs
Provides visual graphs and statistical listing per user connection's attributes, being:

    * Logins/Hits
    * Download
    * Upload
 ![daloRadius_Graphs](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img11.png?raw=true)
 
## Configuration
### General Configuration

    * User Settings
      Allow clear text password in db ( yes or no )
      Allow Random Characters ( characters that is allowd on random )
      Password min length
      Password max length
    * Database Settings
      Database connection information (storage: mysql, postgresql and others),
      credentials (username and password), radius database tables names (radcheck, radacct, etc),
      and database password encryption type (none, md5, sha1)
    * Language Settings
      daloRADIUS is multi-lingual
    * Logging Settings and Debugging
      Logging of different actions, queries and page visiting performed on different pages.
      Also supports debugging of SQL queries executed.
    * Interface Settings
      Support for displaying password text in either clear-text or as asterisks to hide it.
      Table listing spanning across multiple pages is configurable on number of rows per page
      and addition of numbers links for quick-access to different pages.
    * Mail Settings
      E-mail client configuration ( by default the SMTP is TLS without certificate verification )

![daloRadius_Configuration](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img12.png?raw=true)

### Reporting

    * Dashboard Settings
      Settings for dashboar like Soft delay and Hard delay.

![daloRadius_Reporting](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img13.png?raw=true)

### Maintenance

    * Test User Connectivity
      Provides the ability to check if a user's credentials (username and password) are valid by
      executing a radius query to a radius server (configurable for radius port, shared secret, etc)
	* Disconnect User
	  Supply a username and send a PoD (Packet of Disconnect) or CoA (Change of Authority) packet
	  to the NAS to disconnect the user.

![daloRadius_Maintenance](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img14.png?raw=true)

### Operators

daloRADIUS supports Operators for complete management of the entire platform.
Different Operators can be added with their contact information and ACLs settings to
grant or revoke them of permissions to access different pages.

    * List Operators
    * Create New Operator
    * Edit Operator
    * Remove Operator

![daloRadius_Operators](https://github.com/flaviojunior1995/daloradius/blob/master/assents/img15.png?raw=true)

# Credits

 [daloRADIUS](http://www.daloradius.com) makes use of several third-party packages and I would like to thank these great tools and their authors for releasing such a good software to the community.

 * datepicker PHP class	- Stefan Gabos <ix@nivelzero.ro>
 * libchart PHP class	- Jean-Marc Trémeaux <jm.tremeaux@gmail.com>
 * icons collection - Mark James of famfamfam.com icons <mjames@gmail.com>
 * ajax auto complete - Batur Orkun <batur@bilkent.edu.tr>
 * dhtml-Suite - Magne Kalleland <post@dhtmlgoodies.com>
 * dompdf - [https://github.com/dompdf](https://github.com/dompdf)

# Support

Helpful resources to find help and support with daloRADIUS:

 * *Official daloRADIUS Website*: http://www.daloradius.com
 * SourceForge hosted forums area: https://sourceforge.net/p/daloradius/discussion/
 * *Mailing List*: daloradius-users@lists.sourceforge.net and register here to post there: https://lists.sourceforge.net/lists/listinfo/daloradius-users
 * Facebook's daloRADIUS related group: https://www.facebook.com/groups/551404948256611/
 * Edited flaviojunior1995 daloradius version github: https://github.com/flaviojunior1995

# Copyright

Copyright Liran Tal 2007-2024. All rights reserved.
For release information and license, read LICENSE.

[daloRADIUS](http://www.daloradius.com) version 2.0 beta / edited release
by Liran Tal <liran.tal@gmail.com>,
Miguel García <miguelvisgarcia@gmail.com>,
edited by <flaviocamacho95@gmail.com>.