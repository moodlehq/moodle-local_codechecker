This Moodle plugin uses the Pear CodeSniffer library to check that code follows
the Moodle coding guidelines http://docs.moodle.org/en/Development:Coding_style

It was created by developers at the Open University, including sam marshall,
Tim Hunt and Jenny Gray.

To install using git, type this command in the root of your Moodle install
    git clone git://github.com/timhunt/moodle-local_codechecker.git local/codechecker
Then add /local/codechecker to your git ignore.

Alternatively, download the zip from
    https://github.com/timhunt/moodle-local_codechecker/zipball/master
unzip it into the local folder, and then rename the new folder to codechecker.

After you have installed this local plugin , you should see a new option 
Site administration -> Development -> Code checker in the settings block.

We hope you find this tool useful. Please feel free to enhance it.
