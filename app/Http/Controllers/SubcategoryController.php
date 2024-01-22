<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;
use DataTables;
use Auth;
class SubcategoryController extends Controller
{
    use Notifiable;
    function __construct()
    {
        $this->middleware(['permission:sub_categories-list|sub_categories-create|sub_categories-edit|sub_categories-delete'], ['only' => ['index','show']]);
        $this->middleware(['permission:sub_categories-create'], ['only' => ['create','store']]);
        $this->middleware(['permission:sub_categories-edit'], ['only' => ['edit']]);
        $this->middleware(['permission:sub_categories-delete'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->request = $request;

        if ($request->ajax()) {            
            return DataTables::of(Subcategory::with('category')->orderBy('created_at','DESC'))
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<div class="" role="group" style="white-space: nowrap">';
                    $btn.=' <button onClick=editSubcategory('.($row->id).') type="button" title="Edit" class="btn btn-info">Edit</button>';
                    $btn.=' <button onClick=deleteSubcategor('.($row->id).') type="button"  title="Delete" class="btn btn-danger">Delete</button>';
                    return $btn;
                })
                ->addColumn('category_name',function($row){
                    return $row->category->name;
                })
                ->addColumn('created_at',function($row){
                    $date = Carbon::parse($row->created_at);
                    return $date->format('M d,Y H:i A');
                })
                ->addColumn('updated_at',function($row){
                    $date = Carbon::parse($row->updated_at);
                    return $date->format('M d,Y H:i A');
                })
                ->rawColumns(['action','created_at','updated_at','category_name'])
                ->make(true);
        }
        $categories=Category::pluck('name','id');
        return view('sub_categories.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|string|max:255|unique:subcategories',
        ]);
        $request['created_by'] =  Auth::id();
        $sub_category = Subcategory::create($request->all());
        return response()->json($sub_category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sub_category = Subcategory::find($id);
        return response()->json($sub_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sub_category = Subcategory::find($id);
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|string|max:255|unique:subcategories,name,' . $sub_category->id,
        ]);
        $request['updated_by'] =  Auth::id();
        $sub_category->update($request->all());
        return response()->json($sub_category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sub_category = Subcategory::find($id);
        $sub_category->delete();
        return response()->json(['message' => 'Subcategory deleted successfully']);

    }
}
