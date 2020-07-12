<div class="container-fluid">
    <div id="liff-content" class="d-none"></div>
    <div id="liff-loading" class="text-monospace">
        <div class="m-auto lds-ripple">
            <div></div>
            <div></div>
        </div>
        <h2 class="loading-text text-center"><?php echo lang('Liff.page_loading') ?></h2>
    </div>
    <div id="liff-error" class="d-none text-monospace text-center">
        <i class="fas fa-exclamation-triangle text-danger"></i>
        <h1 style="font-weight: bold;"><?php echo lang('Liff.page_unavailable') ?></h1>
    </div>
    <div id="liff-success" class="d-none text-center text-monospace">
        <i class="fas fa-check-circle text-success"></i>
        <h3 class="text"></h3>
        <button type="button" class="btn btn-lg btn-success" onclick="closeLiff()"><?php echo lang('Liff.btn.done') ?></button>
    </div>
</div>

<style>
    #liff-error, #liff-success i {
        font-size: min(50vw, 40vh);
    }
    .loading-text {
        margin-top: min(25vw, 20vh);
    }
    #liff-success button {
        width: 50vw;
    }
    .lds-ripple {
    display: block;
    position: relative;
    width: min(50vw, 40vh);
    height: min(50vw, 40vh);
    top: 15vh;
    }
    .lds-ripple div {
    position: absolute;
    border: 4px solid #555;
    opacity: 1;
    border-radius: 50%;
    animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
    }
    .lds-ripple div:nth-child(2) {
    animation-delay: -0.5s;
    }
    @keyframes lds-ripple {
    0% {
        top: min(25vw, 20vh);
        left: min(25vw, 20vh);
        width: 0;
        height: 0;
        opacity: 1;
    }
    100% {
        top: 0px;
        left: 0px;
        width: min(50vw, 40vh);
        height: min(50vw, 40vh);
        opacity: 0;
    }
    }
</style>
