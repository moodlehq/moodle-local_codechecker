Since version 5.0 of this plugin we have stopped
to manually copy all the tools needed manually
and, instead, we are installing them via `composer`.

Also, note that, with version 5.0 we have raised
PHP requirements to PHP 7.4 (it was 7.0 previously).
That implies that the min. Moodle supported version
is Moodle 3.8.3 (really old).

The tools needed for this to run are (you can also
see the 'composer.json` file for details):

- moodlehq/moodle-cs, that installs:
  - squizlabs/php_codesniffer
  - phpcompatibility/php-compatibility
  - phpcsstandards/phpcsextra
  - phpcsstandards/phpcsutils
- phpcompatibility/php-compatibility (dev version)

Special mention to the last package (phpcompatibility)
because, as far as we are using a `dev` version and not
a released one, we have to require it explicitly.

Once we switch to released versions, that explicit requirement
can be removed, because the `moodle-cs` tool already
includes it too.

To update any component:

1. Remove the .lock file, the vendor directory.
2. Run `composer clearcache` (to clear composer caches).
3. Switch to the lowest PHP version supported by the Moodle version required.
4. Run `composer install` (to install everything).a
5. Update `thirdpartylibs.xml` to annotate the new versions of the tools.
6. Commit changes with details about the tools updated.
7. Test, test, test.
8. Optionally, release.

At some point we may want to make the process above automated, so every time
that a new moodle-cs package is released, everything above (1-8) happens automatically.
See (last point of) https://github.com/moodlehq/moodle-local_codechecker/issues/114
