<?php
$request = \Config\Services::request();
$request->uri->setSilent();

$baseModel = new \App\Models\BaseModel();
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
                <div>Detail Artikel Berita
                    <div class="page-title-subheading">Form Detail</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('direktori') ?>"> Artikel Berita</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Detail Artikel Berita</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Detail Artikel Berita
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm_create"  method="post" action="">
                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="form-group">
                                          <label for="title">Judul Artikel*</label>
                                          <div>
                                                <input type="text" class="form-control" id="title" name="title" placeholder="Judul Artikel" value="<?= set_value('title', $direktori->title); ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-3">
                                    <div class="form-group">
                                          <label for="author">Pengarang/Penulis*</label>
                                          <div>
                                                <input type="text" class="form-control" id="author" name="author" placeholder="Pengarang/Penulis" value="<?= set_value('author', $direktori->author); ?>" />
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-3">
                                    <div class="form-group">
                                          <label for="edition">Edisi</label>
                                          <div>
                                                <input type="text" class="form-control" id="edition" name="edition" placeholder="Edisi" value="<?= set_value('edition', $direktori->edition); ?>" />
                                                <small class="info help-block text-muted">Edisi/ tanggal pemuatan artikel</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="form-group">
                                          <label for="description">Abstrak</label>
                                          <div>
                                                <textarea id="description" name="description" placeholder="Abstrak" rows="1" class="form-control autosize-input"><?= set_value('description', $direktori->description); ?></textarea>
                                                <small class="info help-block text-muted">Ringkasan isi artikel</small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="form-group">
                                          <label for="subject">Subjek/Kata Kunci</label>
                                          <div>
                                                <select class="form-control tags" id="subject" name="subject[]" multiple="multiple">
                                                      <?php $tags = explode(";", $direktori->subject);?>
                                                      <?php foreach ($tags as $row): ?>
                                                      <option value="<?=$row?>" selected><?=$row?></option>
                                                      <?php endforeach; ?>
                                                </select>
                                                <small class="info help-block text-muted">Pisahkan dengan tanda titik koma(;)</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="form-group">
                                          <label for="author_additional">Pengarang Tambahan</label>
                                          <div>
                                                <select class="form-control tags" id="author_additional" name="author_additional[]" multiple="multiple">
                                                      <?php $tags = explode(";", $direktori->author_additional);?>
                                                      <?php foreach ($tags as $row): ?>
                                                      <option value="<?=$row?>" selected><?=$row?></option>
                                                      <?php endforeach; ?>
                                                </select>
                                                <small class="info help-block text-muted">Pisahkan dengan tanda titik koma(;)</small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="form-group">
                                          <label for="institution">Badan yang memiliki</label>
                                          <div>
                                                <select class="form-control tags" id="institution" name="institution[]" multiple="multiple">
                                                      <?php $tags = explode(";", $direktori->institution);?>
                                                      <?php foreach ($tags as $row): ?>
                                                      <option value="<?=$row?>" selected><?=$row?></option>
                                                      <?php endforeach; ?>
                                                </select>
                                                <small class="info help-block text-muted">Pisahkan dengan tanda titik koma(;)</small>
                                          </div>
                                    </div>
                              </div>                            
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="form-group">
                                          <label for="url">Link Sumber</label>
                                          <div>
                                                <input type="text" class="form-control" id="url" name="url" placeholder="https://file.perpusnas.go.id/digital/123" value="<?= set_value('url', $direktori->url); ?>" />
                                                <small class="info help-block text-muted">Ketik link sumber lampiran file digital</small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="form-group">
                                          <label for="source">Sumber Artikel</label>
                                          <div>
                                                <input type="text" class="form-control" id="source" name="source" placeholder="Sumber Artikel" value="<?= set_value('source', $direktori->source); ?>" />
                                                <small class="info help-block text-muted">Nama Surat Kabar/ Majalah</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="file_image" class="">Foto Cover</label>
                                        <div>
                                            <a href="<?= base_url('uploads/direktori/' . $direktori->file_image) ?>" class="image-link"><img width="150" class="rounded" src="<?= base_url('uploads/direktori/' . $direktori->file_image) ?>" alt=""></a>
                                        </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="file_pdf" class="">File Digital</label>
                                        <div>
                                            <a href="<?= base_url('flip?path=direktori&file=' . $direktori->file_pdf) ?>" class="ajax-popup-link">
												<img width="80" class="rounded" src="<?= base_url('uploads/default/pdf.png') ?>" alt="">
											</a>
                                        </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                            <a href="<?=base_url('direktori')?>" class="btn btn-primary">Kembali</a>
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
                  selector: 'textarea#biography',
                  height: 250,
                  menubar: false,
                  pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
                  plugins: 'link image code table pagebreak paste media lists',
                  toolbar: 'code | undo redo | bold italic underline strikethrough | fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl  | table tabledelete ',
                  setup: function(editor) {
                        editor.on('init', function(e) {
                              // editor.setContent(content);
                        });
                  },
                  fontsize_formats: "12px 14px 16px 18px 20px 24px 28px 32px",
                  content_style: "body { font-size: 14px;}",
            });
      });
</script>
<script>
      $(".tags").select2({
            allowClear: true,
            tags: true,
            tokenSeparators: [';']
      });
</script>
<?= $this->endSection('script'); ?>