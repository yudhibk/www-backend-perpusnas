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
                    <i class="pe-7s-photo icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= lang('Page.action.update') ?> <?= lang('Page.module') ?>
                    <div class="page-title-subheading"><?= lang('Page.form.complete_the_data') ?>.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> <?= lang('Page.label.home') ?></a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('page') ?>"><?= lang('Page.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('Page.action.update') ?> <?= lang('Page.module') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form <?= lang('Page.action.update') ?> <?= lang('Page.module') ?>
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="name"><?= lang('Page.field.name') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="<?= lang('Page.field.name') ?>" value="<?= set_value('name', $page->name); ?>" />
                                                <small class="info help-block text-muted"><?= lang('Page.field.name') ?></small>
                                          </div>
                                    </div>
                              </div>
							  <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="slug"><?= lang('Page.field.slug') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="slug" name="slug" placeholder="<?= lang('Page.field.slug') ?>" value="<?= set_value('slug', $page->slug); ?>" />
                                                <small class="info help-block text-muted">Permalink: <a href="<?=base_url('home/page?slug='.$page->slug)?>" target="_blank"><?=base_url('home/page?slug='.$page->slug)?></a></small>
                                          </div>
                                    </div>
                              </div>
							  <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label>Kategori*</label>
                                          <select class="form-control" name="category_id" id="category_id" tabindex="-1" aria-hidden="true">
                                                <?php foreach (get_ref('ref-page', 'slug') as $row) : ?>
                                                <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                                <?php endforeach; ?>
                                          </select>
                                    </div>
                              </div>
                              <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="sort"><?= lang('Page.field.sort') ?></label>
                                          <div>
                                                <input type="number" class="form-control" id="sort" name="sort" placeholder="<?= lang('Page.field.sort') ?>" value="<?= set_value('sort', $page->sort) ?>" />
                                                <small class="info help-block text-muted"><?= lang('Page.field.sort') ?></small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description"><?= lang('Page.field.description') ?></label>
                              <div>
                                    <textarea id="description" name="description" placeholder="<?= lang('Page.field.description') ?>" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $page->description) ?></textarea>
                              </div>
                        </div>

						<div class="form-group">
                              <label for="content">Uraian</label>
                              <div>
                                    <textarea id="content" name="content" placeholder="Uraian" rows="1" class="form-control autosize-input"><?= set_value('content', $page->content); ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit"><?= lang('Page.action.save') ?></button>
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
                  plugins: 'link image code table pagebreak paste media lists fullscreen',
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