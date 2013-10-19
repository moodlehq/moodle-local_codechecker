Moodle Code Checker
===================

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
```
git clone git://github.com/moodlehq/moodle-local_codechecker.git local/codechecker
```
Then add /local/codechecker to your git ignore.

Alternatively, download the zip from
<https://github.com/moodlehq/moodle-local_codechecker/zipball/master>,
unzip it into the local folder, and then rename the new folder to "codechecker".

After you have installed this local plugin, you
should see a new option in the settings block:

> Site administration -> Development -> Code checker

We hope you find this tool useful. Please feel free to enhance it.
Report any idea or bug @
<https://tracker.moodle.org/browse/CONTRIB/component/12130>, thanks!


IDE Integration
---------------

### Eclipse:

1. If if you use Eclipse for development, you might want to install
   the PHP CodeSniffer plugin (http://www.phpsrc.org/).
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
