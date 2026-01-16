<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display reviews for a specific item
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reviewable_type' => 'required|string',
            'reviewable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $reviews = Review::with('user')
            ->where('reviewable_type', $request->reviewable_type)
            ->where('reviewable_id', $request->reviewable_id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ], 200);
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reviewable_type' => 'required|string|in:App\Models\Locale,App\Models\Candelaria,App\Models\Experiencia,App\Models\Evento,App\Models\Promocion',
            'reviewable_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user already reviewed this item
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('reviewable_type', $request->reviewable_type)
            ->where('reviewable_id', $request->reviewable_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this item. Use update instead.',
            ], 409);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'reviewable_type' => $request->reviewable_type,
            'reviewable_id' => $request->reviewable_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review->load('user'),
        ], 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Only the review owner can update
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this review.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review,
        ], 200);
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this review.',
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ], 200);
    }

    /**
     * Get user's reviews
     */
    public function myReviews(Request $request)
    {
        $reviews = Review::with('reviewable')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ], 200);
    }
}
