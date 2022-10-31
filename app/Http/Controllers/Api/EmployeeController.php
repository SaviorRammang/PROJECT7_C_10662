<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;


use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index() 
    {
        $employee = Employee::all();

        if(count($employee) > 0){
            return response([
                'message' => 'Retrive All Success',
                'data' => $employee
            ], 200);
        } 

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function store(Request $request) 
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            $storeData['tgl_lahir'] = Carbon::createFromFormat('d-m-Y', $request->tgl_lahir)->format('Y-m-d'),
            'nama_pegawai' => 'required|regex:/^[\pL\s]+$/u', 
            'nip' => 'required|numeric|digits:6',
            'role' => 'required|in:Staff,Admin,Manager',
            'alamat' => 'required',
            'tgl_lahir' => 'required|date_format:d-m-Y', 
            'no_telp' => 'required|min:11|max:13|regex:/[0]+[8]/'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $employee = Employee::create($storeData);
        
        return response([
            'message' => 'Add Employee success',
            'data' => $employee
        ],200);
    }
    public function show ($id) 
    {
        $employee = Employee::find($id); // 

        if(!is_null($employee)){
            return response([
                'message' => 'Retrive Employee Success',
                'data' => $employee
            ], 200);
        }

        return response([
            'message'=> 'Employee Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id) 
    {
        $employee = Employee::find($id);

        if(is_null($employee)){
            return response([
                'message' => 'Employee Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
        $updateData['tgl_lahir'] = Carbon::createFromFormat('d-m-Y', $request->tgl_lahir)->format('Y-m-d'),
            'nama_pegawai' => 'required|regex:/^[\pL\s]+$/u', 
            'nip' => 'required|numeric|digits:6',
            'role' => 'required|in:Staff,Admin,Manager',
            'alamat' => 'required',
            'tgl_lahir' => 'required|date_format:d-m-Y',
            'no_telp' => 'required|min:11|max:13|regex:/[0]+[8]/'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $employee->nama_pegawai = $updateData['nama_pegawai'];
        $employee->nip = $updateData['nip'];
        $employee->role = $updateData['role'];
        $employee->alamat = $updateData['alamat'];
        $employee->tgl_lahir = $updateData['tgl_lahir'];
        $employee->no_telp = $updateData['no_telp'];


        if($employee->save()){
            return response([
                'message'=> 'Update Employee Succes',
                'data'=> $employee

            ],200);
        }

        return response([
            'message'=> 'Update Employee Failed',
            'data'=> $employee

        ],400);

    }
  
    public function destroy($id)// method delete atau menghapus sebuah data 
    {
        $employee = Employee::find($id);

        if(is_null($employee)){
            return response([
                'message' => 'Employee Not Found',
                'data' => null
            ], 404);
        }

        if($employee->delete()){
            return response([
                'message'=> 'Delete Employee Succes',
                'data'=> $employee

            ],200);
        }

        return response([
            'message'=> 'Delete Employee Failed',
            'data'=> $employee
        ],400);
    }
}
