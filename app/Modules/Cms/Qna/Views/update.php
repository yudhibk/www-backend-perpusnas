<?php
$request = \Config\Services::request();
$request->uri->setSilent();
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
                    <i class="pe-7s-Comment icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Ubah Kuesioner
                    <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('qna') ?>">Kuesioner</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah Kuesioner</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Kuesioner
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" method="post" action="">
                        <div class="form-row">
							  <div class="col-md-8">
                                    <div class="form-group">
                                          <label for="name">Pertanyaan *</label>
                                          <div>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Pertanyaan " value="<?= set_value('name', $qna->name); ?>" />
                                          </div>
                                    </div>
                              </div>
							  <div class="col-md-3">
							  		<div class="position-relative form-group">
                                          <label>Indikator</label>
                                          <select class="form-control" name="category_id" id="indicator_id" tabindex="-1" aria-hidden="true">
										  		<?php foreach (get_ref('ref-indicator','slug') as $row) : ?>
                                                	<option value="<?= $row->id ?>" <?=($row->id == $qna->category_id)?'selected':''?>><?= $row->name ?></option>
                                                <?php endforeach; ?>
                                          </select>
                                    </div>
                              </div>
							  <div class="col-md-1">
                                    <div class="form-group">
                                          <label for="sort">Urutan</label>
                                          <div>
                                                <input type="text" class="form-control" id="sort" name="sort" placeholder="Urutan" value="<?= set_value('sort', $qna->sort); ?>" />
                                          </div>
                                    </div>
                              </div>
                        </div>
						<div class="form-row">
                              <div class="col-md-12">
									<div class="form-group">
										<label for="description">Deskripsi</label>
										<div>
												<textarea id="frm_create_description" name="description" placeholder="Deskripsi" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $qna->description) ?></textarea>
										</div>
									</div>
                              </div>
                        </div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="main-card mt-3 mb-3 card card-border">
									<div class="card-header">
										Jawaban
										<div class="btn-actions-pane-right actions-icon-btn">
											<button type="button" name="add" data-tbody="option-tbody" class="btn btn-success option-btn-add"><i class="fa fa-plus"></i> Item</button>
										</div>
									</div>
									<div class="card-body">
										<table style="width: 100%;" id="option-tbl" class="table table-hover table-striped table-bordered">
											<thead>
												<tr>
													<th>Pilihan</th>
													<th width="100">Score</th>
													<th width="100">Aksi</th>
												</tr>
											</thead>
											<tbody id="option-tbody">
											</tbody>
										</table>
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
	$(document).ready(function() {
		$(document).on('click', '.option-btn-remove', function() {
			var url = $(this).data('href');
			var row = $(this).closest('tr');

			row.remove(); return false;
		});

		<?php foreach(json_decode($qna->content) as $row):?>
			addIndicator(`<?=$row->option?>`, `<?=$row->score?>`);
		<?php endforeach;?>
		
		function addIndicator(option, score){
			var index = get_unique_id(6);
			var tbody = 'option-tbody';

			$('#'+tbody).append(`
				<tr class="rm-row">
					<td>                        
						<input type="text" class="form-control" name="option[`+index+`]" placeholder="Pilihan" value="`+option+`" />
					</td>
					<td width="100">
						<input type="hidden" name="index[]" value="`+index+`">
						<input type="text" class="form-control" name="score[`+index+`]" placeholder="Score" value="`+score+`" />
					</td>
					<td width="100" class="text-left">
						<button type="button" class="btn btn-danger option-btn-remove" data-href=""><i class="fa fa-trash"></i></button>
					</td>
				</tr>
			`);

		}

		$(".option-btn-add").click(function() {
			var option = 'Sangat Puas';
			var score = 5;
			addIndicator(option, score);
		});
	});
</script>
<?= $this->endSection('script'); ?>