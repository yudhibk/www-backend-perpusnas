<?php
$request = \Config\Services::request();
$request->uri->setSilent();
?>

<li><a href="<?=base_url('home')?>">Beranda</a></li>
<li><a href="<?=base_url('home/news')?>">Pemberitaan</a>
</li>
<li>
	<a href="javascript:void()">Program</a>
	<ul>
		<li><a href="<?=base_url('home/kopumkm')?>">Koperasi dan UKM</a></li>
		<li><a href="<?=base_url('home/article')?>">Artikel Ilmiah</a></li>
	</ul>
</li>
<li><a href="<?=base_url('home/gallery')?>">Galeri</a></li>
<li><a href="<?=base_url('home/book')?>">Buku Digital</a></li>
<li><a href="<?=base_url('home/socmed')?>">Media Sosial</a></li>