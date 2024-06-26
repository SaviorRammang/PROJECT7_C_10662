<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;

class ProductController extends Controller
{

    public function index() //method read atau menampilkan semua data product
    {
        $product = Product::all();

        if(count($product) > 0){
            return response([
                'message' => 'Retrive All Success',
                'data' => $product
            ], 200);
        } // return semua data semua product dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function store(Request $request)
    {
        $storeData = $request->all();
        
        $configData = [

            'table' => 'products',
            'field' => 'kode',
            'length' => 10,
            'prefix' => date('ddMMMyy-')
        ];
        
        $kode = IdGenerator::generate($configData);
        
        $kode = IdGenerator::generate([

                'table' => 'products',
                'field' => 'kode', 
                'length' => 10, 
                'prefix' => date('dMy-')
            ]);
    
        
        $validate = Validator::make($storeData, [
            'nama_barang' => 'required|max:60|unique:products',
            'harga' => 'required|numeric',
            'jumlah' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
                
        $product = Product::create($storeData);
        return response([
            'message' => 'Add product Success',
            'data' => $product
        ], 200);
    }

    public function show ($id) 
    {
        $product = Product::find($id); 

        if(!is_null($product)){
            return response([
                'message' => 'Retrive Product Success',
                'data' => $product
            ], 200);
        }

        return response([
            'message'=> 'Product Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id) 
    {
        $product = Product::find($id);

        if(is_null($product)){
            return response([
                'message' => 'Product Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_barang' => ['required', 'max:60', Rule::unique('products')->ignore($product)],
            'kode' => 'required',
            'harga' => 'required|numeric',
            'jumlah' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $product->nama_barang = $updateData['nama_barang'];
        $product->kode = $updateData['kode'];
        $product->harga = $updateData['harga'];
        $product->jumlah = $updateData['jumlah'];

        if($product->save()){
            return response([
                'message'=> 'Update Product Succes',
                'data'=> $product

            ],200);
        }

        return response([
            'message'=> 'Update Product Failed',
            'data'=> $product

        ],400);

    }
  
    public function destroy($id)// method delete atau menghapus sebuah data product
    {
        $product = Product::find($id);

        if(is_null($product)){
            return response([
                'message' => 'Product Not Found',
                'data' => null
            ], 404);
        }

        if($product->delete()){
            return response([
                'message'=> 'Delete Product Succes',
                'data'=> $product

            ],200);
        }

        return response([
            'message'=> 'Delete Product Failed',
            'data'=> $product
        ],400);
    }
}
