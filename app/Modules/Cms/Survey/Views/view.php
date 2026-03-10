<?php
$request = \Config\Services::request();
$request->uri->setSilent();

// dd($survey);
?>

<?= $this->extend(config('Core')->layout_backend) ?>
<?= $this->section('style') ?>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>


<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Detail Survei 
                    <div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url(
                            'auth'
                        ) ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url(
                            'survey'
                        ) ?>">Survei </a></li>
                        <li class="active breadcrumb-item" aria-current="page">Detail Survei </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
	<div class="main-card mb-3 card">
		<div class="card-header">
				<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Detail Survei 
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div>
			<?= get_message('message') ?>

			<form id="frm" method="post" action="">

				<div class="main-card mb-3 card card-border">
					<div class="card-header bg-night-sky text-light">Data Responden </div>
					<div class="card-body">								
						<div class="form-row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="surveyor_name">Nama Lengkap *</label>
									<div>
										<input type="text" class="form-control" id="surveyor_name" name="surveyor_name" placeholder="" value="<?= set_value(
              'surveyor_name',
              $survey->surveyor_name
          ) ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="surveyor_phone">Nomor HP</label>
									<div>
										<input type="number" class="form-control" id="surveyor_phone" name="surveyor_phone" placeholder="" value="<?= set_value(
              'surveyor_phone',
              $survey->surveyor_phone
          ) ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="surveyor_dob">Tanggal Lahir</label>
									<div>
										<input type="date" class="form-control" id="surveyor_dob" name="surveyor_dob" placeholder="" value="<?= set_value(
              'surveyor_dob',
              $survey->surveyor_dob
          ) ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="surveyor_gender">Jenis Kelamin *</label>
									<div>
										<select class="form-control" name="surveyor_gender" id="surveyor_gender" tabindex="-1" aria-hidden="true">
											<?php foreach (get_ref('ref-gender', 'slug') as $row): ?>
												<option value="<?= $row->name ?>" <?= $row->name == $survey->surveyor_gender
    ? 'selected'
    : '' ?>><?= $row->name ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="surveyor_education">Pendidikan</label>
									<div>
										<select class="form-control" name="surveyor_education" id="surveyor_education" tabindex="-1" aria-hidden="true">
											<?php foreach (get_ref('ref-education', 'slug') as $row): ?>
												<option value="<?= $row->name ?>" <?= $row->name == $survey->surveyor_education
    ? 'selected'
    : '' ?>><?= $row->name ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="surveyor_job">Pekerjaan</label>
									<div>
										<select class="form-control" name="surveyor_job" id="surveyor_job" tabindex="-1" aria-hidden="true">
											<?php foreach (get_ref('ref-job', 'slug') as $row): ?>
												<option value="<?= $row->name ?>" <?= $row->name == $survey->surveyor_job
    ? 'selected'
    : '' ?>><?= $row->name ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="main-card mb-3 card card-border">
					<div class="card-header bg-night-sky text-light">Form Kuesioner </div>
					<div class="card-body">
						<?php foreach ($qnas as $index => $row): ?>
						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="title"><b><?= $index + 1 ?>. <?= $row->name ?></b></label>
									<div>
										<input type="hidden" name="qna_id[]" value="<?= $row->id ?>">
										<?php $items = json_decode($row->content); ?>
										<?php foreach ($items as $item): ?>
											<div class="custom-radio custom-control">
												<input type="radio" id="score_<?= $row->id ?>_<?= $item->score ?>" name="score[<?= $row->id ?>]" value="<?= $item->score ?>" class="custom-control-input" <?= $item->score ==
$answers[$row->id]
    ? 'checked'
    : '' ?>>
												<label class="custom-control-label" for="score_<?= $row->id ?>_<?= $item->score ?>"><?= $item->option ?></label>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div> 

				<div class="main-card mb-3 card card-border">
					<div class="card-header bg-night-sky text-light">Kritik dan Saran </div>
					<div class="card-body">								
						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="surveyor_remark">Berikan Kritik dan Saran agar kami dapat menjadi lebih baik lagi dalam segi pelayanan *</label>
									<div>
										<textarea class="form-control" placeholder="" name="surveyor_remark" rows="5"><?= set_value(
              'surveyor_remark',
              $survey->surveyor_remark
          ) ?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="form-group">
					<a href="<?= base_url('survey') ?>" class="btn btn-primary">Kembali</a>
				</div>
			</form>
		</div>
	</div>
</div>


<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->endSection('script') ?>
