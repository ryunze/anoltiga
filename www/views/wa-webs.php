<!-- Modal -->
<div class="modal fade" id="modalNewWa" tabindex="-1" aria-labelledby="modalNewWaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <label for="inputWaLabel" class="form-label">Masukan Label</label>
                <input type="text" class="form-control" name="waLabel" id="inputWaLabel">
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="custom-bg">
    <div class="w-90">

        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <h2 class="font-weight-bold">WA Webs</h2>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary ml-auto" data-toggle="modal" data-target="#modalNewWa">
                Tambah
            </button>

        </div>

        <table class="table table-hover">
            <thead class="thead-dark">
                <th>ID</th>
                <th>Label</th>
                <th>#</th>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Wa Penagihan</td>
                    <td><a href="#" class="btn-outline-primary">Run</a></td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Wa Penagihan</td>
                    <td><a href="#" class="btn-outline-primary">Run</a></td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Wa Penagihan</td>
                    <td><a href="#" class="btn-outline-primary">Run</a></td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Wa Penagihan</td>
                    <td><a href="#" class="btn-outline-primary">Run</a></td>
                </tr>
            </tbody>
        </table>

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