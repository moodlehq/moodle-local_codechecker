Moodle Code Checker
===================

[![Build Status](https://travis-ci.org/moodlehq/moodle-local_codechecker.svg?branch=master)](https://travis-ci.org/moodlehq/moodle-local_codechecker)

Information
-----------

This Moodle plugin uses the Pear CodeSniffer library to
check that code follows the Moodle coding guidelines, available @
<http://docs.moodle.org/en/Development:Coding_style>.

It was created by developers at the Open University, including Sam Marshall,
Tim Hunt and Jenny Gray. It is now maintained by Moodle HQ.

Available releases can be downloaded and installed from
<https://moodle.org/plugins/view.php?plugin=local_codechecker>.

To install it using git, type this command in the root of your Moodle install:

    git clone git://github.com/moodlehq/moodle-local_codechecker.git local/codechecker

Then add /local/codechecker to your git ignore.

Additionally, remember to only use the version of PHPCS located in ``pear/PHP/scripts/phpcs`` rather than installing PHPCS directly. Add the location of the PHPCS executable to your system path, tell PHPCS about the Moodle coding standard with ``phpcs --config-set installed_paths /path/to/moodle-local_codechecker``  and set the default coding standard to Moodle with ``phpcs --config-set default_standard moodle``.  You can now test a file (or folder) with: ``phpcs /path/to/file.php``.

Alternatively, download the zip from
<https://github.com/moodlehq/moodle-local_codechecker/zipball/master>,
unzip it into the local folder, and then rename the new folder to "codechecker".

After you have installed this local plugin, you
should see a new option in the settings block:

> Site administration -> Development -> Code checker

We hope you find this tool useful. Please feel free to enhance it.
Report any idea or bug in [the Tracker](https://tracker.moodle.org/issues/?jql=project%20%3D%20CONTRIB%20AND%20component%20%3D%20%22Local%3A+Code+checker%22), thanks!


IDE Integration
---------------

### Eclipse:

1. Outdated!: If if you use Eclipse for development, you might want to install the PHP CodeSniffer plugin (http://www.phpsrc.org/).
2. Create a new "CodeSniffer standard" in the preferences page.
3. Point it at the moodle directory inside the codechecker folder.
4. Thank Michael Aherne from University of Strathclyde who worked this out!

### PhpStorm

1. Install the phpcs cli tool
2. Open PhpStorm preferences
3. Go to PHP > CodeSniffer and supply the path to the phpcs executable
4. Go to Inspections > PHP > PHP Code Sniffer Validation
5. In the 'coding standard' dropdown select 'custom' and press the [...]
   button next to the path to the coding standard. Point is at the moodle
   directory inside the this plugin directory.

### Sublime Text

Find documentation [here](https://docs.moodle.org/dev/Setting_up_Sublime2#Sublime_PHP_CS).

After step 3 in the Sublime PHP CS section:

1. Go in your Sublime Text to Preferences -> Package Control -> Package Control: Install Package
2. Write 'phpcs' in the search field, if you see Phpcs and SublimeLinter-phpcs, click on them to install them.
3. If not, check if they are already installed Preferences -> Package Control -> Package Control: Remove Package.
4. To set your codecheck to moodle standards go to Preferences -> Package Settings -> PHP Code Sniffer -> Settings-User and write:

        { "phpcs_additional_args": {
                "--standard": "moodle",
                "-n": "
            },
        }

5. If you don’t have the auto-save plugin turned on, YOU’RE DONE!
6. If you have the auto-save plugin turned on, because the codecheck gets triggered on save, the quick panel will keep popping making it impossible to type.
   To stop quick panel from showing go to Settings-User file and add:

        "phpcs_show_quick_panel": false,

   The line with the error will still get marked and if you’ll click on it you’ll see the error text in the status bar.
