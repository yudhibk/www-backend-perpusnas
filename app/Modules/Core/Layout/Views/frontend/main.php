<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Literasi Koperasi dan UKM</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Responsive Minimal Bootstrap Theme">
		<meta name="keywords" content="responsive,minimal,bootstrap,theme">
		<meta name="author" content="Hamka Mannan">

		<!--[if lt IE 9]>
		<script src="<?=base_url('themes/umkm')?>/js/html5shiv.js"></script>
		<link rel="stylesheet" href="css/ie.css" type="text/css">
		<![endif]-->

		<?=$this->include('Core\Views\layout\frontend\partial\style')?>
		<?=$this->include('Core\Views\layout\frontend\partial\style_custom')?>
		<?= $this->renderSection('style'); ?>
	</head>

	<body>
		<div id="wrapper">
			<?=$this->include('Core\Views\layout\frontend\partial\header')?>
		
			<?= $this->renderSection('header'); ?>

			<div id="content">
				<?= $this->renderSection('page'); ?>
			</div>

			<?=$this->include('Core\Views\layout\frontend\partial\footer')?>
		</div>

		<?=$this->include('Core\Views\layout\frontend\partial\script')?>
		<?=$this->include('Core\Views\layout\frontend\partial\script_custom')?>
		<?= $this->renderSection('script'); ?>
	</body>
</html>
