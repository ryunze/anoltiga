<div class="ms-auto d-flex">
    <button class="btn btn-white d-flex flex-align-center ms-auto me-3" id="btnSmsSettings" data-toggle="modal" data-target="#modalSmsSettings">
        Pengaturan
    </button>
    <button class="btn btn-white d-flex flex-align-center ms-auto" onclick="checkStatusGateway(this)">
        <svg width="24" height="24" class="me-3" fill="#000" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
            clip-rule="evenodd">
            <path
                d="M8 6.012h-6.58l1.935-6.012 1.718 2.223c1.958-1.389 4.346-2.211 6.927-2.211 6.623 0 12 5.377 12 12s-5.377 11.988-12 11.988-12-5.365-12-11.988c0-1.036.132-2.041.379-3h2.079c-.297.947-.458 1.955-.458 3 0 5.52 4.481 10 10 10 5.52 0 10-4.48 10-10 0-5.519-4.48-10-10-10-2.121 0-4.083.668-5.703 1.796l1.703 2.204zm4 1.988c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4z" />
        </svg>
        <span id="statusGatewayInfo">Status: IDLE</span>
    </button>
</div>

<!-- @Partials : sms-settings-popup -->
<div id="sms-settings-popup"></div>

<script>
    $(document).ready(()=> {
        $.ajax({
            url: '/partials/sms-settings-popup.php',
            success: (res) => {
                // console.log(res)
                $("#sms-settings-popup").html(res);
            }
        })
    })
</script>