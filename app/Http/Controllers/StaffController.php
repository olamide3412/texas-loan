<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Rules\OneWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('search', '');
        $orderBy = request('orderBy', 'id');
        $orderDir = request('orderDir', 'desc');

        $staff = Staff::with('user')
            ->when($request->search, function($query) use($q) {
                $query->where('last_name', 'like', '%'.$q.'%')
                    ->orWhere('first_name', 'like', '%'.$q.'%')
                    ->orWhere('other_name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')
                    ->orWhere('phone_number', 'like', '%'.$q.'%')
                    ->orWhere('staff_number', 'like', '%'.$q.'%')
                    ->orWhere('nin', 'like', '%'.$q.'%')
                    ->orWhere('bvn', 'like', '%'.$q.'%');
            })
            ->orderBy($orderBy, $orderDir)
            ->paginate(10)
            ->withQueryString();

        return inertia('Auth/Staff/Index', [
            'staff' => $staff,
            'filters' => $request->only(['search', 'orderBy', 'orderDir'])
        ]);
    }

    public function store(Request $request)
    {

        $request->merge([
            'monthly_salary' => empty($request->input('monthly_salary'))
            ? null
            : str_replace(',', '', $request->input('monthly_salary')),
        ]);

        $rules = [
            'last_name' => ['required', new OneWord],
            'first_name' => ['required', new OneWord],
            'other_name' => ['nullable', new OneWord],
            'staff_number' => ['nullable', 'string', 'max:255', 'unique:staff'],
            'gender' => ['required'],
            'phone_number' => ['required', 'min:11', 'unique:staff', 'unique:clients,phone_number'],
            'email' => ['required', 'email', 'unique:staff,email', 'unique:users,email', 'unique:clients,email'],
            'date_of_birth' => ['nullable', 'date'],
            'residential_address' => ['nullable', 'string'],
            'local_government' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'nin' => ['nullable', 'min:11', 'unique:staff'],
            'bvn' => ['nullable', 'min:11', 'max:11', 'unique:staff'],
            'bank_account' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'date_of_appointment' => ['nullable', 'date'],
            'position' => ['nullable', 'string'],
            'department' => ['nullable', 'string'],
            'photo' => ['nullable', File::types(['png', 'jpg']), 'max:5120'],
            'role' => ['required'],
        ];

        $validatedData = $request->validate($rules);

        // Process names
        $validatedData['first_name'] = strtolower($request->input('first_name'));
        $validatedData['last_name'] = strtolower($request->input('last_name'));

        if ($request->has('other_name')) {
            $validatedData['other_name'] = strtolower($request->input('other_name'));
        }

        // Generate a random password
        $password = 'Texas@123';

        // Create user account first
        $user = User::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($password),
            'role' => $validatedData['role'],
            'status' => 'enable', // Using string value instead of enum
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            $imageName = $request->first_name . '_' . time() . '.' . $request->photo->extension();
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($request->file('photo'));

            $fileSize = $request->file('photo')->getSize();
            $quality = $fileSize > 2 * 1024 * 1024 ? 50 : 75;

            $encodedImage = $image->encode(new AutoEncoder(quality: $quality));
            Storage::put('staff/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'staff/photos/' . $imageName;
        }

        // Create staff record
        $staffData = $validatedData;
        $staffData['user_id'] = $user->id;
       // $staffData['registered_by'] = Auth::id();
        unset($staffData['role']); // Remove role as it's not in staff table

        $staff = Staff::create($staffData);

        // Log the action
        log_new("$staff->full_name (staff) was registered");

        // Send email with credentials (you can implement this later)

        return redirect()->route('staff.show', $staff->id)->with('success', 'Staff member registered successfully!');
    }

    public function show(Staff $staff)
    {
        $staff->load('user');

        //dd($staff->toArray());

        return inertia('Auth/Staff/Show', [
            'staff' => $staff
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
       $request->merge([
            'monthly_salary' => empty($request->input('monthly_salary'))
            ? null
            : str_replace(',', '', $request->input('monthly_salary')),
        ]);

        $rules = [
            'last_name' => ['required', new OneWord],
            'first_name' => ['required', new OneWord],
            'other_name' => ['nullable', new OneWord],
            'staff_number' => ['nullable', 'string', 'max:255', Rule::unique('staff')->ignore($staff->id)],
            'gender' => ['required'],
            'phone_number' => ['required', 'min:11', Rule::unique('staff')->ignore($staff->id), Rule::unique('clients', 'phone_number')],
            'email' => ['required', 'email', Rule::unique('staff')->ignore($staff->id), Rule::unique('users')->ignore($staff->user_id), Rule::unique('clients', 'email')],
            'date_of_birth' => ['nullable', 'date'],
            'residential_address' => ['nullable', 'string'],
            'local_government' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'nin' => ['nullable', 'min:11', Rule::unique('staff')->ignore($staff->id)],
            'bvn' => ['nullable', 'min:11', 'max:11', Rule::unique('staff')->ignore($staff->id)],
            'bank_account' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'date_of_appointment' => ['nullable', 'date'],
            'position' => ['nullable', 'string'],
            'department' => ['nullable', 'string'],
            'photo' => ['nullable', File::types(['png', 'jpg']), 'max:5120'],
            'role' => ['required'],
            'status' => ['required'],
        ];

        $validatedData = $request->validate($rules);

        // Process names
        $validatedData['first_name'] = strtolower($request->input('first_name'));
        $validatedData['last_name'] = strtolower($request->input('last_name'));

        if ($request->has('other_name')) {
            $validatedData['other_name'] = strtolower($request->input('other_name'));
        }

        // Update user account
        $staff->user->update([
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'status' => $validatedData['status'],
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($staff->photo && Storage::exists($staff->photo)) {
                Storage::delete($staff->photo);
            }

            $imageName = $request->first_name . '_' . time() . '.' . $request->photo->extension();
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($request->file('photo'));

            $fileSize = $request->file('photo')->getSize();
            $quality = $fileSize > 2 * 1024 * 1024 ? 50 : 75;

            $encodedImage = $image->encode(new AutoEncoder(quality: $quality));
            Storage::put('staff/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'staff/photos/' . $imageName;
        }

        // Update staff record
        $staffData = $validatedData;
        unset($staffData['role']); // Remove role as it's not in staff table
        unset($staffData['status']); // Remove status as it's not in staff table

        $staff->update($staffData);

        // Log the action
        log_new("$staff->full_name (staff) details were updated");

        return redirect()->route('staff.show', $staff->id)->with('success', 'Staff member updated successfully!');
    }

    public function destroy(Staff $staff)
    {
        try {
            // Delete photo if exists
            if ($staff->photo && Storage::exists($staff->photo)) {
                Storage::delete($staff->photo);
            }

            // Log the action
            log_new("Deleting $staff->full_name (staff) record");

            // Delete user account
            $staff->user->delete();

            // Delete staff record
            $staff->delete();

            return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for foreign key constraint error (SQLSTATE[23000])
            if ($e->getCode() == '23000') {
                return redirect()->route('staff.index')->with('error', 'Staff member cannot be deleted as they are associated with other records.');
            }

            // For any other errors, rethrow the exception
            throw $e;
        }
    }

    public function profile()
    {
        $staff = Auth::user()->staff;

        return inertia('Auth/Staff/Profile', [
            'staff' => $staff->load('user')
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function resetPassword(Request $request, User $user) {
        $validatedData = $request->validate([
            'password' => ['required','confirmed', Password::min(8)->mixedCase()->letters()->numbers()->symbols()]
        ]);

        $user->update([
            'password' => Hash::make($validatedData['password']),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }

}
