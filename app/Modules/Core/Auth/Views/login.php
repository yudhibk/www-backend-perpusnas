<?php helper('app'); $core = config('Core'); ?>
<?= $this->extend($core->layout_blank ); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-container">
    <div class="h-100 bg-animation">
        <div class="d-flex h-100 justify-content-center align-items-center" style="background-image: linear-gradient(180deg, <?=get_parameter('corporate-primary','#004040')?>, <?=get_parameter('corporate-secondary','#006060')?>);">
            <div class="mx-auto app-login-box" style="opacity:0.90">
                <div class="app-logo-inverse mx-auto mb-3"></div>
                <div class="modal-dialog w-100 mx-auto" style="box-shadow:none;">
                    <div class="modal-content" style="background-color:#fefefe; border-radius: 30px; padding: 30px 30px; border:none;">
						<form class="" action="<?= route_to('login') ?>" method="post">
							<?= csrf_field() ?>

							<div class="text-center">
								<?php if (get_parameter('show-logo-login') == 1) : ?>
									<a href="<?= base_url() ?>"><img src="<?= base_url(get_parameter('logo')) ?>" width="250" class="mb-4" /></a>
								<?php endif; ?>

								<p class="mb-0 font-weight-bold text-dark mt-2"><?=get_parameter('site-description')?> Backoffice</p>
								<br>
							</div>

							<div class="modal-body">
								<div id="infoMessage" class="bg-corporate-secondary text-white">
									<?= view('Myth\Auth\Views\_message_block') ?>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-1">
										<div class="position-relative form-group">
											<input type="text" class="form-control text-dark form-control" name="login" placeholder="Email atau username" style="border-color: <?=get_parameter('corporate-primary','#004040')?>; border-radius: 30px; padding: 22px; background-color:#fefefe;">
										</div>
									</div>
									<div class="col-md-12 mb-3">
										<div class="position-relative form-group">
											<input type="password" class="form-control text-dark form-control" name="password"  placeholder="Kata sandi" style="border-color: <?=get_parameter('corporate-primary','#004040')?>; border-radius: 30px; padding: 22px; background-color:#fefefe">
										</div>
									</div>
								</div>

								<div class="text-center">
									<button type="submit" class="btn text-light btn-lg btn-block font-weight-bold" style="border-radius: 25px; padding: 13px; background-color: <?=get_parameter('corporate-primary','#004040')?>">
										M A S U K
									</button>
								<div>
								<?php if ($config->activeResetter) : ?>
								<div class="text-center mt-3">
									<a href="<?= route_to('forgot') ?>" class="text-white" style="font-weight-100 font-size:18px; text-decoration:none;">Lupa Kata Sandi</a>
								</div>
								<?php endif;?>

								<?php if ($config->allowRegistration) : ?>
								<div class="divider"></div>
								<div class="text-center">
									<a href="<?= route_to('register') ?>" class="text-dark" style="font-weight:bold; font-size:18px; text-decoration:none;">Tidak memiliki akun? Daftar</a>
								</div>
								<?php endif;?>
							</div>
                        </form>
                    </div>
                </div>
                <div class="text-center text-dark opacity-8 mt-3"><?=get_parameter('site-copyright', '&copy; 2021 SIMBARA')?></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>