Instructions to upgrade the phpcs bundled version:

- Drop a checkout of git://github.com/squizlabs/PHP_CodeSniffer.git
  within the "pear/PHP" directory of the plugin.
- Delete not needed stuff, like:
  - Tests.
  - composer, npm... files.

Current checkout:

  pre 2.6.0 (8c5d176)

Local modifications (only allowed if there is a PR upstream backing it):

  - none right now.
