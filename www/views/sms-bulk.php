<!-- Modal -->
<div class="modal fade" id="popupSmsBulk" data-backdrop="static" data-keyboard="false"
aria-labelledby="popupSmsBulkLabel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <img src="/assets/loading.gif" width="32" class="mr-4" />
                <h5 class="modal-title" id="exampleModalLabel"><b>Mengirim ke :</b> <span data-popup="phone">-</span>
                </h5>
            </div>
            <div class="modal-body">
                <p data-popup="message">-</p>
            </div>
            <div class="modal-footer">
                <p><span data-popup="timer">0</span> detik</p>
                    <p><b>Antrian:</b> <span data-popup="counter">-/-</span></p>
                <!-- <button class="btn btn-primary" id="btnStop">Stop</button> -->
                <button type="button" class="btn btn-primary" id="btnStop">Stop</button>
            </div>
        </div>
    </div>
</div>

<div>
    <div class="w-90">

        <!-- Header -->
        <div class="d-flex flex-align-center">
            <h2 class="font-weight-bolder mb-4">SMS Bulk</h2>
            <?php include_once(__DIR__ . '/../partials/header-sms.php') ?>
        </div>
        <form action="#" id="formSms" class="mb-6">
            <label for="timer">File CSV</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroupFileAddon01">Pilih File</span>
                </div>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="datafile" accept=".csv" name="datafile">
                    <label class="custom-file-label" id="inputCsvLabel" for="inputGroupFile01">..</label>
                </div>
            </div>
            <div class="form-group">
                <label for="timer">Timer /detik</label>
                <input class="form-control" type="number" id="timer" name="timer" />
            </div>
            <div class="form-group">
                <label for="message">Pesan (Template)</label>
                <textarea class="form-control" name="message" spellcheck="false" id="message" name="message"
                    rows="7"></textarea>
            </div>
        </form>
        <div class="d-flex">
            <button class="btn btn-primary me-4" id="btnPreview">
                <span id="btnSendText">Preview</span>
            </button>
            <button class="btn btn-primary ms-auto" id="btnSend">
                <span id="btnSendText">Kirim</span>
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        // Event File Selected
        $("#datafile").change(function (event) {
            let fileName = event.target.value.split('\\');
            fileName = fileName[fileName.length - 1];
            $("#inputCsvLabel").text(fileName);
        });

        // Status Blast Trigger
        let STATUS_BLAST = false;

        $('button#btnPreview').click(previewMessage);
        $('button#btnSend').click(sendMessage);
        $('button#btnStop').click(function () {
            STATUS_BLAST = false;
            $('#popupSmsBulk').modal('hide');
        });

        function sendMessage() {

            STATUS_BLAST = true;

            const formFile = document.querySelector('#datafile');

            if (!formFile.files[0]) {
                toast("Tidak ada file CSV. Silahkan upload!");
                return false;
            }

            const timer = document.getElementById('timer');
            if (timer.value == "") {
                toast("Timer tidak boleh kosong");
                return false;
            }

            const message = document.getElementById('message');
            if (message.value == "") {
                toast("Pesan tidak boleh kosong");
                return false;
            }

            // $("#popupSmsBulk").addClass("show");
            $('#popupSmsBulk').modal('show')

            const popup = document.getElementById("popupSmsBulk");
            const data = new FormData(document.getElementById("formSms"));

            let popupTimer = popup.querySelector("[data-popup=timer]");
            let popupPhone = popup.querySelector("[data-popup=phone]");
            let popupMessage = popup.querySelector("[data-popup=message]");
            let popupCounter = popup.querySelector("[data-popup=counter]");

            popupTimer.innerText = timer.value;
            popupCounter.innerText = "-/-";
            popupPhone.innerText = '-'
            popupMessage.innerText = '-';

            let count = 0;
            let totalData = 0;

            const d = getData(formFile, function (data) {

                totalData = data.length - 1;

                const sendMsgInterval = setInterval(() => {

                    let timeCount = timer.value -1;

                    const countDownInterval = setInterval(function() {
                        popupTimer.innerText = timeCount;
                        timeCount--;
                        if (timeCount < 0) {
                            popupTimer.innerText = timer.value;
                            clearInterval(countDownInterval);
                        }
                    }, 1000);

                    if (data.length > 0) {

                        if (!STATUS_BLAST) {
                            clearInterval(sendMsgInterval);
                            return false;
                        }

                        // console.log("Send to: " + data[0]['NOMOR']);
                        const msg = modTemplate(message.value, data[0]);
                        // console.log(msg);
                        ++count;
                        popupCounter.innerText = count + "/" + totalData;
                        popupPhone.innerText = data[0]['NOMOR'];
                        popupMessage.innerText = msg;

                        sendSms({
                            'phone': data[0]['NOMOR'],
                            'message': msg,
                            'timer': timer.value
                        });

                        data.shift();
                    } else {
                        clearInterval(sendMsgInterval);
                        $("#popupSmsBulk").modal('hide');
                        toast("Berhasil blast semua sms!");
                    }

                }, timer.value * 1000);
            });

            // console.log(timer)
        }

        function previewMessage() {

            const formFile = document.querySelector('#datafile');
            const d = getData(formFile, function (data) {
                const msgTemplate = document.getElementById('message');
                const resultMsg = modTemplate(msgTemplate.value, data[0]);
                Qual.sw(resultMsg);
            });

        }

        function readFileCsv() {
            const formFile = document.querySelector('#datafile');
            formFile.addEventListener('change', getData, false);
        }

        function modTemplate(pesan, data) {

            const regex = /{{(.*?)}}/g;
            const found = pesan.match(regex);

            // console.log(found)

            if (found != null) {
                found.forEach(function (h) {
                    const k = h.match(/{{(.*?)}}/i);
                    pesan = pesan.replace(k[0], data[k[1].trim()]);
                });
            }

            return pesan;
        }

        function getData(formFile, callback) {

            if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                console.log('The File APIs are not fully supported in this browser.');
                return;
            }

            if (!formFile.files) {
                console.log("This browser doesn't seem to support the files property of file inputs.");
            } else if (!formFile.files[0]) {
                console.log("No file selected.");
                toast("Tidak ada file CSV. Silahkan upload!");
            } else {
                let file = formFile.files[0];
                let fr = new FileReader();
                fr.readAsText(file);

                fr.onload = function () {
                    let datas = [];
                    let hasil = fr.result;

                    hasil = hasil.split('\n');
                    let headData = hasil[0];

                    headData = headData.replace('\r', '');
                    headData = headData.split(',');

                    hasil.shift();
                    hasil.forEach(res => {
                        res = res.replace('\r', '');
                        // console.log(res);
                        const data = res.split(',');
                        let tempData = {};
                        data.forEach(function (v, i) {
                            tempData[headData[i]] = v;
                        });
                        datas.push(tempData);
                    });

                    callback(datas);

                }

            }
        }

        function sendSms(smsData) {

            $.ajax({
                method: 'post',
                url: '/api.php/sms/send',
                data: smsData,
                success: function (res) {
                    console.log(res)
                    if (res.code == 200) {
                        toast("Berhasil kirim SMS");
                    } else {
                        toast("Gagal kirim sms. Periksa pulsa atau koneksi");
                    }
                },
                error: function (res) {
                    console.error(res)
                    toast("Gagal kirim SMS. Periksa pulsa atau koneksi kembali", "error");
                }
            });
        }

    });
</script>