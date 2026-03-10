<?php
$request = \Config\Services::request();
$request->uri->setSilent();
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
                    <i class="pe-7s-date icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= lang('Event.action.add') ?> <?= lang('Event.module') ?>
                    <div class="page-title-subheading"><?= lang('Event.form.complete_the_data') ?>.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('event') ?>"><?= lang('Event.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('Event.action.add') ?> <?= lang('Event.module') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form <?= lang('Event.action.add') ?> <?= lang('Event.module') ?>
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('event/create'); ?>">
                        <div class="form-row">
                              <div class="col-md-9">
                                    <div class="position-relative form-group">
                                          <label for="name"><?= lang('Event.field.name') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_name" name="name" placeholder="<?= lang('Event.field.name') ?> " value="<?= set_value('name'); ?>" />
                                          </div>
                                    </div>
                              </div>
							  <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label>Kategori*</label>
                                          <select class="form-control" name="category_id" id="category_id" tabindex="-1" aria-hidden="true">
                                                <?php foreach (get_ref('ref-event') as $row) : ?>
                                                <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                                <?php endforeach; ?>
                                          </select>
                                    </div>
                              </div>
                        </div>

						<div class="form-row">
							  <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="date_from"><?= lang('Event.field.date_from') ?>*</label>
                                          <div>
                                                <input type="date" class="form-control" id="frm_create_date_from" name="date_from" placeholder="<?= lang('Event.field.date_from') ?> " value="<?= set_value('date_from'); ?>" />
                                          </div>
                                    </div>
                              </div>
							  <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="date_to"><?= lang('Event.field.date_to') ?>*</label>
                                          <div>
                                                <input type="date" class="form-control" id="frm_create_date_to" name="date_to" placeholder="<?= lang('Event.field.date_to') ?> " value="<?= set_value('date_to'); ?>" />
                                          </div>
                                    </div>
                              </div>
							  <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="pic_name"><?= lang('Event.field.pic_name') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_pic_name" name="pic_name" placeholder="<?= lang('Event.field.pic_name') ?> " value="<?= set_value('pic_name'); ?>" />
                                          </div>
                                    </div>
                              </div>
							  <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="pic_phone"><?= lang('Event.field.pic_phone') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_pic_phone" name="pic_phone" placeholder="<?= lang('Event.field.pic_phone') ?>" value="<?= set_value('pic_phone'); ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="host"><?= lang('Event.field.host') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_host" name="host" placeholder="<?= lang('Event.field.host') ?> " value="<?= set_value('host'); ?>" />
                                          </div>
                                    </div>
                              </div>
							  <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="place"><?= lang('Event.field.place') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_place" name="place" placeholder="<?= lang('Event.field.place') ?> " value="<?= set_value('place'); ?>" />
                                          </div>
                                    </div>
                              </div>

                        </div>

                        <div class="form-group">
                              <label for="description"><?= lang('Event.field.description') ?> </label>
                              <div>
                                    <textarea id="frm_create_description" name="description" placeholder="<?= lang('Event.field.description') ?> " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="content">Uraian</label>
                              <div>
                                    <textarea id="content" name="content" placeholder="Uraian" rows="1" class="form-control autosize-input"><?= set_value('content'); ?></textarea>
                              </div>
                        </div>

						<div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="file_image" class="">File Gambar</label>
                                          <div id="file_image" class="dropzone"></div>
                                          <div id="file_image_listed"></div>
                                          <div>
                                                <small class="info help-block text-muted">Format (JPG|PNG). Max 10 MB</small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="file_pdf" class="">File PDF</label>
                                          <div id="file_pdf" class="dropzone"></div>
                                          <div id="file_pdf_listed"></div>
                                          <div>
                                                <small class="info help-block text-muted">Format (PDF). Max 10 MB</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit"><?= lang('Event.action.save') ?></button>
                        </div>
                  </form>
            </div>
    </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	var file_image = setDropzone('file_image', 'event', '.png,.jpg,.jpeg', 1, 100);
	var file_pdf = setDropzone('file_pdf', 'event', '.pdf', 1, 100);
</script>
<script>
      $(document).ready(function() {
            tinyMCE.init({
                  selector: 'textarea#content',
                  height: 430,
                  menubar: false,
                  pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
                  plugins: 'link image code table pagebreak paste media lists fullscreen',
                  toolbar: 'fullscreen code | undo redo | bold italic underline strikethrough | fontsizeselect formatselect fontselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed template link anchor codesample | forecolor backcolor casechange permanentpen formatpainter removeformat | preview save print | pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
                  font_formats: "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",
                  setup: function(editor) {
                        editor.on('init', function(e) {
							editor.execCommand("fontName", false, "System Font");
							// editor.setContent(content);
                        });
                  },
                  fontsize_formats: "12px 14px 16px 18px 20px 24px 28px 32px",
                  content_style: "body { font-size: 14px;}",
            });
      });
</script>
<?= $this->endSection('script'); ?>