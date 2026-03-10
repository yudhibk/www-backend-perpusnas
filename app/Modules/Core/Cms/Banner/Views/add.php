<?php
$request = \Config\Services::request();
$request->uri->setSilent();

$baseModel = new \App\Models\BaseModel();
$baseModel->setTable('c_references');
$categories = $baseModel
    ->select('c_references.*')
    ->join('c_menus','c_menus.id = c_references.menu_id', 'inner')
    ->where('c_menus.name','Banner')
    ->find_all('name', 'asc');
?>

<?php $core = config('Core'); $layout = (!empty($core->layout_backend)) ? $core->layout_backend : 'Views\layout\backend\main'; ?>
<?= $this->extend($layout); ?>
<?= $this->section('style'); ?>
<style>
      .show_column{
            display: block !important;
      }

      .hide_column{
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
                <div>Tambah Banner
                    <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('banner') ?>">Banner</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah Banner</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Banner
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('banner/create'); ?>">
                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="name">Judul Banner*</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_name" name="name" placeholder="Judul Banner" value="<?= set_value('name'); ?>" />
                                                <small class="info help-block text-muted">Judul Banner</small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label>Kategori*</label>
                                          <select class="form-control" name="category_id" id="category_id" tabindex="-1" aria-hidden="true">
                                                <?php foreach ($categories as $row) : ?>
                                                <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                                <?php endforeach; ?>
                                          </select>
                                    </div>
                              </div>
                              <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="sort">Urutan</label>
                                          <div>
                                                <input type="number" class="form-control" id="frm_create_sort" name="sort" placeholder="Urutan" value="<?= set_value('sort') ?>" />
                                                <small class="info help-block text-muted">Urutan Banner</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description">Keterangan</label>
                              <div>
                                    <textarea id="frm_create_description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-8">
                                    <div class="position-relative form-group">
                                          <label for="url">Alamat URL</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_url" name="url" placeholder="Alamat URL" value="<?= set_value('url') ?>" />
                                                <small class="info help-block text-muted">Alamat URL jika banner diklik, contoh: https://google.com</small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-4">
                                    <div class="position-relative form-group">
                                          <label for="url_title">Judul URL</label>
                                          <div>
                                                <input type="text" class="form-control" id="frm_create_url_title" name="url_title" placeholder="Judul Link" value="<?= set_value('url_title'); ?>" />
                                                <small class="info help-block text-muted">Contoh: Selengkapnya</small>
                                          </div>
                                    </div>
                              </div>
                        </div>

						<div class="form-row">
                              <div class="col-md-12">
                                    <div class="position-relative form-group">
                                          <label for="file_image" class="">Foto</label>
                                          <div id="file_image" class="dropzone"></div>
                                          <div id="file_image_listed"></div>
                                          <div>
                                                <small class="info help-block text-muted">Format (JPG|PNG). Max 10 MB</small>
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
	var file_image = setDropzone('file_image', 'page', '.png,.jpg,.jpeg', 1, 10);
</script>
<?= $this->endSection('script'); ?>