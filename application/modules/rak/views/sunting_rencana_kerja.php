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
            border:2px solid gray;
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
                                <h3><b>RENCANA KERJA</b></h3>
                            </div>
                            <div class="panel-content">
                                <form method="POST" action="<?php echo site_url('rak/submit_sunting_rencana_kerja');?>" id="form-po" enctype="multipart/form-data" autocomplete="off">
                                    <input type="hidden" name="id" value="<?= $rak["id"] ?>">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th width="200px">Tanggal</th>
                                            <td><input type="text" class="form-control dtpicker" name="tanggal_rencana_kerja"  value="<?= $tanggal ?>" /></td>
                                        </tr>
                                        <tr>
                                            <th width="100px">Lampiran</th>
                                            <td>:  
                                                <?php foreach($lampiran as $l) : ?>                                    
                                                <a href="<?= base_url("uploads/rak_biaya/".$l["lampiran"]) ?>" target="_blank">Lihat bukti  <?= $l["lampiran"] ?> <br></a></td>
                                                <?php endforeach; ?>
                                        </tr>
                                    </table>
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
                                                <td class="text-center" rowspan="9" style="font-weight:bold;vertical-align:middle;">P<br />E<br />N<br />D<br />A<br />P<br />A<br />T<br />A<br />N</td>
                                                <td style="vertical-align:middle;">PT LMA - Batu Split (Upah Giling)</td>
                                                <td>
                                                <input type="text" id="vol_produk_i" name="vol_produk_i" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_i"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_i" name="price_i" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_i"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">LPA (JLS Brantas)</td>
                                                <td>
                                                <input type="text" id="vol_produk_a" name="vol_produk_a" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_a"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_a" name="price_a" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_a"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">Bu Tampi - Batu Split 0,5 - 10 mm (Upah Giling)</td>
                                                <td>
                                                <input type="text" id="vol_produk_g" name="vol_produk_g" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_g"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_g" name="price_g" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_g"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">Bu Tampi - Batu Boulder</td>
                                                <td>
                                                <input type="text" id="vol_produk_h" name="vol_produk_h" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_h"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_h" name="price_h" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_h"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">Bu Tampi - Batu Split 0,5 - 10 mm</td>
                                                <td>
                                                <input type="text" id="vol_produk_b" name="vol_produk_b" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_b"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_b" name="price_b" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_b"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">P. Antoni - Abu Batu</td>
                                                <td>
                                                <input type="text" id="vol_produk_c" name="vol_produk_c" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_c"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_c" name="price_c" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_c"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">P. Antoni - Batu Split 0,5 - 10 mm</td>
                                                <td>
                                                <input type="text" id="vol_produk_d" name="vol_produk_d" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_d"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_d" name="price_d" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_d"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">P. Antoni - Batu Split 10 - 15 mm</td>
                                                <td>
                                                <input type="text" id="vol_produk_e" name="vol_produk_e" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_e"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_e" name="price_e" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_e"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">P. Antoni - Batu Split 10 - 20 mm</td>
                                                <td>
                                                <input type="text" id="vol_produk_f" name="vol_produk_f" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_produk_f"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="price_f" name="price_f" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["price_f"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center" style="font-weight:bold; vertical-align:middle;">BAHAN</td>
                                                <td style="vertical-align:middle;">Boulder</td>
                                                <td>
                                                <input type="text" id="vol_boulder" name="vol_boulder" class="form-control numberformat text-right" value="<?php echo number_format($rak["vol_boulder"],2,',','.');?>" autocomplete="off">
                                                </td>
                                                <td>
                                                <input type="text" id="boulder" name="boulder" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["boulder"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center" rowspan="6" style="font-weight:bold; vertical-align:middle;">A<br />L<br />A<br />T</td>
                                                <td style="vertical-align:middle;">Stone Crusher + Gendet</td>
                                                <td></td>
                                                <td>
                                                <input type="text" id="stone_crusher" name="stone_crusher" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["stone_crusher"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">Wheel Loader</td>
                                                <td></td>
                                                <td>
                                                <input type="text" id="wheel_loader" name="wheel_loader" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["wheel_loader"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">Maintenance</td>
                                                <td></td>
                                                <td>
                                                <input type="text" id="maintenance" name="maintenance" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["maintenance"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">BBM Solar</td>
                                                <td></td>
                                                <td>
                                                <input type="text" id="bbm_solar" name="bbm_solar" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["bbm_solar"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">Tangki Solar</td>
                                                <td></td>
                                                <td>
                                                <input type="text" id="tangki" name="tangki" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["tangki"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align:middle;">Timbangan</td>
                                                <td></td>
                                                <td>
                                                <input type="text" id="timbangan" name="timbangan" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["timbangan"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center" style="font-weight:bold; vertical-align:middle;">BUA</td>
                                                <td style="vertical-align:middle;">Overhead</td>
                                                <td></td>
                                                <td>
                                                <input type="text" id="overhead" name="overhead" class="form-control rupiahformat text-right" value="<?php echo number_format($rak["overhead"],0,',','.');?>" onchange="changeData(1)" autocomplete="off">
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
                                    <br />
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <a href="<?= site_url('admin/rencana_kerja');?>" class="btn btn-danger" style="margin-bottom:0px; font-weight:bold; border-radius:5px;">BATAL</a>
                                            <button type="submit" class="btn btn-success" style="font-weight:bold; border-radius:5px;">KIRIM</button>
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
            var price_g = $('#price_g').val();
            var price_h = $('#price_h').val();
            var price_i = $('#price_i').val();
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
            price_g = ( price_g);
            $('#price_g').val(price_g);
            price_h = ( price_h);
            $('#price_h').val(price_h);
            price_i = ( price_i);
            $('#price_i').val(price_i);
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

            sub_total = (parseFloat($('#price_a').val()) + parseFloat($('#price_b').val()) + parseFloat($('#price_c').val()) + parseFloat($('#price_d').val()) + parseFloat($('#price_e').val()) + parseFloat($('#price_f').val()) + parseFloat($('#price_g').val()) + parseFloat($('#price_h').val()) + parseFloat($('#price_i').val())) - (parseFloat($('#boulder').val()) + parseFloat($('#stone_crusher').val()) + parseFloat($('#wheel_loader').val()) + parseFloat($('#maintenance').val()) + parseFloat($('#bbm_solar').val()) + parseFloat($('#tangki').val()) + parseFloat($('#timbangan').val()) + parseFloat($('#overhead').val()));
            
            $('#sub-total-val').val(sub_total);
            $('#sub-total').text($.number( sub_total, 0,',','.' ));

            total_total = parseFloat(sub_total);
            $('#total-val').val(total_total);
            $('#total').text($.number( total_total, 0,',','.' ));
        }

    </script>


</body>
</html>
