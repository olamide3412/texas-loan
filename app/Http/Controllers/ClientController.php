<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Customer;
use App\Rules\OneWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

class ClientController extends Controller
{
    public function index(Request $request){
        $q = $request->get('search', '');
        $orderBy = request('orderBy', 'id');
        $orderDir = request('orderDir', 'asc');

        $clients = Client::when($request->search, function($query) use($q){
            $query->where('last_name','like', '%'.$q.'%')
                    ->orWhere('first_name','like', '%'.$q.'%')
                    ->orWhere('other_name','like', '%'.$q.'%')
                    ->orWhere('email','like', '%'.$q.'%')
                    ->orWhere('phone_number','like', '%'.$q.'%')
                    ->orWhere('nin','like', '%'.$q.'%')
                    ->orWhere('bvn','like', '%'.$q.'%');
        })->orderBy($orderBy, $orderDir)->paginate(5)->withQueryString();

        //dd($clients->toArray());
        return inertia('Auth/Clients/Index',[
            'clients' => $clients,
        ]);

    }

    public function store(Request $request) {
         //dd($request->all());
        $request->merge([
            'annual_income' => str_replace(',', '', $request->input('annual_income')),
        ]);
        $rules = [
            'last_name' =>  ['required', new OneWord],
            'first_name' => ['required', new OneWord],
            'other_name' => ['nullable',  new OneWord],
            'email' =>      ['nullable','email','unique:clients,email'],
            'date_of_birth' => ['required','date'],
            'gender' =>     ['required'],
            'phone_number' =>  ['required','min:11','unique:clients,phone_number'],
            'residential_address' =>  ['required'],
            'local_government' => ['required'],
            'state' => ['required'],
            'occupation' =>  ['required'],
            'nin' => ['required','min:11','unique:clients,nin'],
            'bvn' => ['required', 'min:11', 'max:11', 'unique:clients,bvn'],
            'annual_income' => ['required', 'numeric', 'min:0'],
            'photo'      =>  ['nullable', File::types(['png', 'jpg']),'max:5120'],
        ];

        $validatedData = $request->validate($rules);


        $validatedData['first_name'] = strtolower($request->input('first_name'));
        $validatedData['last_name'] = strtolower($request->input('last_name'));

        if($request->has('other_name')){
            $validatedData['other_name'] = strtolower($request->input('other_name'));
        }

        $validatedData['registered_by'] = Auth::check() ? Auth::user()->id :null;


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
            Storage::put('client/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'client/photos/' . $imageName;

        }



        $client = Client::create($validatedData);
        log_new("$client->full_name (client) was registered,  Verification is pending");

        return redirect(route('client.show', $client->id))->with('success', 'Client register successfully!!!');
        //return redirect()->back()->with('success', 'Client register successfully!!!');

    }


    public function show(Client $client){
        //dd($client->toArray());

        return inertia('Auth/Clients/Show',[
            'client' => $client,
        ]);
    }

    public function update(Request $request, Client $client)
    {
        //dd($request->all());

        $request->merge([
            'annual_income' => str_replace(',', '', $request->input('annual_income')),
        ]);
        $validatedData = $request->validate([
            'last_name' => ['required', new OneWord],
            'first_name' => ['required', new OneWord],
            'other_name' => ['nullable', new OneWord],
            'email' => ['nullable', 'email', Rule::unique('clients', 'email')->ignore($client->id)],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required'],
            'phone_number' => ['required', 'min:11', Rule::unique('clients', 'phone_number')->ignore($client->id)],
            'residential_address' => ['required'],
            'local_government' => ['required'],
            'state' => ['required'],
            'occupation' => ['required'],
            'nin' => ['required', 'min:11', Rule::unique('clients', 'nin')->ignore($client->id)],
            'bvn' => ['required', 'min:11', 'max:11', Rule::unique('clients', 'bvn')->ignore($client->id)],
            'annual_income' => ['required', 'numeric', 'min:0'],
            'photo' => ['nullable', File::types(['png', 'jpg']), 'max:5120'],
        ]);

        $validatedData['first_name'] = strtolower($request->input('first_name'));
        $validatedData['last_name'] = strtolower($request->input('last_name'));

        if ($request->has('other_name')) {
            $validatedData['other_name'] = strtolower($request->input('other_name'));
        }



        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($client->photo && Storage::exists($client->photo)) {
                Storage::delete($client->photo);
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
            Storage::put('client/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'client/photos/' . $imageName;
        }

        $client->update($validatedData);

        log_new("$client->full_name (client) detatils was updated");

        return redirect()->route('client.show', $client->id)->with('success', 'Client details updated successfully!');
    }

    public function destroy(Client $client)
    {
        try {
            // Attempt to delete the client
            if ($client->photo && Storage::exists($client->photo)) {
                Storage::delete($client->photo); // Delete the photo if it exists
            }
            log_new("Deleting $client->full_name (client) record");

            $client->delete();

            return redirect()->route('client.index')->with('success', 'Client deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for foreign key constraint error (SQLSTATE[23000])
            if ($e->getCode() == '23000') {
                return redirect()->route('client.index')->with('error', 'Client cannot be deleted as it is associated with other records.');
            }

            // For any other errors, rethrow the exception
            throw $e;
        }
    }
}
