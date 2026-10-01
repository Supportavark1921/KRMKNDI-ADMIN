<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VendorsController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-vendors');

        $query = Vendor::with('user')->withCount('products');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($s = $request->query('search')) {
            $query->where(fn ($q) => $q->where('business_name', 'like', "%{$s}%")
                ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")));
        }

        return view('admin.store.vendors.index', [
            'vendors'  => $query->latest()->paginate(20)->withQueryString(),
            'pending'  => Vendor::pending()->count(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-vendors');
        return view('admin.store.vendors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-vendors');

        $data = $request->validate([
            'user_name'      => ['required', 'string', 'max:150'],
            'user_email'     => ['required', 'email', 'unique:users,email'],
            'user_password'  => ['required', 'string', 'min:8'],
            'business_name'  => ['required', 'string', 'max:200'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:150'],
            'address'        => ['nullable', 'string'],
            'city'           => ['nullable', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'max:100'],
            'gstin'          => ['nullable', 'string', 'max:20'],
            'pan'            => ['nullable', 'string', 'max:10'],
            'status'         => ['required', 'in:pending,active,suspended'],
            'logo'           => ['nullable', 'image', 'max:5120'],
            'bank_account_no'=> ['nullable', 'string', 'max:25'],
            'bank_ifsc'      => ['nullable', 'string', 'max:12'],
            'bank_name'      => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($request, $data) {
            $user = User::create([
                'name'     => $data['user_name'],
                'email'    => $data['user_email'],
                'password' => Hash::make($data['user_password']),
                'role'     => 'vendor',
            ]);

            $vendorData = array_filter([
                'user_id'        => $user->id,
                'business_name'  => $data['business_name'],
                'contact_person' => $data['contact_person'] ?? null,
                'phone'          => $data['phone'] ?? null,
                'email'          => $data['email'] ?? null,
                'address'        => $data['address'] ?? null,
                'city'           => $data['city'] ?? null,
                'state'          => $data['state'] ?? null,
                'gstin'          => $data['gstin'] ?? null,
                'pan'            => $data['pan'] ?? null,
                'status'         => $data['status'],
                'bank_details'   => array_filter([
                    'account_no' => $data['bank_account_no'] ?? null,
                    'ifsc'       => $data['bank_ifsc'] ?? null,
                    'bank_name'  => $data['bank_name'] ?? null,
                ]) ?: null,
            ], fn ($v) => $v !== null);

            if ($request->hasFile('logo')) {
                $vendorData['logo'] = $request->file('logo')->store('vendors', 'public');
            }

            if ($data['status'] === 'active') {
                $vendorData['approved_at'] = now();
                $vendorData['approved_by'] = auth()->id();
            }

            Vendor::create($vendorData);
        });

        return redirect()->route('admin.store.vendors.index')->with('success', 'Vendor created and login credentials set.');
    }

    public function show(Vendor $vendor): View
    {
        Gate::authorize('manage-vendors');
        $vendor->load(['user', 'approvedBy', 'products' => fn ($q) => $q->latest()->limit(10)]);
        return view('admin.store.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor): View
    {
        Gate::authorize('manage-vendors');
        $vendor->load('user');
        return view('admin.store.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        Gate::authorize('manage-vendors');

        $data = $request->validate([
            'business_name'  => ['required', 'string', 'max:200'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:150'],
            'address'        => ['nullable', 'string'],
            'city'           => ['nullable', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'max:100'],
            'gstin'          => ['nullable', 'string', 'max:20'],
            'pan'            => ['nullable', 'string', 'max:10'],
            'status'         => ['required', 'in:pending,active,suspended'],
            'admin_notes'    => ['nullable', 'string', 'max:1000'],
            'logo'           => ['nullable', 'image', 'max:5120'],
            'bank_account_no'=> ['nullable', 'string', 'max:25'],
            'bank_ifsc'      => ['nullable', 'string', 'max:12'],
            'bank_name'      => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasFile('logo')) {
            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $data['logo'] = $request->file('logo')->store('vendors', 'public');
        }

        $data['bank_details'] = array_filter([
            'account_no' => $data['bank_account_no'] ?? null,
            'ifsc'       => $data['bank_ifsc'] ?? null,
            'bank_name'  => $data['bank_name'] ?? null,
        ]) ?: null;

        unset($data['bank_account_no'], $data['bank_ifsc'], $data['bank_name']);

        // Track approval timestamp
        if ($data['status'] === 'active' && $vendor->status !== 'active') {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
        }

        $vendor->update($data);

        // Keep user role in sync
        $vendor->user->update(['role' => 'vendor']);

        return redirect()->route('admin.store.vendors.show', $vendor)->with('success', 'Vendor updated.');
    }

    public function approve(Vendor $vendor): RedirectResponse
    {
        Gate::authorize('manage-vendors');
        $vendor->update(['status' => 'active', 'approved_at' => now(), 'approved_by' => auth()->id()]);
        return back()->with('success', "{$vendor->business_name} approved.");
    }

    public function suspend(Vendor $vendor): RedirectResponse
    {
        Gate::authorize('manage-vendors');
        $vendor->update(['status' => 'suspended']);
        return back()->with('success', "{$vendor->business_name} suspended.");
    }
}
