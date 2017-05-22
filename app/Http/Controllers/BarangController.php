<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Models\Barang;
use App\Models\Golongan;
use App\Models\Bidang;
use App\Models\Kelompok;
use App\Models\Subkelompok;
use App\Models\Ruangan;
use App\Models\Pegawai;
use App\Models\Satuan;
use App\Models\Kondisi;
use App\Models\Status;
use App\Models\Mutation;
use App\Models\Receipt;

class BarangController extends Controller
{
  public function __construct(){
      $this->middleware('auth');
  }
  public function index(){
    $barangs = Barang::orderBy('created_at', 'desc')->paginate(13);
    return view('barang', ['barangs' => $barangs]);
  }
  public function view($id){
    $barang = Barang::join('ruangan','ruangan_id','=','ruangan.id')
                ->join('pegawai','pegawai_id','=','pegawai.id')
                ->join('golongan_barang','golongan_barang_id','=','golongan_barang.id')
                ->join('bidang_barang','bidang_barang_id','=','bidang_barang.id')
                ->join('kelompok_barang','kelompok_barang_id','=','kelompok_barang.id')
                ->join('sub_kelompok_barang','sub_kelompok_barang_id','=','sub_kelompok_barang.id')
                ->join('mutation','mutation_id','=','mutation.id')
                ->select('barang.*','sub_kelompok_barang.name as sub_kelompok_barang_name', 'kelompok_barang.name as kelompok_barang_name', 'bidang_barang.name as bidang_barang_name', 'golongan_barang.name as golongan_barang_name', 'ruangan.name as ruangan_name', 'pegawai.name as pegawai_name')
                ->find($id);
    //dd($barang);
    return view('barangview', ['barang' => $barang]);
  }
  public function edit($id){
    //dd($id);
    // $kategoris = Kategori::orderBy('name', 'asc')->get();
    $golongans = Golongan::orderBy('name', 'asc')->get();
    $bidangs = Bidang::orderBy('name', 'asc')->get();
    $kelompoks = Kelompok::orderBy('name', 'asc')->get();
    $subkelompoks = Subkelompok::orderBy('name', 'asc')->get();
    $ruangans = Ruangan::orderBy('name', 'asc')->get();
    $pegawais = Pegawai::orderBy('name', 'asc')->get();
    $satuans = Satuan::orderBy('name', 'asc')->get();
    $kondisis = Kondisi::orderBy('name', 'asc')->get();
    $statuss = Status::orderBy('name', 'asc')->get();
    $barang = Barang::join('ruangan','ruangan_id','=','ruangan.id')
                ->join('pegawai','pegawai_id','=','pegawai.id')
                ->join('golongan_barang','golongan_barang_id','=','golongan_barang.id')
                ->join('bidang_barang','bidang_barang_id','=','bidang_barang.id')
                ->join('kelompok_barang','kelompok_barang_id','=','kelompok_barang.id')
                ->join('sub_kelompok_barang','sub_kelompok_barang_id','=','sub_kelompok_barang.id')
                ->join('mutation','mutation_id','=','mutation.id')
                ->select('barang.*','sub_kelompok_barang.name as sub_kelompok_barang_name', 'kelompok_barang.name as kelompok_barang_name', 'bidang_barang.name as bidang_barang_name', 'golongan_barang.name as golongan_barang_name', 'ruangan.name as ruangan_name', 'pegawai.name as pegawai_name')
                ->find($id);
    //$barang = Barang::find($id);
    //dd($barang);
    return view('barangedit', ['barang' => $barang, 'ruangans' => $ruangans, 'pegawais' => $pegawais, 'satuans' => $satuans, 'kondisis' => $kondisis, 'statuss' => $statuss, 'golongans' => $golongans, 'bidangs' => $bidangs, 'kelompoks' => $kelompoks, 'subkelompoks' => $subkelompoks]);
  }
  public function bidang_barang(Request $request){
    //dd($request);
    $bidangbarangs = Bidang::where('golongan_barang_id', $request->bidang_id)->get();
    return \Response::json($bidangbarangs);
  }
  public function kelompok_barang(Request $request){
    //dd($request);
    $kelompokbarangs = Kelompok::where('bidang_barang_id', $request->kelompok_id)->get();
    return \Response::json($kelompokbarangs);
  }
  public function sub_kelompok_barang(Request $request){
    //dd($request);
    $subkelompokbarangs = Subkelompok::where('kelompok_barang_id', $request->subkelompok_id)->get();
    return \Response::json($subkelompokbarangs);
  }
  public function update($id, Request $request){
    //dd($request);
    $barang = Barang::find($id);
    $barang->picture = $request->picture;
    $barang->code = $request->code;
    $barang->number = $request->number;
    $barang->name = $request->name;
    $barang->description = $request->description;
    $barang->price = $request->price;
    $barang->quantity = $request->quantity;
    $barang->kondisi_name = $request->kondisi;
    $barang->tujuan = $request->tujuan;
    $barang->source = $request->source;
    $barang->brand = $request->brand;
    $barang->size = $request->size;
    $barang->satuan_name = $request->satuan;
    $barang->status_name = $request->status;
    $barang->color = $request->color;
    $barang->material = $request->material;
    $barang->year_created = $request->year_created;
    $barang->year_buy = $request->year_buy;
    $barang->ruangan_id = $request->ruangan;
    $barang->golongan_barang_id = $request->golongan;
    $barang->bidang_barang_id = $request->bidang;
    $barang->kelompok_barang_id = $request->kelompok;
    $barang->sub_kelompok_barang_id = $request->subkelompok;
    $barang->save();
    return redirect('/barang');
  }
  public function add(){
    $satuans = Satuan::orderBy('name', 'asc')->get();
    $kondisis = Kondisi::orderBy('name', 'asc')->get();
    $statuss = Status::orderBy('name', 'asc')->get();
    $pegawais = Pegawai::orderBy('name', 'asc')->get();
    $ruangans = Ruangan::orderBy('name', 'asc')->get();
    $golongans = Golongan::orderBy('name', 'asc')->get();
    return view('barangadd',['satuans' => $satuans, 'kondisis' => $kondisis,  'statuss' => $statuss, 'ruangans' => $ruangans, 'pegawais' => $pegawais, 'golongans' => $golongans]);
  }
  public function insert(Request $request){
    //dd($request);
    Barang::create([
      'picture' => $request->picture,
      'code' => $request->code,
      'name' => $request->name,
      'description' => $request->description,
      'tujuan' => $request->tujuan,
      'price' => $request->price,
      'quantity' => $request->quantity,
      'satuan_name' => $request->satuan,
      'kondisi_name' => $request->kondisi,
      'brand' => $request->brand,
      'color' => $request->color,
      'material' => $request->material,
      'number' => $request->number,
      'source' => $request->source,
      'status_name' => $request->status,
      'ruangan_id' => $request->ruangan,
      'year_created' => $request->year_created,
      'year_buy' => $request->year_buy,
      'pegawai_id' => $request->pegawai,
      'golongan_barang_id' => $request->golongan,
      'bidang_barang_id' => $request->bidang,
      'kelompok_barang_id' => $request->kelompok,
      'sub_kelompok_barang_id' => $request->subkelompok,
      'mutation_id' => 1
    ]);
    return redirect('/barang');
  }
  public function delete($id){
    Barang::destroy($id);
    return redirect('/barang');
  }
  public function mutation_view(){
    $golongans = Golongan::orderBy('name', 'asc')->get();
    $bidangs = Bidang::orderBy('name', 'asc')->get();
    $kelompoks = Kelompok::orderBy('name', 'asc')->get();
    $subkelompoks = Subkelompok::orderBy('name', 'asc')->get();
    $ruangans = Ruangan::orderBy('name', 'asc')->get();
    $pegawais = Pegawai::orderBy('name', 'asc')->get();
    $satuans = Satuan::orderBy('name', 'asc')->get();
    $kondisis = Kondisi::orderBy('name', 'asc')->get();
    $statuss = Status::orderBy('name', 'asc')->get();
    return view('mutationedit',['ruangans' => $ruangans, 'pegawais' => $pegawais]);
  }
  public function autocomplete(Request $request){
    //call by non ajax autocomplete
    // if ($request->ajax()) {
      //dd($request);
      $barangs = Barang::join('golongan_barang','golongan_barang_id','=','golongan_barang.id')
                  ->join('ruangan','ruangan_id','=','ruangan.id')
                  ->join('pegawai','pegawai_id','=','pegawai.id')
                  ->select('barang.*','golongan_barang.name as golongan_barang_name','ruangan.name as ruangan_name','pegawai.name as pegawai_name')
                  ->where(function($query) use ($request){
                    $query->orWhere('barang.id','like','%'.$request->term.'%');
                    $query->orWhere('barang.name','like','%'.$request->term.'%');
                })
                ->orderBy('code', 'asc')
                ->take(5)
                ->get();
      //convert to Json
      $results = [];
      foreach ($barangs as $barang) {
        $results[] = ['id' => $barang->code, 'value' => $barang->name];
      }
      //dd($results);
      //return \Response::json($results);
      return response()->json($results);
    // }
  }
}