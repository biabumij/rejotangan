<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN EVALUASI PEMAKAIAN PERALATAN</title>
	  
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
			background-color: none;
			font-size: 7px;
		}

		table tr.table-baris1-bold{
			background-color: none;
			font-size: 7px;
			font-weight: bold;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}

		table tr.table-total2{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 7px;
			color: black;
		}
	  </style>

	</head>
	<body>
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">Evaluasi Biaya Peralatan</div>
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
		
		<table width="98%" border="0" cellpadding="3" border="0">
		
			<?php
			$stone_crusher_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (225,226,228,229)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$stone_crusher_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (225,226,228,229)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_stone_crusher = $stone_crusher_biaya['total'] + $stone_crusher_jurnal['total'];

			$wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (125,139)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (125,139)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_wheel_loader = $wheel_loader_biaya['total'] + $wheel_loader_jurnal['total'];

			$maintenance_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227,140,230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$maintenance_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227,140,230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_maintenance = $maintenance_biaya['total'] + $maintenance_jurnal['total'];

			$tangki_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (231,232)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$tangki_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (231,232)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_tangki = $tangki_biaya['total'] + $tangki_jurnal['total'];

			$timbangan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (233,234)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$timbangan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (233,234)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_timbangan = $timbangan_biaya['total'] + $timbangan_jurnal['total'];

			$date1_ago = date('2020-01-01');
			$date2_ago = date('Y-m-d', strtotime('-1 days', strtotime($date1)));
			$date3_ago = date('Y-m-d', strtotime('-1 months', strtotime($date1)));
			$tanggal_opening_balance = date('Y-m-d', strtotime('-1 days', strtotime($date1)));

			$stock_opname_solar_ago = $this->db->select('cat.volume as volume, cat.total as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date <= '$tanggal_opening_balance')")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->order_by('date','desc')->limit(1)
			->get()->row_array();

			$stok_volume_solar_lalu = $stock_opname_solar_ago['volume'];
			$stok_nilai_solar_lalu = $stock_opname_solar_ago['nilai'];
			$stok_harsat_solar_lalu = (round($stok_volume_solar_lalu,2)!=0)?$stok_nilai_solar_lalu / round($stok_volume_solar_lalu,2) * 1:0;

			$pembelian_solar = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 5")
			->get()->row_array();
		
			$pembelian_volume_solar = $pembelian_solar['volume'];
			$pembelian_nilai_solar = $pembelian_solar['nilai'];
			$pembelian_harga_solar = (round($pembelian_volume_solar,2)!=0)?$pembelian_nilai_solar / round($pembelian_volume_solar,2) * 1:0;

			$total_stok_volume_solar = $stok_volume_solar_lalu + $pembelian_volume_solar;
			$total_stok_nilai_solar = $stok_nilai_solar_lalu + $pembelian_nilai_solar;

			$stock_opname_solar_now = $this->db->select('cat.volume as volume, cat.total as nilai, cat.pemakaian_custom, cat.reset, cat.reset_pemakaian')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date <= '$date2')")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->order_by('date','desc')->limit(1)
			->get()->row_array();

			$volume_stock_opname_solar_now = $stock_opname_solar_now['volume'];
			$nilai_stock_opname_solar_now = $stock_opname_solar_now['nilai'];

			$vol_pemakaian_solar_now = ($stok_volume_solar_lalu + $pembelian_volume_solar) - $volume_stock_opname_solar_now;
			$nilai_pemakaian_solar_now = $stock_opname_solar_now['nilai'];

			$pemakaian_volume_solar = $vol_pemakaian_solar_now;
			$pemakaian_nilai_solar = (($total_stok_nilai_solar - $nilai_stock_opname_solar_now) * $stock_opname_solar_now['reset']) + ($stock_opname_solar_now['pemakaian_custom'] * $stock_opname_solar_now['reset_pemakaian']);
			$pemakaian_harsat_solar = $pemakaian_nilai_solar / $pemakaian_volume_solar;

			$penjualan = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();
			
			$total_volume = 0;
			foreach ($penjualan as $x){
				$total_volume += $x['volume'];
			}

			$rap_alat = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where("rap.tanggal_rap_alat <= '$date2'")
			->where('rap.status','PUBLISH')
			->group_by("rap.id")
			->order_by('rap.tanggal_rap_alat','desc')->limit(1)
			->get()->result_array();

			foreach ($rap_alat as $x){
				$harsat_stone_crusher = $x['harsat_stone_crusher'];
				$harsat_wheel_loader = $x['wheel_loader'];
				$harsat_maintenance = $x['harsat_maintenance'];
				$harsat_bbm_solar = $x['harsat_bbm_solar'];
			}

			$vol_stone_crusher = $total_volume;
			$vol_wheel_loader = $total_volume;
			$vol_maintenance = $total_volume;
			$vol_bbm_solar = $total_volume;
			$vol_tangki = $total_volume;
			$vol_timbangan = $total_volume;

			$stone_crusher = $total_volume * $harsat_stone_crusher;
			$wheel_loader = $total_volume * $harsat_wheel_loader;
			$maintenance = $total_volume * $harsat_maintenance;
			$bbm_solar = $total_volume * $harsat_bbm_solar;
			$tangki = 0;
			$timbangan = 0;

			$pemakaian_vol_stone_crusher = 0;
			$pemakaian_vol_wheel_loadert = 0;
			$pemakaian_vol_maintenance = 0;
			$pemakaian_vol_bbm_solar = $pemakaian_volume_solar;
			
			$total_pemakaian_stone_crusher = $total_nilai_stone_crusher;
			$total_pemakaian_wheel_loader = $total_nilai_wheel_loader;
			$total_pemakaian_maintenance = $total_nilai_maintenance;
			$total_pemakaian_solar = $pemakaian_nilai_solar;
			$total_pemakaian_tangki = $total_nilai_tangki;
			$total_pemakaian_timbangan = $total_nilai_timbangan;
			
			$total_nilai_evaluasi_stone_crusher = ($total_pemakaian_stone_crusher!=0)?$stone_crusher - $total_pemakaian_stone_crusher * 1:0;
			$total_nilai_evaluasi_wheel_loader = ($total_pemakaian_wheel_loader!=0)?$wheel_loader - $total_pemakaian_wheel_loader * 1:0;
			$total_nilai_evaluasi_maintenance = ($total_pemakaian_maintenance!=0)?$maintenance - $total_pemakaian_maintenance * 1:0;
			$total_vol_evaluasi_bbm_solar = ($pemakaian_volume_solar!=0)?$vol_bbm_solar - $pemakaian_vol_bbm_solar * 1:0;
			$total_nilai_evaluasi_bbm_solar = ($pemakaian_nilai_solar!=0)?$bbm_solar - $pemakaian_nilai_solar * 1:0;
			$total_nilai_evaluasi_tangki = ($total_pemakaian_tangki!=0)?$tangki - $total_pemakaian_tangki * 1:0;
			$total_nilai_evaluasi_timbangan = ($total_pemakaian_timbangan!=0)?$timbangan - $total_pemakaian_timbangan * 1:0;

			$total_nilai_rap_alat = $stone_crusher + $wheel_loader + $maintenance + $bbm_solar;
			$total_nilai_realisasi_alat = $total_pemakaian_stone_crusher + $total_pemakaian_wheel_loader + $total_pemakaian_maintenance + $pemakaian_nilai_solar + $pemakaian_nilai_tangki + $pemakaian_nilai_timbangan;
			$total_nilai_evaluasi_alat = $total_nilai_evaluasi_stone_crusher + $total_nilai_evaluasi_wheel_loader + $total_nilai_evaluasi_maintenance + $total_nilai_evaluasi_bbm_solar + $total_nilai_evaluasi_tangki + $total_nilai_evaluasi_timbangan;
			?>
			
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-left:1px solid black; border-bottom:1px solid black;">&nbsp;<br>NO.</th>
				<th width="20%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black;">&nbsp;<br>URAIAN</th>
				<th width="8%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">&nbsp;<br>SATUAN</th>
				<th width="25%" align="center" colspan="3" style="background-color:#e69500; border-top:1px solid black; border-left:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">RAP</th>
				<th width="25%" align="center" colspan="3" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">REALISASI</th>
				<th width="17%" align="center" colspan="2" style="background-color:#e69500; border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">DEVIASI</th>
			</tr>
			<tr class="table-judul">
				<th width="7%" align="center" style="background-color:#e69500; border-left:1px solid black; border-bottom:1px solid black;">VOLUME</th>
				<th width="8%" align="center" style="background-color:#e69500; border-bottom:1px solid black;">HARSAT</th>
				<th width="10%" align="center" style="background-color:#e69500; border-bottom:1px solid black; border-right:1px solid black;">NILAI</th>
				<th width="7%" align="center" style="background-color:#e69500; border-bottom:1px solid black;">VOLUME</th>
				<th width="8%" align="center" style="background-color:#e69500; border-bottom:1px solid black;">HARSAT</th>
				<th width="10%" align="center" style="background-color:#e69500; border-bottom:1px solid black; border-right:1px solid black;">NILAI</th>
				<th width="7%" align="center" style="background-color:#e69500; border-bottom:1px solid black;">VOLUME</th>
				<th width="10%" align="center" style="background-color:#e69500; border-bottom:1px solid black; border-right:1px solid black;">NILAI</th>
			</tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">1.</th>			
				<th align="left">Stone Crusher + Genset</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format($vol_stone_crusher,2,',','.');?></th>
				<th align="right"><?php echo number_format($harsat_stone_crusher,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($stone_crusher,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_vol_stone_crusher,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_stone_crusher?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_stone_crusher,0,',','.');?></a></th>
				<?php
				$styleColor = $total_vol_evaluasi_stone_crusher < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_stone_crusher < 0 ? "(".number_format(-$total_vol_evaluasi_stone_crusher,2,',','.').")" : number_format($total_vol_evaluasi_stone_crusher,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_stone_crusher < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>; border-right:1px solid black;"><?php echo $total_nilai_evaluasi_stone_crusher < 0 ? "(".number_format(-$total_nilai_evaluasi_stone_crusher,0,',','.').")" : number_format($total_nilai_evaluasi_stone_crusher,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">2.</th>			
				<th align="left">Wheel Loader</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format($vol_wheel_loader,2,',','.');?></th>
				<th align="right"><?php echo number_format($harsat_wheel_loader,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($wheel_loader,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_vol_wheel_loader,2,',','.');?></th>
				<?php
				$harsat_pemakaian_wheel_loader = ($pemakaian_vol_wheel_loader!=0)?$total_pemakaian_wheel_loader / $pemakaian_vol_wheel_loader * 1:0;
				?>
				<th align="right"><?php echo number_format($harsat_pemakaian_wheel_loader,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_wl?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_wheel_loader,0,',','.');?></a></th>
				<?php
				$styleColor = $total_vol_evaluasi_wheel_loader < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_wheel_loader < 0 ? "(".number_format(-$total_vol_evaluasi_wheel_loader,2,',','.').")" : number_format($total_vol_evaluasi_wheel_loader,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_wheel_loader < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>; border-right:1px solid black;"><?php echo $total_nilai_evaluasi_wheel_loader < 0 ? "(".number_format(-$total_nilai_evaluasi_wheel_loader,0,',','.').")" : number_format($total_nilai_evaluasi_wheel_loader,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">2.</th>			
				<th align="left">Maintenance</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format($vol_maintenance,2,',','.');?></th>
				<th align="right"><?php echo number_format($harsat_maintenance,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($maintenance,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_vol_maintenance,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_maintenance?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_maintenance,0,',','.');?></a></th>
				<?php
				$styleColor = $total_vol_evaluasi_maintenance < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_maintenance < 0 ? "(".number_format(-$total_vol_evaluasi_maintenance,2,',','.').")" : number_format($total_vol_evaluasi_maintenance,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_maintenance < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>; border-right:1px solid black;"><?php echo $total_nilai_evaluasi_maintenance < 0 ? "(".number_format(-$total_nilai_evaluasi_maintenance,0,',','.').")" : number_format($total_nilai_evaluasi_maintenance,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">4.</th>			
				<th align="left">BBM Solar</th>
				<th align="center" style="border-right:1px solid black;">Liter</th>
				<th align="right"><?php echo number_format($vol_bbm_solar,2,',','.');?></th>
				<th align="right"><?php echo number_format($harsat_bbm_solar,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($bbm_solar,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_solar,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_solar,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($pemakaian_nilai_solar,0,',','.');?></th>
				<?php
				$styleColor = $total_vol_evaluasi_bbm_solar < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_bbm_solar < 0 ? "(".number_format(-$total_vol_evaluasi_bbm_solar,2,',','.').")" : number_format($total_vol_evaluasi_bbm_solar,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_bbm_solar < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>; border-right:1px solid black;"><?php echo $total_nilai_evaluasi_bbm_solar < 0 ? "(".number_format(-$total_nilai_evaluasi_bbm_solar,0,',','.').")" : number_format($total_nilai_evaluasi_bbm_solar,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">5.</th>			
				<th align="left">Tangki Solar</th>
				<th align="center" style="border-right:1px solid black;"></th>
				<th align="right"><?php echo number_format($vol_tangki,2,',','.');?></th>
				<th align="right"><?php echo number_format($harsat_tangki,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($tangki,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_tangki,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_tangki,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_tangki?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_tangki,0,',','.');?></a></th>
				<?php
				$styleColor = $total_vol_evaluasi_tangki < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_tangki < 0 ? "(".number_format(-$total_vol_evaluasi_tangki,2,',','.').")" : number_format($total_vol_evaluasi_tangki,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_tangki < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>; border-right:1px solid black;"><?php echo $total_nilai_evaluasi_tangki < 0 ? "(".number_format(-$total_nilai_evaluasi_tangki,0,',','.').")" : number_format($total_nilai_evaluasi_tangki,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">6.</th>			
				<th align="left">Timbangan</th>
				<th align="center" style="border-right:1px solid black;"></th>
				<th align="right"><?php echo number_format($vol_timbangan,2,',','.');?></th>
				<th align="right"><?php echo number_format($harsat_timbangan,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($timbangan,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_timbangan,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_timbangan,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_timbangan?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_timbangan,0,',','.');?></a></th>
				<?php
				$styleColor = $total_vol_evaluasi_timbangan < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_timbangan < 0 ? "(".number_format(-$total_vol_evaluasi_timbangan,2,',','.').")" : number_format($total_vol_evaluasi_timbangan,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_timbangan < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>; border-right:1px solid black;"><?php echo $total_nilai_evaluasi_timbangan < 0 ? "(".number_format(-$total_nilai_evaluasi_timbangan,0,',','.').")" : number_format($total_nilai_evaluasi_timbangan,0,',','.');?></th>
	        </tr>
			<tr class="table-total">		
				<th align="right" colspan="3" style="border:1px solid black;">TOTAL</th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;"><?php echo number_format($total_nilai_rap_alat,0,',','.');?></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;"><?php echo number_format($total_nilai_realisasi_alat,0,',','.');?></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<?php
				$styleColor = $total_nilai_evaluasi_alat < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>;border-right:1px solid black; border-top:1px solid black; border-bottom:1px solid black;"><?php echo $total_nilai_evaluasi_alat < 0 ? "(".number_format(-$total_nilai_evaluasi_alat,0,',','.').")" : number_format($total_nilai_evaluasi_alat,0,',','.');?></th>
	        </tr>
	    </table>
		<br /><br />
		<?php
		if(in_array($this->session->userdata('admin_id'), array(1,3))){
		?>
		<table width="98%" border="0" cellpadding="3" border="0">
			<?php
			$date1_ago = date('2020-01-01');
			$date2_ago = date('Y-m-d', strtotime('-1 days', strtotime($date1)));
			$date3_ago = date('Y-m-d', strtotime('-1 months', strtotime($date1)));
			$tanggal_opening_balance = date('Y-m-d', strtotime('-1 days', strtotime($date1)));

			$stock_opname_solar_ago = $this->db->select('cat.volume as volume, cat.total as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date <= '$tanggal_opening_balance')")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->order_by('date','desc')->limit(1)
			->get()->row_array();

			$stok_volume_solar_lalu = $stock_opname_solar_ago['volume'];
			$stok_nilai_solar_lalu = $stock_opname_solar_ago['nilai'];
			$stok_harsat_solar_lalu = (round($stok_volume_solar_lalu,2)!=0)?$stok_nilai_solar_lalu / round($stok_volume_solar_lalu,2) * 1:0;

			$pembelian_solar = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 5")
			->get()->row_array();
		
			$pembelian_volume_solar = $pembelian_solar['volume'];
			$pembelian_nilai_solar = $pembelian_solar['nilai'];
			$pembelian_harga_solar = (round($pembelian_volume_solar,2)!=0)?$pembelian_nilai_solar / round($pembelian_volume_solar,2) * 1:0;

			$total_stok_volume_solar = $stok_volume_solar_lalu + $pembelian_volume_solar;
			$total_stok_nilai_solar = $stok_nilai_solar_lalu + $pembelian_nilai_solar;

			$stock_opname_solar_now = $this->db->select('cat.volume as volume, cat.total as nilai, cat.pemakaian_custom, cat.reset, cat.reset_pemakaian')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date <= '$date2')")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->order_by('date','desc')->limit(1)
			->get()->row_array();

			$volume_stock_opname_solar_now = $stock_opname_solar_now['volume'];
			$nilai_stock_opname_solar_now = $stock_opname_solar_now['nilai'];

			$vol_pemakaian_solar_now = ($stok_volume_solar_lalu + $pembelian_volume_solar) - $volume_stock_opname_solar_now;
			$nilai_pemakaian_solar_now = $stock_opname_solar_now['nilai'];

			$pemakaian_volume_solar = $vol_pemakaian_solar_now;
			$pemakaian_nilai_solar = (($total_stok_nilai_solar - $nilai_stock_opname_solar_now) * $stock_opname_solar_now['reset']) + ($stock_opname_solar_now['pemakaian_custom'] * $stock_opname_solar_now['reset_pemakaian']);
			$pemakaian_harsat_solar = $pemakaian_nilai_solar / $pemakaian_volume_solar;	

			$pembelian_solar = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 5")
			->get()->row_array();
			
			$total_volume_solar = $pembelian_solar['volume'];
			$total_harsat_solar = $pembelian_solar['harga'];
			$total_nilai_solar = $pembelian_solar['nilai'];

			$vol_bbm_solar = $total_volume_solar;
			$bbm_solar = $total_nilai_solar;
			$harsat_bbm_solar = $total_harsat_solar;
			$total_nilai_rap_alat = $bbm_solar;
			$pemakaian_vol_bbm_solar = $total_volume_pemakaian_solar;
			
			//SPESIAL//
			$total_pemakaian_bbm_solar = $total_akumulasi_bbm;
			//SPESIAL//

			$total_vol_evaluasi_bbm_solar = ($pemakaian_volume_solar!=0)?($vol_bbm_solar) - $pemakaian_volume_solar * 1:0;
			$total_nilai_evaluasi_bbm_solar = ($pemakaian_nilai_solar!=0)?$bbm_solar - $pemakaian_nilai_solar * 1:0;

			$total_vol_rap_alat = $total_volume;
			$total_nilai_rap_alat = $bbm_solar;
			$total_vol_realisasi_alat = $pemakaian_volume_solar;
			$total_nilai_realisasi_alat = $pemakaian_nilai_solar;
			$total_vol_evaluasi_alat = $total_vol_evaluasi_bbm_solar;
			$total_nilai_evaluasi_alat = $total_nilai_evaluasi_bbm_solar;
			?>
			
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-left:1px solid black; border-bottom:1px solid black;">&nbsp;<br>NO.</th>
				<th width="20%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black;">&nbsp;<br>URAIAN</th>
				<th width="8%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">&nbsp;<br>SATUAN</th>
				<th width="25%" align="center" colspan="3" style="background-color:#e69500; border-top:1px solid black; border-left:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">PEMBELIAN</th>
				<th width="25%" align="center" colspan="3" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">REALISASI</th>
				<th width="17%" align="center" colspan="2" style="background-color:#e69500; border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">DEVIASI</th>
			</tr>
			<tr class="table-judul">
				<th width="7%" align="center" style="background-color:#e69500; border-left:1px solid black; border-bottom:1px solid black;">VOLUME</th>
				<th width="8%" align="center" style="background-color:#e69500; border-bottom:1px solid black;">HARSAT</th>
				<th width="10%" align="center" style="background-color:#e69500; border-bottom:1px solid black; border-right:1px solid black;">NILAI</th>
				<th width="7%" align="center" style="background-color:#e69500; border-bottom:1px solid black;">VOLUME</th>
				<th width="8%" align="center" style="background-color:#e69500; border-bottom:1px solid black;">HARSAT</th>
				<th width="10%" align="center" style="background-color:#e69500; border-bottom:1px solid black; border-right:1px solid black;">NILAI</th>
				<th width="7%" align="center" style="background-color:#e69500; border-bottom:1px solid black;">VOLUME</th>
				<th width="10%" align="center" style="background-color:#e69500; border-bottom:1px solid black; border-right:1px solid black;">NILAI</th>
			</tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">4.</th>			
				<th align="left">BBM Solar</th>
				<th align="center" style="border-right:1px solid black;">Liter</th>
				<th align="right"><?php echo number_format($vol_bbm_solar,2,',','.');?></th>
				<th align="right"><?php echo number_format($harsat_bbm_solar,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($bbm_solar,0,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_volume_solar,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_solar,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($pemakaian_nilai_solar,0,',','.');?></th>
				<?php
				$styleColor = $total_vol_evaluasi_bbm_solar < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_bbm_solar < 0 ? "(".number_format(-$total_vol_evaluasi_bbm_solar,2,',','.').")" : number_format($total_vol_evaluasi_bbm_solar,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_bbm_solar < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>; border-right:1px solid black;"><?php echo $total_nilai_evaluasi_bbm_solar < 0 ? "(".number_format(-$total_nilai_evaluasi_bbm_solar,0,',','.').")" : number_format($total_nilai_evaluasi_bbm_solar,0,',','.');?></th>
			</tr>
			<tr class="table-total">		
				<th align="right" colspan="3" style="border:1px solid black;">TOTAL</th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;"><?php echo number_format($total_nilai_rap_alat,0,',','.');?></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;"><?php echo number_format($total_nilai_realisasi_alat,0,',','.');?></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<?php
				$styleColor = $total_nilai_evaluasi_alat < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>;border-right:1px solid black; border-top:1px solid black; border-bottom:1px solid black;"><?php echo $total_nilai_evaluasi_alat < 0 ? "(".number_format(-$total_nilai_evaluasi_alat,0,',','.').")" : number_format($total_nilai_evaluasi_alat,0,',','.');?></th>
			</tr>
		</table>
		<?php
		}
		?>
		<?php
		if(in_array($this->session->userdata('admin_group_id'), array(1))){
		?>
		<br /><br />
		<table width="98%" style="font-size:7px;">
			<tr>
				<th class="text-left" width="30%" style="background-color:grey; color:white;">&nbsp;&nbsp;Stok Solar Bulan Lalu</th>
				<th class="text-right" width="15%" style="background-color:grey; color:white;"><?php echo number_format($stok_volume_solar_lalu,2,',','');?></th>
				<th class="text-right" width="15%" style="background-color:grey; color:white;"><?php echo number_format($stok_harsat_solar_lalu,0,',','.');?></th>
				<th class="text-right" width="15%" style="background-color:grey; color:white;"><?php echo number_format($stok_nilai_solar_lalu,0,',','.');?>&nbsp;&nbsp;</th>
			</tr>
			<tr>
				<th class="text-left" style="background-color:yellow; color:black;">&nbsp;&nbsp;Pembelian Solar Bulan Ini</th>
				<th class="text-right" style="background-color:yellow; color:black;"><?php echo number_format($pembelian_volume_solar,2,',','');?></th>
				<th class="text-right" style="background-color:yellow; color:black;"><?php echo number_format($pembelian_harga_solar,0,',','.');?></th>
				<th class="text-right" style="background-color:yellow; color:black;"><?php echo number_format($pembelian_nilai_solar,0,',','.');?>&nbsp;&nbsp;</th>
			</tr>
			<tr>
				<th class="text-left" style="background-color:grey; color:white;">&nbsp;&nbsp;Total Stok Solar Bulan Ini</th>
				<th class="text-right" style="background-color:grey; color:white;"><?php echo number_format($total_stok_volume_solar,2,',','');?></th>
				<th class="text-right" style="background-color:grey; color:white;"></th>
				<th class="text-right" style="background-color:grey; color:white;"><?php echo number_format($total_stok_nilai_solar,0,',','.');?>&nbsp;&nbsp;</th>
			</tr>
			<tr>
				<th class="text-left" style="background-color:red; color:white;">&nbsp;&nbsp;Stok Solar Akhir</th>
				<th class="text-right" style="background-color:red; color:white;"><?php echo number_format($volume_stock_opname_solar_now,2,',','');?></th>
				<th class="text-right" style="background-color:red; color:white;"></th>
				<th class="text-right" style="background-color:red; color:white;"><?php echo number_format($nilai_stock_opname_solar_now,0,',','.');?>&nbsp;&nbsp;</th>
			</tr>
			<tr>
				<th class="text-left" style="background-color:blue; color:white;">&nbsp;&nbsp;Pemakaian Solar Bulan Ini</th>
				<th class="text-right" style="background-color:blue; color:white;"><?php echo number_format($pemakaian_volume_solar,2,',','');?></th>
				<th class="text-right" style="background-color:blue; color:white;"><?php echo number_format($pemakaian_harsat_solar,0,',','.');?></th>
				<th class="text-right" style="background-color:blue; color:white;"><?php echo number_format($pemakaian_nilai_solar,0,',','.');?>&nbsp;&nbsp;</th>
			</tr>
		</table>
		<?php
		}
		?>
		<br /><br /><br /><br /><br /><br /><br /><br /><br />
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