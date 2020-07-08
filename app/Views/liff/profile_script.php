<script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>

<script>
    var myLiffId = '<?php echo isset($liffId) ? $liffId : '' ?>'
    var site_url = '<?php echo site_url('LIFF/profile') ?>'
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
            
        });
}

function initializeApp() {
    if (!liff.isLoggedIn()) {
        // set `redirectUri` to redirect the user to a URL other than the endpoint URL of your LIFF app.
        liff.login();
        return;
    }

    // $.post(site_url+'/loadform')
}
</script>

<script>
    $(document).ready(()=>{
        initializeLiff(myLiffId)
    })
</script>