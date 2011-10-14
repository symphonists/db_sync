# Database Synchroniser

## Installation

1. Download and upload the 'db_sync' folder to your Symphony 'extensions' folder.
2. Enable the extension by selecting "Database Synchroniser" in the list and choose Enable from the with-selected menu, then click Apply.
3. Modify the `query()` function in `symphony/lib/toolkit/class.mysql.php` adding the lines between `// Start database logger` and `// End database logger` from the `class.mysql.php.txt` file included with this extension. Place these at the very end of the function just before the `return` to ensure this query does not interfere with Profile performance logging.

## Warning

Since this extension requires a core file modification, changes you make to the MySQL class will be lost when you upgrade Symphony. Remember to add in the logging call back into `class.mysql.php` if you update Symphony!

As of version 0.7 the queries are stored in a file named `db_sync.sql` in your `/manifest` folder. This is unsecured, and therefore I strongly advise that this extension only be enabled on development environments.

## Disclaimer

While this extension has worked well for my own projects, I can't guarantee its stability. My workflow when using a development/staging/production environment is to install this extension on the development server only. When making a release I pull the production database back to staging where I apply the db_sync SQL file. If all goes well after testing, I back up production and run the same db_sync file. The log is then flushed and I can continue developing towards another release.