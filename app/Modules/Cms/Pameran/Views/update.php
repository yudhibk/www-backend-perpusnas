<?php
$request = \Config\Services::request();
$request->uri->setSilent();
$slug = $request->getVar('slug');
$file_image_max = 4;
?>

<?= $this->extend(config('Core')->layout_backend) ?>
<?= $this->section('style') ?>
<style>
.tox.tox-tinymce.tox-fullscreen {
    z-index: 1050;
    top: 60px!important;
    left: 85px!important; 
    width: calc(100% - 85px) !important;
}
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>


<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-date icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Pameran
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url(
          'dashboard'
      ) ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item">Pameran </li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah Pameran </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Pameran
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? '' ?></div>
                  <?= get_message('message') ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="title">Judul Pameran*</label>
									<div>
										<input required type="text" class="form-control" id="title" name="title" placeholder="Judul Pameran" value="<?= set_value(
              'title',
              $pameran->title
          ) ?>" />
										<small class="info help-block text-muted">Permalink: <a href="<?= permalink(
              'publikasi/pameran/' . $pameran->slug
          ) ?>" target="_blank"><?= permalink(
    'publikasi/pameran/' . $pameran->slug
) ?></a></small>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Kategori*</label>
									<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref('ref-pameran', 'slug') as $row): ?>
											<option value="<?= $row->name ?>" <?= $row->name == $pameran->category
    ? 'selected'
    : '' ?>><?= $row->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Tanggal Post*</label>
									<div>
										<input required type="date" class="form-control" id="publish_date" name="publish_date" placeholder="Tanggal Post" value="<?= set_value(
              'publish_date',
              $pameran->publish_date
          ) ?>" />
									</div>
								</div>
							</div>

							<?php if (is_member('admin')): ?>
								<div class="col-md-6">
									<div class="position-relative form-group">
										<label>Unit Kerja (Group)*</label>
										<div class="select-wrapper">
											<select class="form-control select2" name="channel" id="channel" tabindex="-1" aria-hidden="true" style="width:100%">
												<option value="">Pilih</option>
												<?php foreach (
                get_ref_table(
                    'auth_groups',
                    'name,description',
                    'category="Unit Kerja"'
                )
                as $row
            ): ?>
													<option value="<?= $row->name ?>" <?= $row->name == $pameran->channel
    ? 'selected'
    : '' ?>><?= $row->description ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
								</div>
							<?php endif; ?>
                        </div>

						<div class="form-row">
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label for="date_from">Tanggal Mulai*</label>
									<div>
										<input required type="date" class="form-control" id="date_from" name="date_from" placeholder="Tanggal Mulai" value="<?= set_value(
              'date_from',
              $pameran->date_from
          ) ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label for="date_to">Tanggal Selesai*</label>
									<div>
										<input required type="date" class="form-control" id="date_to" name="date_to" placeholder="Tanggal Selesai" value="<?= set_value(
              'date_to',
              $pameran->date_to
          ) ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label required for="contact">Contact Person*</label>
									<div>
										<input type="text" class="form-control" id="contact" name="contact" placeholder="Contact Person" value="<?= set_value(
              'contact',
              $pameran->contact
          ) ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label for="organizer">Penyelenggara</label>
									<div>
										<input type="text" class="form-control" id="organizer" name="organizer" placeholder="Penyelenggara" value="<?= set_value(
              'organizer',
              $pameran->organizer
          ) ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="place">Tempat</label>
									<div>
										<input type="text" class="form-control" id="place" name="place" placeholder="Tempat" value="<?= set_value(
              'place',
              $pameran->place
          ) ?>" />
										<small class="text-muted">Contoh: Gedung Perpusnas Republik Indonesia</small>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="address">Alamat</label>
									<div>
										<input type="text" class="form-control" id="address" name="address" placeholder="Alamat" value="<?= set_value(
              'address',
              $pameran->address
          ) ?>" />
										<small class="text-muted">Jl. Medan Merdeka Selatan No.11, Jakarta 10110</small>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
								<label for="content">Uraian</label>
								<div>
									<textarea id="content" name="content" placeholder="" rows="1" class="form-control autosize-input"><?= set_value(
             'content',
             $pameran->content
         ) ?></textarea>
								</div>
						</div>

						<hr>
						<?php
      $default = base_url('uploads/default/no_cover.jpg');
      $file_cover = !empty($pameran->file_cover)
          ? base_url('uploads/pameran/' . $pameran->file_cover)
          : $default;
      ?>
						<div class="row">
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Preview Cover Berita</label>
									<div>
										<img src="<?= $file_cover ?>" alt="Image" class="img img-thumbnail">	
									</div>
								</div>
							</div>
							<div class="col-md-9">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Upload Cover Berita</label>
									<div id="file_cover" class="dropzone"></div>
									<div id="file_cover_listed"></div>
									<div>
										<small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @ 2MB</small>
									</div>
								</div>
							</div>
						</div>

						<hr>
						<?php $file_images = array_filter(explode(',', $pameran->file_image?? '')); ?>
						<?php $file_image_avail = $file_image_max - count($file_images);?>
						<div class="row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Preview Galeri Berita</label>
									<div class="row">
										<?php foreach ($file_images as $key => $value): ?>
											<?php $file_image = !empty($value)
               ? base_url('uploads/pameran/' . $value)
               : $default; ?>
											<div class="col-md-3">
												<div class="form-group">
													<div>
														<img src="<?= $file_image ?>" alt="Image" class="img img-thumbnail">	
														<div class="form-check">
															<input class="form-check-input check-remove" type="checkbox" data-value="<?= $value ?>" value="" name="file_image_del[]" id="file_image_del_<?= $key ?>">
															<label class="form-check-label" for="file_image_del_<?= $key ?>">
																Checklist untuk Hapus
															</label>
														</div>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
						<?php if ($file_image_avail > 0): ?>
							<div class="row">
								<div class="col-md-12">
									<div class="position-relative form-group">
										<label for="file_image" class="">Upload Galeri Berita</label>
										<div id="file_image" class="dropzone"></div>
										<div id="file_image_listed"></div>
										<div>
											<small class="info help-block text-muted">Format (JPG|PNG). Max <?= $file_image_max -
               count($file_images) ?> Files @ 2MB</small>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>

                        <div class="form-group">
                              <label for="description">Keterangan</label>
                              <div>
                                    <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value(
                                        'description',
                                        $pameran->description
                                    ) ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                        </div>
                  </form>
            </div>
    </div>
</div>


<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script>
	var file_cover = setDropzone('file_cover', 'cms/pameran', '.png,.jpg,.jpeg', 1, 2);
	var max = '<?= $file_image_max - count($file_images) ?>';
	var file_image = setDropzone('file_image', 'cms/pameran', '.png,.jpg,.jpeg,.mp3,.mp4,.mkv,.pdf', max, 2);
</script>
<script>
	$(document).ready(function() {
		tinyMCE.init({
			selector: 'textarea#content',
			height: 430,
			menubar: false,
			pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
			plugins: 'link code image table pagebreak media lists fullscreen',
			toolbar: 'fullscreen code removeformat | bold italic underline strikethrough | fontsizeselect fontselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed link anchor codesample | forecolor backcolor casechange permanentpen formatpainter |  undo redo pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
			font_formats: "System Font=Dosis, san serif; Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva;",
			fontsize_formats: "12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 28pt 32pt 34pt 36pt 72pt",
			content_style: "body { font-size: 12pt;}",
		});
	});
</script>
<?= $this->endSection('script') ?>
