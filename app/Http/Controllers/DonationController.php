<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\Guru;
use App\Services\FcmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function __construct(private readonly FcmService $fcm) {}

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
            'total' => Donation::successful()->sum('donation_amount'),
            'successful' => Donation::successful()->count(),
            'donors' => Donation::successful()->distinct('user_id')->count('user_id'),
            'handling' => Donation::successful()->sum('handling_charge'),
            'gst' => Donation::successful()->sum('gst_amount'),
        ];

        return view('donations.index', [
            'donations' => $query->paginate(25)->withQueryString(),
            'gurus' => Guru::orderBy('name')->get(),
            'categories' => DonationCategory::orderBy('name')->get(),
            'statuses' => Donation::STATUSES,
            'stats' => $stats,
        ]);
    }

    public function show(Donation $donation): View
    {
        Gate::authorize('manage-appointments');

        $donation->load(['user', 'guru', 'category']);

        return view('donations.show', compact('donation'));
    }

    public function updatePayment(Request $request, Donation $donation): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'payment_status' => ['required', 'in:pending,screenshot_uploaded,success,failed,cancelled,refunded'],
            'transaction_id' => ['nullable', 'string', 'max:200'],
        ]);

        $donation->update([
            'payment_status' => $data['payment_status'],
            'transaction_id' => $data['transaction_id'] ?: $donation->transaction_id,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($donation)
            ->withProperties(['payment_status' => $data['payment_status'], 'transaction_id' => $data['transaction_id']])
            ->log('payment_status_updated');

        if ($data['payment_status'] === 'success' && $donation->user_id) {
            $amount = '₹' . number_format($donation->total_amount, 2);
            $this->fcm->sendToUsers(
                [$donation->user_id],
                '🙏 Donation Confirmed',
                "Your donation of {$amount} has been verified. Thank you for your generosity!",
                ['type' => 'donation', 'id' => (string) $donation->id],
            );
        } elseif ($data['payment_status'] === 'failed' && $donation->user_id) {
            $this->fcm->sendToUsers(
                [$donation->user_id],
                '❌ Donation Payment Failed',
                'Your donation payment could not be verified. Please contact us.',
                ['type' => 'donation', 'id' => (string) $donation->id],
            );
        }

        return back()->with('success', 'Payment status updated.');
    }
}
