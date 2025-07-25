<!DOCTYPE html>
<html>
	<head>
	  <title>LAPORAN PIUTANG</title>
	  
	  <style type="text/css">
		body {
			font-family: helvetica;
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

		table tr.table-baris2-bold{
			font-size: 7px;
			background-color: #E8E8E8;
			font-weight: bold;
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
		<table width="98%" border="0" cellpadding="15">
			<tr>
				<td width="100%" align="center">
					<div style="display: block;font-weight: bold;font-size: 11px;">Laporan Piutang</div>
				    <div style="display: block;font-weight: bold;font-size: 11px;">Divisi Stone Crusher</div>
					<?php
					function tgl_indo($date2){
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
						$pecahkan = explode('-', $date2);
						
						// variabel pecahkan 0 = tanggal
						// variabel pecahkan 1 = bulan
						// variabel pecahkan 2 = tahun
					
						return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
						
					}
					?>
					<div style="display: block;font-weight: bold;font-size: 11px;">Per <?= tgl_indo(date($date2)); ?></div>
				</td>
			</tr>
		</table>	
		<table cellpadding="4" width="98%">
			<tr class="table-judul">
				<th width="5%" align="center" rowspan="2">&nbsp; <br />NO.</th>
				<th width="26%" align="center" rowspan="2">&nbsp; <br />REKANAN</th>
				<th width="11%" align="center" rowspan="2">&nbsp; <br />PENJUALAN</th>
				<th width="11%" align="center" rowspan="2">&nbsp; <br />TAGIHAN</th>
				<th width="10%" align="center" rowspan="2">&nbsp; <br />TAGIHAN BRUTO</th>
				<th width="12%" align="center" rowspan="2">&nbsp; <br />PENERIMAAN</th>
				<th width="25%" align="center" colspan="2">SISA PIUTANG</th>
			</tr>
			<tr class="table-judul">
				<th align="center">PENJUALAN</th>
				<th align="center">TAGIHAN</th>
			</tr>			
            <?php
			$i=0;
            if(!empty($data)){
            	foreach ($data as $key => $row) :
            	$i++;
				$bg=($i%2==0?'#F0F0F0':'#E8E8E8') ?>   
            		<!--<tr class="table-baris1-bold">
            			<td align="center"><?php echo $key + 1;?></td>
            			<td align="left" colspan="7"><?php echo $row['name'];?></td>
            		</tr>-->
					<?php
					$jumlah_penerimaan = 0;
					$jumlah_tagihan = 0;
					$jumlah_tagihan_bruto = 0;
					$jumlah_pembayaran = 0;
					$jumlah_sisa_piutang_penerimaan = 0;
					$jumlah_sisa_piutang_tagihan = 0;
            		foreach ($row['mats'] as $mat) {
            			?>
						<!--<tr class="table-baris1-bold">
            			<td align="center"></td>
            			<td align="left"></td>
            			<td align="right"><?php echo $mat['penerimaan'];?></td>
						<td align="right"><?php echo $mat['tagihan'];?></td>
						<td align="right"><?php echo $mat['tagihan_bruto'];?></td>
						<td align="right"><?php echo $mat['pembayaran'];?></td>
						<td align="right"><?php echo $mat['sisa_piutang_penerimaan'];?></td>
						<td align="right"><?php echo $mat['sisa_piutang_tagihan'];?></td>
            		</tr>-->

					<?php
					$jumlah_penerimaan += str_replace(['.', ','], ['', '.'], $mat['penerimaan']);
					$jumlah_tagihan += str_replace(['.', ','], ['', '.'], $mat['tagihan']);
					$jumlah_tagihan_bruto += str_replace(['.', ','], ['', '.'], $mat['tagihan_bruto']);
					$jumlah_pembayaran += str_replace(['.', ','], ['', '.'], $mat['pembayaran']);
					$jumlah_sisa_piutang_penerimaan += str_replace(['.', ','], ['', '.'], $mat['sisa_piutang_penerimaan']);
					$jumlah_sisa_piutang_tagihan += str_replace(['.', ','], ['', '.'], $mat['sisa_piutang_tagihan']);
					}	
					?>
					<tr class="table-baris2" style="background-color:<?php echo $bg; ?>;">
						<td align="center"><?php echo $key + 1;?></td>
						<td align="left"><?php echo $row['name'];?></td>
						<td align="right"><?php echo number_format($jumlah_penerimaan,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_tagihan,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_tagihan_bruto,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_pembayaran,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_sisa_piutang_penerimaan,0,',','.');?></td>
						<td align="right"><?php echo number_format($jumlah_sisa_piutang_tagihan,0,',','.');?></td>
            		</tr>
					<?php
            		endforeach;
            }else {
            	?>
            	<tr>
            		<td width="100%" colspan="8" align="center">Tidak Ada Data</td>
            	</tr>
            	<?php
            }
            ?>
            <tr class="table-total">
				<th align="right" colspan="2">TOTAL</th>
				<th align="right"><?php echo number_format($total_penerimaan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_tagihan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_tagihan_bruto,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_pembayaran,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_sisa_piutang_penerimaan,0,',','.');?></th>
				<th align="right"><?php echo number_format($total_sisa_piutang_tagihan,0,',','.');?></th>
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
								<img src="uploads/ttd_rifka.png" width="30px">
							</td>
							<td align="center">
								<img src="uploads/ttd_satria.png" width="30px">
							</td>
							<td align="center">
								<img src="uploads/ttd_satria.png" width="30px">
							</td>
						</tr>
						<tr>
							<td align="center">
								<b><u>Rifka Dian Bethary</u><br />
								Pj. Keuangan & SDM</b>
							</td>
							<td align="center">
								<b><u>Satria Widura Drana Wisesa</u><br />
								Ka. Unit Bisnis</b>
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