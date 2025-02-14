<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * File ini:
 *
 * View untuk Modul Lapak Admin > Produk
 *
 *
 * donjo-app/views/lapak_admin/produk/index.php
 *
 */

/**
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package	OpenSID
 * @author	Tim Pengembang OpenDesa
 * @copyright	Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright	Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license	http://www.gnu.org/licenses/gpl.html	GPL V3
 * @link 	https://github.com/OpenSID/OpenSID
 */
?>

<div class="content-wrapper">
	<section class="content-header">
		<h1>
			PRODUK
			<small>Daftar Data</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= site_url('hom_sid') ?>"><i class="fa fa-home"></i> Home</a></li>
			<li class="active">Daftar Data</li>
		</ol>
	</section>
	<section class="content">
		<?php $this->load->view("$this->controller/navigasi", $navigasi); ?>
		<div id="maincontent"></div>
		<div class="box box-info">
			<div class="box-header with-border">
				<?php if ($this->CI->cek_hak_akses('u')): ?>
					<a href="<?= site_url("$this->controller/produk_form") ?>" class="btn btn-social btn-flat btn-success btn-sm btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Tambah Data"><i class="fa fa-plus"></i> Tambah Data
					</a>
				<?php endif; ?>
				<?php if ($this->CI->cek_hak_akses('h')): ?>
					<a href="#confirm-delete" title="Hapus Data" onclick="deleteAllBox('mainform','<?=site_url("$this->controller/produk_delete_all"); ?>')" class="btn btn-social btn-flat btn-danger btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block hapus-terpilih"><i class='fa fa-trash-o'></i> Hapus Data Terpilih</a>
				<?php endif; ?>
			</div>
			<form id="mainform" name="mainform" method="post">
				<div class="box-header with-border form-inline">
					<div class="row">
						<div class="col-sm-2">
							<select class="form-control input-sm select2" id="status" name="status">
								<option value="">Semua Status</option>
								<option value="1">Aktif</option>
								<option value="2">Non Aktif</option>
							</select>
						</div>
						<div class="col-sm-2">
							<select class="form-control input-sm select2" id="id_pend" name="id_pend">
								<option value="">Semua Pelapak</option>
								<?php foreach ($pelapak as $pel): ?>
									<option value="<?= $pel->id_pend; ?>"><?= $pel->nik . ' - ' . $pel->pelapak; ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="col-sm-2">
							<select class="form-control input-sm select2" id="id_produk_kategori" name="id_produk_kategori">
								<option value="">Semua Kategori</option>
								<?php foreach ($kategori as $kat): ?>
									<option value="<?= $kat->id; ?>"><?= $kat->kategori; ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped dataTable table-hover tabel-daftar" id="tabel-produk">
							<thead class="bg-gray disabled color-palette">
								<tr>
									<th><input type="checkbox" id="checkall"/></th>
									<th>No</th>
									<th>Aksi</th>
									<th>Pelapak</th>
									<th>Produk</th>
									<th>Kategori</th>
									<th>Harga</th>
									<th>Satuan</th>
									<th>Potongan</th>
									<th>Deskripsi</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</form>
		</div>
	</section>
</div>
<?php $this->load->view('global/confirm_delete'); ?>
<script>
	$(document).ready(function() {
		let tabel_produk = $('#tabel-produk').DataTable({
			'processing': true,
			'serverSide': true,
			'autoWidth': false,
			'pageLength': 10,
			'order': [[4, 'desc']],
			'columnDefs': [
				{ 'orderable': false, 'targets': [0, 1, 2] },
				{ 'className' : 'padat', 'targets': [0, 1, 7, 8] },
				{ 'className' : 'aksi', 'targets': [2] },
				{ 'className' : 'dt-nowrap', 'targets': [9], 'width': '30%' }
			],
			'ajax': {
				'url': "<?= site_url("$this->controller/produk"); ?>",
				'method': 'POST',
				'data': function(d) {
					d.status= $('#status').val();
					d.id_pend = $('#id_pend').val();
					d.id_produk_kategori = $('#id_produk_kategori').val();
				}
			},
			'columns': [
				{
					'data': function(data) {
						return `<input type="checkbox" name="id_cb[]" value="${data.id}"/>`
					}
				},
				{ 'data': null },
				{
					'data': function(data) {
						let status;
						if (data.status == 1) {
							status = `<a href="<?= site_url("$this->controller/produk_status/") ?>${data.id}/2" class="btn bg-navy btn-flat btn-sm" title="Non Aktifkan Produk"><i class="fa fa-unlock"></i></a>`
						} else {
							status = `<a href="<?= site_url("$this->controller/produk_status/") ?>${data.id}/1" class="btn bg-navy btn-flat btn-sm" title="Aktifkan Produk"><i class="fa fa-lock"></i></a>`
						}

						return `
						<?php if ($this->CI->cek_hak_akses('u')): ?>
							<a href="<?= site_url("$this->controller/produk_form/"); ?>${data.id}" title="Edit Data"  class="btn bg-orange btn-flat btn-sm"><i class="fa fa-edit"></i></a>
							${status}
						<?php endif; ?>
						<?php if ($this->CI->cek_hak_akses('h')): ?>
							<a href="#" data-href="<?= site_url("$this->controller/produk_delete/"); ?>${data.id}" class="btn bg-maroon btn-flat btn-sm"  title="Hapus" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i></a>
						<?php endif; ?>
						<a href="<?= site_url("$this->controller/produk_detail/"); ?>${data.id}" class="btn bg-blue btn-flat btn-sm" title="Tampilkan" data-target="#modalBox" data-remote="false" data-toggle="modal" data-backdrop="false" data-keyboard="false" data-title="Detail Produk"><i class="fa fa-eye"></i></a>
						`
					}
				},
				{ 'data': 'pelapak' },
				{ 'data': 'nama' },
				{ 'data': 'kategori' },
				{
					'data': 'harga',
					'render': $.fn.dataTable.render.number( '.', ',', 0, 'Rp ' )
				},
				{ 'data': 'satuan' },
				{
					'data': function(data) {
						return `${(data.tipe_potongan == 1) ? data.potongan + '%' : 'Rp. ' + formatRupiah(data.potongan)}`
					}
				},
				{
					'data': 'deskripsi',
					'render': function (data) {
						return data.length > 150 ? data.substr(0, 150) + '…' : data;
					}
				}
			],
			'language': {
				'url': "<?= base_url('/assets/bootstrap/js/dataTables.indonesian.lang') ?>"
			}
		});

		tabel_produk.on('draw.dt', function() {
			let PageInfo = $('#tabel-produk').DataTable().page.info();
			tabel_produk.column(1, {
				page: 'current'
			}).nodes().each(function(cell, i) {
				cell.innerHTML = i + 1 + PageInfo.start;
			});
		});

		$('#status').on('select2:select', function (e) {
			tabel_produk.ajax.reload();
		});

		$('#id_pend').on('select2:select', function (e) {
			tabel_produk.ajax.reload();
		});

		$('#id_produk_kategori').on('select2:select', function (e) {
			tabel_produk.ajax.reload();
		});
	});
</script>
