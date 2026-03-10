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

<?= $this->section('page'); ?>
<div class="app-main__inner">
      <div class="app-page-title">
            <div class="page-title-wrapper">
                  <div class="page-title-heading">
                        <div class="page-title-icon">
                              <i class="pe-7s-photo icon-gradient bg-strong-bliss"></i>
                        </div>
                        <div>Tambah Peraturan
                              <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>

                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Peraturan</a></li>
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

                  <form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('cms/peraturan/create'); ?>" enctype="multipart/form-data">
                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="name">Judul Peraturan*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_name" name="name" placeholder="Judul Peraturan" value="<?= set_value('name'); ?>" />
                                                <small class="info help-block text-muted">Judul Peraturan</small>
                                          </div>
                                    </div>
                              </div>
                        </div>
                        <div class="form-row">
                              <div class="col-md-6">
                                    <label for="category">Kategori*</label>
                                    <select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
                                          <option value="">Pilih</option>
                                          <?php foreach (get_ref('ref-peraturan', 'slug') as $row): ?>
                                                <option value="<?=$row->name?>" <?=(slugify($row->name) == $slug) ? 'selected' : ''?>><?=$row->name?></option>
                                          <?php endforeach;?>
                                    </select>
                              </div>
                              
                              <?php if(is_member('admin')):?>
                                    <div class="col-md-6">
                                          <div class="position-relative form-group">
                                                <label>Channel*</label>
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
                        </div> 

                        <div class="form-group">
                              <label for="description">Keterangan*</label>
                              <div>
                                    <textarea id="frm_create_description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="file" class="">File</label>
                                          <div id="file" class="dropzone"></div>
                                          <div id="file_listed"></div>
                                          <div>
                                                <small class="info help-block text-muted">Format: PDF. Max: 1 Files @ 10MB</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit"><?= lang('App.btn.save') ?></button>
                        </div>
                  </form>
            </div>
      </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
      var file_image = setDropzone('file', 'cms/peraturan', '.pdf', 1, 10);
      $(document).ready(function() {
            tinyMCE.init({
                  selector: 'textarea#frm_create_description',
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
<?= $this->endSection('script'); ?>