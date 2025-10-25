<?php

namespace App\Http\Controllers;

use App\Models\DrugGroup;
use Illuminate\Http\Request;

class DrugGroupController extends Controller
{
    public function index()
    {
        $groups = DrugGroup::where('user_id', auth()->id())->latest()->paginate(15);
        return view('company.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('company.groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable','string','max:190'],
            'notes' => ['nullable','string'],
        ]);

        $group = DrugGroup::create([
            'user_id' => auth()->id(),
            'title'   => $data['title'] ?? null,
            'notes'   => $data['notes'] ?? null,
            'status'  => 'draft',
        ]);

        return redirect()->route('company.groups.show', $group)->with('status','تم إنشاء المسودة');
    }

    public function show(DrugGroup $group)
    {
        abort_unless($group->user_id === auth()->id(), 403);
        $group->load('drugs');
        return view('company.groups.show', compact('group'));
    }

    public function submit(DrugGroup $group)
    {
        abort_unless($group->user_id === auth()->id(), 403);
        $group->update(['status' => 'submitted', 'submitted_at' => now()]);
        return back()->with('status','تم إرسال المجموعة للمراجعة');
    }

    public function publish(DrugGroup $group)
    {
        abort_unless($group->user_id === auth()->id(), 403);
        foreach ($group->drugs as $drug) {
            $drug->update(['is_active' => true]);
        }
        $group->update(['status' => 'published', 'published_at' => now()]);
        return back()->with('status','تم نشر المجموعة وتفعيل الأدوية');
    }

    public function archive(DrugGroup $group)
    {
        abort_unless($group->user_id === auth()->id(), 403);
        $group->update(['status' => 'archived']);
        return back()->with('status','تمت الأرشفة');
    }
}
