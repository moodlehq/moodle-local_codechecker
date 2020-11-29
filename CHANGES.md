Changes in version 3.0.0 (20201127) - Welcome phpcs 3
-----------------------------------------------------
- Upgrade to PHP_CodeSniffer 3.5.8 (stronk7):
    - PHP_CodeSniffer move to phpcs directory (see readme_moodle.txt for complete instructions).
    - Move Moodle sniffs to PHP_CodeSniffer 3.
    - Create own runner/report.
    - Move as much as possible from locallib.php to classes.
    - Add PHPUnit 8 compatibility, keeping PHPUnit 6 working.
    - Min Moodle version required increases from 3.2 to 3.4.
- [PR#90](https://github.com/moodlehq/moodle-local_codechecker/pull/90): Adapt custom PHP_CodeSniffer runner (classes/runner.php) to use PHP_CodeSniffer 3 API (Sara Arjona).
- [PR#95](https://github.com/moodlehq/moodle-local_codechecker/pull/95): Unlock session before processing files (Víctor Déniz).

Changes in version 2.9.8 (20201002) - Bye and thank you, phpcs 2.x
------------------------------------------------------------------
- [PR#83](https://github.com/moodlehq/moodle-local_codechecker/pull/83) and [PR#84](https://github.com/moodlehq/moodle-local_codechecker/pull/84): Allow a list of files to be checked (Sam Marshall).
- [PR#80](https://github.com/moodlehq/moodle-local_codechecker/pull/80): Remove require of now-deleted coursecatlib.php (Jun Pataleta).
- Several travis changes:
    - [PR#79](https://github.com/moodlehq/moodle-local_codechecker/pull/79): Bump to use moodlehq/moodle-plugin-ci v3.
    - [PR#81](https://github.com/moodlehq/moodle-local_codechecker/pull/81): Add support for 310 branch.
- Small README changes: information section improved and travis status badge update to show travis.com build status. 

Changes in version 2.9.7 (20200718) - Bye bye, JS
-------------------------------------------------
- [PR#75](https://github.com/moodlehq/moodle-local_codechecker/pull/75) and [PR#77](https://github.com/moodlehq/moodle-local_codechecker/pull/77): Small tweaks for better transition to phpcs3.
- CONTRIB-8122: Stop processing non-php files (runner and UI).

Changes in version 2.9.6 (20200611) - June's Roentgenium
--------------------------------------------------------
- [PR#63](https://github.com/moodlehq/moodle-local_codechecker/pull/63): Make MOODLE_INTERNAL check declare() aware (Peter Burnett).
- [PR#72](https://github.com/moodlehq/moodle-local_codechecker/pull/72): Minor php74 fixes and travis refactor.

Changes in version 2.9.5 (20200401) - Poisson d'avril
-----------------------------------------------------
- CONTRIB-8024: Process all files as UTF-8 encoded (defined @ standard level).
- CONTRIB-6175: Only process PHP files (defined @ standard level).
- [PR#69](https://github.com/moodlehq/moodle-local_codechecker/pull/69): Upgrade PHPCompatibility to 9.3.5+ (9fb3244).
- CONTRIB-8031: Detect wrong uses of $PAGE and $OUTPUT in renderers and blocks (Tim Hunt).
- MDLSITE-6093: Don't require MOODLE_INTERNAL check for pure 1-artifact (class, interface, trait) files.
- [PR#65](https://github.com/moodlehq/moodle-local_codechecker/pull/65): PHP 7.4 support.
- [PR#64](https://github.com/moodlehq/moodle-local_codechecker/pull/64): Add Travis support for 38 and 39 branches.
- [PR#62](https://github.com/moodlehq/moodle-local_codechecker/pull/62): Improved command line instructions (Steven McCullagh).

Changes in version 2.9.4 (20191112) - Late Beorc
------------------------------------------------
- [PR#60](https://github.com/moodlehq/moodle-local_codechecker/pull/60): Added support for ``require_admin`` since MDL-58439 (Brendan Heywood).
- CONTRIB-7165: Allow type-hint for foreach variable (Daniel Thee Roperto).
- [PR#58](https://github.com/moodlehq/moodle-local_codechecker/pull/58): Allow tearDownAfterClass as a valid function name (Mikhail Golenkov).
- MDLSITE-5908: Respect eslint configuration comments in JS files (Ruslan Kabalin).
- Various README, thirdpartylib, travis fixes (Tobias Uhmann and others).

Changes in version 2.9.3 (20190118) - Selectivenesses (take #2)
---------------------------------------------------------------
- NOBUG: Some minor changes towards stable travis builds.
- INCOMPETENCE: Fix the version.php information that Eloy missed for 2.9.2 :-)

Changes in version 2.9.2 (20190115) - Selectivenesses
-----------------------------------------------------
- NOBUG: Updated PHP_CodeSniffer to 2.9.2+ (4665f64).
- NOBUG: Updated PHPCompatibility to 9.1.1+ (4487042)
- NOBUG: Added support for PHP 7.3 and Moodle 3.6/3.7dev.
- MDLSITE-5660: Allow spaced, non spaced and nullable return types in function/method declarations.

Changes in version 2.7.2 (20180701) - Barcelona awesomeness
-----------------------------------------------------------
- NOBUG: Upgrade PHPCompatibility to 8.1.0+ (609be5c).
- MDLSITE-2825: Allow obj-op / fluid interfaces + indentation (4 char) checks.
- [PR#46](https://github.com/moodlehq/moodle-local_codechecker/pull/46): Privacy support (null provider).

Changes in version 2.7.1 (20180120) - Slowly yours
--------------------------------------------------
- NOBUG: Bump to moodlerooms/moodle-plugin-ci v2. Apply to 32_STABLE and up in travis conf. Problems with behat forces us to keep it disabled.
- [PR#45](https://github.com/moodlehq/moodle-local_codechecker/pull/45): Upgrade PHPCompatibility to 7.1.5. (Mark Nielsen)
- [PR#41](https://github.com/moodlehq/moodle-local_codechecker/pull/41): Allow newline characters before or after object operators. (Ankit Agarwal)

Changes in version 2.7.0 (20170527) - Boosting you!
---------------------------------------------------
- [PR#40|](https://github.com/moodlehq/moodle-local_codechecker/pull/40): Update PHPCompatibility to 7.1.4. (Mark Nielsen)
- NOBUG: Add instructions for use with Sublime Text editor. (Maria Sorica)
- CONTRIB-6619: Make behat tests pass under Boost.
- MDLSITE-3688: Detect missing login checks and side effects on internal check. (Dan Poltawski)
- NOBUG: Travis: Add support for 32 and 33 stable runs.
- NOBUG: Updated PHP_CodeSniffer to 2.7.0 version.

Changes in version 2.5.4 (20160930) - Internally safe
-----------------------------------------------------
- NOBUG: Updated PHP_CodeSniffer to 2.6.2 version.
- MDLSITE-3688: Verify all files have MOODLE_INTERNAL or require config.php (Dan Poltawski)
- [PR#31](https://github.com/moodlehq/moodle-local_codechecker/pull/31): Avoid PHPUnit's setUpBeforeClass() to generate CS errors. (David Mudrák)
- NOBUG: Adjust php combinations for travis testing on multiple branches.
- [PR#29](https://github.com/moodlehq/moodle-local_codechecker/pull/29): Allow http_response_header global. (Andrew Downes)

Changes in version 2.5.3 (20160608) - Another dent to indent
------------------------------------------------------------
- NOBUG: Add travis support (using the nice [moodle-plugin-ci](https://github.com/moodlerooms/moodle-plugin-ci)
      assistant for Moodle plugins).
- NOBUG: Upgrade PHP_CodeSniffer to 2.6.0 version. (Mark Nielsen)
- CONTRIB-5921: Exclude yui/amd and fixtures by default. (Brendan Heywood)
- CONTRIB-6206: Fixed indentation issues when mixing functions and arrays. Bundled PHP_CodeSniffer upgraded to 2.6.2 dev version.

Changes in version 2.5.2 (20160314) - The March begins...
---------------------------------------------------------
- MDLSITE-4197: Allow backticks within lang strings. Valid Markdown.
- CONTRIB-6146: Better handling of indentation in files having multiple PHP blocks.
    - Moving away from our custom indentation Sniff.
    - Using upstream Generic.Whitespace.ScopeIndent, with open/close tags rooting indentation.
    - Added Generic.Whitespace.DisallowTabIndent (to detect tabs within codechecker).
    - Better coverage of indentation tests.
- NOBUG: Upgrade PHP_CodeSniffer to pre 2.6.0 version (8c5d176).

Changes in version 2.5.1 (20160214) - Valentinius release!
----------------------------------------------------------
- Pull request #19. Minor changes to make it work with CS 2.x (Corey Wallis).
- CONTRIB-5175: Better modifiers matching in regexps.
- CONTRIB-5732: Upgrade to recent version (Mark Nielsen):
    - Upgrade PHP_CodeSniffer to 2.5.1+ (b506fcd).
    - Upgrade PHPCompatibility to PHP 5.6 release.
    - Renew various moodle Sniffs using their updated sources.
    - Fixes to reporting API, unit tests...
    - Allow the UI to skip warnings (warningSeverity).
- CONTRIB-6025, CONTRIB-6105: Allow type hinting phpdoc blocks, supported both in variable/list assignments and variable actions.
- NOBUG: Improve moodle standard unit test coverage.
- NOBUG: Added some UI acceptance tests.

Changes in version 2.3.2 (20140815) - Candelaria, mojo's paradise!
------------------------------------------------------------------
- CONTRIB-5240: Fix CamelCase problems (so it works ok on any OS,
  no matter its file case-sensitiveness).
- NOBUG: Allow clone() (T\_CLONE) to be used as function.
- NOBUG: Add some sensible exclude defaults (Tim Hunt).
- NOBUG: Sort results by path (Tim Hunt).
- CONTRIB-5175: Fixed some regexp false positives (CONTRIB-4146 regression).

Changes in version 2.3.1 (20140707) - San Fermín release!
---------------------------------------------------------
- MDLSITE-2800: Upgrade to CS 1.5.3.
    - Exclude the DefaultTimezoneRequired sniff properly.
    - Upgrade the PHPCompatibility standard to current version.
- CONTRIB-4146: Forbid the use of some functions/operators.
    - extract().
    - eval() - no matter we are aware of few places where they are ok to be used.
    - goto and goto labels.
    - preg\_replace() with /e modifier.
    - backticks shell execution.
    - backticks within strings.
- MDLSITE-3150: Forbid use of AS keyword for table aliasing.

Changes in version 2.3.0 (20140217)
------------------------------------
- CONTRIB-4876: Upgrade to CS 1.5.2.
    - (internal) Changes to use new APIs (sniffs and reporting). Applied to
      the web client, the CLI (run.php) and testing framework.
    - (internal) Renderer modified to work based on a reported xml
      structure (SimpleXMLElement).
    - Added new option to the CLI about running in interactive mode.
    - Beautify the web report, grouping problems per line of code.
- CONTRIB-4742: Fix incorrect thirdpartylibs.xml debugging for Windows.
- CONTRIB-4705: Convert own txt files to markdown.

Changes in version 2.2.9 (20131018)
------------------------------------
- NOBUG: Better instructions for integration with phpStorm (Dan Poltawski).
- NOBUG: Instruct checker about some more valid globals (Sam Hemelryk).
- NOBUG: New sniffs to verify spaces around operators (Ankit Agarwal).
- NOBUG: Internal cleanup.
- CONTRIB-4696: Add support for new 2.6 distributed thirdpartylibs.xml.

Changes in version 2.2.8 (20130713)
------------------------------------
- NOBUG: Update phpcompatibility standard with latest changes (plus testing).
- MDLSITE-2106: Detect underscores in variable names.

Changes in version 2.2.7 (20130606)
------------------------------------
- MDLSITE-2205: Allow 20-120 chars long hyphen commenting-separators.
- NOBUG: Fixed some dev warnings under 2.5.

Changes in version 2.2.6 (20130312)
------------------------------------
- CONTRIB-4160: fail tests if there are unexpected results (errors & warnings).
- CONTRIB-4150: allow phpdocs block before define().
- CONTRIB-4186: fix CSS / rendering of results.
- CONTRIB-3582: allow to specify excluded paths.
- CONTRIB-3562: skip indentation checks on inline html sections within PHP code.

Changes in version 2.2.5 (20130214)
------------------------------------
- CONTRIB-4151: added moodle phpunit support (via local\_codechecker\_testcase).
- CONTRIB-4149: added phpcompatibility sniffs (git://github.com/wimg/PHPCompatibility.git).
- CONTRIB-4145: upgrade to PHPCS 1.4.4.
- CONTRIB-4144: add (this) CHANGES.txt file.

Changes in version v2.2.2 (20120616)
------------------------------------
- add some well known globals to avoid reporting them.
- don't check .xml files (Tim Hunt).

Changes in version v2.2.1 (20120408)
------------------------------------
- maturity stable.
- added plugin icon.
- fixed copy/paste typo @ version.php.
- accept inline comments starting by digit (Tim Hunt).
- improve line length check on non-php files (Tim Hunt).
