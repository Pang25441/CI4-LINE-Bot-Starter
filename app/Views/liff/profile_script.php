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

        const accessToken = liff.getAccessToken();

        $.post(site_url + 'loadform', {
            accessToken: accessToken
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

    function submitForm() {
        $('#liff-loading').removeClass('d-none');
        $('#liff-content').addClass('d-none');
        const accessToken = liff.getAccessToken();
        $('#accessToken').val(accessToken);
        var parameter = $('form').serialize()
        console.log(parameter)

        $.post(site_url + 'saveform', parameter, (data, status) => {
            $('#liff-loading').addClass('d-none');
            if (status == 'success') {
                if (data.result) {
                    $('#liff-success').find('.text').text(data.message);
                    $('#liff-content').addClass('d-none');
                    $('#liff-success').removeClass('d-none');
                } else {
                    $('#liff-content').removeClass('d-none');
                }
            } else {
                $('#liff-content').addClass('d-none');
                $('#liff-error').removeClass('d-none');
            }
        })

        return false
    }

    function closeLiff() {
        liff.closeWindow();
    }
</script>

<script>
    $(document).ready(function() {
        initializeLiff(myLiffId)
    })
</script>