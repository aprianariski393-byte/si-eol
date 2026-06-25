<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section; // <-- Sesuai SOP Filament 4
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // --- KELOMPOK 1: IDENTITAS ASET ---
                Section::make('Identitas Aset')
                    ->description('Informasi dasar dan spesifikasi aset.')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->schema([
                        TextInput::make('asset_code')
                            ->label('Kode Aset')
                            ->placeholder('Contoh: AST-2026-0001')
                            ->default(function () {
                                // 1. Tentukan Prefix berdasarkan tahun saat ini (Contoh: AST-2026-)
                                $prefix = 'AST-' . date('Y') . '-';

                                // 2. Cari data aset terakhir yang dibuat pada tahun ini
                                $lastAsset = Asset::where('asset_code', 'like', $prefix . '%')
                                    ->orderBy('id', 'desc')
                                    ->first();

                                // 3. Jika belum ada data sama sekali di tahun ini, mulai dari 0001
                                if (!$lastAsset) {
                                    return $prefix . '0001';
                                }

                                // 4. Jika sudah ada, ekstrak angka terakhirnya
                                // Contoh: dari "AST-2026-0045" kita pecah dan ambil "0045"
                                $lastCode = $lastAsset->asset_code;
                                $parts = explode('-', $lastCode);
                                $lastNumber = (int) end($parts); // Ubah "0045" jadi integer 45
                    
                                // 5. Tambahkan 1, lalu padukan kembali dengan nol di depan agar minimal 4 digit
                                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Menjadi "0046"
                    
                                return $prefix . $newNumber;
                            })
                            ->helperText('Sistem membuat kode ini otomatis berurutan dari data terakhir, namun Anda dapat mengubahnya.')
                            ->prefixIcon('heroicon-o-qr-code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        TextInput::make('name')
                            ->label('Nama Aset')
                            ->placeholder('Contoh: Server HP ProLiant / Lisensi Windows 11')
                            ->helperText('Masukkan nama perangkat atau lisensi dengan jelas dan deskriptif.')
                            ->required()
                            ->maxLength(255),

                        Select::make('asset_type')
                            ->label('Tipe Aset')
                            ->placeholder('Pilih tipe aset...')
                            ->options([
                                'Hardware' => 'Hardware (Perangkat Keras)',
                                'Software' => 'Software (Perangkat Lunak)'
                            ])
                            ->default('Hardware')
                            ->helperText('Pilih "Software" untuk memunculkan panel detail lisensi langganan.')
                            ->live() // Memicu perubahan UI saat diganti
                            ->required(),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Kategori')
                            ->placeholder('Cari atau pilih kategori...')
                            ->helperText('Kelompok aset (misal: IT Equipment, Furniture, dll).')
                            ->searchable()
                            ->preload(),

                        Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->label('Vendor / Pemasok')
                            ->placeholder('Cari atau pilih vendor...')
                            ->helperText('Perusahaan atau toko tempat aset ini dibeli.')
                            ->searchable()
                            ->preload(),

                        TextInput::make('brand')
                            ->label('Merek')
                            ->placeholder('Contoh: Lenovo, Microsoft, Cisco')
                            ->helperText('Merek dagang pembuat aset tersebut.'),

                        TextInput::make('model_number')
                            ->label('Model / Versi')
                            ->placeholder('Contoh: T14 Gen 3 / v2024')
                            ->helperText('Tipe spesifik atau versi dari merek di atas.'),

                        TextInput::make('serial_number')
                            ->label('Serial Number / Lisensi Key')
                            ->placeholder('Masukkan SN atau Key Unik (Misal: PF-12345)')
                            ->helperText('Biasanya tercetak di bagian bawah fisik perangkat atau di email pembelian software.')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // --- KELOMPOK 2: KHUSUS SOFTWARE (DINAMIS) ---
                Section::make('Detail Lisensi Software')
                    ->description('Atur masa berlaku langganan perangkat lunak (SaaS).')
                    ->icon(Heroicon::OutlinedComputerDesktop)
                    ->schema([
                        Toggle::make('is_subscription')
                            ->label('Apakah Berlangganan (SaaS)?')
                            ->helperText('Aktifkan jika perangkat lunak ini dibayar rutin (bulanan/tahunan).')
                            ->live()
                            ->inline(false),

                        DatePicker::make('subscription_expiry')
                            ->label('Tanggal Kadaluarsa Langganan')
                            ->placeholder('Pilih tanggal kadaluarsa...')
                            ->helperText('Sistem akan memberikan notifikasi otomatis sebelum tanggal ini terlewati.')
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->visible(fn(Get $get) => $get('is_subscription'))
                            ->required(fn(Get $get) => $get('is_subscription')),
                    ])
                    ->columns(2)
                    ->visible(fn(Get $get) => $get('asset_type') === 'Software')
                    ->columnSpanFull(),

                // --- KELOMPOK 3: FINANSIAL & LIFECYCLE ---
                Section::make('Finansial & Siklus Hidup (Lifecycle)')
                    ->description('Data nilai aset dan batas waktu pemakaian idealnya.')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->schema([
                        DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->placeholder('Pilih tanggal beli...')
                            ->helperText('Tanggal faktur atau nota pembelian aset.')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->prefixIcon('heroicon-o-shopping-cart')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state && $get('useful_life_years')) {
                                    $set('eol_date', Carbon::parse($state)->addYears((int)$get('useful_life_years'))->toDateString());
                                }
                            }),

                        TextInput::make('purchase_cost')
                            ->label('Harga Beli')
                            ->placeholder('Contoh: 15000000')
                            ->helperText('Masukkan angka saja tanpa titik/koma pemisah ribuan.')
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(999999999999.99),

                        TextInput::make('useful_life_years')
                            ->label('Umur Ekonomis')
                            ->placeholder('Contoh: 5')
                            ->helperText('Perkiraan umur alat sebelum nilainya habis (depresiasi).')
                            ->numeric()
                            ->suffix('Tahun')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state && $get('purchase_date')) {
                                    $set('eol_date', Carbon::parse($get('purchase_date'))->addYears((int)$state)->toDateString());
                                }
                            }),

                        DatePicker::make('eol_date')
                            ->label('Tanggal EOL (End of Life)')
                            ->placeholder('Pilih tanggal perkiraan rusak...')
                            ->helperText('Batas waktu aman penggunaan alat. Lewat dari ini aset sebaiknya diganti.')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->prefixIcon('heroicon-o-shield-exclamation'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // --- KELOMPOK 4: OPERASIONAL & STATUS ---
                Section::make('Lokasi & Status Operasional')
                    ->description('Tentukan di mana aset digunakan dan kondisinya saat ini.')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->schema([
                        Select::make('status_id')
                            ->relationship('status', 'name')
                            ->label('Status Aset')
                            ->placeholder('Pilih kondisi saat ini...')
                            ->helperText('Misal: Tersedia, Sedang Digunakan, atau Rusak.')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('location_id')
                            ->relationship('location', 'name')
                            ->label('Lokasi')
                            ->placeholder('Pilih gedung / ruangan...')
                            ->helperText('Pilih lokasi fisik tempat aset ini diletakkan.')
                            ->searchable()
                            ->preload(),

                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->label('Departemen Pengguna')
                            ->placeholder('Pilih departemen / divisi...')
                            ->helperText('Divisi atau tim yang diizinkan menggunakan aset ini.')
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_critical')
                            ->label('Aset Kritis (Critical Asset)?')
                            ->helperText('Tandai aset ini jika kerusakannya dapat menghentikan operasional pabrik/kantor secara fatal.')
                            ->onColor('danger')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // --- KELOMPOK 5: CATATAN ---
                Section::make('Catatan Tambahan')
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi Aset')
                            ->hiddenLabel() // Label disembunyikan agar bersih
                            ->placeholder('Tulis spesifikasi tambahan, kelengkapan aksesoris, atau riwayat catatan khusus di sini...')
                            ->helperText('Bisa dikosongkan jika tidak ada detail tambahan.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
