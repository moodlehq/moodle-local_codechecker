#!/bin/bash

# Bash script for running phpcs with our custom moodle rules only on the affected lines from git-diff.
# To install this script - checkout the codechecker project and symlink this script into your path.
# E.g. ln -s <installdir>/git-compare.sh ~/bin/git-cs
# Then run
# > git-cs
# to see the introduced errors since HEAD or
# > git-cs origin/master
# to see the introduced errors since the upstream master (or any valid git-revision).
#
# You can also set this up as a git alias with branch completion.
# Example:
# First create symlink (I call mine git-cs) of this script into PATH (~/bin)
# Add to .gitconfig [alias] section:
#
# cs = !git-cs $1
#
# Now to get branch completion add this to .bashrc:
#
# _git_cs () {
#    _git_branch
# }
#
# Now (in a new shell) type: git cs [TAB][TAB]
# Profit!


# Start of script...
set -e

if [ ! -z "$SERVER_NAME" ]; then
    # This script must be run from the command line.
    exit 0;
fi

# Quick check for --help as last arg.
LASTARG="${@: -1}"

# Print help if requested.
if [ "$LASTARG" = "--help" ]; then
    echo "$0"
    echo ""
    echo "Run codechecker only of the diff between the current working tree and a specified revision."
    echo ""
    echo "Usage: $0 [git-revision]"
    echo "          Where git-revision represents a git revision or branch to compare the working"
    echo "          tree against. Will default to HEAD if not specified."
    exit 0
fi

# Windows users get no warranty anyway.

# Resolve if this script is a symlink so we can get the path to the phpcs binary.
LINKSOURCE=`readlink $0`
if [ -z "$LINKSOURCE" ]; then
    ME=$0
else
    ME=`dirname $0`/$LINKSOURCE
fi

# Get the install location (where codechecker is checked out).
INSTALLDIR=`dirname $ME`

# Report style - untested with other formats.
REPORT=full

# Phpcs binary.
SCRIPT=$INSTALLDIR/pear/PHP/scripts/phpcs

# Our custom ruleset.
STANDARD=$INSTALLDIR/moodle/ruleset.xml

# The git revision to compare to. HEAD is default.
COMPARISON=$1

if [ -z "$COMPARISON" ]; then
    COMPARISON=HEAD
fi

# What files have changed in this diff?
RELATIVEFILES=`git diff $COMPARISON --name-only`

# Where is the root of the current git repo?
TOPDIR=`git rev-parse --show-toplevel`

# We run phpcs for each changed file separately.
FILES=''
for RELATIVEFILE in $RELATIVEFILES; do

    # Phpcs needs the absolute path.
    ABSOLUTEFILE=$TOPDIR/$RELATIVEFILE

    # Generate some tmp file names - will be cleaned up later.
    TMPFILE=`mktemp /tmp/git-cs-XXXXXX`
    REPORT1=`mktemp /tmp/git-cs-report1-XXXXXX`
    REPORT1NOLINES=`mktemp /tmp/git-cs-report1-nolines-XXXXXX`
    REPORT2=`mktemp /tmp/git-cs-report2-XXXXXX`
    REPORT2NOLINES=`mktemp /tmp/git-cs-report2-nolines-XXXXXX`

    # Checkout a copy of the file from the comparison git revision.
    git show $COMPARISON:$RELATIVEFILE > $TMPFILE

    # Generate phpcs report for the unmodified file.
    $SCRIPT --report=$REPORT --standard=$STANDARD --tab-width=4 $TMPFILE > $REPORT1

    # Generate a nolines version of the report.
    cat $REPORT1 | sed -e 's/[0-9 ]\+|/ /g' > $REPORT1NOLINES

    # Generate phpcs report for the file with our local changes.
    $SCRIPT --report=$REPORT --standard=$STANDARD --tab-width=4 $ABSOLUTEFILE > $REPORT2

    # Generate a nolines version of the report.
    cat $REPORT2 | sed -e 's/[0-9 ]\+|/ /g' > $REPORT2NOLINES

    # Get the lines added/removed from the two sniffer reports in a format we can parse.
    LINESADDED=`diff -n $REPORT1NOLINES $REPORT2NOLINES | egrep "^a|^d" | sed -e 's/ /,/g'`

    # For each line group added/removed...
    OFFSET=0
    for RANGE in $LINESADDED; do
        # Used to check if the lines were added or removed.
        ADDED=`echo -n $RANGE|grep "a"`

        # Strip the leading a/d from the line.
        RANGE=`echo -n $RANGE|sed -s 's/[ad]//g'`

        # Get the first number.
        FIRST=`echo -n $RANGE|sed -e 's/,.*//g'`
        # Get the second number.
        SECOND=`echo -n $RANGE|sed -e 's/.*,//g'`
        
        # If no lines added...
        if [ -z "$ADDED" ]; then
            # Lines Deleted
            OFFSET=`dc -e "$OFFSET $SECOND -p"`
        else
            # We only print the lines added in the report (introduced errors).

            # We keep track of the total lines added/removed up to this point
            # so we can correctly calculate the starting line in the "lines" report.
            # The format required by sed means we need to add 1 to the start line and
            # subtract 1 from the number of lines to show.
            FIRSTADJUSTED=`dc -e "$FIRST $OFFSET 1++p"`
            SECONDADJUSTED=`dc -e "$SECOND 1 - p"`

            # Only print the relevant lines from the "lines" report.
            cat $REPORT2 | sed -n "${FIRSTADJUSTED},+${SECONDADJUSTED}p"

            # Lines added
            OFFSET=`dc -e "$OFFSET $SECOND +p"`
        fi

        # Translate negative numbers to use _ instead of - (this is required by dc).
        OFFSET=`echo $OFFSET | sed -e 's/-/_/g'`

    done;

    # Remove temporary files used.
    rm -f $TMPFILE $REPORT1 $REPORT2 $REPORT1NOLINES $REPORT2NOLINES
done;

