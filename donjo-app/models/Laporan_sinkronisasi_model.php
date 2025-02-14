<?php

class Laporan_sinkronisasi_model extends MY_Model {

	private $table = "laporan_sinkronisasi";
	protected $tipe = 'laporan_apbdes';
	
	const ORDER = [
		2 => 'nama',
		3 => 'semester', // atau bulan
		4 => 'tahun',
		5 => 'updated_at',
		6 => 'kirim'
	];
	
	public function set_tipe(string $tipe)
	{
		$this->tipe = $tipe;

		return $this;
	}
	
	public function get_data(string $search = '', $tahun = NULL)
	{
		$this->db->from($this->table);

		if ($search)
		{
			$this->db
				->group_start()
					->like('nama', $search)
					->or_like('tahun', $search)
					->or_like('semester', $search)
					->or_like('nama_file', $search)
				->group_end();
		}

		if ($tahun) $this->db->where('tahun', $tahun);
		
		return $this->db->where('tipe', $this->tipe);
	}

	public function find($id)
	{
		return $this->db
			->where('tipe', $this->tipe)
			->where('id', $id)
			->get($this->table)
			->row();
	}

	public function get_tahun()
	{
		$data = $this->db
			->distinct()
			->select('tahun')
			->where('tipe', $this->tipe)
			->get($this->table)
			->result();

		return $data;
	}

	public function insert($data = null)
	{
		// $data bisa dikirim dari laporan yg dibuat otomatis; kalau kosong ambil dari form
		$data = $data ?: $this->validasi();
		$outp = $this->db->insert($this->table, $data);

		status_sukses($outp);
	}

	public function update($id, $data = null)
	{
		$data = $data ?: $this->validasi();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['kirim'] = NULL;
		$outp = $this->db->where('id', $id)->update($this->table, $data);

		status_sukses($outp);
	}

	public function insert_or_update($where = null, $data = null)
	{
		$id = $this->db->select('id')->get_where($this->table, $where)->row()->id;

		$outp = ($id) ? $this->update($id, $data) : $this->insert($data);

		status_sukses($outp);
	}

	public function delete($id)
	{
		$outp = $this->db->where('id', $id)->where('kirim', NULL)->delete($this->table);

		if ($outp && ($nama_file = $this->find($id)->nama_file))
		{
			unlink(LOKASI_DOKUMEN . $nama_file);
		}

		status_sukses($outp);
	}

	public function delete_all()
	{
		foreach ($this->input->post('id_cb') as $id)
		{
			$this->delete($id);
		}
	}

	private function validasi()
	{
		$post = $this->input->post();

		$data = [
			'judul' => alfanumerik_spasi($post['judul']),
			'semester' => bilangan( ($this->tipe == 'laporan_apbdes') ? $post['semester'] : $post['bulan']),
			'tahun' => bilangan($post['tahun']),
			'nama_file' => $this->upload($post['judul'], $post['old_file']),
			'tipe' => $this->tipe,
		];

		return $data;
	}

	private function upload($nama_file, $old_file)
	{
		$this->load->library('upload');

		$config['upload_path'] = LOKASI_DOKUMEN;
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = 2048;
		$config['file_name'] = namafile($nama_file);

		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload('nama_file'))
		{
			$this->session->error_msg = $this->upload->display_errors();
			$this->session->success = -1;
			return NULL;
		}

		$upload = $this->upload->data();

		if ($old_file) unlink(LOKASI_DOKUMEN . $old_file);
		
		return $upload['file_name'];
	}

	public function opendk($id)
	{
		$list_data = $this->db
			->where_in('id', $id)
			->get($this->table)
			->result_array();

		$kirim = [];
		foreach($list_data as $key => $data)
		{
			$kirim[$key]['id'] = $data['id'];
			$kirim[$key]['judul'] = $data['judul'];
			if ($this->tipe == 'laporan_apbdes')
			{
				$kirim[$key]['semester'] =  $data['semester'];
			}
			else
			{
				$kirim[$key]['bulan'] =  $data['semester'];
			}			
			$kirim[$key]['nama_file'] = $data['nama_file'];
			$kirim[$key]['tahun'] = $data['tahun'];
			$kirim[$key]['created_at'] = $data['created_at'];
			$kirim[$key]['updated_at'] = $data['updated_at'];
			$kirim[$key]['file'] = $this->file($data['nama_file']);
		}

		return $kirim;
	}

	public function file($nama_file)
	{
		return base64_encode(file_get_contents(LOKASI_DOKUMEN . $nama_file));
	}

	public function kirim($id)
	{
		$data['kirim'] = date('Y-m-d H:i:s');
		$outp = $this->db->where_in('id', $id)->update($this->table, $data);

		status_sukses($outp);
	}
}