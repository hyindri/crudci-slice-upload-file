<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->model('welcome_model');
	}

	public function index()
	{
		view('index');
	}

	public function ambil_data()
	{
		$fetch_data = $this->welcome_model->make_datatables();
		$data = array();
		$no = 1;
		foreach($fetch_data as $row)
		{
			$sub_array 		= array();
			$sub_array[] 	= $no++;
			$sub_array[] 	= $row->nim;
			$sub_array[]	= $row->nama;
			$sub_array[] 	= $row->jurusan;
			$sub_array[]	= $row->fakultas;
			$sub_array[] 	= '<a href="'.site_url().'/upload/ktm/'.$row->file_ktm.'" target="_blank">Lihat Data</a>';
			$sub_array[] 	= '<div class="form-group"><button type="button" class="btn btn-warning btn-sm" id="get-ubahModal" data-id="'.$row->id_mahasiswa.'">Edit </button> <button type="button" class="btn btn-danger btn-sm" id="get-hapusModal" data-id="'.$row->id_mahasiswa.'">Hapus</button></div>';			
			$data[]			= $sub_array;
		}

		$output = array(
			"draw " => intval($_POST['draw']),
			"recordsTotal" => $this->welcome_model->get_all_data(),
			"recordsFiltered" => $this->welcome_model->get_filtered_data(),
			"data" => $data
		);
		echo json_encode($output);
	}

	function tambah_data()
	{
		$data = $this->welcome_model->tambahData();
		json_encode($data);
	}

	function ambil_satu_data()
	{
		$output = array();
		$data = $this->welcome_model->ambilSatuData($this->input->post('id_mahasiswa'));
		foreach($data as $row)
		{
			$output['id'] = $row->id_mahasiswa;
			$output['nim'] = $row->nim;
			$output['nama'] = $row->nama;
			$output['jurusan'] = $row->jurusan;
			$output['fakultas'] = $row->fakultas;
			$output['old_ktm'] = $row->file_ktm;
		}
		echo json_encode($output);
	}

	function ubah_data()
	{
		$data = $this->welcome_model->ubahData();
		json_encode($data);
	}

	function hapus_data()
	{
		$data = $this->welcome_model->hapusData();
		json_encode($data);
	}

}
