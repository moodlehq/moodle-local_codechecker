Instructions to upgrade the phpcs bundled version:

- Drop a checkout of git://github.com/squizlabs/PHP_CodeSniffer.git
  within the "pear/PHP" directory of the plugin.
- Delete not needed stuff, like:
  - Tests.
  - travis, composer, npm... files.

Current checkout:

  2.6.2 (4edb770)

Local modifications (only allowed if there is a PR upstream backing it):

  - none right now.
