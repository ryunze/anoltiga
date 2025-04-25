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
                    <textarea v-model="message" name="" id="" class="form-control" placeholder="Tulis pesan di sini.." rows="12"></textarea>      
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
        sendSms() {
            console.log(this.phoneNumber)
            console.log(this.message)
            fetch('http://localhost:8000/api/sms.php?r=send', {
                body: JSON.stringify({
                    phoneNumber: this.phoneNumber,
                    message: this.message
                })
            });
        }
    }
}
</script>