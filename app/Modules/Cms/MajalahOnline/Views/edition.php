<?php
	$request = \Config\Services::request();
	$request->uri->setSilent();

	$slug = $request->getVar('slug')??'';
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
                    <i class="pe-7s-notebook icon-gradient bg-strong-bliss"></i>
                </div>
                <div>  Majalah Online
                    <div class="page-title-subheading">Daftar Semua Edisi</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Publikasi</li>
                        <li class="active breadcrumb-item" aria-current="page">Majalah Online</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
		<li class="nav-item">
			<a class="nav-link <?=($slug == '')?'active':''?>" href="<?=base_url('cms/majalahonline/edition?slug=')?>">
				<span>Semua</span>
			</a>
		</li>
		<?php foreach(get_ref('ref-majalahonline') as $row):?>
			<li class="nav-item">
				<a class="nav-link <?=($slug == slugify($row->name))?'active':''?>" href="<?=base_url('cms/majalahonline/edition?slug='.slugify($row->name))?>">
					<span><?=$row->name?></span>
				</a>
			</li>
		<?php endforeach;?>
	</ul>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Majalah Online
            <div class="btn-actions-pane-right actions-icon-btn">
				<?php if(is_allowed('majalahonline/create')):?>
                <a href="<?= base_url('cms/majalahonline/create?slug='.$slug) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
                    Tambah Majalah Online </a>
                <?php endif;?>
            </div>
        </div>
        <div class="card-body table-responsive">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_grid" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No.</th>
                        <th class="text-center" width="80" style="min-width:80px">Cover</th>
                        <th class="text-center" width="80" style="min-width:80px">Kategori</th>
                        <th class="text-center" width="">Judul Edisi</th>
                        <th class="text-center" width="100">Total Artikel</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
				<tbody id="tbl_grid_tbody"></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_grid').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/majalahonline/datatable_edition/'.$slug); ?>',
			},
			"dom": 
				"<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'f><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'p>>" +
				"<'row'<'col-md-12'tr>>" +
				"<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12 text-right'i>>",
			"pagingType": "full_numbers",
            "oLanguage": {
                "sSearch": "<i class='fa fa-search'></i> _INPUT_",
                "sLengthMenu": "_MENU_",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "<i class='fa fa-chevron-double-right'></i>",
                    "sFirst": "<i class='fa fa-chevron-double-left'></i>",
                }
            },
			"columns": [
				{data: 'no',  className: 'text-right', orderable: false},
				{data: 'file_cover', className: 'text-center'},
				{data: 'category', className: 'text-center'},
				{data: 'edition'},
				{data: 'total', className: 'text-center'},
				{data: 'action',  className: 'text-center', orderable: false},
			],
			"order": [[ 3, "asc" ]],
			"drawCallback": function ( data, type, full, meta ) {
				var api = this.api();
				var data = api.rows().data();
				$.each( data, function( i, row ) {
					$("#lazy"+row.id).Lazy();
				});	

				$('.image-link').magnificPopup({
					type: 'image'
				});

				$('[data-toggle="tooltip"]').tooltip();
			},
			"initComplete": function(settings, json) {
				var $searchInput = $('div.dataTables_filter input');
				$searchInput.unbind();
				$searchInput.bind('keyup', function(e) {
					if(e.keyCode == 13){
						if(this.value.length == 0){
							t.search('').draw();
						}

						if(this.value.length > 3){
							t.search( this.value ).draw();

						}
					} 
				});
			}
		});
	});
</script>
<?= $this->endSection('script'); ?>