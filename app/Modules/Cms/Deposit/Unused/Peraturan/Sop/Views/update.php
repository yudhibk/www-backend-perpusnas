<?php
$request = \Config\Services::request();
$request->uri->setSilent();

$baseModel = new \App\Models\BaseModel();
$baseModel->setTable('c_references');
$categories = $baseModel
      ->select('c_references.*')
      ->join('c_menus', 'c_menus.id = c_references.menu_id', 'inner')
      ->where('c_menus.name', 'Sop')
      ->find_all('name', 'asc');
?>

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
                <div>Ubah Peraturan SOP
                    <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>

                        <li class="breadcrumb-item"><a href="javascript:void(0)">Peraturan Sop</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah Data</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Peraturan Sop
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm" class="col-md-12 mx-auto" method="post" enctype="multipart/form-data"
                action="<?= base_url('deposit/peraturan/sop/edit/' . $sop->id); ?>">
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="title">Judul Peraturan SOP*</label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_title" name="title"
                                    placeholder="Judul Rules Guide" value="<?= $sop->title; ?>" />
                                <small class="info help-block text-muted">Judul Peraturan Sop</small>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="category">Kategori*</label>
                            <div>
                                <?php if (isset($t_categories[1])): ?>
                                <select type="text" class="form-control" id="frm_create_category" name="category"
                                    placeholder="Kategori">
                                    <option value="<?= $t_categories[1]['id'] ?>">
                                        <?= $t_categories[1]['name'] ?></option>
                                </select>
                                <?php else: ?>
                                <p>No category found </p>
                                <?php endif; ?>
                                <small class="info help-block text-muted">Kategori</small>
                            </div>
                        </div>
                    </div>


                    <?php if(is_member('admin')):?>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label>Unit Kerja (Group)*</label>
                            <div class="select-wrapper">
                                <select class="form-control select2" name="channel" id="channel" tabindex="-1"
                                    aria-hidden="true" style="width:100%">
                                    <option value="">Pilih</option>
                                    <?php foreach (get_ref_table('auth_groups', 'name,description', 'category="Unit Kerja"') as $row): ?>
                                    <option value="<?=$row->name?>"><?=$row->description?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>
                </div>


                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="meta_title">Meta Title*</label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_meta_title" name="meta_title"
                                    placeholder="Meta Title" value="<?= $sop->meta_title; ?>" />
                                <small class="info help-block text-muted">Meta Title</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="meta_keywords">Kata Kunci*</label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_meta_keywords"
                                    name="meta_keywords" placeholder="Kata Kunci" value="<?= $sop->meta_keywords; ?>" />
                                <small class="info help-block text-muted">Kata Kunci</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="meta_description">Meta Description*</label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_meta_description"
                                    name="meta_description" placeholder="Meta Description"
                                    value="<?= $sop->meta_description; ?>" />
                                <small class="info help-block text-muted">Meta Description</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label for="content">Konten*</label>
                    <div>
                        <textarea id="frm_create_content" name="content" placeholder="Keterangan" rows="2"
                            class="form-control autosize-input"
                            style="min-height: 38px;"><?= $sop->content ?></textarea>
                    </div>
                </div>


                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="file_upload">Upload File*</label>
                            <div>
                                <!-- File Input -->
                                <input type="file" class="form-control" id="file_upload" name="file"
                                    accept="application/pdf" />
                                <small class="info help-block text-muted">Upload an image or PDF file</small>
                            </div>
                        </div>

                        <!-- Existing File Section -->
                        <?php if (!empty($sop->file) && file_exists(FCPATH . 'uploads/rules-guide/' . $sop->file)): ?>
                        <div id="existing_file" class="mt-3">
                            <p><strong>Existing File:</strong></p>
                            <?php
            $filePath = base_url('uploads/rules-guide/' . $sop->file);
            $fileType = mime_content_type(FCPATH . 'uploads/rules-guide/' . $sop->file);
            ?>
                            <?php if (str_contains($fileType, 'image')): ?>
                            <img src="<?= $filePath ?>" alt="Existing File"
                                style="max-width: 300px; max-height: 300px;" />
                            <?php elseif ($fileType === 'application/pdf'): ?>
                            <p><a href="<?= $filePath ?>" target="_blank">View PDF File</a></p>
                            <?php else: ?>
                            <p><a href="<?= $filePath ?>" target="_blank">Download File</a></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>




                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><?= lang('App.btn.save') ?></button>
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
        selector: 'textarea#frm_create_content',
        height: 430,
        menubar: false,
        pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
        plugins: 'link code image table pagebreak media lists fullscreen',
        toolbar: 'fullscreen code removeformat | bold italic underline strikethrough | fontsizeselect fontselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed link anchor codesample | forecolor backcolor casechange permanentpen formatpainter |  undo redo pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
        font_formats: "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva;",
        fontsize_formats: "12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 28pt 32pt 34pt 36pt 72pt",
        content_style: "body { font-size: 12pt;}",
    });
});
</script>
<script>
    document.getElementById('file_upload').addEventListener('change', function (event) {
        const preview = document.getElementById('file_preview');
        preview.innerHTML = ''; // Clear previous preview

        const file = event.target.files[0];
        if (file) {
            const fileType = file.type;
            
            if (fileType.startsWith('image/')) {
                // For image files
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '300px';
                img.style.maxHeight = '300px';
                img.alt = 'Preview';
                preview.appendChild(img);
            } else if (fileType === 'application/pdf') {
                // For PDF files
                const pdfPreview = document.createElement('p');
                pdfPreview.textContent = `Selected PDF: ${file.name}`;
                preview.appendChild(pdfPreview);
            } else {
                const msg = document.createElement('p');
                msg.textContent = 'Preview not available for this file type.';
                preview.appendChild(msg);
            }
        } else {
            const msg = document.createElement('p');
            msg.textContent = 'No file chosen';
            preview.appendChild(msg);
        }
    });
</script>

<?= $this->endSection('script'); ?>