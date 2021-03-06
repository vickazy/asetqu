@extends('layouts.level3')
@section('title','Mutasi barang - Asetqu')
@section('content')
    <div class="col-sm-10 content2">
      <h5>Daf Inventori Barang</h5>
      <div class="col-sm-6">
        <div class="form-group">
          <div class="input-group col-sm-8">
            <input type="text" class="form-control code" id="code" name="code" placeholder="Ketikkan kode/nama barang" required autofocus>
            <span class="input-group-btn">
              <button type="submit" class="btn btn-sm btn-primary">
                <i class="glyphicon glyphicon-search"></i>
              </button>
            </span>
          </div>
          <script type="text/javascript">
            $(document).ready(function(){
              $('#code').on("mouseover", function(event){
                //$("div").html("Event: " + event.type);
                $("#code").autocomplete({
                  source: "{{ route('barang.mutation.autocomplete') }}",
                  minLength: 3,
                  select: function(event, ui){
                    console.log(ui);
                    // $("#code").val(ui.item.id);
                  }
                });
              });
            });
          </script>
          <script>
            $( ".code" ).change(function(e) {
              var barang_code = e.target.value;
              $.get('/barang/ajax-mutation?barang_code=' + barang_code, function(data){
                console.log(data);
                $('.name').empty();
                $('.description').empty();
                $('.quantity').empty();
                $('.mutation_quantity').empty();
                $('.request_destroy_quantity').empty();
                $('.destroy_quantity').empty();
                $('.barang_id').empty();
                $('.barang_code').empty();
                $('.barang_name').empty();
                $('.brand').empty();
                $.each(data,function(index, bidangObj){
                  $('.name').append('<p>'+bidangObj.name+'</p>');
                  $('.description').append('<p>'+bidangObj.description+'</p>');
                  $('.quantity').append('<p>'+bidangObj.quantity+'</p>');
                  $('.mutation_quantity').append('<p>'+bidangObj.mutation_quantity+'</p>');
                  $('.request_destroy_quantity').append('<p>'+bidangObj.request_destroy_quantity+'</p>');
                  $('.destroy_quantity').append('<p>'+bidangObj.destroy_quantity+'</p>');
                  $('.barang_id').append('<input type="hidden" name="id" value="'+bidangObj.id+'">');
                  $('.barang_code').append('<input type="hidden" name="code" value="'+bidangObj.code+'">');
                  $('.barang_name').append('<input type="hidden" name="name" value="'+bidangObj.name+'">');
                  $('.brand').append('<input type="hidden" name="brand" value="'+bidangObj.brand+'">');
                });
              });
            });
          </script>
        </div>
        <table class="table table-condensed table-hover">
          <thead>
            <tr>
              <th class="col-sm-3">Entitas</th>
              <th class="col-sm-9">Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            <tr> <td>Nama</td> <td class="name"></td> </tr>
            <tr> <td>Deskripsi</td> <td class="description"></td> </tr>
            <tr> <td>Kuantitas</td> <td class="quantity"></td> </tr>
            <tr> <td>Mutasi</td> <td class="mutation_quantity"></td> </tr>
            <tr> <td>Usulan Dihapus</td> <td class="request_destroy_quantity"></td> </tr>
            <tr> <td>Dihapus</td> <td class="destroy_quantity"></td> </tr>
          </tbody>
        </table>
      </div>
      <div class="col-sm-6">
        <form class="form-horizontal" action="/barang/mutation/insert" method="POST">
          <h5>Tujuan Mutasi</h5>
          {{ csrf_field() }}
          <div class="form-group hidden">
            <label class="control-label col-sm-4" for="name">&nbsp;</label>
            <div class="col-sm-8 barang_id"></div>
          </div>
          <div class="form-group hidden">
            <label class="control-label col-sm-4" for="name">&nbsp;</label>
            <div class="col-sm-8 barang_code"></div>
          </div>
          <div class="form-group hidden">
            <label class="control-label col-sm-4" for="name">&nbsp;</label>
            <div class="col-sm-8 barang_name"></div>
          </div>
          <div class="form-group hidden">
            <label class="control-label col-sm-4" for="name">&nbsp;</label>
            <div class="col-sm-8 brand"></div>
          </div>
          <div class="form-group required">
            <label class="control-label col-sm-4" for="name">Quantitas</label>
            <div class="col-sm-8">
              <input type="number" class="form-control" id="text" name="quantity" value="" required>
            </div>
          </div>
          <div class="form-group required">
            <label class="control-label col-sm-4" for="name">Kondisi</label>
            <div class="col-sm-8">
              <select class="from-control" id="sel1" name="kondisi" required>
                <option value="">Pilih Kondisi</option>
                <optgroup label="---">
                  @foreach($kondisis as $kondisi)
                    <option value="{{ $kondisi->name }}">{{ $kondisi->name }}</option>
                  @endforeach
                </optgroup>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="control-label col-sm-4" for="name">Status</label>
            <div class="col-sm-8">
              <select class="from-control" id="sel1" name="status" required>
                <option value="">Pilih Status</option>
                <optgroup label="---">
                  @foreach($statuss as $status)
                    <option value="{{ $status->name }}">{{ $status->name }}</option>
                  @endforeach
                </optgroup>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="control-label col-sm-4" for="name">Pegawai / PIC</label>
            <div class="col-sm-8">
              <select class="from-control" id="sel1" name="pegawai" required>
                <option value="">Pilih PIC</option>
                <optgroup label="---">
                  @foreach($pegawais as $pegawai)
                    <option value="{{ $pegawai->id }}">{{ $pegawai->name }}</option>
                  @endforeach
                </optgroup>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="control-label col-sm-4" for="name">Ruangan</label>
            <div class="col-sm-8">
              <select class="from-control" id="sel1" name="ruangan" required>
                <option value="">Pilih Ruangan</option>
                <optgroup label="---">
                  @foreach($ruangans as $ruangan)
                    <option value="{{ $ruangan->id }}">{{ $ruangan->name }}</option>
                  @endforeach
                </optgroup>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-8">
              <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
              <a href="/barang" type="submit" class="btn btn-sm btn-default">Batal</a>
            </div>
          </div>
        </form>
      </div>
      <div class="col-sm-12">
        <div class="table-responsive">
          <p><strong>{{ $mutations->total() }} Barang</strong> | Tampil {{ $mutations->count() }} list</p>
          <table class="table table-condensed table-hover">
            <thead>
              <tr>
                <th>Kode Barang</th>
                <th>Nama</th>
                <th>Quantitas</th>
                <th>Kodisi</th>
                <th>Status</th>
                <th>Ruangan</th>
                <th>PIC</th>
                <th>Tanggal</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($mutations as $mutation)
                <tr>
                  <td>{{ $mutation->barang_code }}</td>
                  <td>{{ $mutation->barang_name }}</td>
                  <td>{{ $mutation->quantity}}</td>
                  <td>{{ $mutation->kondisi_name }}</td>
                  <td>{{ $mutation->status_name }}</td>
                  <td>{{ $mutation->ruangan_name }}</td>
                  <td>{{ $mutation->pegawai_name }}</td>
                  <td>{{ $mutation->created_at->format('Y-m-d') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <div class="col-sm-12 text-center">
            {{$mutations->links()}}
          </div>
        </div>
      </div>

    </div>
</div>
@endsection
