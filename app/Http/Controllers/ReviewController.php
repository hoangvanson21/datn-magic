<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Reviews::all();
        return view('admin.Reviews.index', compact('reviews'));
    }
    public function toggleStatus($id)
    {
        $review = Reviews::find($id);
        if ($review) {
            // Nếu status là 0 (hiện), chuyển thành 1 (ẩn), ngược lại
            $review->status = ($review->status == 0) ? 1 : 0;
            $review->save();

            return redirect()->route('admin.reviews.index')
                ->with('status', 'Cập nhật trạng thái thành công!');
        }

        return redirect()->route('admin.reviews.index')
            ->with('error', 'Không tìm thấy bình luận!');
    }
    // ReviewController.php

    public function edit($id)
    {
        $review = Reviews::findOrFail($id); // Tìm review theo ID
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'review_text' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|in:0,1', 
        ]);

        $review = Reviews::findOrFail($id); // Tìm review theo ID
        $review->update([
            'review_text' => $request->review_text,
            'rating' => $request->rating,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.reviews.index')->with('status', 'Cập nhật bình luận thành công!');
    }
}
