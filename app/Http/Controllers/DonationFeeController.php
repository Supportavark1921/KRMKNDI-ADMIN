<?php

namespace App\Http\Controllers;

use App\Models\DonationFeeConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DonationFeeController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-appointments');

        return view('donation-fees.index', ['config' => DonationFeeConfig::active()]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'handling_charge' => ['required', 'numeric', 'min:0', 'max:9999'],
            'gst_rate'        => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Deactivate old, create new — keeps audit trail
        DonationFeeConfig::where('is_active', true)->update(['is_active' => false]);
        DonationFeeConfig::create([...$data, 'is_active' => true]);

        return back()->with('success', 'Fee configuration updated.');
    }
}
