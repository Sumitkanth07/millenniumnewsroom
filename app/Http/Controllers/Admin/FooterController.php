<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    public function edit()
    {
        return view('admin.footer.edit', [
            'footer' => FooterSetting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'copyright_text' => ['nullable', 'string', 'max:255'],
            'footer_menus' => ['nullable', 'string'],
            'category_links' => ['nullable', 'string'],
            'social_links' => ['nullable', 'string'],
            'sitemap_links' => ['nullable', 'string'],
        ]);

        foreach (['footer_menus', 'category_links', 'social_links', 'sitemap_links'] as $field) {
            $data[$field] = collect(preg_split('/\r\n|\r|\n/', (string) ($data[$field] ?? '')))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        FooterSetting::current()->update($data);

        return back()->with('status', 'Footer settings saved successfully.');
    }
}
