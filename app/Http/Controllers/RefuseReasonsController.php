<?php

namespace App\Http\Controllers;

use App\Models\refuseReasons;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RefuseReasonsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $refuseReason = refuseReasons::with('refuses')->get();
            return response()->json($refuseReason, 200);
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
                'reason' => 'required|unique:refuse_reasons,reason'
            ]);

            $refuseReason = refuseReasons::create($validationFields);
            return response()->json([
                'message' => 'Thêm lý do thành công',
                'data' => $refuseReason
            ], 201);
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
    public function show($id)
    {
        try {
            $refuseReason = refuseReasons::where('id', $id)->firstOrFail();
            return response()->json($refuseReason, 200);
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
                'reason' => 'required|unique:refuse_reasons,reason,' . $id . ',id'
            ]);

            $refuseReason = refuseReasons::where('id', $id)->firstOrFail();
            $refuseReason->update($validationFields);
            return response()->json([
                'message' => 'Sửa lý do thành công'
            ], 200);
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
            $refuseReason = refuseReasons::where('id', $id)->firstOrFail();
            $refuseReason->delete();
            return response()->json(['message' => 'Xoá lý do thành công'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi khi xoá",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            Log::info("Request nhận được:", $request->all()); // Kiểm tra request

            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer',
            ]);

            $ids = $validated['ids'];

            if (empty($ids)) {
                return response()->json(['message' => 'Không có ID hợp lệ để xóa'], 400);
            }

            $deleteCount = RefuseReasons::whereIn('id', $ids)->delete();

            if ($deleteCount === 0) {
                return response()->json(['message' => 'Không có lý do nào được xóa'], 404);
            }

            return response()->json(['message' => "Đã xóa $deleteCount lý do"], 200);
        } catch (Exception $e) {
            Log::error("Lỗi khi xóa lý do: " . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa lý do', 'error' => $e->getMessage()], 500);
        }
    }
}
