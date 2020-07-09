<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/styles/vs2015.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/highlight.min.js"></script>

<style>
    .lds-ripple {
        display: inline-block;
        position: relative;
        width: 266px;
        height: 266px;
    }

    .lds-ripple div {
        position: absolute;
        border: 4px solid #666;
        opacity: 1;
        border-radius: 50%;
        animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
    }

    .lds-ripple div:nth-child(2) {
        animation-delay: -0.5s;
    }

    @keyframes lds-ripple {
        0% {
            top: 133px;
            left: 133px;
            width: 0;
            height: 0;
            opacity: 1;
        }

        100% {
            top: 0px;
            left: 0px;
            width: 266px;
            height: 266px;
            opacity: 0;
        }
    }
</style>

<script>
    var site_url = '<?php echo site_url('Manage/Richmenu'); ?>/'

    $(document).ready(() => {
        loadRichMenu();
        document.querySelectorAll('.code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    })

    function loadRichMenu() {
        $('[elm-body] .btn').prop('disabled', true)
        $.get(site_url + 'loadRichmenu', (data, status) => {
            $('[elm-body] .btn').prop('disabled', false)
            if (status == 'success') {
                $('[data-list-index]').remove();

                for (let i in data) {
                    var a = $('[data-list-proto]').clone().attr('data-list-index', data[i].id).removeAttr('data-list-proto').removeClass('d-none')
                        .appendTo('[data-list-body]');

                    let index = parseInt(i) + 1;
                    let isDefault = data[i].isDefault == 1 ? '<span class="badge badge-primary">DEFAULT</span> ' : '';
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

                    if (data[i].image) {
                        $(a).find('[data-image]').append('<a href="' + data[i].image + '" target="_blank"><img class="img-fluid" src="' + data[i].image + '"></a>');
                    } else {
                        $(a).find('[data-image]').append('<a href="#" class=" d-block " onClick="loadImage(\'' + data[i].richMenuId + '\')">Reload Image</a>');
                    }

                    if (data[i].isDefault == 1) {
                        $(a).find('[data-action-default]').remove()
                        $(a).find('[data-action-remove]').click(() => {
                            unsetDefault(data[i].id);
                        })
                    } else {
                        $(a).find('[data-action-remove]').remove()
                        $(a).find('[data-action-default]').click(() => {
                            setDefault(data[i].id);
                        })
                    }

                    if (isDefault) {
                        // $(a).addClass('table-active');
                        $(a).find('[data-name]').prepend(isDefault);
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
            $.get(site_url + 'syncRichMenu', (data, status) => {
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

        $('#richmenucreate').modal('hide');
        $('#loading-modal').modal('show');

        return true;
    }

    function reBuildRichmenu(id) {
        // alert('Re Build ' + id)

        // var obj = $('[data-list-index="'+ id +'"]');

    }

    function deleteRichMenu(id) {
        if (confirm('Delete?')) {
            $.post(site_url + 'deleteRichmenu', {
                    id: id
                },
                (data, status) => {
                    console.log(status)
                    console.log(data)
                    if (status == 'success') {
                        if (data.save_status) {
                            alert(data.save_message);
                        } else {
                            alert(data.save_message);
                        }
                        loadRichMenu();
                    }
                })
        }
    }

    function loadImage(richMenuId) {
        $.get(site_url + 'loadImage/' + richMenuId, (data, status) => {
            $('[elm-body] .btn').prop('disabled', false)
            if (status == 'success') {
                // alert('Done !')
                loadRichMenu();
            } else {
                alert('Loading image Failed')
            }
        })
    }

    function setDefault(id) {
        if (confirm('Set Rich Menu to Default?')) {
            $.post(site_url + 'setDefault', {
                    id: id
                },
                (data, status) => {
                    console.log(status)
                    console.log(data)
                    if (status == 'success') {
                        if (data.save_status) {
                            alert(data.save_message);
                        } else {
                            alert(data.save_message);
                        }
                        loadRichMenu();
                    }
                })
        }
    }

    function unsetDefault(id) {
        if (confirm('Remove Default Rich Menu ?')) {
            $.post(site_url + 'unsetDefault', {
                    id: id
                },
                (data, status) => {
                    console.log(status)
                    console.log(data)
                    if (status == 'success') {
                        if (data.save_status) {
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