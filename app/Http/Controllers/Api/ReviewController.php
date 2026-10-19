<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Support\MorphTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display reviews for a specific item
     */
    public function index(Request $request)
    {
        $request->merge(['reviewable_type' => MorphTypes::normalize($request->input('reviewable_type'))]);
        $request->validate([
            'reviewable_type' => 'required|string|in:' . implode(',', MorphTypes::REVIEWABLE),
            'reviewable_id' => 'required|integer|min:1',
        ]);

        $reviews = Review::with('user:id,name,avatar')
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
        $request->merge(['reviewable_type' => MorphTypes::normalize($request->input('reviewable_type'))]);
        $request->validate([
            'reviewable_type' => 'required|string|in:' . implode(',', MorphTypes::REVIEWABLE),
            'reviewable_id' => 'required|integer|min:1',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        abort_unless(
            MorphTypes::exists($request->reviewable_type, (int) $request->reviewable_id),
            404,
            'Ese contenido ya no existe.',
        );

        // Check if user already reviewed this item
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('reviewable_type', $request->reviewable_type)
            ->where('reviewable_id', $request->reviewable_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Ya dejaste una reseña aquí. Puedes editarla.',
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
            'message' => 'Reseña publicada',
            'data' => $review->load('user:id,name,avatar'),
        ], 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        Gate::authorize('update', $review);

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
            'message' => 'Reseña actualizada',
            'data' => $review,
        ], 200);
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        Gate::authorize('delete', $review);

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reseña eliminada',
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
