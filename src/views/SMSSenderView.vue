<template>
    <div class="container">
        <div class="col-md-6 m-auto my-4">
            <h2 class="mb-4 fw-bold">SMS Sender</h2>
            <div class="card">
                <div class="card-body">
                    <form class="mb-4">
                        <div class="mb-3">
                            <input v-model="phoneNumber" type="text" placeholder="Nomor HP" class="form-control">
                        </div>
                        <textarea v-model="message" name="" id="" class="form-control"
                            placeholder="Tulis pesan di sini.." rows="12"></textarea>
                    </form>
                    <div class="d-flex">
                        <button @click="sendSms" class="btn btn-primary m-auto">Kirim</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                phoneNumber: '',
                message: ''
            }
        },
        methods: {
            async sendSms() {
                console.log(JSON.stringify({
                            phoneNumber: this.phoneNumber,
                            message: this.message
                        }))
                try {
                    const response = await fetch('http://localhost:8000/api/sms.php?r=send', {
                        method: 'post',
                        body: JSON.stringify({
                            phoneNumber: this.phoneNumber,
                            message: this.message
                        })
                    });
                    const result = await response.text();
                    // const result = await response.json();
                    console.log(result)
                } catch (error) {
                    console.error(error);
                }
            }
        }
    }
</script>