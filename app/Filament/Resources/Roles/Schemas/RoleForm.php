<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class RoleForm
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Schema $schema): Schema
    {
        // Group permissions by resource (Model name)
        $groupedPermissions = Permission::query()
            ->get()
            ->groupBy(function (Permission $permission) {
                $parts = explode(' ', $permission->name);

                if (count($parts) > 2) {
                    if ($parts[0] === 'View' && $parts[1] === 'Any') {
                        return Str::studly(implode(' ', array_slice($parts, 2)));
                    }

                    if ($parts[0] === 'Force' && $parts[1] === 'Delete') {
                        return Str::studly(implode(' ', array_slice($parts, 2)));
                    }

                    return Str::studly(implode(' ', array_slice($parts, 1)));
                }

                return Str::studly(end($parts));
            });

        $permissionFieldMap = $groupedPermissions->mapWithKeys(function ($permissions, $resource) {
            return [
                'permissions_' . Str::snake($resource) => $permissions->pluck('id')->values()->all(),
            ];
        });

        return $schema->components([

            /**
             * LEFT PANEL — ROLE INFO
             */
            Section::make(__('role.role_information')) // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                ->description(__('role.role_information_desc'))
                ->icon('heroicon-o-identification')
                ->schema([
                    TextInput::make('name') // TextInput: Komponen input teks biasa
                        ->label(__('role.role_name')) // label: Teks label yang ditampilkan untuk komponen ini
                        ->placeholder(__('role.role_name_placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                        ->required() // required: Menandakan bahwa field ini wajib diisi
                        ->minLength(3)
                        ->maxLength(45) // maxLength: Batas maksimal jumlah karakter
                        ->unique(ignoreRecord: true) // unique: Memastikan nilai unik di dalam database
                        ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                    Toggle::make('select_all') // Toggle: Komponen tombol on/off (boolean)
                        ->label(__('role.select_all')) // label: Teks label yang ditampilkan untuk komponen ini
                        ->helperText(__('role.enable_role')) // helperText: Teks bantuan kecil di bawah komponen
                        ->dehydrated(false) // dehydrated: Menentukan apakah data akan dikirim/disimpan ke database
                        ->live() // live: Merespon perubahan input secara real-time ke server
                        ->onIcon(HeroIcon::ShieldCheck)
                        ->offIcon(HeroIcon::ShieldExclamation)

                        // Saat form edit → cek apakah semua permission sudah tercentang
                        ->afterStateHydrated(function ($set, $record) use ($permissionFieldMap) {
                            if (!$record) {
                                return;
                            }

                            $selected = $record->permissions->pluck('id')->sort()->values()->all();
                            $all = collect($permissionFieldMap)->flatten()->sort()->values()->all();

                            $set('select_all', $selected === $all);
                        })

                        // Saat toggle diubah
                        ->afterStateUpdated(function (bool $state, callable $set) use ($permissionFieldMap) { // afterStateUpdated: Fungsi callback yang dijalankan setelah nilai input berubah
                            foreach ($permissionFieldMap as $field => $permissionIds) {
                                $set($field, $state ? $permissionIds : []);
                            }
                        }),
                ])
                ->columns(1) // columns: Menentukan jumlah grid/kolom
                ->columnSpan(1)
                ->collapsible(),

            /**
             * RIGHT PANEL — PERMISSIONS TABS
             */
            Tabs::make(__('role.permission'))
                ->contained()
                ->columnSpan(2)
                ->vertical()
                ->tabs(
                    $groupedPermissions
                        ->map(function ($permissions, string $resource) {
                            $fieldName = 'permissions_' . Str::snake($resource); // unique per tab
                
                            return Tab::make(Str::headline($resource))
                                ->icon(Heroicon::Key)
                                ->schema([
                                    Section::make(Str::headline($resource)) // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                                        ->description("App\\Models\\{$resource}")
                                        ->collapsible()
                                        ->schema([
                                            CheckboxList::make($fieldName)
                                                ->label(__('role.permission_name')) // label: Teks label yang ditampilkan untuk komponen ini
                                                ->relationship( // relationship: Mengambil data dari relasi model
                                                    'permissions',
                                                    'name',
                                                    modifyQueryUsing: fn($query) => $query->whereIn('id', $permissions->pluck('id'))
                                                )
                                                ->getOptionLabelFromRecordUsing(function ($record) {
                                                    $name = strtolower($record->name);
                                                    $map = [
                                                        'view any' => 'view_any',
                                                        'view' => 'view',
                                                        'create' => 'create',
                                                        'update' => 'update',
                                                        'delete' => 'delete',
                                                        'restore' => 'restore',
                                                        'force delete' => 'force_delete',
                                                    ];
                                                    $key = collect($map)->first(fn($value, $startsWith) => str_starts_with($name, $startsWith));
                                                    return $key ? __('role.' . $key) : __($record->name);
                                                })
                                                ->bulkToggleable()
                                                ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                                                ->gridDirection('row')
                                                ->columns([ // columns: Menentukan jumlah grid/kolom
                                                    'default' => 1,
                                                    'md' => 2,
                                                    'lg' => 3,
                                                ])
                                                ->live(), // live: Merespon perubahan input secara real-time ke server
                                        ]),
                                ]);
                        })
                        ->values()
                        ->all()
                ),
        ])->columns(3); // columns: Menentukan jumlah grid/kolom
    }

    /**
     * Method untuk merge semua permissions per tab sebelum disimpan
     */
    public static function mergePermissions(array $data): array
    {
        $allPermissions = collect($data)
            ->filter(fn($value, $key) => str_starts_with($key, 'permissions_'))
            ->flatten()
            ->unique() // unique: Memastikan nilai unik di dalam database
            ->values()
            ->all();

        return array_merge($data, ['permissions' => $allPermissions]);
    }
}
