# Database Synchroniser

* Version: 0.3
* Author: Nick Dunn <http://github.com/nickdunn/>, Richard Warrender <http://github.com/rwarrender>
* Build Date: 2009-08-12
* Requirements: Symphony 2.0.3 with core modification (see below), ASDC Extension <http://github.com/pointybeard/asdc/tree/master>

## Installation

1. Download and upload the 'db_sync' folder to your Symphony 'extensions' folder.
2. Enable the extension by selecting "Database Synchroniser" in the list and choose Enable from the with-selected menu, then click Apply.
3. Modify the `query()` function in `symphony/lib/toolkit/class.mysql.php` adding the lines between `// Start database logger` and `// End database logger` from the `class.mysql.php.txt` file included with this extension.

## Usage

Navigate to System > Preferences to view the current log (download as a SQL dump) or clear the current log.

## Warning

Obviously this is not a support core feature. The modification of the MySQL class will be lost when you upgrade Symphony.

## Disclaimer

While this extension has worked well for my own projects, I can't guarantee its stability. My workflow when using a development/staging/production environment is to install this extension on the development server only. When making a release I pull the production database back to staging where I apply the db_sync SQL file. If all goes well after testing, I back up production and run the same db_sync file. The log is then flushed and I can continue developing towards another release.