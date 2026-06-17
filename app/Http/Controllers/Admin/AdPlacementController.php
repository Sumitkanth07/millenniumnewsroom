<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPlacement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdPlacementController extends Controller
{
    public function index()
    {
        return view('admin.ads.index', ['ads' => AdPlacement::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.ads.form', ['ad' => new AdPlacement()]);
    }

    public function store(Request $request)
    {
        AdPlacement::create($this->validated($request));

        return redirect()->route('admin.ads.index')->with('status', 'Ad placement saved.');
    }

    public function edit(AdPlacement $ad)
    {
        return view('admin.ads.form', compact('ad'));
    }

    public function update(Request $request, AdPlacement $ad)
    {
        $ad->update($this->validated($request));

        return redirect()->route('admin.ads.index')->with('status', 'Ad placement updated.');
    }

    public function destroy(AdPlacement $ad)
    {
        $ad->delete();

        return back()->with('status', 'Ad placement deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'key' => ['nullable', 'string', 'max:120'],
            'code' => ['nullable', 'string'],
        ]);
        $data['key'] = Str::slug($data['key'] ?: $data['name'], '_');
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
