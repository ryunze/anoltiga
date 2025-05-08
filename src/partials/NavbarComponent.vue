<script setup>
    import {
        RouterLink
    } from 'vue-router';
    import SpinnerPartial from './SpinnerPartial.vue';
</script>

<template>

    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="modalConfigSms" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Konfigurasi SMS</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="localAddress" class="form-label" >Kartu SIM</label>
                            <select class="form-control" v-model="smsConfig.simNumber">
                                <option value="1">SIM 1</option>
                                <option value="2">SIM 2</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="localAddress" class="form-label">Local Adress:</label>
                            <input type="text" class="form-control" v-model="smsConfig.localAddress">
                        </div>
                        <div class="mb-3">
                            <label for="localAddress" class="form-label">Username:</label>
                            <input type="text" class="form-control" v-model="smsConfig.username">
                        </div>
                        <div class="mb-3">
                            <label for="localAddress" class="form-label">Password:</label>
                            <input type="text" class="form-control" v-model="smsConfig.password">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="saveConfig">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Anoltiga</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            SMS
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <RouterLink class="dropdown-item" to="/sms/sender">Sender</RouterLink>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <RouterLink class="dropdown-item" to="/sms/blast">Blast</RouterLink>
                            </li>
                        </ul>
                    </li>
                </ul>
                <button type="button" class="btn btn-primary me-4" @click="getSmsConfig">
                    Setelan
                </button>
                <button class="btn" :class="{ 'btn-outline-dark' : sms.btnStatusOff, 'btn-success' : sms.btnStatusOn }"
                    @click="checkStatusGateway">
                    <SpinnerPartial v-if="showSpinner" />
                    <span>
                        Status: {{ sms.status }}
                    </span>
                </button>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        data() {
            return {
                smsConfig: {
                    localAddress: '',
                    username: '',
                    password: '',
                    simNumber: '1'
                },
                sms: {
                    status: 'OFF',
                    btnStatusOff: true,
                    btnStatusOn: false,
                    showSpinner: false,
                },
                showSpinner: false
            }
        },
        methods: {
            async checkStatusGateway() {

                Toastify({
                    text: 'Cek Status Gateway SMS..',
                    position: 'right',
                    gravity: 'bottom'
                }).showToast()

                this.showSpinner = true
                console.log('Check status gateway..')
                try {
                    const response = await fetch('http://localhost:8000/api/sms.php');
                    const result = await response.json();
                    console.log(result)
                    if (result.status == 200) {
                        this.sms.btnStatusOff = false
                        this.sms.btnStatusOn = true
                        this.sms.status = 'ON'
                    } else {
                        this.sms.status = 'OFF'
                        this.sms.btnStatusOff = true
                        this.sms.btnStatusOn = false
                    }
                    
                    this.showSpinner = false

                    Toastify({
                        text: result.message,
                        position: 'right',
                        gravity: 'bottom'
                    }).showToast()

                } catch (error) {
                    this.sms.status = 'OFF'
                    this.showSpinner = false
                    console.error(error);
                }

            },
            async getSmsConfig() {
                const modalConfigSms = new bootstrap.Modal(document.getElementById('modalConfigSms'))
                modalConfigSms.show()
                try {
                    const response = await fetch('http://localhost:8000/api/sms.php?r=config');
                    const result = await response.json();
                    console.log(result)
                    switch (result.status) {
                        case 201:
                            console.log('Berhasil fetch config!')
                            this.smsConfig.localAddress = result.data['local_address']
                            this.smsConfig.username = result.data['user_name']
                            this.smsConfig.password = result.data['user_password']
                            this.smsConfig.simNumber = result.data['sim_number']
                            console.log(this.smsConfig)
                            break;
                    }
                } catch (error) {
                    console.error(error);
                }
            },
            async saveConfig() {
                try {
                    const response = await fetch('http://localhost:8000/api/sms.php?r=save-config', {
                        method: 'post',
                        body: JSON.stringify(this.smsConfig)
                    });
                    const result = await response.json();
                    console.log(result)
                    Toastify({
                        text: result.message,
                        gravity: 'bottom',
                        position: 'right'
                    }).showToast()

                } catch (error) {
                    console.error(error);
                }
            }
        }
    }
</script>