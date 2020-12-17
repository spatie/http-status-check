"use strict";

let app = require('express')();

app.get('/', function (request, response) {
    response.end('<a href="/link1">Link1</a><a href="/link2">Link2</a><a href="/link4">Link4</a><a href="mailto:test@example.com">Email</a>');
});

app.get('/link1', function (request, response) {
    response.end('You are on link1<a href="http://example.com/">Example.com</a>');
});

app.get('/link2', function (request, response) {
    response.end('You are on link2<a href="/link3">Link3</a>');
});

app.get('/link3', function (request, response) {
    response.end('You are on link3<a href="/notExists">not exists</a>');
});

app.get('/link4', function (request, response) {
    response.redirect('/link1');
});

app.get('/redirectToNotFound', function (request, response) {
    response.redirect('/notExists2');
});

let server = app.listen(8080, function () {
    const host = 'localhost';
    const port = server.address().port;

    console.log('Testing server listening at http://%s:%s', host, port);
});
