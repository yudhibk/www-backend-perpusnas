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
                <div>Banner 
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item">Banner </li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah Banner </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Banner
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="name">Judul Banner*</label>
									<div>
										<input type="text" class="form-control" id="title" name="title" placeholder="Judul Banner" value="<?= set_value('name', $banner->title); ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Kategori*</label>
									<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref('ref-banner','slug') as $row) : ?>
											<option value="<?= $row->name ?>" <?=($row->name == $banner->category)?'selected':''?>><?= $row->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<?php if(is_member('admin')):?>
								<div class="col-md-6">
									<div class="position-relative form-group">
										<label>Unit Kerja (Group)*</label>
										<div class="select-wrapper">
											<select class="form-control select2" name="channel" id="channel" tabindex="-1" aria-hidden="true" style="width:100%">
												<option value="">Pilih</option>
												<?php foreach (get_ref_table('auth_groups', 'name,description', 'category="Unit Kerja"') as $row): ?>
													<option value="<?=$row->name?>" <?=($row->name == $banner->channel)?'selected':''?>><?=$row->description?></option>
												<?php endforeach;?>
											</select>
										</div>
									</div>
								</div>
							<?php endif;?>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label for="sort">Urutan</label>
									<div>
										<input type="number" class="form-control" name="sort" id="sort" placeholder="Urutan " value="<?=set_value('sort', $banner->sort);?>" />
									</div>
								</div>
							</div>
                        </div>

                        <div class="form-group">
                              <label for="description">Keterangan</label>
                              <div>
                                    <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $banner->description) ?></textarea>
                              </div>
                        </div>

						<hr>
						<?php 
							$default = base_url('uploads/default/no_cover.jpg'); 
							$file_image = (!empty($banner->file_image)) ? base_url('uploads/banner/' . $banner->file_image) : $default;
						?>
						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="content">Preview Banner</label>
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
	var file_image = setDropzone('file_image', 'cms/banner', '.png,.jpg,.jpeg', 1, 2);
</script>
<?= $this->endSection('script'); ?>