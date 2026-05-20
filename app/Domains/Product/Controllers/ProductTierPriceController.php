<?php

namespace App\Domains\Product\Controllers;

use App\Domains\ActivityLog\Services\AdminLogger;
use App\Domains\Product\Models\ProductVariant;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductTierPriceController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, ProductVariant $variant)
    {
        try {
            $this->authorize('product.update');

            $validated = $request->validate([
                'min_quantity' => 'required|integer|min:1',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0' . ($request->discount_type === 'percentage' ? '|max:100' : ''),
                'has_free_delivery' => 'boolean|nullable',
                'free_delivery_zones' => 'array|nullable',
                'free_delivery_zones.*' => 'integer|exists:shipping_zones,id',
                'gift_product_variant_id' => 'nullable|exists:product_variants,id',
                'gift_quantity' => 'integer|min:1|nullable',
            ]);

            // Set defaults if not provided but marked as boolean
            if (!isset($validated['has_free_delivery'])) {
                $validated['has_free_delivery'] = false;
            }

            // Check if a tier for this quantity already exists to avoid confusion
            $tierPrice = $variant->tierPrices()->updateOrCreate(
                ['min_quantity' => $validated['min_quantity']],
                $validated
            );

            AdminLogger::log(
                'products',
                "Tier price upserted on variant #{$variant->id} ({$variant->sku}) — min_qty:{$validated['min_quantity']}",
                $variant,
                ['tier_id' => $tierPrice->id, 'data' => $validated],
                'tier_price_upserted'
            );

            return ApiResponse::success(
                $tierPrice,
                'Tier price added successfully',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->handleError($e, 'Failed to add tier price');
        }
    }

    public function destroy(ProductVariant $variant, $tierId)
    {
        try {
            $this->authorize('product.update');

            $tier = $variant->tierPrices()->findOrFail($tierId);
            $tierSnapshot = $tier->toArray();
            $tier->delete();

            AdminLogger::log(
                'products',
                "Tier price deleted on variant #{$variant->id} ({$variant->sku}) — tier_id:{$tierId}",
                $variant,
                ['deleted_tier' => $tierSnapshot],
                'tier_price_deleted'
            );

            return ApiResponse::success(null, 'Tier price deleted successfully');
        } catch (Exception $e) {
            return $this->handleError($e, 'Failed to delete tier price');
        }
    }

    /**
     * Updated to use standard ApiResponse
     */
    private function handleError(Exception $e, string $customMessage)
    {
        Log::error($customMessage . ': ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return ApiResponse::error(
            $customMessage,
            config('app.debug') ? $e->getMessage() : null,
            500
        );
    }
}
