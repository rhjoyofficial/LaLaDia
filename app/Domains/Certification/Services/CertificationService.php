<?php

namespace App\Domains\Certification\Services;

use App\Domains\Certification\Models\Certification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CertificationService
{
    private string $path = 'certifications';

    public function getAll()
    {
        return Certification::ordered()->get();
    }

    public function create(array $data): Certification
    {
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $data['logo_path'] = $data['logo']->store($this->path . '/logos', 'public');
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $data['image']->store($this->path . '/images', 'public');
        }

        return Certification::create($data);
    }

    public function update(Certification $certification, array $data): Certification
    {
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            if ($certification->logo_path) {
                Storage::disk('public')->delete($certification->logo_path);
            }
            $data['logo_path'] = $data['logo']->store($this->path . '/logos', 'public');
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($certification->image_path) {
                Storage::disk('public')->delete($certification->image_path);
            }
            $data['image_path'] = $data['image']->store($this->path . '/images', 'public');
        }

        $certification->update($data);
        return $certification->fresh();
    }

    public function delete(Certification $certification): void
    {
        if ($certification->logo_path) {
            Storage::disk('public')->delete($certification->logo_path);
        }
        if ($certification->image_path) {
            Storage::disk('public')->delete($certification->image_path);
        }
        $certification->delete();
    }
}
