<?= $this->extend(config('Core')->layout_backend);?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-photo icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= lang('Page.module') ?>
                    <div class="page-title-subheading"><?= lang('Page.info.list_all') ?> <?= lang('Page.module') ?>
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('page') ?>"><i class="fa fa-home"></i>
                                <?= lang('Page.label.home') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('Page.module') ?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate">
            </i><?= lang('Page.label.table') ?> <?= lang('Page.module') ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if(is_allowed('page/create')):?>
                <a href="<?= base_url('page/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
                    <?= lang('Page.action.add') ?> <?= lang('Page.module') ?> </a>
                <?php endif;?>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_pages" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?= lang('Page.field.no') ?> </th>
                        <th>Foto Cover</th>
                        <th>Judul Halaman / Kategori</th>
                        <th><?= lang('Page.field.description') ?></th>
                        <th><?= lang('Page.field.sort') ?></th>
                        <th><?= lang('Page.field.active') ?></th>
                        <th><?= lang('Page.field.created_by') ?></th>
                        <th><?= lang('Page.field.updated_by') ?></th>
                        <th><?= lang('Page.label.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $row) : ?>
                    <?php 
							$default = base_url('uploads/default/no_cover.jpg');
							$image = base_url('uploads/page/' . $row->file_image);
							$thumb = base_url('uploads/page/thumb_' . $row->file_image);
							if (empty($row->file_image)) {
								$image = $default;
								$thumb = $default;
							}
						?>
                    <tr>
                        <td width="35"></td>
                        <td width="100" style="vertical-align: bottom;">
                            <a href="<?=$image?>" class="image-link">
                                <img width="100" class="rounded" src="<?=$thumb?>"
                                    onerror="this.onerror=null;this.src='<?=$default?>';" alt="">
                            </a>
                            <a href="javascript:void(0);" data-title="Foto Cover"
                                data-format-title="Format (JPG|PNG). Max 10MB" data-format=".jpg,.jpeg,.png"
                                data-dropzone-url="" data-url="" data-redirect="<?= base_url('page') ?>"
                                data-id="<?=$row->id?>" data-field="file_image" data-title="" data-toggle="tooltip"
                                data-placement="top" title="Upload "
                                class="btn btn-sm btn-block btn-secondary upload-data mt-1"
                                style="min-width:35px"><small><i class="fa fa-upload"> </i> Upload</small></a>
                        </td>
                        <td width="200">
                            <?= _spec($row->name); ?> <br>
                            <div class="mr-2 badge badge-pill badge-primary">
                                <?= _spec($row->category); ?>
                            </div>
                        </td>
                        <td><?= _spec($row->description); ?></td>
                        <td width="35"><?= _spec($row->sort); ?></td>
                        <td width="50">
                            <input type="checkbox" class="apply-status"
                                data-href="<?= base_url('page/apply_status'); ?>" data-field="active"
                                data-id="<?=$row->id?>" <?= ($row->active == 1) ? 'checked' : '' ?> data-toggle="toggle"
                                data-onstyle="success">
                        </td>
                        <td width="100">
                            <span class="badge badge-info"><?= _spec($row->created_at); ?></span><br>
                            <span class="badge badge-info"><?= _spec($row->created_name); ?></span>
                        </td>
                        <td width="100">
                            <span class="badge badge-info"><?= _spec($row->updated_at); ?></span><br>
                            <span class="badge badge-info"><?= _spec($row->updated_name ?? '-'); ?></span>
                        </td>
                        <td width="90">
                            <?php if(is_allowed('page/update')):?>
                            <a href="<?= base_url('page/edit/' . $row->id) ?>" data-toggle="tooltip"
                                data-placement="top" title="Ubah Page" class="btn btn-warning show-data"><i
                                    class="pe-7s-note font-weight-bold"> </i></a>
                            <?php endif;?>
                            <?php if(is_allowed('page/delete')):?>
                            <a href="javascript:void(0);" data-href="<?= base_url('page/delete/' . $row->id); ?>"
                                data-toggle="tooltip" data-placement="top" title="Hapus Page"
                                class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
                            <?php endif;?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
Dropzone.autoDiscover = false;
</script>
<?=$this->include('Page\Views\upload_modal');?>
<script>
setDataTable('#tbl_pages', disableOrderCols = [0, 8], defaultOrderCols = [7, 'desc'], autoNumber = true);

$("body").on("click", ".remove-data", function() {
    var url = $(this).attr('data-href');
    Swal.fire({
        title: '<?= lang('App.swal.are_you_sure') ?>',
        text: "<?= lang('App.swal.can_not_be_restored') ?>",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#dd6b55',
        confirmButtonText: '<?= lang('App.btn.yes') ?>',
        cancelButtonText: '<?= lang('App.btn.no') ?>'
    }).then((result) => {
        if (result.value) {
            window.location.href = url;
        }
    });
    return false;
});
</script>
<?= $this->endSection('script'); ?>