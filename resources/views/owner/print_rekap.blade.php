<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Transaksi Parkir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
            padding: 2rem;
            color: #333;
            font-size: 0.875rem;
        }

        .print-container {
            background: #fff;
            max-width: 960px;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .print-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);
            color: #fff;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .print-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .print-header p {
            font-size: 0.8rem;
            opacity: 0.85;
            margin-top: 0.25rem;
        }

        .print-header .period {
            text-align: right;
            font-size: 0.8rem;
        }

        .print-body {
            padding: 1.5rem 2rem;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            border-left: 4px solid #2d6a9f;
            text-align: center;
        }

        .summary-card.success { border-left-color: #15803d; }
        .summary-card.primary { border-left-color: #2d6a9f; }
        .summary-card .number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e3a5f;
        }
        .summary-card .label {
            font-size: 0.75rem;
            color: #666;
            margin-top: 0.2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        thead th {
            background: #1e3a5f;
            color: #fff;
            padding: 0.5rem 0.6rem;
            font-size: 0.78rem;
            font-weight: 600;
            text-align: left;
        }

        tbody td {
            padding: 0.45rem 0.6rem;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) { background: #f8f9fa; }

        .badge-selesai {
            background: #d1fae5;
            color: #065f46;
            padding: 0.15rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-masuk {
            background: #fef3c7;
            color: #92400e;
            padding: 0.15rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .print-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: #666;
        }

        .btn-row {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin: 1.5rem 0;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: none;
        }

        .btn-print { background: #1e3a5f; color: #fff; }
        .btn-back { background: transparent; color: #555; border: 1px solid #ccc; }

        @media print {
            body { background: #fff; padding: 0; }
            .print-container { box-shadow: none; border-radius: 0; max-width: 100%; }
            .btn-row { display: none !important; }
            @page { margin: 10mm; size: A4 landscape; }
        }
    </style>
</head>
<body>

<div class="btn-row">
    <button class="btn btn-print" onclick="window.print()">&#128438; Cetak Laporan</button>
    <a href="{{ route('owner.dashboard') }}" class="btn btn-back">&#8592; Kembali</a>
</div>

<div class="print-container">
    <div class="print-header">
        <div>
            <h2>REKAP TRANSAKSI PARKIR</h2>
            <p>Laporan Pendapatan & Data Transaksi</p>
            @if($kategoriSelected)
                <p>Kategori: {{ $kategoriSelected->nama_kategori }}</p>
            @endif
        </div>
        <div class="period">
            <div><strong>Periode:</strong></div>
            <div>{{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }}</div>
            <div>s.d.</div>
            <div>{{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}</div>
            <div style="margin-top:0.5rem; font-size:0.7rem; opacity:0.7;">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="print-body">
        {{-- Ringkasan --}}
        <div class="summary-cards">
            <div class="summary-card">
                <div class="number">{{ $totalTransaksi }}</div>
                <div class="label">Total Transaksi</div>
            </div>
            <div class="summary-card success">
                <div class="number">{{ $totalSelesai }}</div>
                <div class="label">Transaksi Selesai</div>
            </div>
            <div class="summary-card primary">
                <div class="number">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="label">Total Pendapatan</div>
            </div>
        </div>

        {{-- Tabel Detail --}}
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Tiket</th>
                    <th>Plat Nomor</th>
                    <th>Kategori</th>
                    <th>Area</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Durasi</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiList as $i => $t)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $t->kode_tiket }}</strong></td>
                        <td><strong>{{ $t->kendaraan->plat_nomor ?? $t->plat_nomor }}</strong></td>
                        <td>{{ $t->kendaraan->kategoriKendaraan->nama_kategori ?? $t->kategoriKendaraan->nama_kategori ?? '-' }}</td>
                        <td>{{ $t->areaParkir->nama_area ?? '-' }}</td>
                        <td>{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                        <td>{{ $t->waktu_keluar ? $t->waktu_keluar->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $t->durasi_jam > 0 ? $t->durasi_jam . ' jam' : '-' }}</td>
                        <td>
                            @if($t->total_bayar > 0)
                                <strong>Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</strong>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($t->status === 'selesai')
                                <span class="badge-selesai">Selesai</span>
                            @else
                                <span class="badge-masuk">Parkir</span>
                            @endif
                        </td>
                        <td>{{ $t->petugasMasuk->nama ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align:center; color:#666; padding: 1rem;">
                            Tidak ada data pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="print-footer">
        <div>Total Data: <strong>{{ $transaksiList->count() }}</strong> transaksi</div>
        <div>Total Pendapatan: <strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></div>
        <div>Parkir Center &copy; {{ date('Y') }}</div>
    </div>
</div>

</body>
</html>
