function sendSMS() {

    console.log("[Send SMS : Start]");

    const formData = new FormData(document.getElementById("formSendSms"));

    const data = {
        phone: formData.get("phone"),
        message: formData.get("message")
    };

    const popup = new Popup(); 
    // $("body").append(popup);

    $.ajax({
        url: "/api.php/sms/send",
        method: "post",
        contentType: "application/json",
        data: JSON.stringify(data),
        cache: false,
        proccesData: false,
        success: function (res) {
            console.log(res)
            if (res.code == 200) {
                console.log("[Send SMS] : Check status..");
                check_status_message(res.data.id);
            }
        }
    });

}

function check_status_message(id) {
    console.log("[Send SMS] : Get status..");
    $.ajax({
        url: '/api.php/sms/status/' + id,
        method: 'get',
        success: function(res) {
            console.log(res)
            if (res.code == 200 && res.data.state == 'pending') {
                console.log("[Send SMS] : Recheck status..");
                setTimeout(function() {
                    check_status_message(id);
                }, 3000);
            } else {
                console.log(res)
            }
        }
    });
}

function checkStatusGateway(btn) {

    console.log("[Status Gateway] : Checking..");
    btn.querySelector("span").textContent = "Status: ..";
    btn.querySelector("svg").setAttribute("fill", "#000");

    $.ajax({
        method: "GET",
        url: "/api.php/sms",
        timeout: 3000,
        success: function (res) {
            switch (res.code) {
                case 200:
                    console.log("[Status Gateway] : ON");
                    btn.querySelector("svg").setAttribute("fill", "#006A67");
                    btn.querySelector("span").textContent = "Status: ON";
                    toast("Berhasil terhubung :)");
                    break;
                default:
                    console.log("[Status Gateway] : OFF");
                    console.log("Gagal terhubung dengan ponsel");
                    btn.querySelector("svg").setAttribute("fill", "#FF2929");
                    btn.querySelector("span").textContent = "Status: OFF";
                    toast("Gagal terhubung dengan ponsel :(");
                    break;
            }
        },
        error: function () {
            console.log("[Status Gateway] : ERROR");
            btn.querySelector("svg").setAttribute("fill", "#FF2929");
            btn.querySelector("span").textContent = "Status: OFF";
            toast("Gagal terhubung dengan ponsel :(");
        }
    });
}

function showSmsSettings() {
    
}