<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Department;
use App\Designation;
use App\Employee;
use Image;
use Session;

class EmployeeController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $employees = Employee::paginate(5);
        // return view("Employee.index", compact("employees"));

        $employees = DB::table('employees') 
        ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
        ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
        ->select('employees.*', 'departments.name as department_name', 'departments.id as department_id', 'designations.name as designation_name', 'designations.id as designation_id' )
        ->paginate(5);
        // $employees = Employee::paginate(5);
        return view("Employee.index",['employees' => $employees]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::all();
        $departments = Department::all();
        $designations = Designation::all();
        return view('Employee.create', ['departments' => $departments, 'designations' => $designations]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $employees = Employee::all();
        // $this->validateInput($request);
        // // Upload image

        // $path = $request->file('avatar')->store('public');
        // $keys = ['name', 'email','age', 'phone','address','dateofbirth', 'department_id', 'designation_id', 'joined'];
        // $input = $this->createQueryInput($keys, $request);
        // $input['avatar'] = $path;
        // Employee::create($input);
        // return view('Employee.index', compact('employees', 'avatar'));

            $request-> validate ([
            'photo' => 'required',
            'name' => 'required|min:3',
            'email' => 'required|email',
            'age' => 'required|integer',
            'phone' => 'required',
            'address' => 'required',
            'dateofbirth' => 'required',
            'department_id' => 'required',
            'designation_id' => 'required',
            'joined' => 'required'
        ]);


        $employees = DB::table('employees');

        if($request->hasFile('photo')){
            $photo = $request->file('photo');
            $filename = $photo->getClientOriginalName();
            Image::make($photo)->resize(300, 300)->save('uploads/photos/'. $filename );

            $employees->photo = $photo;
            // $employees->save();
        }
        Employee::create($request->all());
        return view('Employee.index', compact('employees'))
                    ->with('success', "Successful Registered");

    }

        // $request->file('avatar');
        // Storage::put('public/image', $request->file('avatar'));
        // Employee::create($request->except('_token'));

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employees = Employee::find($id);
        $departments = Department::select('name', 'id')->get(); 
        $designations = Designation::select('name', 'id')->get(); 
        return view('Employee.edit',['employees' => $employees,'departments' => $departments,'designations' => $designations]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'photo' => 'required',
            'name' => 'required|min:3',
            'email' => 'required|email',
            'age' => 'required|integer',
            'phone' => 'required',
            'address' => 'required',
            'dateofbirth' => 'required',
            'department_id' => 'required',
            'designation_id' => 'required',
            'joined' => 'required'
        ]);
                var_dump($request->all);
        Employee::find($id)->update($request->all());

         Session::flash('message', 'You have successfully updated Product.'); 
         return redirect()->route('emp.index');
        // $employee = Employee::findOrFail($id);
        // $this->validateInput($request);
        // // Upload image
        // $keys = ['name', 'email', 'age', 'phone','address','dateofbirth', 'department_id', 'designation_id', 'joined'];
        // $input = $this->createQueryInput($keys, $request);
        // if ($request->file('avatar')) {
        //     $path = $request->file('avatar')->store('public');
        //     $input['avatar'] = $path;
        // }

        // Employee::where('id', $id)
        //     ->update($input);

          
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Employee::find($id)->delete();
        return redirect()->route('emp.index')
                        ->with('success', "Employee info Deleted Successful");
        // $employees = Employee::findOrFail($id);
        // Employee::destroy($id);

        // return redirect()->route("emp.index", "Successful Deleted");
        // $products = Product::findOrFail($id);
        // if(\Auth::user()->can('delete-products', $products)) {
        //     Product::destroy($id);
        // }
        // return redirect()->route("product.index");    
    }


    // public function load($name) {
    //      $path = storage_path().'/public/'.$name;
    //     if (file_exists($path)) {
    //         return Response::download($path);
    //     }
    // }

    // private function validateInput(Request $request) {
    //     $this->validate($request, [
    //         'photo' => 'required',
    //         'name' => 'required|min:3',
    //         'email' => 'required|email',
    //         'age' => 'required|integer',
    //         'phone' => 'required',
    //         'address' => 'required',
    //         'dateofbirth' => 'required',
    //         'department_id' => 'required',
    //         'designation_id' => 'required',
    //         'joined' => 'required'
    //     ]);
    //     // $path = $request->file('avatar')->put('avatars');
    // }

    private function createQueryInput($keys, $request) {
        $queryInput = [];
        for($i = 0; $i < sizeof($keys); $i++) {
            $key = $keys[$i];
            $queryInput[$key] = $request[$key];
        }

        return $queryInput;
    }

     public function data(Request $request) {

        if($request->ajax()) {

            $employees = Employee::latest();

            return Datatables::of($employees)
                ->addColumn("action", function($employees) {
                    $data = '<div class="col-md-3"><a href="'.route("emp.edit", $employees->id).'"><button class="btn btn-success"><i class="fa fa-pencil"></i></button></a></div>'.
                            '<div class="col-md-1"><form action="' . route('emp.destroy', $employees->id). '" method="post">'
                                . csrf_field() .
                                 method_field("delete") .
                                '<button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                            </form></div>';
                            return $data;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    return abort(404);
}

}

    //     public function upload(Request $request){

    //     // Handle the user upload of avatar
    //         $employees = Employee::all();
    //     if($request->hasFile('myphoto')){
    //         $avatar = $request->file('myphoto');
    //         $filename = time() . '.' . $avatar->getClientOriginalExtension();
    //         Image::make($avatar)->resize(300, 300)->save( public_path('/uploads/photos/' . $filename ) );

    //         $employees = Auth::user();
    //         $employees->avatar = $filename;
    //         $employees->save();
    //     }

    //     return view('Employee.index', compact('employees'));

    // }





        //     ->addColumn("edit", function($employees) {
        //             $data = "<a class='btn btn-success' href=" . route("emp.edit", $model->id) . ">Edit</a>";
        //             return $data;
        //         })
        //     ->addColumn("delete", function($employees) {
        //             $data = '<form action="' . route('emp.destroy', $model->id). '" method="post">'
        //                         . csrf_field() .
        //                          method_field("delete") .
        //                         '<button class="btn btn-danger">Delete</button>
        //                     </form>';
        //             return $data;
        //         })
        //     ->rawColumns(['edit', 'delete'])
        //     ->toJson();
        // }
        // 
            
// <ul class="dropdown-menu pull-right">
//                                         <li><a href="#" data-toggle="modal" data-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a></li>
//                                         <li><a href="#" data-toggle="modal" data-target="#delete_employee"><i class="fa fa-trash-o m-r-5"></i> Delete</a></li>
//                                     </ul>