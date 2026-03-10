<?php $core = config('Core');
$layout = (!empty($core->layout_backend)) ? $core->layout_backend : 'Views\layout\backend\main'; ?>
<?= $this->extend($layout); ?>
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
                <div><?= lang('Bahanpustaka.module') ?>
                    <div class="page-title-subheading"><?= lang('Bahanpustaka.info.list_all') ?> <?= lang('Bahanpustaka.module') ?> </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('deposit/pengajuan/bahan-pustaka') ?>"><i class="fa fa-home"></i> <?= lang('Bahanpustaka.label.home') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('Bahanpustaka.module') ?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= lang('Bahanpustaka.label.table') ?> <?= lang('Bahanpustaka.module') ?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('deposit/pengajuan/bahan-pustaka/create')): ?>
                    <a href="<?= base_url('deposit/pengajuan/bahan-pustaka/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i> <?= lang('Bahanpustaka.action.add') ?> <?= lang('Bahanpustaka.module') ?> </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body table-responsive">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_grid" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?= lang('Bahanpustaka.field.no') ?> </th>
                        <th>
                            <transitionle>

                                </transition>
                        </th>
                        <th>Konten</th>
                        <th><?= lang('Bahanpustaka.field.created_by') ?></th>
                        <th><?= lang('Bahanpustaka.field.updated_by') ?></th>
                        <th><?= lang('Bahanpustaka.label.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bahanpustakas as $row) : ?>
                        <?php
                        $default = base_url('uploads/default/no_cover.jpg');
                        $image = base_url('uploads/bahan-pustaka/' . $row->file);
                        $thumb = base_url('uploads/bahan-pustaka/thumb_' . $row->file);
                        if (empty($row->file)) {
                            $image = $default;
                            $thumb = $default;
                        }
                        ?>
                        <tr>
                            <td width="35"></td>
                            <td width="200">
                                <?= _spec($row->title); ?> <br>
                                <div class="mr-2 badge badge-pill badge-primary  text-lowercase">
                                    <?= _spec($row->slug); ?>
                                </div>
                            </td>
                            <td><?= substr(strip_tags($row->content), 0, 100) . '...'; ?></td>
                            <td width="100">
                                <span class="badge badge-info"><?= _spec($row->created_at); ?></span><br>
                                <span class="badge badge-info"><?= _spec($row->created_name); ?></span>
                            </td>
                            <td width="100">
                                <span class="badge badge-info"><?= _spec($row->updated_at); ?></span><br>
                                <span class="badge badge-info"><?= _spec($row->updated_name ?? '-'); ?></span>
                            </td>
                            <td width="90">
                                <?php if (is_allowed('deposit/pengajuan/bahan-pustaka/update')): ?>
                                    <a href="<?= base_url('deposit/pengajuan/bahan-pustaka/edit/' . $row->id) ?>" data-toggle="tooltip" data-placement="top" title="Ubah Pengajuan Bahan Pustaka" class="btn btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>
                                <?php endif; ?>
                                <?php if (is_allowed('deposit/pengajuan/bahan-pustaka/delete')): ?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('deposit/pengajuan/bahan-pustaka/delete/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Hapus Pengajuan Bahan Pustaka" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
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
<?= $this->include('DepositPengajuanBahanpustaka\Views\upload_modal'); ?>
<script>
    setDataTable('#tbl_grid', disableOrderCols = [0, 5], defaultOrderCols = [4, 'desc'], autoNumber = true);

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