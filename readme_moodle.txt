Instructions to upgrade the phpcs bundled version:

- Drop a checkout of git://github.com/squizlabs/PHP_CodeSniffer.git
  within the "pear/PHP" directory of the plugin.
- Delete not needed stuff, like:
  - Tests.
  - composer, npm... files.

Current checkout:

  2.6.0 release(1bcdf03)

Local modifications (only allowed if there is a PR upstream backing it):

  - none right now.
