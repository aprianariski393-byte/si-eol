<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
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

                Section::make('Informasi Utama')
                    ->schema([
                        TextInput::make('asset_code')
                            ->label('Kode Aset')
                            ->default(function () {
                                $prefix = 'AST-' . date('Y') . '-';
                                $lastAsset = Asset::where('asset_code', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
                                if (!$lastAsset)
                                    return $prefix . '0001';
                                $parts = explode('-', $lastAsset->asset_code);
                                return $prefix . str_pad(((int) end($parts)) + 1, 4, '0', STR_PAD_LEFT);
                            })
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->columnSpanFull()
                            ->dehydrated()
                            ->maxLength(50)
                            ->prefixIcon('heroicon-m-qr-code')
                            ->helperText('Kode unik yang dihasilkan secara otomatis oleh sistem.'),

                        TextInput::make('name')
                            ->label('Nama Aset')
                            ->placeholder('Contoh: Laptop Asus ROG / Meja Kerja')
                            ->prefixIcon('heroicon-m-cube')
                            ->required()
                            ->maxLength(255),

                        Select::make('category')
                            ->label('Kategori')
                            ->placeholder('Pilih Kategori Aset...')
                            ->options([
                                'IT Equipment' => 'Peralatan IT',
                                'Software' => 'Perangkat Lunak',
                                'Furniture' => 'Mebel',
                                'Vehicles' => 'Kendaraan',
                                'Machinery' => 'Mesin',
                            ])
                            ->searchable()
                            ->native(false)
                            ->prefixIcon('heroicon-m-tag')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($get('purchase_date') && $state) {
                                    $years = match ($state) {
                                        'Software' => 1,
                                        'IT Equipment' => 5,
                                        'Furniture' => 10,
                                        'Vehicles' => 10,
                                        'Machinery' => 10,
                                        default => 5,
                                    };
                                    $set('eol_date', Carbon::parse($get('purchase_date'))->addYears($years)->toDateString());
                                }
                            }),

                        Select::make('department')
                            ->label('Departemen Pengguna')
                            ->placeholder('Pilih Departemen...')
                            ->options([
                                'IT' => 'IT',
                                'HR' => 'HR',
                                'Finance' => 'Finance',
                                'Operations' => 'Operations',
                                'Marketing' => 'Marketing',
                                'General' => 'General',
                            ])
                            ->native(false)
                            ->prefixIcon('heroicon-m-building-office-2')
                            ->searchable(),

                        TextInput::make('brand')
                            ->label('Merek / Tipe')
                            ->placeholder('Contoh: Lenovo Thinkpad T14')
                            ->prefixIcon('heroicon-m-swatch'),

                        TextInput::make('serial_number')
                            ->label('Serial Number / Lisensi')
                            ->placeholder('Masukkan SN atau Lisensi Key')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->unique(ignoreRecord: true),

                        Select::make('status')
                            ->label('Status Aset')
                            ->placeholder('Pilih Status Saat Ini...')
                            ->options([
                                'Active' => 'Aktif',
                                'Maintenance' => 'Dalam Perbaikan',
                                'End of Life' => 'Pensiun (EOL)',
                                'Disposed' => 'Dihapus',
                                'Lost' => 'Hilang',
                            ])
                            ->native(false)
                            ->prefixIcon('heroicon-m-check-badge')
                            ->default('Active')
                            ->required(),
                    ])
                    ->columns(2),

                Group::make([
                    Section::make('Siklus Hidup (End of Life)')
                        ->schema([
                            DatePicker::make('purchase_date')
                                ->label('Tanggal Pembelian')
                                ->placeholder('Pilih Tanggal Beli...')
                                ->default(now())
                                ->native(false)
                                ->displayFormat('d F Y')
                                ->prefixIcon('heroicon-m-calendar-days')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    if ($state && $get('category')) {
                                        $years = match ($get('category')) {
                                            'Software' => 1,
                                            'IT Equipment' => 5,
                                            'Furniture' => 10,
                                            'Vehicles' => 10,
                                            'Machinery' => 10,
                                            default => 5,
                                        };
                                        $set('eol_date', Carbon::parse($state)->addYears($years)->toDateString());
                                    }
                                }),

                            DatePicker::make('eol_date')
                                ->label('Tanggal End of Life (EOL)')
                                ->placeholder('Pilih Tanggal EOL...')
                                ->helperText('Batas waktu aman pemakaian / expired lisensi. Dapat diubah manual jika perlu.')
                                ->native(false)
                                ->displayFormat('d F Y')
                                ->prefixIcon('heroicon-m-exclamation-triangle'),
                        ])
                        ->columns(2),

                    Section::make('Tambahan')
                        ->schema([
                            Textarea::make('description')
                                ->label('Catatan Tambahan')
                                ->placeholder('Masukkan catatan khusus, kelengkapan, dll...')
                                ->rows(3),

                            FileUpload::make('attachments')
                                ->label('Lampiran File (Foto/Dokumen)')
                                ->multiple()
                                ->directory('asset-attachments')
                                ->panelLayout('grid'),
                        ])
                ])
            ]);
    }
}
