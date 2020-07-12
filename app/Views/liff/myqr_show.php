<div class="col-sm-12 col-md-6 col-lg-4 m-auto pt-4 text-center text-monospace">

    <h4><?php echo $information; ?></h4>

    <hr>

    <img src="<?php echo $image_data; ?>" class=" img-fluid">

    <a href="#" class="" onclick="return loadQR(true)"><small>Reload <i class="fas fa-redo-alt"></i></small></a>

</div>

<div class="fixed-bottom text-monospace">
    <div class="col-sm-12 col-md-6 col-lg-4 m-auto pb-4">
        <button type="button" class="btn btn-block btn-success " onclick="closeLiff()"><?php echo lang('Liff.btn.done') ?></button>
    </div>
</div>