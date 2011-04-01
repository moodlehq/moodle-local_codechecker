This is a simple script to check that code follows the Moodle coding guidelines.
    http://docs.moodle.org/en/Development:Coding_style

It was originally written by sam marshall of the Open University, and 
was later improved by several other OU developers.

At the moment the tool is very basic, but can easily be extended in 
future.

To install using git, type this command in the root of your Moodle install
    git clone git://github.com/timhunt/moodle-local_codechecker.git local/codechecker
Alternatively, download the zip from
    https://github.com/timhunt/moodle-local_codechecker/zipball/master
unzip it into the local folder, and then rename the new folder to codechecker.

After you have installed this local plugin , you should see a new option 
Site administration -> Development -> Code checker in the settings block.

We hope you find this tool useful. Please feel free to enhance it.
