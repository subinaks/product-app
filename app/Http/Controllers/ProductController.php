<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }
    public function create()
    {
        return view('products.create');
    }


    public function store(ProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $imageName = time() . '.' . $request->main_image->extension();
            $request->main_image->move(public_path('images'), $imageName);

            $product = Product::create([
                'title' => $request->title,
                'description' => $request->description,
                'main_image' => $imageName,
            ]);

            if ($request->has('variants')) {
                $product->variants()->createMany($request->variants);
            }

            DB::commit();

            return response()->json(['success' => 'Product added successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 'Failed to add product'], 500);
        }
    }

    public function edit($id)
    {

        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();
    
            $product->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);
    
            if ($request->hasFile('main_image')) {
                $imageName = time() . '.' . $request->main_image->extension();
                $request->main_image->move(public_path('images'), $imageName);
                $product->main_image = $imageName;
            }
    
            $product->save();
    
            $product->variants()->delete();
            if ($request->has('variants')) {
                $product->variants()->createMany($request->variants);
            }
    
            DB::commit();
    
            return response()->json(['success' => 'Product updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 'Failed to update product'], 500);
        }
    }
    
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return response()->json(['success' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Failed to delete product'], 500);
        }
    }
    
}
