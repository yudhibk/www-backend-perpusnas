<?= $this->extend(config('Core')->layout_backend) ?>
<?= $this->section('style') ?>
<style>
	.buttom{
		vertical-align: bottom !important;
	}
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Survei 
                    <div class="page-title-subheading">Daftar Semua Survei </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url(
                            'auth'
                        ) ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active breadcrumb-item" aria-current="page"> Survei </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Survei 
            <div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('survey/create')): ?>
                    <a href="<?= base_url(
                        'survey/create'
                    ) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i> Tambah Survei  </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body table-responsive">
            <?= get_message('message') ?>
            <table style="width: 100%;" id="tbl_survey" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="35">No</th>
                        <th>Responden</th>
                        <th>Total Score</th>
                        <th>Tanggal Survei</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
				<tbody id="tbl_survey_tbody"></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_survey').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/survey/datatable'); ?>',
				// "data": function (d) {
				// 	d.category = '<?= get_var('category') ?>';
				// }
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
				{data: 'no', orderable: false},
				{data: 'surveyor_name'},
				{data: 'total_score'},
				{data: 'created_at'},
				{data: 'action', orderable: false},
			],
            "order": ['3','desc'],
			"drawCallback": function ( data, type, full, meta ) {
				var api = this.api();
				var data = api.rows().data();

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

	// $('#category').change(function(e) {
	// 	e.preventDefault();
    //     t.ajax.reload();
    // });

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
<?= $this->endSection('script') ?>
