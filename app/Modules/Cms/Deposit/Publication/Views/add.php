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
                        <div>Tambah Publikasi
                              <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>

                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Publikasi</a></li>
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

                  <form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('deposit/publication/create'); ?>">
                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="name">Judul Publikasi*</label>
                                          <div>
                                                <input type="text" class="form-control" name="title" placeholder="Judul Publikasi" value="<?= set_value('title'); ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>
                        <div class="form-row">
                              <div class="col-md-6">
                                    <label for="category">Kategori*</label>
                                    <select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
                                          <option value="">Pilih</option>
                                          <?php foreach (get_ref('ref-publication', 'slug') as $row): ?>
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

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="author">Pengarang*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_author" name="author" placeholder="Pengarang" value="<?= set_value('author'); ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="publisher">Penerbit*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_publisher" name="publisher" placeholder="Penerbit" value="<?= set_value('publisher'); ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="city">Kota*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_city" name="city" placeholder="Kota" value="<?= set_value('city'); ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="publication_year">Tahun*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_publication_year" name="publication_year" placeholder="Tahun" value="<?= set_value('publication_year'); ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="edition">Edisi*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_edition" name="edition" placeholder="Edisi" value="<?= set_value('edition'); ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="worksheet">Worksheet*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_worksheet" name="worksheet" placeholder="Worksheet" value="<?= set_value('worksheet'); ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="meta_title">Meta Title*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_meta_title" name="meta_title" placeholder="Meta Title" value="<?= set_value('meta_title'); ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="meta_keywords">Kata Keyword*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_meta_keywords" name="meta_keywords" placeholder="Meta Keyword" value="<?= set_value('meta_keywords'); ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="meta_description">Meta Description*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_meta_description" name="meta_description" placeholder="Meta Description" value="<?= set_value('meta_description'); ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-group">
                              <label for="content">Konten*</label>
                              <div>
                                    <textarea id="frm_create_content" name="content" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('content') ?></textarea>
                              </div>
                        </div>

                        <!-- File -->
                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="file" class="">File</label>
                                          <div id="file" class="dropzone"></div>
                                          <div id="file_listed"></div>
                                          <div>
                                                <small class="info help-block text-muted">Format: PDF. Max: 1 Files @ 10 MB</small>
                                          </div>
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
      var file_image = setDropzone('file', 'deposit/publication', '.pdf,.jpg,.jpeg,.png', 1, 10);
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
<?= $this->endSection('script'); ?>