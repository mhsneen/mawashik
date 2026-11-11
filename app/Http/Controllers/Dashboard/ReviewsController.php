<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{DriverReview, OrderReview};
use Illuminate\Http\Request;

/**
 * ReviewsController — إدارة التقييمات (تقييمات الطلبات/التجار وتقييمات المندوبين).
 * الإخفاء لا يحذف التقييم؛ يُستثنى فقط من المتوسطات ومن العرض للعملاء.
 */
class ReviewsController extends Controller
{
    private function modelFor(string $type): string
    {
        return $type === 'driver' ? DriverReview::class : OrderReview::class;
    }

    public function index(Request $request)
    {
        $type = $request->query('type') === 'driver' ? 'driver' : 'order';

        $query = $type === 'driver'
            ? DriverReview::with(['client:id,name,mobile', 'driver:id,name', 'order:id,order_number'])
            : OrderReview::with(['client:id,name,mobile', 'trader:id,name', 'order:id,order_number']);

        $items = $query
            ->when($request->filled('rating'), fn($q) => $q->where('rating', (int) $request->rating))
            ->when($request->query('visibility') === 'hidden', fn($q) => $q->where('is_hidden', true))
            ->when($request->query('visibility') === 'visible', fn($q) => $q->where('is_hidden', false))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(fn($w) => $w->where('comment', 'like', $term)
                    ->orWhereHas('client', fn($c) => $c->where('name', 'like', $term)->orWhere('mobile', 'like', $term)));
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'order'  => ['count' => OrderReview::count(), 'avg' => round((float) OrderReview::visible()->avg('rating'), 2), 'hidden' => OrderReview::where('is_hidden', true)->count()],
            'driver' => ['count' => DriverReview::count(), 'avg' => round((float) DriverReview::visible()->avg('rating'), 2), 'hidden' => DriverReview::where('is_hidden', true)->count()],
        ];

        return view('dashboard.reviews.index', compact('items', 'type', 'stats'));
    }

    public function toggle(Request $request, string $type, int $id)
    {
        $review = $this->modelFor($type)::findOrFail($id);
        $review->update(['is_hidden' => !$review->is_hidden]);

        return back()->with('success', __('dashboard.Review_Updated'));
    }

    public function destroy(string $type, int $id)
    {
        $this->modelFor($type)::findOrFail($id)->delete();

        return back()->with('success', __('dashboard.Review_Deleted'));
    }
}
