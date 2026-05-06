@extends('layouts.admin')

@section('title', 'Certifications')

@section('content')

<div x-data="certificationManager()" x-init="init()">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-brown">Certifications</h2>
            <p class="text-sm text-muted mt-0.5">Manage product certifications & badges</p>
        </div>
        @can('certification.create')
        <button @click="openCreate()"
            class="inline-flex items-center gap-2 bg-gold-antique hover:bg-gold-antique text-white text-sm font-medium px-4 py-2 rounded-lg transition cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            Add Certification
        </button>
        @endcan
    </div>

    {{-- Search + Stats bar --}}
    <div class="bg-white border border-champagne rounded-xl mb-4 p-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-72">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-taupe text-xs"></i>
            <input
                type="text"
                x-model="search"
                @input.debounce.400ms="loadCertifications(1)"
                placeholder="Search certifications…"
                class="w-full pl-9 pr-4 py-2 text-sm border border-champagne rounded-lg outline-none focus:ring-2 focus:ring-gold-antique"
            >
        </div>
        <p class="text-sm text-muted" x-text="meta.total !== undefined ? meta.total + ' certifications' : ''"></p>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-champagne rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream border-b border-champagne">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase w-12">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Logo</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Category / Org</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Dates</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    {{-- Loading skeleton --}}
                    <template x-if="loading">
                        <template x-for="i in 5" :key="i">
                            <tr>
                                <td colspan="7" class="px-5 py-4">
                                    <div class="h-4 bg-gray-100 rounded animate-pulse w-full"></div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    {{-- Rows --}}
                    <template x-if="!loading">
                        <template x-for="cert in certifications" :key="cert.id">
                            <tr class="hover:bg-cream transition">
                                <td class="px-5 py-3 text-taupe text-xs" x-text="cert.id"></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <template x-if="cert.logo_url">
                                            <img :src="cert.logo_url" class="w-10 h-10 rounded-lg object-contain bg-white border border-champagne p-1">
                                        </template>
                                        <template x-if="!cert.logo_url">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center border border-champagne">
                                                <i class="fa-solid fa-stamp text-taupe text-xs"></i>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-5 py-3 font-medium text-brown" x-text="cert.name"></td>
                                <td class="px-5 py-3">
                                    <div class="text-xs text-brown" x-text="cert.category || '—'"></div>
                                    <div class="text-[10px] text-muted uppercase tracking-wider" x-text="cert.organization || '—'"></div>
                                </td>
                                <td class="px-5 py-3 text-xs text-muted">
                                    <div x-show="cert.given_date">Given: <span x-text="cert.given_date"></span></div>
                                    <div x-show="cert.expiry_date">Expires: <span x-text="cert.expiry_date"></span></div>
                                    <div x-show="!cert.given_date && !cert.expiry_date">—</div>
                                </td>
                                <td class="px-5 py-3">
                                    <button @click="toggleStatus(cert)" 
                                        :class="cert.is_active ? 'bg-cream text-gold-antique' : 'bg-gray-100 text-muted'"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium cursor-pointer transition">
                                        <span x-text="cert.is_active ? 'Active' : 'Inactive'"></span>
                                    </button>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        @can('certification.update')
                                        <button @click="openEdit(cert)"
                                            class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                        @endcan
                                        @can('certification.delete')
                                        <button @click="confirmDelete(cert.id)"
                                            class="inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 font-medium transition cursor-pointer">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && certifications.length === 0">
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-taupe">
                                <i class="fa-solid fa-certificate text-2xl mb-2 block"></i>
                                No certifications found
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-3 border-t border-champagne flex items-center justify-between" x-show="meta.last_page > 1">
            <p class="text-xs text-muted">
                Page <span x-text="meta.current_page"></span> of <span x-text="meta.last_page"></span>
                &bull; <span x-text="meta.total"></span> total
            </p>
            <div class="flex gap-2">
                <button
                    @click="loadCertifications(meta.current_page - 1)"
                    :disabled="meta.current_page <= 1"
                    class="px-3 py-1.5 text-xs font-medium border border-champagne rounded-lg disabled:opacity-40 hover:bg-cream transition cursor-pointer disabled:cursor-not-allowed">
                    &larr; Prev
                </button>
                <button
                    @click="loadCertifications(meta.current_page + 1)"
                    :disabled="meta.current_page >= meta.last_page"
                    class="px-3 py-1.5 text-xs font-medium border border-champagne rounded-lg disabled:opacity-40 hover:bg-cream transition cursor-pointer disabled:cursor-not-allowed">
                    Next &rarr;
                </button>
            </div>
        </div>
    </div>

    {{-- ============================
         Create / Edit Modal
    ============================= --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto no-scrollbar"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-champagne sticky top-0 bg-white z-10">
                <h3 class="text-base font-bold text-brown"
                    x-text="isEditing ? 'Edit Certification' : 'Add Certification'"></h3>
                <button @click="showModal = false" class="text-taupe hover:text-muted cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Form --}}
            <form @submit.prevent="saveCertification()" class="p-6 space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Name --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-brown mb-1">Certification Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            :class="errors.name ? 'border-red-400' : ''"
                            placeholder="e.g. ISO 9001:2015">
                        <p x-show="errors.name" class="mt-1 text-xs text-red-600" x-text="errors.name?.[0]"></p>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Category</label>
                        <input type="text" x-model="form.category"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            placeholder="e.g. Quality Management">
                    </div>

                    {{-- Organization --}}
                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Issuing Organization</label>
                        <input type="text" x-model="form.organization"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            placeholder="e.g. ISO International">
                    </div>

                    {{-- Given Date --}}
                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Issue Date</label>
                        <input type="date" x-model="form.given_date"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                    </div>

                    {{-- Expiry Date --}}
                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Expiry Date</label>
                        <input type="date" x-model="form.expiry_date"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                    </div>
                </div>

                {{-- Additional Details --}}
                <div>
                    <label class="block text-sm font-medium text-brown mb-1">Additional Details</label>
                    <textarea x-model="form.additional_details" rows="3"
                        class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique resize-none"
                        placeholder="Any additional information…"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Logo --}}
                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Logo (Square icon)</label>
                        <div class="flex items-center gap-4">
                            <template x-if="logoPreview">
                                <img :src="logoPreview" class="w-16 h-16 rounded-lg object-contain bg-white border border-champagne p-1">
                            </template>
                            <label class="flex-1 cursor-pointer inline-flex items-center justify-center gap-2 border border-dashed border-gray-300 rounded-lg px-4 py-4 text-sm text-muted hover:border-gold-antique hover:text-gold-antique transition">
                                <i class="fa-solid fa-upload text-xs"></i>
                                <span x-text="logoPreview ? 'Change logo' : 'Upload logo'"></span>
                                <input type="file" accept="image/*" class="sr-only" @change="handleLogoChange($event)">
                            </label>
                        </div>
                        <p x-show="errors.logo" class="mt-1 text-xs text-red-600" x-text="errors.logo?.[0]"></p>
                    </div>

                    {{-- Main Image --}}
                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Certificate Image (Full document)</label>
                        <div class="flex items-center gap-4">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="w-16 h-16 rounded-lg object-cover border border-champagne">
                            </template>
                            <label class="flex-1 cursor-pointer inline-flex items-center justify-center gap-2 border border-dashed border-gray-300 rounded-lg px-4 py-4 text-sm text-muted hover:border-gold-antique hover:text-gold-antique transition">
                                <i class="fa-solid fa-upload text-xs"></i>
                                <span x-text="imagePreview ? 'Change image' : 'Upload image'"></span>
                                <input type="file" accept="image/*" class="sr-only" @change="handleImageChange($event)">
                            </label>
                        </div>
                        <p x-show="errors.image" class="mt-1 text-xs text-red-600" x-text="errors.image?.[0]"></p>
                    </div>
                </div>

                {{-- Sort Order & Status --}}
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Sort Order</label>
                        <input type="number" x-model.number="form.sort_order" min="0"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-gold-antique rounded-full transition"></div>
                                <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm font-medium text-brown group-hover:text-gold-antique transition">Active</span>
                        </label>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-champagne">
                    <button type="button" @click="showModal = false"
                        class="px-4 py-2 text-sm font-medium text-muted hover:text-brown transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" :disabled="saving"
                        class="inline-flex items-center gap-2 bg-gold-antique hover:bg-gold-antique disabled:opacity-60 text-white text-sm font-medium px-6 py-2 rounded-lg transition cursor-pointer">
                        <i x-show="saving" class="fa-solid fa-spinner fa-spin text-xs"></i>
                        <span x-text="isEditing ? 'Update Certification' : 'Create Certification'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ============================
         Delete Confirm Modal
    ============================= --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/50" @click="showDeleteModal = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
            </div>
            <h3 class="text-base font-bold text-brown mb-1">Delete Certification?</h3>
            <p class="text-sm text-muted mb-5">This action cannot be undone. It will be removed from all products.</p>
            <div class="flex gap-3 justify-center">
                <button @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm font-medium border border-champagne rounded-lg hover:bg-cream transition cursor-pointer">
                    Cancel
                </button>
                <button @click="deleteCertification()"
                    class="px-4 py-2 text-sm font-medium bg-red-600 hover:bg-red-700 text-white rounded-lg transition cursor-pointer">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function certificationManager() {
    return {
        certifications: [],
        meta: {},
        loading: true,
        search: '',

        showModal: false,
        showDeleteModal: false,
        isEditing: false,
        saving: false,
        deleteId: null,
        errors: {},

        form: {
            id: null,
            name: '',
            category: '',
            organization: '',
            given_date: '',
            expiry_date: '',
            additional_details: '',
            is_active: true,
            sort_order: 0,
        },
        logoFile: null,
        logoPreview: null,
        imageFile: null,
        imagePreview: null,

        async init() {
            await this.loadCertifications();
        },

        async loadCertifications(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({ page, per_page: 15 });
                if (this.search) params.set('q', this.search);

                const r = await fetch(`/api/v1/admin/certifications?${params}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                this.certifications = data.data ?? [];
                this.meta = data.meta ?? {};
            } catch (e) {
                console.error('Failed to load certifications', e);
            } finally {
                this.loading = false;
            }
        },

        openCreate() {
            this.isEditing = false;
            this.form = { 
                id: null, 
                name: '', 
                category: '', 
                organization: '',
                given_date: '',
                expiry_date: '',
                additional_details: '',
                is_active: true, 
                sort_order: 0 
            };
            this.logoFile = null;
            this.logoPreview = null;
            this.imageFile = null;
            this.imagePreview = null;
            this.errors = {};
            this.showModal = true;
        },

        openEdit(cert) {
            this.isEditing = true;
            this.form = {
                id: cert.id,
                name: cert.name,
                category: cert.category ?? '',
                organization: cert.organization ?? '',
                given_date: cert.given_date ?? '',
                expiry_date: cert.expiry_date ?? '',
                additional_details: cert.additional_details ?? '',
                is_active: cert.is_active,
                sort_order: cert.sort_order ?? 0,
            };
            this.logoPreview = cert.logo_url ?? null;
            this.imagePreview = cert.image_url ?? null;
            this.logoFile = null;
            this.imageFile = null;
            this.errors = {};
            this.showModal = true;
        },

        handleLogoChange(e) {
            this.logoFile = e.target.files[0] ?? null;
            if (this.logoFile) {
                const reader = new FileReader();
                reader.onload = (ev) => { this.logoPreview = ev.target.result; };
                reader.readAsDataURL(this.logoFile);
            }
        },

        handleImageChange(e) {
            this.imageFile = e.target.files[0] ?? null;
            if (this.imageFile) {
                const reader = new FileReader();
                reader.onload = (ev) => { this.imagePreview = ev.target.result; };
                reader.readAsDataURL(this.imageFile);
            }
        },

        async toggleStatus(cert) {
            try {
                const r = await fetch(`/api/v1/admin/certifications/${cert.id}/toggle-active`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
                if (r.ok) {
                    cert.is_active = !cert.is_active;
                }
            } catch (e) {
                console.error('Toggle status failed', e);
            }
        },

        async saveCertification() {
            this.saving = true;
            this.errors = {};

            const fd = new FormData();
            fd.append('name', this.form.name);
            fd.append('category', this.form.category ?? '');
            fd.append('organization', this.form.organization ?? '');
            fd.append('given_date', this.form.given_date ?? '');
            fd.append('expiry_date', this.form.expiry_date ?? '');
            fd.append('additional_details', this.form.additional_details ?? '');
            fd.append('is_active', this.form.is_active ? '1' : '0');
            fd.append('sort_order', this.form.sort_order ?? 0);
            
            if (this.logoFile) fd.append('logo', this.logoFile);
            if (this.imageFile) fd.append('image', this.imageFile);

            const isEdit = this.isEditing;
            const url = isEdit
                ? `/api/v1/admin/certifications/${this.form.id}`
                : '/api/v1/admin/certifications';

            if (isEdit) fd.append('_method', 'PUT');

            try {
                const r = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: fd,
                });

                const data = await r.json();

                if (r.ok) {
                    this.showModal = false;
                    await this.loadCertifications(this.meta.current_page ?? 1);
                    if (window.flash) window.flash(isEdit ? 'Certification updated' : 'Certification created', 'success');
                } else if (r.status === 422) {
                    this.errors = data.errors ?? {};
                } else {
                    alert(data.message ?? 'Something went wrong.');
                }
            } catch (e) {
                alert('Network error. Please try again.');
            } finally {
                this.saving = false;
            }
        },

        confirmDelete(id) {
            this.deleteId = id;
            this.showDeleteModal = true;
        },

        async deleteCertification() {
            try {
                const r = await fetch(`/api/v1/admin/certifications/${this.deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                if (r.ok) {
                    this.showDeleteModal = false;
                    await this.loadCertifications(this.meta.current_page ?? 1);
                    if (window.flash) window.flash('Certification deleted', 'success');
                } else {
                    const data = await r.json();
                    alert(data.message ?? 'Delete failed.');
                }
            } catch (e) {
                alert('Network error. Please try again.');
            }
        },
    };
}
</script>
@endpush
