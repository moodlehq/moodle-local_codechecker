# Install using commands
# 
#  docker build .
#  docker images    (get the IMAGE ID)
#  docker tag <IMAGE ID> moodle/phpcs
#
#  Then create a script name phpcs in /usr/local/bin and put following lines

#  #!/bin/bash
#  docker run -v $(pwd):$(pwd) -w=$(pwd) --rm moodle/phpcs phpcs $@


FROM php:5-alpine

ADD moodle /tmp/moodle
ADD PHPCompatibility /tmp/PHPCompatibility

RUN pear install pear/PHP_CodeSniffer-2.7.0


# Set some useful defaults to phpcs
# show_progress - I like to see a progress while phpcs does its magic
# colors - Enable colors; My terminal supports more than black and white
# report_width - I am using a large display so I can afford a larger width
# encoding - Unicode all the way


RUN mv /tmp/moodle $(pear config-get php_dir)/PHP/CodeSniffer/Standards && \
    mv /tmp/PHPCompatibility $(pear config-get php_dir)/PHP/CodeSniffer/Standards && \
    /usr/local/bin/phpcs --config-set show_progress 1 && \
    /usr/local/bin/phpcs --config-set colors 1 && \
    /usr/local/bin/phpcs --config-set report_width 140 && \
    /usr/local/bin/phpcs --config-set encoding utf-8 && \
    /usr/local/bin/phpcs --config-set default_standard moodle

