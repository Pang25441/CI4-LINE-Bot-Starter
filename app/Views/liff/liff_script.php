<script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>

<script>
    var myLiffId = '<?php echo isset($liffid) ? $liffid : '' ?>'
    var site_url = '<?php echo isset($endpoint) ? $endpoint : '' ?>/'
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

    
    function closeLiff() {
        liff.closeWindow();
    }

    function showError() {
        $('#liff-loading').addClass('d-none');
        $('#liff-content').addClass('d-none');
        $('#liff-error').removeClass('d-none');
        $('#liff-success').addClass('d-none');
    }

    function showContent(data=null){
        if(data)
        {
            $('#liff-content').find('*').remove();
            $('#liff-content').append(data);
        }
        $('#liff-loading').addClass('d-none');
        $('#liff-content').removeClass('d-none');
        $('#liff-error').addClass('d-none');
        $('#liff-success').addClass('d-none');
    }

    function showLoading() {
        $('#liff-loading').removeClass('d-none');
        $('#liff-content').addClass('d-none');
        $('#liff-error').addClass('d-none');
        $('#liff-success').addClass('d-none');
    }

    function showSuccess(message=null) {
        if(message)
        {
            $('#liff-success').find('.text').text(message);
        }
        $('#liff-loading').addClass('d-none');
        $('#liff-content').addClass('d-none');
        $('#liff-error').addClass('d-none');
        $('#liff-success').removeClass('d-none');
    }
</script>

<script>
    $(document).ready(function() {
        initializeLiff(myLiffId)
    })
</script>