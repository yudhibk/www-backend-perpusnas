<?php
$request = \Config\Services::request();
$request->uri->setSilent();

$slug = $request->getVar('slug');
$file_image_max = 4;
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

<?=$this->section('page');?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-network icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Organisasi
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?=base_url('dashboard')?>"><i class="fa fa-home"></i>
                                Beranda</a></li>
                        <li class="breadcrumb-item">Organisasi </li>
                        <li class="breadcrumb-item active">Ubah Organisasi </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Organisasi
        </div>
        <div class="card-body">
            <div id="infoMessage"><?=$message ?? '';?></div>
            <?=get_message('message');?>

            <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                <div class="form-row">

                    <div class="col-md-2">
                        <div class="position-relative form-group">
                            <label>Unit Kerja (Group)*</label>
                            <div class="select-wrapper">
                                <select class="form-control select2" name="category_sub" id="category_sub" tabindex="-1"
                                    aria-hidden="true">
                                    <?php foreach (get_ref_table('auth_groups', 'name', 'category="Unit Kerja"') as $row): ?>
                                    <option value="<?=$row->name?>"
                                        <?=($row->name == $organisasi->category_sub) ? 'selected' : ''?>><?=$row->name?>
                                    </option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="position-relative form-group">
                            <label for="name">Judul Organisasi*</label>
                            <div>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Judul Organisasi"
                                    value="<?=set_value('name', $organisasi->title);?>" />
                                <small class="info help-block text-muted">Permalink: <a
                                        href="<?=permalink($organisasi->category_sub)?>"
                                        target="_blank"><?=permalink($organisasi->category_sub)?></a></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="position-relative form-group">
                            <label for="sort">Urutan</label>
                            <div>
                                <input type="number" class="form-control" name="sort" id="sort" placeholder="Urutan "
                                    value="<?=set_value('sort', $organisasi->sort);?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label>Kategori*</label>
                            <select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
                                <?php foreach (get_ref('ref-organisasi', 'slug') as $row): ?>
                                <option value="<?=$row->name?>"
                                    <?=($row->name == $organisasi->category) ? 'selected' : ''?>><?=$row->name?>
                                </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="sort">Link Url</label>
                            <div>
                                <input type="text" class="form-control" name="url" id="url" placeholder="Urutan "
                                    value="<?=set_value('url',$organisasi->url);?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Keterangan</label>
                    <div>
                        <textarea id="description" name="description" placeholder="Keterangan" rows="2"
                            class="form-control autosize-input"
                            style="min-height: 38px;"><?=set_value('description', $organisasi->description)?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Uraian</label>
                    <div>
                        <textarea id="content" name="content" placeholder="" rows="1"
                            class="form-control autosize-input"><?=set_value('content', $organisasi->content);?></textarea>
                    </div>
                </div>

                <hr>
                <?php 
							$default = base_url('uploads/default/no_cover.jpg'); 
							$file_image = (!empty($organisasi->file_image)) ? base_url('uploads/organisasi/' . $organisasi->file_image) : $default;
						?>
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content">Preview Banner</label>
                            <div>
                                <img width="100%" src="<?=$file_image?>" alt="Image" class="img">
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="file_image" class="">Upload File</label>
                            <div id="file_image" class="dropzone"></div>
                            <div id="file_image_listed"></div>
                            <div>
                                <small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @ 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (is_admin()): ?>
                <?=$this->include('Organisasi\Views\meta_add');?>
                <?php endif;?>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?=$this->endSection('page');?>

<?= $this->section('script'); ?>
<?=$this->include('Organisasi\Views\meta_add_script');?>
<script>
var file_image = setDropzone('file_image', 'cms/organisasi', '.png,.jpg,.jpeg', 1, 2);
</script>
<script>
$(document).ready(function() {
    <?php if(!empty($organisasi->meta)):?>
    <?php foreach(json_decode($organisasi->meta) as $row):?>
    addIndicator(`<?=$row->key?>`, `<?=$row->value?>`);
    <?php endforeach;?>
    <?php endif;?>
});

$(document).ready(function() {
    tinyMCE.init({
        selector: 'textarea#content',
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