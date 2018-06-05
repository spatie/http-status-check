#!/usr/bin/env bash

npm install

if [ -z ${TRAVIS_JOB_ID} ]; then
    # not running under travis, stay in foreground until stopped
    node server.js
else
    cd tests/server

    # running under travis, daemonize
    (node server.js &) || /bin/true
fi
