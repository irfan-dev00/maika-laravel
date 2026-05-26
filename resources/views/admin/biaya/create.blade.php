@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Tambah Biaya Harian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/biaya') }}">Biaya Harian</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>

        @include('admin.partials.flash')

        <form method="post" action="{{ url('/admin/biaya') }}">
            @csrf
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', $tanggal->toDateString()) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" value="{{ old('catatan') }}" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <a class="btn btn-outline-secondary w-100" href="{{ url('/admin/biaya/create?tanggal='.$tanggal->toDateString().'&copy_from='.$prevDate) }}">
                                Salin dari {{ $prevDate }}
                            </a>
                        </div>
                    </div>
                    @if ($copyFrom)
                        <div class="alert alert-info mt-3 mb-0 py-2 small">Item diisi dari biaya tanggal <strong>{{ $copyFrom }}</strong> (silakan koreksi).</div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Detail Item Biaya</span>
                    <button type="button" id="btn-add-row" class="btn btn-sm btn-outline-primary">+ Tambah baris</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="tbl-biaya">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Item</th>
                                    <th class="text-end" style="width:120px;">Qty</th>
                                    <th style="width:120px;">Satuan</th>
                                    <th class="text-end" style="width:160px;">Harga Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $i => $row)
                                    <tr>
                                        <td class="p-1"><input type="text" name="detail[{{ $i }}][nama_item]" value="{{ old('detail.'.$i.'.nama_item', $row['nama_item']) }}" class="form-control form-control-sm"></td>
                                        <td class="p-1"><input type="number" step="0.01" min="0" name="detail[{{ $i }}][qty]" value="{{ old('detail.'.$i.'.qty', $row['qty']) }}" class="form-control form-control-sm text-end"></td>
                                        <td class="p-1"><input type="text" name="detail[{{ $i }}][satuan]" value="{{ old('detail.'.$i.'.satuan', $row['satuan']) }}" class="form-control form-control-sm"></td>
                                        <td class="p-1"><input type="number" step="0.01" min="0" name="detail[{{ $i }}][harga_satuan]" value="{{ old('detail.'.$i.'.harga_satuan', $row['harga_satuan']) }}" class="form-control form-control-sm text-end"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a class="btn btn-outline-secondary" href="{{ url('/admin/biaya') }}">Kembali</a>
                    <span class="ms-auto small text-body-secondary align-self-center">Baris kosong akan diabaikan.</span>
                </div>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const tbody = document.querySelector('#tbl-biaya tbody');
            const btn = document.getElementById('btn-add-row');
            btn.addEventListener('click', function () {
                const idx = tbody.querySelectorAll('tr').length;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="p-1"><input type="text" name="detail[${idx}][nama_item]" class="form-control form-control-sm"></td>
                    <td class="p-1"><input type="number" step="0.01" min="0" name="detail[${idx}][qty]" class="form-control form-control-sm text-end"></td>
                    <td class="p-1"><input type="text" name="detail[${idx}][satuan]" class="form-control form-control-sm"></td>
                    <td class="p-1"><input type="number" step="0.01" min="0" name="detail[${idx}][harga_satuan]" class="form-control form-control-sm text-end"></td>
                `;
                tbody.appendChild(tr);
            });
        })();
    </script>
@endsection
