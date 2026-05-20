<?php

namespace App\Domains\Product\Controllers;

use App\Domains\ActivityLog\Services\AdminLogger;
use App\Domains\Product\Models\Combo;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ComboTierPriceController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Combo $combo)
    {
        try {
            $this->authorize('product.update');

            $validated = $request->validate([
                'min_quantity'           => 'required|integer|min:1',
                'discount_type'          => 'required|in:percentage,fixed',
                'discount_value'         => 'required|numeric|min:0' . ($request->discount_type === 'percentage' ? '|max:100' : ''),
                'has_free_delivery'      => 'nullable|boolean',
                'free_delivery_zones'    => 'nullable|array',
                'free_delivery_zones.*'  => 'integer|exists:shipping_zones,id',
                'gift_product_variant_id'=> 'nullable|exists:product_variants,id',
                'gift_quantity'          => 'nullable|integer|min:1',
            ]);

            if (!isset($validated['has_free_delivery'])) {
                $validated['has_free_delivery'] = false;
            }

            $tierPrice = $combo->tierPrices()->updateOrCreate(
                ['min_quantity' => $validated['min_quantity']],
                $validated
            );

            AdminLogger::log(
                'products',
                "Combo tier price upserted on combo #{$combo->id} ({$combo->title}) — min_qty:{$validated['min_quantity']}",
                $combo,
                ['tier_id' => $tierPrice->id, 'data' => $validated],
                'combo_tier_price_upserted'
            );

            return ApiResponse::success($tierPrice, 'Tier price added successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->handleError($e, 'Failed to add combo tier price');
        }
    }

    public function destroy(Combo $combo, $tierId)
    {
        try {
            $this->authorize('product.update');

            $tier = $combo->tierPrices()->findOrFail($tierId);
            $snapshot = $tier->toArray();
            $tier->delete();

            AdminLogger::log(
                'products',
                "Combo tier price deleted on combo #{$combo->id} ({$combo->title}) — tier_id:{$tierId}",
                $combo,
                ['deleted_tier' => $snapshot],
                'combo_tier_price_deleted'
            );

            return ApiResponse::success(null, 'Tier price deleted successfully');
        } catch (Exception $e) {
            return $this->handleError($e, 'Failed to delete combo tier price');
        }
    }

    private function handleError(Exception $e, string $customMessage)
    {
        Log::error($customMessage . ': ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return ApiResponse::error(
            $customMessage,
            config('app.debug') ? $e->getMessage() : null,
            500
        );
    }
}
