<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use PDF;
use App\Models\Barang;
use App\Models\Inventori;
use App\Models\Mutation;
use App\Models\Report;
use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Golongan;
use App\Models\Bidang;
use App\Models\Satuankerja;
use App\Models\Barangmasuk;
use App\Models\Barangkeluar;

class PDFController extends Controller
{
  public function __construct(){
      $this->middleware('auth');
  }
  public function index(){
    return view('laporan');
  }
  public function inventaris_all(){
    $barangs = Barang::join('golongan_barang','golongan_barang_id','=','golongan_barang.id')
    ->join('bidang_barang','bidang_barang_id','=','bidang_barang.id')
    ->join('kelompok_barang','kelompok_barang_id','=','kelompok_barang.id')
    ->select('barang.*', DB::raw('COUNT(barang.id) as total_barang'), DB::raw('SUM(barang.price) as total_price'),'golongan_barang.code as golongan_barang_code','golongan_barang.name as golongan_barang_name', 'bidang_barang.code as bidang_barang_code', 'bidang_barang.name as bidang_barang_name','kelompok_barang.code as kelompok_barang_code', DB::raw('GROUP_CONCAT(DISTINCT(kelompok_barang.name)) as kelompok_barang_name'))
    ->whereYear('barang.created_at','=',date('Y'))
    ->groupBy('kelompok_barang_code')
    // , 'bidang_barang_id', 'kelompok_barang_id','bidang_barang_name', 'kelompok_barang_name', 'golongan_barang_code', 'bidang_barang_code')
    ->orderBy('golongan_barang_code', 'asc')
    // ->havingRaw('COUNT(barang.id) > 0')
    ->get();

    $golongans = Barang::join('golongan_barang','golongan_barang_id','=','golongan_barang.id')
    ->select(DB::raw('SUM(barang.price) as total_price'), 'golongan_barang.code as golongan_barang_code')
    ->groupBy('golongan_barang_code')
    ->orderBy('golongan_barang_code', 'asc')
    ->get();

    $pdf=PDF::loadView('pdf.inventaris', ['barangs' => $barangs, 'golongans' => $golongans]);
    return $pdf->stream('Inventaris_All.pdf');
    // return view('pdf.inventaris', ['barangs' => $barangs, 'golongans' => $golongans]);
  }
  public function inventaris_pertahun(){
    // 1
    $barangs = Barang::join('golongan_barang','golongan_barang_id','=','golongan_barang.id')
    ->join('bidang_barang','bidang_barang_id','=','bidang_barang.id')
    ->select(
      'barang.*',
      DB::raw('SUM(barang.in_stock) as total_barang'),
      DB::raw('SUM(barang.price) as total_price'),
      'golongan_barang.code as golongan_barang_code',
      'golongan_barang.name as golongan_barang_name',
      'bidang_barang.code as bidang_barang_code',
      'bidang_barang.name as bidang_barang_name')
      // 'kelompok_barang.code as kelompok_barang_code',
      // DB::raw('GROUP_CONCAT(DISTINCT(kelompok_barang.name)) as kelompok_barang_name'))
    ->whereYear('barang.created_at','=',date('Y'))
    ->groupBy('bidang_barang.code')
    ->orderBy('golongan_barang.code', 'asc')
    // ->havingRaw('SUM(barang.in_stock) >= 0')
    ->get();

    $pdf=PDF::loadView('pdf.inventaris', ['barangs' => $barangs])->setPaper('a4', 'landscape');
    return $pdf->stream('Inventaris Tahun '.date('Y').'.pdf');
  }
  public function inventaris_tabel_kode_barang(){
    $barangs = Barang::join('golongan_barang','barang.golongan_barang_id','=','golongan_barang.id')
      // ->join('golongan_barang','golongan_barang_id','=','golongan_barang.id')
      ->join('bidang_barang','barang.bidang_barang_id','=','bidang_barang.id')
      ->join('kelompok_barang','kelompok_barang_id','=','kelompok_barang.id')
      ->join('sub_kelompok_barang','sub_kelompok_barang_id','=','sub_kelompok_barang.id')
      ->select('barang.*','golongan_barang.code as golongan_barang_code',
      'golongan_barang.name as golongan_barang_name',
      'bidang_barang.code as bidang_barang_code',
      'bidang_barang.name as bidang_barang_name',
      'kelompok_barang.code as kelompok_barang_code',
      'kelompok_barang.name as kelompok_barang_name',
      'sub_kelompok_barang.code as sub_kelompok_barang_code',
      'sub_kelompok_barang.name as sub_kelompok_barang_name')
      ->orderBy('golongan_barang.code','asc')
      ->orderBy('bidang_barang.code','asc')
      ->orderBy('kelompok_barang.code','asc')
      ->orderBy('sub_kelompok_barang.code','asc')
      ->get();

    $pdf=PDF::loadView('pdf.tabelkodebarang', ['barangs' => $barangs])->setPaper('a4', 'landscape');
    return $pdf->stream('Buku Inventaris '.date('Y').'.pdf');
  }
  public function inventaris_buku(){
    // 1
    $barangs = Barangmasuk::join('barang','barang_masuk.barang_id','=','barang.id')
      ->select(
        'barang_masuk.*',
        'barang.code as barang_code',
        'barang.name as barang_name',
        'barang.brand as barang_brand',
        'barang.satuan_name as barang_satuan_name')
      ->orderBy('barang.code', 'asc')
      ->get();

    $pdf=PDF::loadView('pdf.bukuinventaris', ['barangs' => $barangs])->setPaper('a4', 'landscape');
    return $pdf->stream('Buku Inventaris '.date('Y').'.pdf');
  }
  public function inventaris_aktif_usulan(){
    //2
    $barangaktifs = Barangmasuk::join('barang','barang_id','=','barang.id')
      ->join('pegawai','barang_masuk.pegawai_id','=','pegawai.id')
      ->join('status_barang','barang_masuk.status_barang_id','=','status_barang.id')
      ->join('kondisi_barang','barang_masuk.kondisi_barang_id','=','kondisi_barang.id')
      ->select('barang_masuk.*','barang.code as barang_code','barang.name as barang_name','barang.brand as barang_brand','status_barang.name as status_barang_name','kondisi_barang.name as kondisi_barang_name','pegawai.name as pegawai_name','barang.code as barang_code','barang.name as barang_name')
      ->where('status_barang.name','Aktif')
      // ->orWhere('kondisi_barang.name','Dalam usulan penghapusan')
      ->orderBy('barang_code', 'asc')
      ->get();
    $barangusulans = Barangkeluar::join('barang','barang_id','=','barang.id')
        ->join('pegawai','barang_keluar.pegawai_id','=','pegawai.id')
        ->join('status_mutasi','barang_keluar.status_mutasi_id','=','status_mutasi.id')
        ->join('kondisi_barang','barang_keluar.kondisi_barang_id','=','kondisi_barang.id')
        ->select('barang_keluar.*','barang.code as barang_code','barang.name as barang_name','barang.brand as barang_brand','status_mutasi.name as status_mutasi_name','kondisi_barang.name as kondisi_barang_name','pegawai.name as pegawai_name','barang.code as barang_code','barang.name as barang_name')
        ->where('status_mutasi.id','2')
        // ->orWhere('kondisi_barang.name','Dalam usulan penghapusan')
        ->orderBy('barang_code', 'asc')
        ->get();
    $pdf=PDF::loadView('pdf.inventaris2', ['barangaktifs'=>$barangaktifs,'barangusulans'=>$barangusulans])->setPaper('a4', 'landscape');
    return $pdf->stream('Inventaris Aset Aktif dan usulan penghapusan.pdf');
  }
  public function inventaris_usulan(){
    //3
    $barangs = Barangkeluar::join('barang','barang_id','=','barang.id')
      ->join('ruangan','barang_keluar.ruangan_id','=','ruangan.id')
      ->join('status_mutasi','barang_keluar.status_mutasi_id','=','status_mutasi.id')
      ->select('barang_keluar.*','barang.brand as barang_brand','ruangan.code as ruangan_code','barang.code as barang_code','barang.name as barang_name')
      ->where('status_mutasi.id','2')
      ->orderBy('barang_code', 'asc')
      ->get();

    $pdf=PDF::loadView('pdf.inventaris3', ['barangs' => $barangs])->setPaper('a4', 'landscape');
    return $pdf->stream('Inventaris Aset Dalam Usulan Penghapusan.pdf');
  }
  public function inventaris_mutasi_hapus(){
    //4
    $barangs = Inventori::join('barang','barang_id','=','barang.id')
      ->join('pegawai','inventori_barang.pegawai_id','=','pegawai.id')
      ->select('inventori_barang.*','pegawai.name as pegawai_name','barang.code as barang_code','barang.name as barang_name')
      ->where('inventori_barang.status_name','Mutasi Pindah')
      ->orWhere('inventori_barang.status_name','Dihapuskan')
      ->orderBy('barang_code', 'asc')
      ->get();

    $pdf=PDF::loadView('pdf.inventaris4', ['barangs' => $barangs])->setPaper('a4', 'landscape');
    return $pdf->stream('Inventaris Aset Dalam Usulan Penghapusan.pdf');
  }
  public function inventaris_perpengguna(Request $request){
    //5
    $pegawais = Pegawai::where('id', $request->pengguna)->first();
    $barangs = Inventori::join('barang','barang_id','=','barang.id')
      ->join('pegawai','inventori_barang.pegawai_id','=','pegawai.id')
      ->join('golongan_barang','inventori_barang.golongan_barang_id','=','golongan_barang.id')
      ->select('inventori_barang.*','pegawai.name as pegawai_name','barang.code as barang_code','barang.name as barang_name','golongan_barang.name as golongan_name')
      ->where('inventori_barang.pegawai_id',$request->pengguna)
      ->whereNotIn('inventori_barang.golongan_barang_id',[1])
      ->orderBy('barang_code', 'asc')
      ->get();

    $pdf=PDF::loadView('pdf.inventaris5', ['barangs' => $barangs, 'pegawai' => $pegawais])->setPaper('a4', 'landscape');
    return $pdf->stream('Inventaris_Bulanan.pdf');
  }
  public function inventaris_perjenis(Request $request){
    //6
    $barangs = Inventori::join('barang','barang_id','=','barang.id')
      ->join('golongan_barang','inventori_barang.golongan_barang_id','=','golongan_barang.id')
      ->select('inventori_barang.*',DB::raw('SUM(inventori_barang.quantity) as jumlah_barang'),DB::raw('SUM(inventori_barang.price) as total_harga'),'barang.code as barang_code','barang.name as barang_name','golongan_barang.name as golongan_name')
      ->where('inventori_barang.bidang_barang_id',$request->bidang)
      ->groupBy('barang_code')
      ->orderBy('barang_code', 'asc')
      ->get();

    $pdf=PDF::loadView('pdf.inventaris6', ['barangs' => $barangs])->setPaper('a4', 'landscape');
    return $pdf->stream('Inventaris_Bulanan.pdf');
  }
  public function inventaris_perjenis_persatuan_kerja(Request $request){
    //7
    // dd($request);
    // $bidangs = Bidang::where('id', $request->bidang)->first();
    // $satuankerjas = Satuankerja::where('id', $request->satuan)->first();
    // $barangs = Barang::join('bidang_barang','barang.bidang_barang_id','=','bidang_barang.id')
    //           ->join('pegawai','pegawai_id','=','pegawai.id')
    //           ->get();
    // $grouped = $barangs->groupBy('golongan_barang_id')->sortBy('code')->toArray();
    // // $total_masuk = $grouped->count();
    // dd($grouped);

    $barangs = Inventori::join('barang','barang_id','=','barang.id')
      ->join('pegawai','inventori_barang.pegawai_id','=','pegawai.id')
      ->join('satuan_kerja','pegawai.satuan_kerja_id','=','satuan_kerja.id')
      ->join('golongan_barang','inventori_barang.golongan_barang_id','=','golongan_barang.id')
      ->join('bidang_barang','inventori_barang.bidang_barang_id','=','bidang_barang.id')
      ->select(
        'inventori_barang.*',
        DB::raw('SUM(inventori_barang.quantity) as jumlah_barang'),
        DB::raw('SUM(inventori_barang.price) as total_harga'),
        'barang.code as barang_code',
        'barang.name as barang_name',
        'golongan_barang.name as golongan_barang_name',
        'bidang_barang.name as bidang_barang_name',
        'satuan_kerja.name as satuan_kerja_name')
      ->where('inventori_barang.golongan_barang_id',[1])
      ->where('inventori_barang.bidang_barang_id',$request->bidang)
      ->where('satuan_kerja_id',$request->satuan)
      ->groupBy('barang_code')
      ->orderBy('barang_code', 'asc')
      ->get();
      // dd($barangs);

    $pdf=PDF::loadView('pdf.inventaris7', ['barangs'=>$barangs])->setPaper('a4', 'landscape');
    return $pdf->stream('Inventaris_Bulanan.pdf');
  }
  public function getPDF(){
    $kategoris = Barang::all();
    $pdf=PDF::loadView('pdf.customer', ['kategoris' => $kategoris]);
    // return $pdf->download('customer.pdf');
    return $pdf->stream('customer.pdf');
  }
}
