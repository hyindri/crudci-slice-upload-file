<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome_model extends CI_Model
{
    // Deklarasi

    public $table = "mahasiswa";
    public $select_column = array('id_mahasiswa', 'nim', 'nama', 'jurusan', 'fakultas','file_ktm');
    public $order_column = array(null, 'id_mahasiswa', 'nim', 'nama', 'jurusan', 'fakultas','file_ktm', null);

    public function make_query()
    {
        // Pemanggilan table dan kolom
        $this->db->select($this->select_column);
        $this->db->from($this->table);

        if (isset($_POST['search']['value'])) {
            // Fungsi Search
            $this->db->like('nim', $_POST['search']['value']);
            $this->db->or_like('nama', $_POST['search']['value']);

        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('id_mahasiswa');
        }
    }

    public function make_datatables()
    {
        $this->make_query();
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_filtered_data()
    {
        $this->make_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_data()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function tambahData()
    {
        $data = array(
            'id_mahasiswa' => uniqid(),
            'nim' => $this->input->post('nim'),
            'nama' => $this->input->post('nama'),
            'jurusan' => $this->input->post('jurusan'),
            'fakultas' => $this->input->post('fakultas'),
        );

        if (!empty($_FILES['file_ktm']['name'])) {            
            $upload = $this->_upload_KTM();
            $data['file_ktm'] = $upload;
        }


        $this->db->where('nim', $data['nim']);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return $this->db->insert('');
        } else {
            return $this->db->insert($this->table, $data);
        }
    }

    public function ambilSatuData($id_mahasiswa)
    {
        $this->db->where('id_mahasiswa', $id_mahasiswa);
        return $this->db->get($this->table)->result();
    }

    public function ubahData()
    {
        $id_mahasiswa['id_mahasiswa'] = $this->input->post('id_mahasiswa');
        $data = array(
            'nim' => $this->input->post('nim'),
            'nama' => $this->input->post('nama'),
            'jurusan' => $this->input->post('jurusan'),
            'fakultas' => $this->input->post('fakultas'),
        );

        if (!empty($_FILES['file_ktm']['name'])) {
            if (!empty($this->input->post('old_file_ktm'))) {
                unlink('upload/ktm/' . $this->input->post('old_file_ktm'));
            }
            $upload = $this->_upload_KTM();
            $data['file_ktm'] = $upload;
        }

        return $this->db->update($this->table, $data, $id_mahasiswa);
    }

    public function hapusData()
    {
        $id['id_mahasiswa'] = $this->input->post('id_mahasiswa');
        unlink('upload/ktm/' . $this->input->post('old_file_ktm'));
        return $this->db->delete($this->table, $id);
    }

    private function _upload_KTM()
    {        
        if (!file_exists('upload/ktm/')) {
            mkdir('upload/ktm/', 0777, true);
        }
        $config6['upload_path']          = 'upload/ktm/';
        $config6['allowed_types']        = 'pdf';
        $config6['max_size']             = 3000; //set max size allowed in Kilobyte
        $config6['file_name']            = round(microtime(true) * 1000); //just milisecond timestamp fot unique name

        $this->load->library('upload', $config6);
        $this->upload->initialize($config6);

        if (!$this->upload->do_upload('file_ktm')) //upload and validate
        {
            $data['inputerror'][] = 'file_ktm';
            $data['error_string'][] = 'Upload error: ' . $this->upload->display_errors('', ''); //show ajax error
            $data['status'] = FALSE;
            echo json_encode($data);
            exit();
        }
        return $this->upload->data('file_name');
    }

}
