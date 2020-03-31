Instructions to upgrade the phpcs bundled version:

- Drop a checkout of git://github.com/squizlabs/PHP_CodeSniffer.git
  within the "pear/PHP" directory of the plugin. Always removing
  all the previous contents.
- Delete not needed stuff, like:
  - Tests.
  - travis, composer, npm... files.

Current checkout:

  2.9.2 (4665f64)

Local modifications (only allowed if there is a PR upstream backing it):

  - b98fcbc : MDLSITE-2825 followup: backport #2009 to phpcs 2.7.x. Once
    we bump to to phpcs 3.3.0 this hack can be left out. Upstream ref:
    https://github.com/squizlabs/PHP_CodeSniffer/issues/2009

  - 04810ac and 8ea64e0: php 7.4 basic compatibility. Will auto-fix once
    we bump to to phpcs 3.5.0 or later. Upstream refs:
    - https://github.com/squizlabs/PHP_CodeSniffer/issues/2558
    - https://github.com/squizlabs/PHP_CodeSniffer/issues/2561

===== ===== ===== ===== ===== ===== =====

Instructions to upgrade the PHPCompatibility bundled version:

- Drop a checkout of the PHPCompatibility dir of https://github.com/wimg/PHPCompatibility.git
  within the "PHPCompatibility" directory of the local_codechecker plugin. Always
  removing all the previous contents.
- Don't delete anything. 100% complete drop.

Current checkout:

  9.3.5+ (9fb3244)

Local modifications (only allowed if there is a PR upstream backing it):

  - none right now

===== ===== ===== ===== ===== ===== =====
