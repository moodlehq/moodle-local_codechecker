Instructions to upgrade the phpcs bundled version:

- Drop a checkout of git://github.com/squizlabs/PHP_CodeSniffer.git
  within the "phpcs" directory of the plugin. Always removing
  all the previous contents before copying.
- Also, remove not needed stuff, like:
  - All dot (.*) files and directories (git, travis...).
  - Any composer.* and vendor files.
  - All .ini, .xsd, .neon and .dist files.
  - The scripts, tests and vendor directories.

Current checkout:

  3.5.6+ (7c1df7d2c)

Local modifications (only allowed if there is a PR upstream backing it):

  - PHP 7.4 fix: https://github.com/squizlabs/PHP_CodeSniffer/pull/3130

===== ===== ===== ===== ===== ===== =====

Instructions to upgrade the PHPCompatibility bundled version:

- Drop a checkout of the PHPCompatibility dir of https://github.com/wimg/PHPCompatibility.git
  within the "PHPCompatibility" directory of the local_codechecker plugin. Always
  removing all the previous contents.
- Don't delete anything. 100% complete drop.

Current checkout:

  9.3.5+ (9fb3244)

Local modifications (only allowed if there is a PR upstream backing it):

  - Added PHPCSAliases.php to base dir to provide phpcs 2/3 compatibility. Needed
    because still there are a number of old class names within the standard. This
    doesn't have any upstream PR, because the file is there, just we had not needed
    it before the jump to phpcs 3.

===== ===== ===== ===== ===== ===== =====
