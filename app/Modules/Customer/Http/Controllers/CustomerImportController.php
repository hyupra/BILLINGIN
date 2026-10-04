<?php

namespace App\Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerImportController extends Controller
{
    public function create()
    {
        return view('mockup.dashboard.customers-import');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $normalizedHeader = array_map(fn ($h) => mb_strtolower(trim((string) $h)), $header ?: []);

        $required = ['nama', 'wa', 'paket', 'username_pppoe'];
        $missing = array_diff($required, $normalizedHeader);

        if ($missing !== []) {
            fclose($handle);

            return back()->withErrors([
                'csv_file' => 'Kolom wajib tidak ada di header CSV: '.implode(', ', $missing).'.',
            ]);
        }

        $columnIndex = array_flip($normalizedHeader);
        $packagesByName = Package::where('is_active', true)->get()->keyBy(fn ($p) => mb_strtolower($p->name));

        $rows = [];
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            $name = trim((string) ($row[$columnIndex['nama']] ?? ''));
            $phone = trim((string) ($row[$columnIndex['wa']] ?? ''));
            $packageName = trim((string) ($row[$columnIndex['paket']] ?? ''));
            $pppUsername = trim((string) ($row[$columnIndex['username_pppoe']] ?? ''));
            $address = isset($columnIndex['alamat']) ? trim((string) ($row[$columnIndex['alamat']] ?? '')) : null;

            if ($name === '') {
                $errors[] = "Baris {$rowNumber}: nama kosong.";

                continue;
            }
            if (mb_strlen($name) > 150) {
                $errors[] = "Baris {$rowNumber}: nama terlalu panjang (maks 150 karakter).";

                continue;
            }
            if (! preg_match('/^(08|628)[0-9]{8,13}$/', $phone)) {
                $errors[] = "Baris {$rowNumber}: nomor WA \"{$phone}\" tidak valid.";

                continue;
            }
            if ($address !== null && mb_strlen($address) > 255) {
                $errors[] = "Baris {$rowNumber}: alamat terlalu panjang (maks 255 karakter).";

                continue;
            }
            $package = $packagesByName->get(mb_strtolower($packageName));
            if (! $package) {
                $errors[] = "Baris {$rowNumber}: paket \"{$packageName}\" tidak ditemukan atau nonaktif.";

                continue;
            }

            $rows[] = [
                'name' => $name,
                'phone' => $phone,
                'address' => $address !== '' ? $address : null,
                'package_id' => $package->id,
                'ppp_username' => $pppUsername !== '' ? $pppUsername : null,
                'billing_type' => 'postpaid',
                'status' => 'active',
                'due_day' => 10, // matches CustomerController::store()'s S1 hardcoded fallback
            ];
        }
        fclose($handle);

        $created = 0;

        try {
            DB::transaction(function () use ($rows, &$created) {
                foreach ($rows as $row) {
                    $row['customer_code'] = 'C-'.(1000 + Customer::withTrashed()->count());
                    Customer::create($row);
                    $created++;
                }
            });
        } catch (QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            return back()->withErrors([
                'csv_file' => 'Impor dibatalkan: ada baris dengan data yang bentrok dengan data lain (kemungkinan username PPPoE atau kode pelanggan duplikat). Tidak ada baris yang disimpan — perbaiki file dan unggah ulang.',
            ]);
        }

        return back()->with('import_summary', ['created' => $created, 'errors' => $errors]);
    }
}
