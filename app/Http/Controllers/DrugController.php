<?php

namespace App\Http\Controllers;

use App\Models\DrugGroup;
use App\Models\Drug;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    public function create(DrugGroup $group)
    {
        abort_unless($group->user_id === auth()->id(), 403);
        return view('company.drugs.create', compact('group'));
    }

    public function store(Request $request, DrugGroup $group)
    {
        abort_unless($group->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'         => ['required','string','max:190'],
            'generic_name' => ['nullable','string','max:190'],
            'dosage_form'  => ['nullable','string','max:120'],
            'strength'     => ['nullable','string','max:120'],
            'pack_size'    => ['nullable','integer','min:1'],
            'unit'         => ['nullable','string','max:30'],
            'sku'          => ['nullable','string','max:190'],
            'barcode'      => ['nullable','string','max:32','unique:drugs,barcode'],
            'price'        => ['nullable','numeric','min:0'],
            'stock'        => ['nullable','integer','min:0'],
            'image_url' => ['nullable','url','max:255'],
        ]);

        Drug::create([
            'user_id'       => auth()->id(),
            'drug_group_id' => $group->id,
            'name'          => $data['name'],
            'generic_name'  => $data['generic_name'] ?? null,
            'dosage_form'   => $data['dosage_form'] ?? null,
            'strength'      => $data['strength'] ?? null,
            'pack_size'     => $data['pack_size'] ?? 1,
            'unit'          => $data['unit'] ?? null,
            'sku'           => $data['sku'] ?? null,
            'barcode'       => $data['barcode'] ?? null,
            'price'         => $data['price'] ?? 0,
            'stock'         => $data['stock'] ?? 0,
            'is_active'     => false,
            'image_url'    => $data['image_url'] ?? null,
        ]);

        return redirect()->route('company.groups.show', $group)->with('status','تم إضافة الدواء');
    }
}
