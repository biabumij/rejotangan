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

?>
    