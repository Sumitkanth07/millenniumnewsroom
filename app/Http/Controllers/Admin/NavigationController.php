<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        return view('admin.navigation.index', ['items' => NavigationItem::orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.navigation.form', ['item' => new NavigationItem()]);
    }

    public function store(Request $request)
    {
        NavigationItem::create($this->validated($request) + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('admin.navigation.index')->with('status', 'Navigation item created.');
    }

    public function edit(NavigationItem $navigation)
    {
        return view('admin.navigation.form', ['item' => $navigation]);
    }

    public function update(Request $request, NavigationItem $navigation)
    {
        $navigation->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('admin.navigation.index')->with('status', 'Navigation item updated.');
    }

    public function destroy(NavigationItem $navigation)
    {
        $navigation->delete();
        return back()->with('status', 'Navigation item deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }
}
