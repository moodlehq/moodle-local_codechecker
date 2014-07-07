Changes in version 2.3.1 (20140707) - San Fermín release!
---------------------------------------------------------
- MDLSITE-2800: Upgrade to CS 1.5.3.
    - Exclude the DefaultTimezoneRequired sniff properly.
    - Upgrade the PHPCompatibility standard to current version.
- CONTRIB-4146: Forbid the use of some functions/operators.
    - extract().
    - eval() - no matter we are aware of few places where they are ok to be used.
    - goto and goto labels.
    - preg_replace() with /e modifier.
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
- CONTRIB-4151: added moodle phpunit support (via local_codechecker_testcase).
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
