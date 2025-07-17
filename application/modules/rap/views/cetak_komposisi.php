<!DOCTYPE html>
<html>
	<head>
	  <?= include 'lib.php'; ?>
	  <title>RAP BAHAN</title>
	  
	  <style type="text/css">
	  	body{
		  font-family: helvetica;
		  font-size: 7.5px;
	  	}
	  	table.minimalistBlack {
		  border: 0px solid #000000;
		  width: 100%;
		  text-align: left;
		}
		table.minimalistBlack td, table.minimalistBlack th {
		  border: 1px solid #000000;
		  padding: 5px 4px;
		}
		table.minimalistBlack tr td {
		  text-align:center;
		}
		table.minimalistBlack tr th {
		  font-weight: bold;
		  color: #000000;
		  text-align: center;
		  padding: 10px;
		}
		table.head tr th {
		  font-weight: bold;
		  color: #000000;
		  text-align: left;
		  padding: 10px;
		}
		table tr.table-active{
            background-color: #9d7a10;
        }
        table tr.table-active2{
            background-color: #cac8c8;
        }
		table tr.table-active3{
            background-color: #eee;
        }
		hr{
			margin-top:0;
			margin-bottom:30px;
		}
		h3{
			margin-top:0;
		}
	  </style>

	</head>
	<body>
		<table width="100%" border="0" cellpadding="3">
			<tr>
				<td align="center">
					<div style="display: block;font-weight: bold;font-size: 10px;">RAP BAHAN</div>
					<div style="display: block;font-weight: bold;font-size: 10px; text-transform:uppercase;">DIVISI STONE CRUSHER</div>
				</td>
			</tr>
		</table>
		<br /><br />
		<table class="head" width="100%" border="0" cellpadding="3">
			<tr>
				<th width="20%">JENIS PEKERJAAN</th>
				<th width="2%">:</th>
				<th align="left"><div style="text-transform:uppercase;"><?php echo $row['jobs_type'];?></div></th>
			</tr>
			<tr>
				<th width="20%">SATUAAN PEKERJAAN</th>
				<th width="2%">:</th>
				<th align="left"><?php echo $row['measure'] = $this->crud_global->GetField('pmm_measures',array('id'=>$row['measure']),'measure_name');?></th>
			</tr>
		</table>
		<br />
		<br />
		<table class="minimalistBlack" cellpadding="3" width="98%">
			<tr class="table-active">
				<th align="center" rowspan="2" width="5%">&nbsp; <br />NO.</th>
				<th align="center" rowspan="2" width="23%">&nbsp; <br />URAIAN</th>
				<th align="center" rowspan="2" width="20%">&nbsp; <br />VOLUME</th>
				<th align="center" rowspan="2" width="12%">&nbsp; <br />SATUAN</th>
				<th align="center" rowspan="2" width="20%">&nbsp; <br />HARGA SATUAN</th>
				<th align="center" width="20%" colspan="2">&nbsp; <br />JUMLAH HARGA</th>
            </tr>
			<tr class="table-active">
				<th align="center">(%)</th>
				<th align="center">(Rp.)</th>
			</tr>
			<tr>
				<td align="left" colspan="7"><i><b>BEBAN POKOK PRODUKSI</i></b></td>
			</tr>
			<tr>
				<td align="center"><b>A.</b></td>
				<td align="left" colspan="6"><b>BAHAN</b></td>
			</tr>
			<tr>
				<?php
				$total_bahan = $row['total_a'];
				?>
				
				<?php
				$row_alat = $this->db->select('*')
				->from('rap_alat')
				->where('id',$row['rap_alat'])
				->get()->row_array();

				$total_alat = $row_alat['stone_crusher'] + $row_alat['wheel_loader'] + $row_alat['maintenance'] + $row_alat['bbm_solar'];
				$total_bua = 96774;
				$total_persiapan = 67496;
				$sub_total = $total_bahan + $total_alat + $total_bua + $total_persiapan;
				
				$total_diskonto = $harga_jual * 3 /100;
				$total = $sub_total + $total_diskonto;
				?>

				<?php
				$row_bua = $this->db->select('*')
				->from('rap_bua')
				->where('id',$row['id'])
				->get()->row_array();
				$total_bua = ($row_bua['total'] / 6) /5000;
				?>

				<?php
				$total = $total_bahan + $total_alat + $total_bua;
				?>
				<td align="center">1.</td>
				<td align="left"><?= $row['produk_a'] = $this->crud_global->GetField('produk',array('id'=>$row['produk_a']),'nama_produk'); ?></td>
				<td align="center"><?php echo number_format($row['volume'],4,',','.');?></td>
				<td align="center"><?= $row['measure_a']  = $this->crud_global->GetField('pmm_measures',array('id'=>$row['measure_a']),'measure_name'); ?></td>
				<td align="right"><?php echo number_format($row['price_a'],2,',','.');?></td>
				<td align="right"></td>
				<td align="right"><?php echo number_format($row['total_a'],2,',','.');?></td>
			</tr>
				
			<tr>
				<td align="right" colspan="5"><b>JUMLAH HARGA BAHAN</b></td>
				<td align="right"><b><?php echo number_format(($total_bahan / $total) * 100,2,',','.');?>%</b></td>
				<td align="right"><b><?php echo number_format($total_bahan,2,',','.');?></b></td>
			</tr>
			<tr>
				<td align="center"><b>B.</b></td>
				<td align="left" colspan="6"><b>PERALATAN</b></td>
			</tr>
			<tr>
				<td align="center">1.</td>
				<td align="left">Stone Crusher + Genset</td>
				<td align="center"><?php echo number_format($row_alat['vol_stone_crusher'],4,',','.');?></td>
				<td align="center">M3</td>
				<td align="right"><?php echo number_format($row_alat['harsat_stone_crusher'],2,',','.');?></td>
				<td align="right"></td>
				<td align="right"><?php echo number_format($row_alat['stone_crusher'],2,',','.');?></td>
			</tr>
			<tr>
				<td align="center">2.</td>
				<td align="left">Wheel Loader</td>
				<td align="center"><?php echo number_format($row_alat['vol_wheel_loader'],4,',','.');?></td>
				<td align="center">M3</td>
				<td align="right"><?php echo number_format($row_alat['harsat_wheel_loader'],2,',','.');?></td>
				<td align="right"></td>
				<td align="right"><?php echo number_format($row_alat['wheel_loader'],2,',','.');?></td>
			</tr>
			<tr>
				<td align="center">3.</td>
				<td align="left">Maintenance</td>
				<td align="center"><?php echo number_format($row_alat['vol_maintenance'],4,',','.');?></td>
				<td align="center">M3</td>
				<td align="right"><?php echo number_format($row_alat['harsat_maintenance'],2,',','.');?></td>
				<td align="right"></td>
				<td align="right"><?php echo number_format($row_alat['maintenance'],2,',','.');?></td>
			</tr>
			<tr>
				<td align="center">4.</td>
				<td align="left">BBM Solar</td>
				<td align="center"><?php echo number_format($row_alat['vol_bbm_solar'],4,',','.');?></td>
				<td align="center">Liter</td>
				<td align="right"><?php echo number_format($row_alat['harsat_bbm_solar'],2,',','.');?></td>
				<td align="right"></td>
				<td align="right"><?php echo number_format($row_alat['bbm_solar'],2,',','.');?></td>
			</tr>
			<tr>
				<td align="right" colspan="5"><b>JUMLAH HARGA PERALATAN</b></td>
				<td align="right"><b><?php echo number_format(($total_alat / $total) * 100,2,',','.');?>%</b></td>
				<td align="right"><b><?php echo number_format($total_alat,2,',','.');?></b></td>
			</tr>
			
			<tr>
				<td align="center"><b>C.</b></td>
				<td align="left" colspan="6"><b>BUA</b></td>
			</tr>
			<tr>
				<td align="center">1.</td>
				<td align="left">BUA</td>
				<td align="center"><?php echo number_format(1,4,',','.');?></td>
				<td align="center">Bulan</td>
				<td align="right"><?php echo number_format($total_bua,2,',','.');?></td>
				<td align="right"></td>
				<td align="right"><?php echo number_format($total_bua,2,',','.');?></td>
			</tr>
			<tr>
				<td align="right" colspan="5"><b>JUMLAH BUA</b></td>
				<td align="right"><b><?php echo number_format(($total_bua / $total) * 100,2,',','.');?>%</b></td>
				<td align="right"><b><?php echo number_format($total_bua,2,',','.');?></b></td>
			</tr>
			
			<tr>
				<td align="right" colspan="5"><b>JUMLAH HARGA POKOK PENJUALAN (A+B+C)</b></td>
				<td align="right"></td>
				<td align="right"><b><?php echo number_format($total,2,',','.');?></b></td>
			</tr>
		</table>
	</body>
</html>