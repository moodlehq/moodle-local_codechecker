Instructions to upgrade the phpcs bundled version:

- Drop a checkout of git://github.com/squizlabs/PHP_CodeSniffer.git
  within the "pear/PHP" directory of the plugin.
- Delete not needed stuff, like:
  - Tests.
  - travis, composer, npm... files.

Current checkout:

  2.7.0 (571e27b)

Local modifications (only allowed if there is a PR upstream backing it):

  - b98fcbc : MDLSITE-2825 followup: backport #2009 to phpcs 2.7.x. Once
    we bump to to phpcs 3.3.0 this hack can be left out. Upstream ref:
    https://github.com/squizlabs/PHP_CodeSniffer/issues/2009

===== ===== ===== ===== ===== ===== =====

Instructions to upgrade the PHPCompatibility bundled version:

- Drop a checkout of https://github.com/wimg/PHPCompatibility.git
  within the "PHPCompatibility" directory of the plugin.
- Don't delete anything. 100% complete drop.

Current checkout:

  7.1.4 (95e143e)

Local modifications (only allowed if there is a PR upstrea backing it):

  - none right now

===== ===== ===== ===== ===== ===== =====
