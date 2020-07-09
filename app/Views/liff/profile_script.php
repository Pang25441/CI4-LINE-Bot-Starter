<script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>

<script>
    var myLiffId = '<?php echo isset($liffid) ? $liffid : '' ?>'
    var site_url = '<?php echo site_url('LIFF/profile') ?>/'
</script>

<script>
    function initializeLiff(myLiffId) {
        liff
            .init({
                liffId: myLiffId
            })
            .then(() => {
                // start to use LIFF's api
                const idToken = liff.getIDToken();
                console.log(idToken)
                initializeApp();
            })
            .catch((err) => {
                $('#liff-loading').addClass('d-none');
                $('#liff-error').removeClass('d-none');
                console.log(err)
            });
    }

    function initializeApp() {
        if (!liff.isLoggedIn()) {
            // set `redirectUri` to redirect the user to a URL other than the endpoint URL of your LIFF app.
            liff.login();
            return;
        }

        liff.getFriendship().then((data) => {
            console.log(data)
            if (data.friendFlag) {
                loadForm();
            } else {
                $('#liff-loading').addClass('d-none');
                $('#liff-error').removeClass('d-none');
            }
        })
    }

    function loadForm() {
        if (!liff.isLoggedIn()) {
            $('#liff-loading').addClass('d-none');
            $('#liff-error').removeClass('d-none');
            return false;
        }
        
        $.post(site_url + 'loadform', {
            idToken: idToken
        }, (data, status) => {
            $('#liff-loading').addClass('d-none');
            if (status == 'success') {
                $('#liff-content').append(data);
                $('#liff-content').removeClass('d-none');
            } else {
                $('#liff-content').addClass('d-none');
                $('#liff-error').removeClass('d-none');
            }
        })
    }
</script>

<script>
    $(document).ready(function() {
        initializeLiff(myLiffId)
    })
</script>