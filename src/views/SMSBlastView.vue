<template>

    <!-- Modal Progress -->
    <div class="modal fade" id="progressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Mengirim ke: {{ sms.phoneNumber }}</h1>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <p>
                        {{ sms.message }}
                    </p>
                    <div class="progress" role="progressbar" aria-label="Animated striped example" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" :style="progressBar"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <p class="me-auto">Data {{ counterSms.current }} / {{ counterSms.total }} </p>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="stopLoopSender">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <div class="col-md-6 m-auto my-4">
            <h2 class="mb-4 fw-bold section-title">SMS Blast</h2>
            <div class="card">
                <div class="card-body">
                    <form class="mb-4">
                        <div class="mb-3">
                            <label class="form-label">File CSV</label>
                            <input type="file" @change="setFileCsv" name="fileCsv" class="form-control" accept="text/csv">
                        </div>
                        <textarea name="message" v-model="message" id="" class="form-control"
                            placeholder="Tulis pesan di sini.." rows="12"></textarea>
                    </form>
                    <div class="d-flex">
                        <button @click="blastMessages" class="btn btn-primary m-auto">Kirim</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {
        reactive
    } from 'vue'

    export default {
        setup() {
            const progressBar = reactive({
                width: '0%'
            })
            return {
                progressBar
            }
        },
        data() {
            return {
                message: '',
                sms: {
                    message: '',
                    phoneNumber: ''
                },
                counterSms: {
                    current: 0,
                    total: 0
                },
                statusBlast: false
            }
        },
        methods: {
            setFileCsv(f) {
                this.getDataCsv(f.target)
            },
            stopLoopSender() {
                console.log('Stop!!!')
                this.statusBlast = false
            },
            blastMessages() {
                
                // Change status blast
                this.statusBlast = true

                const progressModal = new bootstrap.Modal(document.getElementById('progressModal'))
                progressModal.show()
                const datas = JSON.parse(localStorage.sms)['datacsv'];
                this.counterSms.total = datas.length

                var loopSendSms = setInterval(() => {

                    if (this.statusBlast) {
                        console.log('Send sms!')
                    } else {
                        console.log('Stop send sms!')
                        // clearInterval(loopSendSms)
                        window.location.reload()
                    }
                    
                    this.counterSms.current++
                    this.progressBar.width = (this.counterSms.current / this.counterSms.total) * 100 + '%'
                    this.sms.phoneNumber = datas[0]['NOMOR']
                    this.sms.message = this.modTemplate(this.message, datas[0])
                    
                    datas.shift()

                    if (datas.length > 0) {
                        this.sms.message = this.modTemplate(this.message, datas[0])
                    } else {
                        clearInterval(loopSendSms)
                        progressModal.hide()
                        return false
                    }

                }, 1000)
            },
            getDataCsv(formFile) {

                if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                    console.log('The File APIs are not fully supported in this browser.');
                    return;
                }

                if (!formFile.files) {
                    console.log("This browser doesn't seem to support the files property of file inputs.");
                } else if (!formFile.files[0]) {
                    console.log("No file selected.");
                } else {
                    let file = formFile.files[0];
                    let fr = new FileReader();
                    fr.readAsText(file);

                    let tempData = [];

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

                        // console.log(datas)
                        localStorage.sms = JSON.stringify({
                            datacsv: datas
                        })

                    }

                }
            },
            async sendSms(data) {
                try {
                    const response = await fetch('http://localhost:8000/api/sms.php?r=send', {
                        method: 'post',
                        body: JSON.stringify({
                            phoneNumber: data.phoneNumber,
                            message: data.message
                        })
                    });
                    const result = await response.json();
                    console.log(result)
                    switch (result.code) {
                        case 200:
                            Toastify({
                                text: 'Berhasil kirim sms.',
                                position: 'right',
                                gravity: 'bottom'
                            }).showToast();
                            break;
                        default:
                            Toastify({
                                text: 'Ada kesalahan. Periksa gateway..',
                                position: 'right',
                                gravity: 'bottom'
                            }).showToast();
                            break;
                    }
                } catch (error) {
                    console.error(error);
                }
            },
            modTemplate(pesan, data) {

                const regex = /{{(.*?)}}/g;
                const found = pesan.match(regex);

                if (found != null) {
                    found.forEach(function (h) {
                        const k = h.match(/{{(.*?)}}/i);
                        pesan = pesan.replace(k[0], data[k[1].trim()]);
                    });
                }

                return pesan;
            }
        }
    }
</script>