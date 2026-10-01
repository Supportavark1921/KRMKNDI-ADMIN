@extends('layouts.app', ['title' => 'Stock Log — '.$product->name])
@section('content')
<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>📦 Stock Log — {{ $product->name }}</span><small>{{ $product->product_code }}</small></div>
    <div style="display:flex;gap:10px">
        <a class="nav-link topbar-link" href="{{ route('admin.store.products.show', $product) }}">← Product</a>
    </div>
</header>

<div style="max-width:900px;margin:30px auto;padding:0 20px 80px">

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:20px;margin-bottom:18px;display:flex;gap:24px;font-size:13px">
    @php $inv = $product->inventory; @endphp
    <div><span style="color:#8a9ab8;display:block;font-size:11px">Available</span><b style="color:#276946;font-size:18px">{{ $inv->available_stock ?? 0 }}</b></div>
    <div><span style="color:#8a9ab8;display:block;font-size:11px">Total</span><b style="color:#15233d;font-size:18px">{{ $inv->total_stock ?? 0 }}</b></div>
    <div><span style="color:#8a9ab8;display:block;font-size:11px">Sold</span><b style="color:#536078;font-size:18px">{{ $inv->sold_stock ?? 0 }}</b></div>
</div>

<div style="background:#fff;border:1px solid #e6e8f0;border-radius:18px;overflow:hidden">
<table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead>
        <tr style="background:#f9f9fb;border-bottom:1px solid #e6e8f0">
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Type</th>
            <th style="padding:12px 16px;text-align:right;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Change</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Reference</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Actor</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Notes</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em">Date</th>
        </tr>
    </thead>
    <tbody>
    @forelse($transactions as $tx)
        <tr style="border-bottom:1px solid #f0f2f8">
            <td style="padding:11px 16px"><span style="font-family:monospace;font-size:11px;color:#536078">{{ $tx->type }}</span></td>
            <td style="padding:11px 16px;text-align:right;font-weight:700;color:{{ $tx->quantity_change >= 0 ? '#276946' : '#c0392b' }}">{{ $tx->quantity_change >= 0 ? '+' : '' }}{{ $tx->quantity_change }}</td>
            <td style="padding:11px 16px;font-size:11px;color:#8a9ab8;font-family:monospace">{{ $tx->reference_type ? $tx->reference_type.'#'.$tx->reference_id : '—' }}</td>
            <td style="padding:11px 16px;color:#536078">{{ $tx->actor?->name ?? 'System' }}</td>
            <td style="padding:11px 16px;color:#8a9ab8;font-size:12px">{{ $tx->notes ?: '—' }}</td>
            <td style="padding:11px 16px;color:#8a9ab8;font-size:12px">{{ $tx->created_at->format('d M Y H:i') }}</td>
        </tr>
    @empty
        <tr><td colspan="6" style="padding:30px;text-align:center;color:#8a9ab8">No transactions yet.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div style="margin-top:16px">{{ $transactions->links() }}</div>
</div>
</main>
@endsection
