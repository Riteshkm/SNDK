<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;
use DataTables;
use Auth;
class CategoryController extends Controller
{
    use Notifiable;
    function __construct()
    {
        $this->middleware(['permission:category-list|category-create|category-edit|category-delete'], ['only' => ['index','show']]);
        $this->middleware(['permission:category-create'], ['only' => ['create','store']]);
        $this->middleware(['permission:category-edit'], ['only' => ['edit']]);
        $this->middleware(['permission:category-delete'], ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->request = $request;
        if ($request->ajax()) {
            return DataTables::of(Category::orderBy('created_at','DESC'))
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<div class="" role="group" style="white-space: nowrap">';
                    $btn.=' <button onClick=editCategory('.($row->id).') type="button" title="Edit" class="btn btn-info">Edit</button>';
                    $btn.=' <button onClick=deleteCategor('.($row->id).') type="button"  title="Delete" class="btn btn-danger">Delete</button>';
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
        return view('categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);
        $request['created_by'] =  Auth::id();
        $category = Category::create($request->all());
        return response()->json($category, 201);
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
        $category = Category::find($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);
        $request['updated_by'] =  Auth::id();
        $category->update($request->all());
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
