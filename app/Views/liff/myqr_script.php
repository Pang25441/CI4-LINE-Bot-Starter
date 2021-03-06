<script>
    function initializeApp() {
        if (!liff.isLoggedIn()) {
            liff.login();
            return;
        }

        liff.getFriendship().then((data) => {
            console.log(data)
            if (data.friendFlag) {
                loadQR();
            } else {
                showError()
            }
        })
    }

    function loadQR(reload = false) {

        if (!liff.isLoggedIn()) {
            showError()
            return false;
        }

        if (reload && !confirm('Reload QR Code Image?')) {
            return false
        }

        showLoading()

        const accessToken = liff.getAccessToken();

        let param = {
            accessToken: accessToken,
            reload: ''
        }
        if (reload) {
            param.reload = 'reload'
        }

        $.post(site_url + 'generateQR', param, (data, status) => {
            if (status == 'success') {
                showContent(data)
            } else {
                showError()
            }
        }).fail(() => {
            showError()
        })
        return false;
    }

    function scanQR() {
        if (liff.scanCode) {
            liff.scanCode().then(result => {
                if (result.value) {
                    liff.sendMessages([{
                            type: 'text',
                            text: result.value
                        }])
                        .then(() => {
                            console.log('message sent');
                        })
                        .catch((err) => {
                            console.log('error', err);
                        });
                }
            });
        }
    }
</script>