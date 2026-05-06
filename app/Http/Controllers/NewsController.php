<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\User;
use App\Models\PackagePurchase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manageNews']);
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = $request->get('query', '');
        $perPage = $request->get('perPage', 10); // default 10 records per page

        $newsList = News::when($query, function ($q) use ($query) {
            $q->where('title', 'like', "%$query%")
                ->orWhere('description', 'like', "%$query%")
                ->orWhere('url', 'like', "%$query%");
        })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends(['query' => $query, 'perPage' => $perPage]);

        // If AJAX request, return only table rows
       if ($request->ajax()) {
    return view('admin.news._table', compact('newsList'))->render();
}

        return view('admin.news.index', compact('newsList'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $packages = \App\Models\Package::all();
        return view('admin.news.create', compact('packages'));
    }
    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'date' => 'required|date',
        'time' => 'required|date_format:H:i',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'url' => 'nullable|url|max:255',
        'status' => 'required|boolean',
        'audience_type' => 'required|in:all,subscribers,package',
        'package_id' => 'required_if:audience_type,package|nullable|exists:packages,id',
    ]);

    // ✅ Upload image if exists
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('news_images', 'public');
    }

    // ✅ Create the news record
    $news = \App\Models\News::create([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'date' => $validated['date'],
        'time' => $validated['time'],
        'image' => $imagePath,
        'url' => $validated['url'] ?? null,
        'status' => $validated['status'],
        'audience_type' => $validated['audience_type'],
        'package_id' => $validated['audience_type'] === 'package' ? $validated['package_id'] : null,
    ]);

    // 🧠 Debug log to confirm what was created
    \Log::info('News created', [
        'id' => $news->id,
        'audience_type' => $news->audience_type,
        'package_id' => $news->package_id,
        'status' => $news->status,
    ]);

    // ✅ Send push notification if active
    if ($news->status) {
        \Log::info('Sending notification for news', ['audience_type' => $news->audience_type]);

        $tokens = [];

        switch (strtolower($news->audience_type)) {
            case 'all':
                $tokens = \App\Models\User::whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
                break;

            case 'subscribers':
                $tokens = \App\Models\User::where('type', 'subscriber')
                    ->whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
                break;

            case 'package':
    // ✅ Use same logic as NotificationController or UserMessageController
    $users = \App\Models\User::whereHas('packagePurchases', function ($query) use ($news) {
    $query->where('package_id', $news->package_id)
          ->where('status', 'approved'); // ✅ Only approved purchases
})->whereNotNull('fcm_token')->get();


    $tokens = $users->pluck('fcm_token')->toArray();

    \Log::info('Package users FCM tokens from NewsController', [
        'package_id' => $news->package_id,
        'tokens' => $tokens,
    ]);


                \Log::info('Package users FCM tokens from NewsController', [
                    'package_id' => $news->package_id,
                    'count' => count($tokens),
                ]);

                // Send one-by-one (same behavior as NotificationController)
                foreach ($tokens as $token) {
                    app(\App\Services\FcmService::class)->sendToTokens(
                        [$token],
                        '📰 New News',
                        $news->title,
                        [
                            'type' => 'news',
                            'news_id' => (string) $news->id,
                        ]
                    );
                }

                break;
        }

        // ✅ Log overall token count
        \Log::info('News FCM tokens summary', [
            'audience_type' => $news->audience_type,
            'package_id' => $news->package_id,
            'count' => count($tokens),
        ]);

        // ✅ Send to all (except package, already sent one-by-one)
        if (!empty($tokens) && $news->audience_type !== 'package') {
            app(\App\Services\FcmService::class)->sendToTokens(
                $tokens,
                '📰 New News',
                $news->title,
                [
                    'type' => 'news',
                    'news_id' => (string) $news->id,
                ]
            );
        } elseif (empty($tokens)) {
            \Log::warning('No FCM tokens found for audience type: ' . $news->audience_type);
        }
    }

    return redirect()->route('news.index')->with('success', 'News item created successfully.');
}


    
    public function getFcmTokensByAudienceType($type, $packageId = null)
{
    switch ($type) {
        case 'all':
            return User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

        case 'subscribers':
            return User::where('is_subscribed', true) // Or your logic
                ->whereNotNull('fcm_token')
                ->pluck('fcm_token')
                ->toArray();

        case 'package':
            return $this->getTargetUserFcmTokens($packageId);

        default:
            return [];
    }
}



private function getTargetUserFcmTokens($packageId)
{
    if (!$packageId) {
        return [];
    }

    // Get all users who purchased this package
    $userIds = \App\Models\PackagePurchase::where('package_id', $packageId)
        ->pluck('user_id')
        ->toArray();

    if (empty($userIds)) {
        return [];
    }

    // Get FCM tokens of those users
    return \App\Models\User::whereIn('id', $userIds)
        ->whereNotNull('fcm_token')
        ->pluck('fcm_token')
        ->toArray();
}




    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'title' => 'required|string',
    //         'description' => 'required|string',
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'url' => 'nullable|url|max:255',
    //         'status' => 'required|boolean',
    //     ]);

    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $validated['image'] = $request->file('image')->store('news_images', 'public');
    //     }

    //     $news = \App\Models\News::create([
    //         'title' => $validated['title'],
    //         'description' => $validated['description'],
    //         'image' => $validated['image'],
    //         'url' => $validated['url'],
    //         'status' => $validated['status'],
    //     ]);

    //     // 🔔 Send notification ONLY if status = 1 (active)
    //     if ($news->status == 1) {
    //         $tokens = \App\Models\User::whereNotNull('fcm_token')->pluck('fcm_token')->all();
    //         if (!empty($tokens)) {
    //             app(\App\Services\FcmService::class)->sendToTokens(
    //                 $tokens,
    //                 '📰 New News',
    //                 $news->title,
    //                 [
    //                     'type' => 'news',
    //                     'news_id' => (string) $news->id,
    //                 ]
    //             );
    //         }
    //     }

    //     return redirect()->route('news.index')->with('success', 'News item created successfully.');
    // }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     **/
    public function edit($id)
{
    $news = News::findOrFail($id);
    $packages = \App\Models\Package::all();

    return view('admin.news.edit', compact('news', 'packages'));
}
// public function update(Request $request, $id)
// {
//     $request->validate([
//         'title' => 'required|string|max:255',
//         'description' => 'required|string',
//         'url' => 'nullable|url',
//          'date' => 'required|date',
//             'time' => 'required|date_format:H:i',
//         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//         'status' => 'required|boolean',
//     ]);

//     $news = News::findOrFail($id);

//     $news->title = $request->title;
//     $news->description = $request->description;
//     $news->url = $request->url;
//     $news->status = $request->status;
//     $news->date = $request->date;
//     $news->time = $request->time;

//     if ($request->hasFile('image')) {
//         if ($news->image && Storage::exists($news->image)) {
//             Storage::delete($news->image);
//         }
//         $news->image = $request->file('image')->store('news_images', 'public');
//     }

//     $news->save();

//     // ✅ Push Notification Logic
//     if ($news->status) {
//         $tokens = [];

//         switch ($news->audience_type) {
//             case 'all':
//                 $tokens = \App\Models\User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
//                 break;

//             case 'subscribers':
//                 $tokens = \App\Models\User::where('type', 'subscriber')
//                     ->whereNotNull('fcm_token')
//                     ->pluck('fcm_token')
//                     ->toArray();
//                 break;

//             case 'package':
//                 $tokens = $this->getTargetUserFcmTokens($news->package_id);
//                 break;
//         }

//         \Log::info('FCM tokens for update:', ['count' => count($tokens)]);

//         if (!empty($tokens)) {
//             app(\App\Services\FcmService::class)->sendToTokens(
//                 $tokens,
//                 '📰 Updated News',
//                 $news->title,
//                 [
//                     'type' => 'news',
//                     'news_id' => (string) $news->id,
//                 ]
//             );
//         }
//     }

//     return redirect()->route('news.index')->with('success', 'News updated successfully!');
// }

public function update(Request $request, $id)
{
    // dd($request->all());
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'url' => 'nullable|url|max:255',
        'date' => 'required|date',
        'time' => 'required|date_format:H:i',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'status' => 'required|boolean',
    ]);

    $news = \App\Models\News::findOrFail($id);

    // ✅ Handle image update
    if ($request->hasFile('image')) {
        if ($news->image && \Storage::disk('public')->exists($news->image)) {
            \Storage::disk('public')->delete($news->image);
        }
        $news->image = $request->file('image')->store('news_images', 'public');
    }

    // ✅ Update fields
    $news->update([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'url' => $validated['url'] ?? null,
        'status' => $validated['status'],
        'date' => $validated['date'],
        'time' => $validated['time'],
    ]);

    // 🧠 Debug log
    \Log::info('News updated', [
        'id' => $news->id,
        'audience_type' => $news->audience_type,
        'package_id' => $news->package_id,
        'status' => $news->status,
    ]);

    // ✅ Send push notification if active
    if ($request->status ==1) {
        \Log::info('Sending notification for updated news', ['audience_type' => $news->audience_type]);

        $tokens = [];

        switch (strtolower($news->audience_type)) {
            case 'all':
                $tokens = \App\Models\User::whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
                break;

            case 'subscribers':
                $tokens = \App\Models\User::where('type', 'subscriber')
                    ->whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
                break;

            case 'package':
                $users = \App\Models\User::whereHas('packagePurchases', function ($query) use ($news) {
                    $query->where('package_id', $news->package_id)
                        ->where('status', 'approved'); // ✅ Only approved
                })->whereNotNull('fcm_token')->get();

                $tokens = $users->pluck('fcm_token')->toArray();

                \Log::info('Package users FCM tokens from NewsController (update)', [
                    'package_id' => $news->package_id,
                    'count' => count($tokens),
                ]);

                // ✅ Send one-by-one
                foreach ($tokens as $token) {
                    app(\App\Services\FcmService::class)->sendToTokens(
                        [$token],
                        '📰 Updated News',
                        $news->title,
                        [
                            'type' => 'news',
                            'news_id' => (string) $news->id,
                        ]
                    );
                }

                break;
        }

        // ✅ Log summary
        \Log::info('Updated News FCM tokens summary', [
            'audience_type' => $news->audience_type,
            'package_id' => $news->package_id,
            'count' => count($tokens),
        ]);

        // ✅ Send in bulk for all/subscribers (package handled above)
        if (!empty($tokens) && $news->audience_type !== 'package') {
            app(\App\Services\FcmService::class)->sendToTokens(
                $tokens,
                '📰 Updated News',
                $news->title,
                [
                    'type' => 'news',
                    'news_id' => (string) $news->id,
                ]
            );
        } elseif (empty($tokens)) {
            \Log::warning('No FCM tokens found for audience type (update): ' . $news->audience_type);
        }
    }

    return redirect()->route('news.index')->with('success', 'News updated successfully!');
}


    // Delete news
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        if ($news->image && Storage::exists($news->image)) {
            Storage::delete($news->image);
        }

        $news->delete();

        return redirect()->route('news.index')->with('success', 'News deleted successfully!');
    }
}
