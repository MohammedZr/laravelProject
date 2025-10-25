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
        $type = $request->get('type', 'all'); // all|name|generic|form

        $drugs = Drug::query()
            ->where('is_active', true)
            ->when($q !== '', function ($qr) use ($q, $type) {
                $like = '%'.$q.'%';
                return match ($type) {
                    'name'    => $qr->where('name', 'like', $like),
                    'generic' => $qr->where('generic_name', 'like', $like),
                    'form'    => $qr->where('dosage_form', 'like', $like),
                    default   => $qr->where(function ($w) use ($like) {
                        $w->where('name', 'like', $like)
                          ->orWhere('generic_name', 'like', $like)
                          ->orWhere('dosage_form', 'like', $like)
                          ->orWhere('strength', 'like', $like);
                    }),
                };
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('pharmacy.search', compact('drugs','q','type'));
    }
}
