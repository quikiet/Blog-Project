<?php

namespace App\Http\Controllers;

use App\Models\refuseReasons;
use App\Models\refuses;
use App\Http\Requests\StorerefusesRequest;
use App\Http\Requests\UpdaterefusesRequest;
use Exception;
use Illuminate\Http\Request;

class RefusesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $refuseReason = refuseReasons::with('refuses')->get();
            return response()->json(['message' => 'Truy vấn lý do từ chối thành công'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi truy vấn lý do từ chối",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validationFields = $request->validate([
                'reason' => 'required|unique'
            ]);

            $refuseReason = refuseReasons::create($validationFields);
            return response()->json(['message' => 'Thêm lý do thành công'], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi khi thêm",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(refuses $refuses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validationFields = $request->validate([
                'reason' => 'required|unique'
            ]);

            $refuseReason = refuseReasons::findOrFail($id);
            $refuseReason->update($validationFields);
            return response()->json(['message' => 'Sửa lý do thành công'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi khi cập nhật",
                'error' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $refuseReason = refuseReasons::findOrFail($id);
            $refuseReason->delete();
            return response()->json(['message' => 'Xoá lý do thành công'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi khi xoá",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
