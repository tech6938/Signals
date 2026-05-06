<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\BankDetailController;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FcmTestController;
use App\Http\Controllers\ValuesController;
use App\Http\Controllers\userMessageController;
use App\Http\Controllers\AdminWalletController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PackagePurchaseController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\WebviewPackageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ValueSubscriptionController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


use App\Http\Controllers\Api\ChatController;

use App\Models\User;
use App\Services\FcmService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;



Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }else{
        Session::flesh('error', 'Invalid locale selected.');
    }
    return redirect()->back();
});

Route::get('optimize-clear', function () {
   \Artisan::call('optimize:clear');
});

//for webview
Route::get('/change-language', function (Illuminate\Http\Request $request) {
    $lang = $request->get('lang', 'en');
    session(['locale' => $lang]);
    app()->setLocale($lang);
    return back();
})->name('change.language');



Route::get('/send-test', function (FcmService $fcm) {
    $tokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();

    $fcm->sendToTokens(
        tokens: $tokens,
        title: 'Hello Users 🎉',
        body: 'This is a test notification from Laravel!',
        data: ['type' => 'test']
    );

    return 'Notification sent to all users!';
});

Route::get('/admin/package-purchases', [PackagePurchaseController::class, 'index'])->name('admin.package.purchases');
Route::post('package-purchases/{id}/update-status', [PackagePurchaseController::class, 'updateStatus'])->name('admin.package.purchases.updateStatus');


Route::get('/users/{user}/edit-roles', [UserController::class, 'editRoles']);
Route::put('/users/{user}/update-roles', [UserController::class, 'updateRoles']);

Route::middleware(['auth', 'role:admin|manager|staff'])->group(function () {
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{userId}', [ChatController::class, 'getChat']);

    // Show all users/messages list
    Route::get('/admin/chats', [ChatController::class, 'index'])->name('admin.chat.index');

    // Start new chat with user (MUST come before the {userId} route)
    Route::get('/admin/chat/start-new', [ChatController::class, 'startNewChat'])->name('admin.chat.start_new');
    Route::post('/admin/chat/{userId}/start', [ChatController::class, 'sendInitialMessage'])->name('admin.chat.send_initial');

    // Show chat with specific user
    Route::get('/admin/chat/{userId}', [ChatController::class, 'show'])->name('admin.chat.show');

    // Reply to user
    Route::post('/admin/chat/{userId}/reply', [ChatController::class, 'reply'])->name('admin.chat.reply');
    Route::get('bank-details/activate/{id}', [BankDetailController::class, 'activate'])->name('bankDetails.activate');
    Route::prefix('admin')->group(function () {
        Route::resource('bankDetails', BankDetailController::class);
        Route::resource('wallet', AdminWalletController ::class);
        Route::get('wallet/user/{userId}', [AdminWalletController::class, 'userDetail'])->name('wallet.user.detail');
        Route::resource('news', NewsController::class);
        Route::resource('values', ValuesController::class);
        Route::resource('signals', SignalController::class);
        Route::resource('users', UserController::class);
        Route::resource('userMessages', userMessageController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('packages', PackageController::class);
        Route::resource('notifications', NotificationController::class);
        Route::get('value-subscriptions', [ValueSubscriptionController::class,'index'])->name('admin.value-subscriptions.index');
        Route::get('value-subscriptions/create', [ValueSubscriptionController::class,'create'])->name('admin.value-subscriptions.create');
        Route::post('value-subscriptions', [ValueSubscriptionController::class,'store'])->name('admin.value-subscriptions.store');
        Route::delete('value-subscriptions/{user}/{value}', [ValueSubscriptionController::class,'destroy'])->name('admin.value-subscriptions.destroy');
        Route::get('value-subscriptions/search-values', [ValueSubscriptionController::class,'searchValues'])->name('admin.value-subscriptions.searchValues');
        Route::get('value-subscriptions/user-packages', [ValueSubscriptionController::class,'getUserPurchasedPackages'])->name('admin.value-subscriptions.user-packages');

    });

  Route::get('/admin/invoices/approved', [InvoiceController::class, 'approved'])->name('invoices.approved');
  Route::get('/admin/invoices/newUser', [UserController::class, 'newUser'])->name('invoices.newUser');
  Route::post('/admin/invoices/newUserStore', [UserController::class, 'newUserStore'])->name('invoices.newUserStore');



Route::get('/user-packages/{user}', [InvoiceController::class, 'getUserPackages'])
    ->name('user.packages');
Route::post('/admin/invoices/statusApproved', [InvoiceController::class, 'statusApproved'])
    ->name('invoices.statusApproved');


    Route::get('/users/staff', [UserController::class, 'staff'])
        ->name('users.staff');

    Route::post('/users/{user}/update-status', [UserController::class, 'updateStatus'])
        ->name('users.updateStatus');
    Route::get('/admin/profile', [UserController::class, 'profile'])->name('admin.profile');
    Route::put('admin/profile/update', [UserController::class, 'updatePassword'])->name('profile.update');


    // Notification routes
    Route::get('/admin/notifications/search-users', [NotificationController::class, 'searchUsers'])->name('notifications.searchUsers');
    Route::post('/admin/notifications/{notification}/resend', [NotificationController::class, 'resend'])->name('notifications.resend');

    Route::get('admin/getroles', [RoleController::class, 'getroles'])->name('admin.getroles');

    Route::get('assaign/{id}/edit', [RoleController::class, 'assaignEdit'])->name('assaign.edit');
    Route::put('assaign/{id}', [RoleController::class, 'assaignUpdate'])->name('assaign.update');

    Route::get('/admin/dashboard', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');
});



Route::post('/admin/store/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/admin/list/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
Route::get('/admin/invoices/create/', [InvoiceController::class, 'create'])->name('invoices.create');
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'showPdf'])->name('invoices.pdf');
Route::post('/invoices/check-expiration', [InvoiceController::class, 'checkExpiration'])->name('invoices.check-expiration');




// Login routes
Route::get('/admin/login', [AuthController::class, 'index'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Optional: redirect root to login or dashboard
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});


Route::get('/fcm-test', [FcmTestController::class, 'sendDirect']);

// Test route for user search debugging
Route::get('/test-user-search', function () {
    $users = \App\Models\User::select('id', 'f_name', 'last_name', 'email', 'type')->take(5)->get();
    return response()->json([
        'total_users' => \App\Models\User::count(),
        'sample_users' => $users
    ]);
});

// Test route for notification search debugging
Route::get('/test-notification-search', function (\Illuminate\Http\Request $request) {
    $query = $request->get('q', '');

    if (strlen($query) < 2) {
        return response()->json([]);
    }

    $users = \App\Models\User::where(function ($q) use ($query) {
        $q->where('f_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%");
    })
        ->select('id', 'f_name', 'last_name', 'email', 'type')
        ->limit(10)
        ->get();

    return response()->json($users->map(function ($user) {
        return [
            'id' => $user->id,
            'text' => $user->f_name . ' ' . $user->last_name . ' (' . $user->email . ') - ' . ucfirst($user->type),
            'email' => $user->email,
            'type' => $user->type
        ];
    }));
});

Route::get('/admin/about', [AboutController::class, 'index'])->name('about');


// Route::get('/admin/webview/packages', [WebviewPackageController::class, 'index'])
//     ->name('/webview.packages');
    Route::match(['get', 'post'], '/admin/webview/packages', [WebviewPackageController::class, 'index']);

Route::post('/admin/webview/packages/purchase', [WebviewPackageController::class, 'purchase'])
    ->name('webview.package.purchase');

Route::post('/admin/webview/generate-invoice', [WebviewPackageController::class, 'generateInvoice'])
    ->name('webview.generate-invoice');

// API endpoint to get current user info (for Flutter app)
Route::get('/api/current-user', function(Request $request) {
    $user = null;

    // Try to get user from API token
    if ($request->bearerToken()) {
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
    }

    // Try to get user from token parameter
    if (!$user && $request->token) {
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->token)?->tokenable;
    }

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    return response()->json([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->f_name . ' ' . $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone
        ]
    ]);
});

// Debug route to test invoice generation
Route::get('/admin/webview/debug-invoice', function() {
    $user = Auth::user();
    $package = \App\Models\Package::first();

    // Check if token is passed in request
    $token = request('token');
    $userFromToken = null;

    if ($token) {
        $userFromToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
    }

    return response()->json([
        'session_user' => $user ? [
            'id' => $user->id,
            'name' => $user->f_name . ' ' . $user->last_name,
            'phone' => $user->phone,
            'email' => $user->email
        ] : null,
        'user_from_token' => $userFromToken ? [
            'id' => $userFromToken->id,
            'name' => $userFromToken->f_name . ' ' . $userFromToken->last_name,
            'phone' => $userFromToken->phone,
            'email' => $userFromToken->email
        ] : null,
        'provided_token' => $token ? 'Token provided' : 'No token',
        'package' => $package ? [
            'id' => $package->id,
            'name' => $package->name,
            'price' => $package->price,
            'lyd_price' => $package->lyd_price,
            'duration_days' => $package->duration_days
        ] : null,
        'banks_count' => \App\Models\BankDetail::where('is_active', 1)->count(),
        'auth_method' => $user ? 'session' : ($userFromToken ? 'token' : 'none')
    ]);
});

Route::get('/admin/download', [BackupController::class, 'downloadBackup'])->name('backup.download');
