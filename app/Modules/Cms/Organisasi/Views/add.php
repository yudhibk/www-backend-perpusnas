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
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?=base_url('page')?>"><?=lang('Page.module')?></a></li>
                        <li class="breadcrumb-item active">Tambah Organisasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Organisasi
        </div>
        <div class="card-body">
            <div id="infoMessage"><?=$message ?? '';?></div>
            <?=get_message('message');?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post"
                action="<?=base_url('cms/organisasi/create?slug=' . $slug);?>">
                <div class="form-row">
                    <div class="col-md-2">
                        <div class="position-relative form-group">
                            <label>Kategori*</label>
                            <select class="form-control" name="category_sub" id="category_sub" tabindex="-1"
                                aria-hidden="true">
                                <?php foreach (get_ref('ref-organisasi', 'slug') as $row): ?>
                                <option value="<?=$row->name?>" <?=(slugify($row->name) == $slug) ? 'selected' : ''?>>
                                    <?=$row->name?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="position-relative form-group">
                            <label for="title">Judul Organisasi*</label>
                            <div>
                                <input type="text" class="form-control" name="title" id="title"
                                    placeholder="Judul Organisasi " value="<?=set_value('title');?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="position-relative form-group">
                            <label for="sort">Urutan</label>
                            <div>
                                <input type="number" class="form-control" name="sort" id="sort" placeholder="Urutan "
                                    value="<?=set_value('sort');?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label>Kategori*</label>
                            <select class="form-control" name="category_sub" id="category_sub" tabindex="-1"
                                aria-hidden="true">
                                <?php foreach (get_ref('ref-organisasi', 'slug') as $row): ?>
                                <option value="<?=$row->name?>" <?=(slugify($row->name) == $slug) ? 'selected' : ''?>>
                                    <?=$row->name?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="sort">Link Url</label>
                            <div>
                                <input type="text" class="form-control" name="url" id="url" placeholder="Urutan "
                                    value="<?=set_value('url');?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Keterangan </label>
                    <div>
                        <textarea id="frm_create_description" name="description" placeholder="Keterangan " rows="2"
                            class="form-control autosize-input"
                            style="min-height: 38px;"><?=set_value('description')?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Uraian</label>
                    <div>
                        <textarea id="content" name="content" placeholder="" rows="1"
                            class="form-control autosize-input"><?=set_value('content');?></textarea>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="file_cover" class="">Upload Cover Organisasi</label>
                            <div id="file_cover" class="dropzone"></div>
                            <div id="file_cover_listed"></div>
                            <div>
                                <small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @ 2MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="file_image" class="">Upload Galeri Organisasi</label>
                            <div id="file_image" class="dropzone"></div>
                            <div id="file_image_listed"></div>
                            <div>
                                <small class="info help-block text-muted">Format (JPG|PNG). Max 4 Files @ 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?=$this->endSection('page');?>

<?=$this->section('script');?>
<script>
var file_cover = setDropzone('file_cover', 'page', '.png,.jpg,.jpeg', 1, 2);
var file_image = setDropzone('file_image', 'page', '.png,.jpg,.jpeg', 4, 2);
</script>
<script>
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
<?=$this->endSection('script');?>