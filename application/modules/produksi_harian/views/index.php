<!doctype html>
<html lang="en" class="fixed">
    <head>
        <?php echo $this->Templates->Header(); ?>
        <style type="text/css">
            body {
                font-family: helvetica;
            }
            
            .mytable thead th {
            background-color:	#e69500;
            color: #ffffff;
            text-align: center;
            vertical-align: middle;
            padding: 5px;
            }
            
            .mytable tbody td {
            padding: 5px;
            }
            
            .mytable tfoot td {
            background-color:	#e69500;
            color: #FFFFFF;
            padding: 5px;
            }

            button {
                border: none;
                border-radius: 5px;
                padding: 5px;
                font-size: 12px;
                text-transform: uppercase;
                cursor: pointer;
                color: white;
                background-color: #2196f3;
                box-shadow: 0 0 4px #999;
                outline: none;
            }

            .ripple {
                background-position: center;
                transition: background 0.8s;
            }
            .ripple:hover {
                background: #47a7f5 radial-gradient(circle, transparent 1%, #47a7f5 1%) center/15000%;
            }
            .ripple:active {
                background-color: #6eb9f7;
                background-size: 100%;
                transition: background 0s;
            }
        </style>
    </head>
    <body>
        <div class="wrap">
            <?php echo $this->Templates->PageHeader(); ?>
            <div class="page-body">
                <div class="content">
                    <div class="row animated fadeInUp">
                        <div class="col-sm-12 col-lg-12">
                            <div class="panel">
                                <div class="panel-header">
                                    <h3><b style="text-transform:uppercase;"><?php echo $row[0]->menu_name; ?></b></h3>
                                    <div class="text-left">
                                        <a href="<?php echo site_url('admin');?>">
                                        <button class="ripple"><b><i class="fa-solid fa-rotate-left"></i> KEMBALI</b></button></a>
                                    </div>
                                </div>
                                <div class="panel-content">
                                    <ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#kalibrasi" aria-controls="kalibrasi" role="tab" data-toggle="tab" style="border-radius:10px; font-weight:bold;">KALIBRASI</a></li>
                                    <li role="presentation"><a href="#produksi_harian" aria-controls="produksi_harian" role="tab" data-toggle="tab" style="border-radius:10px; font-weight:bold;">PRODUKSI HARIAN</a></li>
                                </ul>
                                    <div class="tab-content">
                                        <br />
                                        <div role="tabpanel" class="tab-pane active" id="kalibrasi">
                                            <div class="col-sm-3">
											    <input type="text" id="filter_date_kalibrasi" name="filter_date" class="form-control dtpickerange" autocomplete="off" placeholder="Filter By Date">
                                            </div>
                                            <div class="col-sm-4">
                                                <select id="jobs_type" name="jobs_type" class="form-control select2">
                                                    <option value="">Pilih Judul</option>
                                                    <?php
                                                    foreach ($judul as $key => $jd) {
                                                    ?>
                                                        <option value="<?php echo $jd['jobs_type']; ?>"><?php echo $jd['jobs_type']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-5">
                                                <button style="background-color:#88b93c; border:1px solid black; border-radius:10px; line-height:30px;"><a href="<?php echo site_url('produksi/form_kalibrasi'); ?>"><b style="color:white;">BUAT KALIBRASI</b></a></button>
                                            </div>
                                            <br />
										    <br />
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-center" id="table_kalibrasi" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Tanggal</th>
                                                            <th>Nomor Kalibrasi</th>
                                                            <th>Judul</th>
                                                            <th>Lampiran</th>
                                                            <th>Hapus</th>
                                                            <th>Cetak</th>
                                                            <th>Status</th>
                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                    <tfoot>
                                                    
                                                    </tfoot>
                                                </table>
                                            </div>
									    </div>

                                        <div role="tabpanel" class="tab-pane" id="produksi_harian">
                                            <div class="col-sm-3">
                                                <input type="text" id="filter_date_produksi_harian" name="filter_date" class="form-control dtpickerange" autocomplete="off" placeholder="Filter By Date">
                                            </div>
                                            <div class="col-sm-5">
                                                <button style="background-color:#88b93c; border:1px solid black; border-radius:10px; line-height:30px;"><a href="<?php echo site_url('produksi/form_produksi_harian'); ?>"><b style="color:white;">BUAT PRODUKSI HARIAN</b></a></button>
                                            </div>
                                            <br />
										    <br />
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-center" id="table_produksi_harian" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>	
                                                            <th>Tanggal</th>
                                                            <th>Nomor Produksi Harian</th>	
                                                            <th>Durasi Produksi (Jam)</th>
                                                            <th>Pemakaian Bahan Baku (Ton)</th>
                                                            <th>Kapasitas Produksi (Ton/Jam)</th>
                                                            <th>Keterangan</th>
                                                            <th>Lampiran</th>
                                                            <th>Hapus</th>
                                                            <th>Cetak</th>
                                                            <th>Status</th>												
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                    <tfoot>
                                                    
                                                    </tfoot>
                                                </table>
                                            </div>
									    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $this->Templates->Footer(); ?>
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/jquery.number.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/bootbox.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">
        <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/moment.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/daterangepicker.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/back/theme/vendor/daterangepicker/daterangepicker.css">
        <script src="https://kit.fontawesome.com/591a1bf2f6.js" crossorigin="anonymous"></script>

        <script>
            <?php
            $kunci_rakor = $this->db->select('date')->order_by('date','desc')->limit(1)->get_where('kunci_rakor')->row_array();
            $last_opname = date('d-m-Y', strtotime('+1 days', strtotime($kunci_rakor['date'])));
            ?>
            $('.dtpicker').daterangepicker({
                singleDatePicker: true,
                showDropdowns : false,
                locale: {
                    format: 'DD-MM-YYYY'
                },
                minDate: '<?php echo $last_opname;?>',
                //maxDate: moment().add(+0, 'd').toDate(),
                //minDate: moment().startOf('month').toDate(),
                maxDate: moment().endOf('month').toDate(),
            });

            $('.dtpickerange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'DD-MM-YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                showDropdowns: true,
            });

            var table_produksi_harian = $('#table_produksi_harian').DataTable( {"bAutoWidth": false,
                ajax: {
                    processing: true,
                    serverSide: true,
                    url: '<?php echo site_url('produksi/table_produksi_harian'); ?>',
                    type: 'POST',
                    data: function(d) {
                        d.filter_date = $('#filter_date_produksi_harian').val();
                    }
                },
                responsive: true,
                "deferRender": true,
                "language": {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
                },
                columns: [
                    {
                        "data": "no"
                    },
                    {
                        "data": "date_prod"
                    },
                    {
                        "data": "no_prod"
                    },
                    {
                        "data": "duration"
                    },
                    {
                        "data": "used"
                    },
                    {
                        "data": "capacity"
                    },
                    {
                        "data": "memo"
                    },
                    {
                        "data": "lampiran"
                    },
                    {
                        "data": "actions"
                    },
                    {
                        "data": "print"
                    },
                    {
                        "data": "status"
                    }
                ],
                "columnDefs": [
                    { "width": "5%", "targets": 0, "className": 'text-center'},
                    { "targets": [3, 4, 5], "className": 'text-right'},
                ],
            });
            
            $('#filter_date_produksi_harian').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
            table_produksi_harian.ajax.reload();
            });

            function DeleteProduksiHarian(id) {
            bootbox.confirm("Anda yakin akan menghapus data ini ?", function(result) {
                // console.log('This was logged in the callback: ' + result); 
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('produksi/delete_produksi_harian'); ?>",
                        dataType: 'json',
                        data: {
                            id: id
                        },
                        success: function(result) {
                            if (result.output) {
                                table_produksi_harian.ajax.reload();
                                bootbox.alert('<b>Berhasil Menghapus Produksi Harian</b>');
                            } else if (result.err) {
                                bootbox.alert(result.err);
                            }
                        }
                    });
                }
                });
            }

            var table_kalibrasi = $('#table_kalibrasi').DataTable( {"bAutoWidth": false,
            ajax: {
                processing: true,
                serverSide: true,
                url: '<?php echo site_url('produksi/table_kalibrasi'); ?>',
                type: 'POST',
                data: function(d) {
                    d.filter_date = $('#filter_date_kalibrasi').val();
					d.jobs_type = $('#jobs_type').val();
                }
            },
            responsive: true,
            "deferRender": true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            columns: [
				{
                    "data": "no"
                },
				{
                    "data": "tanggal_kalibrasi"
                },
				{
                    "data": "no_kalibrasi"
                },
				{
                    "data": "jobs_type"
                },
				{
                    "data": "lampiran"
                },
                {
					"data": "actions"
				},
                {
					"data": "print"
				},
                {
                    "data": "status"
                }
            ],
            "columnDefs": [
                { "width": "5%", "targets": 0, "className": 'text-center'},
            ],
        });
		
		$('#jobs_type').change(function() {
        table_kalibrasi.ajax.reload();
		});
		
		$('#filter_date_kalibrasi').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        table_kalibrasi.ajax.reload();
		});

        function DeleteDataKalibrasi(id) {
        bootbox.confirm("Anda yakin akan menghapus data ini ?", function(result) {
            // console.log('This was logged in the callback: ' + result); 
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('produksi/delete_kalibrasi'); ?>",
                    dataType: 'json',
                    data: {
                        id: id
                    },
                    success: function(result) {
                        if (result.output) {
                            table_kalibrasi.ajax.reload();
                            bootbox.alert('<b>Berhasil Menghapus Kalibrasi</b>');
                        } else if (result.err) {
                            bootbox.alert(result.err);
                        }
                    }
                });
            }
            });
        }
        </script>
    </body>
</html>