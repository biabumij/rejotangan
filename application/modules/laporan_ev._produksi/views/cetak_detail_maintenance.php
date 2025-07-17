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
		<div align="center" style="display: block;font-weight: bold;font-size: 11px;">Maintenance</div>
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

			$maintenance_biaya_all = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227,140,230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$maintenance_jurnal_all = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227,140,230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_maintenance_all = $maintenance_biaya_all['total'] + $maintenance_jurnal_all['total'];
			$total_nilai_evaluasi_maintenance_all = ($total_nilai_maintenance_all!=0)?$maintenance - $total_nilai_maintenance_all * 1:0;

			$maintenance_sc_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$maintenance_sc_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_maintenance_sc = $maintenance_sc_biaya['total'] + $maintenance_sc_jurnal['total'];

			$maintenance_wl_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (140)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$maintenance_wl_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (140)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_maintenance_wl = $maintenance_wl_biaya['total'] + $maintenance_wl_jurnal['total'];

			$maintenance_genset_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$maintenance_genset_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_maintenance_genset = $maintenance_genset_biaya['total'] + $maintenance_genset_jurnal['total'];
			?>
			
			<tr class="table-judul">
				<th width="25%" align="center" rowspan="2" style="background-color:#e69500; border-top:1px solid black; border-bottom:1px solid black; border-left:1px solid black;">&nbsp;<br>URAIAN</th>
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
			<tr class="table-baris1" style="font-weight:bold;">		
				<th align="left" style="border-left:1px solid black;">Maintenance</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format($vol_maintenance,2,',','.');?></th>
				<th align="right"><?php echo number_format($harsat_maintenance,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($maintenance,0,',','.');?></th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format($total_nilai_maintenance_all,0,',','.');?></th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_maintenance_all < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>; border-right:1px solid black;"><?php echo $total_nilai_evaluasi_maintenance_all < 0 ? "(".number_format(-$total_nilai_evaluasi_maintenance_all,0,',','.').")" : number_format($total_nilai_evaluasi_maintenance_all,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">			
				<th align="left" style="border-left:1px solid black;">&nbsp;&nbsp;Maintenance Stone Crusher</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"=><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format(0,0,',','.');?></th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_maintenance_sc?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_nilai_maintenance_sc,0,',','.');?></a></th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format(0,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">			
				<th align="left" style="border-left:1px solid black;">&nbsp;&nbsp;Maintenance Wheel Loader</th>
				<th align="center" style="border-right:1px solid black;">M3</th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"=><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format(0,0,',','.');?></th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_maintenance_wl?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_nilai_maintenance_wl,0,',','.');?></a></th>
				<th align="right"><?php echo number_format(0,2,',','.');?></th>
				<th align="right" style="border-right:1px solid black;"><?php echo number_format(0,0,',','.');?></th>
	        </tr>
			<tr class="table-baris1">			
				<th align="left" style="border-left:1px solid black; border-bottom:1px solid black;">&nbsp;&nbsp;Maintenance Genset</th>
				<th align="center" style="border-right:1px solid black; border-bottom:1px solid black;">M3</th>
				<th align="right" style="border-bottom:1px solid black;"><?php echo number_format(0,2,',','.');?></th>
				<th align="right" style="border-bottom:1px solid black;"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black; border-bottom:1px solid black;"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-bottom:1px solid black;"><?php echo number_format(0,2,',','.');?></th>
				<th align="right" style="border-bottom:1px solid black;"><?php echo number_format(0,0,',','.');?></th>
				<th align="right" style="border-right:1px solid black; border-bottom:1px solid black;"><a target="_blank" href="<?= base_url("laporan/cetak_detail_maintenance_genset?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_nilai_maintenance_genset,0,',','.');?></a></th>
				<th align="right" style="border-bottom:1px solid black;"><?php echo number_format(0,2,',','.');?></th>
				<th align="right" style="border-right:1px solid black; border-bottom:1px solid black;"><?php echo number_format(0,0,',','.');?></th>
	        </tr>
	    </table>
	</body>
</html>