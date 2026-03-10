<?php
$request = \Config\Services::request();
$request->uri->setSilent();

$baseModel = new \App\Models\BaseModel();
$baseModel->setTable('c_references');
$categories = $baseModel
      ->select('c_references.*')
      ->join('c_menus', 'c_menus.id = c_references.menu_id', 'inner')
      ->where('c_menus.name', 'RulesGuide')
      ->find_all('name', 'asc');
?>

<?php $core = config('Core');
$layout = (!empty($core->layout_backend)) ? $core->layout_backend : 'Views\layout\backend\main'; ?>
<?= $this->extend($layout); ?>
<?= $this->section('style'); ?>
<style>
.show_column {
    display: block !important;
}

.hide_column {
    display: none !important;
}

.tox-statusbar {
    display: none !important;
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
                <div>Tambah Rules Guide
                    <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>

                        <li class="breadcrumb-item"><a href="javascript:void(0)">Rules Guide</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah Data</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Data
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <?php
                  foreach ($categories as $category) :
                        echo $category['name'];
                  endforeach; ?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post"
                action="<?= base_url('cms/rules-guide/create'); ?>" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="title">Judul Rules Guide*</label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_title" name="title"
                                    placeholder="Judul Rules Guide" value="<?= set_value('title'); ?>" />
                                <small class="info help-block text-muted">Judul Rules Guide</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="category">Kategori*</label>
                            <div>
                                <select type="text" class="form-control" id="frm_create_category" name="category"
                                    placeholder="Kategori">
                                    <?php foreach ($t_categories as $category) : ?>
                                    <option value=" <?= $category['id'] ?>"><?= $category['name']  ?></option>
                                    <?php endforeach ?>
                                </select>
                                <small class="info help-block text-muted">Kategori</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="meta_title">Meta Title*</label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_meta_title" name="meta_title"
                                    placeholder="Meta Title" value="<?= set_value('meta_title'); ?>" />
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
                                    name="meta_keywords" placeholder="Kata Kunci"
                                    value="<?= set_value('meta_keywords'); ?>" />
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
                                    value="<?= set_value('meta_description'); ?>" />
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
                            style="min-height: 38px;"><?= set_value('content') ?></textarea>
                    </div>
                </div>
                <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="file" class="">File <span class="font-italic text-secondary">(optional)</span></label>
                                          <div id="file" class="dropzone"></div>
                                          <div id="file_listed"></div>
                                          <div>
                                                <small class="info help-block text-muted">Format (PDF|XLS). Max 10 MB</small>
                                          </div>
                                    </div>
                              </div>
              </div>

                <!-- Preview area for uploaded document -->
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label>Preview:</label>
                            <iframe id="pdfPreview" class="w-100" style="height: 400px; display: none;"
                                frameborder="0"></iframe>
                        </div>
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
       var file_image = setDropzone('file', 'cms/rules-guide', '.pdf,.jpg,.png', 1, 10);
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
      document.getElementById('frm_create_document').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type === "application/pdf") {
                  const preview = document.getElementById('pdfPreview');
                  preview.src = URL.createObjectURL(file);
                  preview.style.display = 'block';
            } else {
                  alert('Please upload a valid PDF file.');
            }
      });
</script>
<?= $this->endSection('script'); ?>