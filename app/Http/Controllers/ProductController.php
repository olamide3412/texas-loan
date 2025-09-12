<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q', '');

        $products = Product::latest()
            ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', '%' . $q . '%')
                      ->orWhere('description', 'LIKE', '%' . $q . '%');
            })
            ->paginate(25);

        return inertia('Auth/Products/Index',[
            'products' => $products,
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        //Not in use cause index has both list and create
        return view('products.create');
    }

    /**
     * Store a new product
     */
    public function store(Request $request)
    {
        $request->merge([
            'price' => str_replace(',', '', $request->input('price')),
        ]);

        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'photo' => ['nullable', File::types(['png', 'jpg', 'jpeg']), 'max:5120'],
        ];

        $validatedData = $request->validate($rules);

        if ($request->hasFile('photo')) {
            // Generate filename
            $imageName = Str($request->name)->slug('_') . '_' . time() . '.' . $request->photo->extension();

            // Use Intervention
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($request->file('photo'));
            $fileSize = $request->file('photo')->getSize();

            // Quality adjustment
            $quality = $fileSize > 2 * 1024 * 1024 ? 50 : 75;

            $encodedImage = $image->encode(new AutoEncoder(quality: $quality));
            Storage::disk('public')->put('products/photos/' . $imageName, $encodedImage);
            Storage::put('products/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'products/photos/' . $imageName;
        }

        $product = Product::create($validatedData);

        log_new("Product {$product->name} created");

        return redirect()->route('product.show', $product->id)->with('success', 'Product created successfully!');
    }

    /**
     * Show single product
     */
    public function show(Product $product)
    {
        //dd($product->toArray());
        return inertia('Auth/Products/Show',[
            'product' => $product,
        ]);
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        //dd($request->toArray());
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($product->id)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'photo' => ['nullable', File::types(['png', 'jpg', 'jpeg']), 'max:5120'],
        ];

        $validatedData = $request->validate($rules);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($product->photo && Storage::exists($product->photo)) {
                Storage::delete($product->photo);
            }

            // Generate filename
            $imageName = Str($request->name)->slug('_') . '_' . time() . '.' . $request->photo->extension();

            $manager = new ImageManager(Driver::class);
            $image = $manager->read($request->file('photo'));
            $fileSize = $request->file('photo')->getSize();

            $quality = $fileSize > 2 * 1024 * 1024 ? 50 : 75;

            $encodedImage = $image->encode(new AutoEncoder(quality: $quality));
            Storage::disk('public')->put('products/photos/' . $imageName, $encodedImage);
            Storage::put('products/photos/' . $imageName, $encodedImage);
            $validatedData['photo'] = 'products/photos/' . $imageName;
        }

        $product->update($validatedData);

        log_new("Product {$product->name} updated");

        return redirect()->route('product.show', $product->id)->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        try {
            if ($product->photo && Storage::exists($product->photo)) {
                Storage::delete($product->photo);
            }

            log_new("Deleting product {$product->name}");

            $product->delete();

            return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('products.index')->with('error', 'Product cannot be deleted because it is linked to orders.');
            }
            throw $e;
        }
    }
}
