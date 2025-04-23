<div>
    <div class="w-90">

        <!-- Header -->
        <div class="d-flex align-items-center">
            <h2 class="font-weight-bold mb-4">SMS Sender</h2>
            <?php include_once(__DIR__ . '/../partials/header-sms.php') ?>
        </div>

        <form action="#" id="formSms" class="mb-6">
            <div class="form-group">
                <label for="phone">Nomor</label>
                <input type="text" class="form-control" id="phone" name="phone" />
            </div>
            <div class="form-group">
                <label for="message">Pesan</label>
                <textarea name="message" class="form-control" spellcheck="false" id="message" name="message" rows="7"></textarea>
            </div>
        </form>

        <button class="btn btn-primary w-100" id="btnSend">
            <span id="btnSendText">Kirim</span>
        </button>

        <!-- Loading -->
        <div class="d-none" id="loadingSend">
            <div class="ms-auto d-flex flex-align-center flex-justify-center">
                <div class="lds-ring" id="loadingSend">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <span>Mengirim..</span>
            </div>
        </div>
    </div>
</div>

<script>
    
    $(document).ready(function () {
        
        const appDate = new Date();
        let lastSend = localStorage.getItem('sms_sender_last_send');
        
        if (lastSend != null) {

            let lastSendTime = parseInt(lastSend) + (60000 * 1440);
            // console.log(appDate.getTime() > lastSendTime);
    
            if (appDate.getTime() > lastSend) {
                localStorage.setItem('sms_sender_limit', 10);
                // console.log("Reset Limit Sms Sender 10X");
            }
        }


        const btnSend = $('#btnSend');

        btnSend.click(function (btn) {

            $('#loadingSend').removeClass('d-none');
            btnSend.addClass('d-none');
            btnSend.attr('disabled', 'true');

            const smsData = new FormData(document.getElementById('formSms'));
            sendSms(smsData);

        });

        function checkLimit() {

            const smsSenderLimit = localStorage.getItem('sms_sender_limit');

            if (smsSenderLimit != null) {
                if (smsSenderLimit < 1) {
                    Qual.infod("Batas Harian 10X Habis!", "Silahkan upgrade ke premium Rp10.000/bln");
                    return false;
                }
                return true;
            }
        }

        function checkStatus() {
            $.ajax({
                url: '/api.php/sms',
                timeout: 5000,
                success: function (res) {
                    console.log(res);
                    sendSms();
                },
                error: function () {
                    console.error("Gagal terhubung");
                    hideBtnLoading();
                    toast("Gagal terhubung. Periksa koneksi ponsel", "error");
                }
            });
        }

        function sendSms(smsData) {

            $.ajax({
                method: 'post',
                url: '/api.php/sms/send',
                data: smsData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (res) {
                    if (res.code == 200) {
                        toast("Berhasil kirim SMS");
                    } else {
                        toast("Gagal kirim sms. Periksa pulsa atau koneksi");
                    }
                    hideBtnLoading();
                },
                error: function () {
                    toast("Gagal kirim SMS. Periksa pulsa atau koneksi kembali", "error");
                    hideBtnLoading();
                }
            });
        }

        function hideBtnLoading() {
            $('#loadingSend').addClass('d-none');
            btnSend.removeClass('d-none');
            btnSend.removeAttr('disabled');
        }

    });
</script>