#!/usr/bin/env bash

npm install
cd tests/server
npm install

pwd
ls -al

(node server.js &) || /bin/true
