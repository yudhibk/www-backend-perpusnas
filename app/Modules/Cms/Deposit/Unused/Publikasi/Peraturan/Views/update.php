<?php
$request = \Config\Services::request();
$request->uri->setSilent();

$baseModel = new \App\Models\BaseModel();
$baseModel->setTable('c_references');
$categories = $baseModel
      ->select('c_references.*')
      ->join('c_menus', 'c_menus.id = c_references.menu_id', 'inner')
      ->where('c_menus.name', 'Peraturan')
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
                        <div>Ubah Peraturan
                              <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Deposit</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Peraturan</a></li>
                                    <li class="active breadcrumb-item" aria-current="page">Ubah Data</li>
                              </ol>
                        </nav>
                  </div>
            </div>
      </div>
      <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Peraturan
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="<?= base_url('deposit/publikasi/peraturan/edit/' . $peraturan->id); ?>">
                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="name">Judul Peraturan*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_name" name="name" placeholder="Judul Peraturan" value="<?= $peraturan->name ?>" />
                                                <small class="info help-block text-muted">Judul Peraturan</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description">Keterangan*</label>
                              <div>
                                    <textarea id="frm_create_description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= $peraturan->description ?></textarea>
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
      Dropzone.autoDiscover = false;

      var dropzone_file = new Dropzone("#file", {
            dictDefaultMessage: "<span class='text-muted'><i class='material-icons mt-2'>cloud_upload</i><br>DRAG &amp; DROP FILES HERE OR CLICK TO UPLOAD</span>",
            url: "<?= base_url('deposit/publikasi/peraturan/do_upload') ?>", // /do_uploads if multiple
            paramName: "file", // files if multiple
            maxFiles: 2,
            maxFilesize: 10,
            addRemoveLinks: true,
            acceptedFiles: 'image/*',
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

                  var existingFile = "<?= $peraturan->file ?>";
                  if (existingFile) {
                        var files = existingFile.split(',');
                        files.forEach(function(file) {
                              var uuid = Date.now();
                              var modulePath = "<?= base_url('uploads/deposit/publikasi/peraturan/') ?>";
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

                  $('#dropzone_file_listed').append('<input type="hidden" name="file[' + uuid + ']" value="' + name + '" />');
            },
            removedfile: function(file) {
                  console.log(file);
                  var name = "";
                  var path = "<?= WRITEPATH . 'uploads/' ?>";
                  if (file.upload !== undefined) {
                        name = file.upload.filename;
                  } else {
                        name = file.name;
                        path = "<?= ROOTPATH . 'public/uploads/deposit/publikasi/peraturan/' ?>";
                  }

                  $.ajax({
                        type: 'POST',
                        url: "<?= base_url('deposit/publikasi/peraturan/do_delete') ?>",
                        data: "name=" + name + "&path=" + path,
                        dataType: 'html'
                  });
                  var _ref;
                  return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            }

      });

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