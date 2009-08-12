# Database Synchroniser

* Version: 0.3
* Author: Nick Dunn <http://github.com/nickdunn/>, Richard Warrender <http://github.com/rwarrender>
* Build Date: 2009-08-12
* Requirements: Symphony 2.0.3 and core modification (see below)

## Installation

1. Download and upload the 'db_sync' folder to your Symphony 'extensions' folder.
2. Enable the extension by selecting "Database Synchroniser" in the list and choose Enable from the with-selected menu, then click Apply.
3. Modify the `query()` function in `symphony/lib/toolkit/class.mysql.php` adding the lines between `// Start database logger` and `// End database logger` from the `class.mysql.php.txt` file included with this extension.

## Usage

Navigate to System > Preferences to view the current log (download as a SQL dump) or clear the current log.