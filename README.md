This repository holds the current translation of the My Colony game
to Czech language.

## Checking for changes

The script `check-translation-changes.php` can be used to check for changes in new version, it will store the result in a file `report.txt`.

**Usage**: `php check-translation-changes.php 0.95.0` where `0.95.0` is
the version you want to check against. The results will be a list of 
differences against the current version stored in `strings_original.js`.
