<?php

namespace App\Http\Controllers;

use App\Enums\Staff\StaffDepartmentEnums;
use App\Enums\Staff\StaffPositionEnums;
use App\Models\Staff;
use App\Rules\OneWord;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

class StaffController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request) {


        $q = $request->get('q', '');

          $staffs = Staff::latest()->where('last_name', 'LIKE', '%'.$q.'%')
                        ->orWhere('first_name', 'LIKE', '%'.$q.'%')
                        ->orWhere('other_name', 'LIKE', '%'.$q.'%')
                        ->orWhere('email', 'LIKE', '%'.$q.'%')
                        ->orWhere('phone_number', 'LIKE', '%'.$q.'%')
                        ->orWhere('nin', 'LIKE', '%'.$q.'%')
                        ->orWhere('bvn', 'LIKE', '%'.$q.'%')
                        ->paginate(25);


        if ($request->ajax()) {
            return response()->json([
                'html' => view('staffs.include.staff-table', compact('staffs'))->render(),
            ]);
        }

        return view('staffs.index', compact('staffs'));
    }
    public function create(){

        return view('staffs.create');
    }

    public function store(Request $request){

        $rules = [
            'last_name' =>  ['required', new OneWord],
            'first_name' => ['required', new OneWord],
            'other_name' => ['nullable',  new OneWord],
            'email' =>      ['required','email','unique:staff,email'],
            'staff_number' =>   ['required','string','min:4','max:6','unique:staff,staff_number'],
            'date_of_birth' => ['nullable','date'],
            'gender' =>     ['required'],
            'phone_number' =>  ['required','min:11','unique:staff,phone_number'],
            'residential_address' =>  ['nullable'],
            'local_government' => ['nullable'],
            'state' => ['nullable'],
            'nin' => ['nullable','min:11','unique:staff,nin'],
            'bvn' => ['nullable', 'min:11', 'max:11', 'unique:staff,bvn'],
            'bank_account' =>  ['nullable', 'min:10', 'max:10'],
            'bank_name' => ['nullable'],
            'date_of_appointment' =>    ['required','date'],
            'position' =>               ['required', new Enum(StaffPositionEnums::class)],
            'department'  =>            ['required', new Enum(StaffDepartmentEnums::class)],
            'monthly_salary'  =>        ['nullable', 'numeric'],
            'photo'      =>  ['nullable', File::types(['png', 'jpg']),'max:5120'],
        ];

        // Only super admins can provide branch_id
        if ($request->user()->can('super_admin')) {
            $rules['branch_id'] = ['required', 'exists:branches,id'];
        }

        $validatedData = $request->validate($rules);

        $validatedData['first_name'] = strtolower($request->input('first_name'));
        $validatedData['last_name'] = strtolower($request->input('last_name'));

        if($request->has('other_name')){
            $validatedData['other_name'] = strtolower($request->input('other_name'));
        }

        if($request->hasFile('photo')){

            // Generate a unique filename
            $imageName = $request->first_name . '_' . time() . '.' . $request->photo->extension();
            // Create a new ImageManager instance with GD driver
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($request->file('photo'));
            // Check the file size
            $fileSize = $request->file('photo')->getSize(); // Get the size in bytes
            // Encode the image with reduced quality (e.g., 75%)
             // Set the quality based on the file size
            if ($fileSize > 2 * 1024 * 1024) { // Check if file size is greater than 2MB
                $quality = 50; // Set quality to 50%
            } else {
                $quality = 75; // Set quality to 75% for files less than or equal to 2MB
            }

            // Encode the image with the determined quality
            $encodedImage = $image->encode(new AutoEncoder(quality: $quality));
            // Save the encoded image to storage
            Storage::put('staff/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'staff/photos/' . $imageName;

        }


        $staff = Staff::create($validatedData);
        log_new("$staff->full_name (staff) was registered");

        return redirect()->back()->with('success', 'Staff register successfully!!!');

    }

    public function show(Staff $staff){
        $this->authorize('view', $staff);
        $staff->load(['user']);

        return view('staffs.show', compact('staff'));
    }


    public function update(Request $request, Staff $staff){
        $validatedData = $request->validate([
            'last_name' =>  ['required', new OneWord],
            'first_name' => ['required', new OneWord],
            'other_name' => ['nullable',  new OneWord],
            'email' =>      ['required','email', Rule::unique('staff', 'email')->ignore($staff->id)],
            'staff_number' =>   ['required','string','min:4','max:6', Rule::unique('staff', 'staff_number')->ignore($staff->id)],
            'date_of_birth' => ['nullable','date'],
            'gender' =>     ['required'],
            'phone_number' =>  ['required','min:11', Rule::unique('staff', 'phone_number')->ignore($staff->id)],
            'residential_address' =>  ['nullable'],
            'local_government' => ['nullable'],
            'state' => ['nullable'],
            'nin' => ['nullable','min:11',Rule::unique('staff', 'nin')->ignore($staff->id)],
            'bvn' => ['nullable', 'min:11', 'max:11', Rule::unique('staff', 'bvn')->ignore($staff->id)],
            'bank_account' =>  ['nullable', 'min:10', 'max:10'],
            'bank_name' => ['nullable'],
            'date_of_appointment' =>    ['nullable','date'],
            'position' =>               ['nullable', new Enum(StaffPositionEnums::class)],
            'department'  =>            ['nullable', new Enum(StaffDepartmentEnums::class)],
            'monthly_salary'  =>        ['nullable', 'numeric'],
            'photo'      =>  ['nullable', File::types(['png', 'jpg']),'max:5120'],
        ]);

        $validatedData['first_name'] = strtolower($request->input('first_name'));
        $validatedData['last_name'] = strtolower($request->input('last_name'));

        if($request->has('other_name')){
            $validatedData['other_name'] = strtolower($request->input('other_name'));
        }



        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
             if ($staff->photo && Storage::exists($staff->photo)) {
                Storage::delete($staff->photo);
            }

            // Generate a unique filename
            $imageName = $request->first_name . '_' . time() . '.' . $request->photo->extension();
            // Create a new ImageManager instance with GD driver
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($request->file('photo'));
            // Check the file size
            $fileSize = $request->file('photo')->getSize(); // Get the size in bytes
            // Encode the image with reduced quality (e.g., 75%)
             // Set the quality based on the file size
            if ($fileSize > 2 * 1024 * 1024) { // Check if file size is greater than 2MB
                $quality = 50; // Set quality to 50%
            } else {
                $quality = 75; // Set quality to 75% for files less than or equal to 2MB
            }

            // Encode the image with the determined quality
            $encodedImage = $image->encode(new AutoEncoder(quality: $quality));
            // Save the encoded image to storage
            Storage::put('staff/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'staff/photos/' . $imageName;



        }

        $staff->update($validatedData);


        log_new("$staff->full_name (staff) was updated");

        return redirect()->back()->with('success', 'Staff updated successfully!!!');
    }

    public function destroy(Staff $staff){
        try {
            // Attempt to delete the staff
            if ($staff->photo && Storage::exists($staff->photo)) {
                Storage::delete($staff->photo); // Delete the photo if it exists
            }
            log_new("Deleting $staff->full_name (staff) record");

            $staff->delete();

            return redirect()->route('staff.index')->with('success', 'Staff deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for foreign key constraint error (SQLSTATE[23000])
            if ($e->getCode() == '23000') {
                return redirect()->route('staff.index')->with('error', 'Staff cannot be deleted as it is associated with other records.');
            }

            // For any other errors, rethrow the exception
            throw $e;
        }
    }

}
