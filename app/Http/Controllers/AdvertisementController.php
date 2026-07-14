<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Advertisement;

class AdvertisementController extends Controller
{
    /**
     * Increment the view counter for an advertisement dynamically.
     */
    public function trackView(int $id)
    {
        DB::table('advertisements')
            ->where('id', $id)
            ->update([
                'current_views' => DB::raw('current_views + 1'),
                'last_viewed_at' => now(),
            ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Increment click counter and redirect to the target destination.
     */
    public function trackClick(int $id)
    {
        $ad = Advertisement::findOrFail($id);

        DB::table('advertisements')
            ->where('id', $id)
            ->update([
                'current_clicks' => DB::raw('current_clicks + 1'),
                'last_clicked_at' => now(),
            ]);

        $target = $ad->target_url ?: '/';
        return redirect()->away($target);
    }
}
