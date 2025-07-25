<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN EVALUASI PEMAKAIAN BAHAN</title>
	  
	  <?php
		$search = array(
		'January',
		'February',
		'March',
		'April',
		'May',
		'June',
		'July',
		'August',
		'September',
		'October',
		'November',
		'December'
		);
		
		$replace = array(
		'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
		);
		
		$subject = "$filter_date";

		echo str_replace($search, $replace, $subject);
	  ?>
	  
	  <style type="text/css">
		body {
			font-family: helvetica;
			font-size: 7px;
		}
		
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: #F0F0F0;
			font-size: 7px;
		}

		table tr.table-baris1-bold{
			background-color: #F0F0F0;
			font-size: 7px;
			font-weight: bold;
		}
			
		table tr.table-baris2{
			font-size: 7px;
			background-color: #E8E8E8;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">Evaluasi Pemakaian Bahan Baku</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">Divisi Stone Crusher</div>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">Periode <?php echo str_replace($search, $replace, $subject);?></div>
		<br /><br /><br />
		<?php
		$data = array();
		
		$arr_date = $this->input->get('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table width="98%" cellpadding="5">
		
			<?php
			$rap_bahan = $this->db->select('*')
			->from('pmm_agregat ')
			->where("date_agregat <= '$date2'")
			->where('status','PUBLISH')
			->group_by("id")
			->order_by('date_agregat','desc')->limit(1)
			->get()->result_array();

			foreach ($rap_bahan as $x){
				$harsat_bahan = $x['price_a'];
			}

			$pemakaian_boulder = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 22")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_boulder = $pemakaian_boulder['volume'];
			$pemakaian_nilai_boulder = $pemakaian_boulder['nilai'];
			$pemakaian_harsat_boulder = ($pemakaian_volume_boulder!=0)?$pemakaian_nilai_boulder / $pemakaian_volume_boulder * 1:0;

			$total_volume_boulder_rap = $pemakaian_volume_boulder;
			$total_nilai_boulder_rap = $pemakaian_volume_boulder * $harsat_bahan;
			$total_harsat_boulder_rap = ($total_volume_boulder_rap!=0)?$total_nilai_boulder_rap / $total_volume_boulder_rap * 1:0;

			$total_volume_boulder_realisasi = $pemakaian_volume_boulder;
			$total_nilai_boulder_realisasi = $pemakaian_nilai_boulder;
			$total_harsat_boulder_realisasi = $pemakaian_harsat_boulder;

			$total_volume_boulder_evaluasi = $total_volume_boulder_rap - $total_volume_boulder_realisasi;
			$total_nilai_boulder_evaluasi = $total_nilai_boulder_rap - $total_nilai_boulder_realisasi;
	        ?>
			
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2">&nbsp;<br/>NO.</th>
				<th width="12%" align="center" rowspan="2">&nbsp;<br/>URAIAN</th>
				<th width="7%" align="center" rowspan="2">&nbsp;<br/>SATUAN</th>
				<th width="29%" align="center" colspan="3">KOMPOSISI</th>
				<th width="29%" align="center" colspan="3">REALISASI</th>
				<th width="19%" align="center" colspan="2">DEVIASI</th>
	        </tr>
			<tr class="table-judul">
				<th width="8%" align="right">VOLUME</th>
				<th width="9%" align="right">HARSAT</th>
				<th width="12%" align="right">NILAI</th>
				<th width="8%" align="right">VOLUME</th>
				<th width="9%" align="right">HARSAT</th>
				<th width="12%" align="right">NILAI</th>
				<th width="8%" align="right">VOLUME</th>
				<th width="11%" align="right">NILAI</th>
	        </tr>
			<?php
				$styleColorA = $evaluasi_volume_a < 0 ? 'color:red' : 'color:black';
				$styleColorB = $evaluasi_volume_b < 0 ? 'color:red' : 'color:black';
				$styleColorC = $evaluasi_volume_c < 0 ? 'color:red' : 'color:black';
				$styleColorD = $evaluasi_volume_d < 0 ? 'color:red' : 'color:black';
				$styleColorE = $evaluasi_volume_e < 0 ? 'color:red' : 'color:black';

				$styleColorAA = $evaluasi_nilai_a < 0 ? 'color:red' : 'color:black';
				$styleColorBB = $evaluasi_nilai_b < 0 ? 'color:red' : 'color:black';
				$styleColorCC = $evaluasi_nilai_c < 0 ? 'color:red' : 'color:black';
				$styleColorDD = $evaluasi_nilai_d < 0 ? 'color:red' : 'color:black';
				$styleColorEE = $total_nilai_evaluasi < 0 ? 'color:red' : 'color:black';
				$styleColorFF = $evaluasi_nilai_e < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">1.</th>			
				<th align="left">Semen</th>
				<th align="center">Ton</th>
				<th align="right"><?php echo number_format($volume_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_semen,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_semen,0,',','.');?></th>
				<th align="right"><a target="_blank" href="<?= base_url("laporan/cetak_detail_semen?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($pemakaian_nilai_semen,0,',','.');?></a></th>
				<th align="right" style="<?php echo $styleColorA ?>"><?php echo $evaluasi_volume_a < 0 ? "(".number_format(-$evaluasi_volume_a,2,',','.').")" : number_format($evaluasi_volume_a,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorAA ?>"><?php echo $evaluasi_nilai_a < 0 ? "(".number_format(-$evaluasi_nilai_a,0,',','.').")" : number_format($evaluasi_nilai_a,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">2.</th>			
				<th align="left">Pasir</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_pasir,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_pasir,0,',','.');?></th>
				<th align="right"><a target="_blank" href="<?= base_url("laporan/cetak_detail_pasir?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($pemakaian_nilai_pasir,0,',','.');?></a></th>
				<th align="right" style="<?php echo $styleColorB ?>"><?php echo $evaluasi_volume_b < 0 ? "(".number_format(-$evaluasi_volume_b,2,',','.').")" : number_format($evaluasi_volume_b,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorBB ?>"><?php echo $evaluasi_nilai_b < 0 ? "(".number_format(-$evaluasi_nilai_b,0,',','.').")" : number_format($evaluasi_nilai_b,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">3.</th>			
				<th align="left">Batu Split 10-20</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_1020,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_1020,0,',','.');?></th>
				<th align="right"><a target="_blank" href="<?= base_url("laporan/cetak_detail_1020?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($pemakaian_nilai_1020,0,',','.');?></a></th>
				<th align="right" style="<?php echo $styleColorC ?>"><?php echo $evaluasi_volume_c < 0 ? "(".number_format(-$evaluasi_volume_c,2,',','.').")" : number_format($evaluasi_volume_c,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorCC ?>"><?php echo $evaluasi_nilai_c < 0 ? "(".number_format(-$evaluasi_nilai_c,0,',','.').")" : number_format($evaluasi_nilai_c,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">4.</th>			
				<th align="left">Batu Split 20-30</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_2030,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_2030,0,',','.');?></th>
				<th align="right"><a target="_blank" href="<?= base_url("laporan/cetak_detail_2030?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($pemakaian_nilai_2030,0,',','.');?></a></th>
				<th align="right" style="<?php echo $styleColorD ?>"><?php echo $evaluasi_volume_d < 0 ? "(".number_format(-$evaluasi_volume_d,2,',','.').")" : number_format($evaluasi_volume_d,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorDD ?>"><?php echo $evaluasi_nilai_d < 0 ? "(".number_format(-$evaluasi_nilai_d,0,',','.').")" : number_format($evaluasi_nilai_d,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">5.</th>			
				<th align="left">Additive</th>
				<th align="center">Liter</th>
				<th align="right"><?php echo number_format($volume_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_additive,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_additive,0,',','.');?></th>
				<th align="right"><a target="_blank" href="<?= base_url("laporan/cetak_detail_additive?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($pemakaian_nilai_additive,0,',','.');?></a></th>
				<th align="right" style="<?php echo $styleColorE ?>"><?php echo $evaluasi_volume_e < 0 ? "(".number_format(-$evaluasi_volume_e,2,',','.').")" : number_format($evaluasi_volume_e,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorFF ?>"><?php echo $evaluasi_nilai_e < 0 ? "(".number_format(-$evaluasi_nilai_e,0,',','.').")" : number_format($evaluasi_nilai_e,0,',','.');?></th>
	        </tr>
			<tr class="table-total">		
				<th align="right" colspan="3">TOTAL</th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_nilai_komposisi,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_nilai_realisasi,0,',','.');?></th>
				<th align="right"></th>
				<th align="right" class="table-border-spesial-kanan" style="<?php echo $styleColorEE ?>"><?php echo $total_nilai_evaluasi < 0 ? "(".number_format(-$total_nilai_evaluasi,0,',','.').")" : number_format($total_nilai_evaluasi,0,',','.');?></th>
	        </tr>
	    </table>
		<br /><br />
		<?php
		if(in_array($this->session->userdata('admin_id'), array(1,3))){
		?>
		<table width="98%" cellpadding="5">
			<?php
			$pembelian_semen = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 1")
			->get()->row_array();
		
			$pembelian_volume_semen = $pembelian_semen['volume'];
			$pembelian_nilai_semen = $pembelian_semen['nilai'];
			$pembelian_harga_semen = (round($pembelian_volume_semen,2)!=0)?$pembelian_nilai_semen / round($pembelian_volume_semen,2) * 1:0;

			$pemakaian_semen = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 1")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_semen = $pemakaian_semen['volume'];
			$pemakaian_nilai_semen = $pemakaian_semen['nilai'];
			$pemakaian_harsat_semen = ($pemakaian_volume_semen!=0)?$pemakaian_nilai_semen / $pemakaian_volume_semen * 1:0;
			
			$pembelian_pasir = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 2")
			->get()->row_array();
		
			$pembelian_volume_pasir = $pembelian_pasir['volume'];
			$pembelian_nilai_pasir = $pembelian_pasir['nilai'];
			$pembelian_harga_pasir = (round($pembelian_volume_pasir,2)!=0)?$pembelian_nilai_pasir / round($pembelian_volume_pasir,2) * 1:0;

			$pemakaian_pasir = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 2")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_pasir = $pemakaian_pasir['volume'];
			$pemakaian_nilai_pasir = $pemakaian_pasir['nilai'];
			$pemakaian_harsat_pasir = ($pemakaian_volume_pasir!=0)?$pemakaian_nilai_pasir / $pemakaian_volume_pasir * 1:0;

			$pembelian_1020 = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 3")
			->get()->row_array();
		
			$pembelian_volume_1020 = $pembelian_1020['volume'];
			$pembelian_nilai_1020 = $pembelian_1020['nilai'];
			$pembelian_harga_1020 = (round($pembelian_volume_1020,2)!=0)?$pembelian_nilai_1020 / round($pembelian_volume_1020,2) * 1:0;

			$pemakaian_1020 = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 3")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_1020 = $pemakaian_1020['volume'];
			$pemakaian_nilai_1020 = $pemakaian_1020['nilai'];
			$pemakaian_harsat_1020 = ($pemakaian_volume_1020!=0)?$pemakaian_nilai_1020 / $pemakaian_volume_1020 * 1:0;

			$pembelian_2030 = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 4")
			->get()->row_array();
		
			$pembelian_volume_2030 = $pembelian_2030['volume'];
			$pembelian_nilai_2030 = $pembelian_2030['nilai'];
			$pembelian_harga_2030 = (round($pembelian_volume_2030,2)!=0)?$pembelian_nilai_2030 / round($pembelian_volume_2030,2) * 1:0;

			$pemakaian_2030 = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 4")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_2030 = $pemakaian_2030['volume'];
			$pemakaian_nilai_2030 = $pemakaian_2030['nilai'];
			$pemakaian_harsat_2030 = ($pemakaian_volume_2030!=0)?$pemakaian_nilai_2030 / $pemakaian_volume_2030 * 1:0;

			$pembelian_additive = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 6")
			->get()->row_array();
		
			$pembelian_volume_additive = $pembelian_additive['volume'];
			$pembelian_nilai_additive = $pembelian_additive['nilai'];
			$pembelian_harga_additive = (round($pembelian_volume_additive,2)!=0)?$pembelian_nilai_additive / round($pembelian_volume_additive,2) * 1:0;

			$pemakaian_additive = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 19")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_additive = $pemakaian_additive['volume'];
			$pemakaian_nilai_additive = $pemakaian_additive['nilai'];
			$pemakaian_harsat_additive = ($pemakaian_volume_additive!=0)?$pemakaian_nilai_additive / $pemakaian_volume_additive * 1:0;

			$total_volume_realisasi = $pemakaian_volume_semen + $pemakaian_volume_pasir + $pemakaian_volume_1020 + $pemakaian_volume_2030 + $pemakaian_volume_additive;
			$total_nilai_realisasi = $pemakaian_nilai_semen + $pemakaian_nilai_pasir + $pemakaian_nilai_1020 + $pemakaian_nilai_2030 + $pemakaian_nilai_additive;
			
			$volume_a = $pembelian_volume_semen;
			$volume_b = $pembelian_volume_pasir;
			$volume_c = $pembelian_volume_1020;
			$volume_d = $pembelian_volume_2030;
			$volume_e = $pembelian_volume_additive;

			$nilai_a = $pembelian_nilai_semen;
			$nilai_b = $pembelian_nilai_pasir;
			$nilai_c = $pembelian_nilai_1020;
			$nilai_d = $pembelian_nilai_2030;
			$nilai_e = $pembelian_nilai_additive;

			$price_a = ($total_volume_a!=0)?$nilai_a / $volume_a * 1:0;
			$price_b = ($total_volume_b!=0)?$nilai_b / $volume_b * 1:0;
			$price_c = ($total_volume_c!=0)?$nilai_c / $volume_c * 1:0;
			$price_d = ($total_volume_d!=0)?$nilai_d / $volume_d * 1:0;
			$price_e = ($total_volume_e!=0)?$nilai_e / $volume_e * 1:0;

			$total_volume_komposisi = $volume_a + $volume_b + $volume_c + $volume_d + $volume_e;
			$total_nilai_komposisi = $nilai_a + $nilai_b + $nilai_c + $nilai_d + $nilai_e;
			
			$evaluasi_volume_a = round($volume_a - $pemakaian_volume_semen,2);
			$evaluasi_volume_b = round($volume_b - $pemakaian_volume_pasir,2);
			$evaluasi_volume_c = round($volume_c - $pemakaian_volume_1020,2);
			$evaluasi_volume_d = round($volume_d - $pemakaian_volume_2030,2);
			$evaluasi_volume_e = round($volume_e - $pemakaian_volume_additive,2);

			$evaluasi_nilai_a = round($nilai_a - $pemakaian_nilai_semen,0);
			$evaluasi_nilai_b = round($nilai_b - $pemakaian_nilai_pasir,0);
			$evaluasi_nilai_c = round($nilai_c - $pemakaian_nilai_1020,0);
			$evaluasi_nilai_d = round($nilai_d - $pemakaian_nilai_2030,0);
			$evaluasi_nilai_e = round($nilai_e - $pemakaian_nilai_additive,0);

			$total_volume_evaluasi = round($total_volume_komposisi - $total_volume_realisasi,2);
			$total_nilai_evaluasi = round($evaluasi_nilai_a + $evaluasi_nilai_b + $evaluasi_nilai_c + $evaluasi_nilai_d + $evaluasi_nilai_e,0);
			?>
			
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2">&nbsp;<br/>NO.</th>
				<th width="12%" align="center" rowspan="2">&nbsp;<br/>URAIAN</th>
				<th width="7%" align="center" rowspan="2">&nbsp;<br/>SATUAN</th>
				<th width="29%" align="center" colspan="3">PEMBELIAN</th>
				<th width="29%" align="center" colspan="3">REALISASI</th>
				<th width="19%" align="center" colspan="2">DEVIASI</th>
			</tr>
			<tr class="table-judul">
				<th width="8%" align="right">VOLUME</th>
				<th width="9%" align="right">HARSAT</th>
				<th width="12%" align="right">NILAI</th>
				<th width="8%" align="right">VOLUME</th>
				<th width="9%" align="right">HARSAT</th>
				<th width="12%" align="right">NILAI</th>
				<th width="8%" align="right">VOLUME</th>
				<th width="11%" align="right">NILAI</th>
			</tr>
			<?php
				$styleColorA = $evaluasi_volume_a < 0 ? 'color:red' : 'color:black';
				$styleColorB = $evaluasi_volume_b < 0 ? 'color:red' : 'color:black';
				$styleColorC = $evaluasi_volume_c < 0 ? 'color:red' : 'color:black';
				$styleColorD = $evaluasi_volume_d < 0 ? 'color:red' : 'color:black';
				$styleColorE = $evaluasi_volume_e < 0 ? 'color:red' : 'color:black';

				$styleColorAA = $evaluasi_nilai_a < 0 ? 'color:red' : 'color:black';
				$styleColorBB = $evaluasi_nilai_b < 0 ? 'color:red' : 'color:black';
				$styleColorCC = $evaluasi_nilai_c < 0 ? 'color:red' : 'color:black';
				$styleColorDD = $evaluasi_nilai_d < 0 ? 'color:red' : 'color:black';
				$styleColorEE = $total_nilai_evaluasi < 0 ? 'color:red' : 'color:black';
				$styleColorFF = $evaluasi_nilai_e < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">1.</th>			
				<th align="left">Semen</th>
				<th align="center">Ton</th>
				<th align="right"><?php echo number_format($volume_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_semen,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_semen,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_nilai_semen,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorA ?>"><?php echo $evaluasi_volume_a < 0 ? "(".number_format(-$evaluasi_volume_a,2,',','.').")" : number_format($evaluasi_volume_a,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorAA ?>"><?php echo $evaluasi_nilai_a < 0 ? "(".number_format(-$evaluasi_nilai_a,0,',','.').")" : number_format($evaluasi_nilai_a,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">2.</th>			
				<th align="left">Pasir</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_pasir,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_pasir,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_nilai_pasir,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorB ?>"><?php echo $evaluasi_volume_b < 0 ? "(".number_format(-$evaluasi_volume_b,2,',','.').")" : number_format($evaluasi_volume_b,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorBB ?>"><?php echo $evaluasi_nilai_b < 0 ? "(".number_format(-$evaluasi_nilai_b,0,',','.').")" : number_format($evaluasi_nilai_b,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">3.</th>			
				<th align="left">Batu Split 10-20</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_1020,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_1020,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_nilai_1020,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorC ?>"><?php echo $evaluasi_volume_c < 0 ? "(".number_format(-$evaluasi_volume_c,2,',','.').")" : number_format($evaluasi_volume_c,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorCC ?>"><?php echo $evaluasi_nilai_c < 0 ? "(".number_format(-$evaluasi_nilai_c,0,',','.').")" : number_format($evaluasi_nilai_c,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">4.</th>			
				<th align="left">Batu Split 20-30</th>
				<th align="center">M3</th>
				<th align="right"><?php echo number_format($volume_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_2030,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_2030,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_nilai_2030,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorD ?>"><?php echo $evaluasi_volume_d < 0 ? "(".number_format(-$evaluasi_volume_d,2,',','.').")" : number_format($evaluasi_volume_d,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorDD ?>"><?php echo $evaluasi_nilai_d < 0 ? "(".number_format(-$evaluasi_nilai_d,0,',','.').")" : number_format($evaluasi_nilai_d,0,',','.');?></th>
			</tr>
			<tr class="table-baris1">
				<th align="center" style="vertical-align:middle">5.</th>			
				<th align="left">Additive</th>
				<th align="center">Liter</th>
				<th align="right"><?php echo number_format($volume_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($price_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($nilai_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_additive,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_additive,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_nilai_additive,0,',','.');?></th>
				<th align="right" style="<?php echo $styleColorE ?>"><?php echo $evaluasi_volume_e < 0 ? "(".number_format(-$evaluasi_volume_e,2,',','.').")" : number_format($evaluasi_volume_e,2,',','.');?></th>
				<th align="right" style="<?php echo $styleColorFF ?>"><?php echo $evaluasi_nilai_e < 0 ? "(".number_format(-$evaluasi_nilai_e,0,',','.').")" : number_format($evaluasi_nilai_e,0,',','.');?></th>
			</tr>
			<tr class="table-total">		
				<th align="right" colspan="3">TOTAL</th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_nilai_komposisi,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_nilai_realisasi,0,',','.');?></th>
				<th align="right"></th>
				<th align="right" class="table-border-spesial-kanan" style="<?php echo $styleColorEE ?>"><?php echo $total_nilai_evaluasi < 0 ? "(".number_format(-$total_nilai_evaluasi,0,',','.').")" : number_format($total_nilai_evaluasi,0,',','.');?></th>
			</tr>
		</table>
		<?php
		}
		?>
		<table width="98%" border="0" cellpadding="30">
			<tr >
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" >
								Disetujui Oleh
							</td>
							<td align="center">
								Dibuat Oleh
							</td>
						</tr>
						<tr class="">
							<td align="center" height="55px">
							
							</td> 
							<td align="center">
								<img src="uploads/ttd_satria.png" width="50px">
							</td>
						</tr>
						<tr>
							<td align="center">
								<b><u>Deddy Sarwobiso</u><br />
								Direktur Utama</b>
							</td>
							<td align="center">
								<b><u>Satria Widura Drana Wisesa</u><br />
								Ka. Unit Bisnis</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
	</body>
</html>