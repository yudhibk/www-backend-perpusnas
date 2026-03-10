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
                        <div>Ubah Publikasi
                              <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>

                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Publication</a></li>
                                    <li class="active breadcrumb-item" aria-current="page">Ubah Data</li>
                              </ol>
                        </nav>
                  </div>
            </div>
      </div>
      <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Publikasi
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="<?= base_url('deposit/publication/edit/' . $publication->id); ?>">
                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="title">Judul Publikasi*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_title" name="title" placeholder="Judul Publikasi" value="<?= $publication->title; ?>" />
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
                                                <option value="<?=$row->name?>" <?=(slugify($row->name) == $publication->category) ? 'selected' : ''?>><?=$row->name?></option>
                                          <?php endforeach;?>
                                    </select>
                              </div>
                              
                              <?php if (is_member('admin')): ?>
                                    <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label>Channel*</label>
                                          <div class="select-wrapper">
                                                <select class="form-control select2" name="channel" id="channel" tabindex="-1" aria-hidden="true" style="width:100%">
                                                <option value="">Pilih</option>
                                                <?php foreach (get_ref_table('auth_groups', 'name,description', 'category="Unit Kerja"') as $row): ?>
                                                      <option value="<?= $row->name ?>" <?= $publication->channel == $row->name ? 'selected' : ''; ?>><?= $row->description ?></option>
                                                <?php endforeach; ?>
                                                </select>
                                          </div>
                                    </div>
                                    </div>
                              <?php endif; ?>
                        </div> 

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="author">Pengarang*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_author" name="author" placeholder="Pengarang" value="<?= $publication->author; ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="publisher">Penerbit*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_publisher" name="publisher" placeholder="Penerbit" value="<?= $publication->publisher; ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="city">Kota*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_city" name="city" placeholder="Kota" value="<?= $publication->city; ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="publication_year">Tahun*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_publication_year" name="publication_year" placeholder="Tahun" value="<?= $publication->publication_year; ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="edition">Edisi*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_edition" name="edition" placeholder="Edisi" value="<?= $publication->edition; ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="worksheet">Worksheet*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_worksheet" name="worksheet" placeholder="Worksheet" value="<?= $publication->worksheet; ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="meta_title">Meta Title*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_meta_title" name="meta_title" placeholder="Meta Title" value="<?= $publication->meta_title; ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="meta_keywords">Meta Keyword*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_meta_keywords" name="meta_keywords" placeholder="Meta Keyword" value="<?= $publication->meta_keywords; ?>" />
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
                                                <input type="text" class="form-control" id="frm_create_meta_description" name="meta_description" placeholder="Meta Description" value="<?= $publication->meta_description; ?>" />
                                                <small class="info help-block text-muted">Meta Description</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-group">
                              <label for="content">Konten*</label>
                              <div>
                                    <textarea id="frm_create_content" name="content" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= $publication->content ?></textarea>
                              </div>
                        </div>

                        <!-- File -->
                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                    <label for="file" class="">File <span class="font-italic text-secondary">(optional)</span></label>
                                    <div id="file" class="dropzone"></div>
                                    <div id="file_listed">
                                          <?php if (!empty($publication->file)): ?>
                                                <p>File Saat Ini: <a href="<?= base_url('uploads/publication/' . $publication->file) ?>" target="_blank"><?= $publication->file ?></a></p>
                                          <?php endif; ?>
                                    </div>
                                    <div>
                                          <small class="info help-block text-muted">Format (PDF). Max: 1 Files @ 10MB</small>
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
      Dropzone.autoDiscover = false;

      var dropzone_file = new Dropzone("#file", {
            dictDefaultMessage: "<span class='text-muted'><i class='material-icons mt-2'>cloud_upload</i><br>DRAG &amp; DROP FILES HERE OR CLICK TO UPLOAD</span>",
            url: "<?= base_url('deposit/publication/do_upload') ?>", // /do_uploads if multiple
            paramName: "file", // files if multiple
            maxFiles: 1,
            maxFilesize: 10,
            addRemoveLinks: true,
            acceptedFiles: 'image/*,application/pdf',
            renameFile: function(file) {
                  return new Date().getTime() + '_' + file.name.toLowerCase().replace(' ', '_');
            },
            accept: function(file, done) {
                  console.log("uploaded");
                  done();
            },
            init: function() {
                  this.on("maxfilesexceeded", function(file) {
                        console.log("max file");
                  });
                  thisDropzone = this;

                  var existingFile = "<?= $publication->document ?>";
                  if (existingFile) {
                        var files = existingFile.split(',');
                        files.forEach(function(file) {
                              var uuid = Date.now();
                              var modulePath = "<?= base_url('uploads/publication/') ?>";
                              var filePath = modulePath + '/' + file;
                              var mockFile = {
                                    name: file,
                                    size: 68000
                              };
                              thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                              thisDropzone.options.thumbnail.call(thisDropzone, mockFile, filePath);
                              {
                                    $('[data-dz-thumbnail]').css('height', '120');
                                    $('[data-dz-thumbnail]').css('width', '120');
                                    $('[data-dz-thumbnail]').css('object-fit', 'cover');
                              };
                              $(thisDropzone.previewsContainer).find('.dz-progress').hide();
                              $('#file_listed').append('<input type="hidden" name="file[' + uuid + ']" value="' + file + '" />');
                        });
                  }
            },
            success: function(file, response) {
                  console.log(file);
                  console.log(response);
                  // file.previewElement.querySelector("img").src = response.files[0].url;
                  // file.previewElement.classList.add("dz-success");
                  // var fileuploded = file.previewElement.querySelector("[data-dz-name]");
                  // fileuploded.innerHTML = response.files[0].name;
                  // file.name = response.files[0].name;

                  var uuid = file.upload.uuid;
                  var name = file.upload.filename;

                  $('#file_listed').append('<input type="hidden" name="file[' + uuid + ']" value="' + name + '" />');
            },
            removedfile: function(file) {
                  console.log(file);
                  var name = "";
                  var path = "<?= WRITEPATH . 'uploads/' ?>";
                  if (file.upload !== undefined) {
                        name = file.upload.filename;
                  } else {
                        name = file.name;
                        path = "<?= ROOTPATH . 'public/uploads/publication/' ?>";
                  }

                  $.ajax({
                        type: 'POST',
                        url: "<?= base_url('deposit/publication/do_delete') ?>",
                        data: "name=" + name + "&path=" + path,
                        dataType: 'html'
                  });
                  var _ref;
                  return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            }

      });

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