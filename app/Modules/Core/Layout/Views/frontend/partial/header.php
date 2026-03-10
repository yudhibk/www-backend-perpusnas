<?php
$request = \Config\Services::request();
$request->uri->setSilent();
?>

<div id="sliding-bar">
	<div class="sliding-content">
		<?=$this->include('Core\Views\layout\frontend\partial\footer_content')?>
	</div>
	<div class="sliding-toggle"></div>
</div>

<header>
	<div class="info">
		<div class="container">
			<div class="row">
				<div class="span6 info-text">
					<?php if(!logged_in()):?>
						<a href="<?=base_url('login')?>"><strong>Masuk</strong></a>
					<?php else:?>
						<a href="<?=base_url('user/profile')?>"><strong>Halo, <?=user()->username?></strong></a>
						<span class="separator"></span>
						<a href="<?=base_url('dashboard')?>"><strong>Dashboard</strong></a> 
						<span class="separator"></span>
						<a href="<?=base_url('logout')?>"><strong>Keluar</strong></a> 
					<?php endif;?>
				</div>
				<div class="span6 text-right">
					<div class="social-icons">
						<!-- <a class="social-icon sb-icon-facebook" href="https://www.facebook.com/ayokeperpusnas/" target="_blank"></a>
						<a class="social-icon sb-icon-twitter" href="https://twitter.com/perpusnas1" target="_blank"></a> -->
						<a class="social-icon sb-icon-instagram" href="<?=get_parameter('contact-instagram-url')?>" target="_blank"></a>
						<a class="social-icon sb-icon-youtube" href="<?=get_parameter('contact-youtube-url')?>" target="_blank"></a>

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div id="logo">
			<div class="inner">
				<a href="<?=base_url()?>"><img src="<?=base_url('themes/umkm')?>/images/logo.png" alt="logo"></a>
			</div>
		</div>

		<ul id="mainmenu" style="margin:0; padding:0">
			<?php if (get_parameter('sidebar-mode') == 'auto') : ?>
				<?=display_menu_frontend(0,1);?>
			<?php else :?> 
				<?=$this->include('Core\layout\frontend\partial\navigation');?>
			<?php endif; ?>
		</ul>
	</div>
</header>