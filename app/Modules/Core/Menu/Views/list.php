<?php
	$baseModel = new \App\Models\BaseModel();
	$request = \Config\Services::request();
	$request->uri->setSilent();
	$slug = $request->getVar('slug') ?? 'backend-menu';
	$show_category = $request->getVar('show-category') ?? '1';

	$baseModel->setTable('c_categories');
	$categories = $baseModel
	->select('c_categories.*')
	->find_all('sort', 'asc');

	$baseModel->setTable('c_categories');
	$category = $baseModel
	->select('c_categories.*')
	->where('slug', $slug)
	->row();
?>

<?=$this->extend('\Layout\Views\backend\main');?>
<?=$this->section('style');?>
<link rel="stylesheet" href="<?=base_url('assets/vendors')?>/nestable/nestable.css">
<style>
    .dd{
        max-width: none !important;
    }
    .dd-handle-label {
        opacity: 0;
    }

    .clickable {
        cursor: pointer;
    }

    .menu-toggle-activate {
        cursor: pointer;
    }

    .menu-toggle-activate_inactive >.dd3-content {
        background: #F7D2DC !important;
    }

    .dd-item > button { display: block; position: relative; cursor: pointer; float: left; width: 30px; height: 35px; margin: 2px 0; padding: 0; text-indent: 100%; white-space: nowrap; overflow: hidden; border: 0; background: transparent; font-size: 16px; line-height: 1; text-align: center; font-weight: bold; }
    .dd-item > button:before { content: '+'; display: block; position: absolute; width: 100%; text-align: center; text-indent: 0; }
    .dd-item > button[data-action="collapse"]:before { content: '-'; }
    .app-page-title {
        padding: 15px 30px;
    }
</style>
<?=$this->endSection('style');?>

<?=$this->section('page');?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-menu icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Menu
                    <div class="page-title-subheading">Daftar Semua Menu</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?=base_url('dashboard')?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item">Setting</li>
                        <li class="active breadcrumb-item" aria-current="page">Menu</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
		<?php foreach($categories as $row):?>
			<li class="nav-item">
				<a class="nav-link <?=($row->slug == $slug) ? 'active' : ''?>" href="<?=base_url('menu?slug=' . $row->slug)?>">
					<span><?=$row->name?></span>
				</a>
			</li>
		<?php endforeach;?>
		<li class="nav-item">
			<?php if (is_allowed('menu/create')): ?>
				<a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success mt-2" title="Tambah Kategori Menu" style="padding: 0.5rem 1rem;"><i class="fa fa-plus"></i> Tambah Kategori</a>
			<?php endif;?>
		</li>
	</ul>

    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i> <?=$category->name?>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        <?php if (is_allowed('menu/create')): ?>
                            <a href="<?=base_url('menu/create?slug=' . $slug)?>" class="btn btn-success" title="Tambah Menu"><i class="fa fa-plus"></i> <?=$category->name?></a>
                        <?php endif;?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-dismissible fade show">
                        # Double Click Menu untuk Aktifkan atau Nonaktifkan
                    </div>

                    <div class="dd" id="nestable" style="width:100% !important">
                        <?php
$menu = display_menu_module($category->id, 0, 1);
if (empty($menu)): ?>
                            <div class="box-no-data">Data Menu tidak ada</div>
                        <?php else:
  echo $menu;
endif;?>
                    </div>
                    <div class="nestable-output"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection('page');?>

<?=$this->section('script');?>
<?=$this->include('Menu\Views\category_add_modal');?>
<?=$this->include('Menu\Views\category_update_modal');?>

<script src="<?=base_url('assets/vendors');?>/nestable/jquery.nestable.js"></script>

<script>
	$(".apply-fullscreen-status").on('change', function() {
		var switchStatus = $(this).is(':checked');

		if (switchStatus) {
			window.location.href = '<?=base_url('menu?slug='.$slug.'&show-category=0')?>';
		} else {
			window.location.href = '<?=base_url('menu?slug='.$slug.'&show-category=1')?>';
		}
	});

	$(document).ready(function() {
		$('.clickable').on('click', function() {
			var href = $(this).attr('data-href');
			window.location.href = href;
			return false;
		});

		$('#nestable, #categories').on('click', '.remove-data', function() {
			var url = $(this).attr('data-href');
			Swal.fire({
				title: '<?=lang('App.swal.are_you_sure')?>',
				text: "<?=lang('App.swal.can_not_be_restored')?>",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#dd6b55',
				confirmButtonText: '<?=lang('App.btn.yes')?>',
				cancelButtonText: '<?=lang('App.btn.no')?>'
			}).then((result) => {
				if (result.value) {
					window.location.href = url;
				}
			});
			return false;
		});

		$('#categories').on('click', '.show-data', function() {
			var url = $(this).attr('data-href');
			$.ajax({
				url: url,
				type: 'get',
				dataType: 'json',
				success: function(response) {
					$('#frm_edit').attr("data-id", response.id);
					$('#frm_edit_name').val(response.name);
					$('#frm_edit_sort').val(response.sort);
					$('#frm_edit_description').val(response.description);
					$('#frm_edit_slug').val(response.slug);

					$('#modal_edit').modal('show');
				}
			});
		});

		function updateOrderMenu(ignoreMessage) {
			$('.loading').removeClass('loading-hide');
			var shownotif = true;
			var menu = $('.dd').nestable('serialize');

			if (typeof shownotif == 'undefined') {
				var shownotif = true;
			}

			if (typeof ignoreMessage == 'undefined') {
				var ignoreMessage = false;
			}

			// console.log(menu);
			$.ajax({
					url:  BASE_URL + '/api/menu/save_ordering',
					type: 'POST',
					dataType: 'JSON',
					data: {
						'menu': menu,
					},
			})
			.done(function(data) {
				console.log(data);
				if (data.status === 201) {
					if (shownotif) {
						if (!ignoreMessage) {
							toastr['success'](data.messages.success);
						}
					}
				} else {
					if (shownotif) {
						if (!ignoreMessage) {
							toastr['error']('Menu gagal diubah');
						}
					}
				}
			})
			.fail(function(data) {
				if (!ignoreMessage) {
					toastr['error']('Menu gagal diubah');
				}
			});
		}

		function setMenuActive(id, status) {
			var data = [];

			data.push({
				name: 'id',
				value: id
			});

			data.push({
				name: 'status',
				value: status
			});

			$.ajax({
					url:  BASE_URL + '/api/menu/set_status',
					type: 'POST',
					dataType: 'JSON',
					data: data,
			})
			.done(function(data) {
				console.log(data);
				if (data.status === 201) {
					toastr['success'](data.messages.success);
					updateOrderMenu(true)
				} else {
					toastr['error']('Menu gagal diubah');
				}
			})
			.fail(function(data) {
				toastr['error']('Menu gagal diubah');
			});
		}

		var BASE_URL = '<?=base_url()?>';
		var timeout;

		$('#nestable').nestable({
			group: 1,
			maxDepth: 10,
			collapsedClass:'dd-collapsed',
		}).nestable('expandAll');

		$('.dd').on('change', function() {
			clearTimeout(timeout);
			timeout = setTimeout(updateOrderMenu, 2000);
		});

		$('.menu-toggle-activate').dblclick(function(event) {
			event.stopPropagation();
			var status = $(this).data('status');
			var id = $(this).data('id');

			switch (status) {
				case undefined : case 0 :
					$(this).removeClass('menu-toggle-activate_inactive');
					$(this).data('status', 1)
					setMenuActive(id,  1);
				break;
				case 1 :
					$(this).addClass('menu-toggle-activate_inactive');
					$(this).data('status', 0)
					setMenuActive(id,  0);
				break;
			}
		});
	});
</script>
<?=$this->endSection('script');?>