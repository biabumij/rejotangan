<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rak extends Secure_Controller {

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
	
	public function form_rencana_kerja()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] =  $this->db->select('*')
			->from('produk p')
			->where("p.status = 'PUBLISH'")
			->where("p.kategori_produk = 2 ")
			->order_by('nama_produk','asc')
			->get()->result_array();

			$data['komposisi'] = $this->db->select('id, jobs_type,date_agregat')->order_by('date_agregat','desc')->get_where('pmm_agregat',array('status'=>'PUBLISH'))->result_array();
			$data['boulder'] = $this->pmm_model->getMatByPenawaranBoulder();
			$data['solar'] = $this->pmm_model->getMatByPenawaranRencanaKerjaSolar();
			$data['bp'] = $this->pmm_model->getMatByPenawaranRencanaKerjaBP();
			$data['tm'] = $this->pmm_model->getMatByPenawaranRencanaKerjaTM();
			$data['wl'] = $this->pmm_model->getMatByPenawaranRencanaKerjaWL();
			$this->load->view('rak/form_rencana_kerja', $data);
		} else {
			redirect('admin');
		}
	}
	
	public function submit_rencana_kerja()
	{
		$tanggal_rencana_kerja = $this->input->post('tanggal_rencana_kerja');
		$vol_produk_a =  str_replace('.', '', $this->input->post('vol_produk_a'));
		$vol_produk_a =  str_replace(',', '.', $vol_produk_a);
		$vol_produk_b =  str_replace('.', '', $this->input->post('vol_produk_b'));
		$vol_produk_b =  str_replace(',', '.', $vol_produk_b);
		$vol_produk_c =  str_replace('.', '', $this->input->post('vol_produk_c'));
		$vol_produk_c =  str_replace(',', '.', $vol_produk_c);
		$vol_produk_d =  str_replace('.', '', $this->input->post('vol_produk_d'));
		$vol_produk_d =  str_replace(',', '.', $vol_produk_d);
		$vol_produk_e =  str_replace('.', '', $this->input->post('vol_produk_e'));
		$vol_produk_e =  str_replace(',', '.', $vol_produk_e);
		$vol_produk_f =  str_replace('.', '', $this->input->post('vol_produk_f'));
		$vol_produk_f =  str_replace(',', '.', $vol_produk_f);

		$price_a =  str_replace('.', '', $this->input->post('price_a'));
		$price_b =  str_replace('.', '', $this->input->post('price_b'));
		$price_c =  str_replace('.', '', $this->input->post('price_c'));
		$price_d =  str_replace('.', '', $this->input->post('price_d'));
		$price_e =  str_replace('.', '', $this->input->post('price_e'));
		$price_f =  str_replace('.', '', $this->input->post('price_f'));

		$vol_boulder =  str_replace('.', '', $this->input->post('vol_boulder'));
		$vol_boulder =  str_replace(',', '.', $vol_boulder);
		$boulder =  str_replace('.', '', $this->input->post('boulder'));

		$stone_crusher =  str_replace('.', '', $this->input->post('stone_crusher'));
		$wheel_loader =  str_replace('.', '', $this->input->post('wheel_loader'));
		$maintenance =  str_replace('.', '', $this->input->post('maintenance'));
		$bbm_solar =  str_replace('.', '', $this->input->post('bbm_solar'));
		$tangki =  str_replace('.', '', $this->input->post('tangki'));
		$timbangan =  str_replace('.', '', $this->input->post('timbangan'));

		$overhead =  str_replace('.', '', $this->input->post('overhead'));

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'tanggal_rencana_kerja' =>  date('Y-m-d', strtotime($tanggal_rencana_kerja)),
			'vol_produk_a' => $vol_produk_a,
			'vol_produk_b' => $vol_produk_b,
			'vol_produk_c' => $vol_produk_c,
			'vol_produk_d' => $vol_produk_d,
			'vol_produk_e' => $vol_produk_e,
			'vol_produk_f' => $vol_produk_f,
			'price_a' => $price_a,
			'price_b' => $price_b,
			'price_c' => $price_c,
			'price_d' => $price_d,
			'price_e' => $price_e,
			'price_f' => $price_f,
			'vol_boulder' => $vol_boulder,
			'boulder' => $boulder,
			'stone_crusher' => $stone_crusher,
			'wheel_loader' => $wheel_loader,
			'maintenance' => $maintenance,
			'bbm_solar' => $bbm_solar,
			'tangki' => $tangki,
			'timbangan' => $timbangan,
			'overhead' => $overhead,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);
		
		if ($this->db->insert('rak', $arr_insert)) {
			$rak_id = $this->db->insert_id();

			if (!file_exists('uploads/rak')) {
			    mkdir('uploads/rak', 0777, true);
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

					$config['upload_path'] = 'uploads/rak';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'rak_id' => $rak_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('lampiran_rak', $data[$i]);
					}
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error','<b>ERROR</b>');
			redirect('admin/rencana_kerja');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success','<b>SAVED</b>');
			redirect('admin/rencana_kerja');
		}
	}
	
	public function table_rencana_kerja()
	{   
        $data = array();

        $this->db->select('rak.*, lk.lampiran, rak.status');		
		$this->db->join('lampiran_rak lk', 'rak.id = lk.rak_id','left');
		$this->db->where('rak.status','PUBLISH');
		$this->db->order_by('rak.tanggal_rencana_kerja','desc');			
		$query = $this->db->get('rak rak');
		
       	if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['tanggal_rencana_kerja'] = date('d F Y',strtotime($row['tanggal_rencana_kerja']));
				$row['lampiran'] = '<a href="' . base_url('uploads/rak/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';  
				$row['created_by'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));
				$row['updated_by'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['updated_by']),'admin_name');
                $row['updated_on'] = date('d/m/Y H:i:s',strtotime($row['updated_on']));

				$admin_id = $this->session->userdata('admin_id');
				$approval = $this->db->select('*')
				->from('tbl_admin')
				->where("admin_id = $admin_id ")
				->get()->row_array();
				$edit_rap =  $approval['edit_rap'];
				$delete_rap =  $approval['delete_rap'];
				
				if($edit_rap == 1){
					$row['edit'] = '<a href="'.site_url().'rak/sunting_rencana_kerja/'.$row['id'].'" class="btn btn-warning" style="border-radius:5px;"><i class="fa fa-edit"></i> </a>';
				}else {
					$row['edit'] = '-';
				}

				if($delete_rap == 1){
					$row['actions'] = '<a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger" style="border-radius:5px;"><i class="fa fa-close"></i> </a>';
				}else {
					$row['actions'] = '-';
				}

                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rencana_kerja()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){

			$file = $this->db->select('ag.lampiran')
			->from('lampiran_rak ag')
			->where("ag.rak_id = $id")
			->get()->row_array();

			$path = './uploads/rak/'.$file['lampiran'];
			chmod($path, 0777);
			unlink($path);

			$this->db->delete('lampiran_rak',array('rak_id'=>$id));
			$this->db->delete('rak',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

	public function sunting_rencana_kerja($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['tes'] = '';
			$data['rak'] = $this->db->get_where("rak", ["id" => $id])->row_array();
			$data['lampiran'] = $this->db->get_where("lampiran_rak", ["rak_id" => $id])->result_array();
			$data['komposisi'] = $this->db->select('id, jobs_type,date_agregat')->order_by('date_agregat','desc')->get_where('pmm_agregat',array('status'=>'PUBLISH'))->result_array();
			$data['semen'] = $this->pmm_model->getMatByPenawaranRencanaKerjaSemen();
			$data['pasir'] = $this->pmm_model->getMatByPenawaranRencanaKerjaPasir();
			$data['batu1020'] = $this->pmm_model->getMatByPenawaranRencanaKerjaBatu1020();
			$data['batu2030'] = $this->pmm_model->getMatByPenawaranRencanaKerjaBatu2030();
			$data['solar'] = $this->pmm_model->getMatByPenawaranRencanaKerjaSolar();
			$data['tm'] = $this->pmm_model->getMatByPenawaranRencanaKerjaTM();
			$data['exc'] = $this->pmm_model->getMatByPenawaranRencanaKerjaEXC();
			$data['tr'] = $this->pmm_model->getMatByPenawaranRencanaKerjaTransferSemen();
			$rak = $this->db->get_where("rak", ["id" => $id])->row_array();
			$data['tanggal'] = date('d-m-Y',strtotime($rak['tanggal_rencana_kerja']));
			$this->load->view('rak/sunting_rencana_kerja', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_sunting_rencana_kerja()
	{

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); #

		$id = $this->input->post('id');
		$tanggal_rencana_kerja = $this->input->post('tanggal_rencana_kerja');
		$vol_produk_a =  str_replace('.', '', $this->input->post('vol_produk_a'));
		$vol_produk_a =  str_replace(',', '.', $vol_produk_a);
		$vol_produk_b =  str_replace('.', '', $this->input->post('vol_produk_b'));
		$vol_produk_b =  str_replace(',', '.', $vol_produk_b);
		$vol_produk_c =  str_replace('.', '', $this->input->post('vol_produk_c'));
		$vol_produk_c =  str_replace(',', '.', $vol_produk_c);
		$vol_produk_d =  str_replace('.', '', $this->input->post('vol_produk_d'));
		$vol_produk_d =  str_replace(',', '.', $vol_produk_d);
		$vol_produk_e =  str_replace('.', '', $this->input->post('vol_produk_e'));
		$vol_produk_e =  str_replace(',', '.', $vol_produk_e);
		$vol_produk_f =  str_replace('.', '', $this->input->post('vol_produk_f'));
		$vol_produk_f =  str_replace(',', '.', $vol_produk_f);

		$price_a =  str_replace('.', '', $this->input->post('price_a'));
		$price_b =  str_replace('.', '', $this->input->post('price_b'));
		$price_c =  str_replace('.', '', $this->input->post('price_c'));
		$price_d =  str_replace('.', '', $this->input->post('price_d'));
		$price_e =  str_replace('.', '', $this->input->post('price_e'));
		$price_f =  str_replace('.', '', $this->input->post('price_f'));

		$vol_boulder =  str_replace('.', '', $this->input->post('vol_boulder'));
		$vol_boulder =  str_replace(',', '.', $vol_boulder);
		$boulder =  str_replace('.', '', $this->input->post('boulder'));

		$stone_crusher =  str_replace('.', '', $this->input->post('stone_crusher'));
		$wheel_loader =  str_replace('.', '', $this->input->post('wheel_loader'));
		$maintenance =  str_replace('.', '', $this->input->post('maintenance'));
		$bbm_solar =  str_replace('.', '', $this->input->post('bbm_solar'));
		$tangki =  str_replace('.', '', $this->input->post('tangki'));
		$timbangan =  str_replace('.', '', $this->input->post('timbangan'));

		$overhead =  str_replace('.', '', $this->input->post('overhead'));

		$arr_update = array(
			'tanggal_rencana_kerja' =>  date('Y-m-d', strtotime($tanggal_rencana_kerja)),
			'vol_produk_a' => $vol_produk_a,
			'vol_produk_b' => $vol_produk_b,
			'vol_produk_c' => $vol_produk_c,
			'vol_produk_d' => $vol_produk_d,
			'vol_produk_e' => $vol_produk_e,
			'vol_produk_f' => $vol_produk_f,
			'price_a' => $price_a,
			'price_b' => $price_b,
			'price_c' => $price_c,
			'price_d' => $price_d,
			'price_e' => $price_e,
			'price_f' => $price_f,
			'vol_boulder' => $vol_boulder,
			'boulder' => $boulder,
			'stone_crusher' => $stone_crusher,
			'wheel_loader' => $wheel_loader,
			'maintenance' => $maintenance,
			'bbm_solar' => $bbm_solar,
			'tangki' => $tangki,
			'timbangan' => $timbangan,
			'overhead' => $overhead,
			'status' => 'PUBLISH',
			'updated_by' => $this->session->userdata('admin_id'),
			'updated_on' => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);
			if ($this->db->update('rak', $arr_update)) {
				
			}

			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				$this->session->set_flashdata('notif_error', '<b>ERROR</b>');
				redirect('admin/rencana_kerja');
			} else {
				# Everything is Perfect. 
				# Committing data to the database.
				$this->db->trans_commit();
				$this->session->set_flashdata('notif_success', '<b>SAVED</b>');
				redirect('admin/rencana_kerja');
			}
	}

	public function form_rencana_cash_flow()
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['products'] =  $this->db->select('*')
			->from('produk p')
			->where("p.status = 'PUBLISH'")
			->where("p.kategori_produk = 2 ")
			->order_by('nama_produk','asc')
			->get()->result_array();
			$this->load->view('rak/form_rencana_cash_flow', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_rencana_cash_flow()
	{
		$tanggal_rencana_kerja = $this->input->post('tanggal_rencana_kerja');
		$biaya_bahan =  str_replace('.', '', $this->input->post('biaya_bahan'));
		$biaya_alat =  str_replace('.', '', $this->input->post('biaya_alat'));
		$biaya_bank =  str_replace('.', '', $this->input->post('biaya_bank'));
		$overhead =  str_replace('.', '', $this->input->post('overhead'));
		$termin =  str_replace('.', '', $this->input->post('termin'));
		$pajak_keluaran =  str_replace('.', '', $this->input->post('pajak_keluaran'));
		$pajak_masukan =  str_replace('.', '', $this->input->post('pajak_masukan'));
		$penerimaan =  str_replace('.', '', $this->input->post('penerimaan'));
		$pengembalian =  str_replace('.', '', $this->input->post('pengembalian'));

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well 

		$arr_insert = array(
			'tanggal_rencana_kerja' =>  date('Y-m-d', strtotime($tanggal_rencana_kerja)),
			'biaya_bahan' => $biaya_bahan,
			'biaya_alat' => $biaya_alat,
			'biaya_bank' => $biaya_bank,
			'overhead' => $overhead,
			'termin' => $termin,
			'pajak_keluaran' => $pajak_keluaran,
			'pajak_masukan' => $pajak_masukan,
			'penerimaan' => $penerimaan,
			'pengembalian' => $pengembalian,
			'status' => 'PUBLISH',
			'created_by' => $this->session->userdata('admin_id'),
			'created_on' => date('Y-m-d H:i:s')
		);
		
		if ($this->db->insert('rencana_cash_flow', $arr_insert)) {
			$rencana_cash_flow_id = $this->db->insert_id();

			if (!file_exists('uploads/rencana_cash_flow')) {
			    mkdir('uploads/rencana_cash_flow', 0777, true);
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

					$config['upload_path'] = 'uploads/rencana_cash_flow';
					$config['allowed_types'] = 'jpg|jpeg|png|pdf';
					$config['file_name'] = $_FILES['files']['name'][$i];

					$this->load->library('upload', $config);

					if ($this->upload->do_upload('file')) {
						$uploadData = $this->upload->data();
						$filename = $uploadData['file_name'];

						$data['totalFiles'][] = $filename;


						$data[$i] = array(
							'rencana_cash_flow_id' => $rencana_cash_flow_id,
							'lampiran'  => $data['totalFiles'][$i]
						);

						$this->db->insert('lampiran_rencana_cash_flow', $data[$i]);
					}
				}
			}
		}


		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			$this->session->set_flashdata('notif_error','<b>ERROR</b>');
			redirect('admin/rencana_cash_flow');
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			$this->session->set_flashdata('notif_success','<b>SAVED</b>');
			redirect('admin/rencana_cash_flow');
		}
	}

	public function table_rencana_cash_flow()
	{   
        $data = array();

        $this->db->select('rak.*, lk.lampiran, rak.status');		
		$this->db->join('lampiran_rencana_cash_flow lk', 'rak.id = lk.rencana_cash_flow_id','left');
		$this->db->where('rak.status','PUBLISH');
		$this->db->order_by('rak.tanggal_rencana_kerja','desc');			
		$query = $this->db->get('rencana_cash_flow rak');
		
       	if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
                $row['no'] = $key+1;
				$row['tanggal_rencana_kerja'] = date('d F Y',strtotime($row['tanggal_rencana_kerja']));
				$row['lampiran'] = '<a href="' . base_url('uploads/rencana_cash_flow/' . $row['lampiran']) .'" target="_blank">' . $row['lampiran'] . '</a>';  
				$row['created_by'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['created_by']),'admin_name');
                $row['created_on'] = date('d/m/Y H:i:s',strtotime($row['created_on']));
				$row['updated_by'] = $this->crud_global->GetField('tbl_admin',array('admin_id'=>$row['updated_by']),'admin_name');
                $row['updated_on'] = date('d/m/Y H:i:s',strtotime($row['updated_on']));

				$admin_id = $this->session->userdata('admin_id');
				$approval = $this->db->select('*')
				->from('tbl_admin')
				->where("admin_id = $admin_id ")
				->get()->row_array();
				$edit_rap =  $approval['edit_rap'];
				$delete_rap =  $approval['delete_rap'];
				
				if($edit_rap == 1){
					$row['edit'] = '<a href="'.site_url().'rak/sunting_rencana_cash_flow/'.$row['id'].'" class="btn btn-warning" style="border-radius:5px;"><i class="fa fa-edit"></i> </a>';
				}else {
					$row['edit'] = '-';
				}

				if($delete_rap == 1){
					$row['delete'] = '<a href="javascript:void(0);" onclick="DeleteDataRencanaCashFlow('.$row['id'].')" class="btn btn-danger" style="border-radius:5px;"><i class="fa fa-close"></i> </a>';
				}else {
					$row['delete'] = '-';
				}

                $data[] = $row;
            }

        }
        echo json_encode(array('data'=>$data));
    }

	public function delete_rencana_cash_flow()
	{
		$output['output'] = false;
		$id = $this->input->post('id');
		if(!empty($id)){

			$file = $this->db->select('ag.lampiran')
			->from('lampiran_rencana_cash_flow ag')
			->where("ag.rencana_cash_flow_id = $id")
			->get()->row_array();

			$path = './uploads/rencana_cash_flow/'.$file['lampiran'];
			chmod($path, 0777);
			unlink($path);

			$this->db->delete('lampiran_rencana_cash_flow',array('rencana_cash_flow_id'=>$id));
			$this->db->delete('rencana_cash_flow',array('id'=>$id));
			{
				$output['output'] = true;
			}
		}
		echo json_encode($output);
	}

	public function sunting_rencana_cash_flow($id)
	{
		$check = $this->m_admin->check_login();
		if ($check == true) {
			$data['tes'] = '';
			$data['rak'] = $this->db->get_where("rencana_cash_flow", ["id" => $id])->row_array();
			$data['lampiran'] = $this->db->get_where("lampiran_rencana_cash_flow", ["rencana_cash_flow_id" => $id])->result_array();
			$rak = $this->db->get_where("rencana_cash_flow", ["id" => $id])->row_array();
			$data['tanggal'] = date('d-m-Y',strtotime($rak['tanggal_rencana_kerja']));
			$this->load->view('rak/sunting_rencana_cash_flow', $data);
		} else {
			redirect('admin');
		}
	}

	public function submit_sunting_rencana_cash_flow()
	{

		$this->db->trans_start(); # Starting Transaction
		$this->db->trans_strict(FALSE); #

			$id = $this->input->post('id');
			$tanggal_rencana_kerja = $this->input->post('tanggal_rencana_kerja');
			$biaya_bahan =  str_replace('.', '', $this->input->post('biaya_bahan'));
			$biaya_alat =  str_replace('.', '', $this->input->post('biaya_alat'));
			$biaya_bank =  str_replace('.', '', $this->input->post('biaya_bank'));
			$overhead =  str_replace('.', '', $this->input->post('overhead'));
			$termin =  str_replace('.', '', $this->input->post('termin'));
			$pajak_keluaran =  str_replace('.', '', $this->input->post('pajak_keluaran'));
			$pajak_masukan =  str_replace('.', '', $this->input->post('pajak_masukan'));
			$penerimaan =  str_replace('.', '', $this->input->post('penerimaan'));
			$pengembalian =  str_replace('.', '', $this->input->post('pengembalian'));

			$arr_update = array(
				'tanggal_rencana_kerja' =>  date('Y-m-d', strtotime($tanggal_rencana_kerja)),
				'biaya_bahan' => $biaya_bahan,
				'biaya_alat' => $biaya_alat,
				'biaya_bank' => $biaya_bank,
				'overhead' => $overhead,
				'termin' => $termin,
				'pajak_keluaran' => $pajak_keluaran,
				'pajak_masukan' => $pajak_masukan,
				'penerimaan' => $penerimaan,
				'pengembalian' => $pengembalian,
				'status' => 'PUBLISH',
				'updated_by' => $this->session->userdata('admin_id'),
				'updated_on' => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);
			if ($this->db->update('rencana_cash_flow', $arr_update)) {
				
			}

			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				$this->session->set_flashdata('notif_error', '<b>ERROR</b>');
				redirect('admin/rencana_cash_flow');
			} else {
				# Everything is Perfect. 
				# Committing data to the database.
				$this->db->trans_commit();
				$this->session->set_flashdata('notif_success', '<b>SAVED</b>');
				redirect('admin/rencana_cash_flow');
			}
	}

}
?>