<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Parkir Masuk - {{ $transaksi->kode_tiket }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', monospace;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 2rem;
        }

        .struk-wrapper {
            background: #fff;
            width: 320px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            overflow: hidden;
        }

        .struk-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);
            color: #fff;
            text-align: center;
            padding: 1.25rem 1rem;
        }

        .struk-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .struk-header p {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-top: 0.2rem;
        }

        .struk-body {
            padding: 1rem 1.25rem;
        }

        .struk-title {
            text-align: center;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #666;
            padding: 0.5rem 0;
            border-top: 2px dashed #e0e0e0;
            border-bottom: 2px dashed #e0e0e0;
            margin-bottom: 1rem;
        }

        .struk-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            margin-bottom: 0.5rem;
            gap: 0.5rem;
        }

        .struk-row .label {
            color: #888;
            min-width: 110px;
        }

        .struk-row .value {
            font-weight: 600;
            text-align: right;
            color: #222;
        }

        .struk-kode {
            text-align: center;
            margin: 1rem 0;
            background: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 6px;
            padding: 0.75rem;
        }

        .struk-kode .label {
            font-size: 0.7rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .struk-kode .kode {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e3a5f;
            letter-spacing: 0.1em;
            margin-top: 0.2rem;
        }

        .struk-footer {
            background: #f8f9fa;
            border-top: 2px dashed #e0e0e0;
            text-align: center;
            padding: 0.75rem 1rem;
            font-size: 0.72rem;
            color: #666;
            line-height: 1.6;
        }

        .btn-print {
            display: block;
            margin: 1.5rem auto 0;
            width: 320px;
            padding: 0.75rem;
            background: #2d6a9f;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-print:hover {
            background: #1e3a5f;
        }

        .btn-back {
            display: block;
            margin: 0.5rem auto 2rem;
            width: 320px;
            padding: 0.6rem;
            background: transparent;
            color: #555;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        /* CSS PRINT - Sembunyikan tombol saat cetak */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .struk-wrapper {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
                max-width: 80mm;
            }

            .btn-print,
            .btn-back {
                display: none !important;
            }

            @page {
                margin: 5mm;
                size: 80mm auto;
            }
        }
    </style>
</head>
<body>

<div>
    <div class="struk-wrapper">
        <div class="struk-header">
            <h2>&#9646; PARKIR CENTER &#9646;</h2>
            <p>Sistem Manajemen Parkir</p>
        </div>

        <div class="struk-body">
            <div class="struk-title">Tiket Masuk Parkir</div>

            <div class="struk-kode">
                <div class="label">Kode Tiket</div>
                <div class="kode">{{ $transaksi->kode_tiket }}</div>
            </div>

            <div class="struk-row">
                <span class="label">Tanggal</span>
                <span class="value">{{ $transaksi->waktu_masuk->format('d/m/Y') }}</span>
            </div>
            <div class="struk-row">
                <span class="label">Waktu Masuk</span>
                <span class="value">{{ $transaksi->waktu_masuk->format('H:i:s') }}</span>
            </div>
            <div class="struk-row">
                <span class="label">Plat Nomor</span>
                <span class="value" style="font-size: 1rem;">{{ $transaksi->plat_nomor }}</span>
            </div>
            <div class="struk-row">
                <span class="label">Jenis Kendaraan</span>
                <span class="value">{{ $transaksi->kategoriKendaraan->nama_kategori ?? '-' }}</span>
            </div>
            <div class="struk-row">
                <span class="label">Area Parkir</span>
                <span class="value">{{ $transaksi->areaParkir->nama_area ?? '-' }}</span>
            </div>
            <div class="struk-row">
                <span class="label">Petugas</span>
                <span class="value">{{ $transaksi->petugasMasuk->nama ?? '-' }}</span>
            </div>
        </div>

        <div class="struk-footer">
            Simpan tiket ini dengan baik.<br>
            Kehilangan tiket dikenakan biaya tambahan.<br>
            <strong>Terima kasih telah menggunakan parkir kami.</strong>
        </div>
    </div>

    <button class="btn-print" onclick="window.print()">
        &#128438; Cetak Struk Masuk
    </button>
    <a href="{{ route('petugas.transaksi.masuk') }}" class="btn-back">
        &#8592; Kembali ke Transaksi Masuk
    </a>
</div>

</body>
</html>
