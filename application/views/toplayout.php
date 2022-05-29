<nav class="navbar navbar-default">
<div class="container">
	<div class="navbar-header">
			<?=anchor('member','perpustakaan',['class'=>'navbar-brand'])?>
</div>
<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav navbar-right">
		<li><?php echo anchor ('member','Home'); ?></li>
		<li>
		<?php   
		$text_cart_url = '<span class="glyphicon glyphicon-shooping-cart" aria-hidden="true"></span>';
		$text_cart_url .= 'Booking Cart: '. $this->M_perpus->edit_data(array('id_anggota'=>$this->session->userdata('id_agt')),'transaksi')->num_rows() .'Buku';
		?>
		<?=anchor('peminjaman/lihat_keranjang', $text_cart_url)?>
		</li>
		<?php 

		if ($this->session->userdata('id_agt')) {?>
		<li>
		<div style="line-height: 50px;">Hai <b><?=$this->session->userdata('nama_agt')?></b></div>
		</li>		
		<li>
		<?php
		echo anchor('admin/logout','logout');
		?>
		</li>
		<?php } else{ ?>
		<li><?php echo anchor('welcome','Login'); ?></li>
		<?php } ?>
		</ul>
</div>
</div>
</nav>


