<?php
$request = \Config\Services::request();
$request->uri->setSilent();

$slug = $request->getVar('slug');
?>

<?= $this->extend(config('Core')->layout_backend);?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Profil 
                    <div class="page-title-subheading">Daftar semua Profil
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item">Profil </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <?php if(!is_member('admin')):?>
        <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
        
		<li class="nav-item">
			<a class="nav-link" href="<?=base_url('cms/profil?slug=struktur-organisasi')?>">
				<span>struktur-organisasi</span>
			</a>
		</li>
		
	</ul>
    <?php endif;?>
    <?php if(is_member('admin')):?>
	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
        
		<li class="nav-item">
			<a class="nav-link <?=($slug == '')?'active':''?>" href="<?=base_url('cms/profil?slug=')?>">
				<span>Semua</span>
			</a>
		</li>
		<?php foreach(get_ref('ref-profil') as $row):?>
			<li class="nav-item">
				<a class="nav-link <?=($slug == slugify($row->name))?'active':''?>" href="<?=base_url('cms/profil?slug='.slugify($row->name))?>">
					<span><?=$row->name?></span>
				</a>
			</li>
		<?php endforeach;?>
	</ul>
    <?php endif;?>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate">
            </i>Tabel Profil <?= ucwords(unslugify($slug))?>
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if(is_allowed('cms/profil/create')):?>
                <a href="<?= base_url('cms/profil/create?slug='.$slug) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
                    Tambah Profil </a>
                <?php endif;?>
            </div>
        </div>
        <div class="card-body table-responsive">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_grid" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="35">No. </th>
                        <th class="text-center" width="100">Kategori / Channel</th>
                        <th>Judul Profil</th>
                        <th width="50">Urutan</th>
                        <th width="90">Status</th>
                        <th width="190">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
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
				"url": '<?php echo site_url('api/profil/datatable/'.$slug) ?>',
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
				{data: 'no', className: 'text-center', orderable: false},
				{data: 'category_sub'},
				{data: 'title'},
				{data: 'sort', className: 'text-center'},
				{data: 'active', className: 'text-center'},
				{data: 'action', className: 'text-center', orderable: false},
				{data: 'updated_at', visible: false},
			],
            "order": [[6,'desc']],
			"drawCallback": function ( data, type, full, meta ) {
				var api = this.api();
				var data = api.rows().data();
				$.each( data, function( i, row ) {
					$("#lazy"+row.id).Lazy();
				});	

				$('.image-link').magnificPopup({
					type: 'image'
				});
			},
			"initComplete": function(settings, json) {
				var $searchInput = $('div.dataTables_filter input');
				$searchInput.unbind();
				$searchInput.bind('keyup', function(e) {
					if(e.keyCode == 13){
						if(this.value.length == 0){
							t.draw();
						}

						if(this.value.length > 3){
							t.search( this.value ).draw();
						}
					} 
				});
			}
		});
	});

	$("body").on("click", ".remove-data", function() {
        var url = $(this).attr('data-href');
		console.log(url);
        Swal.fire({
            title: '<?= lang('App.swal.are_you_sure') ?>',
            text: "<?= lang('App.swal.can_not_be_restored') ?>",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.value) {
                window.location.href = url;
            }
        });
        return false;
    });
</script>
<?= $this->endSection('script'); ?>