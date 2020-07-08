<div class="container-fluid" elm-body>

    <h1 class="mb-3">Manage/RichMenu</h1>

    <?php if ($save_status === true || $save_status === false) : ?>
        <div class="alert <?php echo $save_status ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo ucfirst($save_message); ?>
        </div>
    <?php endif; ?>

    <div class="row mb-1">
        <div class="col-auto mr-auto">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="reSync()">Re-Sync RichMenu</button>
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
                <th scope="row" class="text-right" data-index>{index}</th>
                <td data-richMenuId>{richMenuId}</td>
                <td data-name>{Name}</td>
                <td align="center">
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
                <td data-image>

                </td>
                <td width="75">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-block btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <button class="dropdown-item" type="button" data-action-default><i class="fas fa-check"></i> Set Default Menu</button>
                            <button class="dropdown-item" type="button" data-action-remove><i class="fas fa-minus-circle"></i> Unset Default Menu</button>
                            <button class="dropdown-item" type="button" data-action-rebuild><i class="fas fa-recycle"></i> Re-Create</button>
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

<script>
    var site_url = '<?php echo site_url(); ?>'

    $(document).ready(() => {
        loadRichMenu();
        document.querySelectorAll('.code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    })

    function loadRichMenu() {
        $('[elm-body] .btn').prop('disabled', true)
        $.get(site_url + '/Manage/Richmenu/loadRichmenu', (data, status) => {
            $('[elm-body] .btn').prop('disabled', false)
            if (status == 'success') {
                $('[data-list-index]').remove();

                for (let i in data) {
                    var a = $('[data-list-proto]').clone().attr('data-list-index', data[i].id).removeAttr('data-list-proto').removeClass('d-none')
                        .appendTo('[data-list-body]');

                    let index = parseInt(i) + 1;
                    let isDefault = data[i].isDefault==1 ? ' (Default)' : '';
                    $(a).find('[data-index]').attr('data-index', data[i].id)
                    $(a).find('[data-index]').text(index);
                    $(a).find('[data-richMenuId]').text(data[i].richMenuId);
                    $(a).find('[data-name]').text(data[i].name+isDefault);
                    $(a).find('[data-data]').text(JSON.stringify(JSON.parse(data[i].data), null, '    '));
                    $(a).find('[elm-databtn]').attr('data-target', '[elm-databox="' + data[i].id + '"]')
                    $(a).find('[elm-databox]').attr('elm-databox', data[i].id)
                    $(a).find('[data-action-rebuild]').click(function() {
                        reBuildRichmenu(data[i].id)
                    })
                    $(a).find('[data-action-delete]').click(function() {
                        deleteRichMenu(data[i].id)
                    })

                    if(data[i].image)
                    {
                        $(a).find('[data-image]').append('<a href="'+data[i].image+'" target="_blank"><img class="img-fluid" src="'+data[i].image+'"></a>');
                    }
                    else
                    {
                        $(a).find('[data-image]').append('<a href="#" onClick="loadImage(\''+data[i].richMenuId+'\')">Reload Image</a>');
                    }

                    if(data[i].isDefault==1)
                    {
                        $(a).find('[data-action-default]').remove()
                        $(a).find('[data-action-remove]').click(()=>{
                            unsetDefault(data[i].id);
                        })
                    }
                    else
                    {
                        $(a).find('[data-action-remove]').remove()
                        $(a).find('[data-action-default]').click(()=>{
                            setDefault(data[i].id);
                        })
                    }

                }

                hljs.configure({
                    useBR: false
                });

                document.querySelectorAll('pre code').forEach((block) => {
                    hljs.highlightBlock(block);
                });

            }
        }, 'JSON')
    }

    function reSync() {
        if (confirm('Sync Richmenu list from LINE OA')) {
            $('[elm-body] .btn').prop('disabled', true)
            $.get(site_url + '/Manage/Richmenu/syncRichMenu', (data, status) => {
                $('[elm-body] .btn').prop('disabled', false)
                if (status == 'success') {
                    // alert('Done !')
                    loadRichMenu();
                } else {
                    alert('Sync Failed')
                }
            })
        }
    }

    function refreshRichmenu() {
        loadRichMenu();
    }

    function createRichMenu() {
        if ($('#richmenuimage').get(0).files.length === 0) {
            alert('Please Select Rich Menu Image');
            return false;
        }

        if ($('#richmenudata').val().length === 0) {
            alert('Rich Menu Object Invalid');
            return false;
        }

        try {
            var json = JSON.parse($('#richmenudata').val());
        } catch (e) {
            alert('Rich Menu Object Invalid');
            return false;
        }

        return true;
    }

    function reBuildRichmenu(id) {
        alert('Re Build ' + id)
    }

    function deleteRichMenu(id) {
        if(confirm('Delete?'))
        {
            $.post(site_url + '/Manage/Richmenu/deleteRichmenu', { id: id },
            (data, status) => {
                console.log(status)
                console.log(data)
                if (status == 'success') {
                    if(data.save_status){
                        alert(data.save_message);
                    } else {
                        alert(data.save_message);
                    }
                    loadRichMenu();
                }
            })
        }
    }

    function loadImage(richMenuId) 
    {
        $.get(site_url + '/Manage/Richmenu/loadImage/'+richMenuId, (data, status) => {
                $('[elm-body] .btn').prop('disabled', false)
                if (status == 'success') {
                    // alert('Done !')
                    loadRichMenu();
                } else {
                    alert('Loading image Failed')
                }
            })
    }

    function setDefault(id)
    {
        if(confirm('Set Rich Menu to Default?'))
        {
            $.post(site_url + '/Manage/Richmenu/setDefault', { id: id },
            (data, status) => {
                console.log(status)
                console.log(data)
                if (status == 'success') {
                    if(data.save_status){
                        alert(data.save_message);
                    } else {
                        alert(data.save_message);
                    }
                    loadRichMenu();
                }
            })
        }
    }

    function unsetDefault(id)
    {
        if(confirm('Remove Default Rich Menu ?'))
        {
            $.post(site_url + '/Manage/Richmenu/unsetDefault', { id: id },
            (data, status) => {
                console.log(status)
                console.log(data)
                if (status == 'success') {
                    if(data.save_status){
                        alert(data.save_message);
                    } else {
                        alert(data.save_message);
                    }
                    loadRichMenu();
                }
            })
        }
    }
</script>