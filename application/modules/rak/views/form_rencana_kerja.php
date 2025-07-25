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
                                    <h3><b>RENCANA KERJA</b></h3>
                                </div>
                            </div>
                            <div class="panel-content">
                                <form method="POST" action="<?php echo site_url('rak/submit_rencana_kerja');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                    <div class="row">
										<div class="col-sm-3">
                                            <label>Tanggal</label>
                                            <input type="text" class="form-control dtpicker" name="tanggal_rencana_kerja"  value="" />
                                        </div>
									</div>
                                    <br />
                                    <div class="table-responsive">
                                        <table id="table-product" class="table table-bordered table-striped table-condensed table-center">
                                            <thead>
                                                <tr class="text-center">
                                                    <th width="5%"></th>
                                                    <th width="35%">URAIAN</th>
                                                    <th width="30%">VOLUME</th>
                                                    <th width="30%">NILAI</th>                                  
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" rowspan="6" style="font-weight:bold;vertical-align:middle;">P<br />E<br />N<br />D<br />A<br />P<br />A<br />T<br />A<br />N</td>
                                                    <td style="vertical-align:middle;">LPA (JLS Brantas) - Batu Split 0,5 - 10 mm (Upah Giling)</td>
													<td>
                                                    <input type="text" id="vol_produk_a" name="vol_produk_a" class="form-control numberformat text-right" value="" autocomplete="off">
                                                    </td>
                                                    <td>
                                                    <input type="text" id="price_a" name="price_a" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">Bu Tampi - Batu Split 0,5 - 10 mm</td>
													<td>
                                                    <input type="text" id="vol_produk_b" name="vol_produk_b" class="form-control numberformat text-right" value="" autocomplete="off">
                                                    </td>
                                                    <td>
                                                    <input type="text" id="price_b" name="price_b" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">P. Antoni - Abu Batu</td>
													<td>
                                                    <input type="text" id="vol_produk_c" name="vol_produk_c" class="form-control numberformat text-right" value="" autocomplete="off">
                                                    </td>
                                                    <td>
                                                    <input type="text" id="price_c" name="price_c" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">P. Antoni - Batu Split 0,5 - 10 mm</td>
													<td>
                                                    <input type="text" id="vol_produk_d" name="vol_produk_d" class="form-control numberformat text-right" value="" autocomplete="off">
                                                    </td>
                                                    <td>
                                                    <input type="text" id="price_d" name="price_d" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">P. Antoni - Batu Split 10 - 15 mm</td>
													<td>
                                                    <input type="text" id="vol_produk_e" name="vol_produk_e" class="form-control numberformat text-right" value="" autocomplete="off">
                                                    </td>
                                                    <td>
                                                    <input type="text" id="price_e" name="price_e" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">P. Antoni - Batu Split 10 - 20 mm</td>
													<td>
                                                    <input type="text" id="vol_produk_f" name="vol_produk_f" class="form-control numberformat text-right" value="" autocomplete="off">
                                                    </td>
                                                    <td>
                                                    <input type="text" id="price_f" name="price_f" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center" style="font-weight:bold; vertical-align:middle;">BAHAN</td>
                                                    <td style="vertical-align:middle;">Boulder</td>
													<td>
                                                    <input type="text" id="vol_boulder" name="vol_boulder" class="form-control numberformat text-right" value="" autocomplete="off">
                                                    </td>
                                                    <td>
                                                    <input type="text" id="boulder" name="boulder" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center" rowspan="6" style="font-weight:bold; vertical-align:middle;">A<br />L<br />A<br />T</td>
                                                    <td style="vertical-align:middle;">Stone Crusher + Gendet</td>
													<td></td>
                                                    <td>
                                                    <input type="text" id="stone_crusher" name="stone_crusher" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">Wheel Loader</td>
													<td></td>
                                                    <td>
                                                    <input type="text" id="wheel_loader" name="wheel_loader" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">Maintenance</td>
													<td></td>
                                                    <td>
                                                    <input type="text" id="maintenance" name="maintenance" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">BBM Solar</td>
													<td></td>
                                                    <td>
                                                    <input type="text" id="bbm_solar" name="bbm_solar" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">Tangki Solar</td>
													<td></td>
                                                    <td>
                                                    <input type="text" id="tangki" name="tangki" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align:middle;">Timbangan</td>
													<td></td>
                                                    <td>
                                                    <input type="text" id="timbangan" name="timbangan" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center" style="font-weight:bold; vertical-align:middle;">BUA</td>
                                                    <td style="vertical-align:middle;">Overhead</td>
													<td></td>
                                                    <td>
                                                    <input type="text" id="overhead" name="overhead" class="form-control rupiahformat text-right" value="" onchange="changeData(1)" autocomplete="off">
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-right">GRAND TOTAL</td>
                                                    <td>
                                                    <input type="text" id="sub-total-val" name="sub_total" value="0" class="form-control rupiahformat tex-left text-right" readonly="">
                                                    </td>
                                                    <td></td>
                                                </tr> 
                                            </tfoot>
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
                                        <a href="<?= site_url('admin/rencana_kerja#rencana_kerja');?>" class="btn btn-danger" style="margin-bottom:0px; width:10%; font-weight:bold; border-radius:5px;">BATAL</a>
                                        <button type="submit" class="btn btn-success" style="width:10%; font-weight:bold; border-radius:5px;">KIRIM</button>
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

        $('input.numberformat').number(true, 2,',','.' );
        $('input.rupiahformat').number(true, 0,',','.' );

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
			var price_a = $('#price_a').val();
            var price_b = $('#price_b').val();
            var price_c = $('#price_c').val();
            var price_d = $('#price_d').val();
            var price_e = $('#price_e').val();
            var price_f = $('#price_f').val();
            var boulder = $('#boulder').val();
            var stone_crusher = $('#stone_crusher').val();
            var wheel_loader = $('#wheel_loader').val();
            var maintenance = $('#maintenance').val();
            var bbm_solar = $('#bbm_solar').val();
            var tangki = $('#tangki').val();
            var timbangan = $('#timbangan').val();
            var overhead = $('#overhead').val();
            				
			price_a = ( price_a);
            $('#price_a').val(price_a);
            price_b = ( price_b);
            $('#price_b').val(price_b);
            vol_produk_c = ( vol_produk_c);
            $('#price_c').val(price_c);
            price_d = ( price_d);
            $('#price_d').val(price_d);
            price_e = ( price_e);
            $('#price_e').val(price_e);
            price_f = ( price_f);
            $('#price_f').val(price_f);
            boulder = ( boulder);
            $('#boulder').val(boulder);
            stone_crusher = ( stone_crusher);
            $('#stone_crusher').val(stone_crusher);
            wheel_loader = ( wheel_loader);
            $('#wheel_loader').val(wheel_loader);
            maintenance = ( maintenance);
            $('#maintenance').val(maintenance);
            bbm_solar = ( bbm_solar);
            $('#bbm_solar').val(bbm_solar);
            tangki = ( tangki);
            $('#tangki').val(tangki);
            timbangan = ( timbangan);
            $('#timbangan').val(timbangan);
            overhead = ( overhead);
            $('#overhead').val(overhead);
            getTotal();
        }

        function getTotal()
        {
            var sub_total = $('#sub-total-val').val();

            sub_total = (parseFloat($('#price_a').val()) + parseFloat($('#price_b').val()) + parseFloat($('#price_c').val()) + parseFloat($('#price_d').val()) + parseFloat($('#price_e').val()) + parseFloat($('#price_f').val())) - (parseFloat($('#boulder').val()) + parseFloat($('#stone_crusher').val()) + parseFloat($('#wheel_loader').val()) + parseFloat($('#maintenance').val()) + parseFloat($('#bbm_solar').val()) + parseFloat($('#tangki').val()) + parseFloat($('#timbangan').val()) + parseFloat($('#overhead').val()));
            
            $('#sub-total-val').val(sub_total);
            $('#sub-total').text($.number( sub_total, 0,',','.' ));

            total_total = parseFloat(sub_total);
            $('#total-val').val(total_total);
            $('#total').text($.number( total_total, 0,',','.' ));
        }
    </script>
</body>
</html>
