<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class BrandingController extends Controller
{
    public function edit()
    {
        return view('admin.branding.edit');
    }

    public function update(Request $request)
    {
        $data = $request->validate([

            'site_name' => ['required', 'string', 'max:255'],

            'site_title' => ['required', 'string', 'max:255'],

            'tagline' => ['required', 'string', 'max:255'],

            'primary_color' => ['required', 'string', 'max:20'],

            'secondary_color' => ['required', 'string', 'max:20'],

            'meta_description' => ['nullable', 'string'],

            'logo' => ['nullable', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],

        ]);

        $uploadedLogo = $request->hasFile('logo');

        if ($uploadedLogo) {

            $file = $request->file('logo');

            $filename =
                time().'.'.$file->getClientOriginalExtension();

            $publicDestination = public_path('uploads');
            $baseDestination = base_path('uploads');

            if (!file_exists($publicDestination)) {
                mkdir($publicDestination, 0775, true);
            }
            if (!file_exists($baseDestination)) {
                mkdir($baseDestination, 0775, true);
            }

            copy($file->getPathname(), $baseDestination.'/'.$filename);
            $file->move($publicDestination, $filename);

            $data['logo'] =
                'uploads/'.$filename;
        }

        foreach ($data as $key => $value) {

            Setting::setValue($key, $value);
        }

        $message = $uploadedLogo
            ? 'Branding saved and logo uploaded successfully.'
            : 'Branding saved successfully.';

        return back()->with('status', $message);
    }
}
