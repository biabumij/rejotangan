<!DOCTYPE html>
<html>
	<head>
	  <title>BIAYA ALAT</title>
	  
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
			font-size: 8px;
		}
		table tr.table-judul{
			background-color: #e69500;
			font-weight: bold;
			font-size: 8px;
		}
			
		table tr.table-baris1{
			background-color: #F0F0F0;
			font-size: 8px;
		}
			
		table tr.table-total{
			background-color: #cccccc;
			font-weight: bold;
			font-size: 8px;
		}
	  </style>

	</head>
	<body>
		<div align="center" style="display: block;font-weight:bold; font-size: 11px;">Biaya Alat</div>
		<div align="center" style="display: block;font-weight:bold; font-size: 11px;">Divisi Stone Crusher</div>
		<div align="center" style="display: block;font-weight:bold; font-size: 11px;">Periode <?php echo str_replace($search, $replace, $subject);?></div>
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

			$pemakaian_solar = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 5")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_solar = $pemakaian_solar['volume'];
			$pemakaian_nilai_solar = $pemakaian_solar['nilai'];
			$pemakaian_harsat_solar = ($pemakaian_volume_solar!=0)?$pemakaian_nilai_solar / $pemakaian_volume_solar * 1:0;

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
				<th width="30%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">&nbsp;<br>URAIAN</th>
				<th width="10%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">&nbsp;<br>SATUAN</th>
				<th width="55%" align="center" colspan="3" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">REALISASI</th>
	        </tr>
			<tr class="table-judul">
				<th width="15%" align="right" style="border-left:1px solid black; border-bottom:1px solid black;">VOLUME</th>
				<th width="15%" align="right" style="border-bottom:1px solid black;">HARSAT</th>
				<th width="25%" align="right" style="border-bottom:1px solid black; border-right:1px solid black;">NILAI</th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">1.</th>			
				<th align="left" style="border-right:1px solid black;">Stone Crusher + Genset</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_stone_crusher?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_stone_crusher,0,',','.');?></a></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">2.</th>			
				<th align="left" style="border-right:1px solid black;">Wheel Loader</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_wl?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_wheel_loader,0,',','.');?></a></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">3.</th>			
				<th align="left" style="border-right:1px solid black;">Maintenance</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_maintenance?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_maintenance,0,',','.');?></a></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">4.</th>			
				<th align="left" style="border-right:1px solid black;">BBM Solar</th>
				<th align="center" style="border-right:1px solid black;">Liter</th>
				<th align="right"><?php echo number_format($pemakaian_volume_solar,2,',','.');?></th>
				<th align="right"><?php echo number_format($pemakaian_harsat_solar,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_solar?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($pemakaian_nilai_solar,0,',','.');?></a></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">5.</th>			
				<th align="left" style="border-right:1px solid black;">Tangki Solar</th>
				<th align="center" style="border-right:1px solid black;"></th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_tangki?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_tangki,0,',','.');?></a></th>
	        </tr>
			<tr class="table-baris1">
				<th align="center" style="border-left:1px solid black;">6.</th>			
				<th align="left" style="border-right:1px solid black;">Timbangan</th>
				<th align="center" style="border-right:1px solid black;"></th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_timbangan?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_pemakaian_timbangan,0,',','.');?></a></th>
	        </tr>
			<tr class="table-total">		
				<th align="right" colspan="3" style="border-top:1px solid black; border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black;">TOTAL</th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black;"></th>
				<th align="right" style="border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;"><?php echo number_format($total_nilai_realisasi_alat,0,',','.');?></th>
	        </tr>
	    </table>
		<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
		<table width="98%">
			<tr >
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center">
								Diperiksa Oleh & Disetujui Oleh
							</td>
							<td align="center" >
								Dibuat Oleh
							</td>	
						</tr>
						<tr>
							<td align="center" height="55px">
								<img src="uploads/ttd_satria.png" width="50px">
							</td>
							<td align="center">
								<img src="uploads/ttd_dian.png" width="50px">
							</td>
						</tr>
						<tr>
							<td align="center" >
								<b><u>Satria Widura Drana Wisesa</u><br />
								Ka. Unit Bisnis</b>
							</td>
							<td align="center" >
								<b><u>Dian Melinda Sari</u><br />
								Produksi & HSE</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
	</body>
</html>