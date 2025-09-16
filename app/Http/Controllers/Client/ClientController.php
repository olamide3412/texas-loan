<?php

namespace App\Http\Controllers\Client;

use App\Enums\OrderStatusEnums;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

class ClientController extends Controller
{


    public function store(Request $request)
    {
        // Simplified validation for self-registration
         $request->merge([
            'annual_income' => empty($request->input('annual_income'))
            ? '0.00'
            : str_replace(',', '', $request->input('annual_income')),
        ]);


        $rules = [
            'last_name' => ['required', new \App\Rules\OneWord],
            'first_name' => ['required', new \App\Rules\OneWord],
            'other_name' => ['nullable', new \App\Rules\OneWord],
            'email' => ['required', 'email', 'unique:clients,email', 'unique:staff,email'],
            'phone_number' => ['required', 'min:11', 'unique:clients,phone_number', 'unique:staff,phone_number'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'occupation' => ['required'],

            // Make sensitive/verification fields optional for self-registration
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable'],
            'residential_address' => ['nullable'],
            'local_government' => ['nullable'],
            'state' => ['nullable'],
            'nin' => ['nullable', 'min:11', 'unique:clients,nin'],
            'bvn' => ['nullable', 'min:11', 'max:11', 'unique:clients,bvn'],
            'annual_income' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:png,jpg', 'max:5120'],
            'cf_turnstile_response' => ['required', new Turnstile()],
        ];

        $validatedData = $request->validate($rules);

        unset($staffData['cf_turnstile_response']);
        // Process names
        $validatedData['first_name'] = strtolower($request->input('first_name'));
        $validatedData['last_name'] = strtolower($request->input('last_name'));

        if ($request->has('other_name')) {
            $validatedData['other_name'] = strtolower($request->input('other_name'));
        }

        // Add password for authentication
        $validatedData['password'] = Hash::make($request->password);

        // For self-registration, registered_by is null or could be a system user
        $validatedData['registered_by'] = null;

        // Handle file upload (same as your existing code)
        if ($request->hasFile('photo')) {
            $imageName = $request->first_name . '_' . time() . '.' . $request->photo->extension();
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($request->file('photo'));

            $fileSize = $request->file('photo')->getSize();
            $quality = $fileSize > 2 * 1024 * 1024 ? 50 : 75;

            $encodedImage = $image->encode(new AutoEncoder(quality: $quality));
            Storage::put('client/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'client/photos/' . $imageName;
        }

        // Create client
        $client = Client::create($validatedData);

        // Log the action
        log_new("$client->full_name (client) self-registered, Verification is pending");

        // Automatically log in the client
        Auth::guard('client')->login($client);

        return redirect()->route('client.dashboard')->with('success', 'Registration successful! Welcome to your dashboard.');
    }

    public function dashboard(Request $request){

        $client = $request->user(); // Gets the authenticated client



        return inertia('Client/DashBoard', [
            'orders' => $client->orders()->with('items.product')->latest()->take(10)->get(),
            'orderStats' => [
                'pending_orders' => $client->orders()->where('status', OrderStatusEnums::Pending->value)->count(),
                'processing_orders' => $client->orders()->where('status', OrderStatusEnums::Processing->value)->count(),
                'completed_orders' => $client->orders()->where('status', OrderStatusEnums::Completed->value)->count(),
                'rejected_orders' => $client->orders()->where('status', OrderStatusEnums::Rejected->value)->count(),
                'cancelled_orders' => $client->orders()->where('status', OrderStatusEnums::Cancelled->value)->count(),
                'total_orders' => $client->orders()->count(),
                'total_spent' => $client->orders()->where('status', OrderStatusEnums::Completed->value)->sum('total_price'),
            ],
        ]);
    }

     public function profile(){

        return inertia('Client/Profile');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $client = $request->user();
        $client->password = Hash::make($request->password);
        $client->save();

        return back()->with('success', 'Password updated successfully.');
    }
}
