<!doctype html>
<html lang="en" class="fixed">
<head>
    <?php echo $this->Templates->Header();?>

    <style type="text/css">
        body{
			font-family: helvetica;
	  	}
    </style>
</head>

<body>
    <div class="wrap">
	<?php echo $this->Templates->PageHeader();?>
	<div class="page-body">
		<div class="content">
			<div class="row animated fadeInUp">
				<div class="col-sm-12 col-lg-12">
					<div class="panel">
						<div class="panel-header"> 
                                <div class="">
                                    <h3 class="">Kalibrasi Baru</h3>
                                    
                                </div>
                            </div>
                            <div class="panel-content">
                                <form method="POST" action="<?php echo site_url('produksi/submit_kalibrasi');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                    <div class="row">
										<div class="col-sm-2">
                                            <label>Judul</label>
                                        </div>
										<div class="col-sm-6">
                                            <input type="text" class="form-control" name="jobs_type" required="" />
                                        </div>
										<br />
										<br />
                                        <div class="col-sm-2">
                                            <label>Tanggal Kalibrasi</label>
                                        </div>
										 <div class="col-sm-2">
                                            <input type="text" class="form-control dtpicker" name="date_kalibrasi" required="" value="" />
                                        </div>
										<br />
										<br />
                                        <div class="col-sm-2">
                                            <label>Nomor Kalibrasi</label>
                                        </div>
										<div class="col-sm-6">
                                            <input type="text" class="form-control" name="no_kalibrasi" required="" value="<?= $this->pmm_model->GetNoKalibrasi();?>">
                                        </div> 										
                                    <br />                               
                                    </div>
                                    <br />
                                    <div class="table-responsive">
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">No</th>
                                                    <th width="35%" class="text-center">Fraksi/Aggregat</th>
                                                    <th width="30%" class="text-center">Satuan</th>
													<th width="30%" class="text-center">Presentase</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 <tr>

                                                    <td class="text-center">1.</td>
                                                    <td>Batu Split 0 - 0,5 (Abu Batu)</td>
                                                    <input type="hidden"  name="produk_a" class="form-control" value="7"/>
                                                    <!--<td>														
                                                        <select id="produk_a" class="form-control form-select2" name="produk_a" required="" >
                                                            <option value="">Pilih Produk</option>
                                                            <?php
                                                            if(!empty($products)){
                                                                foreach ($products as $row) {
                                                                    ?>
                                                                    <option value="<?php echo $row['id'];?>"><?php echo $row['nama_produk'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
                                                    <td class="text-center">Ton</td>
                                                    <input type="hidden"  name="measure_a" class="form-control" value="4"/>
                                                    <!--<td>
                                                        <select id="measure_a" class="form-control form-select2" name="measure_a" required="" >
                                                            <option value="">Pilih Satuan</option>
                                                            <?php
                                                            if(!empty($measures)){
                                                                foreach ($measures as $ms) {
                                                                    ?>
                                                                    <option value="<?php echo $ms['id'];?>"><?php echo $ms['measure_name'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
													<td>
                                                        <input type="number" step=".01" min="0" name="presentase_a" id="presentase_a" class="form-control input-sm text-center numberformat" onkeyup="sum();" required=""/>
                                                    </td>
                                                </tr>
												<tr>
                                                    <td class="text-center">2.</td>
                                                    <td>Batu Split 0,5 - 1</td>
                                                    <input type="hidden"  name="produk_b" class="form-control" value="8"/>
                                                    <!--<td>														
                                                        <select id="produk_b" class="form-control form-select2" name="produk_b" required="" >
                                                            <option value="">Pilih Produk</option>
                                                            <?php
                                                            if(!empty($products)){
                                                                foreach ($products as $row) {
                                                                    ?>
                                                                    <option value="<?php echo $row['id'];?>"><?php echo $row['nama_produk'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
                                                    <td class="text-center">Ton</td>
                                                    <input type="hidden"  name="measure_b" class="form-control" value="4"/>
                                                    <!--<td>
                                                        <select id="measure_b" class="form-control form-select2" name="measure_b" required="" >
                                                            <option value="">Pilih Satuan</option>
                                                            <?php
                                                            if(!empty($measures)){
                                                                foreach ($measures as $ms) {
                                                                    ?>
                                                                    <option value="<?php echo $ms['id'];?>"><?php echo $ms['measure_name'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
													<td>
                                                        <input type="number" step=".01" min="0" name="presentase_b" id="presentase_b" class="form-control input-sm text-center numberformat" onkeyup="sum();" required=""/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">3.</td>
                                                    <td>Batu Split 1 - 1,5</td>
                                                    <input type="hidden"  name="produk_f" class="form-control" value="63"/>
                                                    <!--<td>														
                                                        <select id="produk_f" class="form-control form-select2" name="produk_f" required="" >
                                                            <option value="">Pilih Produk</option>
                                                            <?php
                                                            if(!empty($products)){
                                                                foreach ($products as $row) {
                                                                    ?>
                                                                    <option value="<?php echo $row['id'];?>"><?php echo $row['nama_produk'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
                                                    <td class="text-center">Ton</td>
                                                    <input type="hidden"  name="measure_f" class="form-control" value="4"/>
                                                    <!--<td>
                                                        <select id="measure_f" class="form-control form-select2" name="measure_f" required="" >
                                                            <option value="">Pilih Satuan</option>
                                                            <?php
                                                            if(!empty($measures)){
                                                                foreach ($measures as $ms) {
                                                                    ?>
                                                                    <option value="<?php echo $ms['id'];?>"><?php echo $ms['measure_name'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
													<td>
                                                        <input type="number" step=".01" min="0" name="presentase_f" id="presentase_f" class="form-control input-sm text-center numberformat" onkeyup="sum();" required=""/>
                                                    </td>
                                                </tr>
												<tr>
                                                    <td class="text-center">4.</td>
                                                    <td>Batu Split 1 - 2</td>
                                                    <input type="hidden"  name="produk_c" class="form-control" value="3"/>
                                                    <!--<td>														
                                                        <select id="produk_c" class="form-control form-select2" name="produk_c" required="" >
                                                            <option value="">Pilih Produk</option>
                                                            <?php
                                                            if(!empty($products)){
                                                                foreach ($products as $row) {
                                                                    ?>
                                                                    <option value="<?php echo $row['id'];?>"><?php echo $row['nama_produk'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
                                                    <td class="text-center">Ton</td>
                                                    <input type="hidden"  name="measure_c" class="form-control" value="4"/>
                                                    <!--<td>
                                                        <select id="measure_c" class="form-control form-select2" name="measure_c" required="" >
                                                            <option value="">Pilih Satuan</option>
                                                            <?php
                                                            if(!empty($measures)){
                                                                foreach ($measures as $ms) {
                                                                    ?>
                                                                    <option value="<?php echo $ms['id'];?>"><?php echo $ms['measure_name'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
													<td>
                                                        <input type="number" step=".01" min="0" name="presentase_c" id="presentase_c" class="form-control input-sm text-center numberformat" onkeyup="sum();" required=""/>
                                                    </td>
                                                </tr>
												<tr>
                                                    <td class="text-center">5.</td>
                                                    <td>Batu Split 2 - 3</td>
                                                    <input type="hidden"  name="produk_d" class="form-control" value="4"/>
                                                    <!--<td>														
                                                        <select id="produk_d" class="form-control form-select2" name="produk_d" required="" >
                                                            <option value="">Pilih Produk</option>
                                                            <?php
                                                            if(!empty($products)){
                                                                foreach ($products as $row) {
                                                                    ?>
                                                                    <option value="<?php echo $row['id'];?>"><?php echo $row['nama_produk'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
                                                    <td class="text-center">Ton</td>
                                                    <input type="hidden"  name="measure_d" class="form-control" value="4"/>
                                                    <!--<td>
                                                        <select id="measure_d" class="form-control form-select2" name="measure_d" required="" >
                                                            <option value="">Pilih Satuan</option>
                                                            <?php
                                                            if(!empty($measures)){
                                                                foreach ($measures as $ms) {
                                                                    ?>
                                                                    <option value="<?php echo $ms['id'];?>"><?php echo $ms['measure_name'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
													<td>
                                                        <input type="number" step=".01" min="0" name="presentase_d" id="presentase_d" class="form-control input-sm text-center numberformat" onkeyup="sum();" required=""/>
                                                    </td>
                                                </tr>
												<tr>
                                                    <td class="text-center">6.</td>
                                                    <td>Limbah</td>
                                                    <input type="hidden"  name="produk_e" class="form-control" value="9"/>
                                                    <!--<td>														
                                                        <select id="produk_e" class="form-control form-select2" name="produk_e" required="" >
                                                            <option value="">Pilih Produk</option>
                                                            <?php
                                                            if(!empty($products)){
                                                                foreach ($products as $row) {
                                                                    ?>
                                                                    <option value="<?php echo $row['id'];?>"><?php echo $row['nama_produk'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
                                                    <td class="text-center">Ton</td>
                                                    <input type="hidden"  name="measure_e" class="form-control" value="4"/>
                                                    <!--<td>
                                                        <select id="measure_e" class="form-control form-select2" name="measure_e" required="" >
                                                            <option value="">Pilih Satuan</option>
                                                            <?php
                                                            if(!empty($measures)){
                                                                foreach ($measures as $ms) {
                                                                    ?>
                                                                    <option value="<?php echo $ms['id'];?>"><?php echo $ms['measure_name'];?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>-->
													<td>
                                                        <input type="number" step=".01" min="0" name="presentase_e" id="presentase_e" class="form-control input-sm text-center numberformat" onkeyup="sum();" required="" />
                                                    </td>
                                                </tr>
												<tr>
                                                    <td colspan="3"><b>TOTAL</b></td>														                                               
													<td>
                                                        <input type="number" step=".01" min="0" name="total_presentase" id="total_presentase" class="form-control input-sm text-center numberformat" readonly=""/>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>    
                                    </div>
									<br />
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Keterangan</label>
                                            <textarea class="form-control" name="memo" data-required="false" id="about_text">

                                            </textarea>
                                             <br />
                                        </div>
                                    </div>
                                    <br />
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label>Lampiran</label>
                                            <input type="file" class="form-control" name="files[]"  multiple="" />
                                        </div>
                                        <div class="col-sm-8 form-horizontal">
                                            <input type="hidden" name="total_product" id="total-product" value="1">
                                        </div>
                                        <br />
                                        <br />
                                    </div>
                                    <div class="row">                                            <br />
                                        <div class="col-sm-12 text-center">
                                            <a href="<?= site_url('admin/produksi');?>" class="btn btn-danger" style="margin-bottom:0; font-weight:bold; border-radius:10px;"><i class="fa fa-close"></i> Batal</a>
                                            <button type="submit" class="btn btn-success" style="font-weight:bold; border-radius:10px;"><i class="fa fa-send"></i> Kirim</button>
                                            <br />
                                            <br />
                                        </div>
                                    </div>
								</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <script type="text/javascript">
        var form_control = '';
    </script>
    <?php echo $this->Templates->Footer();?>

    <script src="<?php echo base_url();?>assets/back/theme/vendor/jquery.number.min.js"></script>
    
    <script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
    <script src="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
   
    <script src="<?php echo base_url();?>assets/back/theme/vendor/bootbox.min.js"></script>

    

    <script type="text/javascript">
        
        $('.form-select2').select2();

        $('input.numberformat').number( true, 2,',','.' );
        tinymce.init({
          selector: 'textarea#about_text',
          height: 200,
          menubar: false,
        });
        $('.dtpicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns : true,
            locale: {
              format: 'DD-MM-YYYY'
            }
        });
        $('.dtpicker').on('apply.daterangepicker', function(ev, picker) {
              $(this).val(picker.startDate.format('DD-MM-YYYY'));
              // table.ajax.reload();
        });



        $('#form-po').submit(function(e){
            e.preventDefault();
            var currentForm = this;
            bootbox.confirm({
                message: "Apakah anda yakin untuk proses data ini ?",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if(result){
                        currentForm.submit();
                    }
                    
                }
            });
            
        });
		
		$(document).ready(function() {
            setTimeout(function(){
                $('#produk_a').prop('selectedIndex', 3).trigger('change');
            }, 1000);
        });
		$(document).ready(function() {
            setTimeout(function(){
                $('#produk_b').prop('selectedIndex', 4).trigger('change');
            }, 1000);
        });
		$(document).ready(function() {
            setTimeout(function(){
                $('#produk_c').prop('selectedIndex', 1).trigger('change');
            }, 1000);
        });
		$(document).ready(function() {
            setTimeout(function(){
                $('#produk_d').prop('selectedIndex', 2).trigger('change');
            }, 1000);
        });
		$(document).ready(function() {
            setTimeout(function(){
                $('#produk_e').prop('selectedIndex', 5).trigger('change');
            }, 1000);
        });	
        $(document).ready(function() {
            setTimeout(function(){
                $('#produk_f').prop('selectedIndex', 13).trigger('change');
            }, 1000);
        });	
		$(document).ready(function() {
            setTimeout(function(){
                $('#measure_a').prop('selectedIndex', 3).trigger('change');
            }, 1000);
        });
		$(document).ready(function() {
            setTimeout(function(){
                $('#measure_b').prop('selectedIndex', 3).trigger('change');
            }, 1000);
        });
		$(document).ready(function() {
            setTimeout(function(){
                $('#measure_c').prop('selectedIndex', 3).trigger('change');
            }, 1000);
        });
		$(document).ready(function() {
            setTimeout(function(){
                $('#measure_d').prop('selectedIndex', 3).trigger('change');
            }, 1000);
        });
		$(document).ready(function() {
            setTimeout(function(){
                $('#measure_e').prop('selectedIndex', 3).trigger('change');
            }, 1000);
        });
        $(document).ready(function() {
            setTimeout(function(){
                $('#measure_f').prop('selectedIndex', 3).trigger('change');
            }, 1000);
        });
		
		function sum() {
		var txtFirstNumberValue = document.getElementById('presentase_a').value;
		var txtSecondNumberValue = document.getElementById('presentase_b').value;
		var txtThirdNumberValue = document.getElementById('presentase_c').value;
		var txtFourthNumberValue = document.getElementById('presentase_d').value;
		var txtFifthNumberValue = document.getElementById('presentase_e').value;
        var txtSixthNumberValue = document.getElementById('presentase_f').value;
		var result = parseInt(txtFourthNumberValue) + parseInt(txtSecondNumberValue) + parseInt(txtThirdNumberValue) + parseInt(txtFirstNumberValue) + parseInt(txtFifthNumberValue) + parseInt(txtSixthNumberValue);
		if (!isNaN(result)) {
		 document.getElementById('total_presentase').value = result;
			}
		}
		
    </script>


</body>
</html>
