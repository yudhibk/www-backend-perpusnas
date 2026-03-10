<?php
	$request = \Config\Services::request();
	$request->uri->setSilent();

	$slug = $request->getVar('slug') ?? '';
?>

<?=$this->extend(config('Core')->layout_backend);?>
<?=$this->section('style');?>
<style>
.tox.tox-tinymce.tox-fullscreen {
    z-index: 1050;
    top: 60px !important;
    left: 85px !important;
    width: calc(100% - 85px) !important;
}
</style>
<?=$this->endSection('style');?>

<?=$this->section('page');?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-notebook icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Publikasi
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?=base_url('dashboard')?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?=base_url('cms/bukubaru')?>">Publikasi</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah Publikasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Publikasi
        </div>
        <div class="card-body">
            <div id="infoMessage"><?=$message ?? '';?></div>
            <?=get_message('message');?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post" enctype="multipart/form-data"
                action="<?=base_url('cms/publikasi/create?slug=' . $slug);?>">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="name">Judul Publikasi*</label>
                            <div>
                                <input required type="text" class="form-control" id="title" name="title"
                                    placeholder="Judul Publikasi" value="<?=set_value('title');?>" />
                            </div>
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
												<option value="<?=$row->name?>"><?=$row->description?></option>
											<?php endforeach;?>
										</select>
									</div>
								</div>
							</div>
						<?php endif;?>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label>Kategori*</label>
                            <select required class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
                                <?php foreach (get_ref('ref-publikasi', 'slug') as $row): ?>
                                    <option value="<?=$row->name?>" <?=(slugify($row->name) == $slug) ? 'selected' : ''?>>
                                        <?=$row->name?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label>Tanggal Post*</label>
                            <div>
                                <input required type="date" class="form-control" id="publish_date" name="publish_date"
                                    placeholder="Tanggal Post" value="<?=set_value('publish_date');?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="year">Tahun</label>
                            <div>
                                <input type="number" class="form-control" id="year" name="year" placeholder="Tahun"
                                    value="<?=set_value('year');?>" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="source">Sumber</label>
                            <div>
                                <input type="text" class="form-control" id="source" name="source" placeholder="Sumber"
                                    value="<?=set_value('source');?>" />
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label for="description">Keterangan</label>
                    <div>
                        <textarea id="description" name="description" placeholder="Keterangan" rows="2"
                            class="form-control autosize-input"
                            style="min-height: 38px;"><?=set_value('description')?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Uraian</label>
                    <div>
                        <textarea id="content" name="content" placeholder="" rows="1"
                            class="form-control autosize-input"><?=set_value('content');?></textarea>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="file_cover" class="">Upload Cover Publikasi</label>
                            <div id="file_cover" class="dropzone"></div>
                            <div id="file_cover_listed"></div>
                            <div>
                                <small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @2MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                    <div class="position-relative form-group">
                        <label for="file_pdf">Upload PDF Publikasi</label>
                        <input type="file" class="form-control" id="file_pdf" name="file_pdf" accept=".pdf" onchange="previewPDF(this)">
                        <div class="mt-2">
                            <small class="info help-block text-muted">Format PDF. Max 2MB</small>
                        </div>
                        <div id="pdfPreview" class="mt-3" style="height: 500px;"></div>
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
<?=$this->endSection('page');?>

<?=$this->section('script');?>
<script>
var file_cover = setDropzone('file_cover', 'cms/pubikasi', '.png,.jpg,.jpeg', 1, 2);
var file_pdf = setDropzone('file_pdf', 'cms/pubikasi', '.pdf', 4, 2);
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

function previewPDF(input) {
    const file = input.files[0];
    const pdfPreview = document.getElementById('pdfPreview');
    
    if (file) {
        // Buat object URL dari file
        const objectUrl = URL.createObjectURL(file);
        
        // Tampilkan PDF menggunakan iframe
        pdfPreview.innerHTML = `
            <iframe 
                src="${objectUrl}" 
                width="100%" 
                height="100%" 
                frameborder="0"
                style="border: 1px solid #ddd;">
            </iframe>`;
    } else {
        pdfPreview.innerHTML = '';
    }
}
</script>
<?=$this->endSection('script');?>
