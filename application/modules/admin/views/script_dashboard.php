<?php
function tgl_indo($date_minggu_1_awal){
	$bulan = array (
		1 =>   'Januari',
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
	$pecahkan = explode('-', $date_minggu_1_awal);
	
	// variabel pecahkan 0 = tanggal
	// variabel pecahkan 1 = bulan
	// variabel pecahkan 2 = tahun
 
	return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

$date_now = date('Y-m-d');
$date_januari25_awal = date('2025-01-01');
$date_januari25_akhir = date('2025-01-31');
$date_februari25_awal = date('2025-02-01');
$date_februari25_akhir = date('2025-02-28');
$date_maret25_awal = date('2025-03-01');
$date_maret25_akhir = date('2025-03-31');
$date_april25_awal = date('2025-04-01');
$date_april25_akhir = date('2025-04-30');
$date_mei25_awal = date('2025-05-01');
$date_mei25_akhir = date('2025-05-31');
$date_juni25_awal = date('2025-06-01');
$date_juni25_akhir = date('2025-06-30');
$date_juli25_awal = date('2025-07-01');
$date_juli25_akhir = date('2025-07-31');
$date_agustus25_awal = date('2025-08-01');
$date_agustus25_akhir = date('2025-08-31');
$date_september25_awal = date('2025-09-01');
$date_september25_akhir = date('2025-09-30');
$date_oktober25_awal = date('2025-10-01');
$date_oktober25_akhir = date('2025-10-31');
$date_november25_awal = date('2025-11-01');
$date_november25_akhir = date('2025-11-30');
$date_desember25_awal = date('2025-12-01');
$date_desember25_akhir = date('2025-12-31');

//JULI25
$rak_juli25 = $this->db->select('(r.vol_produk_a + r.vol_produk_b + r.vol_produk_c + r.vol_produk_d + r.vol_produk_e + r.vol_produk_f + r.vol_produk_g + r.vol_produk_h + r.vol_produk_i) as total_produksi')
->from('rak r')
->where("r.tanggal_rencana_kerja between '$date_juli25_awal' and '$date_juli25_akhir'")
->get()->row_array();
file_put_contents("c:/users/Dell/Desktop/test.txt", $this->db->last_query());
$rencana_produksi_juli25 = $rak_juli25['total_produksi'];

$penjualan_juli25 = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
->from('pmm_productions pp')
->join('penerima p', 'pp.client_id = p.id','left')
->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
->where("pp.date_production between '$date_juli25_awal' and '$date_juli25_akhir'")
->where("pp.status = 'PUBLISH'")
->where("ppo.status in ('OPEN','CLOSED')")
->group_by("pp.client_id")
->get()->result_array();
$total_volume_penjualan_juli25 = 0;
foreach ($penjualan_juli25 as $x){
    $total_volume_penjualan_juli25 += $x['volume'];
}
$realisasi_produksi_juli25 = $total_volume_penjualan_juli25;

//AGUSTUS25
$rak_agustus25 = $this->db->select('(r.vol_produk_a + r.vol_produk_b + r.vol_produk_c + r.vol_produk_d + r.vol_produk_e + r.vol_produk_f + r.vol_produk_g + r.vol_produk_h + r.vol_produk_i) as total_produksi')
->from('rak r')
->where("r.tanggal_rencana_kerja between '$date_agustus25_awal' and '$date_agustus25_akhir'")
->get()->row_array();
file_put_contents("c:/users/Dell/Desktop/test.txt", $this->db->last_query());
$rencana_produksi_agustus25 = $rak_agustus25['total_produksi'];

$penjualan_agustus25 = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
->from('pmm_productions pp')
->join('penerima p', 'pp.client_id = p.id','left')
->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
->where("pp.date_production between '$date_agustus25_awal' and '$date_agustus25_akhir'")
->where("pp.status = 'PUBLISH'")
->where("ppo.status in ('OPEN','CLOSED')")
->group_by("pp.client_id")
->get()->result_array();
$total_volume_penjualan_agustus25 = 0;
foreach ($penjualan_agustus25 as $x){
    $total_volume_penjualan_agustus25 += $x['volume'];
}
$realisasi_produksi_agustus25 = $total_volume_penjualan_agustus25;

//SEPTEMBER25
$rak_september25 = $this->db->select('(r.vol_produk_a + r.vol_produk_b + r.vol_produk_c + r.vol_produk_d + r.vol_produk_e + r.vol_produk_f + r.vol_produk_g + r.vol_produk_h + r.vol_produk_i) as total_produksi')
->from('rak r')
->where("r.tanggal_rencana_kerja between '$date_september25_awal' and '$date_september25_akhir'")
->get()->row_array();
file_put_contents("c:/users/Dell/Desktop/test.txt", $this->db->last_query());
$rencana_produksi_september25 = $rak_september25['total_produksi'];

$penjualan_september25 = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
->from('pmm_productions pp')
->join('penerima p', 'pp.client_id = p.id','left')
->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
->where("pp.date_production between '$date_september25_awal' and '$date_september25_akhir'")
->where("pp.status = 'PUBLISH'")
->where("ppo.status in ('OPEN','CLOSED')")
->group_by("pp.client_id")
->get()->result_array();
$total_volume_penjualan_september25 = 0;
foreach ($penjualan_september25 as $x){
    $total_volume_penjualan_september25 += $x['volume'];
}
$realisasi_produksi_september25 = $total_volume_penjualan_september25;

//OKTOBER25
$rak_oktober25 = $this->db->select('(r.vol_produk_a + r.vol_produk_b + r.vol_produk_c + r.vol_produk_d + r.vol_produk_e + r.vol_produk_f + r.vol_produk_g + r.vol_produk_h + r.vol_produk_i) as total_produksi')
->from('rak r')
->where("r.tanggal_rencana_kerja between '$date_oktober25_awal' and '$date_oktober25_akhir'")
->get()->row_array();
file_put_contents("c:/users/Dell/Desktop/test.txt", $this->db->last_query());
$rencana_produksi_oktober25 = $rak_oktober25['total_produksi'];

$penjualan_oktober25 = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
->from('pmm_productions pp')
->join('penerima p', 'pp.client_id = p.id','left')
->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
->where("pp.date_production between '$date_oktober25_awal' and '$date_oktober25_akhir'")
->where("pp.status = 'PUBLISH'")
->where("ppo.status in ('OPEN','CLOSED')")
->group_by("pp.client_id")
->get()->result_array();
$total_volume_penjualan_oktober25 = 0;
foreach ($penjualan_oktober25 as $x){
    $total_volume_penjualan_oktober25 += $x['volume'];
}
$realisasi_produksi_oktober25 = $total_volume_penjualan_oktober25;

//NOVEMBER25
$rak_november25 = $this->db->select('(r.vol_produk_a + r.vol_produk_b + r.vol_produk_c + r.vol_produk_d + r.vol_produk_e + r.vol_produk_f + r.vol_produk_g + r.vol_produk_h + r.vol_produk_i) as total_produksi')
->from('rak r')
->where("r.tanggal_rencana_kerja between '$date_november25_awal' and '$date_november25_akhir'")
->get()->row_array();
file_put_contents("c:/users/Dell/Desktop/test.txt", $this->db->last_query());
$rencana_produksi_november25 = $rak_november25['total_produksi'];

$penjualan_november25 = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
->from('pmm_productions pp')
->join('penerima p', 'pp.client_id = p.id','left')
->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
->where("pp.date_production between '$date_november25_awal' and '$date_november25_akhir'")
->where("pp.status = 'PUBLISH'")
->where("ppo.status in ('OPEN','CLOSED')")
->group_by("pp.client_id")
->get()->result_array();
$total_volume_penjualan_november25 = 0;
foreach ($penjualan_november25 as $x){
    $total_volume_penjualan_november25 += $x['volume'];
}
$realisasi_produksi_november25 = $total_volume_penjualan_november25;

//DESEMBER25
$rak_desember25 = $this->db->select('(r.vol_produk_a + r.vol_produk_b + r.vol_produk_c + r.vol_produk_d + r.vol_produk_e + r.vol_produk_f + r.vol_produk_g + r.vol_produk_h + r.vol_produk_i) as total_produksi')
->from('rak r')
->where("r.tanggal_rencana_kerja between '$date_desember25_awal' and '$date_desember25_akhir'")
->get()->row_array();
file_put_contents("c:/users/Dell/Desktop/test.txt", $this->db->last_query());
$rencana_produksi_desember25 = $rak_desember25['total_produksi'];

$penjualan_desember25 = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
->from('pmm_productions pp')
->join('penerima p', 'pp.client_id = p.id','left')
->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
->where("pp.date_production between '$date_desember25_awal' and '$date_desember25_akhir'")
->where("pp.status = 'PUBLISH'")
->where("ppo.status in ('OPEN','CLOSED')")
->group_by("pp.client_id")
->get()->result_array();
$total_volume_penjualan_desember25 = 0;
foreach ($penjualan_desember25 as $x){
    $total_volume_penjualan_desember25 += $x['volume'];
}
$realisasi_produksi_desember25 = $total_volume_penjualan_desember25;

?>
