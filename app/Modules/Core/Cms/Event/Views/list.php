<?php 
// ini_set('upload_max_filesize','32M');
// ini_set('post_max_size','32M');
// echo ini_get('upload_max_filesize'), ", " , ini_get('post_max_size');
?>
<?= $this->extend(config('Core')->layout_backend);?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-date icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= lang('Event.module') ?> 
                    <div class="page-title-subheading"><?= lang('Event.info.list_all') ?>  <?= lang('Event.module') ?> </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('event') ?>"><i class="fa fa-home"></i> <?= lang('Event.label.home') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="event"><?= lang('Event.module') ?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= lang('Event.label.table') ?> <?= lang('Event.module') ?> 
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if(is_allowed('event/create')):?>
                    <a href="<?= base_url('event/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i> <?= lang('Event.action.add') ?> <?= lang('Event.module') ?> </a>
                <?php endif;?>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_events" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?= lang('Event.field.no') ?> </th>
                        <th>File Gambar</th>
                        <th>File PDF</th>
                        <th><?= lang('Event.field.name') ?> / Kategori</th>
                        <th><?= lang('Event.field.description') ?></th>
                        <th><?= lang('Event.field.active') ?></th>
                        <th><?= lang('Event.field.created_by') ?></th>
                        <th><?= lang('Event.field.updated_by') ?></th>
                        <th><?= lang('Event.label.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $row) : ?>
						<?php 
							$default = base_url('uploads/default/no_image.jpg');
							$image = base_url('uploads/event/' . $row->file_image);
							$thumb = base_url('uploads/event/thumb_' . $row->file_image);
							if (empty($row->file_image)) {
								$image = $default;
								$thumb = $default;
							}
						?>
                        <tr>
                            <td width="35"></td>
							<td width="100" style="vertical-align: bottom;">
								<a href="<?=$image?>" class="image-link">
									<img width="100" class="rounded" src="<?=$thumb?>" onerror="this.onerror=null;this.src='<?=$default?>';" alt="">
								</a>
								<a href="javascript:void(0);" data-title="Cover" data-format-title="Format (JPG|PNG). Max 10MB" data-format=".jpg,.jpeg,.png" data-dropzone-url="" data-url="" data-redirect="<?= base_url('event') ?>" data-id="<?=$row->id?>" data-field="file_image" data-title="" data-toggle="tooltip" data-placement="top"  title="Upload " class="btn btn-sm btn-block btn-secondary upload-data mt-1" style="min-width:35px"><small><i class="fa fa-upload"> </i> Upload</small></a>
                            </td>
							<td width="100" style="vertical-align: bottom;">
                                <?php if(!empty($row->file_pdf)):?>
                                    <a href="<?= base_url('flip?path=event&file=' . $row->file_pdf) ?>" class="ajax-popup-link">
										<img width="80" class="rounded" src="<?= base_url('uploads/default/pdf.png') ?>" alt="">
                                    </a>
                                <?php else:?>
                                    -
                                <?php endif;?>
								<a href="javascript:void(0);" data-title="File" data-format-title="Format (PDF). Max 10MB" data-format=".pdf" data-dropzone-url="" data-url="" data-redirect="<?= base_url('event') ?>" data-id="<?=$row->id?>" data-field="file_pdf" data-title="" data-toggle="tooltip" data-placement="top"  title="Upload " class="btn btn-sm btn-block btn-secondary upload-data mt-1" style="min-width:35px"><small><i class="fa fa-upload"> </i> Upload</small></a>
                            </td>
                            <td width="200">
								<?= _spec($row->name); ?> <br>
                                <div class="mr-2 badge badge-pill badge-primary">
                                    <?= _spec($row->category); ?>
                                </div>
                            </td>
                            <td><?= _spec($row->description); ?></td>
                            <td width="50">
                                <input type="checkbox" class="apply-status" data-href="<?= base_url('event/apply_status'); ?>" data-field="active" data-id="<?=$row->id?>" <?= ($row->active == 1) ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success">
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
                                <?php if(is_allowed('event/update')):?>
                                    <a href="<?= base_url('event/edit/' . $row->id) ?>" data-toggle="tooltip" data-placement="top" title="Ubah Event" class="btn btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>
                                <?php endif;?>
                                <?php if(is_allowed('event/delete')):?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('event/delete/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Hapus Event" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
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
<?=$this->include('Event\Views\upload_modal');?>
<script>
    setDataTable('#tbl_events', disableOrderCols = [0, 8], defaultOrderCols = [7, 'desc'], autoNumber = true);

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