<?php
$request = \Config\Services::request();
$request->uri->setSilent();

$slug = $request->getVar('slug');
$file_image_max = 4;
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
                    <i class="pe-7s-chat icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Testimoni <?=$testimoni->category_sub?>
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item">Testimoni </li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah <?=$testimoni->category_sub?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Testimoni
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="name">Judul Testimoni*</label>
									<div>
										<input required type="text" class="form-control" id="title" name="title" placeholder="Judul Testimoni" value="<?= set_value('title', $testimoni->title); ?>" />
										<small class="info help-block text-muted">Permalink: <a href="<?=permalink('publikasi/testimoni/'.$testimoni->slug)?>" target="_blank"><?=permalink('publikasi/testimoni/'.$testimoni->slug)?></a></small>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Kategori*</label>
									<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref('ref-testimoni','slug') as $row) : ?>
											<option value="<?= $row->name ?>" <?=($row->name == $testimoni->category)?'selected':''?>><?= $row->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Tanggal Post*</label>
									<div>
										<input required type="date" class="form-control" id="publish_date" name="publish_date" placeholder="Tanggal Post" value="<?= set_value('publish_date', $testimoni->publish_date); ?>" />
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Bahasa*</label>
									<select class="form-control" name="language" id="language" tabindex="-1" aria-hidden="true">
										
											<option value="<?= set_value('language', $testimoni->language); ?>"><?=$testimoni->language?></option>
											<option value="id">id</option>
									<option value="en">en</option>
										
									</select>
								</div>
							</div>
                        </div>

						<div class="form-group">
                              <label for="content">Uraian</label>
                              <div>
                                    <textarea id="content" name="content" placeholder="" rows="1" class="form-control autosize-input"><?= set_value('content', $testimoni->content); ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description">Keterangan</label>
                              <div>
                                    <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $testimoni->description) ?></textarea>
                              </div>
                        </div>

						<hr>
						<?php 
							$default = base_url('uploads/default/no_cover.jpg'); 
							$file_cover = (!empty($testimoni->file_cover)) ? base_url('uploads/testimoni/' . $testimoni->file_cover) : $default;
						?>
						<div class="row">
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Preview Cover Testimoni</label>
									<div>
										<img src="<?=$file_cover?>" alt="Image" class="img img-thumbnail">	
									</div>
								</div>
							</div>
							<div class="col-md-9">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Upload Cover Testimoni</label>
									<div id="file_cover" class="dropzone"></div>
									<div id="file_cover_listed"></div>
									<div>
										<small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @ 2MB</small>
									</div>
								</div>
							</div>
						</div>

						<hr>
						<?php $file_images = array_filter(explode(',', $testimoni->file_image?? '')); ?>
						<?php $file_image_avail = $file_image_max - count($file_images);?>
						<div class="row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Preview Galeri Testimoni</label>
									<div class="row">
										<?php foreach($file_images as $key => $value):?>
											<?php 
												$file_image = (!empty($value)) ? base_url('uploads/testimoni/' . $value) : $default;
											?>
											<div class="col-md-3">
												<div class="form-group">
													<div>
														<img src="<?=$file_image?>" alt="Image" class="img img-thumbnail">	
														<div class="form-check">
															<input class="form-check-input check-remove" type="checkbox" data-value="<?=$value?>" value="" name="file_image_del[]" id="file_image_del_<?=$key?>">
															<label class="form-check-label" for="file_image_del_<?=$key?>">
																Checklist untuk Hapus
															</label>
														</div>
													</div>
												</div>
											</div>
										<?php endforeach;?>
									</div>
								</div>
							</div>
						</div>
						<?php if($file_image_avail > 0):?>
							<div class="row">
								<div class="col-md-12">
									<div class="position-relative form-group">
										<label for="file_image" class="">Upload Galeri Testimoni</label>
										<div id="file_image" class="dropzone"></div>
										<div id="file_image_listed"></div>
										<div>
											<small class="info help-block text-muted">Format (JPG|PNG). Max <?=($file_image_max - count($file_images))?> Files @ 2MB</small>
										</div>
									</div>
								</div>
							</div>
						<?php endif;?>

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
	var file_cover = setDropzone('file_cover', 'cms/testimoni', '.png,.jpg,.jpeg', 1, 2);
	var max = '<?=$file_image_max - count($file_images)?>';
	var file_image = setDropzone('file_image', 'cms/testimoni', '.png,.jpg,.jpeg,.mp3,.mp4,.mkv,.pdf', max, 2);
</script>
<script>
	$('.check-remove').click(function(){
		var value = $(this).data('value');
		$(this).attr('value', value);
	});

	$(document).ready(function() {
		tinyMCE.init({
			selector: 'textarea#content',
			height: 430,
			menubar: false,
			pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
			plugins: 'link code image table pagebreak media lists fullscreen',
			toolbar: 'fullscreen code removeformat | bold italic underline strikethrough | fontsizeselect fontselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed link anchor codesample | forecolor backcolor casechange permanentpen formatpainter |  undo redo pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
			font_formats: "System Font=Dosis, san serif; Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva;",
			setup: function(editor) {
				editor.on('init', function(e) {
					editor.execCommand("fontName", true, "System Font");
					editor.setContent(content);
				});
			},
			fontsize_formats: "12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 28pt 32pt 34pt 36pt 72pt",
			content_style: "body { font-size: 12pt;}",
		});
	});
</script>
<?= $this->endSection('script'); ?>