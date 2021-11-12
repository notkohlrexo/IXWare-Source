// Server 2
var io = require("socket.io")(8100);
io.sockets.on("connection",function(socket){
    // Display a connected message
    console.log("Connected");
    
    // When we receive a message...
    socket.on("message",function(data){
        console.log(data);
    });
});