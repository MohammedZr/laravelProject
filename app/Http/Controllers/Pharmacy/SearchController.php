<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
{
    $q = trim((string)$request->get('q', ''));
    $fields = $request->input('fields', ['all']);
    if (!is_array($fields)) {
        $fields = explode(',', (string)$fields);
    }
    $fields = array_values(array_filter($fields));
    if (empty($fields)) $fields = ['all'];

    $drugs = Drug::query()
        ->where('is_active', true)
        ->when($q !== '', function ($qr) use ($q, $fields) {
            $like = '%' . mb_strtolower($q) . '%';
            $map = [
                'name' => 'name',
                'generic' => 'generic_name',
                'form' => 'dosage_form',
                'strength' => 'strength',
            ];

            return $qr->where(function ($w) use ($fields, $map, $like) {
                if (in_array('all', $fields, true)) {
                    foreach ($map as $col) {
                        $w->orWhereRaw("LOWER($col) LIKE ?", [$like]);
                    }
                } else {
                    foreach ($fields as $f) {
                        if (isset($map[$f])) {
                            $w->orWhereRaw("LOWER({$map[$f]}) LIKE ?", [$like]);
                        }
                    }
                }
            });
        })
        ->orderBy('name')
        ->paginate(12)
        ->withQueryString();

    return view('pharmacy.search', [
        'drugs' => $drugs,
        'q' => $q,
    ]);
}

}
