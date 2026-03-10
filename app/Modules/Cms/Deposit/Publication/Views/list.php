<?php
	$request = \Config\Services::request();
	$request->uri->setSilent();

	$slug = $request->getGet('slug')??'';
?>

<?=$this->extend(config('Core')->layout_backend);?>
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
                <div><?= lang('Publication.module') ?>
                    <div class="page-title-subheading"><?= lang('Publication.info.list_all') ?>
                        <?= lang('Publication.module') ?> </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('deposit/publication') ?>"><i
                                    class="fa fa-home"></i> <?= lang('Publication.label.home') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('Publication.module') ?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
		<li class="nav-item">
			<a class="nav-link <?=($slug == '')?'active':''?>" href="<?=base_url('deposit/publication?slug=')?>">
				<span>Semua</span>
			</a>
		</li>
		<?php foreach(get_ref('ref-publication') as $row):?>
			<li class="nav-item">
				<a class="nav-link <?=($slug == slugify($row->name))?'active':''?>" href="<?=base_url('deposit/publication?slug='.slugify($row->name))?>">
					<span><?=$row->name?></span>
				</a>
			</li>
		<?php endforeach;?>
	</ul>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate">
            </i><?= lang('Publication.label.table') ?> <?= lang('Publication.module') ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('deposit/publication/create')): ?>
                <a href="<?= base_url('deposit/publication/create') ?>" class=" btn btn-success" title=""><i
                        class="fa fa-plus"></i> <?= lang('Publication.action.add') ?> <?= lang('Publication.module') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body table-responsive">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_grid" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?= lang('Publication.field.no') ?> </th>
                        <th class="text-center" width="100">Kategori / Channel</th>
                        <th>Judul / Pengarang</th>
                        <th><?= lang('Publication.field.description') ?></th>
                        <th><?= lang('Publication.field.created_by') ?></th>
                        <th><?= lang('Publication.field.updated_by') ?></th>
                        <th><?= lang('Publication.label.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($publications as $row) : ?>
                    <?php
                        $default = base_url('uploads/default/no_cover.jpg');
                        // $image = base_url('uploads/publication/' . $row->file);
                        // $thumb = base_url('uploads/publication/thumb_' . $row->file);
                        if (empty($row->file)) {
                            $image = $default;
                            $thumb = $default;
                        }
                        ?>
                    <tr>
                        <td width="35" class="text-center"></td>
                        <td>
                            <badge class="badge badge-primary badge-pill"><?= _spec($row->category); ?></badge><br>
                            <badge class="badge badge-info badge-pill"><?= _spec($row->channel); ?></badge>
                        </td>
                        <td>
                            <?= _spec($row->title ?? ''); ?><br>
                            <badge class="badge badge-secondary"><?= _spec($row->author ?? ''); ?></badge>
                        </td>
                        <td>Penerbit : <?= _spec($row->publisher ?? ''); ?><br>
                            Kota : <?= _spec($row->city ?? ''); ?><br>
                            Tahun : <?= _spec($row->publication_year ?? ''); ?>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= _spec($row->created_at ?? ''); ?></span><br>
                            <span class="badge badge-info"><?= _spec($row->created_name ?? ''); ?></span>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= _spec($row->updated_at ?? '-'); ?></span><br>
                            <span class="badge badge-info"><?= _spec($row->updated_name ?? '-'); ?></span>
                        </td>
                        <td width="90">
                            <?php if (is_allowed('deposit/publication/update')): ?>
                            <a href="<?= base_url('deposit/publication/edit/' . $row->id) ?>" data-toggle="tooltip"
                                data-placement="top" title="Ubah Publikasi" class="btn btn-warning show-data"><i
                                    class="pe-7s-note font-weight-bold"> </i></a>
                            <?php endif; ?>
                            <?php if (is_allowed('deposit/publication/delete')): ?>
                            <a href="javascript:void(0);"
                                data-href="<?= base_url('deposit/publication/delete/' . $row->id); ?>" data-toggle="tooltip"
                                data-placement="top" title="Hapus Publikasi" class="btn btn-danger remove-data"><i
                                    class="pe-7s-trash font-weight-bold"> </i></a>
                            <?php endif; ?>
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
<?= $this->include('DepositPublication\Views\upload_modal'); ?>
<script>
setDataTable('#tbl_grid', disableOrderCols = [0], defaultOrderCols = [6, 'desc'], autoNumber = true);

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