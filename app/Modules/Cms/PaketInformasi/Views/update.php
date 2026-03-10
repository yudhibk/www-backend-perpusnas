<?php
$request = \Config\Services::request();
$request->uri->setSilent();
?>

<?= $this->extend(config('Core')->layout_backend);?>
<?= $this->section('style'); ?>
<style>
.tox.tox-tinymce.tox-fullscreen {
    z-index: 1050;
    top: 60px!important;
    left: 85px!important; 
    width: calc(100% - 85px) !important;
}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>


<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-photo icon-gradient bg-strong-bliss"></i>
                </div>
                <div>PaketInformasi 
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item">PaketInformasi </li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah PaketInformasi </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah PaketInformasi
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Kategori*</label>
									<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref('ref-paketinformasi','slug') as $row) : ?>
											<option value="<?= $row->name ?>" <?=($row->name == $paketinformasi->category)?'selected':''?>><?= $row->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="col-md-8">
								<div class="position-relative form-group">
									<label for="name">Judul PaketInformasi*</label>
									<div>
										<input type="text" class="form-control" id="title" name="title" placeholder="Judul PaketInformasi" value="<?= set_value('name', $paketinformasi->title); ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-1">
								<div class="position-relative form-group">
									<label for="sort">Urutan</label>
									<div>
										<input type="number" class="form-control" name="sort" id="sort" placeholder="Urutan " value="<?=set_value('sort', $paketinformasi->sort);?>" />
									</div>
								</div>
							</div>
                        </div>

                        <div class="form-group">
                              <label for="description">Keterangan</label>
                              <div>
                                    <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $paketinformasi->description) ?></textarea>
                              </div>
                        </div>

						<hr>
						<?php 
							$default = base_url('uploads/default/no_cover.jpg'); 
							$file_image = (!empty($paketinformasi->file_image)) ? base_url('uploads/paketinformasi/' . $paketinformasi->file_image) : $default;
						?>
						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="content">Preview PaketInformasi</label>
									<div>
										<img width="100%" src="<?=$file_image?>" alt="Image" class="img">
									</div>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="file_image" class="">Upload File</label>
									<div id="file_image" class="dropzone"></div>
									<div id="file_image_listed"></div>
									<div>
										<small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @ 2MB</small>
									</div>
								</div>
							</div>
						</div>

                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                        </div>
                  </form>
            </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	var file_image = setDropzone('file_image', 'cms/paketinformasi', '.png,.jpg,.jpeg', 1, 2);
</script>
<?= $this->endSection('script'); ?>