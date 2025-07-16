<!doctype html>
<html lang="en" class="fixed">
<head>
    <?php echo $this->Templates->Header();?>

    <style type="text/css">
        body{
			font-family: helvetica;
	  	}
        .table-center th{
            text-align:center;
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
                                <div>
                                    <h3>RAP ALAT</h3>
                                    
                                </div>
                            </div>
                            <div class="panel-content">
                                <form method="POST" action="<?php echo site_url('rap/submit_rap_alat');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                    <div class="row">
										<div class="col-sm-3">
                                            <label>Tanggal</label>
                                            <input type="text" class="form-control dtpicker" name="tanggal_rap_alat" required="" value="" />
                                        </div>
										<div class="col-sm-6">
                                            <label>Nomor RAP</label>
                                            <input type="text" class="form-control" name="nomor_rap_alat" required="" value="<?= $this->pmm_model->GetNoRapAlat();?>">
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Masa Kontrak</label>
                                            <input type="text" class="form-control" name="masa_kontrak" required="" value="">
                                        </div>
									</div>
                                    <br />
                                    <div class="table-responsive">
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <thead>
                                                <tr class="text-center">
                                                    <th width="5%">NO.</th>
                                                    <th width="25%">URAIAN</th>
                                                    <th>VOLUME</th>
                                                    <th>SATUAN</th>
													<th>TOTAL</th>                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1.</td>
                                                    <td>STONE CRUSHER + GENSET</td>
													<td>
                                                    <input type="text" id="vol_stone_crusher" name="vol_stone_crusher" class="form-control numberformat text-right" value="" onchange="changeData(1)" required="" autocomplete="off">
                                                    </td>
                                                    <td class="text-center">M3</td>
                                                    <td>
                                                    <input type="text" name="harsat_stone_crusher" id="harsat_stone_crusher" class="form-control rupiahformat text-right" onchange="changeData(1)" required="" autocomplete="off"/>
                                                    </td>
                                                    <td>
                                                    <input type="text" name="stone_crusher" id="stone_crusher" class="form-control rupiahformat text-right" readonly="" value="" required="" autocomplete="off"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">4.</td>
                                                    <td>WHEEL LOADER</td>
													<td>
                                                    <input type="text" id="vol_wheel_loader" name="vol_wheel_loader" class="form-control numberformat text-right" value="" onchange="changeData(1)" required="" autocomplete="off">
                                                    </td>
                                                    <td class="text-center">M3</td>
                                                    <td>
                                                    <input type="text" name="harsat_wheel_loader" id="harsat_wheel_loader" class="form-control rupiahformat text-right" onchange="changeData(1)"  required="" autocomplete="off"/>
                                                    </td>
                                                    <td>
                                                    <input type="text" name="wheel_loader" id="wheel_loader" class="form-control rupiahformat text-right" readonly="" value="" required="" autocomplete="off"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center"></td>
                                                    <td>MAINTENANCE</td>
													<td>
                                                    <input type="text" id="vol_maintenance" name="vol_maintenance" class="form-control numberformat text-right" value="" onchange="changeData(1)" required="" autocomplete="off">
                                                    </td>
                                                    <td class="text-center">M3</td>
                                                    <td>
                                                    <input type="text" name="harsat_maintenance" id="harsat_maintenance" class="form-control rupiahformat text-right" onchange="changeData(1)"  required=""  autocomplete="off"/>
                                                    </td>
                                                    <td>
                                                    <input type="text" name="maintenance" id="maintenance" class="form-control rupiahformat text-right" readonly="" value="" required=""  autocomplete="off"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2.</td>
                                                    <td>BBM SOLAR</td>
													<td>
                                                    <input type="text" id="vol_bbm_solar" name="vol_bbm_solar" class="form-control numberformat text-right" value="" onchange="changeData(1)" required="" autocomplete="off">
                                                    </td>
                                                    <td class="text-center">Liter</td>
                                                    <td>
                                                    <input type="text" name="harsat_bbm_solar" id="harsat_bbm_solar" class="form-control rupiahformat text-right" onchange="changeData(1)" required="" autocomplete="off"/>
                                                    </td>
                                                    <td>
                                                    <input type="text" name="bbm_solar" id="bbm_solar" class="form-control rupiahformat text-right" readonly="" value="" required="" readonly="" autocomplete="off"/>
                                                    </td>
                                                </tr>
                                                
                                            </tbody>
                                            <!--<tfoot>
                                                <tr>
                                                    <td colspan="5" class="text-right">GRAND TOTAL</td>
                                                    <td>
                                                    <input type="text" id="sub-total-val" name="sub_total" value="0" class="form-control rupiahformat tex-left text-right" readonly="">
                                                    </td>
                                                </tr> 
                                            </tfoot>-->
                                        </table>    
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Lampiran</label>
                                                <input type="file" class="form-control" name="files[]"  multiple="" />
                                            </div>
                                        </div>
                                    </div>
                                    <br /><br />
                                    <div class="text-center">
                                        <a href="<?= site_url('admin/rap#alat');?>" class="btn btn-danger" style="margin-bottom:0; font-weight:bold; border-radius:10px;">BATAL</a>
                                        <button type="submit" class="btn btn-success" style="font-weight:bold; border-radius:10px;">KIRIM</button>
                                    </div>
                                    <br /><br />
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

        $('input.numberformat').number(true, 4,',','.' );
        $('input.rupiahformat').number(true, 2,',','.' );

        $('.dtpicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns : true,
            locale: {
              format: 'DD-MM-YYYY'
            }
        });
        $('.dtpicker').on('apply.daterangepicker', function(ev, picker) {
              $(this).val(picker.startDate.format('DD-MM-YYYY'));
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

        function changeData(id)
        {
			var vol_stone_crusher = $('#vol_stone_crusher').val();
            var vol_wheel_loader = $('#vol_wheel_loader').val();
            var vol_maintenance = $('#vol_maintenance').val();
            var vol_bbm_solar = $('#vol_bbm_solar').val();

			var harsat_stone_crusher = $('#harsat_stone_crusher').val();
            var harsat_wheel_loader = $('#harsat_wheel_loader').val();
            var harsat_maintenance = $('#harsat_maintenance').val();
            var harsat_bbm_solar = $('#harsat_bbm_solar').val();
            				
			stone_crusher = ( vol_stone_crusher * harsat_stone_crusher );
            $('#stone_crusher').val(stone_crusher);
            wheel_loader = ( vol_wheel_loader * harsat_wheel_loader );
            $('#wheel_loader').val(wheel_loader);
            maintenance = ( vol_maintenance * harsat_maintenance );
            $('#maintenance').val(maintenance);
            bbm_solar = ( vol_bbm_solar * harsat_bbm_solar );
            $('#bbm_solar').val(bbm_solar);
            getTotal();
        }

        function getTotal()
        {
            var sub_total = $('#sub-total-val').val();

            sub_total = parseInt($('#stone_crusher').val()) + parseInt($('#wheel_loader').val()) + parseInt($('#maintenance').val()) + parseInt($('#bbm_solar').val());
            
            $('#sub-total-val').val(sub_total);
            $('#sub-total').text($.number( sub_total, 2,',','.' ));

            total_total = parseInt(sub_total);
            $('#total-val').val(total_total);
            $('#total').text($.number( total_total, 2,',','.' ));
        }

    </script>


</body>
</html>
