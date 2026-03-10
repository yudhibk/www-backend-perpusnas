<?php
$request = \Config\Services::request();
$request->uri->setSilent();
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
                    <i class="pe-7s-news-paper icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Ubah  Artikel Berita
                    <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('direktori') ?>"> Artikel Berita</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah  Artikel Berita</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah  Artikel Berita
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
							<div class="col-md-8">
								<div class="position-relative form-group">
									<label for="name">Nama Direktori*</label>
									<div>
										<input type="text" class="form-control" id="title" name="title" placeholder="Nama Direktori" value="<?= set_value('title', $direktori->title); ?>" />
									</div>
								</div>
							</div>
						
							<div class="col-md-4">
								<div class="position-relative form-group">
									<label>Kategori*</label>
									<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref('ref-direktori','slug') as $row) : ?>
											<option value="<?= $row->name ?>" <?=($row->name == $direktori->category)?'selected':''?>><?= $row->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<!-- <div class="col-md-3">
								<div class="position-relative form-group">
									<label>Sub Kategori*</label>
									<select class="form-control" name="category_sub" id="category_sub" tabindex="-1" aria-hidden="true">
										<?php foreach (get_ref('ref-majalahonline','slug') as $row) : ?>
											<option value="<?= $row->name ?>" <?=($row->name == $direktori->category_sub)?'selected':''?>><?= $row->name ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div> -->
                        </div>

						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="edition">Area</label>
									<div>
										<input type="text" class="form-control" id="area" name="area" placeholder="Area" value="<?= set_value('area', $direktori->area); ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="subject">Email</label>
									<div>
										<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= set_value('email', $direktori->email); ?>" />
									</div>
								</div>
							</div>
							
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="source">Sumber</label>
									<div>
										<input type="text" class="form-control" id="source" name="source" placeholder="Sumber" value="<?= set_value('source', $direktori->source); ?>" />
									</div>
								</div>
							</div>

                                          <div class="col-md-6">
								<div class="position-relative form-group">
									<label for="source">Website</label>
									<div>
										<input type="text" class="form-control" id="website" name="website" placeholder="Website" value="<?= set_value('website', $direktori->website); ?>" />
									</div>
								</div>
							</div>
							
						</div>
                                    <div class="col-md-6">
								<div class="position-relative form-group">
									<label for="subject">Alamat</label>
									<div>
										<input type="text" class="form-control" id="address" name="address" placeholder="Alalmat" value="<?= set_value('address', $direktori->address); ?>" />
									</div>
								</div>
							</div>

						<div class="form-group">
                              <label for="content">Uraian</label>
                              <div>
                                    <textarea id="content" name="content" placeholder="" rows="1" class="form-control autosize-input"><?= set_value('content', $direktori->content); ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description">Keterangan</label>
                              <div>
                                    <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $direktori->description) ?></textarea>
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
    $(document).ready(function() {
		tinyMCE.init({
				selector: 'textarea#content',
				height: 430,
				menubar: false,
				pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
				plugins: 'link code image table pagebreak media lists fullscreen',
				toolbar: 'fullscreen code | undo redo | bold italic underline strikethrough | fontsizeselect formatselect fontselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed template link anchor codesample | forecolor backcolor casechange permanentpen formatpainter removeformat | preview save print | pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
				font_formats: "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",
				setup: function(editor) {
					editor.on('init', function(e) {
						//   editor.execCommand("fontName", false, "Book Antiqua");
							// editor.setContent(content);
					});
				},
				fontsize_formats: "12px 14px 16px 18px 20px 24px 28px 32px",
				content_style: "body { font-size: 14px;}",
		});
	});
</script>
<?= $this->endSection('script'); ?>