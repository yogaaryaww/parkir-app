<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\KategoriKendaraan;
use App\Models\TarifParkir;
use App\Models\AreaParkir;
use App\Models\TransaksiParkir;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TransaksiKendaraanRelasiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_model_relationships_work_bidirectionally(): void
    {
        $kategori = KategoriKendaraan::first();
        if (!$kategori) {
            $kategori = KategoriKendaraan::create(['nama_kategori' => 'Test Motor']);
        }

        $kendaraan = Kendaraan::firstOrCreate(
            ['plat_nomor' => 'B 7777 TST'],
            [
                'kategori_kendaraan_id' => $kategori->id,
                'nama_pemilik' => 'Test Owner',
                'keterangan' => 'Test Description',
            ]
        );

        $area = AreaParkir::first();
        $user = User::first();

        $transaksi = TransaksiParkir::create([
            'kode_tiket' => 'PRK-TEST-' . uniqid(),
            'plat_nomor' => $kendaraan->plat_nomor,
            'kendaraan_id' => $kendaraan->id,
            'kategori_kendaraan_id' => $kendaraan->kategori_kendaraan_id,
            'area_parkir_id' => $area->id,
            'waktu_masuk' => Carbon::now(),
            'status' => 'masuk',
            'petugas_masuk_id' => $user->id,
        ]);

        // Test belongsTo relation
        $this->assertNotNull($transaksi->kendaraan);
        $this->assertEquals($kendaraan->id, $transaksi->kendaraan->id);
        $this->assertEquals($kendaraan->plat_nomor, $transaksi->kendaraan->plat_nomor);

        // Test hasMany relation
        $this->assertTrue($kendaraan->transaksiParkir->contains('id', $transaksi->id));

        // Clean up test transaction
        $transaksi->delete();
    }

    public function test_transaksi_masuk_with_registered_kendaraan_succeeds(): void
    {
        $petugas = User::where('role', 'petugas')->first() ?? User::create([
            'nama' => 'Petugas Test',
            'username' => 'petugas_test',
            'email' => 'petugas_test@parkir.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
            'status' => 'aktif',
        ]);

        $kendaraan = Kendaraan::first();
        $area = AreaParkir::first();

        // Pastikan tidak ada transaksi aktif untuk kendaraan ini sebelum test
        TransaksiParkir::where('kendaraan_id', $kendaraan->id)->where('status', 'masuk')->delete();

        $response = $this->actingAs($petugas)->post(route('petugas.transaksi.masuk.store'), [
            'plat_nomor' => $kendaraan->plat_nomor,
            'area_parkir_id' => $area->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $transaksi = TransaksiParkir::where('kendaraan_id', $kendaraan->id)
            ->where('status', 'masuk')
            ->latest()
            ->first();

        $this->assertNotNull($transaksi);
        $this->assertEquals($kendaraan->id, $transaksi->kendaraan_id);
        $this->assertEquals($kendaraan->plat_nomor, $transaksi->plat_nomor);
        $this->assertEquals($kendaraan->kategori_kendaraan_id, $transaksi->kategori_kendaraan_id);
    }

    public function test_transaksi_masuk_with_new_unregistered_kendaraan_registers_and_creates_transaction(): void
    {
        $petugas = User::where('role', 'petugas')->first();
        $kategori = KategoriKendaraan::first();
        $area = AreaParkir::first();
        $platBaru = 'B ' . rand(1000, 9999) . ' ' . strtoupper(Str::random(3));

        $response = $this->actingAs($petugas)->post(route('petugas.transaksi.masuk.store'), [
            'plat_nomor' => $platBaru,
            'kategori_kendaraan_id' => $kategori->id,
            'nama_pemilik' => 'Pemilik Baru',
            'keterangan' => 'Registrasi On The Fly',
            'area_parkir_id' => $area->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan master kendaraan baru terbuat
        $kendaraanBaru = Kendaraan::where('plat_nomor', $platBaru)->first();
        $this->assertNotNull($kendaraanBaru);
        $this->assertEquals('Pemilik Baru', $kendaraanBaru->nama_pemilik);
        $this->assertEquals($kategori->id, $kendaraanBaru->kategori_kendaraan_id);

        // Pastikan transaksi parkir terbuat dengan kendaraan_id yang tepat
        $transaksi = TransaksiParkir::where('kendaraan_id', $kendaraanBaru->id)
            ->where('status', 'masuk')
            ->first();

        $this->assertNotNull($transaksi);
        $this->assertEquals($kendaraanBaru->id, $transaksi->kendaraan_id);
        $this->assertEquals($platBaru, $transaksi->plat_nomor);
        $this->assertEquals($kategori->id, $transaksi->kategori_kendaraan_id);
    }

    public function test_transaksi_masuk_with_new_kendaraan_requires_owner_and_category(): void
    {
        $petugas = User::where('role', 'petugas')->first();
        $area = AreaParkir::first();
        $platBaru = 'B 9999 INV';

        // Jika plat belum ada dan petugas tidak mengisi pemilik/kategori, validasi harus gagal
        $response = $this->actingAs($petugas)->post(route('petugas.transaksi.masuk.store'), [
            'plat_nomor' => $platBaru,
            'area_parkir_id' => $area->id,
        ]);

        $response->assertSessionHasErrors(['kategori_kendaraan_id', 'nama_pemilik']);
        $this->assertDatabaseMissing('kendaraan', ['plat_nomor' => $platBaru]);
    }

    public function test_transaksi_masuk_duplicate_active_fails(): void
    {
        $petugas = User::where('role', 'petugas')->first();
        $kendaraan = Kendaraan::first();
        $area = AreaParkir::first();

        // Kendaraan sudah aktif parkir dari test sebelumnya
        $response = $this->actingAs($petugas)->post(route('petugas.transaksi.masuk.store'), [
            'plat_nomor' => $kendaraan->plat_nomor,
            'area_parkir_id' => $area->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_transaksi_keluar_calculates_tariff_and_completes_properly(): void
    {
        $petugas = User::where('role', 'petugas')->first();
        $kendaraan = Kendaraan::first();

        $transaksi = TransaksiParkir::where('kendaraan_id', $kendaraan->id)
            ->where('status', 'masuk')
            ->first();

        $this->assertNotNull($transaksi);

        // Search keluar form
        $responseSearch = $this->actingAs($petugas)->get(route('petugas.transaksi.keluar', ['q' => $transaksi->kode_tiket]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee($kendaraan->plat_nomor);

        // Process exit with payment
        $responseProcess = $this->actingAs($petugas)->post(route('petugas.transaksi.keluar.process', $transaksi->id), [
            'bayar' => 50000,
        ]);

        $responseProcess->assertRedirect(route('petugas.struk.keluar', $transaksi->id));
        $responseProcess->assertSessionHas('success');

        $transaksi->refresh();
        $this->assertEquals('selesai', $transaksi->status);
        $this->assertGreaterThan(0, $transaksi->total_bayar);
        $this->assertEquals(50000, (float)$transaksi->bayar);
        $this->assertEquals(50000 - $transaksi->total_bayar, (float)$transaksi->kembalian);
    }

    public function test_struk_masuk_and_keluar_render_with_relationship(): void
    {
        $petugas = User::where('role', 'petugas')->first();
        $transaksiMasuk = TransaksiParkir::where('status', 'masuk')->latest()->first() ?? TransaksiParkir::latest()->first();
        $transaksiSelesai = TransaksiParkir::where('status', 'selesai')->latest()->first() ?? TransaksiParkir::latest()->first();

        $this->assertNotNull($transaksiMasuk);
        $this->assertNotNull($transaksiSelesai);

        $responseMasuk = $this->actingAs($petugas)->get(route('petugas.struk.masuk', $transaksiMasuk->id));
        $responseMasuk->assertStatus(200);
        $responseMasuk->assertSee($transaksiMasuk->kode_tiket);
        $responseMasuk->assertSee($transaksiMasuk->kendaraan->plat_nomor);

        $responseKeluar = $this->actingAs($petugas)->get(route('petugas.struk.keluar', $transaksiSelesai->id));
        $responseKeluar->assertStatus(200);
        $responseKeluar->assertSee($transaksiSelesai->kode_tiket);
        $responseKeluar->assertSee($transaksiSelesai->kendaraan->plat_nomor);
    }

    public function test_owner_rekap_and_print_renders_with_relationships(): void
    {
        $owner = User::where('role', 'owner')->first() ?? User::create([
            'nama' => 'Owner Test',
            'username' => 'owner_test',
            'email' => 'owner_test@parkir.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'status' => 'aktif',
        ]);

        $responseDashboard = $this->actingAs($owner)->get(route('owner.dashboard'));
        $responseDashboard->assertStatus(200);

        $responsePrint = $this->actingAs($owner)->get(route('owner.rekap.print'));
        $responsePrint->assertStatus(200);
    }

    public function test_admin_cannot_delete_kendaraan_with_active_or_historical_transactions(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::create([
            'nama' => 'Admin Test',
            'username' => 'admin_test',
            'email' => 'admin_test@parkir.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $kendaraan = Kendaraan::whereHas('transaksiParkir')->first();
        $this->assertNotNull($kendaraan);

        $response = $this->actingAs($admin)->delete(route('admin.kendaraan.destroy', $kendaraan->id));
        $response->assertRedirect(route('admin.kendaraan.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('kendaraan', ['id' => $kendaraan->id]);
    }
}
