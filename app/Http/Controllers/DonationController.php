<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-appointments');

        $query = Donation::with(['user', 'guru', 'category'])->latest();

        if ($s = $request->query('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('donation_id', 'like', "%{$s}%")
                  ->orWhere('transaction_id', 'like', "%{$s}%")
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$s}%"));
            });
        }

        if ($guru = $request->query('guru_id')) {
            $query->where('guru_id', $guru);
        }

        if ($cat = $request->query('category_id')) {
            $query->where('category_id', $cat);
        }

        if ($status = $request->query('payment_status')) {
            $query->where('payment_status', $status);
        }

        if ($from = $request->query('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->query('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $stats = [
            'total'          => Donation::successful()->sum('donation_amount'),
            'successful'     => Donation::successful()->count(),
            'donors'         => Donation::successful()->distinct('user_id')->count('user_id'),
            'handling'       => Donation::successful()->sum('handling_charge'),
            'gst'            => Donation::successful()->sum('gst_amount'),
        ];

        return view('donations.index', [
            'donations'  => $query->paginate(25)->withQueryString(),
            'gurus'      => Guru::orderBy('name')->get(),
            'categories' => DonationCategory::orderBy('name')->get(),
            'statuses'   => Donation::STATUSES,
            'stats'      => $stats,
        ]);
    }

    public function show(Donation $donation): View
    {
        Gate::authorize('manage-appointments');

        $donation->load(['user', 'guru', 'category']);

        return view('donations.show', compact('donation'));
    }
}
