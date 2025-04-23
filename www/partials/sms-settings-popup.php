<!-- @ Modal SMS Settings -->
<div class="modal fade" id="modalSmsSettings" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="staticBackdropLabel">Pengaturan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formConfig">
                    <div class="form-group">
                        <label for="localAddress">Local Address</label>
                        <input class="form-control" type="text" name="local_address" id="localAddressInput">
                    </div>
                    <div class="form-group">
                        <label for="localAddress">Username</label>
                        <input class="form-control" type="text" name="user_name" id="usernameInput">
                    </div>
                    <div class="form-group">
                        <label for="localAddress">Password</label>
                        <input class="form-control" type="text" name="user_password" id="passwordInput">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" id="btnSaveConfig">Simpan</button>
            </div>
        </div>
    </div>
</div>
<!-- # Modal SMS Settings -->

<script>
    $(document).ready(function () {

        $("#btnSmsSettings").click(function () {

            $.get("/api.php/sms/config", function (res) {
                console.log(res);
                if (res.code == 200) {
                    $('#localAddressInput').val(res.data['local_address']);
                    $('#usernameInput').val(res.data['user_name']);
                    $('#passwordInput').val(res.data['user_password']);
                }
            });

            const popupSettings = $("#settingsPopup");
            const btnClose = $("#settingsPopup button[data-btn=close]");

            popupSettings.removeClass('d-none');

            btnClose.click(function () {
                popupSettings.addClass("d-none");
            });

        });

        $("#btnSaveConfig").click(function () {

            const formConfig = document.getElementById("formConfig");
            const dataConfig = new FormData(formConfig);

            // console.log(dataConfig.get("user_name"))

            $.ajax({
                url: "/api.php/sms/config",
                method: "post",
                data: dataConfig,
                processData: false,
                contentType: false,
                cache: false,
                success: function (res) {
                    console.log(res);
                    $("#settingsPopup").addClass('d-none');
                    Toastify({
                        text: "Berhasil simpan!",
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: "center", // `left`, `center` or `right`
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)",
                        }
                    }).showToast();
                },
                error: function (res) {
                    console.log("ERROR");
                }
            });
        });

    });
</script>