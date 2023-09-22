Instructions to upgrade the moodle-cs bundled version:

- Drop a checkout of https://github.com/moodlehq/moodle-cs.git
  within the "MoodleCS" directory of the plugin. Always removing
  all the previous contents before copying.
- Also, remove not needed stuff, like:
  - All dot (.*) files and directories (git, travis...).
  - Any composer.* and vendor files.
  - All .xml and .dist files.
  - The moodle/Tests directory
- Update the details in thirdpartylibs.xml
- Update the details in this readme

Current checkout:

  3.3.8 (ff540d2)

Local modifications (only allowed if there is a PR upstream backing it):

  - None, right now.

===== ===== ===== ===== ===== ===== =====

Instructions to upgrade the phpcs bundled version:

- Drop a checkout of https://github.com/squizlabs/PHP_CodeSniffer.git
  within the "phpcs" directory of the plugin. Always removing
  all the previous contents before copying, but the CodeSniffer.conf
  file that is needed to autodetect the PHPCompatibility standard.
- Also, remove not needed stuff, like:
  - All dot (.*) files and directories (git, travis...).
  - Any composer.* and vendor files.
  - All .ini, .xsd, .neon and .dist files.
  - The scripts, tests and vendor directories.
- Update the details in thirdpartylibs.xml
- Update the details in this readme

Current checkout:

  3.7.2 (ed8e00df0)

Local modifications (only allowed if there is a PR upstream backing it):

  - None, right now.

===== ===== ===== ===== ===== ===== =====

Instructions to upgrade the PHPCompatibility bundled version:

- Drop a checkout of the PHPCompatibility dir of https://github.com/PHPCompatibility/PHPCompatibility
  within the "PHPCompatibility" directory of the local_codechecker plugin. Always
  removing all the previous contents.
- Don't delete anything. 100% complete drop.
- Update the details in thirdpartylibs.xml
- Update the details in this readme

Current checkout:

  10.0dev (0a17f9ed)

Local modifications (only allowed if there is a PR upstream backing it):

  - Added PHPCSAliases.php to base dir to provide phpcs 2/3 compatibility. Needed
    because still there are a number of old class names within the standard. This
    doesn't have any upstream PR, because the file is there, just we had not needed
    it before the jump to phpcs 3.

===== ===== ===== ===== ===== ===== =====

Instructions to upgrade the PHPCSExtra bundled version:
- Drop a checkout of https://github.com/PHPCSStandards/PHPCSExtra
  within the "PHPCSExtra" directory of the local_codechecker plugin. Always
  removing all the previous content.
- Don't delete anything. 100% complete drop.
- Update the details in thirdpartylibs.xml
- Update the details in this readme

Current checkout:

  1.1.2 (746c319)

===== ===== ===== ===== ===== ===== =====

Instructions to upgrade the PHPCSUtils bundled version:

- Drop a checkout of the PHPCSUtils dir of https://github.com/PHPCSStandards/PHPCSUtils
  within the "PHPCSUtils" directory of the local_codechecker plugin. Always
  removing all the previous contents.
- Don't delete anything. 100% complete drop.
- Update the details in thirdpartylibs.xml
- Update the details in this readme

Current checkout:

  1.0.8 (69465ca)

Local modifications (only allowed if there is a PR upstream backing it):

  - None, right now.

===== ===== ===== ===== ===== ===== =====
