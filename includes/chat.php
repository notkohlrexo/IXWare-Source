<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die('{"errors":[{"code":401,"message":"Unauthorized"}]}'); 
$notificationSound = pdoQuery($db, "SELECT `notificationPath` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
?>

<button class="btn purple-gradient-rgba open-button w-25 text-white" onclick="openChat()" id="openButton">Beta Chat</button>
<div class="card card-cascade chatx-card chat-popup animated fadeInUp" id="chatcard" style="z-index: 999;">
    <button type="button" class="close text-white ml-1 mt-1 float-left" onclick="closeChat();"><span aria-hidden="true">&times;</span></button>
    <div class="data mt-4 ml-3">
        <div class="col-xl-12">
            <div id="msgarea" class="row">
                <div class="col-md-4">
                    <div class="online-box">
                        <ul class="list-group list-group-flush" id="users"></ul>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="message-box" id="message-box" style="word-break: break-all;" ;>
                        <div class="mr-3" id="chat"></div>
                    </div>
                    <form id="messageForm">
                        <input type="submit" style="display: none;" />
                        <div class="form-group">
                            <div class="md-form mr-3">
                                <input autocomplete="off" id="message" type="text" class="form-control chat-input mt-2 text-darkwhite" placeholder="Message" />
                                <div id="typing"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>