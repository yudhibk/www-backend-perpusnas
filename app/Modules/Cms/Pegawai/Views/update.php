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
                    <i class="pe-7s-user icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Pegawai 
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item">Pegawai </li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah Pegawai </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Data Pegawai
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="name">Nama*</label>
									<div>
										<input type="text" class="form-control" id="name" name="name" placeholder="Nama Lengkap" value="<?= set_value('name', $pegawai->name); ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
										<label for="name">Golongan*</label>
										<div>
											<input type="text" class="form-control" id="class" name="class" placeholder="Golongan" value="<?= set_value('name', $pegawai->class); ?>" />
										</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label>Unit Kerja*</label>
									<div class="select-wrapper">
										<select class="form-control select2" name="division" id="division" tabindex="-1" aria-hidden="true" style="width:100%">
											<option value="">Pilih</option>
											<?php foreach (get_ref_table('auth_groups', 'name,description', 'category="Unit Kerja"') as $row): ?>
												<option value="<?=$row->description?>" <?=($row->description == $pegawai->division)?'selected':''?>><?=$row->description?></option>
											<?php endforeach;?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label for="sort">NIP</label>
									<div>
										<input type="number" class="form-control" name="nip" id="nip" placeholder="NIP" value="<?=set_value('sort', $pegawai->nip);?>" />
									</div>
								</div>
							</div>
                        </div>

                        <div class="form-group">
                              <label for="description">Jabatan</label>
                              <div>
                                    <textarea id="description" name="position" placeholder="Jabatan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('position', $pegawai->position) ?></textarea>
                              </div>
                        </div>

						<hr>
						<?php 
							$default = base_url('uploads/default/no_cover.jpg'); 
							$file_image = (!empty($pegawai->file)) ? base_url('uploads/pegawai/' . $pegawai->file) : $default;
						?>
						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="content">Preview Pegawai</label>
									<div style="text-align: center;">
										<img width="50%" src="<?=$file_image?>" alt="Image" class="img">
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
	var file_image = setDropzone('file_image', 'cms/pegawai', '.png,.jpg,.jpeg', 1, 2);
</script>
<?= $this->endSection('script'); ?>