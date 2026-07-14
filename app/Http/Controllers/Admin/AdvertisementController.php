<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{
    public function dashboard()
    {
        $totalAds = Advertisement::count();
        $activeAds = Advertisement::where('is_active', true)->count();
        $totalViews = Advertisement::sum('current_views');
        $totalClicks = Advertisement::sum('current_clicks');
        $avgCtr = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 2) : 0;

        $placements = Advertisement::select('placement', DB::raw('count(*) as count'))
            ->groupBy('placement')
            ->get();

        return view('admin.advertisements.dashboard', compact(
            'totalAds', 'activeAds', 'totalViews', 'totalClicks', 'avgCtr', 'placements'
        ));
    }

    public function index()
    {
        $ads = Advertisement::orderByDesc('priority')->orderByDesc('id')->paginate(15);
        return view('admin.advertisements.index', compact('ads'));
    }

    public function create()
    {
        $ad = new Advertisement();
        $placements = $this->getPlacementsList();
        return view('admin.advertisements.form', compact('ad', 'placements'));
    }

    public function store(Request $request)
    {
        $data = $this->validateAd($request);
        $data = $this->handleUploads($request, $data);
        $data['created_by'] = auth()->id();

        Advertisement::create($data);

        return redirect()->route('admin.advertisements.index')->with('status', 'Advertisement created successfully.');
    }

    public function edit(Advertisement $advertisement)
    {
        $ad = $advertisement;
        $placements = $this->getPlacementsList();
        return view('admin.advertisements.form', compact('ad', 'placements'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $data = $this->validateAd($request);
        $data = $this->handleUploads($request, $data, $advertisement);

        $advertisement->update($data);

        return redirect()->route('admin.advertisements.index')->with('status', 'Advertisement updated successfully.');
    }

    public function destroy(Advertisement $advertisement)
    {
        foreach (['image', 'image_tablet', 'image_mobile'] as $field) {
            if ($advertisement->$field) {
                Storage::disk('public')->delete($advertisement->$field);
            }
        }
        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')->with('status', 'Advertisement deleted successfully.');
    }

    public function reports()
    {
        $ads = Advertisement::orderByDesc('current_views')->get();
        return view('admin.advertisements.reports', compact('ads'));
    }

    public function settings()
    {
        $settings = [
            'adsense_client_id' => Setting::getValue('adsense_client_id', ''),
            'google_analytics_code' => Setting::getValue('google_analytics_code', ''),
            'google_tag_manager_code' => Setting::getValue('google_tag_manager_code', ''),
            'microsoft_clarity_code' => Setting::getValue('microsoft_clarity_code', ''),
            'facebook_pixel_code' => Setting::getValue('facebook_pixel_code', ''),
            'custom_header_code' => Setting::getValue('custom_header_code', ''),
        ];
        return view('admin.advertisements.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        foreach ($request->only([
            'adsense_client_id', 'google_analytics_code', 'google_tag_manager_code',
            'microsoft_clarity_code', 'facebook_pixel_code', 'custom_header_code'
        ]) as $key => $value) {
            Setting::setValue($key, $value ?: '');
        }

        return redirect()->back()->with('status', 'Advertisement settings updated successfully.');
    }

    private function validateAd(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'placement' => 'required|string|max:100',
            'device' => 'required|in:all,desktop,tablet,mobile',
            'type' => 'required|in:google_adsense,google_ad_manager,media_net,html,js,affiliate,image,iframe',
            'priority' => 'required|integer|min:0',
            'code' => 'nullable|string',
            'code_desktop' => 'nullable|string',
            'code_tablet' => 'nullable|string',
            'code_mobile' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'image_tablet' => 'nullable|image|max:2048',
            'image_mobile' => 'nullable|image|max:2048',
            'target_url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_views' => 'nullable|integer|min:1',
            'max_clicks' => 'nullable|integer|min:1',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
            'target_pages' => 'nullable|array',
            'target_pages.*' => 'string|in:homepage,category,single,search,author,tag,static',
            'is_active' => 'required|boolean',
        ]);
    }

    private function handleUploads(Request $request, array $data, ?Advertisement $ad = null): array
    {
        foreach (['image', 'image_tablet', 'image_mobile'] as $field) {
            if ($request->hasFile($field)) {
                if ($ad && $ad->$field) {
                    Storage::disk('public')->delete($ad->$field);
                }
                $data[$field] = $request->file($field)->store('ads', 'public');
            } else {
                unset($data[$field]);
            }
        }
        return $data;
    }

    private function getPlacementsList(): array
    {
        return [
            'header_top' => 'Header Top',
            'below_header' => 'Below Header',
            'homepage_slider_bottom' => 'Homepage Slider Bottom',
            'homepage_feed' => 'Homepage Feed',
            'homepage_category_section' => 'Homepage Category Section',
            'homepage_bottom' => 'Homepage Bottom',
            'category_top' => 'Category Top',
            'category_middle' => 'Category Middle',
            'category_bottom' => 'Category Bottom',
            'search_page' => 'Search Page',
            'tag_page' => 'Tag Page',
            'author_page' => 'Author Page',
            'sidebar_top' => 'Sidebar Top',
            'sidebar_middle' => 'Sidebar Middle',
            'sidebar_bottom' => 'Sidebar Bottom',
            'single_post_top' => 'Single Post Top',
            'after_3rd_paragraph' => 'After 3rd Paragraph',
            'after_5th_paragraph' => 'After 5th Paragraph',
            'after_7th_paragraph' => 'After 7th Paragraph',
            'before_related_posts' => 'Before Related Posts',
            'after_related_posts' => 'After Related Posts',
            'footer_top' => 'Footer Top',
            'footer_bottom' => 'Footer Bottom',
        ];
    }
}
