<!DOCTYPE html>
<html>
	<head>
	  <title>EVALUASI BIAYA PRODUKSI</title>
	  
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

	  	table tr.table-active{
            background-color: #e69500;
			font-size: 7px;
			font-weight: bold;
		}
			
		table tr.table-active2{
			font-size: 7px;
		}
			
		table tr.table-active3{
			font-size: 7px;
			background-color: #eeeeee;
		}
			
		table tr.table-active4{
			background-color: #D0D0D0;
			font-weight: bold;
			font-size: 7px;
		}
		tr.border-bottom td {
        	border-bottom: 1pt solid #ff000d;
      }
	  </style>

	</head>
	<body>
	<br />
		<br />
		<table width="98%" cellpadding="3">
			<tr>
				<td align="center"  width="100%">
					<div style="display: block;font-weight: bold;font-size: 12px;">Laporan Evaluasi Biaya Produksi<br/>
					<div style="display: block;font-weight: bold;font-size: 12px;">Periode <?php echo str_replace($search, $replace, $subject);?></div></div>
				</td>
			</tr>
		</table>
		<br />
		<br />
		<br />
		<?php
		$data = array();
		
		$arr_date = $this->input->get('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date3 = '';
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date3 	= date('2023-08-01',strtotime($date3));
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));
			$filter_date_2 = date('Y-m-d',strtotime($date3)).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
		}
		
		?>

		<?php
	    ?>

		<?php
		$pembelian_batching_plant = $this->db->select('
		pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
		->from('pmm_receipt_material prm')
		->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
		->join('produk p', 'prm.material_id = p.id','left')
		->join('penerima pn', 'po.supplier_id = pn.id','left')
		->where("prm.date_receipt between '$date1' and '$date2'")
		->where("p.kategori_alat = '1'")
		->where("po.status in ('PUBLISH','CLOSED')")
		->group_by('prm.harga_satuan')
		->order_by('pn.nama','asc')
		->get()->result_array();

		$total_nilai_batching_plant = 0;
		foreach ($pembelian_batching_plant as $x){
			$total_nilai_batching_plant += $x['price'];
		}

		$pemeliharaan_batching_plant_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 138")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$pemeliharaan_batching_plant_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 138")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_pemeliharaan_batching_plant = $pemeliharaan_batching_plant_biaya['total'] + $pemeliharaan_batching_plant_jurnal['total'];

		$penyusutan_batching_plant_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 137")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$penyusutan_batching_plant_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 137")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_penyusutan_batching_plant = $penyusutan_batching_plant_biaya['total'] + $penyusutan_batching_plant_jurnal['total'];

		$angsuran_batching_plant_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 159")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$angsuran_batching_plant_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 159")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_angsuran_batching_plant = $angsuran_batching_plant_biaya['total'] + $angsuran_batching_plant_jurnal['total'];

		$pembelian_wheel_loader = $this->db->select('
		pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
		->from('pmm_receipt_material prm')
		->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
		->join('produk p', 'prm.material_id = p.id','left')
		->join('penerima pn', 'po.supplier_id = pn.id','left')
		->where("prm.date_receipt between '$date1' and '$date2'")
		->where("p.kategori_alat = '3'")
		->where("po.status in ('PUBLISH','CLOSED')")
		->group_by('prm.harga_satuan')
		->order_by('pn.nama','asc')
		->get()->result_array();

		$total_nilai_wheel_loader = 0;
		foreach ($pembelian_wheel_loader as $x){
			$total_nilai_wheel_loader += $x['price'];
		}

		$pemeliharaan_wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 140")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$pemeliharaan_wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 140")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_pemeliharaan_wheel_loader = $pemeliharaan_wheel_loader_biaya['total'] + $pemeliharaan_wheel_loader_jurnal['total'];

		$penyusutan_wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 139")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$penyusutan_wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 139")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_penyusutan_wheel_loader = $penyusutan_wheel_loader_biaya['total'] + $penyusutan_wheel_loader_jurnal['total'];

		$angsuran_wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 160")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$angsuran_wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 160")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_angsuran_wheel_loader = $angsuran_wheel_loader_biaya['total'] + $angsuran_wheel_loader_jurnal['total'];

		$pembelian_truck_mixer = $this->db->select('
		pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
		->from('pmm_receipt_material prm')
		->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
		->join('produk p', 'prm.material_id = p.id','left')
		->join('penerima pn', 'po.supplier_id = pn.id','left')
		->where("prm.date_receipt between '$date1' and '$date2'")
		->where("p.kategori_alat = '2'")
		->where("po.status in ('PUBLISH','CLOSED')")
		->group_by('prm.harga_satuan')
		->order_by('pn.nama','asc')
		->get()->result_array();

		$total_nilai_truck_mixer = 0;
		$total_vol_truck_mixer = 0;
		foreach ($pembelian_truck_mixer as $x){
			$total_nilai_truck_mixer += $x['price'];
			$total_vol_truck_mixer += $x['volume'];
		}

		$alat_truck_mixer_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 124")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$alat_truck_mixer_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->where("pdb.akun = 124")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_alat_truck_mixer = $alat_truck_mixer_biaya['total'] + $alat_truck_mixer_jurnal['total'];

		$pemeliharaan_truck_mixer_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 161")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$pemeliharaan_truck_mixer_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->where("pdb.akun = 161")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_pemeliharaan_truck_mixer = $pemeliharaan_truck_mixer_biaya['total'] + $pemeliharaan_truck_mixer_jurnal['total'];

		$pemeliharaan_batching_plant_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 138")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$pemeliharaan_batching_plant_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 138")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_pemeliharaan_batching_plant = $pemeliharaan_batching_plant_biaya['total'] + $pemeliharaan_batching_plant_jurnal['total'];

		$penyusutan_batching_plant_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 137")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$penyusutan_batching_plant_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 137")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_penyusutan_batching_plant = $penyusutan_batching_plant_biaya['total'] + $penyusutan_batching_plant_jurnal['total'];

		$angsuran_batching_plant_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 159")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$angsuran_batching_plant_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 159")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_angsuran_batching_plant = $angsuran_batching_plant_biaya['total'] + $angsuran_batching_plant_jurnal['total'];

		$pembelian_wheel_loader = $this->db->select('
		pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
		->from('pmm_receipt_material prm')
		->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
		->join('produk p', 'prm.material_id = p.id','left')
		->join('penerima pn', 'po.supplier_id = pn.id','left')
		->where("prm.date_receipt between '$date1' and '$date2'")
		->where("p.kategori_alat = '3'")
		->where("po.status in ('PUBLISH','CLOSED')")
		->group_by('prm.harga_satuan')
		->order_by('pn.nama','asc')
		->get()->result_array();

		$total_nilai_wheel_loader = 0;
		foreach ($pembelian_wheel_loader as $x){
			$total_nilai_wheel_loader += $x['price'];
		}

		$pemeliharaan_wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 140")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$pemeliharaan_wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 140")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_pemeliharaan_wheel_loader = $pemeliharaan_wheel_loader_biaya['total'] + $pemeliharaan_wheel_loader_jurnal['total'];

		$penyusutan_wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 139")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$penyusutan_wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 139")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_penyusutan_wheel_loader = $penyusutan_wheel_loader_biaya['total'] + $penyusutan_wheel_loader_jurnal['total'];

		$angsuran_wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 160")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$angsuran_wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 160")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_angsuran_wheel_loader = $angsuran_wheel_loader_biaya['total'] + $angsuran_wheel_loader_jurnal['total'];

		$pembelian_truck_mixer = $this->db->select('
		pn.nama, po.no_po, po.subject, prm.measure, SUM(prm.volume) as volume, SUM(prm.price) / SUM(prm.volume) as harga_satuan, SUM(prm.price) as price')
		->from('pmm_receipt_material prm')
		->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
		->join('produk p', 'prm.material_id = p.id','left')
		->join('penerima pn', 'po.supplier_id = pn.id','left')
		->where("prm.date_receipt between '$date1' and '$date2'")
		->where("p.kategori_alat = '2'")
		->where("po.status in ('PUBLISH','CLOSED')")
		->group_by('prm.harga_satuan')
		->order_by('pn.nama','asc')
		->get()->result_array();

		$total_nilai_truck_mixer = 0;
		$total_vol_truck_mixer = 0;
		foreach ($pembelian_truck_mixer as $x){
			$total_nilai_truck_mixer += $x['price'];
			$total_vol_truck_mixer += $x['volume'];
		}

		$alat_truck_mixer_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 124")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$alat_truck_mixer_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->where("pdb.akun = 124")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_alat_truck_mixer = $alat_truck_mixer_biaya['total'] + $alat_truck_mixer_jurnal['total'];

		$pemeliharaan_truck_mixer_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 161")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$pemeliharaan_truck_mixer_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->where("pdb.akun = 161")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_pemeliharaan_truck_mixer = $pemeliharaan_truck_mixer_biaya['total'] + $pemeliharaan_truck_mixer_jurnal['total'];

		$insentif_truck_mixer_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 186")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$insentif_truck_mixer_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->where("pdb.akun = 186")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_insentif_truck_mixer = $insentif_truck_mixer_biaya['total'] + $insentif_truck_mixer_jurnal['total'];

		$mobilisasi_truck_mixer_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 187")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$mobilisasi_truck_mixer_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->where("pdb.akun = 187")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$total_nilai_mobilisasi_truck_mixer = $mobilisasi_truck_mixer_biaya['total'] + $mobilisasi_truck_mixer_jurnal['total'];

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
			$vol_rap_batching_plant = $x['vol_batching_plant'];
			$vol_rap_wheel_loader = $x['vol_wheel_loader'];
			$vol_rap_truck_mixer = $x['vol_truck_mixer'];
			$vol_rap_bbm_solar = $x['vol_bbm_solar'];
			$harsat_batching_plant = $x['batching_plant'];
			$harsat_wheel_loader = $x['wheel_loader'];
			$harsat_truck_mixer = $x['truck_mixer'];
			$harsat_bbm_solar = $x['vol_bbm_solar'] * $x['harsat_bbm_solar'];
			
		}

		$vol_batching_plant = $total_volume;
		$vol_wheel_loader = $total_volume;
		$vol_truck_mixer = $total_volume;
		$vol_bbm_solar = $total_volume;

		$batching_plant = $harsat_batching_plant * $total_volume;
		$wheel_loader = $harsat_wheel_loader * $total_volume;
		$truck_mixer = $harsat_truck_mixer * $total_volume;
		$bbm_solar = $harsat_bbm_solar * $vol_bbm_solar;

		$harsat_batching_plant = ($vol_batching_plant!=0)?$batching_plant / $vol_batching_plant * 1:0;
		$harsat_truck_mixer = ($vol_truck_mixer!=0)?$truck_mixer / $vol_truck_mixer * 1:0;
		$harsat_wheel_loader = ($wheel_loader!=0)?$wheel_loader / $vol_wheel_loader * 1:0;
		$harsat_bbm_solar = ($vol_bbm_solar!=0)?$bbm_solar / $vol_bbm_solar * 1:0;
		$total_nilai_rap_alat = $batching_plant + $truck_mixer + $wheel_loader + $bbm_solar;

		$pemakaian_vol_batching_plant = 0;
		$pemakaian_vol_pemeliharaan_batching_plant = 0;
		$pemakaian_vol_penyusutan_batching_plant = $total_volume;
		$pemakaian_vol_truck_mixer = $total_vol_truck_mixer;
		$pemakaian_vol_wheel_loader = 0;
		$pemakaian_vol_pemeliharaan_wheel_loader = 0;
		$pemakaian_vol_penyusutan_wheel_loader = $total_volume;
		$pemakaian_vol_bbm_solar = $total_volume_pemakaian_solar;
		
		//SPESIAL//
		$total_pemakaian_pemeliharaan_batching_plant = $total_nilai_pemeliharaan_batching_plant;
		$total_pemakaian_penyusutan_batching_plant = $total_nilai_penyusutan_batching_plant;
		$total_pemakaian_angsuran_batching_plant = $total_nilai_angsuran_batching_plant;
		$total_pemakaian_batching_plant = $total_nilai_batching_plant + $total_pemakaian_pemeliharaan_batching_plant + $total_nilai_angsuran_batching_plant + $total_nilai_penyusutan_batching_plant;
		$total_pemakaian_truck_mixer = $total_nilai_truck_mixer + $total_nilai_alat_truck_mixer + $total_nilai_pemeliharaan_truck_mixer + $total_nilai_insentif_truck_mixer + $total_nilai_mobilisasi_truck_mixer;
		$total_pemakaian_pemeliharaan_truck_mixer = $total_nilai_pemeliharaan_truck_mixer;
		$total_pemakaian_pemeliharaan_wheel_loader = $total_nilai_pemeliharaan_wheel_loader;
		$total_pemakaian_insentif_truck_mixer =  $total_nilai_insentif_truck_mixer;
		$total_pemakaian_mobilisasi_truck_mixer = $total_nilai_mobilisasi_truck_mixer;
		$total_pemakaian_penyusutan_wheel_loader = $total_nilai_penyusutan_wheel_loader;
		$total_pemakaian_angsuran_wheel_loader = $total_nilai_angsuran_wheel_loader;
		$total_pemakaian_wheel_loader = $total_nilai_wheel_loader + $total_pemakaian_pemeliharaan_wheel_loader + $total_nilai_angsuran_wheel_loader + $total_nilai_penyusutan_wheel_loader;
		$total_pemakaian_bbm_solar = $total_akumulasi_bbm;
		//SPESIAL//

		$total_vol_evaluasi_batching_plant = ($pemakaian_vol_batching_plant!=0)?$vol_batching_plant - $pemakaian_vol_batching_plant * 1:0;
		$total_nilai_evaluasi_batching_plant = ($total_pemakaian_batching_plant!=0)?$batching_plant - $total_pemakaian_batching_plant * 1:0;
		$total_vol_evaluasi_pemeliharaan_batching_plant = ($pemakaian_vol_pemeliharaan_batching_plant!=0)?$vol_pemeliharaan_batching_plant - $pemakaian_vol_pemeliharaan_batching_plant * 1:0;
		$total_nilai_evaluasi_pemeliharaan_batching_plant = ($total_pemakaian_pemeliharaan_batching_plant!=0)?$pemeliharaan_batching_plant - $total_pemakaian_pemeliharaan_batching_plant * 1:0;
		$total_vol_evaluasi_penyusutan_batching_plant = ($pemakaian_vol_penyusutan_batching_plant!=0)?$vol_penyusutan_batching_plant - $pemakaian_vol_penyusutan_batching_plant * 1:0;
		$total_nilai_evaluasi_penyusutan_batching_plant = ($total_pemakaian_penyusutan_batching_plant!=0)?$penyusutan_batching_plant - $total_pemakaian_penyusutan_batching_plant * 1:0;
		$total_vol_evaluasi_angsuran_batching_plant = ($pemakaian_vol_angsuran_batching_plant!=0)?$vol_angsuran_batching_plant - $pemakaian_vol_angsuran_batching_plant * 1:0;
		$total_nilai_evaluasi_angsuran_batching_plant = ($total_pemakaian_angsuran_batching_plant!=0)?$angsuran_batching_plant - $total_pemakaian_angsuran_batching_plant * 1:0;
		$total_vol_evaluasi_truck_mixer = ($pemakaian_vol_truck_mixer!=0)?$vol_truck_mixer - $pemakaian_vol_truck_mixer * 1:0;
		$total_nilai_evaluasi_truck_mixer = ($total_pemakaian_truck_mixer!=0)?$truck_mixer - $total_pemakaian_truck_mixer * 1:0;
		$total_vol_evaluasi_wheel_loader = ($pemakaian_vol_wheel_loader!=0)?$vol_wheel_loader - $pemakaian_vol_wheel_loader * 1:0;
		$total_nilai_evaluasi_wheel_loader = ($total_pemakaian_wheel_loader!=0)?$wheel_loader - $total_pemakaian_wheel_loader * 1:0;
		$total_vol_evaluasi_pemeliharaan_wheel_loader = ($pemakaian_vol_pemeliharaan_wheel_loader!=0)?$vol_pemeliharaan_wheel_loader - $pemakaian_vol_pemeliharaan_wheel_loader * 1:0;
		$total_nilai_evaluasi_pemeliharaan_wheel_loader = ($total_pemakaian_pemeliharaan_wheel_loader!=0)?$pemeliharaan_wheel_loader - $total_pemakaian_pemeliharaan_wheel_loader * 1:0;
		$total_vol_evaluasi_penyusutan_wheel_loader = ($pemakaian_vol_penyusutan_wheel_loader!=0)?$vol_penyusutan_wheel_loader - $pemakaian_vol_penyusutan_wheel_loader * 1:0;
		$total_nilai_evaluasi_penyusutan_wheel_loader = ($total_pemakaian_penyusutan_wheel_loader!=0)?$penyusutan_wheel_loader - $total_pemakaian_penyusutan_wheel_loader * 1:0;
		$total_vol_evaluasi_angsuran_wheel_loader = ($pemakaian_vol_angsuran_wheel_loader!=0)?$vol_angsuran_wheel_loader - $pemakaian_vol_angsuran_wheel_loader * 1:0;
		$total_nilai_evaluasi_angsuran_wheel_loader = ($total_pemakaian_angsuran_wheel_loader!=0)?$angsuran_wheel_loader - $total_pemakaian_angsuran_wheel_loader * 1:0;
		$total_vol_evaluasi_bbm_solar = ($pemakaian_volume_solar!=0)?($vol_rap_bbm_solar * $total_volume) - $pemakaian_volume_solar * 1:0;
		$total_nilai_evaluasi_bbm_solar = ($pemakaian_nilai_solar!=0)?$bbm_solar - $pemakaian_nilai_solar * 1:0;

		$total_vol_rap_alat = $total_volume;
		$total_nilai_rap_alat = $batching_plant + $truck_mixer + $wheel_loader + $bbm_solar;
		$total_vol_realisasi_alat = $pemakaian_vol_batching_plant + $pemakaian_vol_truck_mixer + $pemakaian_vol_wheel_loader + $pemakaian_volume_solar;
		$total_nilai_realisasi_alat = $total_pemakaian_batching_plant + $total_pemakaian_truck_mixer + $total_pemakaian_wheel_loader + $pemakaian_nilai_solar;
		$total_vol_evaluasi_alat = $total_vol_evaluasi_batching_plant + $total_vol_evaluasi_truck_mixer + $total_vol_evaluasi_wheel_loader + $total_vol_evaluasi_bbm_solar;
		$total_nilai_evaluasi_alat = $total_nilai_evaluasi_batching_plant + $total_nilai_evaluasi_truck_mixer + $total_nilai_evaluasi_wheel_loader + $total_nilai_evaluasi_bbm_solar;
		?>

		<?php
		$rap_bua = $this->db->select('sum(det.jumlah) as total')
		->from('rap_bua rap')
		->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
		->where("rap.status = 'PUBLISH'")
		->where("rap.tanggal_rap_bua < '$date2'")
		->group_by("rap.id")
		->order_by('rap.tanggal_rap_bua','desc')->limit(1)
		->get()->row_array();
		$rap_bua = $rap_bua['total'];
		
		//REALISASI
		$konsumsi_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 116")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$konsumsi_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 116")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$konsumsi = $konsumsi_biaya['total'] + $konsumsi_jurnal['total'];

		$listrik_internet_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 118")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$listrik_internet_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 118")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$listrik_internet = $listrik_internet_biaya['total'] + $listrik_internet_jurnal['total'];

		$gaji_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun in ('114','115')")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$gaji_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun in ('114','115')")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$gaji = $gaji_biaya['total'] + $gaji_jurnal['total'];

		$akomodasi_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 143")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$akomodasi_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 143")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$akomodasi = $akomodasi_biaya['total'] + $akomodasi_jurnal['total'];

		$biaya_maintenance_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 141")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_maintenance_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 141")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_maintenance = $biaya_maintenance_biaya['total'] + $biaya_maintenance_jurnal['total'];

		$thr_bonus_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 117")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$thr_bonus_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 117")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$thr_bonus = $thr_bonus_biaya['total'] + $thr_bonus_jurnal['total'];

		$biaya_pengujian_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 178")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_pengujian_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 178")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_pengujian = $biaya_pengujian_biaya['total'] + $biaya_pengujian_jurnal['total'];

		$biaya_donasi_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 179")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_donasi_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 179")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_donasi = $biaya_donasi_biaya['total'] + $biaya_donasi_jurnal['total'];

		$biaya_sewa_kendaraan_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 100")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_sewa_kendaraan_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 100")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_sewa_kendaraan = $biaya_sewa_kendaraan_biaya['total'] + $biaya_sewa_kendaraan_jurnal['total'];

		$bensin_tol_parkir_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 78")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$bensin_tol_parkir_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 78")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$bensin_tol_parkir = $bensin_tol_parkir_biaya['total'] + $bensin_tol_parkir_jurnal['total'];

		$biaya_kirim_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 180")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_kirim_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 180")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_kirim = $biaya_kirim_biaya['total'] + $biaya_kirim_jurnal['total'];

		$pakaian_dinas_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 87")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$pakaian_dinas_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 87")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$pakaian_dinas = $pakaian_dinas_biaya['total'] + $pakaian_dinas_jurnal['total'];

		$perjalanan_dinas_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 62")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$perjalanan_dinas_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 62")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$perjalanan_dinas = $perjalanan_dinas_biaya['total'] + $perjalanan_dinas_jurnal['total'];

		$perlengkapan_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 98")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$perlengkapan_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 98")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$perlengkapan_kantor = $perlengkapan_kantor_biaya['total'] + $perlengkapan_kantor_jurnal['total'];

		$pengobatan_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 70")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$pengobatan_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 70")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$pengobatan = $pengobatan_biaya['total'] + $pengobatan_jurnal['total'];

		$alat_tulis_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 96")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$alat_tulis_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 96")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$alat_tulis_kantor = $alat_tulis_kantor_biaya['total'] + $alat_tulis_kantor_jurnal['total'];

		$keamanan_kebersihan_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 97")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$keamanan_kebersihan_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 97")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$keamanan_kebersihan = $keamanan_kebersihan_biaya['total'] + $keamanan_kebersihan_jurnal['total'];

		$sewa_mess_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 181")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$sewa_mess_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 181")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$sewa_mess = $sewa_mess_biaya['total'] + $sewa_mess_jurnal['total'];

		$biaya_lain_lain_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 94")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_lain_lain_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 94")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_lain_lain = $biaya_lain_lain_biaya['total'] + $biaya_lain_lain_jurnal['total'];
		
		$biaya_adm_bank_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 182")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_adm_bank_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 182")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_adm_bank = $biaya_adm_bank_biaya['total'] + $biaya_adm_bank_jurnal['total'];

		$biaya_jamsostek_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 183")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_jamsostek_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 183")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_jamsostek = $biaya_jamsostek_biaya['total'] + $biaya_jamsostek_jurnal['total'];

		$biaya_iuran_biaya = $this->db->select('sum(pdb.jumlah) as total')
		->from('pmm_biaya pb ')
		->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 184")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();

		$biaya_iuran_jurnal = $this->db->select('sum(pdb.debit) as total')
		->from('pmm_jurnal_umum pb ')
		->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
		->join('pmm_coa c','pdb.akun = c.id','left')
		->where("pdb.akun = 184")
		->where("pb.status = 'PAID'")
		->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
		->get()->row_array();
		$biaya_iuran = $biaya_iuran_biaya['total'] + $biaya_iuran_jurnal['total'];

		$total_nilai_realisasi_bua = $konsumsi + $listrik_internet + $gaji + $akomodasi + $biaya_maintenance + $thr_bonus + $biaya_pengujian + $biaya_donasi + $biaya_sewa_kendaraan + $bensin_tol_parkir + $biaya_kirim + $pakaian_dinas + $perjalanan_dinas + $perlengkapan_kantor + $pengobatan + $alat_tulis_kantor + $keamanan_kebersihan + $sewa_mess + $biaya_lain_lain + $biaya_adm_bank + $biaya_jamsostek + $biaya_iuran;

		$total_volume_rap_bua = $total_volume;
		$total_nilai_rap_bua = $rap_bua / 6;
		$total_harsat_rap_bua = $total_nilai_rap_bua / $total_volume_rap_bua;
		
		$total_volume_realisasi_bua = $total_volume;
		$total_nilai_realisasi_bua = $total_nilai_realisasi_bua;
		$total_harsat_realisasi_bua = $total_nilai_realisasi_bua / $total_volume_realisasi_bua;

		$total_volume_evaluasi_bua = $total_volume_rap_bua - $total_volume_realisasi_bua;
		$total_nilai_evaluasi_bua = $total_nilai_rap_bua - $total_nilai_realisasi_bua;
		?>

		<table width="98%" border="0" cellpadding="3">
			<tr class="table-active">
	            <th width="5%" align="center" rowspan="2">&nbsp; <br />NO.</th>
				<th width="15%" align="center" rowspan="2">&nbsp; <br />URAIAN</th>
				<th width="30%" align="center" colspan="3">RAP</th>
				<th width="30%" align="center" colspan="3">REALISASI</th>
				<th width="20%" align="center" colspan="2">DEVIASI</th>
	        </tr>
			<tr class="table-active">
	            <th align="right">VOL.</th>
				<th align="right">HARSAT</th>
				<th align="right">NILAI</th>
				<th align="right">VOL.</th>
				<th align="right">HARSAT</th>
				<th align="right">NILAI</th>
				<th align="right">VOL.</th>
				<th align="right">NILAI</th>
	        </tr>
			<tr class="table-active3">
	            <th align="center"><b>1</b></th>
				<th align="left"><b>BAHAN</b></th>
				<th align="right"><?php echo number_format($total_volume_komposisi,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_komposisi / $total_volume_komposisi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_komposisi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_realisasi,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_realisasi / $total_volume_realisasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_realisasi,0,',','.');?></th>
				<?php
				$styleColor = $total_volume_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_volume_evaluasi < 0 ? "(".number_format(-$total_volume_evaluasi,2,',','.').")" : number_format($total_volume_evaluasi,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi < 0 ? "(".number_format(-$total_nilai_evaluasi,0,',','.').")" : number_format($total_nilai_evaluasi,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th align="center"><b>2</b></th>
				<th align="left"><b>ALAT</b></th>
				<th align="right"><?php echo number_format($total_vol_rap_alat,2,',','.');?></th>
				<?php
				$total_harsat_rap_alat = (round($total_vol_rap_alat,2)!=0)?($total_nilai_rap_alat / $total_vol_rap_alat) * 1:0;
				?>
				<th align="right"><?php echo number_format($total_harsat_rap_alat ,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_rap_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_vol_realisasi_alat,2,',','.');?></th>
				<?php
				$total_harsat_realisasi_alat = (round($total_vol_realisasi_alat,2)!=0)?($total_nilai_realisasi_alat / $total_vol_realisasi_alat) * 1:0;
				?>
				<th align="right"><?php echo number_format($total_harsat_realisasi_alat,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_realisasi_alat,0,',','.');?></th>
				<?php
				$styleColor = $total_vol_evaluasi_alat < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_alat < 0 ? "(".number_format(-$total_vol_evaluasi_alat,2,',','.').")" : number_format($total_vol_evaluasi_alat,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_alat < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi_alat < 0 ? "(".number_format(-$total_nilai_evaluasi_alat,0,',','.').")" : number_format($total_nilai_evaluasi_alat,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th align="center">3</th>
				<th align="left"><b>BUA</b></th>
				<th align="right"><?php echo number_format($total_volume_rap_bua,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harsat_rap_bua,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_rap_bua,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_volume_realisasi_bua,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_harsat_realisasi_bua,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_nilai_realisasi_bua,0,',','.');?></th>
				<?php
				$styleColor = $total_volume_evaluasi_bua < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo number_format($total_volume_evaluasi_bua,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_bua < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi_bua < 0 ? "(".number_format(-$total_nilai_evaluasi_bua,0,',','.').")" : number_format($total_nilai_evaluasi_bua,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
				<th align="right" colspan="2">TOTAL</th>
				<th align="right"></th>
				<th align="right"></th>
				<?php
				$total_nilai_rap = $total_nilai_komposisi + $total_nilai_rap_alat + $total_nilai_rap_bua;
				?>
				<th align="right"><?php echo number_format($total_nilai_rap,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"></th>
				<?php
				$total_nilai_realisasi = $total_nilai_realisasi + $total_nilai_realisasi_alat + $total_nilai_realisasi_bua;
				?>
				<th align="right"><?php echo number_format($total_nilai_realisasi,0,',','.');?></th>
				<th align="right"></th>
				<?php
				$total_nilai_evaluasi = $total_nilai_evaluasi + $total_nilai_evaluasi_alat + $total_nilai_evaluasi_bua;
				$styleColor = $total_nilai_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th align="right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi < 0 ? "(".number_format(-$total_nilai_evaluasi,0,',','.').")" : number_format($total_nilai_evaluasi,0,',','.');?></th>
			</tr>
		</table>
		<table width="98%" border="0" cellpadding="30">
			<tr >
				<td width="5%"></td>
				<td width="90%">
					<table width="100%" border="0" cellpadding="2">
						<tr>
							<td align="center" >
								Dibuat Oleh
							</td>
							<td align="center">
								Diperiksa Oleh
							</td>
							<td align="center">
								Disetujui Oleh
							</td>
						</tr>
						<tr class="">
							<td align="center" height="50px">
								<img src="uploads/ttd_satria.png" width="50px">
							</td>
							<td align="center">
								<!--<img src="uploads/ttd_erika.png" width="50px">-->
							</td>
							<td align="center">
								<img src="uploads/ttd_deddy.png" width="50px">
							</td>
						</tr>
						<tr>
							<td align="center">
								<b><u>Satria Widura Drana Wisesa</u><br />
								Ka. Unit Bisnis</b>
							</td>
							<td align="center">
								<b><u>Erika Sinaga</u><br />
								Dir. Keuangan & SDM</b>
							</td>
							<td align="center">
								<b><u>Deddy Sarwobiso</u><br />
								Direktur Utama</b>
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"></td>
			</tr>
		</table>
	</body>
</html>