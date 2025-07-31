<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN RENCANA KERJA PRODUKSI</title>
	  
	  <style type="text/css">
		body {
			font-family: helvetica;
			font-size: 6px;
		}

		table.table-border-pojok-kiri, th.table-border-pojok-kiri, td.table-border-pojok-kiri {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid #cccccc;
			border-left: 1px solid black;
		}

		table.table-border-pojok-tengah, th.table-border-pojok-tengah, td.table-border-pojok-tengah {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid #cccccc;
		}

		table.table-border-pojok-kanan, th.table-border-pojok-kanan, td.table-border-pojok-kanan {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
			border-right: 1px solid black;
		}

		table.table-border-spesial, th.table-border-spesial, td.table-border-spesial {
			border-left: 1px solid black;
			border-right: 1px solid black;
		}

		table.table-border-spesial-kiri, th.table-border-spesial-kiri, td.table-border-spesial-kiri {
			border-left: 1px solid black;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table.table-border-spesial-tengah, th.table-border-spesial-tengah, td.table-border-spesial-tengah {
			border-left: 1px solid #cccccc;
			border-right: 1px solid #cccccc;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table.table-border-spesial-kanan, th.table-border-spesial-kanan, td.table-border-spesial-kanan {
			border-left: 1px solid #cccccc;
			border-right: 1px solid black;
			border-top: 2px solid black;
			border-bottom: 2px solid black;
		}

		table tr.table-judul{
			border: 1px solid;
			background-color: #e69500;
			font-weight: bold;
			color: black;
		}
			
		table tr.table-baris1{
			background-color: none;
		}

		table tr.table-baris1-bold{
			background-color: none;
			font-weight: bold;
		}
			
		table tr.table-total{
			background-color: #FFFF00;
			font-weight: bold;
			color: black;
		}

		table tr.table-subtotal{
			background-color: #b1cee8;
			font-weight: bold;
		}

		table tr.table-total2{
			background-color: #ea9999;
			font-weight: bold;
			color: black;
		}
	  </style>

	</head>
	<body>
		<div align="center" style="display: block;font-weight: bold;font-size: 7px;">Laporan Rencana Kerja - Divisi Stone Crusher</div>
		<br /><br />
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
		
		<?php
		//AKUMULASI
		$kunci_bahan_baku = $this->db->select('date')->order_by('date','desc')->limit(1)->get_where('kunci_bahan_baku')->row_array();
		$last_opname = date('d-m-Y', strtotime('0 days', strtotime($kunci_bahan_baku['date'])));

		$date_0_awal = date('2025-01-01');
		$date_0_akhir = date('Y-m-d', strtotime($last_opname));

		$akumulasi_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f, SUM(vol_produk_g) as vol_produk_g, SUM(vol_produk_h) as vol_produk_h, SUM(price_a) as price_a, SUM(price_b) as price_b, SUM(price_c) as price_c, SUM(price_d) as price_d, SUM(price_e) as price_e, SUM(price_f) as price_f, SUM(price_g) as price_g, SUM(price_h) as price_h')
		->from('rak')
		->where("tanggal_rencana_kerja between '$date_0_awal' and '$date_0_akhir'")
		->get()->row_array();
		$akumulasi_vol_produk_a = $akumulasi_produk['vol_produk_a'];
		$akumulasi_produk_a = $akumulasi_produk['price_a'];
		$akumulasi_vol_produk_b = $akumulasi_produk['vol_produk_b'];
		$akumulasi_produk_b = $akumulasi_produk['price_b'];
		$akumulasi_vol_produk_c = $akumulasi_produk['vol_produk_c'];
		$akumulasi_produk_c = $akumulasi_produk['price_c'];
		$akumulasi_vol_produk_d = $akumulasi_produk['vol_produk_d'];
		$akumulasi_produk_d = $akumulasi_produk['price_d'];
		$akumulasi_vol_produk_e = $akumulasi_produk['vol_produk_e'];
		$akumulasi_produk_e = $akumulasi_produk['price_e'];
		$akumulasi_vol_produk_f = $akumulasi_produk['vol_produk_f'];
		$akumulasi_produk_f = $akumulasi_produk['price_f'];
		$akumulasi_vol_produk_g = $akumulasi_produk['vol_produk_g'];
		$akumulasi_produk_g = $akumulasi_produk['price_g'];
		$akumulasi_vol_produk_h = $akumulasi_produk['vol_produk_h'];
		$akumulasi_produk_h = $akumulasi_produk['price_h'];
		$akumulasi_vol_boulder = $akumulasi_produk['vol_boulder'];
		$akumulasi_boulder = $akumulasi_produk['boulder'];
		$akumulasi_stone_crusher = $akumulasi_produk['stone_crusher'];
		$akumulasi_wheel_loader = $akumulasi_produk['wheel_loader'];
		$akumulasi_maintenance = $akumulasi_produk['maintenance'];
		$akumulasi_bbm_solar = $akumulasi_produk['bbm_solar'];
		$akumulasi_tangki = $akumulasi_produk['tangki'];
		$akumulasi_timbangan = $akumulasi_produk['timbangan'];
		$akumulasi_overhead= $akumulasi_produk['overhead'];

		//BULAN SAAT INI
		$date_now = date('Y-m-d');
		$date_1_awal = date('Y-m-01', (strtotime($date_now)));
		$date_1_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_1_awal)));
		$rak1_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f, SUM(vol_produk_g) as vol_produk_g, SUM(vol_produk_h) as vol_produk_h, SUM(price_a) as price_a, SUM(price_b) as price_b, SUM(price_c) as price_c, SUM(price_d) as price_d, SUM(price_e) as price_e, SUM(price_f) as price_f, SUM(price_g) as price_g, SUM(price_h) as price_h')
		->from('rak')
		->where("tanggal_rencana_kerja between '$date_1_awal' and '$date_1_akhir'")
		->get()->row_array();
		$rak1_vol_produk_a = $rak1_produk['vol_produk_a'];
		$rak1_produk_a = $rak1_produk['price_a'];
		$rak1_vol_produk_b = $rak1_produk['vol_produk_b'];
		$rak1_produk_b = $rak1_produk['price_b'];
		$rak1_vol_produk_c = $rak1_produk['vol_produk_c'];
		$rak1_produk_c = $rak1_produk['price_c'];
		$rak1_vol_produk_d = $rak1_produk['vol_produk_d'];
		$rak1_produk_d = $rak1_produk['price_d'];
		$rak1_vol_produk_e = $rak1_produk['vol_produk_e'];
		$rak1_produk_e = $rak1_produk['price_e'];
		$rak1_vol_produk_f = $rak1_produk['vol_produk_f'];
		$rak1_produk_f = $rak1_produk['price_f'];
		$rak1_vol_produk_g = $rak1_produk['vol_produk_g'];
		$rak1_produk_g = $rak1_produk['price_g'];
		$rak1_vol_produk_h = $rak1_produk['vol_produk_h'];
		$rak1_produk_h = $rak1_produk['price_h'];
		$rak1_vol_boulder = $rak1_produk['vol_boulder'];
		$rak1_boulder = $rak1_produk['boulder'];
		$rak1_stone_crusher = $rak1_produk['stone_crusher'];
		$rak1_wheel_loader = $rak1_produk['wheel_loader'];
		$rak1_maintenance = $rak1_produk['maintenance'];
		$rak1_bbm_solar = $rak1_produk['bbm_solar'];
		$rak1_tangki = $rak1_produk['tangki'];
		$rak1_timbangan = $rak1_produk['timbangan'];
		$rak1_overhead= $rak1_produk['overhead'];

		//BULAN 2
		$date_2_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_1_akhir)));
		$date_2_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_2_awal)));
		$rak2_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f, SUM(vol_produk_g) as vol_produk_g, SUM(vol_produk_h) as vol_produk_h, SUM(price_a) as price_a, SUM(price_b) as price_b, SUM(price_c) as price_c, SUM(price_d) as price_d, SUM(price_e) as price_e, SUM(price_f) as price_f, SUM(price_g) as price_g, SUM(price_h) as price_h')
		->from('rak')
		->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
		->get()->row_array();
		$rak2_vol_produk_a = $rak2_produk['vol_produk_a'];
		$rak2_produk_a = $rak2_produk['price_a'];
		$rak2_vol_produk_b = $rak2_produk['vol_produk_b'];
		$rak2_produk_b = $rak2_produk['price_b'];
		$rak2_vol_produk_c = $rak2_produk['vol_produk_c'];
		$rak2_produk_c = $rak2_produk['price_c'];
		$rak2_vol_produk_d = $rak2_produk['vol_produk_d'];
		$rak2_produk_d = $rak2_produk['price_d'];
		$rak2_vol_produk_e = $rak2_produk['vol_produk_e'];
		$rak2_produk_e = $rak2_produk['price_e'];
		$rak2_vol_produk_f = $rak2_produk['vol_produk_f'];
		$rak2_produk_f = $rak2_produk['price_f'];
		$rak2_vol_produk_g = $rak2_produk['vol_produk_g'];
		$rak2_produk_g = $rak2_produk['price_g'];
		$rak2_vol_produk_h = $rak2_produk['vol_produk_h'];
		$rak2_produk_h = $rak2_produk['price_h'];
		$rak2_vol_boulder = $rak2_produk['vol_boulder'];
		$rak2_boulder = $rak2_produk['boulder'];
		$rak2_stone_crusher = $rak2_produk['stone_crusher'];
		$rak2_wheel_loader = $rak2_produk['wheel_loader'];
		$rak2_maintenance = $rak2_produk['maintenance'];
		$rak2_bbm_solar = $rak2_produk['bbm_solar'];
		$rak2_tangki = $rak2_produk['tangki'];
		$rak2_timbangan = $rak2_produk['timbangan'];
		$rak2_overhead= $rak2_produk['overhead'];
		
		//BULAN 3
		$date_3_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_2_akhir)));
		$date_3_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_3_awal)));
		$rak3_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f, SUM(vol_produk_g) as vol_produk_g, SUM(vol_produk_h) as vol_produk_h, SUM(price_a) as price_a, SUM(price_b) as price_b, SUM(price_c) as price_c, SUM(price_d) as price_d, SUM(price_e) as price_e, SUM(price_f) as price_f, SUM(price_g) as price_g, SUM(price_h) as price_h')
		->from('rak')
		->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
		->get()->row_array();
		$rak3_vol_produk_a = $rak3_produk['vol_produk_a'];
		$rak3_produk_a = $rak3_produk['price_a'];
		$rak3_vol_produk_b = $rak3_produk['vol_produk_b'];
		$rak3_produk_b = $rak3_produk['price_b'];
		$rak3_vol_produk_c = $rak3_produk['vol_produk_c'];
		$rak3_produk_c = $rak3_produk['price_c'];
		$rak3_vol_produk_d = $rak3_produk['vol_produk_d'];
		$rak3_produk_d = $rak3_produk['price_d'];
		$rak3_vol_produk_e = $rak3_produk['vol_produk_e'];
		$rak3_produk_e = $rak3_produk['price_e'];
		$rak3_vol_produk_f = $rak3_produk['vol_produk_f'];
		$rak3_produk_f = $rak3_produk['price_f'];
		$rak3_vol_produk_g = $rak3_produk['vol_produk_g'];
		$rak3_produk_g = $rak3_produk['price_g'];
		$rak3_vol_produk_h = $rak3_produk['vol_produk_h'];
		$rak3_produk_h = $rak3_produk['price_h'];
		$rak3_vol_boulder = $rak3_produk['vol_boulder'];
		$rak3_boulder = $rak3_produk['boulder'];
		$rak3_stone_crusher = $rak3_produk['stone_crusher'];
		$rak3_wheel_loader = $rak3_produk['wheel_loader'];
		$rak3_maintenance = $rak3_produk['maintenance'];
		$rak3_bbm_solar = $rak3_produk['bbm_solar'];
		$rak3_tangki = $rak3_produk['tangki'];
		$rak3_timbangan = $rak3_produk['timbangan'];
		$rak3_overhead= $rak3_produk['overhead'];

		//BULAN 4
		$date_4_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_3_akhir)));
		$date_4_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_4_awal)));
		$rak4_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f, SUM(vol_produk_g) as vol_produk_g, SUM(vol_produk_h) as vol_produk_h, SUM(price_a) as price_a, SUM(price_b) as price_b, SUM(price_c) as price_c, SUM(price_d) as price_d, SUM(price_e) as price_e, SUM(price_f) as price_f, SUM(price_g) as price_g, SUM(price_h) as price_h')
		->from('rak')
		->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
		->get()->row_array();
		$rak4_vol_produk_a = $rak4_produk['vol_produk_a'];
		$rak4_produk_a = $rak4_produk['price_a'];
		$rak4_vol_produk_b = $rak4_produk['vol_produk_b'];
		$rak4_produk_b = $rak4_produk['price_b'];
		$rak4_vol_produk_c = $rak4_produk['vol_produk_c'];
		$rak4_produk_c = $rak4_produk['price_c'];
		$rak4_vol_produk_d = $rak4_produk['vol_produk_d'];
		$rak4_produk_d = $rak4_produk['price_d'];
		$rak4_vol_produk_e = $rak4_produk['vol_produk_e'];
		$rak4_produk_e = $rak4_produk['price_e'];
		$rak4_vol_produk_f = $rak4_produk['vol_produk_f'];
		$rak4_produk_f = $rak4_produk['price_f'];
		$rak4_vol_produk_g = $rak4_produk['vol_produk_g'];
		$rak4_produk_g = $rak4_produk['price_g'];
		$rak4_vol_produk_h = $rak4_produk['vol_produk_h'];
		$rak4_produk_h = $rak4_produk['price_h'];
		$rak4_vol_boulder = $rak4_produk['vol_boulder'];
		$rak4_boulder = $rak4_produk['boulder'];
		$rak4_stone_crusher = $rak4_produk['stone_crusher'];
		$rak4_wheel_loader = $rak4_produk['wheel_loader'];
		$rak4_maintenance = $rak4_produk['maintenance'];
		$rak4_bbm_solar = $rak4_produk['bbm_solar'];
		$rak4_tangki = $rak4_produk['tangki'];
		$rak4_timbangan = $rak4_produk['timbangan'];
		$rak4_overhead= $rak4_produk['overhead'];

		//BULAN 5
		$date_5_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_4_akhir)));
		$date_5_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_5_awal)));
		$rak5_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f, SUM(vol_produk_g) as vol_produk_g, SUM(vol_produk_h) as vol_produk_h, SUM(price_a) as price_a, SUM(price_b) as price_b, SUM(price_c) as price_c, SUM(price_d) as price_d, SUM(price_e) as price_e, SUM(price_f) as price_f, SUM(price_g) as price_g, SUM(price_h) as price_h')
		->from('rak')
		->where("tanggal_rencana_kerja between '$date_5_awal' and '$date_5_akhir'")
		->get()->row_array();
		$rak5_vol_produk_a = $rak5_produk['vol_produk_a'];
		$rak5_produk_a = $rak5_produk['price_a'];
		$rak5_vol_produk_b = $rak5_produk['vol_produk_b'];
		$rak5_produk_b = $rak5_produk['price_b'];
		$rak5_vol_produk_c = $rak5_produk['vol_produk_c'];
		$rak5_produk_c = $rak5_produk['price_c'];
		$rak5_vol_produk_d = $rak5_produk['vol_produk_d'];
		$rak5_produk_d = $rak5_produk['price_d'];
		$rak5_vol_produk_e = $rak5_produk['vol_produk_e'];
		$rak5_produk_e = $rak5_produk['price_e'];
		$rak5_vol_produk_f = $rak5_produk['vol_produk_f'];
		$rak5_produk_f = $rak5_produk['price_f'];
		$rak5_vol_produk_g = $rak5_produk['vol_produk_g'];
		$rak5_produk_g = $rak5_produk['price_g'];
		$rak5_vol_produk_h = $rak5_produk['vol_produk_h'];
		$rak5_produk_h = $rak5_produk['price_h'];
		$rak5_vol_boulder = $rak5_produk['vol_boulder'];
		$rak5_boulder = $rak5_produk['boulder'];
		$rak5_stone_crusher = $rak5_produk['stone_crusher'];
		$rak5_wheel_loader = $rak5_produk['wheel_loader'];
		$rak5_maintenance = $rak5_produk['maintenance'];
		$rak5_bbm_solar = $rak5_produk['bbm_solar'];
		$rak5_tangki = $rak5_produk['tangki'];
		$rak5_timbangan = $rak5_produk['timbangan'];
		$rak5_overhead= $rak5_produk['overhead'];

		//BULAN 6
		$date_6_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_5_akhir)));
		$date_6_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_6_awal)));
		$rak6_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f, SUM(vol_produk_g) as vol_produk_g, SUM(vol_produk_h) as vol_produk_h, SUM(price_a) as price_a, SUM(price_b) as price_b, SUM(price_c) as price_c, SUM(price_d) as price_d, SUM(price_e) as price_e, SUM(price_f) as price_f, SUM(price_g) as price_g, SUM(price_h) as price_h')
		->from('rak')
		->where("tanggal_rencana_kerja between '$date_6_awal' and '$date_6_akhir'")
		->get()->row_array();
		$rak6_vol_produk_a = $rak6_produk['vol_produk_a'];
		$rak6_produk_a = $rak6_produk['price_a'];
		$rak6_vol_produk_b = $rak6_produk['vol_produk_b'];
		$rak6_produk_b = $rak6_produk['price_b'];
		$rak6_vol_produk_c = $rak6_produk['vol_produk_c'];
		$rak6_produk_c = $rak6_produk['price_c'];
		$rak6_vol_produk_d = $rak6_produk['vol_produk_d'];
		$rak6_produk_d = $rak6_produk['price_d'];
		$rak6_vol_produk_e = $rak6_produk['vol_produk_e'];
		$rak6_produk_e = $rak6_produk['price_e'];
		$rak6_vol_produk_f = $rak6_produk['vol_produk_f'];
		$rak6_produk_f = $rak6_produk['price_f'];
		$rak6_vol_produk_g = $rak6_produk['vol_produk_g'];
		$rak6_produk_g = $rak6_produk['price_g'];
		$rak6_vol_produk_h = $rak6_produk['vol_produk_h'];
		$rak6_produk_h = $rak6_produk['price_h'];
		$rak6_vol_boulder = $rak6_produk['vol_boulder'];
		$rak6_boulder = $rak6_produk['boulder'];
		$rak6_stone_crusher = $rak6_produk['stone_crusher'];
		$rak6_wheel_loader = $rak6_produk['wheel_loader'];
		$rak6_maintenance = $rak6_produk['maintenance'];
		$rak6_bbm_solar = $rak6_produk['bbm_solar'];
		$rak6_tangki = $rak6_produk['tangki'];
		$rak6_timbangan = $rak6_produk['timbangan'];
		$rak6_overhead= $rak6_produk['overhead'];

		//BULAN 7
		$date_7_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_6_akhir)));
		$date_7_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_7_awal)));
		$rak7_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f, SUM(vol_produk_g) as vol_produk_g, SUM(vol_produk_h) as vol_produk_h, SUM(price_a) as price_a, SUM(price_b) as price_b, SUM(price_c) as price_c, SUM(price_d) as price_d, SUM(price_e) as price_e, SUM(price_f) as price_f, SUM(price_g) as price_g, SUM(price_h) as price_h')
		->from('rak')
		->where("tanggal_rencana_kerja between '$date_7_awal' and '$date_7_akhir'")
		->get()->row_array();
		$rak7_vol_produk_a = $rak7_produk['vol_produk_a'];
		$rak7_produk_a = $rak7_produk['price_a'];
		$rak7_vol_produk_b = $rak7_produk['vol_produk_b'];
		$rak7_produk_b = $rak7_produk['price_b'];
		$rak7_vol_produk_c = $rak7_produk['vol_produk_c'];
		$rak7_produk_c = $rak7_produk['price_c'];
		$rak7_vol_produk_d = $rak7_produk['vol_produk_d'];
		$rak7_produk_d = $rak7_produk['price_d'];
		$rak7_vol_produk_e = $rak7_produk['vol_produk_e'];
		$rak7_produk_e = $rak7_produk['price_e'];
		$rak7_vol_produk_f = $rak7_produk['vol_produk_f'];
		$rak7_produk_f = $rak7_produk['price_f'];
		$rak7_vol_produk_g = $rak7_produk['vol_produk_g'];
		$rak7_produk_g = $rak7_produk['price_g'];
		$rak7_vol_produk_h = $rak7_produk['vol_produk_h'];
		$rak7_produk_h = $rak7_produk['price_h'];
		$rak7_vol_boulder = $rak7_produk['vol_boulder'];
		$rak7_boulder = $rak7_produk['boulder'];
		$rak7_stone_crusher = $rak7_produk['stone_crusher'];
		$rak7_wheel_loader = $rak7_produk['wheel_loader'];
		$rak7_maintenance = $rak7_produk['maintenance'];
		$rak7_bbm_solar = $rak7_produk['bbm_solar'];
		$rak7_tangki = $rak7_produk['tangki'];
		$rak7_timbangan = $rak7_produk['timbangan'];
		$rak7_overhead= $rak7_produk['overhead'];

		//JUMLAH PENDAPATAN USAHA
		$jumlah_vol_produk_a = $akumulasi_vol_produk_a + $rak1_vol_produk_a + $rak2_vol_produk_a + $rak3_vol_produk_a + $rak4_vol_produk_a + $rak5_vol_produk_a + $rak6_vol_produk_a + $rak7_vol_produk_a;
		$jumlah_produk_a = $akumulasi_produk_a + $rak1_produk_a + $rak2_produk_a + $rak3_produk_a + $rak4_produk_a + $rak5_produk_a + $rak6_produk_a + $rak7_produk_a;

		$jumlah_vol_produk_b = $akumulasi_vol_produk_b + $rak1_vol_produk_b + $rak2_vol_produk_b + $rak3_vol_produk_b + $rak4_vol_produk_b + $rak5_vol_produk_b + $rak6_vol_produk_b + $rak7_vol_produk_b;
		$jumlah_produk_b = $akumulasi_produk_b + $rak1_produk_b + $rak2_produk_b + $rak3_produk_b + $rak4_produk_b + $rak5_produk_b + $rak6_produk_b + $rak7_produk_b;

		$jumlah_vol_produk_c = $akumulasi_vol_produk_c + $rak1_vol_produk_c + $rak2_vol_produk_c + $rak3_vol_produk_c + $rak4_vol_produk_c + $rak5_vol_produk_c + $rak6_vol_produk_c + $rak7_vol_produk_c;
		$jumlah_produk_c = $akumulasi_produk_c + $rak1_produk_c + $rak2_produk_c + $rak3_produk_c + $rak4_produk_c + $rak5_produk_c + $rak6_produk_c + $rak7_produk_c;

		$jumlah_vol_produk_d = $akumulasi_vol_produk_d + $rak1_vol_produk_d + $rak2_vol_produk_d + $rak3_vol_produk_d + $rak4_vol_produk_d + $rak5_vol_produk_d + $rak6_vol_produk_d + $rak7_vol_produk_d;
		$jumlah_produk_d = $akumulasi_produk_d + $rak1_produk_d + $rak2_produk_d + $rak3_produk_d + $rak4_produk_d + $rak5_produk_d + $rak6_produk_d + $rak7_produk_d;

		$jumlah_vol_produk_e = $akumulasi_vol_produk_e + $rak1_vol_produk_e + $rak2_vol_produk_e + $rak3_vol_produk_e + $rak4_vol_produk_e + $rak5_vol_produk_e + $rak6_vol_produk_e + $rak7_vol_produk_e;
		$jumlah_produk_e = $akumulasi_produk_e + $rak1_produk_e + $rak2_produk_e + $rak3_produk_e + $rak4_produk_e + $rak5_produk_e + $rak6_produk_e + $rak7_produk_e;

		$jumlah_vol_produk_f = $akumulasi_vol_produk_f + $rak1_vol_produk_f + $rak2_vol_produk_f + $rak3_vol_produk_f + $rak4_vol_produk_f + $rak5_vol_produk_f + $rak6_vol_produk_f + $rak7_vol_produk_f;
		$jumlah_produk_f = $akumulasi_produk_f + $rak1_produk_f + $rak2_produk_f + $rak3_produk_f + $rak4_produk_f + $rak5_produk_f + $rak6_produk_f + $rak7_produk_f;

		$jumlah_vol_produk_g = $akumulasi_vol_produk_g + $rak1_vol_produk_g + $rak2_vol_produk_g + $rak3_vol_produk_g + $rak4_vol_produk_g + $rak5_vol_produk_g + $rak6_vol_produk_g + $rak7_vol_produk_g;
		$jumlah_produk_g = $akumulasi_produk_g + $rak1_produk_g + $rak2_produk_g + $rak3_produk_g + $rak4_produk_g + $rak5_produk_g + $rak6_produk_g + $rak7_produk_g;

		$jumlah_vol_produk_h = $akumulasi_vol_produk_h + $rak1_vol_produk_h + $rak2_vol_produk_h + $rak3_vol_produk_h + $rak4_vol_produk_h + $rak5_vol_produk_h + $rak6_vol_produk_h + $rak7_vol_produk_h;
		$jumlah_produk_h = $akumulasi_produk_h + $rak1_produk_h + $rak2_produk_h + $rak3_produk_h + $rak4_produk_h + $rak5_produk_h + $rak6_produk_h + $rak7_produk_h;

		$jumlah_vol_akumulasi = $akumulasi_vol_produk_a + $akumulasi_vol_produk_b + $akumulasi_vol_produk_c + $akumulasi_vol_produk_d + $akumulasi_vol_produk_e + $akumulasi_vol_produk_f + $akumulasi_vol_produk_g + $akumulasi_vol_produk_h;
		$jumlah_akumulasi = $akumulasi_produk_a + $akumulasi_produk_b + $akumulasi_produk_c + $akumulasi_produk_d + $akumulasi_produk_e + $akumulasi_produk_f + $akumulasi_produk_g + $akumulasi_produk_h;

		$jumlah_vol_rak1 = $rak1_vol_produk_a + $rak1_vol_produk_b + $rak1_vol_produk_c + $rak1_vol_produk_d + $rak1_vol_produk_e + $rak1_vol_produk_f + $rak1_vol_produk_g + $rak1_vol_produk_h;
		$jumlah_rak1 = $rak1_produk_a + $rak1_produk_b + $rak1_produk_c + $rak1_produk_d + $rak1_produk_e + $rak1_produk_f + $rak1_produk_g + $rak1_produk_h;

		$jumlah_vol_rak2 = $rak2_vol_produk_a + $rak2_vol_produk_b + $rak2_vol_produk_c + $rak2_vol_produk_d + $rak2_vol_produk_e + $rak2_vol_produk_f + $rak2_vol_produk_g + $rak2_vol_produk_h;
		$jumlah_rak2 = $rak2_produk_a + $rak2_produk_b + $rak2_produk_c + $rak2_produk_d + $rak2_produk_e + $rak2_produk_f + $rak2_produk_g + $rak2_produk_h;

		$jumlah_vol_rak3 = $rak3_vol_produk_a + $rak3_vol_produk_b + $rak3_vol_produk_c + $rak3_vol_produk_d + $rak3_vol_produk_e + $rak3_vol_produk_f + $rak3_vol_produk_g + $rak3_vol_produk_h;
		$jumlah_rak3 = $rak3_produk_a + $rak3_produk_b + $rak3_produk_c + $rak3_produk_d + $rak3_produk_e + $rak3_produk_f + $rak3_produk_g + $rak3_produk_h;

		$jumlah_vol_rak4 = $rak4_vol_produk_a + $rak4_vol_produk_b + $rak4_vol_produk_c + $rak4_vol_produk_d + $rak4_vol_produk_e + $rak4_vol_produk_f + $rak4_vol_produk_g + $rak4_vol_produk_h;
		$jumlah_rak4 = $rak4_produk_a + $rak4_produk_b + $rak4_produk_c + $rak4_produk_d + $rak4_produk_e + $rak4_produk_f + $rak4_produk_g + $rak4_produk_h;

		$jumlah_vol_rak5 = $rak5_vol_produk_a + $rak5_vol_produk_b + $rak5_vol_produk_c + $rak5_vol_produk_d + $rak5_vol_produk_e + $rak5_vol_produk_f + $rak5_vol_produk_g + $rak5_vol_produk_h;
		$jumlah_rak5 = $rak5_produk_a + $rak5_produk_b + $rak5_produk_c + $rak5_produk_d + $rak5_produk_e + $rak5_produk_f + $rak5_produk_g + $rak5_produk_h;

		$jumlah_vol_rak6 = $rak6_vol_produk_a + $rak6_vol_produk_b + $rak6_vol_produk_c + $rak6_vol_produk_d + $rak6_vol_produk_e + $rak6_vol_produk_f + $rak6_vol_produk_g + $rak6_vol_produk_h;
		$jumlah_rak6 = $rak6_produk_a + $rak6_produk_b + $rak6_produk_c + $rak6_produk_d + $rak6_produk_e + $rak6_produk_f + $rak6_produk_g + $rak6_produk_h;

		$jumlah_vol_rak7 = $rak7_vol_produk_a + $rak7_vol_produk_b + $rak7_vol_produk_c + $rak7_vol_produk_d + $rak7_vol_produk_e + $rak7_vol_produk_f + $rak7_vol_produk_g + $rak7_vol_produk_h;
		$jumlah_rak7 = $rak7_produk_a + $rak7_produk_b + $rak7_produk_c + $rak7_produk_d + $rak7_produk_e + $rak7_produk_f + $rak7_produk_g + $rak7_produk_h;

		$total_jumlah_vol = $jumlah_vol_produk_a + $jumlah_vol_produk_b + $jumlah_vol_produk_c + $jumlah_vol_produk_d + $jumlah_vol_produk_e + $jumlah_vol_produk_f + $jumlah_vol_produk_g + $jumlah_vol_produk_h;
		$total_jumlah = $jumlah_produk_a + $jumlah_produk_b + $jumlah_produk_c + $jumlah_produk_d + $jumlah_produk_e + $jumlah_produk_f + $jumlah_produk_g + $jumlah_produk_h;

		//JUMLAH BAHAN
		$jumlah_vol_boulder = $akumulasi_vol_boulder + $rak1_vol_boulder + $rak2_vol_boulder + $rak3_vol_boulder + $rak4_vol_boulder + $rak5_vol_boulder + $rak6_vol_boulder + $rak7_vol_boulder;
		$jumlah_boulder = $akumulasi_boulder + $rak1_boulder + $rak2_boulder + $rak3_boulder + $rak4_boulder + $rak5_boulder + $rak6_boulder + $rak7_boulder;

		$jumlah_akumulasi_vol_bahan = $akumulasi_vol_boulder;
		$jumlah_akumulasi_bahan = $akumulasi_boulder;
		$jumlah_vol_bahan_rak1 = $rak1_vol_boulder;
		$jumlah_bahan_rak1 = $rak1_boulder;
		$jumlah_vol_bahan_rak2 = $rak2_vol_boulder;
		$jumlah_bahan_rak2 = $rak2_boulder;
		$jumlah_vol_bahan_rak3 = $rak3_vol_boulder;
		$jumlah_bahan_rak3 = $rak3_boulder;
		$jumlah_vol_bahan_rak4 = $rak4_vol_boulder;
		$jumlah_bahan_rak4 = $rak4_boulder;
		$jumlah_vol_bahan_rak5 = $rak5_vol_boulder;
		$jumlah_bahan_rak5 = $rak5_boulder;
		$jumlah_vol_bahan_rak6 = $rak6_vol_boulder;
		$jumlah_bahan_rak6 = $rak6_boulder;
		$jumlah_vol_bahan_rak7 = $rak7_vol_boulder;
		$jumlah_bahan_rak7 = $rak7_boulder;
		$total_jumlah_vol_bahan = $jumlah_vol_boulder;
		$total_jumlah_bahan = $jumlah_boulder;

		//JUMLAH ALAT
		$jumlah_stone_crusher = $akumulasi_stone_crusher + $rak1_stone_crusher + $rak2_stone_crusher + $rak3_stone_crusher + $rak4_stone_crusher + $rak5_stone_crusher + $rak6_stone_crusher + $rak7_stone_crusher;
		$jumlah_wheel_loader = $akumulasi_wheel_loader + $rak1_wheel_loader + $rak2_wheel_loader + $rak3_wheel_loader + $rak4_wheel_loader + $rak5_wheel_loader + $rak6_wheel_loader + $rak7_wheel_loader;
		$jumlah_maintenance = $akumulasi_maintenance + $rak1_maintenance + $rak2_maintenance + $rak3_maintenance + $rak4_maintenance + $rak5_maintenance + $rak6_maintenance + $rak7_maintenance;
		$jumlah_bbm_solar = $akumulasi_bbm_solar + $rak1_bbm_solar + $rak2_bbm_solar + $rak3_bbm_solar + $rak4_bbm_solar + $rak5_bbm_solar + $rak6_bbm_solar + $rak7_bbm_solar;
		$jumlah_tangki = $akumulasi_tangki + $rak1_tangki + $rak2_tangki + $rak3_tangki + $rak4_tangki + $rak5_tangki + $rak6_tangki + $rak7_tangki;
		$jumlah_timbangan = $akumulasi_timbangan + $rak1_timbangan + $rak2_timbangan + $rak3_timbangan + $rak4_timbangan + $rak5_timbangan + $rak6_timbangan + $rak7_timbangan;

		$jumlah_akumulasi_alat = $akumulasi_stone_crusher + $akumulasi_wheel_loader + $akumulasi_maintenance + $akumulasi_bbm_solar + $akumulasi_tangki + $akumulasi_timbangan;
		$jumlah_alat_rak1 = $rak1_stone_crusher + $rak1_wheel_loader + $rak1_maintenance + $rak1_bbm_solar + $rak1_tangki + $rak1_timbangan;
		$jumlah_alat_rak2 = $rak2_stone_crusher + $rak2_wheel_loader + $rak2_maintenance + $rak2_bbm_solar + $rak2_tangki + $rak2_timbangan;
		$jumlah_alat_rak3 = $rak3_stone_crusher + $rak3_wheel_loader + $rak3_maintenance + $rak3_bbm_solar + $rak3_tangki + $rak3_timbangan;
		$jumlah_alat_rak4 = $rak4_stone_crusher + $rak4_wheel_loader + $rak4_maintenance + $rak4_bbm_solar + $rak4_tangki + $rak4_timbangan;
		$jumlah_alat_rak5 = $rak5_stone_crusher + $rak5_wheel_loader + $rak5_maintenance + $rak5_bbm_solar + $rak5_tangki + $rak5_timbangan;
		$jumlah_alat_rak6 = $rak6_stone_crusher + $rak6_wheel_loader + $rak6_maintenance + $rak6_bbm_solar + $rak6_tangki + $rak6_timbangan;
		$jumlah_alat_rak7 = $rak7_stone_crusher + $rak7_wheel_loader + $rak7_maintenance + $rak7_bbm_solar + $rak7_tangki + $rak7_timbangan;
		$total_jumlah_alat = $jumlah_stone_crusher + $jumlah_wheel_loader + $jumlah_maintenance + $jumlah_bbm_solar + $jumlah_tangki + $jumlah_timbangan;

		//JUMLAH OVERHEAD
		$jumlah_overhead = $akumulasi_overhead + $rak1_overhead + $rak2_overhead + $rak3_overhead + $rak4_overhead + $rak5_overhead + $rak6_overhead + $rak7_overhead;
		
		$jumlah_akumulasi_overhead = $akumulasi_overhead;
		$jumlah_overhead_rak1 = $rak1_overhead;
		$jumlah_overhead_rak2 = $rak2_overhead;
		$jumlah_overhead_rak3 = $rak3_overhead;
		$jumlah_overhead_rak4 = $rak4_overhead;
		$jumlah_overhead_rak5 = $rak5_overhead;
		$jumlah_overhead_rak6 = $rak6_overhead;
		$jumlah_overhead_rak7 = $rak7_overhead;
		$total_jumlah_overhead = $jumlah_overhead;

		$sub_akumulasi = $jumlah_akumulasi_bahan + $jumlah_akumulasi_alat + $jumlah_akumulasi_overhead;
		$sub_rak1 = $jumlah_bahan_rak1 + $jumlah_alat_rak1 + $jumlah_overhead_rak1;
		$sub_rak2 = $jumlah_bahan_rak2 + $jumlah_alat_rak2 + $jumlah_overhead_rak2;
		$sub_rak3 = $jumlah_bahan_rak3 + $jumlah_alat_rak3 + $jumlah_overhead_rak3;
		$sub_rak4 = $jumlah_bahan_rak4 + $jumlah_alat_rak4 + $jumlah_overhead_rak4;
		$sub_rak5 = $jumlah_bahan_rak5 + $jumlah_alat_rak5 + $jumlah_overhead_rak5;
		$sub_rak6 = $jumlah_bahan_rak6 + $jumlah_alat_rak6 + $jumlah_overhead_rak6;
		$sub_rak7 = $jumlah_bahan_rak7 + $jumlah_alat_rak7 + $jumlah_overhead_rak7;
		$total_sub = $total_jumlah_bahan + $total_jumlah_alat + $total_jumlah_overhead;

		$laba_akumulasi = $jumlah_akumulasi - $sub_akumulasi;
		$laba_rak1 = $jumlah_rak1 - $sub_rak1;
		$laba_rak2 = $jumlah_rak2 - $sub_rak2;
		$laba_rak3 = $jumlah_rak3 - $sub_rak3;
		$laba_rak4 = $jumlah_rak4 - $sub_rak4;
		$laba_rak5 = $jumlah_rak5 - $sub_rak5;
		$laba_rak6 = $jumlah_rak6 - $sub_rak6;
		$laba_rak7 = $jumlah_rak7 - $sub_rak7;
		$total_laba = $total_jumlah - $total_sub;
		?>
		<table width="99%" cellpadding="3" border="1">
			<tr class="table-judul">
				<th width="3%" align="center" rowspan="2">&nbsp; <br />NO.</th>
				<th width="16%" align="center" rowspan="2">&nbsp; <br />URAIAN</th>
				<th width="9%" align="center" colspan="2" style="text-transform:uppercase;">SD. <?php echo $date_2_awal = date('F Y', strtotime('-1 days', strtotime($date_1_awal)));?></th>
				<th width="9%" align="center" colspan="2" style="text-transform:uppercase;"><?php echo $date_1_awal = date('F Y');?></th>
				<th width="9%" align="center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_1_akhir)));?></th>
				<th width="9%" align="center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_2_akhir)));?></th>
				<th width="9%" align="center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_3_akhir)));?></th>
				<th width="9%" align="center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_4_akhir)));?></th>
				<th width="9%" align="center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_5_akhir)));?></th>
				<th width="9%" align="center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_6_akhir)));?></th>
				<th width="9%" align="center" colspan="2">JUMLAH</th>
	        </tr>
			<tr class="table-judul">
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
				<th align="center" ><b>VOL.</b></th>
				<th align="center" ><b>NILAI</b></th>
			</tr>
			<tr class="table-baris">
				<th align="left" colspan="20"><b>A. PENDAPATAN USAHA</b></th>
			</tr>
			<tr class="table-baris">
				<th align="center">1.</th>
				<th align="left">LPA (JLS Brantas)</th>
				<th align="right"><?php echo number_format($akumulasi_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_produk_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_produk_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_produk_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_produk_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_produk_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_produk_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_produk_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_produk_a,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_produk_a,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produk_a,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="center" rowspan="3">&nbsp; <br />2.</th>
				<th align="left">Bu Tampi - Batu Split 0,5 - 10 mm (Upah Giling)</th>
				<th align="right"><?php echo number_format($akumulasi_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_produk_g,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_produk_g,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_produk_g,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_produk_g,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_produk_g,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_produk_g,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_produk_g,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_produk_g,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_produk_g,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produk_g,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="left">Bu Tampi - Batu Boulder</th>
				<th align="right"><?php echo number_format($akumulasi_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_produk_h,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_produk_h,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_produk_h,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_produk_h,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_produk_h,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_produk_h,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_produk_h,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_produk_h,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_produk_h,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produk_h,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="left">Bu Tampi - Batu Split 0,5 - 10 mm</th>
				<th align="right"><?php echo number_format($akumulasi_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_produk_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_produk_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_produk_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_produk_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_produk_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_produk_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_produk_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_produk_b,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_produk_b,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produk_b,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="center" rowspan="4">&nbsp; <br /><br />3.</th>
				<th align="left">P. Antoni - Abu Batu</th>
				<th align="right"><?php echo number_format($akumulasi_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_produk_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_produk_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_produk_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_produk_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_produk_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_produk_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_produk_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_produk_c,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_produk_c,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produk_c,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="left">P. Antoni - Batu Split 0,5 - 10 mmu</th>
				<th align="right"><?php echo number_format($akumulasi_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_produk_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_produk_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_produk_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_produk_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_produk_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_produk_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_produk_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_produk_d,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_produk_d,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produk_d,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="left">P. Antoni - Batu Split 10 - 15 mm</th>
				<th align="right"><?php echo number_format($akumulasi_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_produk_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_produk_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_produk_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_produk_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_produk_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_produk_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_produk_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_produk_e,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_produk_e,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produk_e,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="left">P. Antoni - Batu Split 10 - 20 mm</th>
				<th align="right"><?php echo number_format($akumulasi_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_produk_f,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_produk_f,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_produk_f,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_produk_f,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_produk_f,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_produk_f,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_produk_f,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_produk_f,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_produk_f,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_produk_f,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">JUMLAH</th>
				<th align="right"><?php echo number_format($jumlah_vol_akumulasi,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_rak1,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_rak1,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_rak2,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_rak2,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_rak3,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_rak3,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_rak4,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_rak4,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_rak5,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_rak5,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_rak6,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_rak6,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_rak7,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_rak7,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_jumlah_vol,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_jumlah,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="left" colspan="20"><b>B. BIAYA BAHAN</b></th>
			</tr>
			<tr class="table-baris">
				<th align="center">1.</th>
				<th align="left">Boulder</th>
				<th align="right"><?php echo number_format($akumulasi_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($akumulasi_boulder,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak1_boulder,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak2_boulder,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak3_boulder,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak4_boulder,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak5_boulder,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak6_boulder,0,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($rak7_boulder,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_boulder,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_boulder,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">JUMLAH</th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_vol_bahan,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_bahan,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_bahan_rak1,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rak1,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_bahan_rak1,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rak2,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_bahan_rak3,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rak3,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_bahan_rak4,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rak4,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_bahan_rak5,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rak5,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_bahan_rak6,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rak6,0,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_vol_bahan_rak7,2,',','.');?></th>
				<th align="right"><?php echo number_format($jumlah_bahan_rak7,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_jumlah_vol_bahan,2,',','.');?></th>
				<th align="right"><?php echo number_format($total_jumlah_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="left" colspan="20"><b>C. BIAYA PERALATAN</b></th>
			</tr>
			<tr class="table-baris">
				<th align="center">1.</th>
				<th align="left">Stone Crusher</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($akumulasi_stone_crusher,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak1_stone_crusher,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak2_stone_crusher,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak3_stone_crusher,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak4_stone_crusher,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak5_stone_crusher,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak6_stone_crusher,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak7_stone_crusher,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_stone_crusher,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="center">2.</th>
				<th align="left">Wheel Loader</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($akumulasi_wheel_loader,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak1_wheel_loader,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak2_wheel_loader,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak3_wheel_loader,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak4_wheel_loader,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak5_wheel_loader,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak6_wheel_loader,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak7_wheel_loader,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_wheel_loader,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="center">3.</th>
				<th align="left">Maintenance</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($akumulasi_maintenance,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak1_maintenance,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak2_maintenance,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak3_maintenance,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak4_maintenance,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak5_maintenance,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak6_maintenance,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak7_maintenance,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_maintenance,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="center">4.</th>
				<th align="left">BBM Solar</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($akumulasi_bbm_solar,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak1_bbm_solar,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak2_bbm_solar,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak3_bbm_solar,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak4_bbm_solar,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak5_bbm_solar,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak6_bbm_solar,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak7_bbm_solar,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_bbm_solar,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="center">5.</th>
				<th align="left">Tangkir Solar</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($akumulasi_tangki,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak1_tangki,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak2_tangki,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak3_tangki,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak4_tangki,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak5_tangki,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak6_tangki,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak7_tangki,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_tangki,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="center">6.</th>
				<th align="left">Timbangan</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($akumulasi_timbangan,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak1_timbangan,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak2_timbangan,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak3_timbangan,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak4_timbangan,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak5_timbangan,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak6_timbangan,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak7_timbangan,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_timbangan,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">JUMLAH</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_alat,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_alat_rak1,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_alat_rak2,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_alat_rak3,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_alat_rak4,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_alat_rak5,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_alat_rak6,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_alat_rak7,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_jumlah_alat,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th align="left" colspan="20"><b>D. BIAYA OVERHEAD</b></th>
			</tr>
			<tr class="table-baris">
				<th align="center">1.</th>
				<th align="left">Overhead</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($akumulasi_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak1_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak2_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak3_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak4_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak5_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak6_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($rak7_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th align="right" colspan="2">JUMLAH</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_akumulasi_overhead,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_overhead_rak1,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_overhead_rak2,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_overhead_rak3,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_overhead_rak4,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_overhead_rak5,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_overhead_rak6,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($jumlah_overhead_rak7,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_jumlah_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-subtotal">
				<th align="right" colspan="2">TOTAL (BAHAN+ALAT+OVERHEAD)</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($sub_akumulasi,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($sub_rak1,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($sub_rak2,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($sub_rak3,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($sub_rak4,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($sub_rak5,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($sub_rak6,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($sub_rak7,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_sub,0,',','.');?></th>
			</tr>
			<tr class="table-total2">
				<th align="right" colspan="2">LABA</th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($laba_akumulasi,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($laba_rak1,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($laba_rak2,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($laba_rak3,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($laba_rak4,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($laba_rak5,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($laba_rak6,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($laba_rak7,0,',','.');?></th>
				<th align="right"></th>
				<th align="right"><?php echo number_format($total_laba,0,',','.');?></th>
			</tr>
		</table>
	</body>
</html>