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
        // دعم حقول متعددة عبر الشيك بوكس: fields[]=name,generic,form | أو all
        $fields = $request->input('fields', ['all']);
        if (!is_array($fields)) {
            $fields = explode(',', (string)$fields);
        }
        $fields = array_values(array_filter($fields));
        if (empty($fields)) $fields = ['all'];

        $drugs = Drug::query()
            ->where('is_active', true)
            ->when($q !== '', function ($qr) use ($q, $fields) {
                $like = '%'.$q.'%';
                $map = [
                    'name' => 'name',
                    'generic' => 'generic_name',
                    'form' => 'dosage_form',
                ];
                if (in_array('all', $fields, true)) {
                    return $qr->where(function ($w) use ($like) {
                        $w->where('name', 'like', $like)
                          ->orWhere('generic_name', 'like', $like)
                          ->orWhere('dosage_form', 'like', $like)
                          ->orWhere('strength', 'like', $like);
                    });
                }
                return $qr->where(function ($w) use ($fields, $map, $like) {
                    foreach ($fields as $f) {
                        if (isset($map[$f])) {
                            $w->orWhere($map[$f], 'like', $like);
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
            'type' => null,
        ]);
    }
}
