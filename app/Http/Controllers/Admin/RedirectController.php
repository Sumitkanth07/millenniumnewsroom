<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function index()
    {
        return view('admin.redirects.index', ['redirects' => Redirect::latest()->get()]);
    }

    public function create()
    {
        return view('admin.redirects.form', ['redirect' => new Redirect()]);
    }

    public function store(Request $request)
    {
        Redirect::create($this->validated($request) + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('admin.redirects.index')->with('status', 'Redirect created.');
    }

    public function edit(Redirect $redirect)
    {
        return view('admin.redirects.form', compact('redirect'));
    }

    public function update(Request $request, Redirect $redirect)
    {
        $redirect->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('admin.redirects.index')->with('status', 'Redirect updated.');
    }

    public function destroy(Redirect $redirect)
    {
        $redirect->delete();
        return back()->with('status', 'Redirect deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'source' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'status_code' => ['required', 'integer', 'in:301,302'],
        ]);
    }
}
