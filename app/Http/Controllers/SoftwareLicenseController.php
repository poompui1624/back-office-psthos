<?php

namespace App\Http\Controllers;

use App\Models\SoftwareLicense;
use App\Models\SoftwareProduct;
use Illuminate\Http\Request;
use App\Models\SoftwareLicenseAction;
use Illuminate\Support\Facades\DB;

class SoftwareLicenseController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('software.view'), 403);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $licenses = SoftwareLicense::query()
            ->with('product')
            ->when($search, function ($query) use ($search) {
                $query->where('license_name', 'like', "%{$search}%")
                    ->orWhere('license_key', 'like', "%{$search}%")
                    ->orWhere('vendor_contact', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('vendor', 'like', "%{$search}%");
                    });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByRaw('expire_date IS NULL')
            ->orderBy('expire_date')
            ->paginate(20)
            ->withQueryString();

        return view('software-licenses.index', compact('licenses', 'search', 'status'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('software.create'), 403);

        return view('software-licenses.create', [
            'products' => SoftwareProduct::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('software.create'), 403);

        $validated = $this->validateLicense($request);

        SoftwareLicense::create($validated);

        return redirect()
            ->route('software-licenses.index')
            ->with('success', 'เพิ่ม Software License เรียบร้อยแล้ว');
    }

    public function edit(SoftwareLicense $softwareLicense)
    {
        abort_unless(auth()->user()->can('software.update'), 403);

        $softwareLicense->load([
            'product',
            'actions.user',
        ]);

        return view('software-licenses.edit', [
            'softwareLicense' => $softwareLicense,
            'products' => SoftwareProduct::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SoftwareLicense $softwareLicense)
    {
        abort_unless(auth()->user()->can('software.update'), 403);

        $validated = $this->validateLicense($request);

        $softwareLicense->update($validated);

        return redirect()
            ->route('software-licenses.index')
            ->with('success', 'แก้ไข Software License เรียบร้อยแล้ว');
    }

    public function destroy(SoftwareLicense $softwareLicense)
    {
        abort_unless(auth()->user()->can('software.delete'), 403);

        $softwareLicense->delete();

        return redirect()
            ->route('software-licenses.index')
            ->with('success', 'ลบ Software License เรียบร้อยแล้ว');
    }

    private function validateLicense(Request $request): array
    {
        return $request->validate([
            'software_product_id' => ['required', 'exists:software_products,id'],
            'license_name' => ['nullable', 'string', 'max:255'],
            'license_key' => ['nullable', 'string', 'max:255'],
            'license_type' => ['nullable', 'string', 'max:255'],
            'total_seats' => ['required', 'integer', 'min:1'],
            'used_seats' => ['nullable', 'integer', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'expire_date' => ['nullable', 'date'],
            'renewed_at' => ['nullable', 'date'],
            'cancelled_at' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'vendor_contact' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'remark' => ['nullable', 'string'],
        ]);
    }

    public function renewForm(SoftwareLicense $softwareLicense)
    {
        abort_unless(auth()->user()->can('software.update'), 403);

        $softwareLicense->load('product');

        return view('software-licenses.renew', compact('softwareLicense'));
    }

    public function renew(Request $request, SoftwareLicense $softwareLicense)
    {
        abort_unless(auth()->user()->can('software.update'), 403);

        $validated = $request->validate([
            'new_expire_date' => ['required', 'date'],
            'renewed_at' => ['required', 'date'],
            'total_seats' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'remark' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($softwareLicense, $validated) {
            $oldValues = $softwareLicense->only([
                'expire_date',
                'renewed_at',
                'total_seats',
                'price',
                'status',
            ]);

            $oldExpireDate = $softwareLicense->expire_date;

            $softwareLicense->update([
                'expire_date' => $validated['new_expire_date'],
                'renewed_at' => $validated['renewed_at'],
                'total_seats' => $validated['total_seats'],
                'price' => $validated['price'] ?? $softwareLicense->price,
                'status' => 'active',
                'cancelled_at' => null,
                'last_expire_notified_at' => null,
            ]);

            $newValues = $softwareLicense->fresh()->only([
                'expire_date',
                'renewed_at',
                'total_seats',
                'price',
                'status',
            ]);

            SoftwareLicenseAction::create([
                'software_license_id' => $softwareLicense->id,
                'user_id' => auth()->id(),
                'action' => 'renewed',
                'old_expire_date' => $oldExpireDate,
                'new_expire_date' => $validated['new_expire_date'],
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'remark' => $validated['remark'] ?? null,
            ]);
        });

        return redirect()
            ->route('software-licenses.index')
            ->with('success', 'ต่ออายุ Software License เรียบร้อยแล้ว');
    }

    public function cancelForm(SoftwareLicense $softwareLicense)
    {
        abort_unless(auth()->user()->can('software.update'), 403);

        $softwareLicense->load('product');

        return view('software-licenses.cancel', compact('softwareLicense'));
    }

    public function cancel(Request $request, SoftwareLicense $softwareLicense)
    {
        abort_unless(auth()->user()->can('software.update'), 403);

        $validated = $request->validate([
            'cancelled_at' => ['required', 'date'],
            'remark' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($softwareLicense, $validated) {
            $oldValues = $softwareLicense->only([
                'status',
                'cancelled_at',
                'remark',
            ]);

            $softwareLicense->update([
                'status' => 'cancelled',
                'cancelled_at' => $validated['cancelled_at'],
                'remark' => $validated['remark'],
            ]);

            $newValues = $softwareLicense->fresh()->only([
                'status',
                'cancelled_at',
                'remark',
            ]);

            SoftwareLicenseAction::create([
                'software_license_id' => $softwareLicense->id,
                'user_id' => auth()->id(),
                'action' => 'cancelled',
                'old_expire_date' => $softwareLicense->expire_date,
                'new_expire_date' => $softwareLicense->expire_date,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'remark' => $validated['remark'],
            ]);
        });

        return redirect()
            ->route('software-licenses.index')
            ->with('success', 'ยกเลิก Software License เรียบร้อยแล้ว');
    }
}