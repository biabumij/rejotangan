<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

	public function __construct()
	{
        parent::__construct();
        // Your own constructor code
        $this->load->model(array('admin/m_admin','crud_global','m_themes','pages/m_pages','menu/m_menu','admin_access/m_admin_access','DB_model','member_back/m_member_back','m_member','pmm_model','admin/Templates'));
        $this->load->model('pmm_reports');
        $this->load->library('enkrip');
		$this->load->library('filter');
		$this->load->library('waktu');
		$this->load->library('session');
		$this->m_admin->check_login();
	}

	public function revenues()
	{

		
		$arr_date = $this->input->post('filter_date');
		if(empty($filter_date)){
			$filter_date = '-';
		}else {
			$filter_date = $arr_date;
		}
		$alphas = range('A', 'Z');
		$data['alphas'] = $alphas;
		$data['clients'] = $this->db->get_where('pmm_client',array('status'=>'PUBLISH'))->result_array();
		$data['arr_date'] = $arr_date;
		$this->load->view('pmm/ajax/reports/revenues',$data);
	}

	public function receipt_materials()
	{
		
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['arr'] =  $this->pmm_reports->ReceiptMaterialTagDetails($arr_date);
		$this->load->view('pmm/ajax/reports/receipt_materials',$data);

	}

	public function receipt_material_detail()
	{
		$id = $this->input->post('id');	
		$arr_date = $this->input->post('filter_date');
		$type = $this->input->post('type');
		$data['type'] = $type;
		$data['arr'] =  $this->pmm_reports->ReceiptMaterialTagDetails($id,$arr_date);
		$data['name'] = $this->input->post('name'); 
		$this->load->view('pmm/ajax/reports/receipt_materials_detail',$data);
	}


	public function material_usage()
	{

		$product_id = $this->input->post('product_id');
		$arr_date = $this->input->post('filter_date');

		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';

    		$arr_date_2 = $start_date.' - '.$end_date;

    		$total_revenue_now = $this->pmm_model->getRevenueAll($arr_date_2);
    		$total_revenue_before = $this->pmm_model->getRevenueAll($arr_date_2,true);
    	}else {



    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));

    		$total_revenue_now = $this->pmm_model->getRevenueAll($arr_date);
    		$total_revenue_before = $this->pmm_model->getRevenueAll($arr_date,true);
    	} 

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));

		if(!empty($product_id)){
			$data['product'] = $this->crud_global->GetField('pmm_product',array('id'=>$product_id),'product');
			$data['total_production'] = $this->pmm_reports->TotalProductions($product_id,$arr_date);
			$data['arr_compo'] = $this->pmm_reports->MaterialUsageCompoProduct($arr_date,$product_id);
			$this->load->view('pmm/ajax/reports/material_usage_product',$data);
		}else {

			$data['arr'] =  $this->pmm_reports->MaterialUsageReal($arr_date);
			$data['arr_compo'] = $this->pmm_reports->MaterialUsageCompo($arr_date);
			$data['total_revenue_now'] = $total_revenue_now;
			$data['total_revenue_before'] =  $total_revenue_before;
			$this->load->view('pmm/ajax/reports/material_usage',$data);	
		}
		
	}

	public function material_usage_detail()
	{
		$id = $this->input->post('id');	
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	} 

		$type = $this->input->post('type');
		$product_id = $this->input->post('product_id');
		$data['type'] = $type;
		if($type == 'compo' || $type == 'compo_cost' || $type == 'compo_now'){
			$data['arr'] =  $this->pmm_reports->MaterialUsageCompoDetails($id,$arr_date,$product_id);
		}else {
			$data['arr'] =  $this->pmm_reports->MaterialUsageDetails($id,$arr_date);	
		}

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['product_id'] = $product_id;
		$data['name'] = $this->input->post('name'); 
		$this->load->view('pmm/ajax/reports/material_usage_detail',$data);
	}

	public function material_remaining()
	{
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	} 

    	$date = array($start_date,$end_date);
		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));

		$data['arr'] =  $this->pmm_reports->MaterialRemainingReal($date);
		$data['arr_compo'] = $this->pmm_reports->MaterialRemainingCompo($date);
		$this->load->view('pmm/ajax/reports/material_remaining',$data);	
		

	}

	public function material_remaining_detail()
	{
		$id = $this->input->post('id');	
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	} 
    	$date = array($start_date,$end_date);
		$type = $this->input->post('type');
		$data['type'] = $type;
		if($type == 'compo'){
			$data['arr'] =  $this->pmm_reports->MaterialRemainingCompoDetails($id,$date);
		}else {
			$data['arr'] =  $this->pmm_reports->MaterialRemainingDetails($id,$arr_date);	
		}

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['name'] = $this->input->post('name'); 
		$this->load->view('pmm/ajax/reports/material_remaining_detail',$data);
	}

	public function equipments()
	{
		$arr_date = $this->input->post('filter_date');
		$supplier_id = $this->input->post('supplier_id');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}

    	$date = array($start_date,$end_date);
    	$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['arr'] =  $this->pmm_reports->EquipmentProd($date);
		$data['equipments'] =  $this->pmm_reports->EquipmentReports($date,$supplier_id);
		$data['solar'] =  $this->pmm_reports->EquipmentUsageReal($date,true);
		$this->load->view('pmm/ajax/reports/equipments',$data);

	}

	public function equipments_detail()
	{
		$id = $this->input->post('id');	
		$arr_date = $this->input->post('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}
    	$date = array($start_date,$end_date);
		$supplier_id = $this->input->post('supplier_id');;
		$data['equipments'] = $this->pmm_reports->EquipmentReportsDetails($id,$date,$supplier_id);
		$data['name'] = $this->input->post('name');
		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$this->load->view('pmm/ajax/reports/equipments_detail',$data);
	}


	public function equipments_data_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$date = $this->input->get('filter_date');
		$supplier_id = $this->input->get('supplier_id');
		$tool_id = $this->input->get('tool_id');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));

			$filter_date = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
			$data['filter_date'] = $filter_date;
			$date = explode(' - ',$start_date.' - '.$end_date);
			$arr_data = $this->pmm_reports->EquipmentsData($date,$supplier_id,$tool_id);

			$data['data'] = $arr_data;
			$data['solar'] =  $this->pmm_reports->EquipmentUsageReal($date);
	        $html = $this->load->view('pmm/equipments_data_print',$data,TRUE);

	        
	        $pdf->SetTitle('Data Alat');
	        $pdf->nsi_html($html);
	        $pdf->Output('Data-Alat.pdf', 'I');

		}else {
			echo 'Please Filter Date First';
		}
		
	}

	public function revenues_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
			$filter_date = '-';
		}else {
			$arr_filter_date = explode(' - ', $arr_date);
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		$data['filter_date'] = $filter_date;
		$alphas = range('A', 'Z');
		$data['alphas'] = $alphas;
		$data['arr_date'] = $arr_date;
		$data['clients'] = $this->db->get_where('pmm_client',array('status'=>'PUBLISH'))->result_array();
        $html = $this->load->view('pmm/revenues_print',$data,TRUE);

        
        $pdf->SetTitle('LAPORAN PENDAPATAN USAHA');
        $pdf->nsi_html($html);
        $pdf->Output('LAPORAN-PENDAPATAN-USAHA.pdf', 'I');
	
	}
	
	public function monitoring_receipt_materials_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}

		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['arr'] =  $this->pmm_reports->ReceiptMaterialTagDetails($arr_date);
        $html = $this->load->view('pmm/monitoring_receipt_materials_print',$data,TRUE);

        
        $pdf->SetTitle('LAPORAN PENERIMAAN BAHAN');
        $pdf->nsi_html($html);
        $pdf->Output('LAPORAN-PENERIMAAN-BAHAN.pdf', 'I');
	
	}

	public function material_usage_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$product_id = $this->input->get('product_id');
		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';

    		$arr_date_2 = $start_date.' - '.$end_date;

    		$total_revenue_now = $this->pmm_model->getRevenueAll($arr_date_2);
    		$total_revenue_before = $this->pmm_model->getRevenueAll($arr_date_2,true);

    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));

    		$total_revenue_now = $this->pmm_model->getRevenueAll($arr_date);
    		$total_revenue_before = $this->pmm_model->getRevenueAll($arr_date,true);
    	}
    	
		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		if(empty($product_id)){
			$data['arr'] =  $this->pmm_reports->MaterialUsageReal($arr_date);
			$data['arr_compo'] = $this->pmm_reports->MaterialUsageCompo($arr_date);
			$data['total_revenue_now'] = $total_revenue_now;
			$data['total_revenue_before'] =  $total_revenue_before;
	        $html = $this->load->view('pmm/material_usage_print',$data,TRUE);
		}else {
			$data['product'] = $this->crud_global->GetField('pmm_product',array('id'=>$product_id),'product');
			$data['total_production'] = $this->pmm_reports->TotalProductions($product_id,$arr_date);
			$data['arr_compo'] = $this->pmm_reports->MaterialUsageCompoProduct($arr_date,$product_id);

			

	        $html = $this->load->view('pmm/material_usage_product_print',$data,TRUE);
		}
		 
        $pdf->SetTitle('LAPORAN PEMAKAIAN MATERIAL');
        $pdf->nsi_html($html);
        $pdf->Output('LAPORAN-PEMAKAIAN-MATERIAL.pdf', 'I');
	
	}

	public function material_remaining_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	} 

    	$date = array($start_date,$end_date);
		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));

		$data['arr'] =  $this->pmm_reports->MaterialRemainingReal($date);
		$data['arr_compo'] = $this->pmm_reports->MaterialRemainingCompo($date);

        $html = $this->load->view('pmm/material_remaining_print',$data,TRUE);

        
        $pdf->SetTitle('Materials Remaining');
        $pdf->nsi_html($html);
        $pdf->Output('Materials-Remaining.pdf', 'I');
	
	}


	public function monitoring_equipments_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		$supplier_id = $this->input->get('supplier_id');
		if(empty($arr_date)){
    		$month = date('Y-m');
    		$start_date = date('Y-m',strtotime('- 1month '.$month)).'-27';
    		$end_date = $month.'-26';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    	}

    	$date = array($start_date,$end_date);
    	$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
		$data['arr'] =  $this->pmm_reports->EquipmentProd($date);
		$data['equipments'] =  $this->pmm_reports->EquipmentReports($date,$supplier_id);
		$data['supplier'] = $this->crud_global->GetField('pmm_supplier',array('id'=>$supplier_id),'name');

        $html = $this->load->view('pmm/monitoring_equipments_print',$data,TRUE);

        
        $pdf->SetTitle('LAPORAN PEMAKAIAN ALAT');
        $pdf->nsi_html($html);
        $pdf->Output('LAPORAN-PEMAKAIAN-ALAT.pdf', 'I');
	
	}

	public function general_cost_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(0);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		$filter_type = $this->input->get('filter_type');
		if(empty($arr_date)){
    		$data['filter_date'] = '-';
    	}else {
    		$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    		$data['filter_date'] = date('d F Y',strtotime($start_date)).' - '.date('d F Y',strtotime($end_date));
    	} 

		


		if(!empty($arr_date)){
			$dt = explode(' - ', $arr_date);
    		$start_date = date('Y-m-d',strtotime($dt[0]));
    		$end_date = date('Y-m-d',strtotime($dt[1]));
    		$this->db->where('date >=',$start_date);
    		$this->db->where('date <=',$end_date);	
		}
		if(!empty($filter_type)){
			$this->db->where('type',$filter_type);
		}
		$this->db->order_by('date','desc');
		$this->db->where('status !=','DELETED');
		$arr = $this->db->get_where('pmm_general_cost');
		$data['arr'] =  $arr->result_array();

        $html = $this->load->view('pmm/general_cost_print',$data,TRUE);

        
        $pdf->SetTitle('General Cost');
        $pdf->nsi_html($html);
        $pdf->Output('General-Cost.pdf', 'I');
	
	}


	public function purchase_order_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_date = $this->input->get('filter_date');
		if(empty($arr_date)){
			$filter_date = '-';
		}else {
			$arr_filter_date = explode(' - ', $arr_date);
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		$data['filter_date'] = $filter_date;
		$data['w_date'] = $arr_date;
		$data['status'] = $this->input->post('status');
		$data['supplier_id'] = $this->input->post('supplier_id');
		$this->db->select('supplier_id');
		$this->db->where('status !=','DELETED');
		if(!empty($data['status'])){
			$this->db->where('supplier_id',$data['status']);
		}
		$this->db->group_by('supplier_id');
		$this->db->order_by('created_on','desc');
		$query = $this->db->get('pmm_purchase_order');

		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/purchase_order_print',$data,TRUE);

        
        $pdf->SetTitle('Purchase Order');
        $pdf->nsi_html($html);
        $pdf->Output('Purchase-Order.pdf', 'I');
	
	}


	public function product_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$tag_id = $this->input->get('product_id');

		if(!empty($tag_id)){
			$this->db->where('tag_id',$tag_id);	
		}
		$this->db->where('status !=','DELETED');
		$this->db->order_by('product','asc');
		$query = $this->db->get('pmm_product');
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$name = "'".$row['product']."'";
				$row['no'] = $key+1;
				$row['created_on'] = date('d F Y',strtotime($row['created_on']));
				$contract_price = $this->pmm_model->GetContractPrice($row['contract_price']);
				$row['contract_price'] = number_format($contract_price,2,',','.');
				$row['riel_price'] = number_format($this->pmm_model->GetRielPrice($row['id']),2,',','.');
				$row['composition'] = $this->crud_global->GetField('pmm_composition',array('id'=>$row['composition_id']),'composition_name');
				$row['tag_name'] = $this->crud_global->GetField('pmm_tags',array('id'=>$row['tag_id']),'tag_name');
				$arr_data[] = $row;
			}

		}

		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/product_print',$data,TRUE);

        
        $pdf->SetTitle('Product');
        $pdf->nsi_html($html);
        $pdf->Output('Product.pdf', 'I');
	
	}

	public function product_hpp_print()
	{
		$id = $this->input->get('id');
		$name = $this->input->get('name');
		if(!empty($id)){
			$this->load->library('pdf');
		

			$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
	        $pdf->setPrintHeader(true);
	        $pdf->SetFont('helvetica','',7); 
	        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
			$pdf->setHtmlVSpace($tagvs);
			        $pdf->AddPage('P');

			$arr_data = array();

			$output = $this->pmm_model->GetRielPriceDetail($id);

			$data['data'] = $output;
			$data['name'] = $name;
	        $html = $this->load->view('pmm/product_hpp_print',$data,TRUE);

	        
	        $pdf->SetTitle('Product-HPP');
	        $pdf->nsi_html($html);
	        $pdf->Output('Product-HPP-'.$name.'.pdf', 'I');
		}else {
			echo "Product Not Found";
		}
		
	
	}

	public function materials_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		
		$pdf->AddPage('P');

		$arr_data = array();
		$tag_id = $this->input->get('tag_id');

		$this->db->where('status !=','DELETED');
		if(!empty($tag_id)){
			$this->db->where('tag_id',$tag_id);
		}
		$this->db->order_by('material_name','asc');
		$query = $this->db->get('pmm_materials');
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$row['price'] = number_format($row['price'],2,',','.');
				$row['cost'] = number_format($row['cost'],2,',','.');
				$row['measure'] = $this->crud_global->GetField('pmm_measures',array('id'=>$row['measure']),'measure_name');
 				$row['created_on'] = date('d F Y',strtotime($row['created_on']));
 				$row['tag_name'] = $this->crud_global->GetField('pmm_tags',array('id'=>$row['tag_id']),'tag_name');
				$arr_data[] = $row;
			}

		}

		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/materials_print',$data,TRUE);

        
        $pdf->SetTitle('Materials');
        $pdf->nsi_html($html);
        $pdf->Output('Materials.pdf', 'I');
	
	}

	public function tools_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$this->db->order_by('tool','asc');
		$query = $this->db->get('pmm_tools');

		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$name = "'".$row['tool']."'";
				$total_cost = $this->db->select('SUM(cost) as total')->get_where('pmm_tool_detail',array('status'=>'PUBLISH','tool_id'=>$row['id']))->row_array();
				$row['total_cost'] = number_format($total_cost['total'],2,',','.');
				$row['measure'] = $this->crud_global->GetField('pmm_measures',array('id'=>$row['measure_id']),'measure_name');
				$row['tag'] = $this->crud_global->GetField('pmm_tags',array('id'=>$row['tag_id']),'tag_name');
 				$row['created_on'] = date('d F Y',strtotime($row['created_on']));
				$row['actions'] = '<a href="javascript:void(0);" onclick="FormDetail('.$row['id'].','.$name.')" class="btn btn-info"><i class="fa fa-search"></i> Detail</a> <a href="javascript:void(0);" onclick="OpenForm('.$row['id'].')" class="btn btn-primary"><i class="fa fa-edit"></i> </a> <a href="javascript:void(0);" onclick="DeleteData('.$row['id'].')" class="btn btn-danger"><i class="fa fa-close"></i> </a>';
				$arr_data[] = $row;
			}

		}
		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/tools_print',$data,TRUE);

        
        $pdf->SetTitle('Tools');
        $pdf->nsi_html($html);
        $pdf->Output('Tools.pdf', 'I');
	
	}

	public function measures_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$query = $this->db->get('pmm_measures');
		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/measures_print',$data,TRUE);

        
        $pdf->SetTitle('Satuan');
        $pdf->nsi_html($html);
        $pdf->Output('satuan.pdf', 'I');
	
	}

	public function composition_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$tag_id = $this->input->get('filter_product');
		$arr_tag = array();
		if(!empty($tag_id)){
			$query_tag = $this->db->select('id')->get_where('pmm_product',array('status'=>'PUBLISH','tag_id'=>$tag_id))->result_array();
			foreach ($query_tag as $pid) {
				$arr_tag[] = $pid['id'];
			}
		}
		$this->db->select('pc.*, pp.product');
		$this->db->where('pc.status !=','DELETED');
		if(!empty($tag_id)){
			$this->db->where_in('product_id',$arr_tag);
		}
		$this->db->join('pmm_product pp','pc.product_id = pp.id','left');
		$this->db->order_by('pc.created_on','desc');
		$query = $this->db->get('pmm_composition pc');
		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/composition_print',$data,TRUE);

        
        $pdf->SetTitle('Composition');
        $pdf->nsi_html($html);
        $pdf->Output('Composition.pdf', 'I');
	
	}

	public function supplier_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$this->db->order_by('name','asc');
		$query = $this->db->get('pmm_supplier');
		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/supplier_print',$data,TRUE);

        
        $pdf->SetTitle('Supplier');
        $pdf->nsi_html($html);
        $pdf->Output('Supplier.pdf', 'I');
	
	}

	public function client_print()
	{
		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$this->db->order_by('client_name','asc');
		$query = $this->db->get('pmm_client');
		$data['data'] = $query->result_array();	
	
		$this->load->library('pdf');
		$this->pdf->setPaper('A4', 'potrait');
		$this->pdf->filename = "laporan-client.pdf";
		$this->pdf->load_view('pmm/client_print', $data);
	
	}
	
	public function slump_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$query = $this->db->get('pmm_slump');
		$data['data'] = $query->result_array();
        $html = $this->load->view('pmm/slump_print',$data,TRUE);

        
        $pdf->SetTitle('Slump');
        $pdf->nsi_html($html);
        $pdf->Output('Slump.pdf', 'I');
	
	}

	public function tags_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		        $pdf->AddPage('P');

		$arr_data = array();
		$type = $this->input->get('type');
		$this->db->where('status !=','DELETED');
		if(!empty($type)){
			$this->db->where('tag_type',$type);
		}
		$this->db->order_by('tag_name','asc');
		$query = $this->db->get('pmm_tags');

		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$price = 0;
				if($row['tag_type'] == 'MATERIAL'){
					$get_price = $this->db->select('AVG(cost) as cost')->get_where('pmm_materials',array('status'=>'PUBLISH','tag_id'=>$row['id']))->row_array();
					if(!empty($get_price)){
						$price = $get_price['cost'];
					}
				}
				$row['price'] = number_format($price,2,',','.');
				$arr_data[] = $row;
			}

		}
		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/tags_print',$data,TRUE);

        
        $pdf->SetTitle('Tags');
        $pdf->nsi_html($html);
        $pdf->Output('Tags.pdf', 'I');
	
	}

	public function production_planning_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$this->db->where('status !=','DELETED');
		$this->db->order_by('created_on','desc');
		$query = $this->db->get('pmm_schedule');
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $key => $row) {
				$row['no'] = $key+1;
				$arr_date = explode(' - ', $row['schedule_date']);
				$row['schedule_name'] = $row['schedule_name'];
				$row['client_name'] = $this->crud_global->GetField('pmm_client',array('id'=>$row['client_id']),'client_name');
				$row['schedule_date'] = date('d F Y',strtotime($arr_date[0])).' - '.date('d F Y',strtotime($arr_date[1]));
				$row['created_on'] = date('d F Y',strtotime($row['created_on']));
				$row['week_1'] = $this->pmm_model->TotalSPOWeek($row['id'],1);
				$row['week_2'] = $this->pmm_model->TotalSPOWeek($row['id'],2);
				$row['week_3'] = $this->pmm_model->TotalSPOWeek($row['id'],3);
				$row['week_4'] = $this->pmm_model->TotalSPOWeek($row['id'],4);
				$row['status'] = $this->pmm_model->GetStatus($row['status']);
				
				$arr_data[] = $row;
			}

		}
		$data['data'] = $arr_data;
        $html = $this->load->view('pmm/production_planning_print',$data,TRUE);

        
        $pdf->SetTitle('cetak_poduction_planning');
        $pdf->nsi_html($html);
        $pdf->Output('production_planning.pdf', 'I');
	
	}
	
	public function receipt_matuse_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$supplier_id = $this->input->get('supplier_id');
		$purchase_order_no = $this->input->get('purchase_order_no');
		$filter_material = $this->input->get('filter_material');
		$start_date = false;
		$end_date = false;
		$total = 0;
		$total_convert = 0;
		$date = $this->input->get('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
			$filter_date = date('d F Y',strtotime($arr_date[0])).' - '.date('d F Y',strtotime($arr_date[1]));

			
			$data['filter_date'] = $filter_date;

			$arr_filter_mats = array();

			$no = 1;
			$this->db->select('ppo.supplier_id,prm.measure,ps.name,SUM(prm.volume) as total, SUM((prm.cost / prm.convert_value) * prm.display_volume) as total_price, prm.convert_value, SUM(prm.volume * prm.convert_value) as total_convert');
			if(!empty($start_date) && !empty($end_date)){
	            $this->db->where('prm.date_receipt >=',$start_date);
	            $this->db->where('prm.date_receipt <=',$end_date);
	        }
	        if(!empty($supplier_id)){
	            $this->db->where('ppo.supplier_id',$supplier_id);
	        }
	        if(!empty($filter_material)){
	            $this->db->where_in('prm.material_id',$filter_material);
	        }
	        if(!empty($purchase_order_no)){
	            $this->db->where('prm.purchase_order_id',$purchase_order_no);
	        }
			$this->db->where('ps.status','PUBLISH');
			$this->db->join('pmm_supplier ps','ppo.supplier_id = ps.id','left');
			$this->db->join('pmm_receipt_material prm','ppo.id = prm.purchase_order_id');
			$this->db->group_by('ppo.supplier_id');
			$this->db->order_by('ps.name','asc');
			$query = $this->db->get('pmm_purchase_order ppo');
			
			if($query->num_rows() > 0){

				foreach ($query->result_array() as $key => $sups) {

					$mats = array();
					$materials = $this->pmm_model->GetReceiptMatUse($sups['supplier_id'],$purchase_order_no,$start_date,$end_date,$arr_filter_mats);
					if(!empty($materials)){
						foreach ($materials as $key => $row) {
							$arr['no'] = $key + 1;
							$arr['measure'] = $row['measure'];
							$arr['material_name'] = $row['material_name'];
							
							$arr['real'] = number_format($row['total'],2,',','.');
							$arr['convert_value'] = number_format($row['convert_value'],2,',','.');
							$arr['total_convert'] = number_format($row['total_convert'],2,',','.');
							$arr['total_price'] = number_format($row['total_price'],2,',','.');
							$mats[] = $arr;
						}
						$sups['mats'] = $mats;
						$total += $sups['total_price'];
						$total_convert += $sups['total_convert'];
						$sups['no'] =$no;
						$sups['real'] = number_format($sups['total'],2,',','.');
						$sups['convert_value'] = number_format($sups['convert_value'],2,',','.');
						$sups['total_convert'] = number_format($sups['total_convert'],2,',','.');
						$sups['total_price'] = number_format($sups['total_price'],2,',','.');
						$sups['measure'] = '';
						$arr_data[] = $sups;
						$no++;
					}
					
					
				}
			}
			if(!empty($filter_material)){
				$total_convert = number_format($total_convert,0,',','.');
			}else {
				$total_convert = '';
			}

			
			$data['data'] = $arr_data;
			$data['total'] = $total;
			$data['total_convert'] = $total_convert;
	        $html = $this->load->view('pmm/receipt_matuse_report_print',$data,TRUE);

	        
	        $pdf->SetTitle('Penerimaan Bahan');
	        $pdf->nsi_html($html);
	        $pdf->Output('Penerimaan-Bahan.pdf', 'I');
		}else {
			echo 'Please Filter Date First';
		}
	
	}

	public function data_material_usage()
	{
		$supplier_id = $this->input->post('supplier_id');
		$filter_material = $this->input->post('filter_material');
		$start_date = false;
		$end_date = false;
		$total = 0;
		$total_convert = 0;
		$query = array();
		$date = $this->input->post('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
		}

    	$this->db->where(array(
    		'status'=>'PUBLISH',
    	));
    	if(!empty($filter_material)){
    		$this->db->where('id',$filter_material);
    	}
    	$this->db->order_by('nama_produk','asc');
    	$tags = $this->db->get_where('produk',array('status'=>'PUBLISH','kategori_produk'=>1))->result_array();

    	if(!empty($tags)){
    		?>
	        <table class="table table-center table-bordered table-condensed">
	        	<thead>
	        		<tr >
		        		<th class="text-center">No</th>
		        		<th class="text-center">Bahan</th>
		        		<th class="text-center">Rekanan</th>
		        		<th class="text-center">Satuan</th>
		        		<th class="text-center">Volume</th>
		        		<th class="text-center">Total</th>
		        	</tr>	
	        	</thead>
	        	<tbody>
	        		<?php
	        		$no=1;
	        		$total_total = 0;
	        		foreach ($tags as $tag) {
		    			$now = $this->pmm_reports->SumMaterialUsage($tag['id'],array($start_date,$end_date));

		    			
		    			$measure_name = $this->crud_global->GetField('pmm_measures',array('id'=>$tag['satuan']),'measure_name');
		    			if($now['volume'] > 0){
				        	
				        	?>
				        	<tr class="active" style="font-weight:bold;">
				        		<td class="text-center"><?php echo $no;?></td>
				        		<td colspan="2"><?php echo $tag['nama_produk'];?></td>
				        		<td class="text-center"><?php echo $measure_name;?></td>
				        		<td class="text-right"><?php echo number_format($now['volume'],2,',','.');?></td>
				        		<td class="text-right"><span class="pull-left">Rp. </span><?php echo number_format($now['total'],0,',','.');?></td>
				        	</tr>
				        	<?php
				        	$now_new = $this->pmm_reports->MatUseBySupp($tag['id'],array($start_date,$end_date),$now['volume'],$now['total']);
				        	if(!empty($now_new)){
				        		$no_2 = 1;
				        		foreach ($now_new as $new) {
					        		
					        		?>
					        		<!--<tr>
					        			<td class="text-center"><?= $no.'.'.$no_2;?></td>
					        			<td></td>
					        			<td><?php echo $new['supplier'];?></td>
					        			<td class="text-center"><?php echo $measure_name;?></td>
						        		<td class="text-right"><?php echo number_format($new['volume'],2,',','.');?></td>
						        		<td class="text-right"><span class="pull-left">Rp. </span><?php echo number_format($new['total'],0,',','.');?></td>
					        		</tr>-->
					        		<?php
					        		$no_2 ++;
					        	}
				        	}
				        	
				        	?>
				        	<tr style="height: 20px">
				        		<td colspan="6"></td>
				        	</tr>
				        	<?php

				        	$no++;
				        	$total_total += $now['total'];
					        
		    			}
		    		}
	        		?>
	        		<tr>
	        			<th colspan="5" class="text-right">TOTAL</th>
	        			<th class="text-right"><span class="pull-left">Rp. </span><?php echo number_format($total_total,0,',','.');?></th>
	        		</tr>
	        	</tbody>
	        </table>
	        <?php	
    	}


	}


	public function material_usage_prod_print()
	{
		$this->load->library('pdf');
	

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(true);
        
        $pdf->SetFont('helvetica','',7); 
        $tagvs = array('div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n'=> 0)));
		$pdf->setHtmlVSpace($tagvs);
		$pdf->AddPage('P');

		$arr_data = array();
		$supplier_id = $this->input->get('supplier_id');
		$purchase_order_no = $this->input->get('purchase_order_no');
		$filter_material = $this->input->get('filter_material');
		$start_date = false;
		$end_date = false;
		$total = 0;
		$total_convert = 0;
		$date = $this->input->get('filter_date');
		if(!empty($date)){
			$arr_date = explode(' - ',$date);
			$start_date = date('Y-m-d',strtotime($arr_date[0]));
			$end_date = date('Y-m-d',strtotime($arr_date[1]));
			$filter_date = date('d F Y',strtotime($arr_date[0])).' - '.date('d F Y',strtotime($arr_date[1]));

			
			$data['filter_date'] = $filter_date;

			$no = 1;
	    	$this->db->where(array(
	    		'status'=>'PUBLISH',
				'kategori_produk'=>1,
	    	));
	    	if(!empty($filter_material)){
	    		$this->db->where('id',$filter_material);
	    	}
	    	$this->db->order_by('nama_produk','asc');
	    	$query = $this->db->get('produk');
			
			if($query->num_rows() > 0){

				foreach ($query->result_array() as $key => $tag) {

					$now = $this->pmm_reports->SumMaterialUsage($tag['id'],array($start_date,$end_date));
	    			$measure_name = $this->crud_global->GetField('pmm_measures',array('id'=>$tag['satuan']),'measure_name');
	    			if($now['volume'] > 0){
	    				$tags['tag_name'] = $tag['nama_produk'];
	    				$tags['no'] = $no;
	    				$tags['volume'] = number_format($now['volume'],2,',','.');
	    				$tags['total'] = number_format($now['total'],2,',','.');
	    				$tags['measure'] = $measure_name;

	    				$now_new = $this->pmm_reports->MatUseBySupp($tag['id'],array($start_date,$end_date),$now['volume'],$now['total']);
			        	if(!empty($now_new)){
			        		$no_2 = 1;
			        		$supps = array();
			        		foreach ($now_new as $new) {

			        			$arr_supps['no'] = $no_2;
			        			$arr_supps['supplier'] = $new['supplier'];
			        			$arr_supps['volume'] = number_format($new['volume'],2,',','.');
			        			$arr_supps['total'] = number_format($new['total'],2,',','.');
			        			$supps[] = $arr_supps;
			        			$no_2 ++;
			        		}

			        		$tags['supps'] = $supps;
			        	}

						$arr_data[] = $tags;	
						$total += $now['total'];
	    			}
					$no++;
					
				}
			}

			
			$data['data'] = $arr_data;
			$data['total'] = $total;
			$data['custom_date'] = $this->input->get('custom_date');
	        $html = $this->load->view('produksi/material_usage_prod_print',$data,TRUE);

	        
	        $pdf->SetTitle('pemakaian-material');
	        $pdf->nsi_html($html);
	        $pdf->Output('pemakaian-material', 'I');
		}else {
			echo 'Please Filter Date First';
		}
	
	}

	public function exec()
	{
		
	}

	//BATAS RUMUS LAMA//
	public function laba_rugi($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date4 = '';
		$date3 = '';
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date3 	= date('2024-01-01',strtotime($date3));
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$date4 = date('Y-m-d', strtotime('-1 days 0 months ', strtotime($date1)));
			$filter_date = date('Y-m-d',strtotime($arr_filter_date[0])).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date_2 = date('Y-m-d',strtotime($date3)).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
		 <style type="text/css">
			body {
				font-family: helvetica;
			}

			table tr.table-active{
				/*background: linear-gradient(90deg, #fdcd3b 20%, #fdcd3b 40%, #e69500 80%);*/
				background-color: #eeeeee;
				font-size: 12px;
				font-weight: bold;
			}
				
			table tr.table-active2{
				background-color: #e69500;
				font-size: 12px;
				font-weight: bold;
				color: white;
			}
				
			table tr.table-active3{
				font-size: 12px;
			}
				
			table tr.table-active4{
				/*background: linear-gradient(90deg, #eeeeee 5%, #cccccc 50%, #cccccc 100%);*/
				font-weight: bold;
				font-size: 12px;
				color: black;
			}
		 </style>
	        <tr class="table-active2">
	            <th colspan="2">Periode</th>
	            <th class="text-right" colspan="2"><?php echo $filter_date = $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
				<th class="text-right" colspan="2">SD. <?php echo $filter_date_2 = date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
	        </tr>
			
			<?php
			//PENJUALAN
			$penjualan = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();
			
			$total_penjualan = 0;
			$total_volume = 0;

			foreach ($penjualan as $x){
				$total_penjualan += $x['price'];
				$total_volume += $x['volume'];
				$satuan = $x['measure'];
			}

			$total_penjualan_all = 0;
			$total_penjualan_all = $total_penjualan;

			//PENJUALAN_2
			$penjualan_2 = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date3' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();

			$total_penjualan_2 = 0;
			$total_volume_2 = 0;

			foreach ($penjualan_2 as $x){
				$total_penjualan_2 += $x['price'];
				$total_volume_2 += $x['volume'];
				$satuan_2 = $x['measure'];
			}

			$total_penjualan_all_2 = 0;
			$total_penjualan_all_2 = $total_penjualan_2;

			//BAHAN
			$bahan = $this->pmm_model->getBahan($date1,$date2);
			$total_nilai = $bahan;

			//BAHAN_2
			$bahan_2 = $this->pmm_model->getBahan2($date3,$date2);
			$total_nilai_2= $bahan_2;

			//ALAT
			$alat = $this->pmm_model->getAlat($date1,$date2);

			//ALAT_2
			$alat_2 = $this->pmm_model->getAkumulasiAlat($date3,$date2);

			//OVERHEAD
			$overhead = $this->pmm_model->getOverheadLabaRugi($date1,$date2);
			$overhead = $overhead;

			//OVERHEAD
			$overhead_2 = $this->pmm_model->getOverheadAkumulasiLabaRugi($date3,$date2);
			$overhead_2 = $overhead_2;

			//DISKONTO
			$diskonto = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 110")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$diskonto = $diskonto['total'];

			//DISKONTO_2
			$diskonto_2 = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 110")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date3' and '$date2')")
			->get()->row_array();
			$diskonto_2 = $diskonto_2['total'];

			$bahan = $total_nilai;
			$alat = $alat;
			$overhead = $overhead;
			$diskonto = $diskonto;
			$total_biaya_operasional = $bahan + $alat + $overhead + $diskonto;
			$laba_kotor = $total_penjualan_all - $total_biaya_operasional;
			$laba_usaha = $laba_kotor;
			$persentase_laba_sebelum_pajak = ($total_penjualan_all!=0)?($laba_usaha / $total_penjualan_all) * 100:0;

			$bahan_2 = $total_nilai_2;
			$alat_2 = $alat_2;
			$overhead_2 = $overhead_2;
			$diskonto_2 = $diskonto_2;
			$total_biaya_operasional_2 = $bahan_2 + $alat_2 + $overhead_2 + $diskonto_2;
			$laba_kotor_2 = $total_penjualan_all_2 - $total_biaya_operasional_2;
			$laba_usaha_2 = $laba_kotor_2;
			$persentase_laba_sebelum_pajak_2 = ($total_penjualan_all_2!=0)?($laba_usaha_2 / $total_penjualan_all_2) * 100:0;
	        ?>

			<tr class="table-active">
	            <th width="100%" class="text-left" colspan="6">Pendapatan Penjualan</th>
	        </tr>
			<tr class="table-active3">
	            <th width="10%" class="text-center">4-40000</th>
				<th width="90%" class="text-left" colspan="5">Pendapatan</th>
	        </tr>
			<?php foreach ($penjualan_2 as $i=>$x): ?>
			<tr class="table-active3">
	            <th width="10%"></th>
				<th width="30%"><?= $penjualan[$i]['nama'] ?></th>
				<th width="12%" class="text-right"><?php echo number_format($penjualan[$i]['volume'],2,',','.');?> (<?= $penjualan[$i]['measure'];?>)</th>
	            <th width="18%" class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_pengiriman_penjualan?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($penjualan[$i]['price'],0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>

				<th width="12%" class="text-right"><?php echo number_format($penjualan_2[$i]['volume'],2,',','.');?> (<?= $penjualan_2[$i]['measure'];?>)</th>
				
				<th width="18%" class="text-right xxx">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_pengiriman_penjualan?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($penjualan_2[$i]['price'],0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<?php endforeach; ?>
			<tr class="table-active3">
				<th class="text-left" colspan="2">Total Pendapatan</th>
				<th class="text-right"><?php echo number_format($total_volume,2,',','.');?> (<?= $satuan;?>)</th>
	            <th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_pengiriman_penjualan?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($total_penjualan_all,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"><?php echo number_format($total_volume_2,2,',','.');?> (<?= $satuan_2;?>)</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_pengiriman_penjualan?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($total_penjualan_all_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
				<th colspan="6"></th>
			</tr>
			<tr class="table-active">
				<th class="text-left" colspan="6">Beban Pokok Penjualan</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="2">Bahan</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_bahan?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($bahan,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
								<span><a target="_blank" href="<?= base_url("laporan/cetak_bahan?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($bahan_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="2">Alat</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_alat?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($alat,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_alat?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($alat_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="2">BUA</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_overhead?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($overhead,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_overhead?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($overhead_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center"></th>
				<th class="text-left" colspan="2">Diskonto</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_diskonto?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($diskonto,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><a target="_blank" href="<?= base_url("laporan/cetak_diskonto?filter_date=".$filter_date_2 = date('d F Y',strtotime($date3)).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($diskonto_2,0,',','.');?></a></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-left" colspan="3">Total Beban Pokok Penjualan</th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left"width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($total_biaya_operasional,0,',','.');?></span>
								</th>
							</tr>
					</table>				
				</th>
				<th class="text-right"></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left"width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo number_format($total_biaya_operasional_2,0,',','.');?></span>
								</th>
							</tr>
					</table>				
				</th>
	        </tr>
			<tr class="table-active3">
				<th colspan="6"></th>
			</tr>
			<?php
				$styleColorLabaKotor = $laba_kotor < 0 ? 'color:red' : 'color:black';
				$styleColorLabaKotor2 = $laba_kotor_2 < 0 ? 'color:red' : 'color:black';
				$styleColorSebelumPajak = $laba_usaha < 0 ? 'color:red' : 'color:black';
				$styleColorSebelumPajak2 = $laba_usaha_2 < 0 ? 'color:red' : 'color:black';
				$styleColorPresentase = $persentase_laba_sebelum_pajak < 0 ? 'color:red' : 'color:black';
				$styleColorPresentase2 = $persentase_laba_sebelum_pajak_2 < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-left" colspan="3">Laba / Rugi Kotor</th>
	            <th class="text-right" style="<?php echo $styleColorLabaKotor ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo $laba_kotor < 0 ? "(".number_format(-$laba_kotor,0,',','.').")" : number_format($laba_kotor,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"></th>
				<th class="text-right" style="<?php echo $styleColorLabaKotor2 ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo $laba_kotor_2 < 0 ? "(".number_format(-$laba_kotor_2,0,',','.').")" : number_format($laba_kotor_2,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
				<th colspan="6"></th>
			</tr>
			<tr class="table-active3">
				<th class="text-left" colspan="3">Biaya Umum & Administrasi</th>
	            <th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span>-</span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"></th>
				<th class="text-right">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span>-</span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
				<th colspan="6"></th>
			</tr>
			<tr class="table-active3">
	            <th colspan="3" class="text-left">Laba / Rugi Usaha</th>
	            <th class="text-right" style="<?php echo $styleColorSebelumPajak ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo $laba_usaha < 0 ? "(".number_format(-$laba_usaha,0,',','.').")" : number_format($laba_usaha,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"></th>
				<th class="text-right" style="<?php echo $styleColorSebelumPajak2 ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo $laba_usaha_2 < 0 ? "(".number_format(-$laba_usaha_2,0,',','.').")" : number_format($laba_usaha_2,0,',','.');?></span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
			<tr class="table-active3">
	            <th colspan="3" class="text-left">Presentase</th>
	            <th class="text-right" style="<?php echo $styleColorPresentase ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo $persentase_laba_sebelum_pajak < 0 ? "(".number_format(-$persentase_laba_sebelum_pajak,2,',','.').")" : number_format($persentase_laba_sebelum_pajak,2,',','.');?> %</span>
								</th>
							</tr>
					</table>
				</th>
				<th class="text-right"></th>
				<th class="text-right" style="<?php echo $styleColorPresentase2 ?>">
					<table width="100%" border="0" cellpadding="0">
						<tr>
								<th class="text-left" width="10%">
									<span>Rp.</span>
								</th>
								<th class="text-right" width="90%">
									<span><?php echo $persentase_laba_sebelum_pajak_2 < 0 ? "(".number_format(-$persentase_laba_sebelum_pajak_2,2,',','.').")" : number_format($persentase_laba_sebelum_pajak_2,2,',','.');?> %</span>
								</th>
							</tr>
					</table>
				</th>
	        </tr>
	    </table>
		<?php
	}

	public function cash_flow($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
			<style type="text/css">
				body {
					font-family: helvetica;
				}

				table tr.table-active-csf{
					background-color: #F0F0F0;
					font-size: 8px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-active2-csf{
					background-color: #E8E8E8;
					font-size: 8px;
					font-weight: bold;
				}
					
				table tr.table-active3-csf{
					font-size: 8px;
					background-color: #F0F0F0;
				}
					
				table tr.table-active4-csf{
					background-color: #e69500;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
				table tr.table-active5-csf{
					background-color: #E8E8E8;
					text-decoration: underline;
					font-size: 8px;
					font-weight: bold;
					color: red;
				}
				table tr.table-active6-csf{
					background-color: #A9A9A9;
					font-size: 8px;
					font-weight: bold;
				}
				table tr.table-active7-csf{
					background-color: #ffd966;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
				table tr.table-active8-csf{
					background-color: #2986cc;
					font-weight: bold;
					font-size: 8px;
					color: black;
				}
			</style>
			<?php
			$date_kunci = $this->db->select('date')->order_by('date','desc')->limit(1)->get_where('kunci_bahan_baku')->row_array();
			$last_opname = date('Y-m-d', strtotime('0 days', strtotime($date_kunci['date'])));
			$date_1_awal = date('Y-m-01', strtotime('+1 days +0 months', strtotime($last_opname)));
			$date_1_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_1_awal)));
			$date_2_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_1_akhir)));
			$date_2_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_2_awal)));
			$date_3_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_2_akhir)));
			$date_3_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_3_awal)));
			$date_4_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_3_akhir)));
			$date_4_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_4_awal)));
			$test = 0;
			?>
			<tr class="table-active4-csf">
				<th class="text-center" rowspan="2" style="vertical-align:middle; background-color:#55ffff;" width="5%">NO.</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle; background-color:#55ffff;">URAIAN</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle; background-color:#55ffff;">CURENT CASH BUDGET</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle; background-color:#55ffff; text-transform:uppercase;">SD. BULAN LALU <br />(<?php echo $last_opname = date('F Y', strtotime('0 days', strtotime($last_opname)));?>)</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle; background-color:#55ffff; text-transform:uppercase;">BULAN INI (<?php echo $date_1_awal = date('F', strtotime('+1 days +1 months', strtotime($last_opname)));?>)</th>
				<th class="text-center" rowspan="2" style="text-transform:uppercase; vertical-align:middle; background-color:#8fce00;">SD. (<?php echo $date_1_awal = date('F', strtotime('+1 days +1 months', strtotime($last_opname)));?>)</th>
				<th class="text-center" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F', strtotime('+1 days', strtotime($date_1_akhir)));?></th>
				<th class="text-center" style="text-transform:uppercase;"><?php echo $date_3_awal = date('F', strtotime('+1 days', strtotime($date_2_akhir)));?></th>
				<th class="text-center" style="text-transform:uppercase;"><?php echo $date_4_awal = date('F', strtotime('+1 days', strtotime($date_3_akhir)));?></th>
				<th class="text-center" rowspan="2" style="vertical-align:middle; background-color:#55ffff;">JUMLAH</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle; background-color:#55ffff;">SISA</th>
	        </tr>
			<tr class="table-active4-csf">
				<th class="text-center"><?php echo $date_2_awal = date('Y', strtotime('+1 days', strtotime($date_1_akhir)));?></th>
				<th class="text-center"><?php echo $date_3_awal = date('Y', strtotime('+1 days', strtotime($date_2_akhir)));?></th>
				<th class="text-center"><?php echo $date_4_awal = date('Y', strtotime('+1 days', strtotime($date_3_akhir)));?></th>
	        </tr>
			<tr class="table-active3-csf">
				<th class="text-center" rowspan="4" style="vertical-align:middle">I</th>
				<th class="text-left" colspan="10"><u>PRODUKSI (EXCL. PPN)</u></th>
			</tr>
			<?php
			$date_agustus24_awal = date('2024-08-01');
			$date_agustus24_akhir = date('2024-08-31');
			$date_september24_awal = date('2024-09-01');
			$date_september24_akhir = date('2024-09-30');
			$date_oktober24_awal = date('2024-10-01');
			$date_oktober24_akhir = date('2024-10-31');
			$date_november24_awal = date('2024-11-01');
			$date_november24_akhir = date('2024-11-30');
			$date_desember24_awal = date('2024-12-01');
			$date_desember24_akhir = date('2024-12-31');
			?>
			<tr class="table-active3-csf">
				<th class="text-left"><u>AKUMULASI %</u></th>
				<th class="text-right">100%</th>
				<th class="text-right"><?php echo number_format(($penjualan_sd_bulan_lalu/$rencana_pendapatan) * 100,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(($penjualan_bulan_ini/$rencana_pendapatan) * 100,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(($penjualan_bulan_ini_sd/$rencana_pendapatan) * 100,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(($penjualan_2/$rencana_pendapatan) * 100,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(($penjualan_3/$rencana_pendapatan) * 100,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(($penjualan_4/$rencana_pendapatan) * 100,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(($jumlah_penjualan/$rencana_pendapatan) * 100,2,',','.');?>%</th>
				<th class="text-right"><?php echo number_format(($sisa_penjualan/$rencana_pendapatan) * 100,2,',','.');?>%</th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left">&nbsp;&nbsp;1. PRODUKSI (Rp.)</th>
				<th class="text-right"><?php echo number_format($rencana_pendapatan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penjualan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_penjualan,0,',','.');?></th>
			</tr>
			<tr class="table-active2-csf">
				<th class="text-left">&nbsp;&nbsp;2. AKUMULASI (Rp.)</th>
				<th class="text-right"><?php echo number_format($rencana_pendapatan,0,',','.');?></th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right">-</th>
				<th class="text-right"><?php echo number_format($penjualan_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_2 + $penjualan_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penjualan_2 + $penjualan_3 + $penjualan_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penjualan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_penjualan,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-center" rowspan="4" style="vertical-align:middle">II</th>
				<th class="text-left" colspan="10"><u>PENERIMAAN (EXCL. PPN)</u></th>
			</tr>
			<?php
			$rencana_termin = $rencana_pendapatan;

			$termin_sd_bulan_lalu = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_awal' and '$last_opname'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$termin_sd_bulan_lalu = $termin_sd_bulan_lalu['total'];

			$termin_bulan_ini = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_1_awal' and '$date_1_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$termin_bulan_ini = $termin_bulan_ini['total'];

			$termin_bulan_ini_sd = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_awal' and '$date_1_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$termin_bulan_ini_sd = $termin_bulan_ini_sd['total'];

			$termin_2 = $this->db->select('SUM(termin) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$termin_2 = $termin_2['total'];

			$termin_3 = $this->db->select('SUM(termin) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$termin_3 = $termin_3['total'];

			$termin_4 = $this->db->select('SUM(termin) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$termin_4 = $termin_4['total'];

			$jumlah_termin = $termin_bulan_ini_sd + $termin_2 + $termin_3 + $termin_4;
			$sisa_termin = $rencana_termin - $jumlah_termin;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;1. Termin / Angsuran</th>
				<th class="text-right"><?php echo number_format($rencana_termin,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($termin_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_termin,0,',','.');?></th>
			</tr>
			<?php
			$rencana_ppn_keluaran = ($rencana_termin * 11) / 100;

			$ppn_keluaran_sd_bulan_lalu = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_awal' and '$last_opname'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$ppn_keluaran_sd_bulan_lalu = $ppn_keluaran_sd_bulan_lalu['total'];

			$ppn_keluaran_bulan_ini = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_1_awal' and '$date_1_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo = 'PPN'")
			->get()->row_array();
			$ppn_keluaran_bulan_ini = $ppn_keluaran_bulan_ini['total'];

			$ppn_keluaran_bulan_ini_sd = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran pm')
			->where("pm.tanggal_pembayaran between '$date_awal' and '$date_1_akhir'")
			->where("pm.status = 'DISETUJUI'")
			->where("pm.memo = 'PPN'")
			->get()->row_array();
			$ppn_keluaran_bulan_ini_sd = $ppn_keluaran_bulan_ini_sd['total'];

			$ppn_keluaran_2 = $this->db->select('SUM(pajak_keluaran) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$ppn_keluaran_2 = $ppn_keluaran_2['total'];

			$ppn_keluaran_3 = $this->db->select('SUM(pajak_keluaran) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$ppn_keluaran_3 = $ppn_keluaran_3['total'];

			$ppn_keluaran_4 = $this->db->select('SUM(pajak_keluaran) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$ppn_keluaran_4 = $ppn_keluaran_4['total'];

			$jumlah_ppn_keluaran = $ppn_keluaran_bulan_ini_sd + $ppn_keluaran_2 + $ppn_keluaran_3 + $ppn_keluaran_4;
			$sisa_ppn_keluaran = $rencana_ppn_keluaran - $jumlah_ppn_keluaran;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. PPN Keluaran</th>
				<th class="text-right"><?php echo number_format($rencana_ppn_keluaran,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_keluaran_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_keluaran_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_keluaran_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_keluaran_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_keluaran_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_keluaran_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_ppn_keluaran,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_ppn_keluaran,0,',','.');?></th>
			</tr>
			<?php
			$rencana_jumlah_penerimaan = $rencana_termin + $rencana_ppn_keluaran;
			$jumlah_termin_sd_bulan_lalu = $termin_sd_bulan_lalu + $ppn_keluaran_sd_bulan_lalu;
			$jumlah_termin_bulan_ini = $termin_bulan_ini + $ppn_keluaran_bulan_ini;
			$jumlah_termin_bulan_ini_sd = $termin_bulan_ini_sd + $ppn_keluaran_bulan_ini_sd;
			$jumlah_termin_2 = $termin_2 + $ppn_keluaran_2;
			$jumlah_termin_3 = $termin_3 + $ppn_keluaran_3;
			$jumlah_termin_4 = $termin_4 + $ppn_keluaran_4;
			$jumlah_jumlah_termin = $jumlah_termin + $jumlah_ppn_keluaran;
			$jumlah_sisa_termin = $sisa_termin + $sisa_ppn_keluaran;
			?>
			<tr class="table-active2-csf">
				<th class="text-left">JUMLAH II</th>
				<th class="text-right"><?php echo number_format($rencana_jumlah_penerimaan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_termin_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_jumlah_termin,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_sisa_termin,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-center" rowspan="7" style="vertical-align:middle">III</th>
				<th class="text-left" colspan="10"><u>PENGELUARAN (EXCL. PPN)</u></th>
			</tr>
			<?php
			$bahan_sd_bulan_lalu = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->join('pmm_penagihan_pembelian ppp','pm.penagihan_pembelian_id = ppp.id','left')
			->join('pmm_purchase_order ppo','ppp.purchase_order_id = ppo.id','left')
			->where("pm.tanggal_pembayaran between '$date_awal' and '$last_opname'")
			->where("ppo.kategori_id = '1'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$bahan_sd_bulan_lalu = $bahan_sd_bulan_lalu['total'];

			$bahan_bulan_ini = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->join('pmm_penagihan_pembelian ppp','pm.penagihan_pembelian_id = ppp.id','left')
			->join('pmm_purchase_order ppo','ppp.purchase_order_id = ppo.id','left')
			->where("pm.tanggal_pembayaran between '$date_1_awal' and '$date_1_akhir'")
			->where("ppo.kategori_id = '1'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$bahan_bulan_ini = $bahan_bulan_ini['total'];

			$bahan_bulan_ini_sd = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->join('pmm_penagihan_pembelian ppp','pm.penagihan_pembelian_id = ppp.id','left')
			->join('pmm_purchase_order ppo','ppp.purchase_order_id = ppo.id','left')
			->where("pm.tanggal_pembayaran between '$date_awal' and '$date_1_akhir'")
			->where("ppo.kategori_id = '1'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$bahan_bulan_ini_sd = $bahan_bulan_ini_sd['total'];

			$bahan_2 = $this->db->select('SUM(biaya_bahan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$bahan_2 = $bahan_2['total'];

			$bahan_3 = $this->db->select('SUM(biaya_bahan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$bahan_3 = $bahan_3['total'];

			$bahan_4 = $this->db->select('SUM(biaya_bahan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$bahan_4 = $bahan_4['total'];

			$jumlah_bahan = $bahan_bulan_ini_sd + $bahan_2 + $bahan_3 + $bahan_4;
			$sisa_bahan = $rencana_biaya_bahan - $jumlah_bahan;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;1. Biaya Bahan</th>
				<th class="text-right"><?php echo number_format($rencana_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bahan_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bahan_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bahan_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bahan_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bahan_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bahan_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_bahan,0,',','.');?></th>
			</tr>
			<?php
			$volume_rak_1 = $this->db->select('*, sum(vol_produk_a + vol_produk_b) as volume')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_agustus24_awal' and '$date_agustus24_akhir'")
			->get()->row_array();

			$volume_rak_2 = $this->db->select('*, sum(vol_produk_a + vol_produk_b) as volume')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_september24_awal' and '$date_september24_akhir'")
			->get()->row_array();

			$volume_rak_3 = $this->db->select('*, sum(vol_produk_a + vol_produk_b) as volume')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_oktober24_awal' and '$date_oktober24_akhir'")
			->get()->row_array();

			$volume_rak_4 = $this->db->select('*, sum(vol_produk_a + vol_produk_b) as volume')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_november24_awal' and '$date_november24_akhir'")
			->get()->row_array();

			$volume_rak_5 = $this->db->select('*, sum(vol_produk_a + vol_produk_b) as volume')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_desember24_awal' and '$date_desember24_akhir'")
			->get()->row_array();

			$harsat_rap_alat = $this->db->select('*')
			->from('rap_alat')
			->order_by('id','desc')->limit(1)
			->get()->row_array();
			$harsat_rap_alat_bp = $harsat_rap_alat['batching_plant'];
			$harsat_rap_alat_tm = $harsat_rap_alat['truck_mixer'];
			$harsat_rap_alat_wl = $harsat_rap_alat['wheel_loader'];
			$harsat_rap_alat_solar = $harsat_rap_alat['bbm_solar'];

			$total_volume_bp_1 = $volume_rak_1['volume'];
			$total_volume_bp_2 = $volume_rak_2['volume'];
			$total_volume_bp_3 = $volume_rak_3['volume'];
			$total_volume_bp_4 = $volume_rak_4['volume'];
			$total_volume_bp_5 = $volume_rak_5['volume'];
			
			$total_nilai_bp_1 = $volume_rak_1['volume'] * $harsat_rap_alat_bp;
			$total_nilai_bp_2 = $volume_rak_2['volume'] * $harsat_rap_alat_bp;
			$total_nilai_bp_3 = $volume_rak_3['volume'] * $harsat_rap_alat_bp;
			$total_nilai_bp_4 = $volume_rak_4['volume'] * $harsat_rap_alat_bp;
			$total_nilai_bp_5 = $volume_rak_5['volume'] * $harsat_rap_alat_bp;

			$total_volume_tm_1 = $volume_rak_1['volume'];
			$total_volume_tm_2 = $volume_rak_2['volume'];
			$total_volume_tm_3 = $volume_rak_3['volume'];
			$total_volume_tm_4 = $volume_rak_4['volume'];
			$total_volume_tm_5 = $volume_rak_5['volume'];

			$total_nilai_tm_1 = $volume_rak_1['volume'] * $harsat_rap_alat_tm;
			$total_nilai_tm_2 = $volume_rak_2['volume'] * $harsat_rap_alat_tm;
			$total_nilai_tm_3 = $volume_rak_3['volume'] * $harsat_rap_alat_tm;
			$total_nilai_tm_4 = $volume_rak_4['volume'] * $harsat_rap_alat_tm;
			$total_nilai_tm_5 = $volume_rak_5['volume'] * $harsat_rap_alat_tm;

			$total_volume_wl_1 = $volume_rak_1['volume'];
			$total_volume_wl_2 = $volume_rak_2['volume'];
			$total_volume_wl_3 = $volume_rak_3['volume'];
			$total_volume_wl_4 = $volume_rak_4['volume'];
			$total_volume_wl_5 = $volume_rak_5['volume'];

			$total_nilai_wl_1 = $volume_rak_1['volume'] * $harsat_rap_alat_wl;
			$total_nilai_wl_2 = $volume_rak_2['volume'] * $harsat_rap_alat_wl;
			$total_nilai_wl_3 = $volume_rak_3['volume'] * $harsat_rap_alat_wl;
			$total_nilai_wl_4 = $volume_rak_4['volume'] * $harsat_rap_alat_wl;
			$total_nilai_wl_5 = $volume_rak_5['volume'] * $harsat_rap_alat_wl;

			$total_volume_solar_1 = $volume_rak_1['volume'];
			$total_volume_solar_2 = $volume_rak_2['volume'];
			$total_volume_solar_3 = $volume_rak_3['volume'];
			$total_volume_solar_4 = $volume_rak_4['volume'];
			$total_volume_solar_5 = $volume_rak_5['volume'];

			$total_nilai_solar_1 = $volume_rak_1['volume'] * $harsat_rap_alat_solar;
			$total_nilai_solar_2 = $volume_rak_2['volume'] * $harsat_rap_alat_solar;
			$total_nilai_solar_3 = $volume_rak_3['volume'] * $harsat_rap_alat_solar;
			$total_nilai_solar_4 = $volume_rak_4['volume'] * $harsat_rap_alat_solar;
			$total_nilai_solar_5 = $volume_rak_5['volume'] * $harsat_rap_alat_solar;

			$total_nilai_bp = $total_nilai_bp_1 + $total_nilai_bp_2 + $total_nilai_bp_3 +  + $total_nilai_bp_4 +  + $total_nilai_bp_5;
			$total_nilai_tm = $total_nilai_tm_1 + $total_nilai_tm_2 + $total_nilai_tm_3 +  + $total_nilai_tm_4 +  + $total_nilai_tm_5;
			$total_nilai_wl = $total_nilai_wl_1 + $total_nilai_wl_2 + $total_nilai_wl_3 +  + $total_nilai_wl_4 +  + $total_nilai_wl_5;
			$total_nilai_solar = $total_nilai_solar_1 + $total_nilai_solar_2 + $total_nilai_solar_3 +  + $total_nilai_solar_4 +  + $total_nilai_solar_5;

			$jumlah_alat_1 = $total_nilai_bp_1 + $total_nilai_tm_1 + $total_nilai_wl_1 + $total_nilai_solar_1;
			$jumlah_alat_2 = $total_nilai_bp_2 + $total_nilai_tm_2 + $total_nilai_wl_2 + $total_nilai_solar_2;
			$jumlah_alat_3 = $total_nilai_bp_3 + $total_nilai_tm_3 + $total_nilai_wl_3 + $total_nilai_solar_3;
			$jumlah_alat_4 = $total_nilai_bp_4 + $total_nilai_tm_4 + $total_nilai_wl_4 + $total_nilai_solar_4;
			$jumlah_alat_5 = $total_nilai_bp_5 + $total_nilai_tm_5 + $total_nilai_wl_5 + $total_nilai_solar_5;
			$rencana_biaya_alat = $jumlah_alat_1 + $jumlah_alat_2 + $jumlah_alat_3 + $jumlah_alat_4 + $jumlah_alat_5;
			
			$alat_sd_bulan_lalu = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->join('pmm_penagihan_pembelian ppp','pm.penagihan_pembelian_id = ppp.id','left')
			->join('pmm_purchase_order ppo','ppp.purchase_order_id = ppo.id','left')
			->where("pm.tanggal_pembayaran between '$date_awal' and '$last_opname'")
			->where("ppo.kategori_id = '5'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$alat_sd_bulan_lalu = $alat_sd_bulan_lalu['total'];

			$alat_bulan_ini = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->join('pmm_penagihan_pembelian ppp','pm.penagihan_pembelian_id = ppp.id','left')
			->join('pmm_purchase_order ppo','ppp.purchase_order_id = ppo.id','left')
			->where("pm.tanggal_pembayaran between '$date_1_awal' and '$date_1_akhir'")
			->where("ppo.kategori_id = '5'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$alat_bulan_ini = $alat_bulan_ini['total'];

			$alat_bulan_ini_sd = $this->db->select('SUM(pm.total) as total')
			->from('pmm_pembayaran_penagihan_pembelian pm')
			->join('pmm_penagihan_pembelian ppp','pm.penagihan_pembelian_id = ppp.id','left')
			->join('pmm_purchase_order ppo','ppp.purchase_order_id = ppo.id','left')
			->where("pm.tanggal_pembayaran between '$date_awal' and '$date_1_akhir'")
			->where("ppo.kategori_id = '5'")
			->where("pm.memo <> 'PPN'")
			->get()->row_array();
			$alat_bulan_ini_sd = $alat_bulan_ini_sd['total'];

			$alat_2 = $this->db->select('SUM(biaya_alat) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$alat_2 = $alat_2['total'];

			$alat_3 = $this->db->select('SUM(biaya_alat) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$alat_3 = $alat_3['total'];

			$alat_4 = $this->db->select('SUM(biaya_alat) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$alat_4 = $alat_4['total'];

			$jumlah_alat = $alat_bulan_ini_sd + $alat_2 + $alat_3 + $alat_4;
			$sisa_alat = $rencana_biaya_alat - $jumlah_alat;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. Biaya Peralatan</th>
				<th class="text-right"><?php echo number_format($rencana_biaya_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($alat_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($alat_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($alat_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($alat_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($alat_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($alat_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_alat,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_alat,0,',','.');?></th>
			</tr>
			<?php
			$rencana_biaya_bank = 0;
			$bank_sd_bulan_lalu = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 110")
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_awal' and '$last_opname'")
			->get()->row_array();
			$bank_sd_bulan_lalu = $bank_sd_bulan_lalu['total'];

			$bank_bulan_ini = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 110")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$bank_bulan_ini = $bank_bulan_ini['total'];

			$bank_bulan_ini_sd = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 110")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date_awal' and '$date_1_akhir')")
			->get()->row_array();
			$bank_bulan_ini_sd = $bank_bulan_ini_sd['total'];

			$bank_2 = $this->db->select('SUM(biaya_bank) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$bank_2 = $bank_2['total'];

			$bank_3 = $this->db->select('SUM(biaya_bank) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$bank_3 = $bank_3['total'];

			$bank_4 = $this->db->select('SUM(biaya_bank) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$bank_4 = $bank_4['total'];

			$jumlah_bank = $bank_bulan_ini_sd + $bank_2 + $bank_3 + $bank_4;
			$sisa_bank = $rencana_biaya_bank - $jumlah_bank;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;3. Biaya Bank</th>
				<th class="text-right"><?php echo number_format($rencana_biaya_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bank_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bank_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bank_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bank_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bank_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bank_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bank,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_bank,0,',','.');?></th>
			</tr>
			<?php
			$rencana_biaya_bua = 0;
			$bua_sd_bulan_lalu_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("c.coa_category in ('15','17')")
			->where("c.id <> 110 ") //Biaya Diskonto Bank
			->where("c.id <> 131 ") //Biaya Persiapan
			->where("c.id <> 124 ") //Biaya Maintenance Truck Mixer
			->where("c.id <> 138 ") //Biaya Maintenance Batching Plant
			->where("c.id <> 140 ") //Biaya Maintenance Wheel Loader
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_awal' and '$last_opname'")
			->get()->row_array();

			$bua_sd_bulan_lalu_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("c.coa_category in ('15','17')")
			->where("c.id <> 110 ") //Biaya Diskonto Bank
			->where("c.id <> 131 ") //Biaya Persiapan
			->where("c.id <> 124 ") //Biaya Maintenance Truck Mixer
			->where("c.id <> 138 ") //Biaya Maintenance Batching Plant
			->where("c.id <> 140 ") //Biaya Maintenance Wheel Loader
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_awal' and '$last_opname'")
			->get()->row_array();
			$bua_sd_bulan_lalu = $bua_sd_bulan_lalu_biaya['total'] + $bua_sd_bulan_lalu_jurnal['total'];

			$bua_bulan_ini_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("c.coa_category in ('15','17')")
			->where("c.id <> 110 ") //Biaya Diskonto Bank
			->where("c.id <> 131 ") //Biaya Persiapan
			->where("c.id <> 124 ") //Biaya Maintenance Truck Mixer
			->where("c.id <> 138 ") //Biaya Maintenance Batching Plant
			->where("c.id <> 140 ") //Biaya Maintenance Wheel Loader
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_1_awal' and '$date_1_akhir'")
			->get()->row_array();

			$bua_bulan_ini_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("c.coa_category in ('15','17')")
			->where("c.id <> 110 ") //Biaya Diskonto Bank
			->where("c.id <> 131 ") //Biaya Persiapan
			->where("c.id <> 124 ") //Biaya Maintenance Truck Mixer
			->where("c.id <> 138 ") //Biaya Maintenance Batching Plant
			->where("c.id <> 140 ") //Biaya Maintenance Wheel Loader
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_1_awal' and '$date_1_akhir'")
			->get()->row_array();
			$bua_bulan_ini = $bua_bulan_ini_biaya['total'] + $bua_bulan_ini_jurnal['total'];

			$bua_bulan_ini_sd_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("c.coa_category in ('15','17')")
			->where("c.id <> 110 ") //Biaya Diskonto Bank
			->where("c.id <> 131 ") //Biaya Persiapan
			->where("c.id <> 124 ") //Biaya Maintenance Truck Mixer
			->where("c.id <> 138 ") //Biaya Maintenance Batching Plant
			->where("c.id <> 140 ") //Biaya Maintenance Wheel Loader
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_awal' and '$date_1_akhir'")
			->get()->row_array();

			$bua_bulan_ini_sd_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("c.coa_category in ('15','17')")
			->where("c.id <> 110 ") //Biaya Diskonto Bank
			->where("c.id <> 131 ") //Biaya Persiapan
			->where("c.id <> 124 ") //Biaya Maintenance Truck Mixer
			->where("c.id <> 138 ") //Biaya Maintenance Batching Plant
			->where("c.id <> 140 ") //Biaya Maintenance Wheel Loader
			->where("pb.status = 'PAID'")
			->where("pb.tanggal_transaksi between '$date_awal' and '$date_1_akhir'")
			->get()->row_array();
			$bua_bulan_ini_sd = $bua_bulan_ini_sd_biaya['total'] + $bua_bulan_ini_sd_jurnal['total'];

			$bua_2 = $this->db->select('SUM(overhead) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$bua_2 = $bua_2['total'];

			$bua_3 = $this->db->select('SUM(overhead) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$bua_3 = $bua_3['total'];

			$bua_4 = $this->db->select('SUM(overhead) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$bua_4 = $bua_4['total'];

			$jumlah_bua = $bua_bulan_ini_sd + $bua_2 + $bua_3 + $bua_4;
			$sisa_bua = $rencana_biaya_bua - $jumlah_bua;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;4. BAU Proyek</th>
				<th class="text-right"><?php echo number_format($rencana_biaya_bua,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bua_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bua_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bua_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bua_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bua_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($bua_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bua,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_bua,0,',','.');?></th>
			</tr>
			<?php
			$rencana_ppn_masukan = ($rencana_biaya_bahan * 11) / 100;
			$ppn_masukan_sd_bulan_lalu = ($bahan_sd_bulan_lalu * 11) / 100;
			$ppn_masukan_bulan_ini = ($bahan_bulan_ini * 11) / 100;
			$ppn_masukan_bulan_ini_sd = ($bahan_bulan_ini_sd * 11) / 100;
			$ppn_masukan_2 = ($bahan_2 * 11) / 100;
			$ppn_masukan_3 = ($bahan_3 * 11) / 100;
			$ppn_masukan_4 = ($bahan_4 * 11) / 100;
			$jumlah_ppn_masukan = ($jumlah_bahan * 11) / 100;
			$sisa_ppn_masukan = ($sisa_bahan * 11) / 100;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;5. PPN Masukan</th>
				<th class="text-right"><?php echo number_format($rencana_ppn_masukan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_masukan_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_masukan_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_masukan_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_masukan_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_masukan_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($ppn_masukan_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_ppn_masukan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_ppn_masukan,0,',','.');?></th>
			</tr>
			<?php
			$rencana_jumlah_pengeluaran = $rencana_biaya_bahan + $rencana_biaya_alat + $rencana_biaya_bank + $rencana_biaya_bua + $rencana_ppn_masukan;
			$jumlah_pengeluaran_sd_bulan_lalu = $bahan_sd_bulan_lalu + $alat_sd_bulan_lalu + $bank_sd_bulan_lalu + $bua_sd_bulan_lalu + $ppn_masukan_sd_bulan_lalu;
			$jumlah_pengeluaran_bulan_ini = $bahan_bulan_ini + $alat_bulan_ini + $bank_bulan_ini + $bua_bulan_ini + $ppn_masukan_bulan_ini;
			$jumlah_pengeluaran_bulan_ini_sd = $bahan_bulan_ini_sd + $alat_bulan_ini_sd + $bank_bulan_ini_sd + $bua_bulan_ini_sd + $ppn_masukan_bulan_ini_sd;
			$jumlah_pengeluaran_2 = $bahan_2 + $alat_2 + $bank_2 + $bua_2 + $ppn_masukan_2;
			$jumlah_pengeluaran_3 = $bahan_3 + $alat_3 + $bank_3 + $bua_3 + $ppn_masukan_3;
			$jumlah_pengeluaran_4 = $bahan_4 + $alat_4 + $bank_4 + $bua_4 + $ppn_masukan_4;
			$jumlah_pengeluaran = $jumlah_bahan + $jumlah_alat + $jumlah_bank + $jumlah_bua + $jumlah_ppn_masukan;
			$sisa_pengeluaran = $sisa_bahan + $sisa_alat + $sisa_bank + $sisa_bua + $sisa_ppn_masukan;
			?>
			<tr class="table-active2-csf">
				<th class="text-left">JUMLAH III</th>
				<th class="text-right"><?php echo number_format($rencana_jumlah_pengeluaran,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pengeluaran_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pengeluaran_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pengeluaran_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pengeluaran_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pengeluaran_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pengeluaran_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pengeluaran,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_pengeluaran,0,',','.');?></th>
			</tr>
			<?php
			$rencana_posisi_2_3 = $rencana_jumlah_penerimaan - $rencana_jumlah_pengeluaran;
			$jumlah_posisi_2_3_sd_bulan_lalu = $jumlah_termin_sd_bulan_lalu - $jumlah_pengeluaran_sd_bulan_lalu;
			$jumlah_posisi_2_3_bulan_ini = $jumlah_termin_bulan_ini - $jumlah_pengeluaran_bulan_ini;
			$jumlah_posisi_2_3_bulan_ini_sd = $jumlah_termin_bulan_ini_sd - $jumlah_pengeluaran_bulan_ini_sd;
			$jumlah_posisi_2_3_2 = $jumlah_termin_2 - $jumlah_pengeluaran_2;
			$jumlah_posisi_2_3_3 = $jumlah_termin_3 - $jumlah_pengeluaran_3;
			$jumlah_posisi_2_3_4 = $jumlah_termin_4 - $jumlah_pengeluaran_4;
			$jumlah_2_3 = $jumlah_jumlah_termin - $jumlah_pengeluaran;
			$sisa_2_3 = $jumlah_sisa_termin - $sisa_pengeluaran;
			?>
			<tr class="table-active3-csf">
				<th class="text-center" style="vertical-align:middle">IV</th>
				<th class="text-left">POSISI ( II - III )</th>
				<?php
				$styleColor = $rencana_posisi_2_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $rencana_posisi_2_3 < 0 ? "(".number_format(-$rencana_posisi_2_3,0,',','.').")" : number_format($rencana_posisi_2_3,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_2_3_sd_bulan_lalu < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_2_3_sd_bulan_lalu < 0 ? "(".number_format(-$jumlah_posisi_2_3_sd_bulan_lalu,0,',','.').")" : number_format($jumlah_posisi_2_3_sd_bulan_lalu,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_2_3_bulan_ini < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_2_3_bulan_ini < 0 ? "(".number_format(-$jumlah_posisi_2_3_bulan_ini,0,',','.').")" : number_format($jumlah_posisi_2_3_bulan_ini,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_2_3_bulan_ini_sd < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_2_3_bulan_ini_sd < 0 ? "(".number_format(-$jumlah_posisi_2_3_bulan_ini_sd,0,',','.').")" : number_format($jumlah_posisi_2_3_bulan_ini_sd,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_2_3_2 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_2_3_2 < 0 ? "(".number_format(-$jumlah_posisi_2_3_2,0,',','.').")" : number_format($jumlah_posisi_2_3_2,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_2_3_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_2_3_3 < 0 ? "(".number_format(-$jumlah_posisi_2_3_3,0,',','.').")" : number_format($jumlah_posisi_2_3_3,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_2_3_4 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_2_3_4 < 0 ? "(".number_format(-$jumlah_posisi_2_3_4,0,',','.').")" : number_format($jumlah_posisi_2_3_4,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_2_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_2_3 < 0 ? "(".number_format(-$jumlah_2_3,0,',','.').")" : number_format($jumlah_2_3,0,',','.');?></th>
				<?php
				$styleColor = $sisa_2_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $sisa_2_3 < 0 ? "(".number_format(-$sisa_2_3,0,',','.').")" : number_format($sisa_2_3,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-center" rowspan="4" style="vertical-align:middle">V</th>
				<th class="text-left" colspan="10"><u>PAJAK</u></th>
			</tr>
			<?php
			$rencana_pajak_keluaran = ($rencana_termin * 11) / 100;
			$pajak_keluaran_sd_bulan_lalu = ($termin_sd_bulan_lalu * 11) / 100;
			$pajak_keluaran_bulan_ini = ($termin_bulan_ini * 11) / 100;
			$pajak_keluaran_bulan_ini_sd = ($termin_bulan_ini_sd * 11) / 100;

			$pajak_keluaran_2 = $this->db->select('SUM(pajak_keluaran) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$pajak_keluaran_2 = $pajak_keluaran_2['total'];

			$pajak_keluaran_3 = $this->db->select('SUM(pajak_keluaran) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$pajak_keluaran_3 = $pajak_keluaran_3['total'];

			$pajak_keluaran_4 = $this->db->select('SUM(pajak_keluaran) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$pajak_keluaran_4 = $pajak_keluaran_4['total'];

			$jumlah_pajak_keluaran = $pajak_keluaran_bulan_ini_sd + $pajak_keluaran_2 + $pajak_keluaran_3 + $pajak_keluaran_4;
			$sisa_pajak_keluaran = $rencana_pajak_keluaran - $jumlah_pajak_keluaran;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;1. Pajak Keluaran</th>
				<th class="text-right"><?php echo number_format($rencana_pajak_keluaran,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_keluaran_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_keluaran_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_keluaran_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_keluaran_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_keluaran_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_keluaran_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pajak_keluaran,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_pajak_keluaran,0,',','.');?></th>
			</tr>
			<?php
			$rencana_pajak_masukan = ($rencana_biaya_bahan * 11) / 100;
			$pajak_masukan_sd_bulan_lalu = ($bahan_sd_bulan_lalu * 11) / 100;
			$pajak_masukan_bulan_ini = ($bahan_bulan_ini * 11) / 100;
			$pajak_masukan_bulan_ini_sd = ($bahan_bulan_ini_sd * 11) / 100;
			$pajak_masukan_2 = $this->db->select('SUM(pajak_masukan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$pajak_masukan_2 = $pajak_masukan_2['total'];

			$pajak_masukan_3 = $this->db->select('SUM(pajak_masukan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$pajak_masukan_3 = $pajak_masukan_3['total'];

			$pajak_masukan_4 = $this->db->select('SUM(pajak_masukan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$pajak_masukan_4 = $pajak_masukan_4['total'];

			$jumlah_pajak_masukan = $pajak_masukan_bulan_ini_sd + $pajak_masukan_2 + $pajak_masukan_3 + $pajak_masukan_4;
			$sisa_pajak_masukan = $rencana_pajak_masukan - $jumlah_pajak_masukan;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. Pajak Masukan</th>
				<th class="text-right"><?php echo number_format($rencana_pajak_masukan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_masukan_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_masukan_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_masukan_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_masukan_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_masukan_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pajak_masukan_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pajak_masukan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_pajak_masukan,0,',','.');?></th>
			</tr>
			<?php
			$rencana_jumlah_pajak = $rencana_pajak_keluaran - $rencana_pajak_masukan;
			$jumlah_pajak_sd_bulan_lalu = $pajak_keluaran_sd_bulan_lalu - $pajak_masukan_sd_bulan_lalu;
			$jumlah_pajak_bulan_ini = $pajak_keluaran_bulan_ini - $pajak_masukan_bulan_ini;
			$jumlah_pajak_bulan_ini_sd = $pajak_keluaran_bulan_ini_sd - $pajak_masukan_bulan_ini_sd;
			$jumlah_pajak_2 = $pajak_keluaran_2 - $pajak_masukan_2;
			$jumlah_pajak_3 = $pajak_keluaran_3 - $pajak_masukan_3;
			$jumlah_pajak_4 = $pajak_keluaran_4 - $pajak_masukan_4;
			$jumlah_pajak = $jumlah_pajak_keluaran - $jumlah_pajak_masukan;
			$sisa_pajak = $sisa_pajak_keluaran - $sisa_pajak_masukan;
			?>
			<tr class="table-active2-csf">
				<th class="text-left">JUMLAH V</th>
				<?php
				$styleColor = $rencana_jumlah_pajak < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $rencana_jumlah_pajak < 0 ? "(".number_format(-$rencana_jumlah_pajak,0,',','.').")" : number_format($rencana_jumlah_pajak,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_pajak_sd_bulan_lalu < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_pajak_sd_bulan_lalu < 0 ? "(".number_format(-$jumlah_pajak_sd_bulan_lalu,0,',','.').")" : number_format($jumlah_pajak_sd_bulan_lalu,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_pajak_bulan_ini < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_pajak_bulan_ini < 0 ? "(".number_format(-$jumlah_pajak_bulan_ini,0,',','.').")" : number_format($jumlah_pajak_bulan_ini,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_pajak_bulan_ini_sd < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_pajak_bulan_ini_sd < 0 ? "(".number_format(-$jumlah_pajak_bulan_ini_sd,0,',','.').")" : number_format($jumlah_pajak_bulan_ini_sd,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_pajak_2 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_pajak_2 < 0 ? "(".number_format(-$jumlah_pajak_2,0,',','.').")" : number_format($jumlah_pajak_2,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_pajak_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_pajak_3 < 0 ? "(".number_format(-$jumlah_pajak_3,0,',','.').")" : number_format($jumlah_pajak_3,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_pajak_4 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_pajak_4 < 0 ? "(".number_format(-$jumlah_pajak_4,0,',','.').")" : number_format($jumlah_pajak_4,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_pajak < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_pajak < 0 ? "(".number_format(-$jumlah_pajak,0,',','.').")" : number_format($jumlah_pajak,0,',','.');?></th>
				<?php
				$styleColor = $sisa_pajak < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $sisa_pajak < 0 ? "(".number_format(-$sisa_pajak,0,',','.').")" : number_format($sisa_pajak,0,',','.');?></th>
			</tr>
			<?php
			$rencana_posisi_4_5 = $rencana_posisi_2_3 - $rencana_jumlah_pajak;
			$jumlah_posisi_4_5_sd_bulan_lalu = $jumlah_posisi_2_3_sd_bulan_lalu - $jumlah_pajak_sd_bulan_lalu;
			$jumlah_posisi_4_5_bulan_ini = $jumlah_posisi_2_3_bulan_ini - $jumlah_pajak_bulan_ini;
			$jumlah_posisi_4_5_bulan_ini_sd = $jumlah_posisi_2_3_bulan_ini_sd - $jumlah_pajak_bulan_ini_sd;
			$jumlah_posisi_4_5_2 = $jumlah_posisi_2_3_2 - $jumlah_pajak_2;
			$jumlah_posisi_4_5_3 = $jumlah_posisi_2_3_3 - $jumlah_pajak_3;
			$jumlah_posisi_4_5_4 = $jumlah_posisi_2_3_4 - $jumlah_pajak_4;
			$jumlah_4_5 = $jumlah_2_3 - $jumlah_pajak;
			$sisa_4_5 = $sisa_2_3 - $sisa_pajak;
			?>
			<tr class="table-active3-csf">
				<th class="text-center" style="vertical-align:middle">VI</th>
				<th class="text-left">POSISI ( IV + V )</th>
				<?php
				$styleColor = $rencana_posisi_4_5 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $rencana_posisi_4_5 < 0 ? "(".number_format(-$rencana_posisi_4_5,0,',','.').")" : number_format($rencana_posisi_4_5,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_4_5_sd_bulan_lalu < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_4_5_sd_bulan_lalu < 0 ? "(".number_format(-$jumlah_posisi_4_5_sd_bulan_lalu,0,',','.').")" : number_format($jumlah_posisi_4_5_sd_bulan_lalu,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_4_5_bulan_ini < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_4_5_bulan_ini < 0 ? "(".number_format(-$jumlah_posisi_4_5_bulan_ini,0,',','.').")" : number_format($jumlah_posisi_4_5_bulan_ini,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_4_5_bulan_ini_sd < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_4_5_bulan_ini_sd < 0 ? "(".number_format(-$jumlah_posisi_4_5_bulan_ini_sd,0,',','.').")" : number_format($jumlah_posisi_4_5_bulan_ini_sd,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_4_5_2 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_4_5_2 < 0 ? "(".number_format(-$jumlah_posisi_4_5_2,0,',','.').")" : number_format($jumlah_posisi_4_5_2,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_4_5_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_4_5_3 < 0 ? "(".number_format(-$jumlah_posisi_4_5_3,0,',','.').")" : number_format($jumlah_posisi_4_5_3,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_4_5_4 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_4_5_4 < 0 ? "(".number_format(-$jumlah_posisi_4_5_4,0,',','.').")" : number_format($jumlah_posisi_4_5_4,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_4_5 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_4_5 < 0 ? "(".number_format(-$jumlah_4_5,0,',','.').")" : number_format($jumlah_4_5,0,',','.');?></th>
				<?php
				$styleColor = $sisa_4_5 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $sisa_4_5 < 0 ? "(".number_format(-$sisa_4_5,0,',','.').")" : number_format($sisa_4_5,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-center" rowspan="4" style="vertical-align:middle">VII</th>
				<th class="text-left" colspan="10"><u>PINJAMAN</u></th>
			</tr>
			<?php
			$rencana_penerimaan_pinjaman = 0;

			$penerimaan_pinjaman_sd_bulan_lalu = $this->db->select('sum(jumlah) as total')
			->from('pmm_terima_uang')
			->where("terima_dari = 147")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date_awal' and '$last_opname')")
			->get()->row_array();
			$penerimaan_pinjaman_sd_bulan_lalu = $penerimaan_pinjaman_sd_bulan_lalu['total'];

			$penerimaan_pinjaman = $this->db->select('sum(jumlah) as total')
			->from('pmm_terima_uang')
			->where("terima_dari = 147")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date_1_awal' and '$date_1_akhir')")
			->get()->row_array(); 
			$penerimaan_pinjaman_bulan_ini = $penerimaan_pinjaman['total'];

			$penerimaan_pinjaman_bulan_ini_sd = $this->db->select('sum(jumlah) as total')
			->from('pmm_terima_uang')
			->where("terima_dari = 147")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date_awal' and '$date_1_akhir')")
			->get()->row_array(); 
			$penerimaan_pinjaman_bulan_ini_sd = $penerimaan_pinjaman_bulan_ini_sd['total'];

			$penerimaan_pinjaman_2 = $this->db->select('SUM(penerimaan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$penerimaan_pinjaman_2 = $penerimaan_pinjaman_2['total'];

			$penerimaan_pinjaman_3 = $this->db->select('SUM(penerimaan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$penerimaan_pinjaman_3 = $penerimaan_pinjaman_3['total'];

			$penerimaan_pinjaman_4 = $this->db->select('SUM(penerimaan) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$penerimaan_pinjaman_4 = $penerimaan_pinjaman_4['total'];

			$jumlah_penerimaan_pinjaman = $penerimaan_pinjaman_bulan_ini_sd + $penerimaan_pinjaman_2 + $penerimaan_pinjaman_3 + $penerimaan_pinjaman_4;
			$sisa_penerimaan_pinjaman = $rencana_penerimaan_pinjaman - $jumlah_penerimaan_pinjaman;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;1. Penerimaan Pinjaman</th>
				<th class="text-right"><?php echo number_format($rencana_penerimaan_pinjaman,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penerimaan_pinjaman_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penerimaan_pinjaman_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penerimaan_pinjaman_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penerimaan_pinjaman_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penerimaan_pinjaman_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($penerimaan_pinjaman_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_penerimaan_pinjaman,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_penerimaan_pinjaman,0,',','.');?></th>
			</tr>
			<?php
			$rencana_pengembalian_pinjaman = 0;

			$pengembalian_pinjaman_sd_bulan_lalu = $this->db->select('sum(jumlah) as total')
			->from('pmm_transfer')
			->where("setor_ke = 147")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date_awal' and '$last_opname')")
			->get()->row_array();
			$pengembalian_pinjaman_sd_bulan_lalu = $pengembalian_pinjaman_sd_bulan_lalu['total'];

			$pengembalian_pinjaman = $this->db->select('sum(jumlah) as total')
			->from('pmm_transfer')
			->where("setor_ke = 147")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date_1_awal' and '$date_1_akhir')")
			->get()->row_array(); 
			$pengembalian_pinjaman_bulan_ini = $pengembalian_pinjaman['total'];

			$pengembalian_pinjaman_bulan_ini_sd = $this->db->select('sum(jumlah) as total')
			->from('pmm_transfer')
			->where("setor_ke = 147")
			->where("status = 'PAID'")
			->where("(tanggal_transaksi between '$date_awal' and '$date_1_akhir')")
			->get()->row_array(); 
			$pengembalian_pinjaman_bulan_ini_sd = $pengembalian_pinjaman_bulan_ini_sd['total'];

			$pengembalian_pinjaman_2 = $this->db->select('SUM(pengembalian) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$pengembalian_pinjaman_2 = $pengembalian_pinjaman_2['total'];

			$pengembalian_pinjaman_3 = $this->db->select('SUM(pengembalian) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$pengembalian_pinjaman_3 = $pengembalian_pinjaman_3['total'];

			$pengembalian_pinjaman_4 = $this->db->select('SUM(pengembalian) as total')
			->from('rencana_cash_flow')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$pengembalian_pinjaman_4 = $pengembalian_pinjaman_4['total'];

			$jumlah_pengembalian_pinjaman = $pengembalian_pinjaman_bulan_ini_sd + $pengembalian_pinjaman_2 + $pengembalian_pinjaman_3 + $pengembalian_pinjaman_4;
			$sisa_pengembalian_pinjaman = $rencana_pengembalian_pinjaman - $jumlah_pengembalian_pinjaman;
			?>
			<tr class="table-active3-csf">
				<th class="text-left">&nbsp;&nbsp;2. Pengembalian Pinjaman</th>
				<th class="text-right"><?php echo number_format($rencana_pengembalian_pinjaman,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pengembalian_pinjaman_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pengembalian_pinjaman_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pengembalian_pinjaman_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pengembalian_pinjaman_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pengembalian_pinjaman_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($pengembalian_pinjaman_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pengembalian_pinjaman,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sisa_pengembalian_pinjaman,0,',','.');?></th>
			</tr>
			<?php
			$rencana_jumlah_pinjaman = $rencana_penerimaan_pinjaman - $rencana_pengembalian_pinjaman;
			$jumlah_pinjaman_sd_bulan_lalu = $penerimaan_pinjaman_sd_bulan_lalu - $pengembalian_pinjaman_sd_bulan_lalu;
			$jumlah_pinjaman_bulan_ini = $penerimaan_pinjaman_bulan_ini - $pengembalian_pinjaman_bulan_ini;
			$jumlah_pinjaman_bulan_ini_sd = $penerimaan_pinjaman_bulan_ini_sd + $pengembalian_pinjaman_bulan_ini_sd;
			$jumlah_pinjaman_2 = $penerimaan_pinjaman_2 - $pengembalian_pinjaman_2;
			$jumlah_pinjaman_3 = $penerimaan_pinjaman_3 - $pengembalian_pinjaman_3;
			$jumlah_pinjaman_4 = $penerimaan_pinjaman_4 - $pengembalian_pinjaman_4;
			$jumlah_jumlah_pinjaman = $jumlah_penerimaan_pinjaman - $jumlah_pengembalian_pinjaman;
			$jumlah_sisa_pinjaman = $sisa_penerimaan_pinjaman - $sisa_pengembalian_pinjaman;
			?>
			<tr class="table-active2-csf">
				<th class="text-left">JUMLAH VII</th>
				<th class="text-right"><?php echo number_format($rencana_jumlah_pinjaman,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pinjaman_sd_bulan_lalu,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pinjaman_bulan_ini,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pinjaman_bulan_ini_sd,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pinjaman_2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pinjaman_3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_pinjaman_4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_jumlah_pinjaman,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_sisa_pinjaman,0,',','.');?></th>
			</tr>
			<?php
			$rencana_posisi_6_7 = $rencana_posisi_4_5 - $rencana_jumlah_pinjaman;
			$jumlah_posisi_6_7_sd_bulan_lalu = $jumlah_posisi_4_5_sd_bulan_lalu - $jumlah_pinjaman_sd_bulan_lalu;
			$jumlah_posisi_6_7_bulan_ini = $jumlah_posisi_4_5_bulan_ini - $jumlah_pinjaman_bulan_ini;
			$jumlah_posisi_6_7_bulan_ini_sd = $jumlah_posisi_4_5_bulan_ini_sd - $jumlah_pinjaman_bulan_ini_sd;
			$jumlah_posisi_6_7_2 = $jumlah_posisi_4_5_2 - $jumlah_pinjaman_2;
			$jumlah_posisi_6_7_3 = $jumlah_posisi_4_5_3 - $jumlah_pinjaman_3;
			$jumlah_posisi_6_7_4 = $jumlah_posisi_4_5_4 - $jumlah_pinjaman_4;
			$jumlah_6_7 = $jumlah_4_5 - $jumlah_pinjaman;
			$sisa_6_7 = $sisa_4_5 - $sisa_pinjaman;
			?>
			<tr class="table-active3-csf">
				<th class="text-center" style="vertical-align:middle">VIII</th>
				<th class="text-left">POSISI ( VI + VII )</th>
				<?php
				$styleColor = $rencana_posisi_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $rencana_posisi_6_7 < 0 ? "(".number_format(-$rencana_posisi_6_7,0,',','.').")" : number_format($rencana_posisi_6_7,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_sd_bulan_lalu < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_sd_bulan_lalu < 0 ? "(".number_format(-$jumlah_posisi_6_7_sd_bulan_lalu,0,',','.').")" : number_format($jumlah_posisi_6_7_sd_bulan_lalu,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_bulan_ini < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_bulan_ini < 0 ? "(".number_format(-$jumlah_posisi_6_7_bulan_ini,0,',','.').")" : number_format($jumlah_posisi_6_7_bulan_ini,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_bulan_ini_sd < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_bulan_ini_sd < 0 ? "(".number_format(-$jumlah_posisi_6_7_bulan_ini_sd,0,',','.').")" : number_format($jumlah_posisi_6_7_bulan_ini_sd,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_2 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_2 < 0 ? "(".number_format(-$jumlah_posisi_6_7_2,0,',','.').")" : number_format($jumlah_posisi_6_7_2,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_3 < 0 ? "(".number_format(-$jumlah_posisi_6_7_3,0,',','.').")" : number_format($jumlah_posisi_6_7_3,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_4 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_4 < 0 ? "(".number_format(-$jumlah_posisi_6_7_4,0,',','.').")" : number_format($jumlah_posisi_6_7_4,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_6_7 < 0 ? "(".number_format(-$jumlah_6_7,0,',','.').")" : number_format($jumlah_6_7,0,',','.');?></th>
				<?php
				$styleColor = $sisa_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $sisa_6_7 < 0 ? "(".number_format(-$sisa_6_7,0,',','.').")" : number_format($sisa_6_7,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-center" style="vertical-align:middle">IX</th>
				<th class="text-left">KAS AWAL</th>
				<?php
				$styleColor = $rencana_posisi_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $rencana_posisi_6_7 < 0 ? "(".number_format(-$rencana_posisi_6_7,0,',','.').")" : number_format($rencana_posisi_6_7,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_sd_bulan_lalu < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_sd_bulan_lalu < 0 ? "(".number_format(-$jumlah_posisi_6_7_sd_bulan_lalu,0,',','.').")" : number_format($jumlah_posisi_6_7_sd_bulan_lalu,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_bulan_ini < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_bulan_ini < 0 ? "(".number_format(-$jumlah_posisi_6_7_bulan_ini,0,',','.').")" : number_format($jumlah_posisi_6_7_bulan_ini,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_bulan_ini_sd < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_bulan_ini_sd < 0 ? "(".number_format(-$jumlah_posisi_6_7_bulan_ini_sd,0,',','.').")" : number_format($jumlah_posisi_6_7_bulan_ini_sd,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_2 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_2 < 0 ? "(".number_format(-$jumlah_posisi_6_7_2,0,',','.').")" : number_format($jumlah_posisi_6_7_2,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_3 < 0 ? "(".number_format(-$jumlah_posisi_6_7_3,0,',','.').")" : number_format($jumlah_posisi_6_7_3,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_4 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_4 < 0 ? "(".number_format(-$jumlah_posisi_6_7_4,0,',','.').")" : number_format($jumlah_posisi_6_7_4,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_6_7 < 0 ? "(".number_format(-$jumlah_6_7,0,',','.').")" : number_format($jumlah_6_7,0,',','.');?></th>
				<?php
				$styleColor = $sisa_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $sisa_6_7 < 0 ? "(".number_format(-$sisa_6_7,0,',','.').")" : number_format($sisa_6_7,0,',','.');?></th>
			</tr>
			<tr class="table-active3-csf">
				<th class="text-center" style="vertical-align:middle">X</th>
				<th class="text-left">KAS AKHIR</th>
				<?php
				$styleColor = $rencana_posisi_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $rencana_posisi_6_7 < 0 ? "(".number_format(-$rencana_posisi_6_7,0,',','.').")" : number_format($rencana_posisi_6_7,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_sd_bulan_lalu < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_sd_bulan_lalu < 0 ? "(".number_format(-$jumlah_posisi_6_7_sd_bulan_lalu,0,',','.').")" : number_format($jumlah_posisi_6_7_sd_bulan_lalu,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_bulan_ini < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_bulan_ini < 0 ? "(".number_format(-$jumlah_posisi_6_7_bulan_ini,0,',','.').")" : number_format($jumlah_posisi_6_7_bulan_ini,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_bulan_ini_sd < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_bulan_ini_sd < 0 ? "(".number_format(-$jumlah_posisi_6_7_bulan_ini_sd,0,',','.').")" : number_format($jumlah_posisi_6_7_bulan_ini_sd,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_2 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_2 < 0 ? "(".number_format(-$jumlah_posisi_6_7_2,0,',','.').")" : number_format($jumlah_posisi_6_7_2,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_3 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_3 < 0 ? "(".number_format(-$jumlah_posisi_6_7_3,0,',','.').")" : number_format($jumlah_posisi_6_7_3,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_posisi_6_7_4 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_posisi_6_7_4 < 0 ? "(".number_format(-$jumlah_posisi_6_7_4,0,',','.').")" : number_format($jumlah_posisi_6_7_4,0,',','.');?></th>
				<?php
				$styleColor = $jumlah_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $jumlah_6_7 < 0 ? "(".number_format(-$jumlah_6_7,0,',','.').")" : number_format($jumlah_6_7,0,',','.');?></th>
				<?php
				$styleColor = $sisa_6_7 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $sisa_6_7 < 0 ? "(".number_format(-$sisa_6_7,0,',','.').")" : number_format($sisa_6_7,0,',','.');?></th>
			</tr>
	    </table>
		<?php
	}

	public function evaluasi_target_produksi($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
			<style type="text/css">
				table tr.table-active{
					background-color: #F0F0F0;
					font-size: 12px;
					font-weight: bold;
					color: black;
				}
					
				table tr.table-active2{
					background-color: #E8E8E8;
					font-size: 12px;
					font-weight: bold;
				}
					
				table tr.table-active3{
					font-size: 12px;
					background-color: #F0F0F0;
				}
					
				table tr.table-active4{
					background-color: #666666;
					font-weight: bold;
					font-size: 12px;
					color: white;
				}
				
				table tr.table-active5{
					background-color: #E8E8E8;
					text-decoration: underline;
					font-size: 12px;
					font-weight: bold;
					color: red;
				}
				table tr.table-activeago1{
					background-color: #ffd966;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
				table tr.table-activeopening{
					background-color: #2986cc;
					font-weight: bold;
					font-size: 12px;
					color: black;
				}
			</style>

			<!-- RAP -->
			<?php
			//VOLUME
			$date_now = date('Y-m-d');
			$rencana_kerja = $this->db->select('r.*')
			->from('rak r')
			->where("(r.tanggal_rencana_kerja between '$date1' and '$date2')")
			->group_by("r.id")
			->get()->result_array();

			$total_vol_a = 0;
			$total_vol_b = 0;
			$total_vol_c = 0;
			$total_vol_d = 0;
			$total_vol_e = 0;

			$total_price_a = 0;
			$total_price_b = 0;
			$total_price_c = 0;
			$total_price_d = 0;
			$total_price_e = 0;

			//$total_overhead = 0;
			//$total_diskonto = 0;

			foreach ($rencana_kerja as $x){
				$total_price_a += $x['vol_produk_a'] * $x['price_a'];
				$total_price_b += $x['vol_produk_b'] * $x['price_b'];
				$total_price_c += $x['vol_produk_c'] * $x['price_c'];
				$total_price_d += $x['vol_produk_d'] * $x['price_d'];
				$total_price_e += $x['vol_produk_e'] * $x['price_e'];

				$total_vol_a += $x['vol_produk_a'];
				$total_vol_b += $x['vol_produk_b'];
				$total_vol_c += $x['vol_produk_c'];
				$total_vol_d += $x['vol_produk_d'];
				$total_vol_e += $x['vol_produk_e'];

				//$total_overhead += $x['overhead'];
				//$total_diskonto += $x['biaya_bank'];
			}

			$volume_rap_produk_a = $total_vol_a;
			$volume_rap_produk_b = $total_vol_b;
			$volume_rap_produk_c = $total_vol_c;
			$volume_rap_produk_d = $total_vol_d;
			$volume_rap_produk_e = $total_vol_e;

			$total_rap_volume = $volume_rap_produk_a + $volume_rap_produk_b + $volume_rap_produk_c + $volume_rap_produk_d + $volume_rap_produk_e;
			
			$harga_jual_125_rap = $total_price_a;
			$harga_jual_225_rap = $total_price_b;
			$harga_jual_250_rap = $total_price_c;
			$harga_jual_250_18_rap = $total_price_d;
			$harga_jual_300_rap = $total_price_e;

			$nilai_jual_125 = $harga_jual_125_rap;
			$nilai_jual_225 = $harga_jual_225_rap;
			$nilai_jual_250 = $harga_jual_250_rap;
			$nilai_jual_250_18 = $harga_jual_250_18_rap;
			$nilai_jual_300 = $harga_jual_300_rap;
			$nilai_jual_all = $nilai_jual_125 + $nilai_jual_225 + $nilai_jual_250 + $nilai_jual_250_18 + $nilai_jual_300;
			
			$total_rap_nilai = $nilai_jual_all;

			//BIAYA
			$komposisi = $this->db->select('pp.date_production, (pp.display_volume) * pk.presentase_a as volume_a, (pp.display_volume) * pk.presentase_b as volume_b, (pp.display_volume) * pk.presentase_c as volume_c, (pp.display_volume) * pk.presentase_d as volume_d, (pp.display_volume * pk.presentase_a) * pk.price_a as nilai_a, (pp.display_volume * pk.presentase_b) * pk.price_b as nilai_b, (pp.display_volume * pk.presentase_c) * pk.price_c as nilai_c, (pp.display_volume * pk.presentase_d) * pk.price_d as nilai_d')
			->from('pmm_productions pp')
			->join('pmm_agregat pk', 'pp.komposisi_id = pk.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->get()->result_array();

			$total_volume_a = 0;
			$total_volume_b = 0;
			$total_volume_c = 0;
			$total_volume_d = 0;

			$total_nilai_a = 0;
			$total_nilai_b = 0;
			$total_nilai_c = 0;
			$total_nilai_d = 0;

			foreach ($komposisi as $x){
				$total_volume_a += $x['volume_a'];
				$total_volume_b += $x['volume_b'];
				$total_volume_c += $x['volume_c'];
				$total_volume_d += $x['volume_d'];
				$total_nilai_a += $x['nilai_a'];
				$total_nilai_b += $x['nilai_b'];
				$total_nilai_c += $x['nilai_c'];
				$total_nilai_d += $x['nilai_d'];
				
			}

			$volume_a = $total_volume_a;
			$volume_b = $total_volume_b;
			$volume_c = $total_volume_c;
			$volume_d = $total_volume_d;

			$nilai_a = $total_nilai_a;
			$nilai_b = $total_nilai_b;
			$nilai_c = $total_nilai_c;
			$nilai_d = $total_nilai_d;

			$price_a = ($total_volume_a!=0)?$total_nilai_a / $total_volume_a * 1:0;
			$price_b = ($total_volume_b!=0)?$total_nilai_b / $total_volume_b * 1:0;
			$price_c = ($total_volume_c!=0)?$total_nilai_c / $total_volume_c * 1:0;
			$price_d = ($total_volume_d!=0)?$total_nilai_d / $total_volume_d * 1:0;

			$total_volume_komposisi = $volume_a + $volume_b + $volume_c + $volume_d;
			$total_nilai_komposisi = $nilai_a + $nilai_b + $nilai_c + $nilai_d;
			
			//BAHAN
			$total_rap_biaya_bahan = $total_nilai_komposisi;

			$total_volume = $this->db->select(' SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->order_by('p.nama_produk','asc')
			->get()->row_array();

			$total_volume_produksi = 0;
			$total_volume_produksi = $total_volume['volume'];

			$rap_alat = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where("rap.tanggal_rap_alat <= '$date2'")
			->where('rap.status','PUBLISH')
			->get()->result_array();

			$total_vol_rap_batching_plant = 0;
			$total_vol_rap_truck_mixer = 0;
			$total_vol_rap_wheel_loader = 0;
			$total_vol_rap_bbm_solar = 0;

			$total_batching_plant = 0;
			$total_truck_mixer = 0;
			$total_wheel_loader = 0;
			$total_bbm_solar = 0;

			foreach ($rap_alat as $x){
				$total_vol_rap_batching_plant += $x['vol_batching_plant'];
				$total_vol_rap_truck_mixer += $x['vol_truck_mixer'];
				$total_vol_rap_wheel_loader += $x['vol_wheel_loader'];
				$total_vol_rap_bbm_solar += $x['vol_bbm_solar'];
				$total_batching_plant = $x['harsat_batching_plant'];
				$total_truck_mixer = $x['harsat_truck_mixer'];
				$total_wheel_loader = $x['harsat_wheel_loader'];
				$total_bbm_solar = $x['harsat_bbm_solar'];
				
			}

			$vol_batching_plant = $total_vol_rap_batching_plant * $total_volume_produksi;
			$vol_truck_mixer = $total_vol_rap_truck_mixer * $total_volume_produksi;
			$vol_wheel_loader = $total_vol_rap_wheel_loader * $total_volume_produksi;
			$vol_bbm_solar = $total_vol_rap_bbm_solar * $total_volume_produksi;

			$batching_plant = $total_batching_plant * $vol_batching_plant;
			$truck_mixer = $total_truck_mixer * $vol_truck_mixer;
			$wheel_loader = $total_wheel_loader * $vol_wheel_loader;
			$transfer_semen = 0;
			$bbm_solar = $total_bbm_solar * $vol_bbm_solar;

			$harsat_batching_plant = ($vol_batching_plant!=0)?$batching_plant / $vol_batching_plant * 1:0;
			$harsat_truck_mixer = ($vol_truck_mixer!=0)?$truck_mixer / $vol_truck_mixer * 1:0;
			$harsat_wheel_loader = ($wheel_loader!=0)?$wheel_loader / $wheel_loader * 1:0;
			$harsat_bbm_solar = ($vol_bbm_solar!=0)?$bbm_solar / $vol_bbm_solar * 1:0;

			$total_nilai_rap_alat = $batching_plant + $truck_mixer + $wheel_loader + $bbm_solar;

			$total_rap_biaya_alat = $total_nilai_rap_alat;
			$total_rap_overhead = 102583 * $total_rap_volume;
			$total_rap_biaya_bank = 29620 * $total_rap_volume;

			$total_biaya_rap_biaya = $total_rap_biaya_bahan + $total_rap_biaya_alat + $total_rap_overhead + $total_rap_biaya_bank;
			?>
			<!-- RAP 2022 -->
			
			<!-- REALISASI -->
			<?php
			$penjualan_realisasi_produk_a = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 2")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_a = $penjualan_realisasi_produk_a['volume'];
			$nilai_realisasi_produk_a = $penjualan_realisasi_produk_a['price'];

			$penjualan_realisasi_produk_b = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 1")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_b = $penjualan_realisasi_produk_b['volume'];
			$nilai_realisasi_produk_b = $penjualan_realisasi_produk_b['price'];

			$penjualan_realisasi_produk_c = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 3")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_c = $penjualan_realisasi_produk_c['volume'];
			$nilai_realisasi_produk_c = $penjualan_realisasi_produk_c['price'];

			$penjualan_realisasi_produk_d = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 11")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_d = $penjualan_realisasi_produk_d['volume'];
			$nilai_realisasi_produk_d = $penjualan_realisasi_produk_d['price'];

			$penjualan_realisasi_produk_e = $this->db->select('p.nama_produk, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume')
			->from('pmm_productions pp')
			->join('produk p', 'pp.product_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("(pp.date_production between '$date1' and '$date2')")
			->where("pp.status = 'PUBLISH'")
			->where("pp.product_id = 41")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.product_id")
			->order_by('p.nama_produk','asc')
			->get()->row_array();
			$volume_realisasi_produk_e = $penjualan_realisasi_produk_e['volume'];
			$nilai_realisasi_produk_e = $penjualan_realisasi_produk_e['price'];

			$total_realisasi_volume = $volume_realisasi_produk_a + $volume_realisasi_produk_b + $volume_realisasi_produk_c + $volume_realisasi_produk_d + $volume_realisasi_produk_e;
			$total_realisasi_nilai = $nilai_realisasi_produk_a + $nilai_realisasi_produk_b + $nilai_realisasi_produk_c + $nilai_realisasi_produk_d + $nilai_realisasi_produk_e;
			?>
			<!-- REALISASI SD. SAAT INI -->

			<!-- REALISASI BIAYA -->
			<?php
			//BAHAN
			$akumulasi = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar as total_nilai_keluar')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi = 0;

			foreach ($akumulasi as $a){
				$total_akumulasi += $a['total_nilai_keluar'];
			}

			$total_bahan_akumulasi = $total_akumulasi;
			//END BAHAN
			?>

			<?php
			//ALAT
			$nilai_alat = $this->db->select('SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("(prm.date_receipt between '$date1' and '$date2')")
			->where("p.kategori_produk = '5'")
			->where("po.status in ('PUBLISH','CLOSED')")
			->get()->row_array();

			$akumulasi_bbm = $this->db->select('pp.date_akumulasi, pp.total_nilai_keluar_2 as total_nilai_keluar_2')
			->from('akumulasi pp')
			->where("(pp.date_akumulasi between '$date1' and '$date2')")
			->get()->result_array();

			$total_akumulasi_bbm = 0;

			foreach ($akumulasi_bbm as $b){
				$total_akumulasi_bbm += $b['total_nilai_keluar_2'];
			}

			$total_nilai_bbm = $total_akumulasi_bbm;
			$total_insentif_tm = 0;
			$insentif_tm = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->where("pdb.akun = 220")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_insentif_tm = $insentif_tm['total'];

			$total_alat_realisasi = $nilai_alat['nilai'] + $total_akumulasi_bbm + $total_insentif_tm;
			//END ALAT
			?>

			<?php
			//OVERHEAD
			$overhead_15_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 168 ") //Biaya Diskonto Bank
			->where("c.id <> 219 ") //Biaya Alat Batching Plant 
			->where("c.id <> 220 ") //Biaya Alat Truck Mixer
			->where("c.id <> 221 ") //Biaya Alat Wheel Loader
			->where("c.id <> 228 ") //Biaya Persiapan
			->where("c.id <> 505 ") //Biaya Oli
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$overhead_jurnal_15_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where('c.coa_category',15)
			->where("c.id <> 168 ") //Biaya Diskonto Bank
			->where("c.id <> 219 ") //Biaya Alat Batching Plant 
			->where("c.id <> 220 ") //Biaya Alat Truck Mixer
			->where("c.id <> 221 ") //Biaya Alat Wheel Loader
			->where("c.id <> 228 ") //Biaya Persiapan
			->where("c.id <> 505 ") //Biaya Oli
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_overhead_realisasi =  $overhead_15_realisasi['total'] + $overhead_jurnal_15_realisasi['total'];
			?>

			<?php
			//DISKONTO
			$diskonto_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 168")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_diskonto_realisasi = $diskonto_realisasi['total'];
			//DISKONTO
			?>

			<?php
			//PERSIAPAN
			$persiapan_biaya_realisasi = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$persiapan_jurnal_realisasi = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 228")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$total_persiapan_realisasi = $persiapan_biaya_realisasi['total'] + $persiapan_jurnal_realisasi['total'];
			$total_biaya_realisasi = $total_bahan_akumulasi + $total_alat_realisasi + $total_overhead_realisasi + $total_diskonto_realisasi;
			//END PERSIAPAN
			?>
			<!-- REALISASI BIAYA -->

			<!-- SISA -->
			<?php
			$sisa_volume_produk_a = $volume_rap_produk_a - $volume_realisasi_produk_a;
			$sisa_volume_produk_b = $volume_rap_produk_b - $volume_realisasi_produk_b;
			$sisa_volume_produk_c = $volume_rap_produk_c - $volume_realisasi_produk_c;
			$sisa_volume_produk_d = $volume_rap_produk_d - $volume_realisasi_produk_d;
			$sisa_volume_produk_e = $volume_rap_produk_e - $volume_realisasi_produk_e;

			$total_sisa_volume_all_produk = $sisa_volume_produk_a + $sisa_volume_produk_b + $sisa_volume_produk_c + $sisa_volume_produk_d + $sisa_volume_produk_e;
			$total_sisa_nilai_all_produk = $total_rap_nilai - $total_realisasi_nilai;

			$sisa_biaya_bahan = $total_rap_biaya_bahan - $total_bahan_akumulasi;
			$sisa_biaya_alat = $total_rap_biaya_alat - $total_alat_realisasi;
			$sisa_overhead = $total_rap_overhead - $total_overhead_realisasi;
			$sisa_biaya_bank = $total_rap_biaya_bank - $total_diskonto_realisasi;
			?>
			<!-- SISA -->

			<!-- TOTAL -->
			<?php
			$total_laba_rap = $total_rap_nilai - $total_biaya_rap_biaya;
			$total_laba_realisasi = $total_realisasi_nilai - $total_biaya_realisasi;

			$sisa_biaya_realisasi = $total_biaya_rap_biaya - $total_biaya_realisasi;
			$presentase_realisasi = ($total_laba_realisasi / $total_realisasi_nilai) * 100;
			?>
			<!-- TOTAL -->

			<tr class="table-active4">
				<th width="5%" class="text-center">NO.</th>
				<th class="text-center">URAIAN</th>
				<th class="text-center">SATUAN</th>
				<th class="text-center">RENCANA</th>
				<th class="text-center">REALISASI</th>
				<th class="text-center">EVALUASI</th>
	        </tr>
			<tr class="table-active2">
				<th class="text-left" colspan="6">RENCANA PRODUKSI & PENDAPATAN USAHA</th>
			</tr>
			<?php
				$styleColorA = $sisa_volume_produk_a < 0 ? 'color:red' : 'color:black';
				$styleColorB = $sisa_volume_produk_b < 0 ? 'color:red' : 'color:black';
				$styleColorC = $sisa_volume_produk_c < 0 ? 'color:red' : 'color:black';
				$styleColorD = $sisa_volume_produk_d < 0 ? 'color:red' : 'color:black';
				$styleColorE = $total_sisa_volume_all_produk < 0 ? 'color:red' : 'color:black';
				$styleColorF = $total_sisa_nilai_all_produk < 0 ? 'color:red' : 'color:black';
				$styleColorG = $sisa_biaya_bahan < 0 ? 'color:red' : 'color:black';
				$styleColorH = $sisa_biaya_alat < 0 ? 'color:red' : 'color:black';
				$styleColorI = $sisa_overhead < 0 ? 'color:red' : 'color:black';
				$styleColorJ = $sisa_biaya_bank < 0 ? 'color:red' : 'color:black';
				$styleColorL = $sisa_biaya_realisasi < 0 ? 'color:red' : 'color:black';
				$styleColorM = $total_laba_realisasi < 0 ? 'color:red' : 'color:black';
				$styleColorN = $sisa_volume_produk_e < 0 ? 'color:red' : 'color:black';
				$styleColorO = $presentase_realisasi < 0 ? 'color:red' : 'color:black';
			?>
			<tr class="table-active3">
				<th class="text-center">1.</th>
				<th class="text-left">Beton K 125 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_a,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorA ?>"><?php echo number_format($sisa_volume_produk_a,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">2.</th>
				<th class="text-left">Beton K 225 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_b,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorB ?>"><?php echo number_format($sisa_volume_produk_b,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">3.</th>
				<th class="text-left">Beton K 250 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_c,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorC ?>"><?php echo number_format($sisa_volume_produk_c,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">4.</th>
				<th class="text-left">Beton K 250 (18±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_d,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorD ?>"><?php echo number_format($sisa_volume_produk_d,2,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">5.</th>
				<th class="text-left">Beton K 300 (10±2)</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($volume_rap_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($volume_realisasi_produk_e,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorN ?>"><?php echo number_format($sisa_volume_produk_e,2,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">TOTAL VOLUME</th>
				<th class="text-center">M3</th>
				<th class="text-right"><?php echo number_format($total_rap_volume,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_realisasi_volume,2,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorE ?>"><?php echo number_format($total_sisa_volume_all_produk,2,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">PENDAPATAN USAHA</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_rap_nilai,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_realisasi_nilai,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorF ?>"><?php echo number_format($total_sisa_nilai_all_produk,0,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-left" colspan="6">BIAYA</th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">1.</th>
				<th class="text-left">Bahan</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_bahan,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_evaluasi_bahan?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($total_bahan_akumulasi,0,',','.');?></a></th>
				<th class="text-right" style="<?php echo $styleColorG ?>"><?php echo number_format($sisa_biaya_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">2.</th>
				<th class="text-left">Alat</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_alat,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_laporan_evaluasi_alat?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($total_alat_realisasi,0,',','.');?></a></th>
				<th class="text-right" style="<?php echo $styleColorH ?>"><?php echo number_format($sisa_biaya_alat,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">3.</th>
				<th class="text-left">Overhead</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_overhead,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_overhead?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($total_overhead_realisasi,0,',','.');?></a></th>
				<th class="text-right" style="<?php echo $styleColorI ?>"><?php echo number_format($sisa_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-active3">
				<th class="text-center">4.</th>
				<th class="text-left">Biaya Bank</th>
				<th class="text-center">LS</th>
				<th class="text-right"><?php echo number_format($total_rap_biaya_bank,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_diskonto?filter_date=".$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]))) ?>"><?php echo number_format($total_diskonto_realisasi,0,',','.');?></a></th>
				<th class="text-right" style="<?php echo $styleColorJ ?>"><?php echo number_format($sisa_biaya_bank,0,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">JUMLAH</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_biaya_rap_biaya,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_biaya_realisasi,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorL ?>"><?php echo number_format($sisa_biaya_realisasi,0,',','.');?></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">LABA</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format($total_laba_rap,0,',','.');?></th>
				<th class="text-right" style="<?php echo $styleColorM ?>"><?php echo number_format($total_laba_realisasi,0,',','.');?></th>
				<th class="text-right"></th>
			</tr>
			<tr class="table-active2">
				<th class="text-right" colspan="2">PRESENTASE</th>
				<th class="text-center"></th>
				<th class="text-right"><?php echo number_format(($total_laba_rap / $total_rap_nilai) * 100,2,',','.');?> %</th>
				<th class="text-right" style="<?php echo $styleColorO ?>"><?php echo number_format($presentase_realisasi,2,',','.');?> %</th>
				<th class="text-right"></th>
			</tr>	
	    </table>
		<?php
	}

	public function rencana_kerja($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d F Y',strtotime($arr_filter_date[0])).' - '.date('d F Y',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
			<style type="text/css">
				table tr.table-judul{
					background-color: #666666;
					font-size: 11px;
					font-weight: bold;
					color: white;
				}
					
				table tr.table-baris{
					background-color: #F0F0F0;
					font-size: 11px;
					font-weight: bold;
				}

				table tr.table-total{
					background-color: #FFFF00;
					font-size: 11px;
					font-weight: bold;
				}

				table tr.table-subtotal{
					background-color: #b1cee8;
					font-size: 11px;
					font-weight: bold;
				}

				table tr.table-total2{
					background-color: #ea9999;
					font-size: 11px;
					font-weight: bold;
				}
				
			</style>
			<?php
			//AKUMULASI
            $kunci_bahan_baku = $this->db->select('date')->order_by('date','desc')->limit(1)->get_where('kunci_bahan_baku')->row_array();
            $last_opname = date('d-m-Y', strtotime('0 days', strtotime($kunci_bahan_baku['date'])));

			$date_0_awal = date('2025-01-01');
			$date_0_akhir = date('Y-m-d', strtotime($last_opname));

			$akumulasi_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_0_awal' and '$date_0_akhir'")
			->get()->row_array();
			$akumulasi_vol_produk_a = $akumulasi_produk['vol_produk_a'];
			$akumulasi_produk_a = $akumulasi_produk['price_a'];
			$akumulasi_vol_produk_b = $akumulasi_produk['vol_produk_b'];
			$akumulasi_produk_b = $akumulasi_produk['price_b'];
			$akumulasi_vol_produk_c = $akumulasi_produk['vol_produk_c'];
			$akumulasi_produk_c = $akumulasi_produk['price_c'];
			$akumulasi_vol_produk_d = $akumulasi_produk['vol_produk_d'];
			$akumulasi_produk_d = $akumulasi_produk['price_d'];
			$akumulasi_vol_produk_e = $akumulasi_produk['vol_produk_e'];
			$akumulasi_produk_e = $akumulasi_produk['price_e'];
			$akumulasi_vol_produk_f = $akumulasi_produk['vol_produk_f'];
			$akumulasi_produk_f = $akumulasi_produk['price_f'];
			$akumulasi_vol_boulder = $akumulasi_produk['vol_boulder'];
			$akumulasi_boulder = $akumulasi_produk['boulder'];
			$akumulasi_stone_crusher = $akumulasi_produk['stone_crusher'];
			$akumulasi_wheel_loader = $akumulasi_produk['wheel_loader'];
			$akumulasi_maintenance = $akumulasi_produk['maintenance'];
			$akumulasi_bbm_solar = $akumulasi_produk['bbm_solar'];
			$akumulasi_tangki = $akumulasi_produk['tangki'];
			$akumulasi_timbangan = $akumulasi_produk['timbangan'];
			$akumulasi_overhead= $akumulasi_produk['overhead'];

			//BULAN SAAT INI
			$date_now = date('Y-m-d');
			$date_1_awal = date('Y-m-01', (strtotime($date_now)));
			$date_1_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_1_awal)));
			$rak1_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_1_awal' and '$date_1_akhir'")
			->get()->row_array();
			$rak1_vol_produk_a = $rak1_produk['vol_produk_a'];
			$rak1_produk_a = $rak1_produk['price_a'];
			$rak1_vol_produk_b = $rak1_produk['vol_produk_b'];
			$rak1_produk_b = $rak1_produk['price_b'];
			$rak1_vol_produk_c = $rak1_produk['vol_produk_c'];
			$rak1_produk_c = $rak1_produk['price_c'];
			$rak1_vol_produk_d = $rak1_produk['vol_produk_d'];
			$rak1_produk_d = $rak1_produk['price_d'];
			$rak1_vol_produk_e = $rak1_produk['vol_produk_e'];
			$rak1_produk_e = $rak1_produk['price_e'];
			$rak1_vol_produk_f = $rak1_produk['vol_produk_f'];
			$rak1_produk_f = $rak1_produk['price_f'];
			$rak1_vol_boulder = $rak1_produk['vol_boulder'];
			$rak1_boulder = $rak1_produk['boulder'];
			$rak1_stone_crusher = $rak1_produk['stone_crusher'];
			$rak1_wheel_loader = $rak1_produk['wheel_loader'];
			$rak1_maintenance = $rak1_produk['maintenance'];
			$rak1_bbm_solar = $rak1_produk['bbm_solar'];
			$rak1_tangki = $rak1_produk['tangki'];
			$rak1_timbangan = $rak1_produk['timbangan'];
			$rak1_overhead= $rak1_produk['overhead'];

			//BULAN 2
			$date_2_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_1_akhir)));
			$date_2_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_2_awal)));
			$rak2_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_2_awal' and '$date_2_akhir'")
			->get()->row_array();
			$rak2_vol_produk_a = $rak2_produk['vol_produk_a'];
			$rak2_produk_a = $rak2_produk['price_a'];
			$rak2_vol_produk_b = $rak2_produk['vol_produk_b'];
			$rak2_produk_b = $rak2_produk['price_b'];
			$rak2_vol_produk_c = $rak2_produk['vol_produk_c'];
			$rak2_produk_c = $rak2_produk['price_c'];
			$rak2_vol_produk_d = $rak2_produk['vol_produk_d'];
			$rak2_produk_d = $rak2_produk['price_d'];
			$rak2_vol_produk_e = $rak2_produk['vol_produk_e'];
			$rak2_produk_e = $rak2_produk['price_e'];
			$rak2_vol_produk_f = $rak2_produk['vol_produk_f'];
			$rak2_produk_f = $rak2_produk['price_f'];
			$rak2_vol_boulder = $rak2_produk['vol_boulder'];
			$rak2_boulder = $rak2_produk['boulder'];
			$rak2_stone_crusher = $rak2_produk['stone_crusher'];
			$rak2_wheel_loader = $rak2_produk['wheel_loader'];
			$rak2_maintenance = $rak2_produk['maintenance'];
			$rak2_bbm_solar = $rak2_produk['bbm_solar'];
			$rak2_tangki = $rak2_produk['tangki'];
			$rak2_timbangan = $rak2_produk['timbangan'];
			$rak2_overhead= $rak2_produk['overhead'];
			
			//BULAN 3
			$date_3_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_2_akhir)));
			$date_3_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_3_awal)));
			$rak3_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_3_awal' and '$date_3_akhir'")
			->get()->row_array();
			$rak3_vol_produk_a = $rak3_produk['vol_produk_a'];
			$rak3_produk_a = $rak3_produk['price_a'];
			$rak3_vol_produk_b = $rak3_produk['vol_produk_b'];
			$rak3_produk_b = $rak3_produk['price_b'];
			$rak3_vol_produk_c = $rak3_produk['vol_produk_c'];
			$rak3_produk_c = $rak3_produk['price_c'];
			$rak3_vol_produk_d = $rak3_produk['vol_produk_d'];
			$rak3_produk_d = $rak3_produk['price_d'];
			$rak3_vol_produk_e = $rak3_produk['vol_produk_e'];
			$rak3_produk_e = $rak3_produk['price_e'];
			$rak3_vol_produk_f = $rak3_produk['vol_produk_f'];
			$rak3_produk_f = $rak3_produk['price_f'];
			$rak3_vol_boulder = $rak3_produk['vol_boulder'];
			$rak3_boulder = $rak3_produk['boulder'];
			$rak3_stone_crusher = $rak3_produk['stone_crusher'];
			$rak3_wheel_loader = $rak3_produk['wheel_loader'];
			$rak3_maintenance = $rak3_produk['maintenance'];
			$rak3_bbm_solar = $rak3_produk['bbm_solar'];
			$rak3_tangki = $rak3_produk['tangki'];
			$rak3_timbangan = $rak3_produk['timbangan'];
			$rak3_overhead= $rak3_produk['overhead'];

			//BULAN 4
			$date_4_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_3_akhir)));
			$date_4_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_4_awal)));
			$rak4_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_4_awal' and '$date_4_akhir'")
			->get()->row_array();
			$rak4_vol_produk_a = $rak4_produk['vol_produk_a'];
			$rak4_produk_a = $rak4_produk['price_a'];
			$rak4_vol_produk_b = $rak4_produk['vol_produk_b'];
			$rak4_produk_b = $rak4_produk['price_b'];
			$rak4_vol_produk_c = $rak4_produk['vol_produk_c'];
			$rak4_produk_c = $rak4_produk['price_c'];
			$rak4_vol_produk_d = $rak4_produk['vol_produk_d'];
			$rak4_produk_d = $rak4_produk['price_d'];
			$rak4_vol_produk_e = $rak4_produk['vol_produk_e'];
			$rak4_produk_e = $rak4_produk['price_e'];
			$rak4_vol_produk_f = $rak4_produk['vol_produk_f'];
			$rak4_produk_f = $rak4_produk['price_f'];
			$rak4_vol_boulder = $rak4_produk['vol_boulder'];
			$rak4_boulder = $rak4_produk['boulder'];
			$rak4_stone_crusher = $rak4_produk['stone_crusher'];
			$rak4_wheel_loader = $rak4_produk['wheel_loader'];
			$rak4_maintenance = $rak4_produk['maintenance'];
			$rak4_bbm_solar = $rak4_produk['bbm_solar'];
			$rak4_tangki = $rak4_produk['tangki'];
			$rak4_timbangan = $rak4_produk['timbangan'];
			$rak4_overhead= $rak4_produk['overhead'];

			//BULAN 5
			$date_5_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_4_akhir)));
			$date_5_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_5_awal)));
			$rak5_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_5_awal' and '$date_5_akhir'")
			->get()->row_array();
			$rak5_vol_produk_a = $rak5_produk['vol_produk_a'];
			$rak5_produk_a = $rak5_produk['price_a'];
			$rak5_vol_produk_b = $rak5_produk['vol_produk_b'];
			$rak5_produk_b = $rak5_produk['price_b'];
			$rak5_vol_produk_c = $rak5_produk['vol_produk_c'];
			$rak5_produk_c = $rak5_produk['price_c'];
			$rak5_vol_produk_d = $rak5_produk['vol_produk_d'];
			$rak5_produk_d = $rak5_produk['price_d'];
			$rak5_vol_produk_e = $rak5_produk['vol_produk_e'];
			$rak5_produk_e = $rak5_produk['price_e'];
			$rak5_vol_produk_f = $rak5_produk['vol_produk_f'];
			$rak5_produk_f = $rak5_produk['price_f'];
			$rak5_vol_boulder = $rak5_produk['vol_boulder'];
			$rak5_boulder = $rak5_produk['boulder'];
			$rak5_stone_crusher = $rak5_produk['stone_crusher'];
			$rak5_wheel_loader = $rak5_produk['wheel_loader'];
			$rak5_maintenance = $rak5_produk['maintenance'];
			$rak5_bbm_solar = $rak5_produk['bbm_solar'];
			$rak5_tangki = $rak5_produk['tangki'];
			$rak5_timbangan = $rak5_produk['timbangan'];
			$rak5_overhead= $rak5_produk['overhead'];

			//BULAN 6
			$date_6_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_5_akhir)));
			$date_6_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_6_awal)));
			$rak6_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_6_awal' and '$date_6_akhir'")
			->get()->row_array();
			$rak6_vol_produk_a = $rak6_produk['vol_produk_a'];
			$rak6_produk_a = $rak6_produk['price_a'];
			$rak6_vol_produk_b = $rak6_produk['vol_produk_b'];
			$rak6_produk_b = $rak6_produk['price_b'];
			$rak6_vol_produk_c = $rak6_produk['vol_produk_c'];
			$rak6_produk_c = $rak6_produk['price_c'];
			$rak6_vol_produk_d = $rak6_produk['vol_produk_d'];
			$rak6_produk_d = $rak6_produk['price_d'];
			$rak6_vol_produk_e = $rak6_produk['vol_produk_e'];
			$rak6_produk_e = $rak6_produk['price_e'];
			$rak6_vol_produk_f = $rak6_produk['vol_produk_f'];
			$rak6_produk_f = $rak6_produk['price_f'];
			$rak6_vol_boulder = $rak6_produk['vol_boulder'];
			$rak6_boulder = $rak6_produk['boulder'];
			$rak6_stone_crusher = $rak6_produk['stone_crusher'];
			$rak6_wheel_loader = $rak6_produk['wheel_loader'];
			$rak6_maintenance = $rak6_produk['maintenance'];
			$rak6_bbm_solar = $rak6_produk['bbm_solar'];
			$rak6_tangki = $rak6_produk['tangki'];
			$rak6_timbangan = $rak6_produk['timbangan'];
			$rak6_overhead= $rak6_produk['overhead'];

			//BULAN 7
			$date_7_awal = date('Y-m-d', strtotime('+1 days', strtotime($date_6_akhir)));
			$date_7_akhir = date('Y-m-d', strtotime('-1 days +1 months', strtotime($date_7_awal)));
			$rak7_produk = $this->db->select('*, SUM(vol_produk_a) as vol_produk_a, SUM(vol_produk_b) as vol_produk_b, SUM(vol_produk_c) as vol_produk_c, SUM(vol_produk_d) as vol_produk_d, SUM(vol_produk_e) as vol_produk_e, SUM(vol_produk_f) as vol_produk_f')
			->from('rak')
			->where("tanggal_rencana_kerja between '$date_7_awal' and '$date_7_akhir'")
			->get()->row_array();
			$rak7_vol_produk_a = $rak7_produk['vol_produk_a'];
			$rak7_produk_a = $rak7_produk['price_a'];
			$rak7_vol_produk_b = $rak7_produk['vol_produk_b'];
			$rak7_produk_b = $rak7_produk['price_b'];
			$rak7_vol_produk_c = $rak7_produk['vol_produk_c'];
			$rak7_produk_c = $rak7_produk['price_c'];
			$rak7_vol_produk_d = $rak7_produk['vol_produk_d'];
			$rak7_produk_d = $rak7_produk['price_d'];
			$rak7_vol_produk_e = $rak7_produk['vol_produk_e'];
			$rak7_produk_e = $rak7_produk['price_e'];
			$rak7_vol_produk_f = $rak7_produk['vol_produk_f'];
			$rak7_produk_f = $rak7_produk['price_f'];
			$rak7_vol_boulder = $rak7_produk['vol_boulder'];
			$rak7_boulder = $rak7_produk['boulder'];
			$rak7_stone_crusher = $rak7_produk['stone_crusher'];
			$rak7_wheel_loader = $rak7_produk['wheel_loader'];
			$rak7_maintenance = $rak7_produk['maintenance'];
			$rak7_bbm_solar = $rak7_produk['bbm_solar'];
			$rak7_tangki = $rak7_produk['tangki'];
			$rak7_timbangan = $rak7_produk['timbangan'];
			$rak7_overhead= $rak7_produk['overhead'];

			//JUMLAH PENDAPATAN USAHA
			$jumlah_vol_produk_a = $akumulasi_vol_produk_a + $rak1_vol_produk_a + $rak2_vol_produk_a + $rak3_vol_produk_a + $rak4_vol_produk_a + $rak5_vol_produk_a + $rak6_vol_produk_a + $rak7_vol_produk_a;
			$jumlah_produk_a = $akumulasi_produk_a + $rak1_produk_a + $rak2_produk_a + $rak3_produk_a + $rak4_produk_a + $rak5_produk_a + $rak6_produk_a + $rak7_produk_a;

			$jumlah_vol_produk_b = $akumulasi_vol_produk_b + $rak1_vol_produk_b + $rak2_vol_produk_b + $rak3_vol_produk_b + $rak4_vol_produk_b + $rak5_vol_produk_b + $rak6_vol_produk_b + $rak7_vol_produk_b;
			$jumlah_produk_b = $akumulasi_produk_b + $rak1_produk_b + $rak2_produk_b + $rak3_produk_b + $rak4_produk_b + $rak5_produk_b + $rak6_produk_b + $rak7_produk_b;

			$jumlah_vol_produk_c = $akumulasi_vol_produk_c + $rak1_vol_produk_c + $rak2_vol_produk_c + $rak3_vol_produk_c + $rak4_vol_produk_c + $rak5_vol_produk_c + $rak6_vol_produk_c + $rak7_vol_produk_c;
			$jumlah_produk_c = $akumulasi_produk_c + $rak1_produk_c + $rak2_produk_c + $rak3_produk_c + $rak4_produk_c + $rak5_produk_c + $rak6_produk_c + $rak7_produk_c;

			$jumlah_vol_produk_d = $akumulasi_vol_produk_d + $rak1_vol_produk_d + $rak2_vol_produk_d + $rak3_vol_produk_d + $rak4_vol_produk_d + $rak5_vol_produk_d + $rak6_vol_produk_d + $rak7_vol_produk_d;
			$jumlah_produk_d = $akumulasi_produk_d + $rak1_produk_d + $rak2_produk_d + $rak3_produk_d + $rak4_produk_d + $rak5_produk_d + $rak6_produk_d + $rak7_produk_d;

			$jumlah_vol_produk_e = $akumulasi_vol_produk_e + $rak1_vol_produk_e + $rak2_vol_produk_e + $rak3_vol_produk_e + $rak4_vol_produk_e + $rak5_vol_produk_e + $rak6_vol_produk_e + $rak7_vol_produk_e;
			$jumlah_produk_e = $akumulasi_produk_e + $rak1_produk_e + $rak2_produk_e + $rak3_produk_e + $rak4_produk_e + $rak5_produk_e + $rak6_produk_e + $rak7_produk_e;

			$jumlah_vol_produk_f = $akumulasi_vol_produk_f + $rak1_vol_produk_f + $rak2_vol_produk_f + $rak3_vol_produk_f + $rak4_vol_produk_f + $rak5_vol_produk_f + $rak6_vol_produk_f + $rak7_vol_produk_f;
			$jumlah_produk_f = $akumulasi_produk_f + $rak1_produk_f + $rak2_produk_f + $rak3_produk_f + $rak4_produk_f + $rak5_produk_f + $rak6_produk_f + $rak7_produk_f;

			$jumlah_vol_akumulasi = $akumulasi_vol_produk_a + $akumulasi_vol_produk_b + $akumulasi_vol_produk_c + $akumulasi_vol_produk_d + $akumulasi_vol_produk_e + $akumulasi_vol_produk_f;
			$jumlah_akumulasi = $akumulasi_produk_a + $akumulasi_produk_b + $akumulasi_produk_c + $akumulasi_produk_d + $akumulasi_produk_e + $akumulasi_produk_f;

			$jumlah_vol_rak1 = $rak1_vol_produk_a + $rak1_vol_produk_b + $rak1_vol_produk_c + $rak1_vol_produk_d + $rak1_vol_produk_e + $rak1_vol_produk_f;
			$jumlah_rak1 = $rak1_produk_a + $rak1_produk_b + $rak1_produk_c + $rak1_produk_d + $rak1_produk_e + $rak1_produk_f;

			$jumlah_vol_rak2 = $rak2_vol_produk_a + $rak2_vol_produk_b + $rak2_vol_produk_c + $rak2_vol_produk_d + $rak2_vol_produk_e + $rak2_vol_produk_f;
			$jumlah_rak2 = $rak2_produk_a + $rak2_produk_b + $rak2_produk_c + $rak2_produk_d + $rak2_produk_e + $rak2_produk_f;

			$jumlah_vol_rak3 = $rak3_vol_produk_a + $rak3_vol_produk_b + $rak3_vol_produk_c + $rak3_vol_produk_d + $rak3_vol_produk_e + $rak3_vol_produk_f;
			$jumlah_rak3 = $rak3_produk_a + $rak3_produk_b + $rak3_produk_c + $rak3_produk_d + $rak3_produk_e + $rak3_produk_f;

			$jumlah_vol_rak4 = $rak4_vol_produk_a + $rak4_vol_produk_b + $rak4_vol_produk_c + $rak4_vol_produk_d + $rak4_vol_produk_e + $rak4_vol_produk_f;
			$jumlah_rak4 = $rak4_produk_a + $rak4_produk_b + $rak4_produk_c + $rak4_produk_d + $rak4_produk_e + $rak4_produk_f;

			$jumlah_vol_rak5 = $rak5_vol_produk_a + $rak5_vol_produk_b + $rak5_vol_produk_c + $rak5_vol_produk_d + $rak5_vol_produk_e + $rak5_vol_produk_f;
			$jumlah_rak5 = $rak5_produk_a + $rak5_produk_b + $rak5_produk_c + $rak5_produk_d + $rak5_produk_e + $rak5_produk_f;

			$jumlah_vol_rak6 = $rak6_vol_produk_a + $rak6_vol_produk_b + $rak6_vol_produk_c + $rak6_vol_produk_d + $rak6_vol_produk_e + $rak6_vol_produk_f;
			$jumlah_rak6 = $rak6_produk_a + $rak6_produk_b + $rak6_produk_c + $rak6_produk_d + $rak6_produk_e + $rak6_produk_f;

			$jumlah_vol_rak7 = $rak7_vol_produk_a + $rak7_vol_produk_b + $rak7_vol_produk_c + $rak7_vol_produk_d + $rak7_vol_produk_e + $rak7_vol_produk_f;
			$jumlah_rak7 = $rak7_produk_a + $rak7_produk_b + $rak7_produk_c + $rak7_produk_d + $rak7_produk_e + $rak7_produk_f;

			$total_jumlah_vol = $jumlah_vol_produk_a + $jumlah_vol_produk_b + $jumlah_vol_produk_c + $jumlah_vol_produk_d + $jumlah_vol_produk_e + $jumlah_vol_produk_f;
			$total_jumlah = $jumlah_produk_a + $jumlah_produk_b + $jumlah_produk_c + $jumlah_produk_d + $jumlah_produk_e + $jumlah_produk_f;

			//JUMLAH BAHAN
			$jumlah_vol_boulder = $akumulasi_vol_boulder + $rak1_vol_boulder + $rak2_vol_boulder + $rak3_vol_boulder + $rak4_vol_boulder + $rak5_vol_boulder + $rak6_vol_boulder + $rak7_vol_boulder;
			$jumlah_boulder = $akumulasi_boulder + $rak1_boulder + $rak2_boulder + $rak3_boulder + $rak4_boulder + $rak5_boulder + $rak6_boulder + $rak7_boulder;

			$jumlah_akumulasi_vol_bahan = $akumulasi_vol_boulder;
			$jumlah_akumulasi_bahan = $akumulasi_boulder;
			$jumlah_vol_bahan_rak1 = $rak1_vol_boulder;
			$jumlah_bahan_rak1 = $rak1_boulder;
			$jumlah_vol_bahan_rak2 = $rak2_vol_boulder;
			$jumlah_bahan_rak2 = $rak2_boulder;
			$jumlah_vol_bahan_rak3 = $rak3_vol_boulder;
			$jumlah_bahan_rak3 = $rak3_boulder;
			$jumlah_vol_bahan_rak4 = $rak4_vol_boulder;
			$jumlah_bahan_rak4 = $rak4_boulder;
			$jumlah_vol_bahan_rak5 = $rak5_vol_boulder;
			$jumlah_bahan_rak5 = $rak5_boulder;
			$jumlah_vol_bahan_rak6 = $rak6_vol_boulder;
			$jumlah_bahan_rak6 = $rak6_boulder;
			$jumlah_vol_bahan_rak7 = $rak7_vol_boulder;
			$jumlah_bahan_rak7 = $rak7_boulder;
			$total_jumlah_vol_bahan = $jumlah_vol_boulder;
			$total_jumlah_bahan = $jumlah_boulder;

			//JUMLAH ALAT
			$jumlah_stone_crusher = $akumulasi_stone_crusher + $rak1_stone_crusher + $rak2_stone_crusher + $rak3_stone_crusher + $rak4_stone_crusher + $rak5_stone_crusher + $rak6_stone_crusher + $rak7_stone_crusher;
			$jumlah_wheel_loader = $akumulasi_wheel_loader + $rak1_wheel_loader + $rak2_wheel_loader + $rak3_wheel_loader + $rak4_wheel_loader + $rak5_wheel_loader + $rak6_wheel_loader + $rak7_wheel_loader;
			$jumlah_maintenance = $akumulasi_maintenance + $rak1_maintenance + $rak2_maintenance + $rak3_maintenance + $rak4_maintenance + $rak5_maintenance + $rak6_maintenance + $rak7_maintenance;
			$jumlah_bbm_solar = $akumulasi_bbm_solar + $rak1_bbm_solar + $rak2_bbm_solar + $rak3_bbm_solar + $rak4_bbm_solar + $rak5_bbm_solar + $rak6_bbm_solar + $rak7_bbm_solar;
			$jumlah_tangki = $akumulasi_tangki + $rak1_tangki + $rak2_tangki + $rak3_tangki + $rak4_tangki + $rak5_tangki + $rak6_tangki + $rak7_tangki;
			$jumlah_timbangan = $akumulasi_timbangan + $rak1_timbangan + $rak2_timbangan + $rak3_timbangan + $rak4_timbangan + $rak5_timbangan + $rak6_timbangan + $rak7_timbangan;

			$jumlah_akumulasi_alat = $akumulasi_stone_crusher + $akumulasi_wheel_loader + $akumulasi_maintenance + $akumulasi_bbm_solar + $akumulasi_tangki + $akumulasi_timbangan;
			$jumlah_alat_rak1 = $rak1_stone_crusher + $rak1_wheel_loader + $rak1_maintenance + $rak1_bbm_solar + $rak2_tangki + $rak1_timbangan;
			$jumlah_alat_rak2 = $rak2_stone_crusher + $rak2_wheel_loader + $rak2_maintenance + $rak2_bbm_solar + $rak2_tangki + $rak2_timbangan;
			$jumlah_alat_rak3 = $rak3_stone_crusher + $rak3_wheel_loader + $rak3_maintenance + $rak3_bbm_solar + $rak2_tangki + $rak3_timbangan;
			$jumlah_alat_rak4 = $rak4_stone_crusher + $rak4_wheel_loader + $rak4_maintenance + $rak4_bbm_solar + $rak2_tangki + $rak4_timbangan;
			$jumlah_alat_rak5 = $rak5_stone_crusher + $rak5_wheel_loader + $rak5_maintenance + $rak5_bbm_solar + $rak2_tangki + $rak5_timbangan;
			$jumlah_alat_rak6 = $rak6_stone_crusher + $rak6_wheel_loader + $rak6_maintenance + $rak6_bbm_solar + $rak2_tangki + $rak6_timbangan;
			$jumlah_alat_rak7 = $rak7_stone_crusher + $rak7_wheel_loader + $rak7_maintenance + $rak7_bbm_solar + $rak2_tangki + $rak7_timbangan;
			$total_jumlah_alat = $jumlah_stone_crusher + $jumlah_wheel_loader + $jumlah_maintenance + $jumlah_bbm_solar + $jumlah_tangki + $jumlah_timbangan;

			//JUMLAH OVERHEAD
			$jumlah_overhead = $akumulasi_overhead + $rak1_overhead + $rak2_overhead + $rak3_overhead + $rak4_overhead + $rak5_overhead + $rak6_overhead + $rak7_overhead;
			
			$jumlah_akumulasi_overhead = $akumulasi_overhead;
			$jumlah_overhead_rak1 = $rak1_overhead;
			$jumlah_overhead_rak2 = $rak2_overhead;
			$jumlah_overhead_rak3 = $rak3_overhead;
			$jumlah_overhead_rak4 = $rak4_overhead;
			$jumlah_overhead_rak5 = $rak5_overhead;
			$jumlah_overhead_rak6 = $rak6_overhead;
			$jumlah_overhead_rak7 = $rak7_overhead;
			$total_jumlah_overhead = $jumlah_overhead;

			$sub_vol_akumulasi = $jumlah_akumulasi_vol_bahan;
			$sub_akumulasi = $jumlah_akumulasi_bahan + $jumlah_akumulasi_alat + $jumlah_akumulasi_overhead;
			$sub_vol_rak1 = $jumlah_vol_bahan_rak1;
			$sub_rak1 = $jumlah_bahan_rak1 + $jumlah_alat_rak1 + $jumlah_overhead_rak1;
			$sub_vol_rak2 = $jumlah_vol_bahan_rak2;
			$sub_rak2 = $jumlah_bahan_rak2 + $jumlah_alat_rak2 + $jumlah_overhead_rak2;
			$sub_vol_rak3 = $jumlah_vol_bahan_rak3;
			$sub_rak3 = $jumlah_bahan_rak3 + $jumlah_alat_rak3 + $jumlah_overhead_rak3;
			$sub_vol_rak4 = $jumlah_vol_bahan_rak4;
			$sub_rak4 = $jumlah_bahan_rak4 + $jumlah_alat_rak4 + $jumlah_overhead_rak4;
			$sub_vol_rak5 = $jumlah_vol_bahan_rak5;
			$sub_rak5 = $jumlah_bahan_rak5 + $jumlah_alat_rak5 + $jumlah_overhead_rak5;
			$sub_vol_rak6 = $jumlah_vol_bahan_rak6;
			$sub_rak6 = $jumlah_bahan_rak6 + $jumlah_alat_rak6 + $jumlah_overhead_rak6;
			$sub_vol_rak7 = $jumlah_vol_bahan_rak7;
			$sub_rak7 = $jumlah_bahan_rak7 + $jumlah_alat_rak7 + $jumlah_overhead_rak7;
			$total_vol_sub = $total_jumlah_vol_bahan;
			$total_sub = $total_jumlah_bahan + $total_jumlah_alat + $total_jumlah_overhead;

			$laba_akumulasi = $jumlah_akumulasi - $sub_akumulasi;
			$laba_rak1 = $jumlah_rak1 - $sub_rak1;
			$laba_rak2 = $jumlah_rak2 - $sub_rak2;
			$laba_rak3 = $jumlah_rak3 - $sub_rak3;
			$laba_rak4 = $jumlah_rak4 - $sub_rak4;
			$laba_rak5 = $jumlah_rak5 - $sub_rak5;
			$laba_rak6 = $jumlah_rak6 - $sub_rak6;
			$laba_rak7 = $jumlah_rak7 - $sub_rak7;
			$total_laba = $total_jumlah - $total_sub;
			?>

			<tr class="table-judul">
				<th width="3%" class="text-center" rowspan="2" style="vertical-align:middle;">NO.</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle;">URAIAN</th>
				<th class="text-center" colspan="2" style="text-transform:uppercase;">SD. <?php echo $date_2_awal = date('F Y', strtotime('-1 days', strtotime($date_1_awal)));?></th>
				<th class="text-center" colspan="2" style="text-transform:uppercase;"><?php echo $date_1_awal = date('F Y');?></th>
				<th class="text-center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_1_akhir)));?></th>
				<th class="text-center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_2_akhir)));?></th>
				<th class="text-center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_3_akhir)));?></th>
				<th class="text-center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_4_akhir)));?></th>
				<th class="text-center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_5_akhir)));?></th>
				<th class="text-center" colspan="2" style="text-transform:uppercase;"><?php echo $date_2_awal = date('F Y', strtotime('+1 days', strtotime($date_6_akhir)));?></th>
				<th class="text-center" colspan="2">JUMLAH</th>
	        </tr>
			<tr class="table-judul">
			<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
				<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
				<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
				<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
				<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
				<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
				<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
				<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
				<th class="text-center" ><b>VOL.</b></th>
				<th class="text-center" ><b>NILAI</b></th>
			</tr>
			<tr class="table-baris">
				<th class="text-left" colspan="20"><b>A. PENDAPATAN USAHA</b></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">1.</th>
				<th class="text-left">LPA (JLS Brantas) - Batu Split 0,5 - 10 mm (Upah Giling)</th>
				<th class="text-right"><?php echo number_format($akumulasi_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_produk_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_produk_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_produk_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_produk_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_produk_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_produk_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_produk_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_produk_a,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_produk_a,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_produk_a,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">2.</th>
				<th class="text-left">Bu Tampi - Batu Split 0,5 - 10 mm</th>
				<th class="text-right"><?php echo number_format($akumulasi_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_produk_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_produk_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_produk_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_produk_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_produk_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_produk_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_produk_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_produk_b,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_produk_b,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_produk_b,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center" rowspan="4" style="vertical-align:middle;">3.</th>
				<th class="text-left">P. Antoni - Abu Batu</th>
				<th class="text-right"><?php echo number_format($akumulasi_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_produk_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_produk_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_produk_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_produk_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_produk_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_produk_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_produk_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_produk_c,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_produk_c,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_produk_c,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-left">P. Antoni - Batu Split 0,5 - 10 mmu</th>
				<th class="text-right"><?php echo number_format($akumulasi_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_produk_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_produk_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_produk_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_produk_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_produk_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_produk_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_produk_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_produk_d,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_produk_d,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_produk_d,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-left">P. Antoni - Batu Split 10 - 15 mm</th>
				<th class="text-right"><?php echo number_format($akumulasi_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_produk_e,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_produk_e,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_produk_e,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_produk_e,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_produk_e,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_produk_e,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_produk_e,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_produk_e,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_produk_e,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_produk_e,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-left">P. Antoni - Batu Split 10 - 20 mm</th>
				<th class="text-right"><?php echo number_format($akumulasi_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_produk_f,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_produk_f,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_produk_f,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_produk_f,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_produk_f,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_produk_f,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_produk_f,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_produk_f,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_produk_f,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_produk_f,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th class="text-right" colspan="2">JUMLAH</th>
				<th class="text-right"><?php echo number_format($jumlah_vol_akumulasi,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_rak1,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_rak1,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_rak2,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_rak2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_rak3,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_rak3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_rak4,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_rak4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_rak5,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_rak5,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_rak6,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_rak6,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_rak7,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_rak7,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_jumlah_vol,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_jumlah,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-left" colspan="20"><b>B. BIAYA BAHAN</b></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">1.</th>
				<th class="text-left">Boulder</th>
				<th class="text-right"><?php echo number_format($akumulasi_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($akumulasi_boulder,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak1_boulder,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak2_boulder,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak3_boulder,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak4_boulder,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak5_boulder,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak6_boulder,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($rak7_boulder,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_boulder,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_boulder,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th class="text-right" colspan="2">JUMLAH</th>
				<th class="text-right"><?php echo number_format($jumlah_akumulasi_vol_bahan,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_akumulasi_bahan,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_bahan_rak1,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan_rak1,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_bahan_rak1,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan_rak2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_bahan_rak3,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan_rak3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_bahan_rak4,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan_rak4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_bahan_rak5,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan_rak5,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_bahan_rak6,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan_rak6,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_vol_bahan_rak7,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($jumlah_bahan_rak7,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_jumlah_vol_bahan,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_jumlah_bahan,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-left" colspan="20"><b>C. BIAYA PERALATAN</b></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">1.</th>
				<th class="text-left">Stone Crusher</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($akumulasi_stone_crusher,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak1_stone_crusher,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak2_stone_crusher,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak3_stone_crusher,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak4_stone_crusher,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak5_stone_crusher,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak6_stone_crusher,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak7_stone_crusher,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_stone_crusher,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">2.</th>
				<th class="text-left">Wheel Loader</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($akumulasi_wheel_loader,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak1_wheel_loader,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak2_wheel_loader,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak3_wheel_loader,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak4_wheel_loader,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak5_wheel_loader,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak6_wheel_loader,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak7_wheel_loader,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_wheel_loader,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">3.</th>
				<th class="text-left">Maintenance</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($akumulasi_maintenance,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak1_maintenance,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak2_maintenance,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak3_maintenance,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak4_maintenance,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak5_maintenance,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak6_maintenance,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak7_maintenance,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_maintenance,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">4.</th>
				<th class="text-left">BBM Solar</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($akumulasi_bbm_solar,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak1_bbm_solar,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak2_bbm_solar,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak3_bbm_solar,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak4_bbm_solar,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak5_bbm_solar,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak6_bbm_solar,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak7_bbm_solar,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_bbm_solar,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">5.</th>
				<th class="text-left">Tangkir Solar</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($akumulasi_tangki,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak1_tangki,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak2_tangki,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak3_tangki,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak4_tangki,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak5_tangki,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak6_tangki,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak7_tangki,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_tangki,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">6.</th>
				<th class="text-left">Timbangan</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($akumulasi_timbangan,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak1_timbangan,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak2_timbangan,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak3_timbangan,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak4_timbangan,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak5_timbangan,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak6_timbangan,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak7_timbangan,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_timbangan,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th class="text-right" colspan="2">JUMLAH</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_akumulasi_alat,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_alat_rak1,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_alat_rak2,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_alat_rak3,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_alat_rak4,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_alat_rak5,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_alat_rak6,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_alat_rak7,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_jumlah_alat,0,',','.');?></th>
			</tr>
			<tr class="table-baris">
				<th class="text-left" colspan="20"><b>D. BIAYA OVERHEAD</b></th>
			</tr>
			<tr class="table-baris">
				<th class="text-center">1.</th>
				<th class="text-left">Overhead</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($akumulasi_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak1_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak2_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak3_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak4_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak5_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak6_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($rak7_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-total">
				<th class="text-right" colspan="2">JUMLAH</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_akumulasi_overhead,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_overhead_rak1,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_overhead_rak2,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_overhead_rak3,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_overhead_rak4,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_overhead_rak5,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_overhead_rak6,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($jumlah_overhead_rak7,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_jumlah_overhead,0,',','.');?></th>
			</tr>
			<tr class="table-subtotal">
				<th class="text-right" colspan="2">TOTAL (BAHAN+ALAT+OVERHEAD)</th>
				<th class="text-right"><?php echo number_format($sub_vol_akumulasi,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_akumulasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_vol_rak1,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_rak1,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_vol_rak2,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_rak2,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_vol_rak3,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_rak3,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_vol_rak4,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_rak4,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_vol_rak5,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_rak5,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_vol_rak6,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_rak6,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_vol_rak7,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($sub_rak7,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_vol_sub,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_sub,0,',','.');?></th>
			</tr>
			<tr class="table-total2">
				<th class="text-right" colspan="2">LABA</th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($laba_akumulasi,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($laba_rak1,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($laba_rak2,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($laba_rak3,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($laba_rak4,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($laba_rak5,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($laba_rak6,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($laba_rak7,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"><?php echo number_format($total_laba,0,',','.');?></th>
			</tr>
		</table>
		<?php
	}

	public function detail_notification()
    {
        $check = $this->m_admin->check_login();
        if($check == true){

            $this->db->select('ppo.*');
			$this->db->where("ppo.status = 'WAITING'");
			$this->db->group_by('ppo.id');
			$this->db->order_by('ppo.created_on','desc');
            $query = $this->db->get('pmm_purchase_order ppo');
            $data['row'] = $query->result_array();
            $this->load->view('admin/detail_notification',$data);
            
        }else {
            redirect('admin');
        }
    }

	public function detail_notification_2()
    {
        $check = $this->m_admin->check_login();
        if($check == true){
			
            $this->load->view('admin/detail_notification_2',$data);
            
        }else {
            redirect('admin');
        }
    }

	public function detail_notification_3()
    {
        $check = $this->m_admin->check_login();
        if($check == true){
			
            $this->load->view('admin/detail_notification_3',$data);
            
        }else {
            redirect('admin');
        }
    }

	public function buku_besar($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';
		$date3 = '';
		$date4 = '';

		if(count($arr_filter_date) == 2){
			$date_now = date('Y-m-d',strtotime($arr_filter_date[0]));
			$date_now = date('Y-m-d', strtotime('-1 days -0 months ', strtotime($date_now)));
			$date4 	= date('Y-m-d',strtotime($date_now));
			$date_now2 = date('2023-01-01');
			$date3 	= date('Y-m-d',strtotime($date_now2));
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));
			$filter_date_2 = date('Y-m-d',strtotime($date3)).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
		 <style type="text/css">
			body {
				font-family: helvetica;
				font-size: 11px;
			}

			table tr.table-active{
				background: linear-gradient(90deg, #fdcd3b 20%, #fdcd3b 40%, #e69500 80%);
				font-size: 11px;
				font-weight: bold;
			}
				
			table tr.table-active2{
				background-color: #e69500;
				font-size: 11px;
				font-weight: bold;
				color: white;
			}
				
			table tr.table-active3{
				font-size: 11px;
			}
				
			table tr.table-active4{
				background: linear-gradient(90deg, #eeeeee 5%, #cccccc 50%, #cccccc 100%);
				font-weight: bold;
				font-size: 11px;
				color: black;
			}

			.spoiler{
			display:none
			}

			.show{
			display:block
			}

			th{
			padding: 5px;
			}
		 </style>
		 <script>
			function myFunction() {
			var x = document.getElementById("myDIV");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction2() {
			var x = document.getElementById("myDIV2");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction3() {
			var x = document.getElementById("myDIV3");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction4() {
			var x = document.getElementById("myDIV4");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction5() {
			var x = document.getElementById("myDIV5");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction6() {
			var x = document.getElementById("myDIV6");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction7() {
			var x = document.getElementById("myDIV7");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction8() {
			var x = document.getElementById("myDIV8");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction9() {
			var x = document.getElementById("myDIV9");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction10() {
			var x = document.getElementById("myDIV10");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction11() {
			var x = document.getElementById("myDIV11");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction12() {
			var x = document.getElementById("myDIV12");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction13() {
			var x = document.getElementById("myDIV13");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction14() {
			var x = document.getElementById("myDIV14");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction15() {
			var x = document.getElementById("myDIV15");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction16() {
			var x = document.getElementById("myDIV16");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction17() {
			var x = document.getElementById("myDIV17");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction18() {
			var x = document.getElementById("myDIV18");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction19() {
			var x = document.getElementById("myDIV19");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction20() {
			var x = document.getElementById("myDIV20");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction21() {
			var x = document.getElementById("myDIV21");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction22() {
			var x = document.getElementById("myDIV22");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction23() {
			var x = document.getElementById("myDIV23");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction24() {
			var x = document.getElementById("myDIV24");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction25() {
			var x = document.getElementById("myDIV25");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction26() {
			var x = document.getElementById("myDIV26");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction27() {
			var x = document.getElementById("myDIV27");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction28() {
			var x = document.getElementById("myDIV28");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction29() {
			var x = document.getElementById("myDIV29");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction30() {
			var x = document.getElementById("myDIV30");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction31() {
			var x = document.getElementById("myDIV31");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction32() {
			var x = document.getElementById("myDIV32");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction33() {
			var x = document.getElementById("myDIV33");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction34() {
			var x = document.getElementById("myDIV34");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction35() {
			var x = document.getElementById("myDIV35");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction36() {
			var x = document.getElementById("myDIV36");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction37() {
			var x = document.getElementById("myDIV37");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction38() {
			var x = document.getElementById("myDIV38");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction39() {
			var x = document.getElementById("myDIV39");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction40() {
			var x = document.getElementById("myDIV40");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction41() {
			var x = document.getElementById("myDIV41");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction42() {
			var x = document.getElementById("myDIV42");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction43() {
			var x = document.getElementById("myDIV43");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction44() {
			var x = document.getElementById("myDIV44");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction45() {
			var x = document.getElementById("myDIV45");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction46() {
			var x = document.getElementById("myDIV46");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction47() {
			var x = document.getElementById("myDIV47");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction48() {
			var x = document.getElementById("myDIV48");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction49() {
			var x = document.getElementById("myDIV49");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction50() {
			var x = document.getElementById("myDIV50");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction51() {
			var x = document.getElementById("myDIV51");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction52() {
			var x = document.getElementById("myDIV52");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction53() {
			var x = document.getElementById("myDIV53");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction54() {
			var x = document.getElementById("myDIV54");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction55() {
			var x = document.getElementById("myDIV55");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction56() {
			var x = document.getElementById("myDIV56");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}

			function myFunction100() {
			var x = document.getElementById("myDIV100");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
			}
		 </script>
	        <tr class="table-active2">
	            <th colspan="3">PERIODE</th>
				<th class="text-center" colspan="4"><?php echo $filter_date = $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
	        </tr>

			<tr class="table-active">
	            <th width="100%" class="text-left" colspan="7">BUKU BESAR</th>
	        </tr>
			<tr class="table-active3">
				<th class="text-left" colspan="7">
					<table width="100% "border="1">
						<tr style="background-color: #cccccc;">
							<th class="text-left" width="10%">NAMA AKUN / TGL</th>
							<th class="text-left" width="10%">TRANSAKSI</th>
							<th class="text-left" width="20%">NO.</th>
							<th class="text-left" width="30%">DESKRIPSI</th>
							<th class="text-right" width="10%">DEBIT</th>
							<th class="text-right" width="10%">KREDIT</th>
							<th class="text-right" width="10%">SALDO</th>
						</tr>
					</table>
					<br />
					<div>
						<?php
						$akun_110008_biaya_lalu = $this->db->select('sum(pdb.jumlah) as kredit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("b.bayar_dari = 1")
						->get()->row_array();

						$akun_110008_jurnal_lalu = $this->db->select('sum(pdj.debit), sum(pdj.kredit)')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 1")
						->get()->row_array();

						$terima_uang_lalu = $this->db->select('sum(jumlah) as debit')
						->from('pmm_terima_uang')
						->where("tanggal_transaksi between '$date3' and '$date4'")
						->where("setor_ke = 1")
						->get()->row_array();
						$akun_110008_lalu = ($terima_uang_lalu['debit'] + $akun_110008_jurnal_lalu['debit']) - ($akun_110008_biaya_lalu['kredit'] + $akun_110008_jurnal_lalu['kredit']);

						$akun_110008_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as kredit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("b.bayar_dari = 1")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_110008_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 1")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$terima_uang = $this->db->select('*, memo as deskripsi, jumlah as debit')
						->from('pmm_terima_uang')
						->where("tanggal_transaksi between '$date1' and '$date2'")
						->where("setor_ke = 1")
						->group_by('id')
						->order_by('tanggal_transaksi','asc')
						->order_by('created_on','asc')
						->get()->result_array();

						$akun_110008 = array_merge($akun_110008_biaya,$akun_110008_jurnal,$terima_uang);

						function sortByOrder($akun_110008_biaya, $akun_110008_jurnal) {
							if ($akun_110008_biaya['tanggal_transaksi'] > $akun_110008_jurnal['tanggal_transaksi']) {
								return 1;
							} elseif ($akun_110008_biaya['tanggal_transaksi'] < $akun_110008_jurnal['tanggal_transaksi']) {
								return -1;
							}
							return 0;
						}
						
						usort($akun_110008, 'sortByOrder');
						
						?>
						<button onclick="myFunction()" class="btn btn-info"><b>(1-10008) Kas<b></button>
						<div id="myDIV" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110008_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110008_lalu < 0 ? "(".number_format(-$akun_110008_lalu,0,',','.').")" : number_format($akun_110008_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110008_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110008 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
								<?php endforeach; ?>
							</table>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10008) Kas | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110008_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110008 = ($akun_110008_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110008 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110008 < 0 ? "(".number_format(-$saldo_110008,0,',','.').")" : number_format($saldo_110008,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_110100_lalu = $this->db->select('ppp.total as debit,pm.total as kredit')
						->from('pmm_penagihan_penjualan ppp')
						->join('pmm_pembayaran pm', 'ppp.id = pm.penagihan_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.status_pembayaran = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_110100_lalu = $akun_110100_lalu['debit'] - $akun_110100_lalu['kredit'];

						$akun_110100 = $this->db->select('ppp.tanggal_invoice as tanggal_transaksi,ppp.nomor_invoice as nomor_transaksi,ppp.nomor_kontrak as deskripsi,ppp.total as debit,pm.total as kredit')
						->from('pmm_penagihan_penjualan ppp')
						->join('pmm_pembayaran pm', 'ppp.id = pm.penagihan_id','left')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->where("ppp.status_pembayaran = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->get()->result_array();
						?>
						<button onclick="myFunction2()" class="btn btn-info"><b>(1-10100) Piutang Usaha<b></button>
						<div id="myDIV2" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110100_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110100_lalu < 0 ? "(".number_format(-$akun_110100_lalu,0,',','.').")" : number_format($akun_110100_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110100_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110100 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">SALES INVOICE</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">SALES ORDER : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10100) Piutang Usaha | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110100_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110100 = ($akun_110100_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110100 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110100 < 0 ? "(".number_format(-$saldo_110100,0,',','.').")" : number_format($saldo_110100,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						/*$akun_110101_uncreated_lalu =$this->db->select('sum(pp.display_price) as total')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date3' and '$date4'")
						->where("pp.status = 'PUBLISH'")
						->where("pp.status_payment = 'UNCREATED'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->get()->row_array();

						$akun_110101_created_lalu =$this->db->select('sum(pp.display_price) as total')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date3' and '$date4'")
						->where("pp.status = 'PUBLISH'")
						->where("pp.status_payment in ('CREATED','CREATING','PAID')")
						->where("ppo.status in ('OPEN','CLOSED')")
						->get()->row_array();
						$akun_110101_lalu = $akun_110101_uncreated_lalu['total'] - $akun_110101_created_lalu['total'];

						$akun_110101_uncreated = $this->db->select('pp.date_production as tanggal_transaksi,ppo.contract_number as deskripsi,pp.display_price as debit')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date1' and '$date2'")
						->where("pp.status = 'PUBLISH'")
						->where("pp.status_payment = 'UNCREATED'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->group_by('pp.id')
						->get()->result_array();

						$akun_110101_created = $this->db->select('pp.date_production as tanggal_transaksi,ppo.contract_number as deskripsi,pp.display_price as kredit')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date1' and '$date2'")
						->where("pp.status = 'PUBLISH'")
						->where("pp.status_payment in ('CREATED','CREATING','PAID')")
						->where("ppo.status in ('OPEN','CLOSED')")
						->group_by('pp.id')
						->get()->result_array();

						$akun_110101 = array_merge($akun_110101_uncreated,$akun_110101_created);
						usort($akun_110101, 'sortByOrder');*/

						$akun_110101_uncreated_lalu =$this->db->select('sum(pp.display_price) as total')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date3' and '$date4'")
						->where("pp.status = 'PUBLISH'")
						->where("pp.status_payment = 'UNCREATED'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->get()->row_array();
						$akun_110101_lalu = $akun_110101_uncreated_lalu['total'];

						$akun_110101 = $this->db->select('pp.no_production, pp.date_production as tanggal_transaksi,ppo.contract_number as deskripsi,pp.display_price as debit')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date1' and '$date2'")
						->where("pp.status = 'PUBLISH'")
						->where("pp.status_payment = 'UNCREATED'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->group_by('pp.id')
						->get()->result_array();
						?>
						<button onclick="myFunction3()" class="btn btn-info"><b>(1-10101) Piutang Belum Ditagih<b></button>
						<div id="myDIV3" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110101_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110101_lalu < 0 ? "(".number_format(-$akun_110101_lalu,0,',','.').")" : number_format($akun_110101_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110101_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110101 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PENGIRIMAN</th>
									<th width="20%" class="text-left">NO. SURAT JALAN : <?php echo $x['no_production'];?></th>
									<th width="30%" class="text-left">SALES ORDER : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10101) Piutang Belum Ditagih | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110101_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110101 = ($akun_110101_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110101 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110101 < 0 ? "(".number_format(-$saldo_110101,0,',','.').")" : number_format($saldo_110101,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_110124_lalu = $this->db->select('ppp.total as debit,pm.total as kredit')
						->from('pmm_penagihan_penjualan ppp')
						->join('pmm_pembayaran pm', 'ppp.id = pm.penagihan_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.client_id = '22'")
						->where("ppp.status_pembayaran = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_110124_lalu = $akun_110124_lalu['debit'] - $akun_110124_lalu['kredit'];

						$akun_110124 = $this->db->select('ppp.tanggal_invoice as tanggal_transaksi,ppp.nomor_invoice as nomor_transaksi,ppp.nomor_kontrak as deskripsi,ppp.total as debit,pm.total as kredit')
						->from('pmm_penagihan_penjualan ppp')
						->join('pmm_pembayaran pm', 'ppp.id = pm.penagihan_id','left')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->where("ppp.client_id = '22'")
						->where("ppp.status_pembayaran = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->get()->result_array();
						?>
						<button onclick="myFunction4()" class="btn btn-info"><b>(1-10124) Piutang Wika - Rudy Jaya KSO<b></button>
						<div id="myDIV4" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110124_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110124_lalu < 0 ? "(".number_format(-$akun_110124_lalu,0,',','.').")" : number_format($akun_110124_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110124_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110124 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">SALES INVOICE</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">SALES ORDER : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10124) Piutang Wika - Rudy Jaya KSO | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110124_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110124 = ($akun_110124_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110124 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110124 < 0 ? "(".number_format(-$saldo_110124,0,',','.').")" : number_format($saldo_110124,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_110126_lalu = $this->db->select('ppp.total as debit,pm.total as kredit')
						->from('pmm_penagihan_penjualan ppp')
						->join('pmm_pembayaran pm', 'ppp.id = pm.penagihan_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.client_id = '2'")
						->where("ppp.status_pembayaran = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_110126_lalu = $akun_110126_lalu['debit'] - $akun_110126_lalu['kredit'];

						$akun_110126 = $this->db->select('ppp.tanggal_invoice as tanggal_transaksi,ppp.nomor_invoice as nomor_transaksi,ppp.nomor_kontrak as deskripsi,ppp.total as debit,pm.total as kredit')
						->from('pmm_penagihan_penjualan ppp')
						->join('pmm_pembayaran pm', 'ppp.id = pm.penagihan_id','left')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->where("ppp.client_id = '2'")
						->where("ppp.status_pembayaran = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->get()->result_array();
						?>
						<button onclick="myFunction5()" class="btn btn-info"><b>(1-10126) Piutang Nindya - TPP KSO<b></button>
						<div id="myDIV5" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110126_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110126_lalu < 0 ? "(".number_format(-$akun_110126_lalu,0,',','.').")" : number_format($akun_110126_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110126_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110126 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">SALES INVOICE</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">SALES ORDER : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10126) Piutang Nindya - TPP KSO | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110126_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110126 = ($akun_110126_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110126 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110126 < 0 ? "(".number_format(-$saldo_110126,0,',','.').")" : number_format($saldo_110126,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
					<?php
						$pembelian_semen = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_bahan = 1")
						->get()->row_array();

						$pembelian_pasir = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_bahan = 2")
						->get()->row_array();

						$pembelian_1020 = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_bahan = 3")
						->get()->row_array();

						$pembelian_2030 = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_bahan = 4")
						->get()->row_array();

						$pembelian_additive = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_bahan = 6")
						->get()->row_array();
						$akun_110201_pembelian = $pembelian_semen['nilai'] + $pembelian_pasir['nilai'] + $pembelian_1020['nilai'] + $pembelian_2030['nilai'] + $pembelian_additive['nilai'];

						$stock_opname = $this->db->select('date')
						->from('pmm_remaining_materials_cat')
						->where("date between '$date1' and '$date2'")
						->where("material_id = 1")
						->order_by('id','desc')->limit(1)
						->get()->row_array();
						
						$stock_opname_semen = $this->db->select('cat.*, (cat.total) as nilai')
						->from('pmm_remaining_materials_cat cat')
						->where("cat.date between '$date1' and '$date2'")
						->where("cat.material_id = 1")
						->where("cat.status = 'PUBLISH'")
						->group_by('cat.id')
						->order_by('cat.id','desc')->limit(1)
						->get()->result_array();
						$nilai_semen = 0;
						foreach ($stock_opname_semen as $x){
							$nilai_semen += $x['nilai'];
						}
				
						$stock_opname_pasir = $this->db->select('cat.*, (cat.total) as nilai')
						->from('pmm_remaining_materials_cat cat')
						->where("cat.date between '$date1' and '$date2'")
						->where("cat.material_id = 2")
						->where("cat.status = 'PUBLISH'")
						->group_by('cat.id')
						->order_by('cat.id','desc')->limit(1)
						->get()->result_array();
						$nilai_pasir = 0;
						foreach ($stock_opname_pasir as $x){
							$nilai_pasir += $x['nilai'];
						}
				
						$stock_opname_batu1020 = $this->db->select('cat.*, (cat.total) as nilai')
						->from('pmm_remaining_materials_cat cat')
						->where("cat.date between '$date1' and '$date2'")
						->where("cat.material_id = 3")
						->where("cat.status = 'PUBLISH'")
						->group_by('cat.id')
						->order_by('cat.id','desc')->limit(1)
						->get()->result_array();
						$nilai_batu1020 = 0;
						foreach ($stock_opname_batu1020 as $x){
							$nilai_batu1020 += $x['nilai'];
						}
				
						$stock_opname_batu2030 = $this->db->select('cat.*, (cat.total) as nilai')
						->from('pmm_remaining_materials_cat cat')
						->where("cat.date between '$date1' and '$date2'")
						->where("cat.material_id = 4")
						->where("cat.status = 'PUBLISH'")
						->group_by('cat.id')
						->order_by('cat.id','desc')->limit(1)
						->get()->result_array();
						$nilai_batu2030 = 0;
						foreach ($stock_opname_batu2030 as $x){
							$nilai_batu2030 += $x['nilai'];
						}
				
						$stock_opname_solar = $this->db->select('cat.*, (cat.total) as nilai')
						->from('pmm_remaining_materials_cat cat')
						->where("cat.date between '$date1' and '$date2'")
						->where("cat.material_id = 5")
						->where("cat.status = 'PUBLISH'")
						->group_by('cat.id')
						->order_by('cat.id','desc')->limit(1)
						->get()->result_array();
						$nilai_solar = 0;
						foreach ($stock_opname_solar as $x){
							$nilai_solar += $x['nilai'];
						}
				
						$stock_opname_additive = $this->db->select('cat.*, (cat.total) as nilai')
						->from('pmm_remaining_materials_cat cat')
						->where("cat.date between '$date1' and '$date2'")
						->where("cat.material_id = 19")
						->where("cat.status = 'PUBLISH'")
						->group_by('cat.id')
						->order_by('cat.id','desc')->limit(1)
						->get()->result_array();
						$nilai_additive = 0;
						foreach ($stock_opname_additive as $x){
							$nilai_additive += $x['nilai'];
						}
						
						$akun_110201_stock_opname = $nilai_semen + $nilai_pasir + $nilai_batu1020 + $nilai_batu2030 + $nilai_solar + $nilai_additive;
						
						$akun_110201_pemakaian = $akun_110201_pembelian - $akun_110201_stock_opname;
						?>
						<button onclick="myFunction6()" class="btn btn-info"><b>(1-10201) Persediaan Bahan Baku<b></button>
						<div id="myDIV6" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110201_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110201_lalu < 0 ? "(".number_format(-$akun_110201_lalu,0,',','.').")" : number_format($akun_110201_lalu,0,',','.');?></th>
								</tr>
							</table>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $stock_opname['date'];?></th>
									<th width="10%" class="text-left">STOCK OPNAME</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left">STOCK OPNAME TERAKHIR (<?php echo $stock_opname['date'];?>)</th>
									<th width="10%" class="text-right"><?php echo number_format($akun_110201_pembelian,0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($akun_110201_pemakaian,0,',','.');?></th>
									<?php
									$saldo = $akun_110201_stock_opname;
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10201) Persediaan Bahan Baku | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110201_pembelian,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110201_pemakaian,0,',','.');?></th>
									<?php
									$saldo_110201 = $saldo;
									$styleColor = $saldo_110201 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110201 < 0 ? "(".number_format(-$saldo_110201,0,',','.').")" : number_format($saldo_110201,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_110202_lalu = $this->db->select('sum(pp.display_price) as total')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date3' and '$date4'")
						->where("pp.status = 'PUBLISH'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->get()->row_array();
						$akun_110202_lalu = $akun_110202_lalu['total'];

						$akun_110202 = $this->db->select('pp.*,pp.date_production as tanggal_transaksi,pp.no_production as nomor_transaksi,ppo.contract_number as deskripsi,pp.display_price as debit,pp.display_price as kredit')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date1' and '$date2'")
						->where("pp.status = 'PUBLISH'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->group_by('pp.id')
						->get()->result_array();
						?>
						<button onclick="myFunction7()" class="btn btn-info"><b>(1-10202) Persediaan Barang Jadi<b></button>
						<div id="myDIV7" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110202_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110202_lalu < 0 ? "(".number_format(-$akun_110202_lalu,0,',','.').")" : number_format($akun_110202_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110202_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110202 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PENGIRIMAN</th>
									<th width="20%" class="text-left">NO. SURAT JALAN : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">SALES ORDER : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10202) Persediaan Barang Jadi | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110202_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110202 = ($akun_110202_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110202 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110202 < 0 ? "(".number_format(-$saldo_110202,0,',','.').")" : number_format($saldo_110202,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<!--<div>
						<?php
						$akun_110403_lalu =$this->db->select('sum(ppp.uang_muka) as debit')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_penagihan_pembelian_detail ppd','ppp.id = ppd.penagihan_pembelian_id','left')
						->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->get()->row_array();
						$akun_110403_lalu = $akun_110403_lalu['debit'];

						$akun_110403 = $this->db->select('ppp.tanggal_invoice as tanggal_transaksi,ppp.nomor_invoice as nomor_transaksi,ppo.no_po as deskripsi,ppp.uang_muka as debit')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_penagihan_pembelian_detail ppd','ppp.id = ppd.penagihan_pembelian_id','left')
						->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->where("ppp.uang_muka >= 1")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction8()" class="btn btn-info"><b>(1-10403) Uang Muka<b></button>
						<div id="myDIV8" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110403_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110403_lalu < 0 ? "(".number_format(-$akun_110403_lalu,0,',','.').")" : number_format($akun_110403_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110403_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110403 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">UANG MUKA</th>
									<th width="20%" class="text-left">INV: <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO: <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10403) Uang Muka | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110403_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110403 = ($akun_110403_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110403 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110403 < 0 ? "(".number_format(-$saldo_110403,0,',','.').")" : number_format($saldo_110403,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />-->
					<div>
						<?php
						$akun_110501_biaya_lalu = $this->db->select('sum(pdb.jumlah) as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("b.bayar_dari = 164")
						->get()->row_array();

						$akun_110501_jurnal_lalu = $this->db->select('sum(pdj.debit) as debit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 164")
						->get()->row_array();
						$akun_110501_lalu = $akun_110501_biaya_lalu['debit'] +  $akun_110501_jurnal_lalu['debit'];

						$akun_110501_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("b.bayar_dari = 164")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_110501_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 164")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_110501 = array_merge($akun_110501_biaya,$akun_110501_jurnal);
						usort($akun_110501, 'sortByOrder');
						?>
						<button onclick="myFunction8()" class="btn btn-info"><b>(1-10501) Pajak Dibayar Di Muka - PPh 22<b></button>
						<div id="myDIV8" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110501_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110501_lalu < 0 ? "(".number_format(-$akun_110501_lalu,0,',','.').")" : number_format($akun_110501_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110501_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110501 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PPH 22</th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10501) Pajak Dibayar Di Muka - PPh 22 | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110501_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110501 = ($akun_110501_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110501 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110501 < 0 ? "(".number_format(-$saldo_110501,0,',','.').")" : number_format($saldo_110501,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_110504_lalu = $this->db->select('sum(ppd.tax) as debit')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_penagihan_pembelian_detail ppd','ppp.id = ppd.penagihan_pembelian_id','left')
						->join('pmm_purchase_order ppo', 'ppp.purchase_order_id = ppo.id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->get()->row_array();
						$akun_110504_lalu = $akun_110504_lalu['debit'];

						$akun_110504 = $this->db->select('ppp.tanggal_invoice as tanggal_transaksi,ppp.nomor_invoice as nomor_transaksi,ppp.no_po as deskripsi,sum(ppd.tax) as debit')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_penagihan_pembelian_detail ppd','ppp.id = ppd.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->where("ppd.tax >= 1")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction9()" class="btn btn-info"><b>(1-10504) PPN Masukan<b></button>
						<div id="myDIV9" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_110504_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110504_lalu < 0 ? "(".number_format(-$akun_110504_lalu,0,',','.').")" : number_format($akun_110504_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_110504_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_110504 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(1-10504) PPN Masukan | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_110504_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_110504 = ($akun_110504_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_110504 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_110504 < 0 ? "(".number_format(-$saldo_110504,0,',','.').")" : number_format($saldo_110504,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220100_lalu = $this->db->select('sum(total) as total')
						->from('pmm_penagihan_pembelian')
						->where("tanggal_invoice between '$date3' and '$date4'")
						->get()->row_array();

						$akun_220100_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_pembayaran_penagihan_pembelian pm')
						->join('pmm_penagihan_pembelian ppp','pm.penagihan_pembelian_id = ppp.id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->get()->row_array();
						$akun_220100_lalu = $akun_220100_lalu['total'] - $akun_220100_pembayaran_lalu['total'];

						$akun_220100 = $this->db->select('ppp.tanggal_invoice as tanggal_transaksi,ppp.nomor_invoice as nomor_transaksi,pm.nomor_transaksi as deskripsi,ppp.total as debit,sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_penagihan_pembelian_detail ppd','ppp.id = ppd.penagihan_pembelian_id','left')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction10()" class="btn btn-info"><b>(2-20100) Hutang Usaha<b></button>
						<div id="myDIV10" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220100_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220100_lalu < 0 ? "(".number_format(-$akun_220100_lalu,0,',','.').")" : number_format($akun_220100_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220100 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PEMBAYARAN : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20100) Hutang Usaha | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220100_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220100 = ($akun_220100_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220100 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220100 < 0 ? "(".number_format(-$saldo_220100,0,',','.').")" : number_format($saldo_220100,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						/*$akun_220101_uncreated_lalu = $this->db->select('sum(prm.display_price) as total')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
						->where("prm.date_receipt between '$date3' and '$date4'")
						->where("prm.status_payment = 'UNCREATED'")
						->where("ppo.status in ('PUBLISH','CLOSED')")
						->get()->row_array();

						$akun_220101_created_lalu = $this->db->select('sum(prm.display_price) as total')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
						->where("prm.date_receipt between '$date3' and '$date4'")
						->where("prm.status_payment in ('CREATED','CREATING')")
						->where("ppo.status in ('PUBLISH','CLOSED')")
						->get()->row_array();
						$akun_220101_lalu = $akun_220101_uncreated_lalu['total'] - $akun_220101_created_lalu['total'];

						$akun_220101_uncreated = $this->db->select('ppo.date_po as tanggal_transaksi,ppo.no_po as nomor_transaksi,sum(prm.display_price) as debit')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("prm.status_payment = 'UNCREATED'")
						->where("ppo.status in ('PUBLISH','CLOSED')")
						->group_by('prm.id')
						->order_by('ppo.date_po','asc')
						->order_by('ppo.no_po','asc')
						->get()->result_array();

						$akun_220101_created = $this->db->select('ppo.date_po as tanggal_transaksi,ppo.no_po as nomor_transaksi,sum(prm.display_price) as kredit')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("prm.status_payment in ('CREATED','CREATING')")
						->where("ppo.status in ('PUBLISH','CLOSED')")
						->group_by('prm.id')
						->order_by('ppo.date_po','asc')
						->order_by('ppo.no_po','asc')
						->get()->result_array();

						$akun_220101 = array_merge($akun_220101_uncreated,$akun_220101_created);
						usort($akun_220101, 'sortByOrder');*/

						$akun_220101_lalu = $this->db->select('sum(prm.display_price) as total')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
						->where("prm.date_receipt between '$date3' and '$date4'")
						->where("prm.status_payment = 'UNCREATED'")
						->where("ppo.status in ('PUBLISH','CLOSED')")
						->get()->row_array();
						$akun_220101_lalu = $akun_220101_lalu['total'];

						$akun_220101 = $this->db->select('ppo.date_po as tanggal_transaksi,ppo.no_po as nomor_transaksi,sum(prm.display_price) as debit')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order ppo', 'prm.purchase_order_id = ppo.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("prm.status_payment = 'UNCREATED'")
						->where("ppo.status in ('PUBLISH','CLOSED')")
						->group_by('prm.id')
						->order_by('ppo.date_po','asc')
						->order_by('ppo.no_po','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction11()" class="btn btn-info"><b>(2-20101) Hutang Belum Ditagih<b></button>
						<div id="myDIV11" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220101_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220101_lalu < 0 ? "(".number_format(-$akun_220101_lalu,0,',','.').")" : number_format($akun_220101_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_220101_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220101 as $x):
							if ($x['kredit']==0) { $saldo = $saldo + $x['kredit'];} else
							{$saldo = $saldo + $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PENERIMAAN</th>
									<th width="20%" class="text-left">PO : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20101) Hutang Belum Ditagih | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220101_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220101 = ($akun_220101_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220101 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220101 < 0 ? "(".number_format(-$saldo_220101,0,',','.').")" : number_format($saldo_220101,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220119_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 17")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220119_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 17")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220119_lalu = $akun_220119_tagihan_lalu['total'] - $akun_220119_pembayaran_lalu['total'];

						$akun_220119 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, pm.total as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 17")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction12()" class="btn btn-info"><b>(2-20119) Hutang PT. Sarana Logistik Indonesia<b></button>
						<div id="myDIV12" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220119_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220119_lalu < 0 ? "(".number_format(-$akun_220119_lalu,0,',','.').")" : number_format($akun_220119_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220119 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV: <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO: <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20119) Hutang PT. Sarana Logistik Indonesia | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220119_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220119 = ($akun_220119_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220119 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220119 < 0 ? "(".number_format(-$saldo_220119,0,',','.').")" : number_format($saldo_220119,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220167_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 18")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220167_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 18")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220167_lalu = $akun_220167_tagihan_lalu['total'] - $akun_220167_pembayaran_lalu['total'];

						$akun_220167 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 18")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction13()" class="btn btn-info"><b>(2-20167) Hutang PT. Hutama Sari Bumi<b></button>
						<div id="myDIV13" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220167_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220167_lalu < 0 ? "(".number_format(-$akun_220167_lalu,0,',','.').")" : number_format($akun_220167_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220167 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20167) Hutang PT. Hutama Sari Bumi | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220167_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220167 = ($akun_220167_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220167 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220167 < 0 ? "(".number_format(-$saldo_220167,0,',','.').")" : number_format($saldo_220167,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220168_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 1")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220168_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 1")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220168_lalu = $akun_220168_tagihan_lalu['total'] - $akun_220168_pembayaran_lalu['total'];

						$akun_220168 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 1")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction14()" class="btn btn-info"><b>(2-20168) Hutang PT. Anugerah Nadya Alyssa<b></button>
						<div id="myDIV14" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220168_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220168_lalu < 0 ? "(".number_format(-$akun_220168_lalu,0,',','.').")" : number_format($akun_220168_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220168 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20168) Hutang PT. Anugerah Nadya Alyssa | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220168_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220168 = ($akun_220168_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220168 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220168 < 0 ? "(".number_format(-$saldo_220168,0,',','.').")" : number_format($saldo_220168,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220169_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 27")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220169_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 27")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220169_lalu = $akun_220169_tagihan_lalu['total'] - $akun_220169_pembayaran_lalu['total'];

						$akun_220169 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 27")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction15()" class="btn btn-info"><b>(2-20169) Hutang PT. Jasa Tiga Sekawan<b></button>
						<div id="myDIV15" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220169_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220169_lalu < 0 ? "(".number_format(-$akun_220169_lalu,0,',','.').")" : number_format($akun_220169_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220169 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20169) Hutang PT. Hutang PT. Jasa Tiga Sekawan | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220169_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220169 = ($akun_220169_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220169 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220169 < 0 ? "(".number_format(-$saldo_220169,0,',','.').")" : number_format($saldo_220169,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220173_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 30")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220173_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 30")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220173_lalu = $akun_220173_tagihan_lalu['total'] - $akun_220173_pembayaran_lalu['total'];

						$akun_220173 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 30")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction16()" class="btn btn-info"><b>(2-20173) Hutang PT. Mitra Beton Mandiri<b></button>
						<div id="myDIV16" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220173_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220173_lalu < 0 ? "(".number_format(-$akun_220173_lalu,0,',','.').")" : number_format($akun_220173_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220173 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20173) Hutang PT. Mitra Beton Mandiri | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220173_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220173 = ($akun_220173_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220173 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220173 < 0 ? "(".number_format(-$saldo_220173,0,',','.').")" : number_format($saldo_220173,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220174_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 19")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220174_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 19")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220174_lalu = $akun_220174_tagihan_lalu['total'] - $akun_220174_pembayaran_lalu['total'];

						$akun_220174 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 19")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction17()" class="btn btn-info"><b>(2-20174) Hutang CV. Anugerah Tirta Selabung<b></button>
						<div id="myDIV17" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220174_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220174_lalu < 0 ? "(".number_format(-$akun_220174_lalu,0,',','.').")" : number_format($akun_220174_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220174 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20174) Hutang CV. Anugerah Tirta Selabung | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220174_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220174 = ($akun_220174_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220174 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220174 < 0 ? "(".number_format(-$saldo_220174,0,',','.').")" : number_format($saldo_220174,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220182_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 20")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220182_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 20")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220182_lalu = $akun_220182_tagihan_lalu['total'] - $akun_220182_pembayaran_lalu['total'];

						$akun_220182 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 20")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction18()" class="btn btn-info"><b>(2-20182) Hutang PT. Lie Otto Pratama<b></button>
						<div id="myDIV18" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220182_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220182_lalu < 0 ? "(".number_format(-$akun_220182_lalu,0,',','.').")" : number_format($akun_220182_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220182 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20182) Hutang PT. Lie Otto Pratama | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220182_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220182 = ($akun_220182_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220182 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220182 < 0 ? "(".number_format(-$saldo_220182,0,',','.').")" : number_format($saldo_220182,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220184_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 29")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220184_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 29")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220184_lalu = $akun_220184_tagihan_lalu['total'] - $akun_220184_pembayaran_lalu['total'];

						$akun_220184 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 29")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction19()" class="btn btn-info"><b>(2-20184) Hutang CV. Selabung Aji Mandiri<b></button>
						<div id="myDIV19" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220184_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220184_lalu < 0 ? "(".number_format(-$akun_220184_lalu,0,',','.').")" : number_format($akun_220184_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220184 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20184) Hutang CV. Selabung Aji Mandiri | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220184_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220184 = ($akun_220184_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220184 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220184 < 0 ? "(".number_format(-$saldo_220184,0,',','.').")" : number_format($saldo_220184,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220185_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 28")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220185_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 28")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220185_lalu = $akun_220185_tagihan_lalu['total'] - $akun_220185_pembayaran_lalu['total'];

						$akun_220185 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 28")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction20()" class="btn btn-info"><b>(2-20185) Hutang PT. Maxi Pilar Indonesia<b></button>
						<div id="myDIV20" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220185_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220185_lalu < 0 ? "(".number_format(-$akun_220185_lalu,0,',','.').")" : number_format($akun_220185_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220185 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20185) Hutang PT. Maxi Pilar Indonesia | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220185_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220185 = ($akun_220185_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220185 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220185 < 0 ? "(".number_format(-$saldo_220185,0,',','.').")" : number_format($saldo_220185,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220186_tagihan_lalu = $this->db->select('ppp.total as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 26")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();

						$akun_220186_pembayaran_lalu = $this->db->select('sum(pm.total) as total')
						->from('pmm_penagihan_pembelian ppp')
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->where("ppp.supplier_id = 26")
						->where("ppp.status = 'BELUM LUNAS'")
						->get()->row_array();
						$akun_220186_lalu = $akun_220186_tagihan_lalu['total'] - $akun_220186_pembayaran_lalu['total'];

						$akun_220186 = $this->db->select('ppp.*, ppp.tanggal_invoice as tanggal_transaksi, ppp.nomor_invoice as nomor_transaksi, ppp.no_po as deskripsi, ppp.total as debit, sum(pm.total) as kredit')
						->from('pmm_penagihan_pembelian ppp')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->join('pmm_pembayaran_penagihan_pembelian pm', 'ppp.id = pm.penagihan_pembelian_id','left')
						->where("ppp.supplier_id = 26")
						->where("ppp.status = 'BELUM LUNAS'")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction21()" class="btn btn-info"><b>(2-20186) Hutang PT. Tricakra Indonesia<b></button>
						<div id="myDIV21" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220186_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220186_lalu < 0 ? "(".number_format(-$akun_220186_lalu,0,',','.').")" : number_format($akun_220186_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = 0;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220186 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TAGIHAN PEMBELIAN</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20186) Hutang PT. Tricakra Indonesia | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220186_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220186 = ($akun_220186_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220186 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220186 < 0 ? "(".number_format(-$saldo_220186,0,',','.').")" : number_format($saldo_220186,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220404_biaya_lalu = $this->db->select('sum(pdb.jumlah) as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("b.bayar_dari = 185")
						->get()->row_array();

						$akun_220404_jurnal_lalu = $this->db->select('sum(pdj.debit) as debit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 185")
						->get()->row_array();
						$akun_220404_lalu = $akun_220404_biaya_lalu['debit'] +  $akun_220404_jurnal_lalu['debit'];

						$akun_220404_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("b.bayar_dari = 185")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_220404_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 185")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_220404 = array_merge($akun_220404_biaya,$akun_220404_jurnal);
						usort($akun_220404, 'sortByOrder');
						?>
						<button onclick="myFunction56()" class="btn btn-info"><b>(2-20201) Hutang Biaya<b></button>
						<div id="myDIV56" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220404_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220404_lalu < 0 ? "(".number_format(-$akun_220404_lalu,0,',','.').")" : number_format($akun_220404_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_220404_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220404 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">HUTANG BIAYA</th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO :<?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20201) Hutang Biaya | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220404_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220404 = ($akun_220404_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220404 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220404 < 0 ? "(".number_format(-$saldo_220404,0,',','.').")" : number_format($saldo_220404,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220300_biaya_lalu = $this->db->select('sum(pdb.jumlah) as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("b.bayar_dari = 154")
						->get()->row_array();

						$akun_220300_jurnal_lalu = $this->db->select('sum(pdj.debit) as debit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 154")
						->get()->row_array();
						$akun_220300_lalu = $akun_220300_biaya_lalu['debit'] +  $akun_220300_jurnal_lalu['debit'];

						$akun_220300_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("b.bayar_dari = 154")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_220300_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 154")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_220300 = array_merge($akun_220300_biaya,$akun_220300_jurnal);
						usort($akun_220300, 'sortByOrder');
						?>
						<button onclick="myFunction22()" class="btn btn-info"><b>(2-20300) Hutang Bank<b></button>
						<div id="myDIV22" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220300_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220300_lalu < 0 ? "(".number_format(-$akun_220300_lalu,0,',','.').")" : number_format($akun_220300_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_220300_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220300 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">HUTANG BANK</th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">PO :<?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20300) Hutang Bank | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220300_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220300 = ($akun_220300_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220300 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220300 < 0 ? "(".number_format(-$saldo_220300,0,',','.').")" : number_format($saldo_220300,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220205_lalu = $this->db->select('sum(tu.jumlah) as total')
						->from('pmm_terima_uang tu')
						->where("tu.tanggal_transaksi between '$date3' and '$date4'")
						->get()->row_array();
						$akun_220205_lalu = $akun_220205_lalu['total'];

						$akun_220205 = $this->db->select('tu.tanggal_transaksi as tanggal_transaksi,tu.nomor_transaksi as nomor_transaksi,sum(tu.jumlah) as debit')
						->from('pmm_terima_uang tu')
						->where("tu.tanggal_transaksi between '$date1' and '$date2'")
						->group_by('tu.id')
						->order_by('tu.tanggal_transaksi','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction23()" class="btn btn-info"><b>(2-20205) Hutang Modal<b></button>
						<div id="myDIV23" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220205_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220205_lalu < 0 ? "(".number_format(-$akun_220205_lalu,0,',','.').")" : number_format($akun_220205_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_220205_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220205 as $x):
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">DROPING</th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20205) Hutang Modal | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220205_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220205 = ($akun_220205_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220205 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220205 < 0 ? "(".number_format(-$saldo_220205,0,',','.').")" : number_format($saldo_220205,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220404_lalu = $this->db->select('sum(tu.jumlah) as total')
						->from('pmm_terima_uang tu')
						->where("tu.tanggal_transaksi between '$date3' and '$date4'")
						->get()->row_array();

						$akun_220404_jurnal_lalu = $this->db->select('sum(pdj.debit) as debit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 147")
						->get()->row_array();
						$akun_220404_lalu = $akun_220404_lalu['total'] +  $akun_220404_jurnal_lalu['kredit'];

						$akun_220404_terima = $this->db->select('tu.*,tu.jumlah as kredit, tu.memo as deskripsi')
						->from('pmm_terima_uang tu')
						->where("tu.tanggal_transaksi between '$date1' and '$date2'")
						->where("tu.terima_dari = 147")
						->order_by('tu.tanggal_transaksi','asc')
						->order_by('tu.created_on','asc')
						->get()->result_array();

						$akun_220404_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 147")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_220404 = array_merge($akun_220404_terima,$akun_220404_jurnal);
						usort($akun_220404, 'sortByOrder');
						?>
						<button onclick="myFunction24()" class="btn btn-info"><b>(2-20404) Pinjaman Proyek<b></button>
						<div id="myDIV24" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220404_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220404_lalu < 0 ? "(".number_format(-$akun_220404_lalu,0,',','.').")" : number_format($akun_220404_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_220404_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220404 as $x): 
							if ($x['kredit']==0) { $saldo = $saldo + $x['kredit'];} else
							{$saldo = $saldo + $x['kredit'] ;}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">TERIMA UANG</th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20404) Pinjaman Proyek | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220404_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220404 = ($akun_220404_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_220404 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220404 < 0 ? "(".number_format(-$saldo_220404,0,',','.').")" : number_format($saldo_220404,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220505_lalu = $this->db->select('sum(ppd.tax) as total')
						->from('pmm_penagihan_penjualan ppp')
						->join('pmm_penagihan_penjualan_detail ppd','ppp.id = ppd.penagihan_id','left')
						->where("ppp.tanggal_invoice between '$date3' and '$date4'")
						->get()->row_array();
						$akun_220505_lalu = $akun_220505_lalu['total'];

						$akun_220505 = $this->db->select('ppp.tanggal_invoice as tanggal_transaksi,ppp.nomor_invoice as nomor_transaksi,ppo.contract_number as deskripsi,sum(ppd.tax) as debit')
						->from('pmm_penagihan_penjualan ppp')
						->join('pmm_penagihan_penjualan_detail ppd','ppp.id = ppd.penagihan_id','left')
						->join('pmm_sales_po ppo', 'ppp.sales_po_id = ppo.id','left')
						->where("ppp.tanggal_invoice between '$date1' and '$date2'")
						->where("ppd.tax >= 1")
						->group_by('ppp.id')
						->order_by('ppp.tanggal_invoice','asc')
						->order_by('ppp.created_on','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction25()" class="btn btn-info"><b>(2-20505) PPN Keluaran<b></button>
						<div id="myDIV25" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220505_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220505_lalu < 0 ? "(".number_format(-$akun_220505_lalu,0,',','.').")" : number_format($akun_220505_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_220505_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220505 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">SALES INVOICE</th>
									<th width="20%" class="text-left">INV : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">SALES ORDER : <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20505) PPN Keluaran | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220505_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220505 = ($akun_220505_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220505 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220505 < 0 ? "(".number_format(-$saldo_220505,0,',','.').")" : number_format($saldo_220505,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_220506_biaya_lalu = $this->db->select('sum(pdb.jumlah) as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("b.bayar_dari = 157")
						->get()->row_array();

						$akun_220506_jurnal_lalu = $this->db->select('sum(pdj.debit) as debit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 157")
						->get()->row_array();
						$akun_220506_lalu = $akun_220506_biaya_lalu['debit'] +  $akun_220506_jurnal_lalu['debit'];

						$akun_220506_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("b.bayar_dari = 157")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_220506_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 157")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_220506 = array_merge($akun_220506_biaya,$akun_220506_jurnal);
						usort($akun_220506, 'sortByOrder');
						?>
						<button onclick="myFunction26()" class="btn btn-info"><b>(2-20506) Hutang Pajak - PPh Pasal 4 Ayat (2)<b></button>
						<div id="myDIV26" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_220506_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220506_lalu < 0 ? "(".number_format(-$akun_220506_lalu,0,',','.').")" : number_format($akun_220506_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_220506_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_220506 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'] - $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PPH 22</th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(2-20506) Hutang Pajak - PPh Pasal 4 Ayat (2) | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_220506_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_220506 = ($akun_220506_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_220506 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_220506 < 0 ? "(".number_format(-$saldo_220506,0,',','.').")" : number_format($saldo_220506,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_440001_uncreated_lalu =$this->db->select('sum(pp.display_price) as total')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date3' and '$date4'")
						->where("pp.status = 'PUBLISH'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->get()->row_array();
						$akun_440001_lalu = $akun_440001_uncreated_lalu['total'];

						$akun_440001 = $this->db->select('pp.no_production as nomor_transaksi, pp.memo as deskripsi, pp.date_production as tanggal_transaksi,ppo.contract_number as deskripsi,pp.display_price as kredit')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date1' and '$date2'")
						->where("pp.status = 'PUBLISH'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->group_by('pp.id')
						->get()->result_array();
						?>
						<button onclick="myFunction27()" class="btn btn-info"><b>(4-40001) Pendapatan Usaha<b></button>
						<div id="myDIV27" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_440001_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_440001_lalu < 0 ? "(".number_format(-$akun_440001_lalu,0,',','.').")" : number_format($akun_440001_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_440001_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_440001 as $x): 
							if ($x['kredit']==0) { $saldo = $saldo + $x['kredit'];} else
							{$saldo = $saldo + $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PENGIRIMAN</th>
									<th width="20%" class="text-left">NO. SURAT JALAN : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(4-40001) Pendapatan Usaha | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_440001_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_440001 = ($akun_440001_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_440001 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_440001 < 0 ? "(".number_format(-$saldo_440001,0,',','.').")" : number_format($saldo_440001,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php

						$akun_440004_uncreated_lalu =$this->db->select('sum(pp.display_price) as total')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date3' and '$date4'")
						->where("pp.status = 'PUBLISH'")
						->where("pp.status_payment = 'UNCREATED'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->get()->row_array();
						$akun_440004_lalu = $akun_440004_uncreated_lalu['total'];

						$akun_440004 = $this->db->select('pp.no_production as nomor_transaksi, pp.date_production as tanggal_transaksi,ppo.contract_number as deskripsi,pp.display_price as kredit')
						->from('pmm_productions pp')
						->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
						->where("pp.date_production between '$date1' and '$date2'")
						->where("pp.status = 'PUBLISH'")
						->where("pp.status_payment = 'UNCREATED'")
						->where("ppo.status in ('OPEN','CLOSED')")
						->group_by('pp.id')
						->get()->result_array();
						?>
						<button onclick="myFunction28()" class="btn btn-info"><b>(4-40004) Pendapatan Belum Ditagih<b></button>
						<div id="myDIV28" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_440004_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_440004_lalu < 0 ? "(".number_format(-$akun_440004_lalu,0,',','.').")" : number_format($akun_440004_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_440004_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_440004 as $x): 
							if ($x['kredit']==0) { $saldo = $saldo + $x['kredit'];} else
							{$saldo = $saldo + $x['kredit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PENGIRIMAN</th>
									<th width="20%" class="text-left">NO. SURAT JALAN : <?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left">SALES ORDER: <?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(4-40004) Pendapatan Belum Ditagih | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_440004_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_440004 = ($akun_440004_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_440004 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_440004 < 0 ? "(".number_format(-$saldo_440004,0,',','.').")" : number_format($saldo_440004,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550100_lalu = $this->db->select('nilai as total')
						->from('pemakaian_bahan')
						->where("date between '$date3' and '$date4'")
						->where("status = 'PUBLISH'")
						->get()->row_array();
						$akun_550100_lalu = $akun_550100_lalu['total'];

						$akun_550100 = $this->db->select('pm.*, pm.date as tanggal_transaksi, p.nama_produk as deskripsi, pm.nilai as debit')
						->from('pemakaian_bahan pm')
						->join('produk p','pm.material_id = p.id','left')
						->where("pm.date between '$date1' and '$date2'")
						->where("pm.status = 'PUBLISH'")
						->group_by('pm.id')
						->order_by('pm.date','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction29()" class="btn btn-info"><b>(5-50100) Biaya Bahan<b></button>
						<div id="myDIV29" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550100_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550100_lalu < 0 ? "(".number_format(-$akun_550100_lalu,0,',','.').")" : number_format($akun_550100_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550100_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550100 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PEMAKAIAN BAHAN</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50100) Biaya Bahan | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550100_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550100 = ($akun_550100_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_550100 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550100 < 0 ? "(".number_format(-$saldo_550100,0,',','.').")" : number_format($saldo_550100,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550201_pembelian_lalu = $this->db->select('prm.price as total')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->join('penerima pn', 'po.supplier_id = pn.id','left')
						->where("prm.date_receipt between '$date3' and '$date4'")
						->where("p.kategori_alat = '1'")
						->where("po.status in ('PUBLISH','CLOSED')")
						->get()->row_array();

						$akun_550201_pembelian = $this->db->select('prm.date_receipt as tanggal_transaksi, prm.memo as deskripsi, prm.price as debit')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->join('penerima pn', 'po.supplier_id = pn.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_alat = '1'")
						->where("po.status in ('PUBLISH','CLOSED')")
						->group_by('prm.id')
						->order_by('prm.date_receipt','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction30()" class="btn btn-info"><b>(5-50201) Biaya Alat Batching Plant<b></button>
						<div id="myDIV30" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550201_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550201_lalu < 0 ? "(".number_format(-$akun_550201_lalu,0,',','.').")" : number_format($akun_550201_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550201_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550201 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50201) Biaya Alat Batching Plant | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550201_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550201 = ($akun_550201_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_550201 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550201 < 0 ? "(".number_format(-$saldo_550201,0,',','.').")" : number_format($saldo_550201,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_5502013_lalu = $this->db->select('sum(pdb.debit) as total')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 159")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date3' and '$date4')")
						->get()->row_array();
						$akun_5502013_lalu = $akun_5502013_lalu['total'];

						$akun_5502013 = $this->db->select('pb.*, pb.tanggal_transaksi, pdb.deskripsi, pdb.debit as debit')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 159")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
						->group_by('pdb.id')
						->order_by('pb.tanggal_transaksi','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction31()" class="btn btn-info"><b>(5-50201.3) Angsuran Batching Plant<b></button>
						<div id="myDIV31" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_5502013_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_5502013_lalu < 0 ? "(".number_format(-$akun_5502013_lalu,0,',','.').")" : number_format($akun_5502013_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_5502013_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_5502013 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50201.3) Angsuran Batching Plant | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_5502013_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_5502013 = ($akun_5502013_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_5502013 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_5502013 < 0 ? "(".number_format(-$saldo_5502013,0,',','.').")" : number_format($saldo_5502013,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_5502012_lalu = $this->db->select('sum(pdb.debit) as total')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 138")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date3' and '$date4')")
						->get()->row_array();
						$akun_5502012_lalu = $akun_5502012_lalu['total'];

						$akun_5502012 = $this->db->select('pb.*, pb.tanggal_transaksi, pdb.deskripsi, pdb.debit as debit')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 138")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
						->group_by('pdb.id')
						->order_by('pb.tanggal_transaksi','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction32()" class="btn btn-info"><b>(5-50201.2) Maintenance Batching Plant<b></button>
						<div id="myDIV32" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_5502012_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_5502012_lalu < 0 ? "(".number_format(-$akun_5502012_lalu,0,',','.').")" : number_format($akun_5502012_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_5502012_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_5502012 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50201.2) Maintenance Batching Plant | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_5502012_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_5502012 = ($akun_5502012_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_5502012 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_5502012 < 0 ? "(".number_format(-$saldo_5502012,0,',','.').")" : number_format($saldo_5502012,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550202_pembelian_lalu = $this->db->select('prm.price as total')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->join('penerima pn', 'po.supplier_id = pn.id','left')
						->where("prm.date_receipt between '$date3' and '$date4'")
						->where("p.kategori_alat = '2'")
						->where("po.status in ('PUBLISH','CLOSED')")
						->get()->row_array();

						$akun_550202_pembelian = $this->db->select('prm.date_receipt as tanggal_transaksi, prm.memo as deskripsi, prm.price as debit')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->join('penerima pn', 'po.supplier_id = pn.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_alat = '2'")
						->where("po.status in ('PUBLISH','CLOSED')")
						->group_by('prm.id')
						->order_by('prm.date_receipt','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction33()" class="btn btn-info"><b>(5-50202) Biaya Alat Truck Mixer<b></button>
						<div id="myDIV33" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550202_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550202_lalu < 0 ? "(".number_format(-$akun_550202_lalu,0,',','.').")" : number_format($akun_550202_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550202_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550202 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50202) Biaya Alat Truck Mixer | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550202_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550202 = ($akun_550202_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_550202 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550202 < 0 ? "(".number_format(-$saldo_550202,0,',','.').")" : number_format($saldo_550202,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_5502022_lalu = $this->db->select('sum(pdb.debit) as total')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 161")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date3' and '$date4')")
						->get()->row_array();
						$akun_5502022_lalu = $akun_5502022_lalu['total'];

						$akun_5502022 = $this->db->select('pb.*, pb.tanggal_transaksi, pdb.deskripsi, pdb.debit as debit')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 161")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
						->group_by('pdb.id')
						->order_by('pb.tanggal_transaksi','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction34()" class="btn btn-info"><b>(5-50202.2) Maintenance Truck Mixer<b></button>
						<div id="myDIV34" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_5502022_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_5502022_lalu < 0 ? "(".number_format(-$akun_5502022_lalu,0,',','.').")" : number_format($akun_5502022_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_5502022_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_5502022 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50202.2) Maintenance Truck Mixer | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_5502022_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_5502022 = ($akun_5502022_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_5502022 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_5502022 < 0 ? "(".number_format(-$saldo_5502022,0,',','.').")" : number_format($saldo_5502022,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_5502024_pembelian_lalu = $this->db->select('prm.price as total')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->join('penerima pn', 'po.supplier_id = pn.id','left')
						->where("prm.date_receipt between '$date3' and '$date4'")
						->where("p.kategori_alat = '2'")
						->where("po.status in ('PUBLISH','CLOSED')")
						->get()->row_array();

						$akun_5502024_pembelian = $this->db->select('prm.date_receipt as tanggal_transaksi, prm.memo as deskripsi, prm.price as debit')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->join('penerima pn', 'po.supplier_id = pn.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_alat = '2'")
						->where("po.status in ('PUBLISH','CLOSED')")
						->group_by('prm.id')
						->order_by('prm.date_receipt','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction35()" class="btn btn-info"><b>(5-50202.4) Sewa Truck Mixer<b></button>
						<div id="myDIV35" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_5502024_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_5502024_lalu < 0 ? "(".number_format(-$akun_5502024_lalu,0,',','.').")" : number_format($akun_5502024_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_5502024_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_5502024 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50202.4) Sewa Truck Mixer | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_5502024_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_5502024 = ($akun_5502024_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_5502024 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_5502024 < 0 ? "(".number_format(-$saldo_5502024,0,',','.').")" : number_format($saldo_5502024,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550203_pembelian_lalu = $this->db->select('prm.price as total')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->join('penerima pn', 'po.supplier_id = pn.id','left')
						->where("prm.date_receipt between '$date3' and '$date4'")
						->where("p.kategori_alat = '3'")
						->where("po.status in ('PUBLISH','CLOSED')")
						->get()->row_array();

						$akun_550203_pembelian = $this->db->select('prm.date_receipt as tanggal_transaksi, prm.memo as deskripsi, prm.price as debit')
						->from('pmm_receipt_material prm')
						->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
						->join('produk p', 'prm.material_id = p.id','left')
						->join('penerima pn', 'po.supplier_id = pn.id','left')
						->where("prm.date_receipt between '$date1' and '$date2'")
						->where("p.kategori_alat = '3'")
						->where("po.status in ('PUBLISH','CLOSED')")
						->group_by('prm.id')
						->order_by('prm.date_receipt','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction36()" class="btn btn-info"><b>(5-50203) Biaya Alat Wheel Loader<b></button>
						<div id="myDIV36" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550203_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550203_lalu < 0 ? "(".number_format(-$akun_550203_lalu,0,',','.').")" : number_format($akun_550203_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550203_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550203 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50203) Biaya Alat Wheel Loader | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550203_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550203 = ($akun_550203_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_550203 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550203 < 0 ? "(".number_format(-$saldo_550203,0,',','.').")" : number_format($saldo_550203,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_5502032_lalu = $this->db->select('sum(pdb.debit) as total')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 140")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date3' and '$date4')")
						->get()->row_array();
						$akun_5502032_lalu = $akun_5502032_lalu['total'];

						$akun_5502032 = $this->db->select('pb.*, pb.tanggal_transaksi, pdb.deskripsi, pdb.debit as debit')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 140")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
						->group_by('pdb.id')
						->order_by('pb.tanggal_transaksi','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction37()" class="btn btn-info"><b>(5-50203.2) Maintenance Wheel Loader<b></button>
						<div id="myDIV37" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_5502032_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_5502032_lalu < 0 ? "(".number_format(-$akun_5502032_lalu,0,',','.').")" : number_format($akun_5502032_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_5502032_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_5502032 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50203.2) Maintenance Wheel Loader | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_5502032_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_5502032 = ($akun_5502032_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_5502032 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_5502032 < 0 ? "(".number_format(-$saldo_5502032,0,',','.').")" : number_format($saldo_5502032,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_55020333_lalu = $this->db->select('sum(pdb.debit) as total')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 160")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date3' and '$date4')")
						->get()->row_array();
						$akun_55020333_lalu = $akun_55020333_lalu['total'];

						$akun_55020333 = $this->db->select('pb.*, pb.tanggal_transaksi, pdb.deskripsi, pdb.debit as debit')
						->from('pmm_jurnal_umum pb ')
						->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
						->join('pmm_coa c','pdb.akun = c.id','left')
						->where("pdb.akun = 160")
						->where("pb.status = 'PAID'")
						->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
						->group_by('pdb.id')
						->order_by('pb.tanggal_transaksi','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction38()" class="btn btn-info"><b>(5-502033.3) Angsuran Wheel Loader<b></button>
						<div id="myDIV38" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_55020333_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_55020333_lalu < 0 ? "(".number_format(-$akun_55020333_lalu,0,',','.').")" : number_format($akun_55020333_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_55020333_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_55020333 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">JURNAL UMUM</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-502033.3) Angsuran Wheel Loader | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_55020333_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_55020333 = ($akun_55020333_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_55020333 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_55020333 < 0 ? "(".number_format(-$saldo_55020333,0,',','.').")" : number_format($saldo_55020333,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550204_lalu = $this->db->select('nilai as total')
						->from('pemakaian_bahan')
						->where("date between '$date3' and '$date4'")
						->where("status = 'PUBLISH'")
						->where("material_id = 5")
						->get()->row_array();
						$akun_550204_lalu = $akun_550204_lalu['total'];

						$akun_550204 = $this->db->select('pm.*, pm.date as tanggal_transaksi, p.nama_produk as deskripsi, pm.nilai as debit')
						->from('pemakaian_bahan pm')
						->join('produk p','pm.material_id = p.id','left')
						->where("pm.date between '$date1' and '$date2'")
						->where("pm.status = 'PUBLISH'")
						->where("material_id = 5")
						->group_by('pm.id')
						->order_by('pm.date','asc')
						->get()->result_array();
						?>
						<button onclick="myFunction39()" class="btn btn-info"><b>(5-50204) Biaya Solar<b></button>
						<div id="myDIV39" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550204_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550204_lalu < 0 ? "(".number_format(-$akun_550204_lalu,0,',','.').")" : number_format($akun_550204_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550204_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550204 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left">PEMAKAIAN SOLAR</th>
									<th width="20%" class="text-left"></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50204) Biaya Solar | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550204_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550204 = ($akun_550204_lalu + $total_debit) + $total_kredit;
									$styleColor = $saldo_550204 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550204 < 0 ? "(".number_format(-$saldo_550204,0,',','.').")" : number_format($saldo_550204,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550301_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 116")
						->get()->row_array();

						$akun_550301_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 116")
						->get()->row_array();
						$akun_550301_lalu = $akun_550301_biaya_lalu['total'] + $akun_550301_jurnal['total'];

						$akun_550301_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 116")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550301_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 116")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550301 = array_merge($akun_550301_biaya,$akun_550301_jurnal);
						usort($akun_550301, 'sortByOrder');
						?>
						<button onclick="myFunction40()" class="btn btn-info"><b>(5-50301) Konsumsi<b></button>
						<div id="myDIV40" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550301_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550301_lalu < 0 ? "(".number_format(-$akun_550301_lalu,0,',','.').")" : number_format($akun_550301_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550301_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550301 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50301) Konsumsi | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550301_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550301 = ($akun_550301_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550301 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550301 < 0 ? "(".number_format(-$saldo_550301,0,',','.').")" : number_format($saldo_550301,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550302_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 118")
						->get()->row_array();

						$akun_550302_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 118")
						->get()->row_array();
						$akun_550302_lalu = $akun_550302_biaya_lalu['total'] + $akun_550302_jurnal['total'];

						$akun_550302_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 118")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550302_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 118")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550302 = array_merge($akun_550302_biaya,$akun_550302_jurnal);
						usort($akun_550302, 'sortByOrder');
						?>
						<button onclick="myFunction41()" class="btn btn-info"><b>(5-50302) Listrik & Internet<b></button>
						<div id="myDIV41" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550302_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550302_lalu < 0 ? "(".number_format(-$akun_550302_lalu,0,',','.').")" : number_format($akun_550302_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550302_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550302 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50302) Listrik & Internet | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550302_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550302 = ($akun_550302_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550302 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550302 < 0 ? "(".number_format(-$saldo_550302,0,',','.').")" : number_format($saldo_550302,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550304_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 114")
						->get()->row_array();

						$akun_550304_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 114")
						->get()->row_array();
						$akun_550304_lalu = $akun_550304_biaya_lalu['total'] + $akun_550304_jurnal['total'];

						$akun_550304_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 114")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550304_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 114")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550304 = array_merge($akun_550304_biaya,$akun_550304_jurnal);
						usort($akun_550304, 'sortByOrder');
						?>
						<button onclick="myFunction42()" class="btn btn-info"><b>(5-50304) Gaji<b></button>
						<div id="myDIV42" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550304_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550304_lalu < 0 ? "(".number_format(-$akun_550304_lalu,0,',','.').")" : number_format($akun_550304_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550304_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550304 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50304) Gaji | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550304_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550304 = ($akun_550304_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550304 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550304 < 0 ? "(".number_format(-$saldo_550304,0,',','.').")" : number_format($saldo_550304,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550305_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 143")
						->get()->row_array();

						$akun_550305_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 143")
						->get()->row_array();
						$akun_550305_lalu = $akun_550305_biaya_lalu['total'] + $akun_550305_jurnal['total'];

						$akun_550305_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 143")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550305_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 143")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550305 = array_merge($akun_550305_biaya,$akun_550305_jurnal);
						usort($akun_550305, 'sortByOrder');
						?>
						<button onclick="myFunction43()" class="btn btn-info"><b>(5-50305) Akomodasi Tamu<b></button>
						<div id="myDIV43" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550305_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550305_lalu < 0 ? "(".number_format(-$akun_550305_lalu,0,',','.').")" : number_format($akun_550305_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550305_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550305 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50305) Akomodasi Tamu | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550305_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550305 = ($akun_550305_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550305 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550305 < 0 ? "(".number_format(-$saldo_550305,0,',','.').")" : number_format($saldo_550305,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550306_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 141")
						->get()->row_array();

						$akun_550306_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 141")
						->get()->row_array();
						$akun_550306_lalu = $akun_550306_biaya_lalu['total'] + $akun_550306_jurnal['total'];

						$akun_550306_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 141")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550306_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 141")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550306 = array_merge($akun_550306_biaya,$akun_550306_jurnal);
						usort($akun_550306, 'sortByOrder');
						?>
						<button onclick="myFunction44()" class="btn btn-info"><b>(5-50306) Biaya Perbaikan & Pemeliharaan<b></button>
						<div id="myDIV44" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550306_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550306_lalu < 0 ? "(".number_format(-$akun_550306_lalu,0,',','.').")" : number_format($akun_550306_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550306_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550306 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50306) Biaya Perbaikan & Pemeliharaan | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550306_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550306 = ($akun_550306_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550306 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550306 < 0 ? "(".number_format(-$saldo_550306,0,',','.').")" : number_format($saldo_550306,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550307_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 117")
						->get()->row_array();

						$akun_550307_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 117")
						->get()->row_array();
						$akun_550307_lalu = $akun_550307_biaya_lalu['total'] + $akun_550307_jurnal['total'];

						$akun_550307_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 117")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550307_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 117")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550307 = array_merge($akun_550307_biaya,$akun_550307_jurnal);
						usort($akun_550307, 'sortByOrder');
						?>
						<button onclick="myFunction45()" class="btn btn-info"><b>(5-50307) THR & Bonus<b></button>
						<div id="myDIV45" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550307_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550307_lalu < 0 ? "(".number_format(-$akun_550307_lalu,0,',','.').")" : number_format($akun_550307_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550307_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550307 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50307) Gaji | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550307_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550307 = ($akun_550307_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550307 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550307 < 0 ? "(".number_format(-$saldo_550307,0,',','.').")" : number_format($saldo_550307,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550310_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 100")
						->get()->row_array();

						$akun_550310_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 100")
						->get()->row_array();
						$akun_550310_lalu = $akun_550310_biaya_lalu['total'] + $akun_550310_jurnal['total'];

						$akun_550310_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 100")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550310_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 100")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550310 = array_merge($akun_550310_biaya,$akun_550310_jurnal);
						usort($akun_550310, 'sortByOrder');
						?>
						<button onclick="myFunction46()" class="btn btn-info"><b>(5-50310) Sewa Kendaraan<b></button>
						<div id="myDIV46" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550310_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550310_lalu < 0 ? "(".number_format(-$akun_550310_lalu,0,',','.').")" : number_format($akun_550310_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550310_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550310 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50310) Sewa Kendaraan | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550310_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550310 = ($akun_550310_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550310 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550310 < 0 ? "(".number_format(-$saldo_550310,0,',','.').")" : number_format($saldo_550310,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550311_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 78")
						->get()->row_array();

						$akun_550311_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 78")
						->get()->row_array();
						$akun_550311_lalu = $akun_550311_biaya_lalu['total'] + $akun_550311_jurnal['total'];

						$akun_550311_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 78")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550311_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 78")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550311 = array_merge($akun_550311_biaya,$akun_550311_jurnal);
						usort($akun_550311, 'sortByOrder');
						?>
						<button onclick="myFunction47()" class="btn btn-info"><b>(5-50311) Bensin, Tol dan Parkir<b></button>
						<div id="myDIV47" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550311_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550311_lalu < 0 ? "(".number_format(-$akun_550311_lalu,0,',','.').")" : number_format($akun_550311_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550311_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550311 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50311) Bensin, Tol dan Parkir | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550311_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550311 = ($akun_550311_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550311 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550311 < 0 ? "(".number_format(-$saldo_550311,0,',','.').")" : number_format($saldo_550311,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550313_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 87")
						->get()->row_array();

						$akun_550313_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 87")
						->get()->row_array();
						$akun_550313_lalu = $akun_550313_biaya_lalu['total'] + $akun_550313_jurnal['total'];

						$akun_550313_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 87")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550313_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 87")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550313 = array_merge($akun_550313_biaya,$akun_550313_jurnal);
						usort($akun_550313, 'sortByOrder');
						?>
						<button onclick="myFunction48()" class="btn btn-info"><b>(5-50313) Pakaian Dinas & K3<b></button>
						<div id="myDIV48" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550313_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550313_lalu < 0 ? "(".number_format(-$akun_550313_lalu,0,',','.').")" : number_format($akun_550313_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550313_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550313 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50313) Pakaian Dinas & K3 | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550313_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550313 = ($akun_550313_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550313 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550313 < 0 ? "(".number_format(-$saldo_550313,0,',','.').")" : number_format($saldo_550313,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550314_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 62")
						->get()->row_array();

						$akun_550314_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 62")
						->get()->row_array();
						$akun_550314_lalu = $akun_550314_biaya_lalu['total'] + $akun_550314_jurnal['total'];

						$akun_550314_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 62")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550314_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 62")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550314 = array_merge($akun_550314_biaya,$akun_550314_jurnal);
						usort($akun_550314, 'sortByOrder');
						?>
						<button onclick="myFunction49()" class="btn btn-info"><b>(5-50314) Perjalanan Dinas Umum<b></button>
						<div id="myDIV49" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550314_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550314_lalu < 0 ? "(".number_format(-$akun_550314_lalu,0,',','.').")" : number_format($akun_550314_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550314_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550314 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50314) Perjalanan Dinas Umum | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550314_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550314 = ($akun_550314_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550314 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550314 < 0 ? "(".number_format(-$saldo_550314,0,',','.').")" : number_format($saldo_550314,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550315_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 97")
						->get()->row_array();

						$akun_550315_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 97")
						->get()->row_array();
						$akun_550315_lalu = $akun_550315_biaya_lalu['total'] + $akun_550315_jurnal['total'];

						$akun_550315_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 97")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550315_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 97")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550315 = array_merge($akun_550315_biaya,$akun_550315_jurnal);
						usort($akun_550315, 'sortByOrder');
						?>
						<button onclick="myFunction50()" class="btn btn-info"><b>(5-50315) Perlengkapan Kantor<b></button>
						<div id="myDIV50" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550315_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550315_lalu < 0 ? "(".number_format(-$akun_550315_lalu,0,',','.').")" : number_format($akun_550315_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550315_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550315 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50315) Perlengkapan Kantor | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550315_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550315 = ($akun_550315_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550315 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550315 < 0 ? "(".number_format(-$saldo_550315,0,',','.').")" : number_format($saldo_550315,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550316_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 70")
						->get()->row_array();

						$akun_550316_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 70")
						->get()->row_array();
						$akun_550316_lalu = $akun_550316_biaya_lalu['total'] + $akun_550316_jurnal['total'];

						$akun_550316_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 70")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550316_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 70")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550316 = array_merge($akun_550316_biaya,$akun_550316_jurnal);
						usort($akun_550316, 'sortByOrder');
						?>
						<button onclick="myFunction51()" class="btn btn-info"><b>(5-50316) Pengobatan<b></button>
						<div id="myDIV51" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550316_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550316_lalu < 0 ? "(".number_format(-$akun_550316_lalu,0,',','.').")" : number_format($akun_550316_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550316_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550316 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50316) Pengobatan | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550316_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550316 = ($akun_550316_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550316 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550316 < 0 ? "(".number_format(-$saldo_550316,0,',','.').")" : number_format($saldo_550316,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550317_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 96")
						->get()->row_array();

						$akun_550317_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 96")
						->get()->row_array();
						$akun_550317_lalu = $akun_550317_biaya_lalu['total'] + $akun_550317_jurnal['total'];

						$akun_550317_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 96")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550317_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 96")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550317 = array_merge($akun_550317_biaya,$akun_550317_jurnal);
						usort($akun_550317, 'sortByOrder');
						?>
						<button onclick="myFunction52()" class="btn btn-info"><b>(5-50317) Alat Tulis Kantor & Printing<b></button>
						<div id="myDIV52" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550317_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550317_lalu < 0 ? "(".number_format(-$akun_550317_lalu,0,',','.').")" : number_format($akun_550317_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550317_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550317 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50317) Alat Tulis Kantor & Printing | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550317_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550317 = ($akun_550317_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550317 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550317 < 0 ? "(".number_format(-$saldo_550317,0,',','.').")" : number_format($saldo_550317,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550318_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 97")
						->get()->row_array();

						$akun_550318_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 97")
						->get()->row_array();
						$akun_550318_lalu = $akun_550318_biaya_lalu['total'] + $akun_550318_jurnal['total'];

						$akun_550318_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 97")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550318_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 97")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550318 = array_merge($akun_550318_biaya,$akun_550318_jurnal);
						usort($akun_550318, 'sortByOrder');
						?>
						<button onclick="myFunction53()" class="btn btn-info"><b>(5-50318) Keamanan dan Kebersihan<b></button>
						<div id="myDIV53" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550318_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550318_lalu < 0 ? "(".number_format(-$akun_550318_lalu,0,',','.').")" : number_format($akun_550318_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550318_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550318 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50318) Keamanan dan Kebersihan | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550318_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550318 = ($akun_550318_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550318 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550318 < 0 ? "(".number_format(-$saldo_550318,0,',','.').")" : number_format($saldo_550318,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550321_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 94")
						->get()->row_array();

						$akun_550321_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 94")
						->get()->row_array();
						$akun_550321_lalu = $akun_550321_biaya_lalu['total'] + $akun_550321_jurnal['total'];

						$akun_550321_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 94")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550321_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 94")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550321 = array_merge($akun_550321_biaya,$akun_550321_jurnal);
						usort($akun_550321, 'sortByOrder');
						?>
						<button onclick="myFunction54()" class="btn btn-info"><b>(5-50321) Biaya Lain-Lain<b></button>
						<div id="myDIV54" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550321_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550321_lalu < 0 ? "(".number_format(-$akun_550321_lalu,0,',','.').")" : number_format($akun_550321_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550321_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550321 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50321) Biaya Lain-Lain | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550321_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550321 = ($akun_550321_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550321 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550321 < 0 ? "(".number_format(-$saldo_550321,0,',','.').")" : number_format($saldo_550321,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_550400_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 110")
						->get()->row_array();

						$akun_550400_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 110")
						->get()->row_array();
						$akun_550400_lalu = $akun_550400_biaya_lalu['total'] + $akun_550400_jurnal['total'];

						$akun_550400_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 110")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_550400_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 110")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_550400 = array_merge($akun_550400_biaya,$akun_550400_jurnal);
						usort($akun_550400, 'sortByOrder');
						?>
						<button onclick="myFunction55()" class="btn btn-info"><b>(5-50400) Biaya Diskonto Bank<b></button>
						<div id="myDIV55" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_550400_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_550400_lalu < 0 ? "(".number_format(-$akun_550400_lalu,0,',','.').")" : number_format($akun_550400_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_550400_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_550400 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(5-50400) Biaya Diskonto Bank | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_550400_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_550400 = ($akun_550400_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_550400 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_550400 < 0 ? "(".number_format(-$saldo_550400,0,',','.').")" : number_format($saldo_550400,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
					<br />
					<div>
						<?php
						$akun_660200_biaya_lalu = $this->db->select('sum(pdb.jumlah) as total')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdb.akun = 131")
						->get()->row_array();

						$akun_660200_jurnal = $this->db->select('sum(pdj.debit) as total')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date3' and '$date4'")
						->where("pdj.akun = 131")
						->get()->row_array();
						$akun_660200_lalu = $akun_660200_biaya_lalu['total'] + $akun_660200_jurnal['total'];

						$akun_660200_biaya = $this->db->select('b.*, pdb.deskripsi, pdb.jumlah as debit')
						->from('pmm_biaya b')
						->join('pmm_detail_biaya pdb', 'b.id = pdb.biaya_id','left')
						->where("b.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdb.akun = 131")
						->group_by('pdb.id')
						->order_by('b.tanggal_transaksi','asc')
						->order_by('b.created_on','asc')
						->get()->result_array();

						$akun_660200_jurnal = $this->db->select('j.*,pdj.deskripsi, pdj.debit, pdj.kredit')
						->from('pmm_jurnal_umum j')
						->join('pmm_detail_jurnal pdj','j.id = pdj.jurnal_id','left')
						->where("j.tanggal_transaksi between '$date1' and '$date2'")
						->where("pdj.akun = 131")
						->group_by('pdj.id')
						->order_by('j.tanggal_transaksi','asc')
						->order_by('j.created_on','asc')
						->get()->result_array();

						$akun_660200 = array_merge($akun_660200_biaya,$akun_660200_jurnal);
						usort($akun_660200, 'sortByOrder');
						?>
						<button onclick="myFunction100()" class="btn btn-info"><b>(6-60200) Biaya Persiapan<b></button>
						<div id="myDIV100" style="display:none;">
							<table width="100% "border="1">
								<tr>
									<th class="text-left" colspan="6" width="90%">Saldo Awal</th>
									<?php
									$styleColor = $akun_660200_lalu < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_660200_lalu < 0 ? "(".number_format(-$akun_660200_lalu,0,',','.').")" : number_format($akun_660200_lalu,0,',','.');?></th>
								</tr>
							</table>
							<?php
							$saldo = $akun_660200_lalu;
							$total_debit = 0;
							$total_kredit = 0;
							foreach ($akun_660200 as $x): 
							if ($x['debit']==0) { $saldo = $saldo + $x['debit'] - $x['kredit'] ;} else
							{$saldo = $saldo + $x['debit'];}

							$total_debit += $x['debit'];
							$total_kredit += $x['kredit'];
							$total_saldo = $total_debit - $total_kredit;
							 
							?>
							<table width="100% "border="1">
								<tr>
									<th width="10%" class="text-left"><?php echo $x['tanggal_transaksi'];?></th>
									<th width="10%" class="text-left"><?php echo $x['transaksi'];?></th>
									<th width="20%" class="text-left"><?php echo $x['nomor_transaksi'];?></th>
									<th width="30%" class="text-left"><?php echo $x['deskripsi'];?></th>
									<th width="10%" class="text-right"><?php echo number_format($x['debit'],0,',','.');?></th>
									<th width="10%" class="text-right"><?php echo number_format(0,0,',','.');?></th>
									<?php
									$styleColor = $saldo < 0 ? 'color:red' : 'color:black';
									?>
									<th width="10%" class="text-right" style="<?php echo $styleColor ?>"><?php echo $saldo < 0 ? "(".number_format(-$saldo,0,',','.').")" : number_format($saldo,0,',','.');?></th>
								</tr>
							</table>
							<?php endforeach; ?>
						</div>
						<div>
							<table width="100% "border="0">
								<tr>
									<th class="text-right" width="70%">(6-60200) Biaya Persiapan | Saldo Akhir</th>
									<th class="text-right" width="10%"><?php echo number_format($akun_660200_lalu + $total_debit,0,',','.');?></th>
									<th class="text-right" width="10%"><?php echo number_format($total_kredit,0,',','.');?></th>
									<?php
									$saldo_660200 = ($akun_660200_lalu + $total_debit) - $total_kredit;
									$styleColor = $saldo_660200 < 0 ? 'color:red' : 'color:black';
									?>
									<th class="text-right" width="10%" style="<?php echo $styleColor ?>"><?php echo $saldo_660200 < 0 ? "(".number_format(-$saldo_660200,0,',','.').")" : number_format($saldo_660200,0,',','.');?></th>
								</tr>
							</table>
						</div>
					</div>
				</th>
			</tr>
	    </table>
		<?php
	}

	public function neraca($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';
		$date3 = '';
		$date4 = '';


		if(count($arr_filter_date) == 2){
			$date_now = date('Y-m-d',strtotime($arr_filter_date[0]));
			$date_now = date('Y-m-d', strtotime('-1 days -0 months ', strtotime($date_now)));
			$date4 	= date('Y-m-d',strtotime($date_now));
			$date_now2 = date('2023-01-01');
			$date3 	= date('Y-m-d',strtotime($date_now2));
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));
			$filter_date_2 = date('Y-m-d',strtotime($date3)).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
		 <style type="text/css">
			body {
				font-family: helvetica;
				font-size: 11px;
			}

			table tr.table-active{
				background: linear-gradient(90deg, #fdcd3b 20%, #fdcd3b 40%, #e69500 80%);
				font-size: 11px;
				font-weight: bold;
			}
				
			table tr.table-active2{
				background-color: #e69500;
				font-size: 11px;
				font-weight: bold;
				color: white;
			}
				
			table tr.table-active3{
				font-size: 11px;
			}
				
			table tr.table-active4{
				background: linear-gradient(90deg, #eeeeee 5%, #cccccc 50%, #cccccc 100%);
				font-weight: bold;
				font-size: 11px;
				color: black;
			}
		 </style>
	        <tr class="table-active2">
	            <th colspan="2">PERIODE</th>
				<th class="text-center"><?php echo $filter_date = $filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));?></th>
	        </tr>

			<tr class="table-active">
	            <th width="100%" class="text-left" colspan="3">ASET</th>
	        </tr>
			<tr class="table-active4">
	            <th width="100%" class="text-left" colspan="3">&nbsp;&nbsp;ASET LANCAR</th>
	        </tr>
				<?php
				$akun_110002 = $this->pmm_model->get110002($date1,$date2);
				$akun_110001 = $this->pmm_model->get110001($date1,$date2);
				$akun_110001 = $akun_110002 - $akun_110001;
				?>
			<tr class="table-active3">
	            <th width="10%" class="text-center">1-10001</th>
				<th class="text-left">Kas</th>
				<!--<th class="text-right"><a target="_blank" href="<?= base_url("pmm/reports/detail_transaction/".$date1."/".$date2."/".'1'."") ?>"><?php echo $akun_1_10001 < 0 ? "(".number_format(-$akun_1_10001,0,',','.').")" : number_format($akun_1_10001,0,',','.');?></a></th>-->
				<?php
				$styleColor = $akun_110001 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110001 < 0 ? "(".number_format(-$akun_110001,0,',','.').")" : number_format($akun_110001,0,',','.');?></th>
			</tr>
				<?php
				$akun_110100 = $this->pmm_model->get110100($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10100</th>
				<th class="text-left">Piutang Usaha</th>
				<?php
				$styleColor = $akun_110100 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110100 < 0 ? "(".number_format(-$akun_110100,0,',','.').")" : number_format($akun_110100,0,',','.');?></th>
	        </tr>
				<?php
				$akun_110101 = $this->pmm_model->get110101($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10101</th>
				<th class="text-left">Piutang Belum Ditagih</th>
				<?php
				$styleColor = $akun_110101 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110101 < 0 ? "(".number_format(-$akun_110101,0,',','.').")" : number_format($akun_110101,0,',','.');?></th>
	        </tr>
				<?php
				$akun_110201 = $this->pmm_model->get110201($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10201</th>
				<th class="text-left">Persediaan Bahan Baku</th>
				<?php
				$styleColor = $akun_110201 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110201 < 0 ? "(".number_format(-$akun_110201,0,',','.').")" : number_format($akun_110201,0,',','.');?></th>
	        </tr>
				<?php
				$akun_110403 = $this->pmm_model->get110403($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10403</th>
				<th class="text-left">Uang Muka</th>
				<?php
				$styleColor = $akun_110403 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110403 < 0 ? "(".number_format(-$akun_110403,0,',','.').")" : number_format($akun_110403,0,',','.');?></th>
	        </tr>
				<?php
				$akun_110500 = $this->pmm_model->get110500($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10500</th>
				<th class="text-left">PPN Masukan</th>
				<?php
				$styleColor = $akun_110500 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110500 < 0 ? "(".number_format(-$akun_110500,0,',','.').")" : number_format($akun_110500,0,',','.');?></th>
	        </tr>
				<?php
				$total_aset_lancar = $akun_110001 + $akun_110100 + $akun_110101 + $akun_110201 + $akun_110403 + $akun_110500;
				$styleColor = $total_aset_lancar < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL ASET LANCAR</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_aset_lancar < 0 ? "(".number_format(-$total_aset_lancar,0,',','.').")" : number_format($total_aset_lancar,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
	            <th width="100%" class="text-left" colspan="3">&nbsp;&nbsp;ASET TETAP</th>
	        </tr>
				<?php
				$akun_110703 = $this->pmm_model->get110703($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10703</th>
				<th class="text-left">Aset Tetap - Kendaraan</th>
				<?php
				$styleColor = $akun_110703 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110703 < 0 ? "(".number_format(-$akun_110703,0,',','.').")" : number_format($akun_110703,0,',','.');?></th>
	        </tr>
				<?php
				$akun_110704 = $this->pmm_model->get110704($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10704</th>
				<th class="text-left">Aset Tetap - Peralatan</th>
				<?php
				$styleColor = $akun_110704 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110704 < 0 ? "(".number_format(-$akun_110704,0,',','.').")" : number_format($akun_110704,0,',','.');?></th>
	        </tr>
				<?php
				$akun_110705 = $this->pmm_model->get110705($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10705</th>
				<th class="text-left">Aset Tetap - Perlengkapan Kantor Inventaris</th>
				<?php
				$styleColor = $akun_110705 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110705 < 0 ? "(".number_format(-$akun_110705,0,',','.').")" : number_format($akun_110705,0,',','.');?></th>
	        </tr>
				<?php
				$total_aset_tetap = $akun_110703 + $akun_110704 + $akun_110705;
				$styleColor = $total_aset_tetap < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL ASET TETAP</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_aset_tetap < 0 ? "(".number_format(-$total_aset_tetap,0,',','.').")" : number_format($total_aset_tetap,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
	            <th width="100%" class="text-left" colspan="3">&nbsp;&nbsp;DEPRESIASI & AMORTISASI</th>
	        </tr>
				<?php
				$akun_110753 = $this->pmm_model->get110753($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10753</th>
				<th class="text-left">Akumulasi Penyusutan - Kendaraan</th>
				<?php
				$styleColor = $akun_110753 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110753 < 0 ? "(".number_format(-$akun_110753,0,',','.').")" : number_format($akun_110753,0,',','.');?></th>
	        </tr>
				<?php
				$akun_110754 = $this->pmm_model->get110754($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10754</th>
				<th class="text-left">Akumulasi Penyusutan - Peralatan</th>
				<?php
				$styleColor = $akun_110754 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110754 < 0 ? "(".number_format(-$akun_110754,0,',','.').")" : number_format($akun_110754,0,',','.');?></th>
	        </tr>
				<?php
				$akun_110755 = $this->pmm_model->get110755($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">1-10755</th>
				<th class="text-left">Akumulasi Penyusutan - Peralatan Kantor Inventaris</th>
				<?php
				$styleColor = $akun_110755 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_110755 < 0 ? "(".number_format(-$akun_110755,0,',','.').")" : number_format($akun_110755,0,',','.');?></th>
	        </tr>
				<?php
				$total_depresiasi_amortisasi = $akun_110753 + $akun_110755 + $akun_110755;
				$styleColor = $total_depresiasi_amortisasi < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL DEPRESIASI & AMORTISASI</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_depresiasi_amortisasi < 0 ? "(".number_format(-$total_depresiasi_amortisasi,0,',','.').")" : number_format($total_depresiasi_amortisasi,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
	            <th width="100%" class="text-left" colspan="3">&nbsp;&nbsp;LAIN-LAIN</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL ASET LAIN-LAIN</th>
				<th class="text-right"><?php echo number_format(0,0,',','.');?></th>
	        </tr>
				<?php
				$total_aset = $total_aset_lancar + $total_aset_tetap + $total_depresiasi_amortisasi;
				$styleColor = $total_aset < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL ASET</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_aset < 0 ? "(".number_format(-$total_aset,0,',','.').")" : number_format($total_aset,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
	            <th width="100%" class="text-left" colspan="3">&nbsp;&nbsp;LIABILITAS & MODAL</th>
	        </tr>
			<tr class="table-active4">
	            <th width="100%" class="text-left" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;LIABILITAS JANGKA PENDEK</th>
	        </tr>
				<?php
				$akun_220100 = $this->pmm_model->get220100($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">2-20100</th>
				<th class="text-left">Hutang Usaha</th>
				<?php
				$styleColor = $akun_220100 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220100 < 0 ? "(".number_format(-$akun_220100,0,',','.').")" : number_format($akun_220100,0,',','.');?></th>
	        </tr>
				<?php
				$akun_220101 = $this->pmm_model->get220101($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">2-20101</th>
				<th class="text-left">Hutang Belum Ditagih</th>
				<?php
				$styleColor = $akun_220101 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220101 < 0 ? "(".number_format(-$akun_220101,0,',','.').")" : number_format($akun_220101,0,',','.');?></th>
	        </tr>
				<?php
				$akun_220200 = $this->pmm_model->get220200($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">2-20200</th>
				<th class="text-left">Hutang Lain Lain</th>
				<?php
				$styleColor = $akun_220200 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220200 < 0 ? "(".number_format(-$akun_220200,0,',','.').")" : number_format($akun_220200,0,',','.');?></th>
	        </tr>
				<?php
				$akun_220201 = $this->pmm_model->get220201($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">2-20201</th>
				<th class="text-left">Hutang Biaya</th>
				<?php
				$styleColor = $akun_220201 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220201 < 0 ? "(".number_format(-$akun_220201,0,',','.').")" : number_format($akun_220201,0,',','.');?></th>
	        </tr>
				<?php
				$akun_220205 = $this->pmm_model->get110002($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">2-20205</th>
				<th class="text-left">Hutang Modal</th>
				<?php
				$styleColor = $akun_220205 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220205 < 0 ? "(".number_format(-$akun_220205,0,',','.').")" : number_format($akun_220205,0,',','.');?></th>
	        </tr>

				<?php
				$akun_220500 = $this->pmm_model->get220505($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">2-20505</th>
				<th class="text-left">PPN Keluaran</th>
				<?php
				$styleColor = $akun_220500 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_220500 < 0 ? "(".number_format(-$akun_220500,0,',','.').")" : number_format($akun_220500,0,',','.');?></th>
	        </tr>
				<?php
				$total_liabilitas_jangka_pendek = $akun_220100 + $akun_220101 + $akun_220200 + $akun_220201 + $akun_220205 + $akun_220500;
				$styleColor = $total_liabilitas_jangka_pendek < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL LIABILITAS JANGKA PENDEK</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_liabilitas_jangka_pendek < 0 ? "(".number_format(-$total_liabilitas_jangka_pendek,0,',','.').")" : number_format($total_liabilitas_jangka_pendek,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
	            <th width="100%" class="text-left" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;LIABILITAS JANGKA PANJANG</th>
	        </tr>
				<?php
				$total_liabilitas = $total_liabilitas_jangka_pendek;
				$styleColor = $total_liabilitas < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL LIABILITAS</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_liabilitas < 0 ? "(".number_format(-$total_liabilitas,0,',','.').")" : number_format($total_liabilitas,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
	            <th width="100%" class="text-left" colspan="3">&nbsp;&nbsp;MODAL PEMILIK</th>
	        </tr>
				<?php
				$akun_330000 = $this->pmm_model->get330000($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">3-30000</th>
				<th class="text-left">Modal Saham</th>
				<?php
				$styleColor = $akun_330000 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_330000 < 0 ? "(".number_format(-$akun_330000,0,',','.').")" : number_format($akun_330000,0,',','.');?></th>
	        </tr>
				<?php
				$akun_330999 = $this->pmm_model->get330999($date1,$date2);
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center">3-30999</th>
				<th class="text-left">Ekuitas Saldo Awal</th>
				<?php
				$styleColor = $akun_330999 < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $akun_330999 < 0 ? "(".number_format(-$akun_330999,0,',','.').")" : number_format($akun_330999,0,',','.');?></th>
	        </tr>
				<?php
				$pendapatan_tahun_lalu = $this->pmm_model->getpendapatantahunlalu($date1,$date2);
				$styleColor = $pendapatan_tahun_lalu < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center"></th>
				<th class="text-left">Pendapatan sampai Tahun Lalu</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $pendapatan_tahun_lalu < 0 ? "(".number_format(-$pendapatan_tahun_lalu,0,',','.').")" : number_format($pendapatan_tahun_lalu,0,',','.');?></th>
	        </tr>
				<?php
				$pendapatan_periode_ini = $this->pmm_model->getpendapatanperiodeini($date3,$date2);
				$styleColor = $pendapatan_periode_ini < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
				<th width="10%" class="text-center"></th>
				<th class="text-left">Pendapatan Periode Ini</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $pendapatan_periode_ini < 0 ? "(".number_format(-$pendapatan_periode_ini,0,',','.').")" : number_format($pendapatan_periode_ini,0,',','.');?></th>
	        </tr>
				<?php
				$total_modal_pemilik = $akun_330000 + $akun_330999 + $pendapatan_tahun_lalu + $pendapatan_periode_ini;
				$styleColor = $total_modal_pemilik < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL MODAL PEMILIK</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_modal_pemilik < 0 ? "(".number_format(-$total_modal_pemilik,0,',','.').")" : number_format($total_modal_pemilik,0,',','.');?></th>
	        </tr>
			<?php
				$total_liabilitas_modal = $total_liabilitas + $total_modal_pemilik;
				$styleColor = $total_modal_pemilik < 0 ? 'color:red' : 'color:black';
				?>
			<tr class="table-active3">
	            <th class="text-right" colspan="2">TOTAL LIABILITAS & MODAL</th>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_liabilitas_modal < 0 ? "(".number_format(-$total_liabilitas_modal,0,',','.').")" : number_format($total_liabilitas_modal,0,',','.');?></th>
	        </tr>
	    </table>
		<?php
	}

	public function detail_transaction($date1,$date2,$id)
    {
        $check = $this->m_admin->check_login();
        if($check == true){

			$this->db->select('t.*, pb.nomor_transaksi as trx_biaya, pj.nomor_transaksi as trx_jurnal, tu.nomor_transaksi as trx_terima, tr.nomor_transaksi as trx_transfer, sum(t.debit) as debit, sum(t.kredit) as kredit');
			$this->db->join('pmm_biaya pb','t.biaya_id = pb.id','left');
			$this->db->join('pmm_jurnal_umum pj','t.jurnal_id = pj.id','left');
			$this->db->join('pmm_terima_uang tu','t.terima_id = tu.id','left');
			$this->db->join('pmm_transfer tr','t.transfer_id = tr.id','left');
			$this->db->where('t.tanggal_transaksi >=',$date1);
            $this->db->where('t.tanggal_transaksi <=',$date2);
            $this->db->where('t.akun',$id);
			$this->db->group_by('t.id');
			$this->db->order_by('t.tanggal_transaksi','asc');
			$this->db->order_by('t.id','asc');
            $query = $this->db->get('transactions t');
            $data['row'] = $query->result_array();
            $this->load->view('laporan_keuangan/detail_transaction',$data);
            
        }else {
            redirect('admin');
        }
    }

	public function detail_transaction2($date1,$date2,$id)
    {
        $check = $this->m_admin->check_login();
        if($check == true){

			$this->db->select('t.*, pb.nomor_transaksi as trx_biaya, pj.nomor_transaksi as trx_jurnal, tu.nomor_transaksi as trx_terima, tr.nomor_transaksi as trx_transfer, sum(t.debit) as debit, sum(t.kredit) as kredit');
			$this->db->join('pmm_biaya pb','t.biaya_id = pb.id','left');
			$this->db->join('pmm_jurnal_umum pj','t.jurnal_id = pj.id','left');
			$this->db->join('pmm_terima_uang tu','t.terima_id = tu.id','left');
			$this->db->join('pmm_transfer tr','t.transfer_id = tr.id','left');
			$this->db->where('t.tanggal_transaksi >=',$date1);
            $this->db->where('t.tanggal_transaksi <=',$date2);
            $this->db->where('t.akun',$id);
			$this->db->group_by('t.id');
			$this->db->order_by('t.tanggal_transaksi','asc');
			$this->db->order_by('t.id','asc');
            $query = $this->db->get('transactions t');
            $data['row'] = $query->result_array();

			$this->db->select('t.*, sum(t.debit) as debit, sum(t.kredit) as kredit');
			$this->db->where('t.tanggal_transaksi >=',$date1);
            $this->db->where('t.tanggal_transaksi <=',$date2);
            $this->db->where('t.akun',1);
			$this->db->group_by('t.id');
			$this->db->order_by('t.tanggal_transaksi','asc');
			$this->db->order_by('t.id','asc');
            $query = $this->db->get('transactions t');
            $data['row2'] = $query->result_array();
            $this->load->view('laporan_keuangan/detail_transaction2',$data);
            
        }else {
            redirect('admin');
        }
    }

	public function laporan_evaluasi_biaya_produksi($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date3 = '';
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date3 	= date('2023-08-01',strtotime($date3));
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));
			$filter_date_2 = date('Y-m-d',strtotime($date3)).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
		 <style type="text/css">
			body {
				font-family: helvetica;
				font-size: 11px;
			}

			table tr.table-active{
				background-color: #e69500;
				font-size: 11px;
				font-weight: bold;
				color: white;
			}
				
			table tr.table-active2{
				background-color: #e69500;
				font-size: 11px;
				font-weight: bold;
				color: white;
			}
				
			table tr.table-active3{
				font-size: 11px;
			}
				
			table tr.table-active4{
				background-color: #eeeeee;
				font-weight: bold;
				font-size: 11px;
				color: black;
			}
		 </style>
			<?php
			$rap_bahan = $this->db->select('*')
			->from('pmm_agregat ')
			->where("date_agregat <= '$date2'")
			->where('status','PUBLISH')
			->group_by("id")
			->order_by('date_agregat','desc')->limit(1)
			->get()->result_array();

			foreach ($rap_bahan as $x){
				$harsat_bahan = $x['price_a'];
			}

			$pemakaian_boulder = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 22")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$date1_ago = date('2020-01-01');
			$date2_ago = date('Y-m-d', strtotime('-1 days', strtotime($date1)));
			$date3_ago = date('Y-m-d', strtotime('-1 months', strtotime($date1)));
			$tanggal_opening_balance = date('Y-m-d', strtotime('-1 days', strtotime($date1)));

			$stock_opname_boulder_ago = $this->db->select('cat.volume as volume, cat.total as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date <= '$tanggal_opening_balance')")
			->where("cat.material_id = 22")
			->where("cat.status = 'PUBLISH'")
			->order_by('date','desc')->limit(1)
			->get()->row_array();

			$stok_volume_boulder_lalu = $stock_opname_boulder_ago['volume'];
			$stok_nilai_boulder_lalu = $stock_opname_boulder_ago['nilai'];
			$stok_harsat_boulder_lalu = (round($stok_volume_boulder_lalu,2)!=0)?$stok_nilai_boulder_lalu / round($stok_volume_boulder_lalu,2) * 1:0;

			$pembelian_boulder = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 7")
			->get()->row_array();
		
			$pembelian_volume_boulder = $pembelian_boulder['volume'];
			$pembelian_nilai_boulder = $pembelian_boulder['nilai'];
			$pembelian_harga_boulder = (round($pembelian_volume_boulder,2)!=0)?$pembelian_nilai_boulder / round($pembelian_volume_boulder,2) * 1:0;

			$total_stok_volume_boulder = $stok_volume_boulder_lalu + $pembelian_volume_boulder;
			$total_stok_nilai_boulder = $stok_nilai_boulder_lalu + $pembelian_nilai_boulder;

			$stock_opname_boulder_now = $this->db->select('cat.volume as volume, cat.total as nilai, cat.pemakaian_custom, cat.reset, cat.reset_pemakaian')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date <= '$date2')")
			->where("cat.material_id = 22")
			->where("cat.status = 'PUBLISH'")
			->order_by('date','desc')->limit(1)
			->get()->row_array();

			$volume_stock_opname_boulder_now = $stock_opname_boulder_now['volume'];
			$nilai_stock_opname_boulder_now = $stock_opname_boulder_now['nilai'];

			$vol_pemakaian_boulder_now = ($stok_volume_boulder_lalu + $pembelian_volume_boulder) - $volume_stock_opname_boulder_now;
			$nilai_pemakaian_boulder_now = $stock_opname_boulder_now['nilai'];

			$pemakaian_volume_boulder = $vol_pemakaian_boulder_now;
			$pemakaian_nilai_boulder = (($total_stok_nilai_boulder - $nilai_stock_opname_boulder_now) * $stock_opname_boulder_now['reset']) + ($stock_opname_boulder_now['pemakaian_custom'] * $stock_opname_boulder_now['reset_pemakaian']);
			$pemakaian_harsat_boulder = $pemakaian_nilai_boulder / $pemakaian_volume_boulder;

			$total_volume_boulder_rap = $pemakaian_volume_boulder;
			$total_nilai_boulder_rap = $pemakaian_volume_boulder * $harsat_bahan;
			$total_harsat_boulder_rap = ($total_volume_boulder_rap!=0)?$total_nilai_boulder_rap / $total_volume_boulder_rap * 1:0;

			$total_volume_boulder_realisasi = $pemakaian_volume_boulder;
			$total_nilai_boulder_realisasi = $pemakaian_nilai_boulder;
			$total_harsat_boulder_realisasi = $pemakaian_harsat_boulder;

			$total_volume_boulder_evaluasi = $total_volume_boulder_rap - $total_volume_boulder_realisasi;
			$total_nilai_boulder_evaluasi = $total_nilai_boulder_rap - $total_nilai_boulder_realisasi;
	        ?>

			<?php
			$stone_crusher_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (225,226,228,229)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$stone_crusher_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (225,226,228,229)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_stone_crusher = $stone_crusher_biaya['total'] + $stone_crusher_jurnal['total'];

			$wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (125,139)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (125,139)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_wheel_loader = $wheel_loader_biaya['total'] + $wheel_loader_jurnal['total'];

			$maintenance_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227,140,230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$maintenance_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227,140,230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_maintenance = $maintenance_biaya['total'] + $maintenance_jurnal['total'];

			$tangki_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (231,232)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$tangki_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (231,232)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_tangki = $tangki_biaya['total'] + $tangki_jurnal['total'];

			$timbangan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (233,234)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$timbangan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (233,234)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_timbangan = $timbangan_biaya['total'] + $timbangan_jurnal['total'];

			$date1_ago = date('2020-01-01');
			$date2_ago = date('Y-m-d', strtotime('-1 days', strtotime($date1)));
			$date3_ago = date('Y-m-d', strtotime('-1 months', strtotime($date1)));
			$tanggal_opening_balance = date('Y-m-d', strtotime('-1 days', strtotime($date1)));

			$stock_opname_solar_ago = $this->db->select('cat.volume as volume, cat.total as nilai')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date <= '$tanggal_opening_balance')")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->order_by('date','desc')->limit(1)
			->get()->row_array();

			$stok_volume_solar_lalu = $stock_opname_solar_ago['volume'];
			$stok_nilai_solar_lalu = $stock_opname_solar_ago['nilai'];
			$stok_harsat_solar_lalu = (round($stok_volume_solar_lalu,2)!=0)?$stok_nilai_solar_lalu / round($stok_volume_solar_lalu,2) * 1:0;

			$pembelian_solar = $this->db->select('prm.display_measure as satuan, SUM(prm.display_volume) as volume, (prm.display_price / prm.display_volume) as harga, SUM(prm.display_price) as nilai')
			->from('pmm_receipt_material prm')
			->join('pmm_purchase_order po', 'prm.purchase_order_id = po.id','left')
			->join('produk p', 'prm.material_id = p.id','left')
			->where("prm.date_receipt between '$date1' and '$date2'")
			->where("p.kategori_bahan = 5")
			->get()->row_array();
		
			$pembelian_volume_solar = $pembelian_solar['volume'];
			$pembelian_nilai_solar = $pembelian_solar['nilai'];
			$pembelian_harga_solar = (round($pembelian_volume_solar,2)!=0)?$pembelian_nilai_solar / round($pembelian_volume_solar,2) * 1:0;

			$total_stok_volume_solar = $stok_volume_solar_lalu + $pembelian_volume_solar;
			$total_stok_nilai_solar = $stok_nilai_solar_lalu + $pembelian_nilai_solar;

			$stock_opname_solar_now = $this->db->select('cat.volume as volume, cat.total as nilai, cat.pemakaian_custom, cat.reset, cat.reset_pemakaian')
			->from('pmm_remaining_materials_cat cat ')
			->where("(cat.date <= '$date2')")
			->where("cat.material_id = 5")
			->where("cat.status = 'PUBLISH'")
			->order_by('date','desc')->limit(1)
			->get()->row_array();

			$volume_stock_opname_solar_now = $stock_opname_solar_now['volume'];
			$nilai_stock_opname_solar_now = $stock_opname_solar_now['nilai'];

			$vol_pemakaian_solar_now = ($stok_volume_solar_lalu + $pembelian_volume_solar) - $volume_stock_opname_solar_now;
			$nilai_pemakaian_solar_now = $stock_opname_solar_now['nilai'];

			$pemakaian_volume_solar = $vol_pemakaian_solar_now;
			$pemakaian_nilai_solar = (($total_stok_nilai_solar - $nilai_stock_opname_solar_now) * $stock_opname_solar_now['reset']) + ($stock_opname_solar_now['pemakaian_custom'] * $stock_opname_solar_now['reset_pemakaian']);
			$pemakaian_harsat_solar = $pemakaian_nilai_solar / $pemakaian_volume_solar;

			$penjualan = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();
			
			$total_volume = 0;
			foreach ($penjualan as $x){
				$total_volume += $x['volume'];
			}

			$rap_alat = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where("rap.tanggal_rap_alat <= '$date2'")
			->where('rap.status','PUBLISH')
			->group_by("rap.id")
			->order_by('rap.tanggal_rap_alat','desc')->limit(1)
			->get()->result_array();

			foreach ($rap_alat as $x){
				$harsat_stone_crusher = $x['harsat_stone_crusher'];
				$harsat_wheel_loader = $x['wheel_loader'];
				$harsat_maintenance = $x['harsat_maintenance'];
				$harsat_bbm_solar = $x['harsat_bbm_solar'];
			}

			$vol_stone_crusher = $total_volume;
			$vol_wheel_loader = $total_volume;
			$vol_maintenance = $total_volume;
			$vol_bbm_solar = $total_volume;
			$vol_tangki = $total_volume;
			$vol_timbangan = $total_volume;

			$stone_crusher = $total_volume * $harsat_stone_crusher;
			$wheel_loader = $total_volume * $harsat_wheel_loader;
			$maintenance = $total_volume * $harsat_maintenance;
			$bbm_solar = $total_volume * $harsat_bbm_solar;
			$tangki = 0;
			$timbangan = 0;

			$pemakaian_vol_stone_crusher = 0;
			$pemakaian_vol_wheel_loadert = 0;
			$pemakaian_vol_maintenance = 0;
			$pemakaian_vol_bbm_solar = $pemakaian_volume_solar;
			
			$total_pemakaian_stone_crusher = $total_nilai_stone_crusher;
			$total_pemakaian_wheel_loader = $total_nilai_wheel_loader;
			$total_pemakaian_maintenance = $total_nilai_maintenance;
			$total_pemakaian_solar = $pemakaian_nilai_solar;
			$total_pemakaian_tangki = $total_nilai_tangki;
			$total_pemakaian_timbangan = $total_nilai_timbangan;
			
			$total_nilai_evaluasi_stone_crusher = ($total_pemakaian_stone_crusher!=0)?$stone_crusher - $total_pemakaian_stone_crusher * 1:0;
			$total_nilai_evaluasi_wheel_loader = ($total_pemakaian_wheel_loader!=0)?$wheel_loader - $total_pemakaian_wheel_loader * 1:0;
			$total_nilai_evaluasi_maintenance = ($total_pemakaian_maintenance!=0)?$maintenance - $total_pemakaian_maintenance * 1:0;
			$total_vol_evaluasi_bbm_solar = ($pemakaian_volume_solar!=0)?$vol_bbm_solar - $pemakaian_vol_bbm_solar * 1:0;
			$total_nilai_evaluasi_bbm_solar = ($pemakaian_nilai_solar!=0)?$bbm_solar - $pemakaian_nilai_solar * 1:0;
			$total_nilai_evaluasi_tangki = ($total_pemakaian_tangki!=0)?$tangki - $total_pemakaian_tangki * 1:0;
			$total_nilai_evaluasi_timbangan = ($total_pemakaian_timbangan!=0)?$timbangan - $total_pemakaian_timbangan * 1:0;

			$total_nilai_rap_alat = $stone_crusher + $wheel_loader + $maintenance + $bbm_solar;
			$total_nilai_realisasi_alat = $total_pemakaian_stone_crusher + $total_pemakaian_wheel_loader + $total_pemakaian_maintenance + $pemakaian_nilai_solar + $pemakaian_nilai_tangki + $pemakaian_nilai_timbangan;
			$total_nilai_evaluasi_alat = $total_nilai_evaluasi_stone_crusher + $total_nilai_evaluasi_wheel_loader + $total_nilai_evaluasi_maintenance + $total_nilai_evaluasi_bbm_solar + $total_nilai_evaluasi_tangki + $total_nilai_evaluasi_timbangan;
			?>

			<?php
			$rap_bua = $this->db->select('sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("rap.tanggal_rap_bua < '$date2'")
			->group_by("rap.id")
			->order_by('rap.tanggal_rap_bua','desc')->limit(1)
			->get()->row_array();
			$rap_bua = $rap_bua['total'];
			
			//REALISASI
			$konsumsi_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 116")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$konsumsi_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 116")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$konsumsi = $konsumsi_biaya['total'] + $konsumsi_jurnal['total'];

			$listrik_internet_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 118")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$listrik_internet_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 118")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$listrik_internet = $listrik_internet_biaya['total'] + $listrik_internet_jurnal['total'];

			$gaji_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('114','115')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$gaji_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('114','115')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$gaji = $gaji_biaya['total'] + $gaji_jurnal['total'];

			$akomodasi_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 143")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$akomodasi_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 143")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$akomodasi = $akomodasi_biaya['total'] + $akomodasi_jurnal['total'];

			$biaya_maintenance_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 141")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_maintenance_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 141")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$biaya_maintenance = $biaya_maintenance_biaya['total'] + $biaya_maintenance_jurnal['total'];

			$thr_bonus_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 117")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$thr_bonus_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 117")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$thr_bonus = $thr_bonus_biaya['total'] + $thr_bonus_jurnal['total'];

			$bensin_tol_parkir_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 78")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$bensin_tol_parkir_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 78")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$bensin_tol_parkir = $bensin_tol_parkir_biaya['total'] + $bensin_tol_parkir_jurnal['total'];

			$biaya_kirim_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 180")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_kirim_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 180")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$biaya_kirim = $biaya_kirim_biaya['total'] + $biaya_kirim_jurnal['total'];

			$pakaian_dinas_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 87")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$pakaian_dinas_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 87")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$pakaian_dinas = $pakaian_dinas_biaya['total'] + $pakaian_dinas_jurnal['total'];

			$perjalanan_dinas_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 62")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$perjalanan_dinas_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 62")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$perjalanan_dinas = $perjalanan_dinas_biaya['total'] + $perjalanan_dinas_jurnal['total'];

			$perlengkapan_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 98")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$perlengkapan_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 98")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$perlengkapan_kantor = $perlengkapan_kantor_biaya['total'] + $perlengkapan_kantor_jurnal['total'];

			$alat_tulis_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 96")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$alat_tulis_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 96")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$alat_tulis_kantor = $alat_tulis_kantor_biaya['total'] + $alat_tulis_kantor_jurnal['total'];

			$keamanan_kebersihan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 97")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$keamanan_kebersihan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 97")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$keamanan_kebersihan = $keamanan_kebersihan_biaya['total'] + $keamanan_kebersihan_jurnal['total'];

			$sewa_mess_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 181")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$sewa_mess_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 181")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$sewa_mess = $sewa_mess_biaya['total'] + $sewa_mess_jurnal['total'];

			$biaya_lain_lain_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 94")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_lain_lain_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 94")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$biaya_lain_lain = $biaya_lain_lain_biaya['total'] + $biaya_lain_lain_jurnal['total'];
			
			$biaya_adm_bank_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 182")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_adm_bank_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 182")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$biaya_adm_bank = $biaya_adm_bank_biaya['total'] + $biaya_adm_bank_jurnal['total'];

			$total_nilai_realisasi_bua = $konsumsi + $listrik_internet + $gaji + $akomodasi + $biaya_maintenance + $thr_bonus + $bensin_tol_parkir + $biaya_kirim + $pakaian_dinas + $perjalanan_dinas + $perlengkapan_kantor + $alat_tulis_kantor + $keamanan_kebersihan + $sewa_mess + $biaya_lain_lain + $biaya_adm_bank;

			$total_volume_rap_bua = $total_volume;
			$total_nilai_rap_bua = $rap_bua / 6;
			$total_harsat_rap_bua = $total_nilai_rap_bua / $total_volume_rap_bua;
			
			$total_volume_realisasi_bua = $total_volume;
			$total_nilai_realisasi_bua = $total_nilai_realisasi_bua;
			$total_harsat_realisasi_bua = $total_nilai_realisasi_bua / $total_volume_realisasi_bua;

			$total_volume_evaluasi_bua = $total_volume_rap_bua - $total_volume_realisasi_bua;
			$total_nilai_evaluasi_bua = $total_nilai_rap_bua - $total_nilai_realisasi_bua;
			?>

			<tr class="table-active">
	            <th class="text-center" rowspan="2" style="vertical-align:middle;">NO.</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle;">URAIAN</th>
				<th class="text-center" colspan="3">RAP</th>
				<th class="text-center" colspan="3">REALISASI</th>
				<th class="text-center" colspan="3">DEVIASI</th>
	        </tr>
			<tr class="table-active">
	            <th class="text-center">VOL.</th>
				<th class="text-center">HARSAT</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOL.</th>
				<th class="text-center">HARSAT</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOL.</th>
				<th class="text-center">NILAI</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center">1</th>
				<th class="text-left">BAHAN</th>
				<th class="text-right"><?php echo number_format($total_volume_boulder_rap,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harsat_boulder_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_boulder_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_boulder_realisasi,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harsat_boulder_realisasi,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_boulder_realisasi,0,',','.');?></th>
				<?php
				$styleColor = $total_volume_boulder_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_volume_boulder_evaluasi < 0 ? "(".number_format(-$total_volume_boulder_evaluasi,2,',','.').")" : number_format($total_volume_boulder_evaluasi,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_boulder_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_boulder_evaluasi < 0 ? "(".number_format(-$total_nilai_boulder_evaluasi,0,',','.').")" : number_format($total_nilai_boulder_evaluasi,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center">2</th>
				<th class="text-left">ALAT</th>
				<th class="text-right"><?php echo number_format($total_vol_rap_alat,2,',','.');?></th>
				<?php
				$total_harsat_rap_alat = (round($total_vol_rap_alat,2)!=0)?($total_nilai_rap_alat / $total_vol_rap_alat) * 1:0;
				?>
				<th class="text-right"><?php echo number_format($total_harsat_rap_alat ,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_laporan_evaluasi_alat?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_nilai_rap_alat,0,',','.');?></a></th>
				<th class="text-right"><?php echo number_format($total_vol_realisasi_alat,2,',','.');?></th>
				<?php
				$total_harsat_realisasi_alat = (round($total_vol_realisasi_alat,2)!=0)?($total_nilai_realisasi_alat / $total_vol_realisasi_alat) * 1:0;
				?>
				<th class="text-right"><?php echo number_format($total_harsat_realisasi_alat,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_laporan_evaluasi_alat?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_nilai_realisasi_alat,0,',','.');?></a></th>
				<?php
				$styleColor = $total_vol_evaluasi_alat < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_alat < 0 ? "(".number_format(-$total_vol_evaluasi_alat,2,',','.').")" : number_format($total_vol_evaluasi_alat,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_alat < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi_alat < 0 ? "(".number_format(-$total_nilai_evaluasi_alat,0,',','.').")" : number_format($total_nilai_evaluasi_alat,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center">3</th>
				<th class="text-left">BUA</th>
				<th class="text-right"><?php echo number_format($total_volume_rap_bua,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harsat_rap_bua,0,',','.');?></th>
				<?php
				$rap_bua = $this->db->select('rap.id')
				->from('rap_bua rap')
				->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
				->where("rap.status = 'PUBLISH'")
				->where("rap.tanggal_rap_bua < '$date2'")
				->group_by("rap.id")
				->order_by('rap.tanggal_rap_bua','desc')->limit(1)
				->get()->row_array();
				$rap_bua = $rap_bua['id'];
				?>
				<th class="text-right"><a target="_blank" href="<?= base_url("rap/cetak_rap_bua/".$rap_bua) ?>"><?php echo number_format($total_nilai_rap_bua,0,',','.');?></a></th>
				<th class="text-right"><?php echo number_format($total_volume_realisasi_bua,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harsat_realisasi_bua,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_evaluasi_bua/".$date1."/".$date2."/".$rap_bua."") ?>"><?php echo number_format($total_nilai_realisasi_bua,0,',','.');?></a></th>
				<?php
				$styleColor = $total_volume_evaluasi_bua < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo number_format($total_volume_evaluasi_bua,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_bua < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi_bua < 0 ? "(".number_format(-$total_nilai_evaluasi_bua,0,',','.').")" : number_format($total_nilai_evaluasi_bua,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
				<th class="text-right" colspan="2">TOTAL</th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<?php
				$total_nilai_rap = $total_nilai_komposisi + $total_nilai_rap_alat + $total_nilai_rap_bua;
				?>
				<th class="text-right"><?php echo number_format($total_nilai_rap,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<?php
				$total_nilai_realisasi = $total_nilai_realisasi + $total_nilai_realisasi_alat + $total_nilai_realisasi_bua;
				?>
				<th class="text-right"><?php echo number_format($total_nilai_realisasi,0,',','.');?></th>
				<th class="text-right"></th>
				<?php
				$total_nilai_evaluasi = $total_nilai_evaluasi + $total_nilai_evaluasi_alat + $total_nilai_evaluasi_bua;
				$styleColor = $total_nilai_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi < 0 ? "(".number_format(-$total_nilai_evaluasi,0,',','.').")" : number_format($total_nilai_evaluasi,0,',','.');?></th>
			</tr>
	    </table>
		<?php
	}

	public function laporan_evaluasi_biaya_produksi_pemakaian($arr_date)
	{
		$data = array();
		
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date3 = '';
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date3 	= date('2023-08-01',strtotime($date3));
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('d/m/Y',strtotime($arr_filter_date[0])).' - '.date('d/m/Y',strtotime($arr_filter_date[1]));
			$filter_date_2 = date('Y-m-d',strtotime($date3)).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
		 <style type="text/css">
			body {
				font-family: helvetica;
				font-size: 11px;
			}

			table tr.table-active{
				background-color: #e69500;
				font-size: 11px;
				font-weight: bold;
				color: white;
			}
				
			table tr.table-active2{
				background-color: #e69500;
				font-size: 11px;
				font-weight: bold;
				color: white;
			}
				
			table tr.table-active3{
				font-size: 11px;
			}
				
			table tr.table-active4{
				background-color: #eeeeee;
				font-weight: bold;
				font-size: 11px;
				color: black;
			}
		 </style>
			<?php
			$rap_bahan = $this->db->select('*')
			->from('pmm_agregat ')
			->where("date_agregat <= '$date2'")
			->where('status','PUBLISH')
			->group_by("id")
			->order_by('date_agregat','desc')->limit(1)
			->get()->result_array();

			foreach ($rap_bahan as $x){
				$harsat_bahan = $x['price_a'];
			}

			$pemakaian_boulder = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 22")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_boulder = $pemakaian_boulder['volume'];
			$pemakaian_nilai_boulder = $pemakaian_boulder['nilai'];
			$pemakaian_harsat_boulder = ($pemakaian_volume_boulder!=0)?$pemakaian_nilai_boulder / $pemakaian_volume_boulder * 1:0;

			$total_volume_boulder_rap = $pemakaian_volume_boulder;
			$total_nilai_boulder_rap = $pemakaian_volume_boulder * $harsat_bahan;
			$total_harsat_boulder_rap = ($total_volume_boulder_rap!=0)?$total_nilai_boulder_rap / $total_volume_boulder_rap * 1:0;

			$total_volume_boulder_realisasi = $pemakaian_volume_boulder;
			$total_nilai_boulder_realisasi = $pemakaian_nilai_boulder;
			$total_harsat_boulder_realisasi = $pemakaian_harsat_boulder;

			$total_volume_boulder_evaluasi = $total_volume_boulder_rap - $total_volume_boulder_realisasi;
			$total_nilai_boulder_evaluasi = $total_nilai_boulder_rap - $total_nilai_boulder_realisasi;
	        ?>

			<?php
			$stone_crusher_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (225,226,228,229)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$stone_crusher_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (225,226,228,229)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_stone_crusher = $stone_crusher_biaya['total'] + $stone_crusher_jurnal['total'];

			$wheel_loader_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (125,139)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$wheel_loader_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (125,139)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_wheel_loader = $wheel_loader_biaya['total'] + $wheel_loader_jurnal['total'];

			$maintenance_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227,140,230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$maintenance_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (227,140,230)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_maintenance = $maintenance_biaya['total'] + $maintenance_jurnal['total'];

			$tangki_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (231,232)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$tangki_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (231,232)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_tangki = $tangki_biaya['total'] + $tangki_jurnal['total'];

			$timbangan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (233,234)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
	
			$timbangan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in (233,234)")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$total_nilai_timbangan = $timbangan_biaya['total'] + $timbangan_jurnal['total'];

			$pemakaian_solar = $this->db->select('sum(volume) as volume, sum(nilai) as nilai')
			->from('pemakaian_bahan')
			->where("date between '$date1' and '$date2'")
			->where("material_id = 5")
			->where("status = 'PUBLISH'")
			->get()->row_array();

			$pemakaian_volume_solar = $pemakaian_solar['volume'];
			$pemakaian_nilai_solar = $pemakaian_solar['nilai'];
			$pemakaian_harsat_solar = ($pemakaian_volume_solar!=0)?$pemakaian_nilai_solar / $pemakaian_volume_solar * 1:0;

			$penjualan = $this->db->select('p.nama, pp.client_id, SUM(pp.display_price) as price, SUM(pp.display_volume) as volume, pp.convert_measure as measure')
			->from('pmm_productions pp')
			->join('penerima p', 'pp.client_id = p.id','left')
			->join('pmm_sales_po ppo', 'pp.salesPo_id = ppo.id','left')
			->where("pp.date_production between '$date1' and '$date2'")
			->where("pp.status = 'PUBLISH'")
			->where("ppo.status in ('OPEN','CLOSED')")
			->group_by("pp.client_id")
			->get()->result_array();
			
			$total_volume = 0;
			foreach ($penjualan as $x){
				$total_volume += $x['volume'];
			}

			$rap_alat = $this->db->select('rap.*')
			->from('rap_alat rap')
			->where("rap.tanggal_rap_alat <= '$date2'")
			->where('rap.status','PUBLISH')
			->group_by("rap.id")
			->order_by('rap.tanggal_rap_alat','desc')->limit(1)
			->get()->result_array();

			foreach ($rap_alat as $x){
				$harsat_stone_crusher = $x['harsat_stone_crusher'];
				$harsat_wheel_loader = $x['wheel_loader'];
				$harsat_maintenance = $x['harsat_maintenance'];
				$harsat_bbm_solar = $x['harsat_bbm_solar'];
			}

			$vol_stone_crusher = $total_volume;
			$vol_wheel_loader = $total_volume;
			$vol_maintenance = $total_volume;
			$vol_bbm_solar = $total_volume;
			$vol_tangki = $total_volume;
			$vol_timbangan = $total_volume;

			$stone_crusher = $total_volume * $harsat_stone_crusher;
			$wheel_loader = $total_volume * $harsat_wheel_loader;
			$maintenance = $total_volume * $harsat_maintenance;
			$bbm_solar = $total_volume * $harsat_bbm_solar;
			$tangki = 0;
			$timbangan = 0;

			$pemakaian_vol_stone_crusher = 0;
			$pemakaian_vol_wheel_loadert = 0;
			$pemakaian_vol_maintenance = 0;
			$pemakaian_vol_bbm_solar = $pemakaian_volume_solar;
			
			$total_pemakaian_stone_crusher = $total_nilai_stone_crusher;
			$total_pemakaian_wheel_loader = $total_nilai_wheel_loader;
			$total_pemakaian_maintenance = $total_nilai_maintenance;
			$total_pemakaian_solar = $pemakaian_nilai_solar;
			$total_pemakaian_tangki = $total_nilai_tangki;
			$total_pemakaian_timbangan = $total_nilai_timbangan;
			
			$total_nilai_evaluasi_stone_crusher = ($total_pemakaian_stone_crusher!=0)?$stone_crusher - $total_pemakaian_stone_crusher * 1:0;
			$total_nilai_evaluasi_wheel_loader = ($total_pemakaian_wheel_loader!=0)?$wheel_loader - $total_pemakaian_wheel_loader * 1:0;
			$total_nilai_evaluasi_maintenance = ($total_pemakaian_maintenance!=0)?$maintenance - $total_pemakaian_maintenance * 1:0;
			$total_vol_evaluasi_bbm_solar = ($pemakaian_volume_solar!=0)?$vol_bbm_solar - $pemakaian_vol_bbm_solar * 1:0;
			$total_nilai_evaluasi_bbm_solar = ($pemakaian_nilai_solar!=0)?$bbm_solar - $pemakaian_nilai_solar * 1:0;
			$total_nilai_evaluasi_tangki = ($total_pemakaian_tangki!=0)?$tangki - $total_pemakaian_tangki * 1:0;
			$total_nilai_evaluasi_timbangan = ($total_pemakaian_timbangan!=0)?$timbangan - $total_pemakaian_timbangan * 1:0;

			$total_nilai_rap_alat = $stone_crusher + $wheel_loader + $maintenance + $bbm_solar;
			$total_nilai_realisasi_alat = $total_pemakaian_stone_crusher + $total_pemakaian_wheel_loader + $total_pemakaian_maintenance + $pemakaian_nilai_solar + $pemakaian_nilai_tangki + $pemakaian_nilai_timbangan;
			$total_nilai_evaluasi_alat = $total_nilai_evaluasi_stone_crusher + $total_nilai_evaluasi_wheel_loader + $total_nilai_evaluasi_maintenance + $total_nilai_evaluasi_bbm_solar + $total_nilai_evaluasi_tangki + $total_nilai_evaluasi_timbangan;
			?>

			<?php
			$rap_bua = $this->db->select('sum(det.jumlah) as total')
			->from('rap_bua rap')
			->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
			->where("rap.status = 'PUBLISH'")
			->where("rap.tanggal_rap_bua < '$date2'")
			->group_by("rap.id")
			->order_by('rap.tanggal_rap_bua','desc')->limit(1)
			->get()->row_array();
			$rap_bua = $rap_bua['total'];
			
			//REALISASI
			$konsumsi_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 116")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$konsumsi_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 116")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$konsumsi = $konsumsi_biaya['total'] + $konsumsi_jurnal['total'];

			$listrik_internet_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 118")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$listrik_internet_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 118")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$listrik_internet = $listrik_internet_biaya['total'] + $listrik_internet_jurnal['total'];

			$gaji_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('114','115')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$gaji_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun in ('114','115')")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$gaji = $gaji_biaya['total'] + $gaji_jurnal['total'];

			$akomodasi_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 143")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$akomodasi_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 143")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$akomodasi = $akomodasi_biaya['total'] + $akomodasi_jurnal['total'];

			$biaya_maintenance_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 141")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_maintenance_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 141")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$biaya_maintenance = $biaya_maintenance_biaya['total'] + $biaya_maintenance_jurnal['total'];

			$thr_bonus_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 117")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$thr_bonus_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 117")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$thr_bonus = $thr_bonus_biaya['total'] + $thr_bonus_jurnal['total'];

			$bensin_tol_parkir_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 78")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$bensin_tol_parkir_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 78")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$bensin_tol_parkir = $bensin_tol_parkir_biaya['total'] + $bensin_tol_parkir_jurnal['total'];

			$biaya_kirim_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 180")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_kirim_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 180")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$biaya_kirim = $biaya_kirim_biaya['total'] + $biaya_kirim_jurnal['total'];

			$pakaian_dinas_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 87")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$pakaian_dinas_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 87")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$pakaian_dinas = $pakaian_dinas_biaya['total'] + $pakaian_dinas_jurnal['total'];

			$perjalanan_dinas_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 62")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$perjalanan_dinas_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 62")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$perjalanan_dinas = $perjalanan_dinas_biaya['total'] + $perjalanan_dinas_jurnal['total'];

			$perlengkapan_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 98")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$perlengkapan_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 98")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$perlengkapan_kantor = $perlengkapan_kantor_biaya['total'] + $perlengkapan_kantor_jurnal['total'];

			$alat_tulis_kantor_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 96")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$alat_tulis_kantor_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 96")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$alat_tulis_kantor = $alat_tulis_kantor_biaya['total'] + $alat_tulis_kantor_jurnal['total'];

			$keamanan_kebersihan_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 97")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$keamanan_kebersihan_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 97")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$keamanan_kebersihan = $keamanan_kebersihan_biaya['total'] + $keamanan_kebersihan_jurnal['total'];

			$sewa_mess_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 181")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$sewa_mess_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 181")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$sewa_mess = $sewa_mess_biaya['total'] + $sewa_mess_jurnal['total'];

			$biaya_lain_lain_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 94")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_lain_lain_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 94")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$biaya_lain_lain = $biaya_lain_lain_biaya['total'] + $biaya_lain_lain_jurnal['total'];
			
			$biaya_adm_bank_biaya = $this->db->select('sum(pdb.jumlah) as total')
			->from('pmm_biaya pb ')
			->join('pmm_detail_biaya pdb','pb.id = pdb.biaya_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 182")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();

			$biaya_adm_bank_jurnal = $this->db->select('sum(pdb.debit) as total')
			->from('pmm_jurnal_umum pb ')
			->join('pmm_detail_jurnal pdb','pb.id = pdb.jurnal_id','left')
			->join('pmm_coa c','pdb.akun = c.id','left')
			->where("pdb.akun = 182")
			->where("pb.status = 'PAID'")
			->where("(pb.tanggal_transaksi between '$date1' and '$date2')")
			->get()->row_array();
			$biaya_adm_bank = $biaya_adm_bank_biaya['total'] + $biaya_adm_bank_jurnal['total'];

			$total_nilai_realisasi_bua = $konsumsi + $listrik_internet + $gaji + $akomodasi + $biaya_maintenance + $thr_bonus + $bensin_tol_parkir + $biaya_kirim + $pakaian_dinas + $perjalanan_dinas + $perlengkapan_kantor + $alat_tulis_kantor + $keamanan_kebersihan + $sewa_mess + $biaya_lain_lain + $biaya_adm_bank;

			$total_volume_rap_bua = $total_volume;
			$total_nilai_rap_bua = $rap_bua / 6;
			$total_harsat_rap_bua = $total_nilai_rap_bua / $total_volume_rap_bua;
			
			$total_volume_realisasi_bua = $total_volume;
			$total_nilai_realisasi_bua = $total_nilai_realisasi_bua;
			$total_harsat_realisasi_bua = $total_nilai_realisasi_bua / $total_volume_realisasi_bua;

			$total_volume_evaluasi_bua = $total_volume_rap_bua - $total_volume_realisasi_bua;
			$total_nilai_evaluasi_bua = $total_nilai_rap_bua - $total_nilai_realisasi_bua;
			?>

			<tr class="table-active">
	            <th class="text-center" rowspan="2" style="vertical-align:middle;">NO.</th>
				<th class="text-center" rowspan="2" style="vertical-align:middle;">URAIAN</th>
				<th class="text-center" colspan="3">RAP</th>
				<th class="text-center" colspan="3">REALISASI</th>
				<th class="text-center" colspan="3">DEVIASI</th>
	        </tr>
			<tr class="table-active">
	            <th class="text-center">VOL.</th>
				<th class="text-center">HARSAT</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOL.</th>
				<th class="text-center">HARSAT</th>
				<th class="text-center">NILAI</th>
				<th class="text-center">VOL.</th>
				<th class="text-center">NILAI</th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center">1</th>
				<th class="text-left">BAHAN</th>
				<th class="text-right"><?php echo number_format($total_volume_boulder_rap,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harsat_boulder_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_nilai_boulder_rap,0,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_volume_boulder_realisasi,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harsat_boulder_realisasi,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_detail_boulder?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_nilai_boulder_realisasi,0,',','.');?></a></th>
				<?php
				$styleColor = $total_volume_boulder_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_volume_boulder_evaluasi < 0 ? "(".number_format(-$total_volume_boulder_evaluasi,2,',','.').")" : number_format($total_volume_boulder_evaluasi,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_boulder_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_boulder_evaluasi < 0 ? "(".number_format(-$total_nilai_boulder_evaluasi,0,',','.').")" : number_format($total_nilai_boulder_evaluasi,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center">2</th>
				<th class="text-left">ALAT</th>
				<th class="text-right"><?php echo number_format($total_vol_rap_alat,2,',','.');?></th>
				<?php
				$total_harsat_rap_alat = (round($total_vol_rap_alat,2)!=0)?($total_nilai_rap_alat / $total_vol_rap_alat) * 1:0;
				?>
				<th class="text-right"><?php echo number_format($total_harsat_rap_alat ,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_evaluasi_alat_pemakaian?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_nilai_rap_alat,0,',','.');?></a></th>
				<th class="text-right"><?php echo number_format($total_vol_realisasi_alat,2,',','.');?></th>
				<?php
				$total_harsat_realisasi_alat = (round($total_vol_realisasi_alat,2)!=0)?($total_nilai_realisasi_alat / $total_vol_realisasi_alat) * 1:0;
				?>
				<th class="text-right"><?php echo number_format($total_harsat_realisasi_alat,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_evaluasi_alat_pemakaian?filter_date=".$filter_date = date('d-m-Y',strtotime($date1)).' - '.date('d-m-Y',strtotime($date2))) ?>"><?php echo number_format($total_nilai_realisasi_alat,0,',','.');?></a></th>
				<?php
				$styleColor = $total_vol_evaluasi_alat < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_vol_evaluasi_alat < 0 ? "(".number_format(-$total_vol_evaluasi_alat,2,',','.').")" : number_format($total_vol_evaluasi_alat,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_alat < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi_alat < 0 ? "(".number_format(-$total_nilai_evaluasi_alat,0,',','.').")" : number_format($total_nilai_evaluasi_alat,0,',','.');?></th>
	        </tr>
			<tr class="table-active3">
	            <th class="text-center">3</th>
				<th class="text-left">BUA</th>
				<th class="text-right"><?php echo number_format($total_volume_rap_bua,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harsat_rap_bua,0,',','.');?></th>
				<?php
				$rap_bua = $this->db->select('rap.id')
				->from('rap_bua rap')
				->join('rap_bua_detail det','rap.id = det.rap_bua_id','left')
				->where("rap.status = 'PUBLISH'")
				->where("rap.tanggal_rap_bua < '$date2'")
				->group_by("rap.id")
				->order_by('rap.tanggal_rap_bua','desc')->limit(1)
				->get()->row_array();
				$rap_bua = $rap_bua['id'];
				?>
				<th class="text-right"><a target="_blank" href="<?= base_url("rap/cetak_rap_bua/".$rap_bua) ?>"><?php echo number_format($total_nilai_rap_bua,0,',','.');?></a></th>
				<th class="text-right"><?php echo number_format($total_volume_realisasi_bua,2,',','.');?></th>
				<th class="text-right"><?php echo number_format($total_harsat_realisasi_bua,0,',','.');?></th>
				<th class="text-right"><a target="_blank" href="<?= base_url("laporan/cetak_evaluasi_bua/".$date1."/".$date2."/".$rap_bua."") ?>"><?php echo number_format($total_nilai_realisasi_bua,0,',','.');?></a></th>
				<?php
				$styleColor = $total_volume_evaluasi_bua < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo number_format($total_volume_evaluasi_bua,2,',','.');?></th>
				<?php
				$styleColor = $total_nilai_evaluasi_bua < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi_bua < 0 ? "(".number_format(-$total_nilai_evaluasi_bua,0,',','.').")" : number_format($total_nilai_evaluasi_bua,0,',','.');?></th>
	        </tr>
			<tr class="table-active4">
				<th class="text-right" colspan="2">TOTAL</th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<?php
				$total_nilai_rap = $total_nilai_komposisi + $total_nilai_rap_alat + $total_nilai_rap_bua;
				?>
				<th class="text-right"><?php echo number_format($total_nilai_rap,0,',','.');?></th>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<?php
				$total_nilai_realisasi = $total_nilai_realisasi + $total_nilai_realisasi_alat + $total_nilai_realisasi_bua;
				?>
				<th class="text-right"><?php echo number_format($total_nilai_realisasi,0,',','.');?></th>
				<th class="text-right"></th>
				<?php
				$total_nilai_evaluasi = $total_nilai_evaluasi + $total_nilai_evaluasi_alat + $total_nilai_evaluasi_bua;
				$styleColor = $total_nilai_evaluasi < 0 ? 'color:red' : 'color:black';
				?>
				<th class="text-right" style="<?php echo $styleColor ?>"><?php echo $total_nilai_evaluasi < 0 ? "(".number_format(-$total_nilai_evaluasi,0,',','.').")" : number_format($total_nilai_evaluasi,0,',','.');?></th>
			</tr>
	    </table>
		<?php
	}

	public function jurnal($arr_date)
	{
		$data = array();
		$filter_akun = $this->input->post('filter_akun');
		$arr_date = $this->input->post('filter_date');
		$arr_filter_date = explode(' - ', $arr_date);
		$date1 = '';
		$date2 = '';

		if(count($arr_filter_date) == 2){
			$date1 	= date('Y-m-d',strtotime($arr_filter_date[0]));
			$date2 	= date('Y-m-d',strtotime($arr_filter_date[1]));
			$filter_date = date('Y-m-d',strtotime($arr_filter_date[0])).' - '.date('Y-m-d',strtotime($arr_filter_date[1]));
		}
		
		?>
		
		<table class="table table-bordered" width="100%">
		 <style type="text/css">
			body {
				font-family: helvetica;
			}

			table tr.table-active{
				/*background: linear-gradient(90deg, #fdcd3b 20%, #fdcd3b 40%, #e69500 80%);*/
				background-color: #eeeeee;
				font-size: 12px;
				font-weight: bold;
			}
				
			table tr.table-active2{
				background-color: #e69500;
				font-size: 12px;
				font-weight: bold;
				color: white;
			}
				
			table tr.table-active3{
				font-size: 12px;
			}
				
			table tr.table-active4{
				/*background: linear-gradient(90deg, #eeeeee 5%, #cccccc 50%, #cccccc 100%);*/
				font-weight: bold;
				font-size: 12px;
				color: black;
			}
		 </style>
			
			<?php
			$this->db->select('*');
			if(!empty($filter_akun) || $filter_akun != 0){
				$this->db->where('akun',$filter_akun);
			}
			$this->db->where("tanggal_transaksi between '$date1' and '$date2'");
			$this->db->group_by('id');
			$this->db->order_by('tanggal_transaksi','desc');
			$this->db->order_by('created_on','desc');
			$transactions = $this->db->get('transactions')->result_array();
			?>

			<tr class="table-active2">
	            <th class="text-center">Tanggal</th>
				<th class="text-center">Nomor Bukti</th>
				<th class="text-center">Penerima</th>
				<th class="text-center">Uraian</th>
				<th class="text-center">Produk</th>
				<th class="text-center">Kode Akun</th>
				<th class="text-center">Nama Akun</th>
				<th class="text-center">Kategori Akun</th>
				<th class="text-center">Debet</th>
				<th class="text-center">Kredit</th>
	        </tr>
			<?php
			if(!empty($transactions)){
				foreach ($transactions as $key => $x) {
				?>
				
			<tr class="table-active3">
	            <th class="text-center"><?= date('d-m-Y',strtotime($x["tanggal_transaksi"])) ?></th>
				<td class="text-left"><a target="_blank" href="<?= base_url("pmm/reports/detail_transaction_jurnal/".$x['id']."/".$x['biaya_id']."/".$x['jurnal_id']."/".$x['production_id']."/".$x['tagihan_id']."/".$x['pembayaran_id']."/".$x['receipt_id']."/".$x['tagihan_pembelian_id']."/".$x['pembayaran_pembelian_id']."/".$x['terima_id']."/".$x['transfer_id']."") ?>"><?php echo $x['nomor_transaksi'];?></a></td>
				<td class="text-left"><?= $this->crud_global->GetField('penerima',array('id'=>$x['penerima']),'nama');?></td>
				<td class="text-left"><?= $x['deskripsi'];?></td>
				<td class="text-left"><?= $this->crud_global->GetField('produk',array('id'=>$x['produk']),'nama_produk');?></td>
				<td align="left"><?= $this->crud_global->GetField('pmm_coa',array('id'=>$x['akun']),'coa');?></td>
				<td align="center"><?= $this->crud_global->GetField('pmm_coa',array('id'=>$x['akun']),'coa_number');?></td>
				<td class="text-left"><?= $x['transaksi'];?></td>
				<td class="text-right"><?php echo number_format($x['debit'],0,',','.');?></td>
				<td class="text-right"><?php echo number_format($x['kredit'],0,',','.');?></td>
	        </tr>
				<?php
				}
			}
			?>
	    </table>
		<?php
	}

	public function detail_transaction_jurnal($id,$biaya_id,$jurnal_id,$production_id,$tagihan_id,$pembayaran_id,$receipt_id,$tagihan_pembelian_id,$pembayaran_pembelian_id,$terima_id,$transfer_id)
    {
        $check = $this->m_admin->check_login();
        if($check == true){

            $this->db->select('*');
            $this->db->where('id',$biaya_id);
            $query = $this->db->get('pmm_biaya');
            $data['row_biaya'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$jurnal_id);
            $query = $this->db->get('pmm_jurnal_umum');
            $data['row_jurnal'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$production_id);
            $query = $this->db->get('pmm_productions');
            $data['row_production'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$tagihan_id);
            $query = $this->db->get('pmm_penagihan_penjualan');
            $data['row_tagihan'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$pembayaran_id);
            $query = $this->db->get('pmm_pembayaran');
            $data['row_pembayaran'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$receipt_id);
            $query = $this->db->get('pmm_receipt_material');
            $data['row_receipt'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$tagihan_pembelian_id);
            $query = $this->db->get('pmm_penagihan_pembelian');
            $data['row_tagihan_pembelian'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$pembayaran_pembelian_id);
            $query = $this->db->get('pmm_pembayaran_penagihan_pembelian');
            $data['row_pembayaran_pembelian'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$terima_id);
            $query = $this->db->get('pmm_terima_uang');
            $data['row_terima'] = $query->result_array();

			$this->db->select('*');
            $this->db->where('id',$transfer_id);
            $query = $this->db->get('pmm_transfer');
            $data['row_transfer'] = $query->result_array();

            $this->load->view('laporan_keuangan/detail_transaction_jurnal',$data);
            
        }else {
            redirect('admin');
        }
    }

}