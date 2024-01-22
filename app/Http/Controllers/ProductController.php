<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;
use DataTables;
use Auth;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:products-list|products-create|products-edit|products-delete'], ['only' => ['index','show']]);
        $this->middleware(['permission:products-create'], ['only' => ['create','store']]);
        $this->middleware(['permission:products-edit'], ['only' => ['edit']]);
        $this->middleware(['permission:products-delete'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->request = $request;
        if ($request->ajax()) {
            return DataTables::of(Product::orderBy('created_at','DESC'))
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<div class="" role="group" style="white-space: nowrap">';
                    $btn.=' <a href="'.route('products.edit', $row->id) .'" title="Edit" class="btn btn-info">Edit</a>';
                    $btn.=' <button onClick=deleteProduct('.($row->id).') type="button"  title="Delete" class="btn btn-danger">Delete</button>';
                    return $btn;
                })
                ->addColumn('created_at',function($row){
                    $date = Carbon::parse($row->created_at);
                    return $date->format('M d,Y H:i A');
                })
                ->addColumn('updated_at',function($row){
                    $date = Carbon::parse($row->updated_at);
                    return $date->format('M d,Y H:i A');
                })
                ->rawColumns(['action','created_at','updated_at'])
                ->make(true);
        }
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories=Category::pluck('name','id');
        return view('products.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'required|max:255',
            'regular_price' => 'required|numeric',
            'size' => 'required|array|min:1',
            'size.*' => 'required',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $product = new Product();
            $product->category_id = $request->input('category_id');
            $product->sub_category_id = $request->input('sub_category_id');
            $product->name = $request->input('name');
            $product->price = $request->input('regular_price');
            $product->created_by =  Auth::id();
            $product->save();

            foreach ($request->input('size') as $key => $size) {
                $sizePrice = new ProductSize();
                $sizePrice->product_id = $product->id;
                $sizePrice->size = $size;
                $sizePrice->price = $request->input('price')[$key];
                $sizePrice->created_by =  Auth::id();
                $sizePrice->save();
            }

            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = 'images/' . $imageName;
                $productImage->created_by =  Auth::id();
                $productImage->save();
            }

            DB::commit();

            return response()->json(['message' => 'Product created successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create the product'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edit=true;
        $categories=Category::pluck('name','id');
        $product = Product::find($id);
        $sub_categories=Subcategory::where('category_id',$product->category_id)->pluck('name','id');
        $productImage = ProductImage::where('product_id',$id)->get();
        $productSize = ProductSize::where('product_id',$id)->get();
        return view('products.edit',compact('edit','categories','product','sub_categories','productImage','productSize'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        $productSize = ProductSize::where('product_id',$product->id)->pluck('id')->toArray();
        
        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'required|max:255',
            'regular_price' => 'required|numeric',
            'size' => 'required|array|min:1',
            'size.*' => 'required',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric',
            // 'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $product->category_id = $request->input('category_id');
            $product->sub_category_id = $request->input('sub_category_id');
            $product->name = $request->input('name');
            $product->price = $request->input('regular_price');
            $product->updated_by =  Auth::id();
            $product->update();

            if($request->file('images') != '' && count($request->file('images')) > 0){
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = 'images/' . $imageName;
                $productImage->created_by =  Auth::id();
                $productImage->save();
            }
            }
            if(count($request->input('size'))>0){
                foreach ($request->input('size') as $key => $size) {
                    if($request->input('product_size_id')[$key] > 0){
                        $sizePrice = ProductSize::find($request->input('product_size_id')[0]);
                        $sizePrice->product_id = $product->id;
                        $sizePrice->size = $size;
                        $sizePrice->price = $request->input('price')[$key];
                        $sizePrice->updated_by =  Auth::id();
                        $sizePrice->update();
                    }else{
                        $sizePrice = new ProductSize();
                        $sizePrice->product_id = $product->id;
                        $sizePrice->size = $size;
                        $sizePrice->price = $request->input('price')[$key];
                        $sizePrice->created_by =  Auth::id();
                        $sizePrice->save();
                    }
                }
            }

            $deleteSize = array_diff($productSize,$request->input('product_size_id'));
           
            if(count($deleteSize)>0){
                $productSize = ProductSize::whereIn('id',$deleteSize);
                $productSize->delete();
            }

            DB::commit();
            return response()->json(['message' => 'Product Update successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to Update the product'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        $productImage = ProductImage::where('product_id',$id);
        $productSize = ProductSize::where('product_id',$id);
        $product->delete();
        $productImage->delete();
        $productSize->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function fetchSubcategories(Request $request)
    {
        $category_id = $request->input('category_id');

        // Fetch sub-categories based on the selected category_id
        $subcategories = Subcategory::where('category_id', $category_id)->pluck('name', 'id');

        return response()->json($subcategories);
    }

    public function deleteProductsImage(string $id){
        $productImage = ProductImage::find($id);
        $productImage->delete();
        return response()->json(['message' => 'Product Image deleted successfully']);
    }
}
