# Change log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](https://semver.org/).

The format of this change log follows the advice given at [Keep a CHANGELOG](https://keepachangelog.com).

## [Unreleased]
### Changed
- Switch changelog format

## [v5.2.1] - 2026-11-20 - A smoother route to release
### Added
- Introduce plugin release workflow with Moodle Marketplace.

## [v5.2.0] - 2026-11-20 - A bump in the code
### Changed
- Updated styling for Moodle 5.3 and up.
- Updated CI to test current supported Moodle branches and PHP versions.
- Bundled dependencies updated:
  - Moodle CS v3.9.1.

## [v5.1.0] - 2025-12-02 - No longer compatible
### Changed
- Bundled dependencies updated:
  - Moodle CS v3.7.0
- Bundled dependencies removed:
  - PHPCompatibility

## [v5.0.13] - 2025-09-16 - I depend upon you!
### Changed
- Bundled dependencies updated:
  - Moodle CS v3.6.0

## [v5.0.12] - 2025-08-15 - Git excludes thee
### Changed
- Fixed Bundled dependencies

## [v5.0.11] - 2025-08-14 - I attribute this change to PHPUnit
### Changed
- Bundled dependencies updated:
  - Moodle CS v3.5.2

## [v5.0.10] - 2025-07-30 - Too soon?
### Changed
- Fixed bundled dependencies

## [v5.0.9] - 2025-07-30 - Long time no bump
### Changed
- Bundled dependencies updated:
  - Moodle CS v3.5.1

## [v5.0.8] - 2024-07-05 - Refreshing summer
### Changed
- Bundled dependencies updated:
  - Moodle CS v3.4.10

## [v5.0.7] - 2024-06-14 - It's a small world
### Changed
- Bundled dependencies updated:
  - Moodle CS v3.4.8

## [v5.0.6] - 2024-05-31 - Bye, May!
### Changed
- Bundled dependencies updated:
  - Moodle CS v3.4.7
  - PHP Compatibility 96072c30
  - PHP CodeSniffer 3.10.1
  - PHPCS Utils 1.0.12

## [v5.0.5] - 2024-04-10 - Fools plus 9
### Changed
- Bundled dependencies updated:
  - Moodle CS v3.4.6
  - PHP Compatibility e5cd2e24
  - PHP CodeSniffer 3.9.1
  - PHPCS Utils 1.0.10

## [v5.0.4] - 2024-01-26 - Depend-a-bot
### Changed
- First steps towards managing dependencies automatically with Dependabot.
- Bundled dependencies updated:
  - Moodle CS updated to v3.3.14.
  - PHP CodeSniffer updated to 3.8.1.

## [v5.0.3] - 2023-12-19 - Bumping bumps
### Changed
- Bundled dependencies updated:
  - PHP CodeSniffer updated to 3.8.0.
  - PHP Compatibility updated to 306cd263.
  - PHPCS Extra updated to 1.2.1.
  - PHPCS Utils updated to 1.0.9.
  - Moodle CS updated to v3.3.12.

## [v5.0.2] - 2023-11-29 - Mainly main
### Changed
- This repository has moved its default branch [from "master" to "main"](https://github.com/moodlehq/moodle-local_codechecker/issues/225).
- Tests updated to suit Moodle 4.4dev requirements.
- Bundled dependencies updated:
  - [moodle-cs](https://github.com/moodlehq/moodle-cs) updated to [v3.3.11](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.11).

## [v5.0.1] - 2023-10-20 - Daddy's birthday
### Changed
- Added `MOODLE_403_STABLE` tests.
- Bundled dependencies updated:
  - [moodle-cs](https://github.com/moodlehq/moodle-cs) updated to [v3.3.10](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.10).

## [v5.0.0] - 2023-09-26 - Cinque, this is cinque!
### Changed
- IMPORTANT - New (raised) requirements:
  - PHP 7.4 and up supported (previously PHP 7.0 and up).
  - Moodle v3.8.3 and up supported (previously Moodle v3.7.0).
- Changed the process to build the plugin. Until now it was a tedious process, manually copying all the dependencies within the plugin (`PHP_CodeSniffer`, `PHPCompatibility`, `PHPCSExtra`, `PHPCSUtil` and `moodle-cs`). Now all the dependencies are managed via Composer and the plugin comes with the `vendor` directory ready to be used.
- This change has been applied for improving the release process, and towards 100% automating it, now that the plugin is just a Web/CLI wrapper over all the tools listed above.
- The old 4.x series will not receive more updates (unless absolutely needed).
- Bundled dependencies updated:
  - [moodle-cs](https://github.com/moodlehq/moodle-cs) updated to [v3.3.9](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.9).

## [v4.0.8] - 2023-09-23 - Few hours later
### Changed
- Bundled dependencies updated:
  - [moodle-cs](https://github.com/moodlehq/moodle-cs) updated to [v3.3.8](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.8).
  - [PHPCSExtra](https://github.com/PHPCSStandards/PHPCSExtra) updated to [1.1.2](https://github.com/PHPCSStandards/PHPCSExtra/releases/tag/1.1.2). It was missing in the previous release!

## [v4.0.7] - 2023-09-22 - Day after Saint Matthew here!
### Changed
- Bundled dependencies updated:
  - [moodle-cs](https://github.com/moodlehq/moodle-cs) updated to [v3.3.7](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.7).

## [v4.0.6] - 2023-09-08 - Composing our lifes
### Changed
- Various small internal CI changes:
  - PHP 8.2 support.
- Bundled dependencies updated:
  - [PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibility) updated to 10.0dev (0a17f9ed).
  - [PHPCSUtils](https://github.com/PHPCSStandards/PHPCSUtils) updated to 1.0.8.
  - [moodle-cs](https://github.com/moodlehq/moodle-cs) updated to [v3.3.5](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.5).

## [v4.0.5] - 2023-05-28 - Forever in my heart, Tina
### Changed
- Various small internal CI changes:
  - 402_STABLE support.
  - [moodle-plugin-ci](https://github.com/moodlehq/moodle-plugin-ci) updated to [v4.0.0](https://github.com/moodlehq/moodle-plugin-ci/releases/tag/4.0.0).
- Bundled dependencies updated:
    - [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) updated to 3.7.2.
    - [PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibility) updated to 10.0dev (70e4ca24).
    - [PHPCSUtils](https://github.com/PHPCSStandards/PHPCSUtils) updated to 1.0.5.
    - [moodle-cs](https://github.com/moodlehq/moodle-cs) updated to [v3.3.4](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.4).

## [v4.0.4] - 2023-03-14 - Forth and back
### Changed
- Update the [moodle-cs](https://github.com/moodlehq/moodle-cs) dependency to [v3.3.3](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.3).

## [v4.0.3] - 2023-02-14 - Valentine's D
### Changed
- [PR#214](https://github.com/moodlehq/moodle-local_codechecker/pull/214): Update the [moodle-cs](https://github.com/moodlehq/moodle-cs) dependency to [v3.3.2](https://github.com/moodlehq/moodle-cs/releases/tag/v3.3.2).

## [v4.0.2] - 2023-01-20 - Comp-at-ibility
### Changed
- Various small internal changes:
  - 401_STABLE CI support.
  - Prepare for 4.2 new requirements.
  - PHP 8.1 fixes.
- Bundled dependencies overhaul:
    - [moodle-cs](https://github.com/moodlehq/moodle-cs) updated to 3.3.1 ([from 3.2.4](https://github.com/moodlehq/moodle-cs/compare/v3.2.4...v3.3.1)).
    - [PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibility) updated to 10.0dev (2fb82334).
    - [PHPCSUtils](https://github.com/PHPCSStandards/PHPCSUtils) updated to 1.0.1.

## [v4.0.1] - 2022-10-07 - Dear Windows
### Changed
- [PR#205](https://github.com/moodlehq/moodle-local_codechecker/pull/205): Allow CRLF (Windows) line ending in .csv testing fixtures (stronk7).
- [PR#206](https://github.com/moodlehq/moodle-local_codechecker/pull/206): Fix various links to point to the [new coding style docs](https://moodledev.io/general/development/policies/codingstyle) (Tim Hunt).
- [PR#209](https://github.com/moodlehq/moodle-local_codechecker/pull/209): Solve a problem, specially noticeable under Windows, where core's PHP_CodeSniffer configuration was interfering with codechecker runs (stronk7).

## [v4.0.0] - 2022-08-25 - Welcome moodle-cs
### Changed
- [PR#192](https://github.com/moodlehq/moodle-local_codechecker/pull/192), [PR#197](https://github.com/moodlehq/moodle-local_codechecker/pull/197): Various fixes to own CI tests (stronk7).
- [PR#193](https://github.com/moodlehq/moodle-local_codechecker/pull/193): Move to use the [moodle-cs](https://github.com/moodlehq/moodle-cs) standard that is now the source for everything else and should be the one used for all (tools, editors, IDEs...) integration. (Andrew Lyons and stronk7). This change includes, noticeably:
    - PHP_CodeSniffer updated to current 3.7.1.
    - moodle-cs updated to current 3.2.4+.
    - Adjust tests, docs and other bits to keep everything working.
- [PR#199](https://github.com/moodlehq/moodle-local_codechecker/pull/199): Correct handling of empty files (Michael Milette).
- [PR#200](https://github.com/moodlehq/moodle-local_codechecker/pull/200): Fix GitHub repository URLs (David Mudrák).

## [v3.1.0] - 2022-02-25 - Fondant chocolate
### Changed
- [PR#176](https://github.com/moodlehq/moodle-local_codechecker/pull/176): Avoid some `use` statements to make the `MOODLE_INTERNAL` check to be required (stronk7).
- [PR#177](https://github.com/moodlehq/moodle-local_codechecker/pull/177): Make it possible to show the erroring standard/sniff/rule in the UI executions (Ruslan Kabalin).
- [PR#178](https://github.com/moodlehq/moodle-local_codechecker/pull/178): Warn, for Moodle 4.0 and up, about [unit tests missing coverage information](https://docs.moodle.org/dev/Writing_PHPUnit_tests#Code_coverage) (stronk7).
- [PR#180](https://github.com/moodlehq/moodle-local_codechecker/pull/180): Allow `@codeCoverageIgnore` annotations in inline comments (stronk7).
- [PR#181](https://github.com/moodlehq/moodle-local_codechecker/pull/181): Apply the `ValidFunctionName` sniff to all scoped tokens (stronk7).
- [PR#183](https://github.com/moodlehq/moodle-local_codechecker/pull/183): Ensure that the "other" checks do observe UI defined exclusions (stronk7).
- [PR#186](https://github.com/moodlehq/moodle-local_codechecker/pull/186): Add support for installing via Composer (Andrew Lyons).
- [PR#190](https://github.com/moodlehq/moodle-local_codechecker/pull/190): Control spacing around array indexes (stronk7).
- [PR#191](https://github.com/moodlehq/moodle-local_codechecker/pull/191): Fix a problem about not detecting relevant code in files without any artifact (stronk7).
- Various small fixes and tweaks.

## [v3.0.6] - 2022-01-17 - January's crunch (take 2)
### Changed
- [PR#174](https://github.com/moodlehq/moodle-local_codechecker/pull/174): Lower the unexpected MOODLE_INTERNAL check to be a warning, instead of error, as originally planned (stronk7).

## [v3.0.5] - 2022-01-11 - January's crunch
### Changed
- PHP_CodeSniffer upgraded to 3.6.2 release (stronk7).
- [PR#164](https://github.com/moodlehq/moodle-local_codechecker/pull/164): Make testcase class names to be fixable by `phpcbf` (stronk7).
- [PR#167](https://github.com/moodlehq/moodle-local_codechecker/pull/167): Verify that tescase location matches sub-namespace definition (stronk7).
- [PR#168](https://github.com/moodlehq/moodle-local_codechecker/pull/168): Check class opening curly brackets correctness (kabalin).
- [PR#169](https://github.com/moodlehq/moodle-local_codechecker/pull/169): Report unexpected MOODLE_INTERNAL uses (stronk7).
- [PR#172](https://github.com/moodlehq/moodle-local_codechecker/pull/172): Allow phpcs:xxx annotations in the first line of files (stronk7).
- Small doc changes and typo fixes here and there.

## [v3.0.4] - 2021-12-04 - Downgrading expectations
### Changed
- [PR#162](https://github.com/moodlehq/moodle-local_codechecker/pull/162): Downgrade some recently code added to the "moodle" standard to make it PHP 7.0 compliant, needed to continue supporting old branches by various tools (stronk7).

## [v3.0.3] - 2021-11-30 - Cyber releasing
### Changed
- Various internal changes and improvements:
    - Own conformance with new [PHPUnit naming rules](https://docs.moodle.org/dev/PHPUnit_integration#Actual_.28Moodle_3.11_and_up.29).
    - New [MoodleUtil](https://github.com/moodlehq/moodle-local_codechecker/blob/main/moodle/Util/MoodleUtil.php) class to be able to detect Moodle dir root, branch and components (calculated or imported) within code checker. This new awareness will help improving various sniffs.
    - Drop some Moodle own &lt; 3.7 testing. Code checker continues supporting Moodle 3.4 and up.
    - Improvements to the base test case, now able to automatically verify `phpcbf` fixes.
    - Bye, bye to Travis for self-tests. Now relying only on GHA.
- [PR#155](https://github.com/moodlehq/moodle-local_codechecker/pull/155): Make some common errors to be fixable by `phpcbf` (cameron1729).
- [PR#158](https://github.com/moodlehq/moodle-local_codechecker/pull/158): Fixes for the MOODLE_INTERNAL sniff to better detect some allowed exceptions (stronk7).
- [PR#161](https://github.com/moodlehq/moodle-local_codechecker/pull/161): New sniff to follow [PHPUnit files, classes and namespaces  naming rules](https://docs.moodle.org/dev/PHPUnit_integration#Actual_.28Moodle_3.11_and_up.29) (stronk7).

## [v3.0.2] - 2021-07-16 - Summer break
### Changed
- Various internal changes and improvements:
    - Removed some legacy code (33_STABLE) and out of support 7.1 tests.
    - Disable coverage reporting in Travis own builds.
    - Added PHP 8.0 support in own tests.
    - Fulfill Moodle 4.0 requirements.
    - Make mariadb own tests sticky to 10.5 until [MDL-72131](https://tracker.moodle.org/browse/MDL-72131) is fixed.
- [PR#146](https://github.com/moodlehq/moodle-local_codechecker/pull/146): Suggest debugging() as alternative to error_log() (Ruslan Kabalin).
- [PR#148](https://github.com/moodlehq/moodle-local_codechecker/pull/148): Verify that there is one and only one EOL @ EOF (stronk7).

## [v3.0.1] - 2021-04-23 - April's cool
### Changed
- Various internal changes and improvements:
    - Travis and GHA support updated.
    - Support docker login to workaround anonymous pull limits.
    - Added instructions to work with VSCode.
    - Apply for own `coverage.php` to better define coverage reporting.
- [PR#132](https://github.com/moodlehq/moodle-local_codechecker/pull/132): jsonSerialize() is now a valid function name (Tobias Goltz).
- [PR#136](https://github.com/moodlehq/moodle-local_codechecker/pull/136): Added support for --exclude option in the CLI runner (Adrian Perez).
- [PR#139](https://github.com/moodlehq/moodle-local_codechecker/pull/139): Added check for `abstract/final/static` positioning in function declarations (stronk7).
- [PR#141](https://github.com/moodlehq/moodle-local_codechecker/pull/141): Deprecated `print_error()` (stronk7).
- [PR#143](https://github.com/moodlehq/moodle-local_codechecker/pull/143): Added support for `return new class extends` PHPDoc blocks (stronk7).

## [v3.0.0] - 2020-11-27 - Welcome phpcs 3
### Changed
- Upgrade to PHP_CodeSniffer 3.5.8 (stronk7):
    - PHP_CodeSniffer move to phpcs directory (see readme_moodle.txt for complete instructions).
    - Move Moodle sniffs to PHP_CodeSniffer 3.
    - Create own runner/report.
    - Move as much as possible from locallib.php to classes.
    - Add PHPUnit 8 compatibility, keeping PHPUnit 6 working.
    - Min Moodle version required increases from 3.2 to 3.4.
- [PR#90](https://github.com/moodlehq/moodle-local_codechecker/pull/90): Adapt custom PHP_CodeSniffer runner (classes/runner.php) to use PHP_CodeSniffer 3 API (Sara Arjona).
- [PR#95](https://github.com/moodlehq/moodle-local_codechecker/pull/95): Unlock session before processing files (Víctor Déniz).

## [v2.9.8] - 2020-10-02 - Bye and thank you, phpcs 2.x
### Changed
- [PR#83](https://github.com/moodlehq/moodle-local_codechecker/pull/83) and [PR#84](https://github.com/moodlehq/moodle-local_codechecker/pull/84): Allow a list of files to be checked (Sam Marshall).
- [PR#80](https://github.com/moodlehq/moodle-local_codechecker/pull/80): Remove require of now-deleted coursecatlib.php (Jun Pataleta).
- Several travis changes:
    - [PR#79](https://github.com/moodlehq/moodle-local_codechecker/pull/79): Bump to use moodlehq/moodle-plugin-ci v3.
    - [PR#81](https://github.com/moodlehq/moodle-local_codechecker/pull/81): Add support for 310 branch.
- Small README changes: information section improved and travis status badge update to show travis.com build status.

## [v2.9.7] - 2020-07-18 - Bye bye, JS
### Changed
- [PR#75](https://github.com/moodlehq/moodle-local_codechecker/pull/75) and [PR#77](https://github.com/moodlehq/moodle-local_codechecker/pull/77): Small tweaks for better transition to phpcs3.
- CONTRIB-8122: Stop processing non-php files (runner and UI).

## [v2.9.6] - 2020-06-11 - June's Roentgenium
### Changed
- [PR#63](https://github.com/moodlehq/moodle-local_codechecker/pull/63): Make MOODLE_INTERNAL check declare() aware (Peter Burnett).
- [PR#72](https://github.com/moodlehq/moodle-local_codechecker/pull/72): Minor php74 fixes and travis refactor.

## [v2.9.5] - 2020-04-01 - Poisson d'avril
### Changed
- CONTRIB-8024: Process all files as UTF-8 encoded (defined @ standard level).
- CONTRIB-6175: Only process PHP files (defined @ standard level).
- [PR#69](https://github.com/moodlehq/moodle-local_codechecker/pull/69): Upgrade PHPCompatibility to 9.3.5+ (9fb3244).
- CONTRIB-8031: Detect wrong uses of $PAGE and $OUTPUT in renderers and blocks (Tim Hunt).
- MDLSITE-6093: Don't require MOODLE_INTERNAL check for pure 1-artifact (class, interface, trait) files.
- [PR#65](https://github.com/moodlehq/moodle-local_codechecker/pull/65): PHP 7.4 support.
- [PR#64](https://github.com/moodlehq/moodle-local_codechecker/pull/64): Add Travis support for 38 and 39 branches.
- [PR#62](https://github.com/moodlehq/moodle-local_codechecker/pull/62): Improved command line instructions (Steven McCullagh).

## [v2.9.4] - 2019-11-12 - Late Beorc
### Changed
- [PR#60](https://github.com/moodlehq/moodle-local_codechecker/pull/60): Added support for ``require_admin`` since MDL-58439 (Brendan Heywood).
- CONTRIB-7165: Allow type-hint for foreach variable (Daniel Thee Roperto).
- [PR#58](https://github.com/moodlehq/moodle-local_codechecker/pull/58): Allow tearDownAfterClass as a valid function name (Mikhail Golenkov).
- MDLSITE-5908: Respect eslint configuration comments in JS files (Ruslan Kabalin).
- Various README, thirdpartylib, travis fixes (Tobias Uhmann and others).

## [v2.9.3] - 2019-01-18 - Selectivenesses (take #2)
### Changed
- NOBUG: Some minor changes towards stable travis builds.
- INCOMPETENCE: Fix the version.php information that Eloy missed for 2.9.2 :-)

## [v2.9.2] - 2019-01-15 - Selectivenesses
### Changed
- NOBUG: Updated PHP_CodeSniffer to 2.9.2+ (4665f64).
- NOBUG: Updated PHPCompatibility to 9.1.1+ (4487042)
- NOBUG: Added support for PHP 7.3 and Moodle 3.6/3.7dev.
- MDLSITE-5660: Allow spaced, non spaced and nullable return types in function/method declarations.

## [v2.7.2] - 2018-07-01 - Barcelona awesomeness
### Changed
- NOBUG: Upgrade PHPCompatibility to 8.1.0+ (609be5c).
- MDLSITE-2825: Allow obj-op / fluid interfaces + indentation (4 char) checks.
- [PR#46](https://github.com/moodlehq/moodle-local_codechecker/pull/46): Privacy support (null provider).

## [v2.7.1] - 2018-01-20 - Slowly yours
### Changed
- NOBUG: Bump to moodlerooms/moodle-plugin-ci v2. Apply to 32_STABLE and up in travis conf. Problems with behat forces us to keep it disabled.
- [PR#45](https://github.com/moodlehq/moodle-local_codechecker/pull/45): Upgrade PHPCompatibility to 7.1.5. (Mark Nielsen)
- [PR#41](https://github.com/moodlehq/moodle-local_codechecker/pull/41): Allow newline characters before or after object operators. (Ankit Agarwal)

## [v2.7.0] - 2017-05-27 - Boosting you!
### Changed
- [PR#40|](https://github.com/moodlehq/moodle-local_codechecker/pull/40): Update PHPCompatibility to 7.1.4. (Mark Nielsen)
- NOBUG: Add instructions for use with Sublime Text editor. (Maria Sorica)
- CONTRIB-6619: Make behat tests pass under Boost.
- MDLSITE-3688: Detect missing login checks and side effects on internal check. (Dan Poltawski)
- NOBUG: Travis: Add support for 32 and 33 stable runs.
- NOBUG: Updated PHP_CodeSniffer to 2.7.0 version.

## [v2.5.4] - 2016-09-30 - Internally safe
### Changed
- NOBUG: Updated PHP_CodeSniffer to 2.6.2 version.
- MDLSITE-3688: Verify all files have MOODLE_INTERNAL or require config.php (Dan Poltawski)
- [PR#31](https://github.com/moodlehq/moodle-local_codechecker/pull/31): Avoid PHPUnit's setUpBeforeClass() to generate CS errors. (David Mudrák)
- NOBUG: Adjust php combinations for travis testing on multiple branches.
- [PR#29](https://github.com/moodlehq/moodle-local_codechecker/pull/29): Allow http_response_header global. (Andrew Downes)

## [v2.5.3] - 2016-06-08 - Another dent to indent
### Changed
- NOBUG: Add travis support (using the nice [moodle-plugin-ci](https://github.com/moodlerooms/moodle-plugin-ci)
      assistant for Moodle plugins).
- NOBUG: Upgrade PHP_CodeSniffer to 2.6.0 version. (Mark Nielsen)
- CONTRIB-5921: Exclude yui/amd and fixtures by default. (Brendan Heywood)
- CONTRIB-6206: Fixed indentation issues when mixing functions and arrays. Bundled PHP_CodeSniffer upgraded to 2.6.2 dev version.

## [v2.5.2] - 2016-03-14 - The March begins...
### Changed
- MDLSITE-4197: Allow backticks within lang strings. Valid Markdown.
- CONTRIB-6146: Better handling of indentation in files having multiple PHP blocks.
    - Moving away from our custom indentation Sniff.
    - Using upstream Generic.Whitespace.ScopeIndent, with open/close tags rooting indentation.
    - Added Generic.Whitespace.DisallowTabIndent (to detect tabs within codechecker).
    - Better coverage of indentation tests.
- NOBUG: Upgrade PHP_CodeSniffer to pre 2.6.0 version (8c5d176).

## [v2.5.1] - 2016-02-14 - Valentinius release!
### Changed
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

## [v2.3.2] - 2014-08-15 - Candelaria, mojo's paradise!
### Changed
- CONTRIB-5240: Fix CamelCase problems (so it works ok on any OS,
  no matter its file case-sensitiveness).
- NOBUG: Allow clone() (T\_CLONE) to be used as function.
- NOBUG: Add some sensible exclude defaults (Tim Hunt).
- NOBUG: Sort results by path (Tim Hunt).
- CONTRIB-5175: Fixed some regexp false positives (CONTRIB-4146 regression).

## [v2.3.1] - 2014-07-07 - San Fermín release!
### Changed
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

## [v2.3.0] - 2014-02-17
### Changed
- CONTRIB-4876: Upgrade to CS 1.5.2.
    - (internal) Changes to use new APIs (sniffs and reporting). Applied to
      the web client, the CLI (run.php) and testing framework.
    - (internal) Renderer modified to work based on a reported xml
      structure (SimpleXMLElement).
    - Added new option to the CLI about running in interactive mode.
    - Beautify the web report, grouping problems per line of code.
- CONTRIB-4742: Fix incorrect thirdpartylibs.xml debugging for Windows.
- CONTRIB-4705: Convert own txt files to markdown.

## [v2.2.9] - 2013-10-18
### Changed
- NOBUG: Better instructions for integration with phpStorm (Dan Poltawski).
- NOBUG: Instruct checker about some more valid globals (Sam Hemelryk).
- NOBUG: New sniffs to verify spaces around operators (Ankit Agarwal).
- NOBUG: Internal cleanup.
- CONTRIB-4696: Add support for new 2.6 distributed thirdpartylibs.xml.

## [v2.2.8] - 2013-07-13
### Changed
- NOBUG: Update phpcompatibility standard with latest changes (plus testing).
- MDLSITE-2106: Detect underscores in variable names.

## [v2.2.7] - 2013-06-06
### Changed
- MDLSITE-2205: Allow 20-120 chars long hyphen commenting-separators.
- NOBUG: Fixed some dev warnings under 2.5.

## [v2.2.6] - 2013-03-12
### Changed
- CONTRIB-4160: fail tests if there are unexpected results (errors & warnings).
- CONTRIB-4150: allow phpdocs block before define().
- CONTRIB-4186: fix CSS / rendering of results.
- CONTRIB-3582: allow to specify excluded paths.
- CONTRIB-3562: skip indentation checks on inline html sections within PHP code.

## [v2.2.5] - 2013-02-14
### Changed
- CONTRIB-4151: added moodle phpunit support (via local\_codechecker\_testcase).
- CONTRIB-4149: added phpcompatibility sniffs (git://github.com/wimg/PHPCompatibility.git).
- CONTRIB-4145: upgrade to PHPCS 1.4.4.
- CONTRIB-4144: add (this) CHANGES.txt file.

## [v2.2.2] - 2012-06-16
### Changed
- add some well known globals to avoid reporting them.
- don't check .xml files (Tim Hunt).

## [v2.2.1] - 2012-04-08
### Changed
- maturity stable.
- added plugin icon.
- fixed copy/paste typo @ version.php.
- accept inline comments starting by digit (Tim Hunt).
- improve line length check on non-php files (Tim Hunt).
