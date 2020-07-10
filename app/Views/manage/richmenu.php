<div class="container-fluid" elm-body>

    <h1 class="mb-3">Manage/RichMenu</h1>

    <?php if ($save_status === true || $save_status === false) : ?>
        <div class="alert <?php echo $save_status ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo ucfirst($save_message); ?>
        </div>
    <?php endif; ?>

    <div class="row mb-1">
        <div class="col-auto mr-auto">
            <button type="button" class="btn btn-sm btn-outline-primary text-center" onclick="reSync()"><i class="fas fa-cloud-download-alt"></i> Re-Sync RichMenu</button>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshRichmenu()"><i class="fas fa-redo"></i> Refresh</button>
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#richmenucreate"><i class="fas fa-plus"></i> Create</button>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">richMenuId</th>
                <th scope="col" class="text-center">Name</th>
                <th scope="col" class="text-center">Data</th>
                <th scope="col" class="text-center" width="20%">Image</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody data-list-body>
            <tr data-list-proto class="d-none">
                <th scope="row" class="text-right align-middle" data-index>{index}</th>
                <td data-richMenuId class="align-middle">{richMenuId}</td>
                <td data-name class="align-middle">{Name}</td>
                <td class="text-center align-middle">
                    <a elm-databtn href="#" data-toggle="modal" data-target="" class="btn btn-sm btn-outline-secondary"><i class="fas fa-code"></i><br>Show Data</a>

                    <div elm-databox class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg ">
                            <div class="modal-content">
                                <div class="modal-body text-left">
                                    <!-- <textarea data-data class="form-control" rows="20"></textarea> -->
                                    <pre><code data-data class="json">...</code></pre>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary m-auto" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td data-image class="text-center align-middle">

                </td>
                <td width="75" class="align-middle">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-block btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <b> Action</b>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <button class="dropdown-item text-primary" type="button" data-action-default><i class="fas fa-check"></i> Set As Default Menu</button>
                            <button class="dropdown-item text-warning" type="button" data-action-remove><i class="fas fa-minus-circle"></i> Unset Default Menu</button>
                            <!-- <button class="dropdown-item" type="button" data-action-rebuild><i class="fas fa-recycle"></i> Re-Create</button> -->
                            <button class="dropdown-item text-danger" type="button" data-action-delete><i class="far fa-trash-alt"></i> Delete</button>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

</div>

<div class="modal fade" id="richmenucreate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" action="<?php echo site_url('Manage/Richmenu/createRichmenu'); ?>" onsubmit="return createRichMenu()">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create RichMenu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="richmenuimage">Rich Menu Image</label>
                        <input type="file" class="form-control-file" name="richmenuimage" id="richmenuimage" accept="image/png,image/jpeg">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="richmenudata">Rich Menu Object (JSON)</label>
                        <textarea class="form-control" name="richmenudata" id="richmenudata" cols="30" rows="20"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal " id="loading-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="lds-ripple"><div></div><div></div></div>
                <h3 class="text-center">Please Wait</h3>
            </div>
        </div>
    </div>
</div>