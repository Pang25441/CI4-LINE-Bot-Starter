<div class="container-fluid" elm-body>

    <h1 class="mb-3">Manage/RichMenu</h1>

    <div class="row mb-1">
        <div class="col-auto mr-auto">
            <button type="button" class="btn btn-sm btn-danger" onclick="reSync()">Re-Sync RichMenu</button>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-sm btn-secondary" onclick="refreshRichmenu()">Refresh</button>
            <button type="button" class="btn btn-sm btn-success">Create</button>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">richMenuId</th>
                <th scope="col" class="text-center">Name</th>
                <th scope="col" class="text-center">Data</th>
                <th scope="col" class="text-center">Image</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody data-list-body>
            <tr data-list-proto class="d-none">
                <th scope="row" data-index>{index}</th>
                <td data-richMenuId>{richMenuId}</td>
                <td data-name>{Name}</td>
                <td>
                    <a elm-databtn href="#" data-toggle="modal" data-target="">Show Data</a>

                    <div elm-databox class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg ">
                            <div class="modal-content">
                                <div class="modal-body">
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
                            <button class="dropdown-item" type="button" data-action-rebuild>Re-Create</button>
                            <button class="dropdown-item" type="button" data-action-delete>Delete</button>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

</div>

<script>
    var site_url = '<?php echo site_url(); ?>'

    $(document).ready(() => {
        loadRichMenu();
    })

    function loadRichMenu() {
        $('[elm-body] .btn').prop('disabled', true)
        $.get(site_url + '/Manage/loadRichmenu', (data, status) => {
            $('[elm-body] .btn').prop('disabled', false)
            if (status == 'success') {
                $('[data-list-index]').remove();

                for (let i in data) {
                    var a = $('[data-list-proto]').clone().attr('data-list-index', i).removeClass('d-none')
                        .appendTo('[data-list-body]');

                    let index = parseInt(i) + 1;
                    $(a).find('[data-index]').attr('data-index', data[i].id)
                    $(a).find('[data-index]').text(index);
                    $(a).find('[data-richMenuId]').text(data[i].richMenuId);
                    $(a).find('[data-name]').text(data[i].name);
                    $(a).find('[data-data]').text(JSON.stringify(JSON.parse(data[i].data), null, '    '));
                    $(a).find('[elm-databtn]').attr('data-target', '[elm-databox="' + data[i].id + '"]')
                    $(a).find('[elm-databox]').attr('elm-databox', data[i].id)
                    $(a).find('[data-action-rebuild]').click(function() {
                        reBuildRichmenu(data[i].id)
                    })
                    $(a).find('[data-action-delete]').click(function() {
                        deleteRichMenu(data[i].id)
                    })

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
            $.get(site_url + '/Manage/syncRichMenu', (data, status) => {
                $('[elm-body] .btn').prop('disabled', false)
                if(status == 'success'){
                    alert('Done !')
                    loadRichMenu();
                }
                else
                {
                    alert('Sync Failed')
                }
            })
        }
    }

    function refreshRichmenu() {
        loadRichMenu();
    }

    function createRichMenu() {

    }

    function reBuildRichmenu(id) {
        alert('Re Build ' + id)
    }

    function deleteRichMenu(id) {
        alert('Delete ' + id)
    }
</script>