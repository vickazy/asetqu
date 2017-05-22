@extends('layouts.level3')
@section('title','Edit satuan kerja  - Asetqu')
@section('content')
    <div class="col-sm-10 content2">
      <h3>Edit Satuan Kerja</h3>
      <form class="form-horizontal" action="/bidang/satuankerja/{{$satuankerja->id}}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        <div class="form-group required">
          <label class="control-label col-sm-3" for="name">Kode</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" id="text" name="code" value="{{ $satuankerja->code }}" required>
          </div>
        </div>
        <div class="form-group required">
          <label class="control-label col-sm-3" for="name">Nama Satuan Kerja</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" id="text" name="name" value="{{ $satuankerja->name }}" required>
          </div>
        </div>
        <div class="form-group required">
          <label class="control-label col-sm-3" for="select">Pilih Unit Kerja</label>
          <div class="col-sm-9">
            <select class="from-control" id="sel1" name="unitkerja">
              <option value="{{ $satuankerja->unit_kerja_id }}">{{ $satuankerja->unit_kerja_name }}</option>
              <optgroup label="---">
                @foreach($unitkerjas as $unitkerja)
                  <option value="{{ $unitkerja->id }}">{{ $unitkerja->name }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            <a href="/bidang" type="submit" class="btn btn-sm btn-primary">Batal</a>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
