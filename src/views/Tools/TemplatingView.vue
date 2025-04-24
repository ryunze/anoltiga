<template>
    <div class="container" v-if="showForm">
        <div class="col-md-6 m-auto my-4">
            <h2 class="mb-4 fw-bold">Templating</h2>
            <div class="card">
                <div class="card-body">
                    <form class="mb-4">
                        <div class="mb-3">
                            <label class="form-label">File CSV</label>
                            <input type="file" class="form-control">
                        </div>
                        <textarea name="" id="" class="form-control" placeholder="Tulis pesan di sini.."
                            rows="12"></textarea>
                    </form>
                    <div class="d-flex">
                        <button @click="generateTemplate" class="btn btn-primary m-auto">Generate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container my-4" v-if="showResult">
        <div class="row">
            <div class="col-md-3 mb-4" v-for="n in 10">
            <div class="card">
                <div class="card-header">
                    Featured
                </div>
                <div class="card-body">
                    <h5 class="card-title">Special title treatment</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="btn btn-light me-2">Copy</a>
                    <a href="#" class="btn btn-primary me-2">SMS</a>
                    <a href="#" class="btn btn-success">Whatsapp</a>
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
            showForm: true,
            showResult: false
        }
    },
    methods: {
        generateTemplate() {
            this.showForm = false,
            this.showResult = true
        }
    }
}
</script>