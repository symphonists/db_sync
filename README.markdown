# Database Synchroniser

## Installation

1. Download and upload the 'db_sync' folder to your Symphony 'extensions' folder.
2. Enable the extension by selecting "Database Synchroniser" in the list and choose Enable from the with-selected menu, then click Apply.

## Warning

As of version 0.7, queries are stored in a file named `db_sync.sql` in your `/manifest` folder. This file is visible to anyone,  therefore I strongly advise that this extension only be enabled on development environments. Don't deploy it to production, or disable it entirely by looking for `db_sync` in Symphony's config file.

## Disclaimer

While this extension has worked well for my own projects, I can't guarantee its stability for your own. My workflow when using a development/staging/production environment is to install this extension on the development server only. When making a release I pull the production database back to staging where I apply the db_sync SQL file. If all goes well after testing, I back up production and run the same db_sync file there. The file is then removed locally and I can continue developing towards another release.

Please, please, please back up your production database before applying any structural changes.