<?php
defined('BASEPATH') or exit ('No Direct Script Accsess Allowed');

class Peminjaman extends CI_Controller{
    function __construct(){
        parent::__construct();
        if ($this->session->userdata('status') != "login") {
$alert=$this->session->set_flashdata('alert','anda belum login');
redirect(base_url());
        }
    }
    function index(){
        $data['peminjaman'] = $this->db->query("SELECT * FROM detail_pinjam D,peminjaman P, Buku B, anggota A WHERE B.id_buku=D.id_buku and A.id_anggota=P.id_anggota")->result();
    $this->load->view('admin/header');
    $this->load->view('admin/peminjaman');
    $this->load->view('admin/footer');
    }
    public function kode_otomatis(){
        // cek data pinjaman
        $this->db->select('right(id_pinjam,3) as kode', false);
        $this->db->order_by('id_pinjam','desc');
        $this->db->limit(1);
        $query=$this->db->get('peminjaman');
        // cek tebal transaksi berdasarkan id_anggota
        $id_agt=$this->session->userdata('id_agt');
        $qry="select id_pinjam as kode from transaksi where id_anggota='".$id_agt."' 
                order by id_pinjam limit 1";
        $dt = $this->db->query($qry);
        // Cek data table transaksi
        //$this->db->select('right(id_pinjam,3) as kode', false);
        //$this->db->order_by('id_pinjam','desc');
        //$this->db->limit(1);
        $qry2="SELECT right(id_pinjam,3) kode FROM transaksi UNION 
                select right(id_pinjam,3) kode from peminjaman ORDER by kode DESC LIMIT 1";
        $qryTransaksi=$this->db->query($qry2);
        
        if ($dt->num_rows()<>0){
            $dts=$dt->row();
            $kodejadi=$dts->kode;
        
        }else{
        if ($qryTransaksi->num_rows()<>0){
            $data=$qryTransaksi->row();
            $kode=intval($data->kode)+1;
        }else {
        if($query->num_rows()<>0){
            $data=$query->row();
            $kode=intval($data->kode)+1;
        }else{
            $kode=1;
        }
        }
        
        $kodemax=str_pad($kode,3,"0", STR_PAD_LEFT);
        $kodejadi='PJ'.$kodemax;
        }
        return $kodejadi;
    }
 
    public function tambah_pinjam($id){
        if ($this->session->userdata('status') != "login") {
            $alert=$this->session->set_flashdata('alert','anda belum login');
            redirect(base_url());
        }else {
            $d = $this->M_perpus->find($id,'buku');
            $isi = array(
                'id_pinjam' => $this->M_perpus->kode_otomatis(),
                'id_buku' =>$id,
                'id_anggota' =>$this->session->userdata('id_agt'),
                'tgl_pencatatan' =>date('Y-m-d'),
                'tgl_pinjam'=>'_',
                'tgl_kembali'=>'_',
                'denda' => '10000',
                'tgl_pengembalian' => '_',
                'total_denda' =>'0',
                'status_peminjaman' =>'Belum selesai',
                'status_pengembalian' =>'Belum kembali');
                $id_agt= $this->session->userdata('id_agt');
                $id_pinjam = $this->M_perpus->kode_otomatis();
                
                $o = $this->M_perpus->edit_data(array('id_buku'=>$id,'id_anggota' => $id_agt,'id_pinjam' => $id_pinjam ),'transaksi')->num_rows();
                
                $o = $this->M_perpus->edit_data(array('id_buku'=>$id),'transaksi')->num_rows();
            if ($o>0) {
                $this->session->set_flashdata('alert','Buku ini sudah ada dikeranjang');
                redirect(base_url().'member');

            }
            $this->M_perpus->insert_data($isi, 'transaksi');
            $jml_buku = $d->jumlah_buku-1;
            $w=array('id_buku'=>$id);
            $data = array('jumlah_buku' =>$jml_buku);
            $this->M_perpus->update_data('buku',$data,$w);
            redirect(base_url().'member');
        
        }
           
    }
    public function lihat_keranjang(){
        $data['anggota'] = $this->M_perpus->edit_data(array('id_anggota' =>
       $this->session->userdata('id_agt')),'anggota')->result();
        $where = $this->session->userdata('id_agt');
        $data['peminjaman']=$this->db->query("select*from transaksi t,buku
       b,anggota a where b.id_buku=t.id_buku and a.id_anggota=t.id_anggota and
       a.id_anggota=$where")->result();
        $d=$this->M_perpus->edit_data(array('id_anggota' => $this->session->userdata('id_agt')),'transaksi')->num_rows();
        if($d>0){
        $this->load->view('desain');
        $this->load->view('toplayout',$data);
        $this->load->view('keranjang', $data);
        }else{redirect('member');}
        }
        function hapus_keranjang($nomor){
        $w = array('id_buku' => $nomor);
        $data = $this->M_perpus->edit_data($w,'transaksi')->row();
        // tambah script stock buku
        $dt = $this->M_perpus->edit_data($w,'buku')->row();
        $jml_buku=$dt->jumlah_buku+1;
        // 
        $ww = array('id_buku' => $data->id_buku);
        $data2 = array('status_buku' => '1', 'jumlah_buku' => $jml_buku);
        
        $dw = array('id_buku' => $data->id_buku,'id_anggota' => 
                    $this->session->userdata('id_agt'));
        $this->M_perpus->update_data('buku',$data2,$ww);
        $this->M_perpus->delete_data($dw,'transaksi');
        redirect(base_url().'peminjaman/lihat_keranjang');
    }
    public function selesai_booking($where){
        $d = $this->M_perpus->find($where, 'transaksi');
        $isi = array(
            'id_pinjam' => $this->M_perpus->kode_otomatis(),
            'tanggal_input' => date('Y-m-d H:m:s'),
            'id_anggota' => $where,
            'tgl_pinjam' => '-',
            'tgl_kembali' => '-',
            'totaldenda' => '0',
            'status_peminjaman' => 'Booking',
            'status_pengembalian' => 'Belum Kembali'
        );
        $this->M_perpus->insert_data($isi, 'peminjaman');
        $this->M_perpus->insert_detail($where);
        $this->M_perpus->kosongkan_data('transaksi');
        $data['useraktif'] = $this->M_perpus->edit_data(array('id_anggota' => $this->session->userdata('id_agt')),'anggota')->result();
        $data['items'] = $this->db->query("select * from peminjaman p,detail_pinjam d, buku b where b.id_buku=d.id_buku and d.id_pinjam=p.id_pinjam and p.id_anggota='$where'")->result();
        $this->load->view('desain');
        $this->load->view('toplayout',$data);
        $this->load->view('info', $data);
    }
    }
?>