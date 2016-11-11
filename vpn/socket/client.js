//solution for SSL beside ignoring : https://github.com/request/request#tlsssl-protocol
process.env['NODE_TLS_REJECT_UNAUTHORIZED'] = '0';
//require('ssl-root-cas').inject();
var WebSocket = require('ws');
  var fs = require('fs');





//var ws = new WebSocket('wss://localhost:8080');
//var ws = new WebSocket('wss://45.32.184.118:8081');
var ws = new WebSocket('wss://mybeta.xyz:8081');


ws.on('open', function open() {
  ws.send('something');
});


ws.on('message', function(data, flags) {
  // flags.binary will be set if a binary data is received.
  // flags.masked will be set if the data was masked.
});





    ws.on('message', function (message,flags) {
// When we receive a message handel here ....


    });

     ws.on('close', function (code, message) {
// When we receive a message handel here ....


    });

      ws.on('error', function (error)  {
// When we receive a message handel here ....


    });


     ws.on('ping', function (data, flags) {
// When we receive a message handel here ....


    });


     ws.on('pong', function (data, flags) {
// When we receive a message handel here ....


    });

