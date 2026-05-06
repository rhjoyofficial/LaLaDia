<?php

namespace App\Domains\Certification\Controllers;

use App\Domains\Certification\Models\Certification;
use App\Domains\Certification\Requests\CertificationRequest;
use App\Domains\Certification\Resources\CertificationResource;
use App\Domains\Certification\Services\CertificationService;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class AdminCertificationController extends Controller
{
    public function __construct(private CertificationService $service) {}

    public function index()
    {
        $this->authorize('certification.view');
        
        $query = Certification::ordered();

        if (request()->has('q')) {
            $search = request('q');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%");
            });
        }

        $certifications = $query->paginate(request('per_page', 15));
        return ApiResponse::paginated(CertificationResource::collection($certifications));
    }

    public function all()
    {
        $this->authorize('certification.view');
        $certifications = $this->service->getAll();
        return ApiResponse::success(CertificationResource::collection($certifications), 'All certifications loaded');
    }

    public function store(CertificationRequest $request)
    {
        $certification = $this->service->create($request->validated());
        return ApiResponse::success(new CertificationResource($certification), 'Certification created successfully', 201);
    }

    public function show(Certification $certification)
    {
        $this->authorize('certification.view');
        return ApiResponse::success(new CertificationResource($certification), 'Certification loaded');
    }

    public function update(CertificationRequest $request, Certification $certification)
    {
        $updated = $this->service->update($certification, $request->validated());
        return ApiResponse::success(new CertificationResource($updated), 'Certification updated successfully');
    }

    public function destroy(Certification $certification)
    {
        $this->authorize('certification.delete');
        $this->service->delete($certification);
        return ApiResponse::success(null, 'Certification deleted successfully');
    }

    public function toggleActive(Certification $certification)
    {
        $this->authorize('certification.update');
        $certification->update(['is_active' => !$certification->is_active]);
        return ApiResponse::success(new CertificationResource($certification), 'Status updated successfully');
    }
}
