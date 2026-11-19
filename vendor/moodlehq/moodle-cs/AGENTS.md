# Agent Instructions: moodle-cs

This repository is the Moodle Coding Style, implemented as PHP_CodeSniffer (PHPCS 4.x, currently requiring `^4.0.4`) standards. It is a PHPCS *standard* package, not an application.

- `moodle/ruleset.xml` – the `moodle` standard (custom sniffs + selected PSR12/Generic/Squiz/PHPCSExtra rules).
- `moodle-extra/ruleset.xml` – the `moodle-extra` standard (stricter, extends `moodle`).
- `moodle/Sniffs/<Category>/<Name>Sniff.php` – custom sniffs (namespace `MoodleHQ\MoodleCS\moodle\Sniffs\<Category>`).
- `moodle/Util/` – shared helpers (`MoodleUtil`, `TokenUtil`, `Docblocks`, `TypeUtil`, ...). Reuse these before writing new token-walking code.
- `moodle/Tests/Sniffs/<Category>/<Name>/<Name>Test.php` with fixtures under `moodle/Tests/Sniffs/<Category>/<Name>/fixtures/<fixtureName>`.
- `moodle/Tests/Standard/` – whole-standard tests. `moodle/Tests/fixtures/integration_test_ci.php` is used by CI integration checks.
- `phpcs.xml.dist` – ruleset used to lint this repo itself. `CHANGELOG.md` – Keep a Changelog format. `docs/ReleaseNewVersion.md` – release process.

## Ground rules

- Never guess sniff names, error codes, or properties. Verify them in the sniff class (`$this->...` public properties, `addError/addWarning/addFixableError` codes) or in `vendor/squizlabs/php_codesniffer` / `vendor/phpcsstandards/*`. Use `vendor/bin/phpcs --standard=moodle -e` to list sniffs.
- Always use fully-qualified references in rulesets: `Standard.Category.Sniff[.Code]` (e.g. `moodle.Files.BoilerplateComment`). Do not pull in whole categories blindly.
- Don't re-declare rules already inherited from an included standard unless changing severity/properties. Use `<exclude>` only to remove a specific sub-rule.
- Support PHP >= 8.0 (CI covers multiple versions and Windows). Avoid syntax or functions newer than that; keep line endings LF and paths OS-agnostic.
- Match the existing code style; the repo lints itself with `vendor/bin/phpcs -ps .`.
- Keep changes surgical; don't refactor unrelated sniffs.

## Validation (run before declaring done)

```bash
vendor/bin/phplint                      # syntax
vendor/bin/phpcs -ps .                  # this repo's own coding style
vendor/bin/phpunit                      # all tests
vendor/bin/phpunit moodle/Tests/Sniffs/<Category>/<Name>   # focused run
xmllint --noout moodle/ruleset.xml moodle-extra/ruleset.xml   # if rulesets were edited
```

CI additionally enforces: >= 80% coverage, and an integration test (`phpcs`/`phpcbf` on `moodle/Tests/fixtures/integration_test_ci.php` with fixed expected exit codes and output). If you change fixable behaviour or ruleset inclusion, check whether that integration fixture still behaves as the workflow expects.

## Writing a new sniff

1. Pick the category dir under `moodle/Sniffs/`; file `<Name>Sniff.php`, class `<Name>Sniff implements Sniff`, with the Moodle GPL header and docblock (`@copyright`, `@license`).
2. Implement `register()` returning the narrowest set of tokens, and `process(File $phpcsFile, $stackPtr)`. Use `$phpcsFile->addError()/addWarning()` (or the `Fixable` variants only when a safe auto-fix exists) with a short, specific error code (e.g. `'Missing'`) and a clear message. Prefer existing `moodle/Util` helpers.
3. Public properties become ruleset-configurable; document them and name them in camelCase as PHPCS requires.
4. Tests (required): create `moodle/Tests/Sniffs/<Category>/<Name>/<Name>Test.php` extending `MoodleCSBaseTestCase` (see `PHP/MemberVarScope` for a model) and a fixture file. Use `setStandard('moodle')`, `setSniff('moodle.<Category>.<Name>')`, `setFixture()`, `setErrors()`, `setWarnings()`, and `verifyCsResults()`. Cover valid cases (no errors), each error code, and edge cases. For fixers, add a `.fixed` expected file as other tests do. Add `@covers` annotations.
5. Register the sniff in `moodle/ruleset.xml` (and `moodle-extra` only if it's meant to be stricter-only), with exclusions/properties as needed. Check that the repo's own `phpcs -ps .` and the integration fixture are unaffected.
6. Add an entry under `## [Unreleased]` in `CHANGELOG.md` (`Added`).

## Fixing bugs in an existing sniff

1. Reproduce first: add a failing fixture case + expected line/error in the sniff's existing test (extend, don't duplicate, existing fixtures). Confirm the test fails for the right reason.
2. Fix the sniff with the smallest change; reuse `Util` helpers. Consider PHP 8+ syntax (attributes, enums, union/nullable/intersection types, readonly, named args, match) and both `phpcs` and `phpcbf` runs when touching fixers.
3. Run the focused test, then the full suite, `phpcs -ps .`, and phplint.
4. Add a `CHANGELOG.md` entry under `## [Unreleased]` (`Fixed`), naming the sniff in backticks.

## Maintaining the repo

- Dependency bumps (PHPCS, PHPCSExtra): update `composer.json`, run `composer update`, then the full validation; fix deprecated/removed sniff references in the rulesets and note them under `Changed`/`Removed` in the changelog.
- Ruleset changes (adding/removing/excluding a sniff, changing properties): validate XML, run the full test suite, and record **why** in `CHANGELOG.md`.
- Don't commit `vendor/`, `coverage/`, `clover.xml`, or cache files.
- Releases follow `docs/ReleaseNewVersion.md`; don't release unless asked.

## Changelog conventions

Keep a Changelog sections (`Added`, `Changed`, `Fixed`, `Removed`) under `## [Unreleased]`, one bullet per change, sniff names in backticks (e.g. `moodle.Commenting.ValidTags`), with the reason when behaviour changes.
