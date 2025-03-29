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
    public function index()
    {
        $refuse = refuses::all();
        return response()->json([
            'message' => 'Lấy ràng buộc các bài báo bị từ chối',
            'data' => $refuse
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validationFields = $request->validate([
                'post_id' => 'required|exists:posts,id',
                'reason_id' => 'required|exists:refuse_reasons,id'
            ]);

            $refuses = refuses::create($validationFields);
            return response()->json([
                'message' => 'Thêm ràng buộc lý do từ chối bài viết thành công',
                'data' => $refuses
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi khi thêm ràng buộc",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $refuse = refuses::where('id', $id)->firstOrFail();
            return response()->json($refuse, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validationFields = $request->validate([
                'post_id' => 'required|exists:posts,id',
                'reason_id' => 'required|exists:refuse_reasons,id'
            ]);

            $refuses = refuses::findOrFail($id);
            $refuses->update($validationFields);
            return response()->json([
                'message' => 'Cập nhật buộc lý do từ chối bài viết thành công',
                'data' => $refuses
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi khi cập nhật ràng buộc",
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
            $refuse = refuses::where('id', $id)->firstOrFail();
            $refuse->delete();
            return response()->json(['message' => 'Xoá lý do thành công'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi khi xoá",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
