const base_url = "http://localhost/php-video-call/";
conn.onopen = function (e) {
    console.log(e)
}
let user_id = $(".u-p-id").data("userid");
let profileid = $(".u-p-id").data("profileid")
conn.onmessage = function (e) {
    console.log(e.data)
    let data = JSON.parse(e.data);
    switch (data.type) {
        case "CONNECTION_ESTABLISHED":
            $(".user-status").html(data.status);
            loadConnectedPeers();
            break;
        case "CONNECTION_DISCONNECTED":
            $(".user-status").html(data.status);
            loadConnectedPeers();
            break;
        case "NEW_USER_CONNECTION":
            loadConnectedPeers();
            break;
        case "NEW_USER_DISCONNECTION":
            loadConnectedPeers();
            break;
        case "client-is-ready":
            clientProcess(data.success);
            break;
        case "offer":
            offerProcess(data.offer, data.sender);
            break;
        case "answer":
            alert(data.receiver)
            answerProcess(data.answer);
            break;


    }
}

function loadConnectedPeers(){
    let user_id = $(".u-p-id").data("userid");
    let profileid = $(".u-p-id").data("profileid")
    if (user_id != undefined){
        $.post(base_url+"core/ajax/loadConnectionPres.php", {user_id:user_id, otherid:profileid}, function (data){
            $(".g-users").html(data);
        });
    }
}
let local_video = document.querySelector("#local-video");
let stream;
let peerConnection;

setTimeout(function (){
    if (conn.readyState === 1){
        if (user_id !== null){
            sendToServer("client-is-ready", null, user_id)
        }else {
            console.log("not working")
        }
    }
}, 3000)


function sendToServer(type, data, target){
    conn.send(JSON.stringify({
        type: type,
        data: data,
        target: target
    }))
}

function clientProcess(success){
    if (success === true){
        const constrain = {
            audio: false,
            video: true
        }
        navigator.mediaDevices.getUserMedia(constrain).then(myStream => {
            stream = myStream;
            console.log("gom", myStream)
            local_video.srcObject=stream;
            const configuration = {
                "iceServers" :[{
                    "url": "stun:stun2.1.google.com:19302"
                }]
            }

            peerConnection =new RTCPeerConnection(configuration);
            console.log("perr conn: ", peerConnection)
        }).catch(error => {
            console.error("error", error)
        })
    }
}
function offerProcess(offer, sender){
    peerConnection
        .setRemoteDescription(new RTCSessionDescription(offer))
        .then(function () {
            peerConnection.createAnswer(function (answer){
                sendToServer("answer", answer, sender)
                peerConnection.setLocalDescription(answer)
            },function (error){
                alert("error answer")
            })
        });
}

function answerProcess(answer){
    peerConnection.setRemoteDescription(new RTCSessionDescription(answer))
}
loadConnectedPeers()

$(document).on("click", ".video-call", function (){
    let receive = $(this).data('profileid')
    if (receive != null){
        //
        // peerConnection.createOffer().then(function(offer) {
        //     return peerConnection.setLocalDescription(offer);
        // })
        //     .then(function() {
        //         sendToServer("offer", offer, receive);
        //     })
        //     .catch(function(reason) {
        //          alert(reason)
        //     });


        peerConnection.createOffer(function (offer) {
            sendToServer("offer", offer, receive)
            // console.log(offer, receive)
            peerConnection.setLocalDescription(offer)
        }, function (error) {
            alert("error offer   ")
        })
    }
})