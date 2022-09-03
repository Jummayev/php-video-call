const q = elem=>document.querySelector(elem);
console.log(window);

if (q(".homePageWrapper")){
    q(".createNewButton").addEventListener('click', function (e){
        e.preventDefault();
        q("#meetingID").value=getRandomId(10);
    })
    q(".joinBtn").addEventListener("click", function (e){
        e.preventDefault();
        const meeting_id = q("#meetingID").value.trim();
        const username = q("#username").value.trim();

        if (!username || !meeting_id){
            alert("Fields must not be empty!")
            return;
        }
        sessionStorage.setItem("username", username);
        let meetingURL = "http://localhost:3000?meetingID="+meeting_id;
        window.location.href = meetingURL;
        q("#username").value="";
        q("#meetingID").value="";

    })
}

const getRandomId = (number) =>{
    let text = '';
    const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (let i = 0; i < number; i++){
        text += possible.charAt(Math.random() * possible.length)
    }
    return text
}
