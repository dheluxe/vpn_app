//Documentation is here : https://github.com/websockets/ws/blob/master/doc/ws.md

(function () {
    'use strict';
    var fs = require('fs');
    // you'll probably load configuration from config
    var cfg = {
        ssl: true,
        port: 8081,
        ssl_key: 'srvr.key',
        ssl_cert: 'srvr.crt',
        ssl_ca: 'srvr.csr'
    };
    var httpServ = (cfg.ssl) ? require('https') : require('http');
    var WebSocketServer = require('ws').Server;
    var app = null;
    var clients={};

    var mysql=require('mysql');
    var connection = mysql.createConnection({
        host    :'localhost',
        port : 3306,
        user : 'root',
        password : 'kdcsev113$db',
        database:'newvpn'
    });

    // dummy request processing
    var processRequest = function (req, res) {
        res.writeHead(200);
        res.end('All glory to WebSockets!\n');
    };
    if (cfg.ssl) {
        app = httpServ.createServer({
            // providing server with SSL key/cert
            key: fs.readFileSync(cfg.ssl_key),
            cert: fs.readFileSync(cfg.ssl_cert)
        }, processRequest).listen(cfg.port);
    } else {
        app = httpServ.createServer(processRequest).listen(cfg.port);
    }

    // passing or reference to web server so WS would knew port and SSL capabilities
    var wss = new WebSocketServer({ server: app });
    console.log('wss here!');
    wss.on('connection', function (wsConnect) {
        //On receiving a new connection ...
        console.log("We got a Customer YO");

        // Broadcast to all.
        wss.broadcast = function broadcast(data) {
            wss.clients.forEach(function each(client) {
                client.send(data);
            });
        };

        /*wss.on('connection', function connection(ws) {
            console.log('start message');
            console.log(ws);
            ws.on('message', function message(data) {
                // Broadcast to everyone else.
                console.log("we got a message for connection: " + data);
                wss.clients.forEach(function each(client) {
                    console.log('ttt');
                    console.log(ws);
                    if (client == ws) client.send(data);
                });
            });
        });*/

        wsConnect.on('message', function (message,flags){
            // When we receive a message handel here ....
            console.log("we got a message YO-: " + message);

            var msg_obj=JSON.parse(message);
            console.log(msg_obj);

            if(msg_obj.message_type=="request"){
                if(msg_obj.type=="authorize"){
                    clients[msg_obj.value.token]=wsConnect;
                }
                if(msg_obj.type=="get_tunnels"){
                    /*var query = connection.query('select * from customers_data',function(err,rows){
                        console.log(rows);
                        res.json(rows);
                    });
                    wsConnect.send(message);*/
                }
            }
        });

        wsConnect.on('close', function (code, message) {
            // When we receive a message handel here ....
            wsConnect.send("close signal");

        });

        wsConnect.on('error', function (error)  {
            // When we receive a message handel here ....
            wsConnect.send("error signal");

        });

        wsConnect.on('ping', function (data, flags) {
            // When we receive a message handel here ....
            wsConnect.send("ping signal");

        });

        wsConnect.on('pong', function (data, flags) {
            // When we receive a message handel here ....
            wsConnect.send("pong signal");

        });

    });


}());







