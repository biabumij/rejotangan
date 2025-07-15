<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produksi extends Secure_Controller {

	public function __construct()
	{
        parent::__construct();
        // Your own constructor code
        $this->load->model(array('admin/m_admin','crud_global','m_themes','pages/m_pages','menu/m_menu','admin_access/m_admin_access','DB_model','member_back/m_member_back','m_member','pmm/pmm_model','admin/Templates','pmm/pmm_finance','produk/m_produk'));
        $this->load->library('enkrip');
		$this->load->library('filter');
		$this->load->library('waktu');
		$this->load->library('session');
    }

	public function sunting_stock_opname($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['row'] = $this->db->get_where("pmm_remaining_materials_cat", ["id" => $id])->row_array();
			$cat = $this->db->get_where("pmm_remaining_materials_cat", ["id" => $id])->row_array();
			$data['tanggal'] = date('d-m-Y',strtotime($cat['date']));
			$this->load->view('produksi/sunting_stock_opname', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_sunting_stock_opname()
	{

		$id = $this->input->post('id');
		$date = date('Y-m-d',strtotime($this->input->post('date')));
		$material_id = $this->input->post('material_id');
		$volume =  str_replace('.', '', $this->input->post('volume'));
		$volume =  str_replace(',', '.', $volume);
		$total =  str_replace('.', '', $this->input->post('total'));
		$measure = $this->input->post('measure');
		$notes = $this->input->post('notes');

		$arr_update = array(
			'material_id' => $material_id,
			'date' => $date,
			'measure' => $measure,
			'volume' => $volume,
			'convert' => $convert,
			'display_volume' => $volume,
			'display_measure' => $measure,
			'notes' => $notes,
			'total' => $total,
			'price' => $total / $volume,
			'updated_by' => $this->session->userdata('admin_id'),
			'updated_on' => date('Y-m-d H:i:s')
		);

		$this->db->where('id', $id);
		if ($this->db->update('pmm_remaining_materials_cat', $arr_update)) {
			
		}

		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', '<b>ERROR</b>');
			redirect('admin/stock_opname');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', '<b>SAVED</b>');
			redirect('admin/stock_opname');
		}
	}

	
	public function cetak_stock_opname()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetPrintFooter(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
			$filter_date = '-';
		}else {
			$arr_filter_date = explode(' - ', $arr_date);
			$start_date = date('Y-m-d',strtotime($arr_filter_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		$data['filter_date'] = $filter_date;
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['date1'] = date('d F Y',strtotime($arr_filter_date[0]));
		$data['date2'] = date('d F Y',strtotime($arr_filter_date[1]));
		$filter_date = $this->input->get('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('date >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('date <=',date('Y-m-d',strtotime($arr_date[1])));
		}
		$query = $this->db->get('pmm_remaining_materials_cat');
		$data['data'] = $query->result_array();
		$data['custom_date'] = $this->input->get('custom_date');
        $html = $this->load->view('produksi/cetak_stock_opname',$data,TRUE);

        
        $pdf->SetTitle('');
        $pdf->nsi_html($html);
        $pdf->Output('stock_opname.pdf', 'I');
	
	}

	public function table_pemakaian()
	{   
        $data = array();
		$filter_date = $this->input->post('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('date >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('date <=',date('Y-m-d',strtotime($arr_date[1])));
		}
        $this->db->select('*');
		$this->db->order_by('date','desc');
		$query = $this->db->get('kunci_bahan_baku');
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
                $row['date'] = date('d F Y',strtotime($row['date']));
                $row['jumlah_bahan'] = number_format($row['nilai_semen'] + $row['nilai_pasir'] + $row['nilai_1020'] + $row['nilai_2030'] + $row['nilai_additive'],0,',','.');
				$row['jumlah_solar'] = number_format($row['nilai_solar'],0,',','.');
				$row['status'] = $row['status'];
				$row['admin_name'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));

				$admin_id = $this->session->userdata('admin_id');
				$approval = $this->db->select('*')
				->from('tbl_admin')
				->where("admin_id = $admin_id ")
				->get()->row_array();
				$edit_rap =  $approval['edit_rap'];

				if($edit_rap == 1){
					$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteDataPemakaian('.$row['id'].')" class="btn btn-danger" style="font-weight:bold; border-radius:10px;"><i class="fa fa-close"></i> </a>';
				}else {
					$row['actions'] = '-';
				}

                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_pemakaian()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('kunci_bahan_baku',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}
	

	public function form_pemakaian()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] = $this->db->select('*')->get_where('produk', array('status' => 'PUBLISH', 'kategori_produk' => 1))->result_array();
			$this->load->view('produksi/form_pemakaian', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_pemakaian()
	{
		$date = $this->input->post('date');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'date' => date('Y-m-d', strtotime($date)),
			'unit_head' => 6,
			'logistik' => 10,
			'admin' => 10,
			'keu' => 9,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);

		$this->db->insert('kunci_bahan_baku', $arr_insert);

		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error','<b>ERROR</b>');
			redirect('/stock_opname/pemakaian');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success','<b>SAVED</b>');
			redirect('admin/stock_opname');
		}
	}

	public function table_pemakaian_bahan()
	{   
        $data = array();
		$filter_date = $this->input->post('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('date >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('date <=',date('Y-m-d',strtotime($arr_date[1])));
		}
		
        $last_opname = $this->db->select('date')
		->from('kunci_rakor')
		->order_by('date','desc')->limit(1)
		//->order_by('date','desc')->limit(1,2)
		->get()->row_array();
		$last_opname = date('Y-m-d', strtotime($last_opname['date']));

        $this->db->select('*');
		$this->db->where("date > '$last_opname'");
		$this->db->order_by('date','desc');
		$query = $this->db->get('pemakaian_bahan');
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
                $row['date'] = date('d F Y',strtotime($row['date']));
				$row['material_id'] = $this->crud_global->GetField('produk',array('id'=>$row['material_id']),'nama_produk');
                $row['volume'] = number_format($row['volume'],2,',','.');
				$row['nilai'] = number_format($row['nilai'],0,',','.');
				$row['status'] = $row['status'];
				$row['admin_name'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));

				if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 2 || $this->session->userdata('admin_group_id') == 3 || $this->session->userdata('admin_group_id') == 4){
					$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteDataPemakaianBahan('.$row['id'].')" class="btn btn-danger" style="font-weight:bold; border-radius:10px;"><i class="fa fa-close"></i> </a>';
				}else {
					$row['actions'] = '-';
				}

                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_pemakaian_bahan()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('pemakaian_bahan',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

	public function form_pemakaian_bahan()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] = $this->db->select('*')->get_where('produk', array('status' => 'PUBLISH', 'kategori_produk' => 1))->result_array();
			$this->load->view('produksi/form_pemakaian_bahan', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_pemakaian_bahan()
	{
		$date = $this->input->post('date');
		$material_id = $this->input->post('material_id');
		$volume =  str_replace('.', '', $this->input->post('volume'));
		$volume =  str_replace(',', '.', $volume);
		$nilai = str_replace('.', '', $this->input->post('nilai'));
		$notes = $this->input->post('notes');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'date' => date('Y-m-d', strtotime($date)),
			'material_id' => $material_id,
			'volume' => $volume,
			'nilai' => $nilai,
			'notes' => $notes,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);

		$this->db->insert('pemakaian_bahan', $arr_insert);

		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error','<b>ERROR</b>');
			redirect('/stock_opname#pemakaian_bahan');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success','<b>SAVED</b>');
			redirect('admin/stock_opname#pemakaian_bahan');
		}
	}

	public function table_rakor()
	{   
        $data = array();
		$filter_date = $this->input->post('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('date >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('date <=',date('Y-m-d',strtotime($arr_date[1])));
		}
        $this->db->select('*');
		$this->db->order_by('date','desc');
		$query = $this->db->get('kunci_rakor');
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
                $row['date'] = date('d F Y',strtotime($row['date']));
				$row['admin_name'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));

				if($this->session->userdata('admin_group_id') == 1){
					$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteDataRakor('.$row['id'].')" class="btn btn-danger" style="font-weight:bold; border-radius:10px;"><i class="fa fa-close"></i> </a>';
				}else {
					$row['actions'] = '-';
				}
				
                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rakor()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){
			$this->db->delete('kunci_rakor',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

	public function form_rakor()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] = $this->db->select('*')->get_where('produk', array('status' => 'PUBLISH', 'kategori_produk' => 1))->result_array();
			$this->load->view('produksi/form_rakor', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_rakor()
	{
		$date = $this->input->post('date');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'date' => date('Y-m-d', strtotime($date)),
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->insert('kunci_rakor', $arr_insert);

		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error','<b>ERROR</b>');
			redirect('/stock_opname#rakor');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success','<b>SAVED</b>');
			redirect('admin/stock_opname');
		}
	}

	public function form_produksi_harian()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['kalibrasi'] = $this->db->select('*')->get_where('pmm_kalibrasi', array('status' => 'PUBLISH'))->result_array();
			$this->load->view('produksi/form_produksi_harian', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_produksi_harian()
	{
		$date_prod = $this->input->post('date_prod');
		$no_prod = $this->input->post('no_prod');
		$total_product = $this->input->post('total_product');
		$memo = $this->input->post('memo');
		$attach = $this->input->post('files[]');

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'date_prod' => date('Y-m-d', strtotime($date_prod)),	
			'no_prod' => $no_prod,			
			'memo' => $memo,
			'attach' => $attach,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);

		if ($this->db->insert('pmm_produksi_harian', $arr_insert)) {
			$produksi_harian_id = $this->db->insert_id();

			if (!file_exists('uploads/produksi_harian')) {
			    mkdir('uploads/produksi_harian', 0777, true);
			}

			$data = [];
			$count = count($_FILES['files']['name']);
			for ($i = 0; $i < $count; $i++) {

				if (!empty($_FILES['files']['name'][$i])) {

					$_FILES['file']['name'] = $_FILES['files']['name'][$i];
					$_FILES['file']['type'] = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error'] = $_FILES['files']['error'][$i];
					$_FILES['file']['size'] = $_FILES['files']['size'][$i];

					$config['upload_path'] = 'uploads/produksi_harian';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'produksi_harian_id' => $produksi_harian_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('pmm_lampiran_produksi_harian', $data[$i]);
					}
				}
			}

			for ($i = 1; $i <= $total_product; $i++) {
				$product_id = $this->input->post('product_' . $i);
				$start = $this->input->post('start_' . $i);
				$end = $this->input->post('end_' . $i);
				$duration = $this->input->post('duration_' . $i);
				$start = $this->input->post('start_' . $i);
				$hours = $this->input->post('hours_' . $i);
				$day = $this->input->post('day_' . $i);
				$use = $this->input->post('use_' . $i);
				$capacity = $this->input->post('capacity_' . $i);
				if (!empty($product_id)) {
					$arr_detail = array(
						'produksi_harian_id' => $produksi_harian_id,
						'product_id' => $product_id,
						'start' => $start,
						'end' => $end,
						'duration' => $duration,
						'hours' => $hours,
						'day' => $day,
						'use' => $use,
						'capacity' => $capacity,
					);

					$this->db->insert('pmm_produksi_harian_detail', $arr_detail);
				} else {
					redirect('produksi_harian/produksi_harian');
					exit();
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error','REJECTED');
			redirect('produksi_harian/produksi_harian');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success','SAVED');
			redirect('admin/produksi_harian');
		}
	}
	
	public function table_produksi_harian()
	{   
        $data = array();
		$filter_date = $this->input->post('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('kb.date_prod >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('kb.date_prod <=',date('Y-m-d',strtotime($arr_date[1])));
		}
        $this->db->select('kb.id, kb.date_prod, kb.no_prod, SUM(phd.duration) as duration, SUM(phd.use) as used, SUM(phd.use) / SUM(phd.duration) as capacity, lk.produksi_harian_id, lk.lampiran, kb.memo, kb.created_by, kb.created_on, kb.status');
		$this->db->join('pmm_produksi_harian_detail phd','kb.id = phd.produksi_harian_id','left');
		$this->db->join('pmm_lampiran_produksi_harian lk', 'kb.id = lk.produksi_harian_id','left');
		$this->db->where('kb.date_prod >=', date('2023-08-01'));
		$this->db->where('kb.status','PUBLISH');
		$this->db->group_by('kb.id');	
		$this->db->order_by('kb.date_prod','desc');			
		$query = $this->db->get('pmm_produksi_harian kb');
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
                $row['date_prod'] = date('d-m-Y',strtotime($row['date_prod']));
				$row['no_prod'] = $row['no_prod'];
				$row['duration'] = $row['duration'];
				$row['used'] = $row['used'];
				$row['capacity'] = number_format($row['capacity'],2,',','.');
				$row['memo'] = $row['memo'];
				$row['lampiran'] = "<a target='_blank' href=" . base_url('uploads/produksi_harian/' . $row["lampiran"]) . ">" . $row["lampiran"] . "</a>";
				$row['admin_name'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));
				$row['status'] = $this->pmm_model->GetStatus4($row['status']);
				$row['view'] = '<a href="'.site_url().'produksi/data_produksi_harian/'.$row['id'].'" class="btn btn-warning" style="border-radius:10px"><i class="glyphicon glyphicon-folder-open"></i> </a>';
				$row['print'] = '<a href="'.site_url().'produksi/cetak_produksi_harian/'.$row['id'].'" target="_blank" class="btn btn-info" style="border-radius:10px"><i class="fa fa-print"></i> </a>';
                if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 11 || $this->session->userdata('admin_group_id') == 15){
					$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteProduksiHarian('.$row['id'].')" class="btn btn-danger" style="font-weight:bold; border-radius:10px;"><i class="fa fa-close"></i> </a>';
				}else {
					$row['actions'] = '<button type="button" class="btn btn-danger" style="font-weight:bold; border-radius:10px;"><i class="fa fa-ban"></i> No Access</button>';
				}

                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }
	
	public function data_produksi_harian($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['tes'] = '';
			$data['prod'] = $this->db->get_where("pmm_produksi_harian", ["id" => $id])->row_array();
			$data['lampiran'] = $this->db->get_where("pmm_lampiran_produksi_harian", ["produksi_harian_id" => $id])->result_array();
			$data['details'] = $this->db->query("SELECT * FROM `pmm_produksi_harian_detail` WHERE produksi_harian_id = '$id'")->result_array();
			$this->load->view('produksi/data_produksi_harian', $data);
		} else {
			redirect('admin');
		}
	}
	
	public function hapus_produksi_harian($id)
    {
        $this->db->trans_start(); # Starting Transaction


        $this->db->delete('pmm_produksi_harian', array('id' => $id));
		$this->db->delete('pmm_produksi_harian_detail', array('produksi_harian_id' => $id));
		//$this->db->update('pmm_produksi_harian',array('status'=>'DELETED'),array('id'=>$id));

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            $this->session->set_flashdata('notif_error', 'Gagal Menghapus Produksi Harian');
            redirect('produksi/data_produksi_harian');
        } else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
            $this->session->set_flashdata('notif_success', 'Berhasil Menghapus Produksi Harian');
            redirect("admin/produksi_harian");
        }
    }

	public function form_kalibrasi()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] = $this->db->select('*')->get_where('produk', array('status' => 'PUBLISH'))->result_array();
			$data['measures'] = $this->db->select('*')->get_where('pmm_measures', array('status' => 'PUBLISH'))->result_array();
			$this->load->view('produksi/form_kalibrasi', $data);
		} else {
			redirect('admin');
		}
	}
	
	public function submit_kalibrasi()
	{
		$date_kalibrasi = $this->input->post('date_kalibrasi');
		$no_kalibrasi = $this->input->post('no_kalibrasi');
		$jobs_type = $this->input->post('jobs_type');
		
		$produk_a = $this->input->post('produk_a');
		$produk_b = $this->input->post('produk_b');
		$produk_c = $this->input->post('produk_c');
		$produk_d = $this->input->post('produk_d');
		$produk_e = $this->input->post('produk_e');
		$produk_f = $this->input->post('produk_f');
		$measure_a = $this->input->post('measure_a');
		$measure_b = $this->input->post('measure_b');
		$measure_c = $this->input->post('measure_c');
		$measure_d = $this->input->post('measure_d');
		$measure_e = $this->input->post('measure_e');
		$measure_f = $this->input->post('measure_f');
		$presentase_a = $this->input->post('presentase_a');
		$presentase_b = $this->input->post('presentase_b');
		$presentase_c = $this->input->post('presentase_c');
		$presentase_d = $this->input->post('presentase_d');
		$presentase_e = $this->input->post('presentase_e');
		$presentase_f = $this->input->post('presentase_f');
		
		$memo = $this->input->post('memo');
		$attach = $this->input->post('files[]');


		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'date_kalibrasi' => date('Y-m-d', strtotime($date_kalibrasi)),
			'no_kalibrasi' => $no_kalibrasi,
			'jobs_type' => $jobs_type,
			'produk_a' => $produk_a,
			'produk_b' => $produk_b,
			'produk_c' => $produk_c,
			'produk_d' => $produk_d,
			'produk_e' => $produk_e,
			'produk_f' => $produk_f,
			'measure_a' => $measure_a,
			'measure_b' => $measure_b,
			'measure_c' => $measure_c,
			'measure_d' => $measure_d,
			'measure_e' => $measure_e,
			'measure_f' => $measure_f,
			'presentase_a' => $presentase_a,
			'presentase_b' => $presentase_b,
			'presentase_c' => $presentase_c,
			'presentase_d' => $presentase_d,
			'presentase_e' => $presentase_e,
			'presentase_f' => $presentase_f,
			'memo' => $memo,
			'attach' => $attach,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);

		if ($this->db->insert('pmm_kalibrasi', $arr_insert)) {
			$kalibrasi_id = $this->db->insert_id();

			if (!file_exists('uploads/kalibrasi')) {
			    mkdir('uploads/kalibrasi', 0777, true);
			}

			$data = [];
			$count = count($_FILES['files']['name']);
			for ($i = 0; $i < $count; $i++) {

				if (!empty($_FILES['files']['name'][$i])) {

					$_FILES['file']['name'] = $_FILES['files']['name'][$i];
					$_FILES['file']['type'] = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error'] = $_FILES['files']['error'][$i];
					$_FILES['file']['size'] = $_FILES['files']['size'][$i];

					$config['upload_path'] = 'uploads/kalibrasi';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'kalibrasi_id' => $kalibrasi_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('pmm_lampiran_kalibrasi', $data[$i]);
					}
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error', '<b>REJECTED</b>');
			redirect('produksi_harian/kalibrasi');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success', '<b>SAVED</b>');
			redirect('admin/produksi_harian');
		}
	}

	public function table_kalibrasi()
	{   
        $data = array();
		$filter_date = $this->input->post('filter_date');
		if(!empty($filter_date)){
			$arr_date = explode(' - ', $filter_date);
			$this->db->where('kb.date_kalibrasi >=',date('Y-m-d',strtotime($arr_date[0])));
			$this->db->where('kb.date_kalibrasi <=',date('Y-m-d',strtotime($arr_date[1])));
		}
		$jobs_type = $this->input->post('jobs_type');
		if(!empty($jobs_type)){
			$this->db->where('kb.jobs_type',$jobs_type);
		}
        $this->db->select('kb.id, kb.jobs_type, kb.no_kalibrasi, kb.date_kalibrasi, lk.kalibrasi_id, lk.lampiran, kb.created_by, kb.created_on, kb.status');
		$this->db->join('pmm_lampiran_kalibrasi lk', 'kb.id = lk.kalibrasi_id','left');
		$this->db->where('kb.date_kalibrasi >=', date('2023-08-01'));
		$this->db->order_by('kb.created_on','desc');		
		$query = $this->db->get('pmm_kalibrasi kb');
		
       if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['jobs_type'] = $row["jobs_type"];
                $row['tanggal_kalibrasi'] = date('d-m-Y',strtotime($row['date_kalibrasi']));
                $row['no_kalibrasi'] = $row['no_kalibrasi'];
				$row['lampiran'] = "<a  target='_blank' href=" . base_url('uploads/kalibrasi/' . $row["lampiran"]) . ">" . $row["lampiran"] . "</a>";  
                $row['admin_name'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));
				$row['status'] = $this->pmm_model->GetStatus4($row['status']);
				$row['view'] = '<a href="'.site_url().'produksi/data_kalibrasi/'.$row['id'].'" class="btn btn-warning" style="border-radius:10px";><i class="glyphicon glyphicon-folder-open"></i> </a>';
				$row['print'] = '<a href="'.site_url().'produksi/cetak_kalibrasi/'.$row['id'].'" target="_blank" class="btn btn-info" style="border-radius:10px"><i class="fa fa-print"></i> </a>';
				if($this->session->userdata('admin_group_id') == 1 || $this->session->userdata('admin_group_id') == 5 || $this->session->userdata('admin_group_id') == 6 || $this->session->userdata('admin_group_id') == 11 || $this->session->userdata('admin_group_id') == 15){
					$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteDataKalibrasi('.$row['id'].')" class="btn btn-danger" style="font-weight:bold; border-radius:10px;"><i class="fa fa-close"></i> </a>';
				}else {
					$row['actions'] = '<button type="button" class="btn btn-danger" style="font-weight:bold; border-radius:10px;"><i class="fa fa-ban"></i> No Access</button>';
				}
				
                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }
          
	public function approve_kalibrasi($id)
    {
        $this->db->set("status", "OPEN");
        $this->db->where("id", $id);
        $this->db->update("kalibrasi");

        $this->db->update('kalibrasi_detail', array('status' => 'OPEN'), array('kalibrasi_id' => $id));
        $this->session->set_flashdata('notif_success', 'Berhasil menyetujui Kalibrasi');
        redirect("admin/produksi_harian");
    }

    public function closed_kalibrasi($id)
    {
        $this->db->set("status", "CLOSED");
        $this->db->where("id", $id);
        $this->db->update("kalibrasi");

        $this->db->update('kalibrasi_detail', array('status' => 'CLOSED'), array('kalibrasi_id' => $id));
        $this->session->set_flashdata('notif_success', 'Berhasil Melakukan Closed Kalibrasi');
        redirect("admin/produksi_harian");
    }

    public function hapus_kalibrasi($id)
    {
        $this->db->trans_start(); # Starting Transaction


        $this->db->delete('pmm_kalibrasi', array('id' => $id));

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            $this->session->set_flashdata('notif_error', 'Gagal Menghapus Kalibrasi');
            redirect('admin/produksi_harian');
        } else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
            $this->session->set_flashdata('notif_success', 'Berhasil Menghapus Kalibrasi');
            redirect("admin/produksi_harian");
        }
    }

	public function cetak_kalibrasi($id){

		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$data['row'] = $this->db->get_where('pmm_kalibrasi',array('id'=>$id))->row_array();
        $html = $this->load->view('produksi/cetak_kalibrasi',$data,TRUE);
        $row = $this->db->get_where('pmm_kalibrasi',array('id'=>$id))->row_array();

        $pdf->SetTitle($row['no_kalibrasi']);
        $pdf->nsi_html($html);
        $pdf->Output($row['no_kalibrasi'].'.pdf', 'I');
	}

}
?>