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
                    <i class="pe-7s-albums icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Koleksi <?=$koleksi->category_sub?>
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item">Koleksi </li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah <?=$koleksi->category_sub?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Koleksi
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="name">Judul Koleksi*</label>
									<div>
										<input required type="text" class="form-control" id="title" name="title" placeholder="Judul Koleksi" value="<?= set_value('title', $koleksi->title); ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="slug">Slug*</label>
									<div>
										<input required type="text" class="form-control" id="slug" name="slug" placeholder="Slug" value="<?= set_value('slug', $koleksi->slug); ?>" />
										<small class="info help-block text-muted">Permalink: <a href="<?=permalink('publikasi/koleksi/'.$koleksi->slug)?>" target="_blank"><?=permalink('publikasi/koleksi/'.$koleksi->slug)?></a></small>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Kategori*</label>
									<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
										<option value="Koleksi">Koleksi</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Sub Kategori*</label>
									<select class="form-control" name="category_sub" id="category_sub" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref('ref-koleksi','slug') as $row) : ?>
											<option value="<?= $row->name ?>" <?=($row->name == $koleksi->category_sub)?'selected':''?>><?= $row->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="position-relative form-group">
									<label>Tanggal Post*</label>
									<div>
										<input required type="date" class="form-control" id="publish_date" name="publish_date" placeholder="Tanggal Post" value="<?= set_value('publish_date', $koleksi->publish_date); ?>" />
									</div>
								</div>
							</div>
                        </div>

						<div class="form-group">
                              <label for="content">Uraian</label>
                              <div>
                                    <textarea id="content" name="content" placeholder="" rows="1" class="form-control autosize-input"><?= set_value('content', $koleksi->content); ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description">Keterangan</label>
                              <div>
                                    <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $koleksi->description) ?></textarea>
                              </div>
                        </div>

						<hr>
						<?php 
							$default = base_url('uploads/default/no_cover.jpg'); 
							$file_cover = (!empty($koleksi->file_cover)) ? base_url('uploads/koleksi/' . $koleksi->file_cover) : $default;
						?>
						<div class="row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Preview Cover Koleksi</label>
									<div>
										<img src="<?=$file_cover?>" alt="Image" class="img img-thumbnail">	
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Upload Cover Koleksi</label>
									<div id="file_cover" class="dropzone"></div>
									<div id="file_cover_listed"></div>
									<div>
										<small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @2MB</small>
									</div>
								</div>
							</div>
						</div>

						<hr>
						<div class="row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label>Sumber Galeri Koleksi</label>
									<div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="file_source" id="source1" value="upload" <?=($koleksi->file_source == 'upload')?'checked':''?>>
											<label class="form-check-label" for="source1">Upload File</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="file_source" id="source2" value="url" <?=($koleksi->file_source == 'url')?'checked':''?>>
											<label class="form-check-label" for="source2">URL File</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row file_source_upload" style="display:<?=($koleksi->file_source == 'upload')?'blok':'none'?>">
							<?php
								$file_images = array_filter(explode(',', $koleksi->file_image?? '')); 
								$file_image_avail = $file_image_max - count($file_images);
							?>
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="file_cover" class="">Preview Galeri Koleksi</label>
									<div class="row">
										<?=(count($file_images) == 0)?'<div class="col-md-6">-</div>':''?>

										<?php foreach($file_images as $key => $value):?>
											<?php 
												$file_image = (!empty($value)) ? base_url('uploads/koleksi/' . $value) : $default;
												if(strtolower($koleksi->category_sub) == 'flipbook'){
													$file_image =  base_url('flip?file=uploads/koleksi/' . $value);
												}
											?>
											<?php if($koleksi->category_sub == 'Video'):?>
												<div class="col-md-6">
													<video controls="controls" controlsList="nodownload" style="width: 100%; height:auto">
														<source src="<?=$file_image?>" />
													</video>
													<div class="form-check">
														<input class="form-check-input check-remove" type="checkbox" data-value="<?=$value?>" value="" name="file_image_del[]" id="file_image_del_<?=$key?>">
														<label class="form-check-label" for="file_image_del_<?=$key?>">
															Hapus File <?=$key+1?>
														</label>
													</div>
												</div>
											<?php elseif($koleksi->category_sub == 'Audio'):?>
												<div class="col-md-6">
													<audio controls controlsList="nodownload" style="width: 100%; height:50px">
														<source src="<?=$file_image?>" type="audio/mpeg" />
													</audio>
													<div class="form-check">
														<input class="form-check-input check-remove" type="checkbox" data-value="<?=$value?>" value="" name="file_image_del[]" id="file_image_del_<?=$key?>">
														<label class="form-check-label" for="file_image_del_<?=$key?>">
															Hapus File <?=$key+1?>
														</label>
													</div>
												</div>
											<?php elseif($koleksi->category_sub == 'Foto'):?>
												<div class="col-md-3">
													<div class="form-group">
														<div>
															<img src="<?=$file_image?>" alt="Image" class="img img-thumbnail">	
															<div class="form-check">
																<input class="form-check-input check-remove" type="checkbox" data-value="<?=$value?>" value="" name="file_image_del[]" id="file_image_del_<?=$key?>">
																<label class="form-check-label" for="file_image_del_<?=$key?>">
																	Hapus File <?=$key+1?>
																</label>
															</div>
														</div>
													</div>
												</div>
											<?php else:?>
												<div class="col-md-3">
													<div class="form-group">
														<div>
															<a class="popup-link btn btn-primary" href="<?=$file_image?>">File Flipbook <?=$key+1?></a><br>
															<div class="form-check">
																<input class="form-check-input check-remove" type="checkbox" data-value="<?=$value?>" value="" name="file_image_del[]" id="file_image_del_<?=$key?>">
																<label class="form-check-label" for="file_image_del_<?=$key?>">
																	Hapus File <?=$key+1?>
																</label>
															</div>
														</div>
													</div>
												</div>
											<?php endif;?>
										<?php endforeach;?>
									</div>
								</div>
							</div>

							<?php if($file_image_avail > 0):?>
								<div class="col-md-12">
									<div class="position-relative form-group">
										<label for="file_image" class="">Upload Galeri Koleksi</label>
										<div id="file_image" class="dropzone"></div>
										<div id="file_image_listed"></div>
										<div>
											<small class="info help-block text-muted">Format (JPG|PNG). Max <?=($file_image_max - count($file_images))?> Files @10MB</small>
										</div>
									</div>
								</div>
							<?php endif;?>
						</div>

						<div class="row file_source_url" style="display:<?=($koleksi->file_source == 'url')?'blok':'none'?>">
							<?php
								$file_urls = explode(',',$koleksi->file_url);							
							?>
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="file_cover" class="">URL Galeri Koleksi</label>
									<div class="row">
										<?php foreach($file_urls as $key => $value):?>
											<div class="col-md-6">
												<input type="text" class="form-control mb-3" name="file_url[]" placeholder="URL File" value="<?= set_value('file_url[]', $value); ?>" />
											</div>
										<?php endforeach;?>
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
	var file_cover = setDropzone('file_cover', 'cms/koleksi', '.png,.jpg,.jpeg', 1, 2);
	var max = '<?=$file_image_max - count($file_images)?>';
	var file_image = setDropzone('file_image', 'cms/koleksi', '.png,.jpg,.jpeg,.mp3,.mp4,.mkv,.pdf', max, 10);
</script>
<script>
	$('.check-remove').click(function(){
		var value = $(this).data('value');
		$(this).attr('value', value);
	});

	$('.image-link').magnificPopup({
		type: 'image'
	});

	$('.popup-link').magnificPopup({
        type: 'iframe',
        iframe: {
            markup: '<style>.mfp-iframe-holder .mfp-content {max-width: 95%;height:95%}</style>' +
                '<div class="mfp-iframe-scaler" >' +
                '<div class="mfp-close"></div>' +
                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                '</div></div>'
        },
    });

	$(document).ready(function() {
		$('input[type=radio][name=file_source]').change(function() {
			if (this.value == 'url') {
				$('.file_source_url').show();
				$('.file_source_upload').hide();
			}
			else if (this.value == 'upload') {
				$('.file_source_url').hide();
				$('.file_source_upload').show();
			}
		});

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