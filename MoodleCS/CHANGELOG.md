# Change log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

The format of this change log follows the advice given at [Keep a CHANGELOG](http://keepachangelog.com).

## [Unreleased]

## [v3.3.1] - 2023-01-19
### Fixed
- Updated the outdated list of valid magic methods.

## [v3.3.0] - 2023-01-13
### Added
- Enforce the use of `&&` and `||` logical operators, warning about `and` and `or`: `Squiz.Operators.ValidLogicalOperators`

### Changed
- Many internal changes towards better self-testing and integration with other tools ([GH workflows](https://github.com/moodlehq/moodle-cs/actions), codechecker, core, phpunit...).
- Upgraded the [PHPCompatibility standard](https://github.com/PHPCompatibility/PHPCompatibility) from 3 years old version 9.3.5 (no releases since then) to current development version.

### Fixed
- Stop considering `class_alias` like a side effect.
- Add back the `Squiz.Arrays.ArrayBracketSpacing` sniff.

## v3.2.0 - 2022-02-28
This release is the first release of the new [moodlehq/moodle-cs](https://packagist.org/packages/moodlehq/moodle-plugin-ci) packages.

These rules, in an identical form, were previously available as a part of the [local_codechecker Moodle plugin](https://moodle.org/plugins/local_codechecker)  but are being moved to their own repository to make installation friendlier for developers.

All features are maintained and no new features have been introduced to either the rules, or the sniffs.

All the details about [previous releases] can be found in [local_codechecker](https://github.com/moodlehq/moodle-local_codechecker) own change log.

[Unreleased]: https://github.com/moodlehq/moodle-cs/compare/v3.3.1...main
[v3.3.1]: https://github.com/moodlehq/moodle-cs/compare/v3.3.0...v3.3.1
[v3.3.0]: https://github.com/moodlehq/moodle-cs/compare/v3.2.0...v3.3.0
[Previous releases]: https://github.com/moodlehq/moodle-local_codechecker/blob/master/CHANGES.md#changes-in-version-400-20220825---welcome-moodle-cs
