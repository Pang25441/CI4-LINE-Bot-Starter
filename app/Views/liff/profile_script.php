<script>
    function initializeApp() {
        if (!liff.isLoggedIn()) {
            liff.login();
            return;
        }

        liff.getFriendship().then((data) => {
            console.log(data)
            if (data.friendFlag) {
                loadForm();
            } else {
                showError()
            }
        })
    }

    function loadForm() {

        if (!liff.isLoggedIn()) {
            showError()
            return false;
        }

        const accessToken = liff.getAccessToken();

        $.post(site_url + 'loadform', {
            accessToken: accessToken
        }, (data, status) => {
            $('#liff-loading').addClass('d-none');
            if (status == 'success') {
                showContent(data);
            } else {
                showError();
            }
        }).fail(function(){
            showError();
        })
    }

    function submitForm() {

        const accessToken = liff.getAccessToken();

        showLoading()

        $('#accessToken').val(accessToken);
        
        var parameter = $('form').serialize()

        $.post(site_url + 'saveform', parameter, (data, status) => {
            if (status == 'success') {
                if (data.result) {
                    showSuccess(data.message)
                } else {
                    showContent()
                }
            } else {
                showError()
            }
        })

        return false
    }

</script>