# Database Synchroniser

* Version: 0.6
* Author: Nick Dunn <http://github.com/nickdunn/>, Richard Warrender <http://github.com/rwarrender>
* Build Date: 2009-09-06
* Requirements: Symphony 2.0.3 with modification (see below)

## Installation

1. Download and upload the 'db_sync' folder to your Symphony 'extensions' folder.
2. Enable the extension by selecting "Database Synchroniser" in the list and choose Enable from the with-selected menu, then click Apply.
3. Modify the `query()` function in `symphony/lib/toolkit/class.mysql.php` adding the lines between `// Start database logger` and `// End database logger` from the `class.mysql.php.txt` file included with this extension. Place these at the very end of the function just before the `return` to ensure this query does not interfere with Profile performance logging.

## Usage

Navigate to System > Preferences to view the current log (download as a SQL dump) or clear the current log.

## Warning

Obviously this is not a supported core feature. The modification of the MySQL class will be lost when you upgrade Symphony.

## Disclaimer

While this extension has worked well for my own projects, I can't guarantee its stability. My workflow when using a development/staging/production environment is to install this extension on the development server only. When making a release I pull the production database back to staging where I apply the db_sync SQL file. If all goes well after testing, I back up production and run the same db_sync file. The log is then flushed and I can continue developing towards another release.

## Version History

0.6
* skipped public 0.5 version (in-house release)
* removed ASDC dependency
* added support for "events" so that queries are logged in batches
* removed `/content/log.php` viewing page for simplicity

0.4
* added escaping of logged SQL string to fix apostrophes and regular expressions (ND)
* removed Database Sync from System menu in favour of button on System Preferences page (ND)
* added query count to UI and downloaded SQL dump (ND)

0.3
* added export/flush controls to System > Preferences (RW)
* added timestamp to log (RW)

0.1 and 0.2
* internal Airlock releases (ND)