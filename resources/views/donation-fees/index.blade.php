@extends('layouts.app')
@section('content')
<style>
.don-page{background:radial-gradient(circle at 88% 5%,#fff4e0 0,transparent 22%),#f8f6f2!important}
.fee-wrap{max-width:720px;margin:0 auto}
.fee-hero{margin:32px auto 20px;padding:30px 36px;border-radius:20px;color:#fff;background:radial-gradient(circle at 82% 10%,#f0a060 0,transparent 28%),linear-gradient(118deg,#2a1505,#7a3a10)}
.fee-hero h1{margin:8px 0;font-size:28px;color:#fff}.fee-hero p{margin:0;color:#f8e4cc;line-height:1.6;font-size:13px}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.fee-section{padding:26px 30px;border:1px solid #ede0d0;border-radius:16px;background:#fff;margin-bottom:16px;box-shadow:0 4px 12px #7a3a1006}
.fee-title{display:flex;align-items:center;gap:10px;margin:0 0 20px;font-size:15px;font-weight:800;color:#2a1810}
.fee-title span{display:grid;width:28px;height:28px;place-items:center;border-radius:8px;background:#fff4eb;color:#e8813a;font-size:14px}
.fee-label{display:block;margin:0 0 6px;font-size:13px;font-weight:700;color:#4a3020}
.fee-input-wrap{display:flex;align-items:center;gap:0}
.fee-prefix{display:flex;align-items:center;justify-content:center;padding:11px 14px;border:1px solid #e8ddd0;border-right:0;border-radius:10px 0 0 10px;background:#fff8f0;color:#8a6040;font-size:15px;font-weight:800}
.fee-input{flex:1;padding:11px 13px;border:1px solid #e8ddd0;border-radius:0 10px 10px 0;font:inherit;color:#2a1810;background:#fff;outline:none;transition:.2s}
.fee-input:focus{border-color:#e8813a;box-shadow:0 0 0 3px #fff4eb}
.fee-suffix{display:flex;align-items:center;justify-content:center;padding:11px 14px;border:1px solid #e8ddd0;border-left:0;border-radius:0 10px 10px 0;background:#fff8f0;color:#8a6040;font-size:13px;font-weight:700}
.fee-input.pct{border-radius:10px 0 0 10px}
.fee-help{display:block;margin-top:5px;color:#9a8070;font-size:11px}
.fee-error{display:block;margin-top:5px;color:#d94040;font-size:12px;font-weight:600}
.fee-example{padding:18px 20px;border:1px solid #f0e4d0;border-radius:12px;background:#fffaf5;margin-top:16px}
.fee-example-title{font-size:11px;font-weight:800;color:#a08060;text-transform:uppercase;letter-spacing:.06em;margin:0 0 12px}
.ex-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f5ece0;font-size:13px}
.ex-row:last-child{border:0}
.ex-row .el{color:#6a5040}
.ex-row .er{font-weight:700;color:#2a1810}
.ex-total{display:flex;justify-content:space-between;padding:10px 0 0;margin-top:4px;border-top:2px solid #e8ddd0;font-size:15px;font-weight:900}
.ex-total .el{color:#2a1810}
.ex-total .er{color:#e8813a}
.fee-row-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.fee-actions{display:flex;justify-content:flex-end;padding:20px 0 0}
.fee-btn{padding:12px 26px;border-radius:11px;border:0;color:#fff;background:linear-gradient(100deg,#c4601a,#e8813a);font:700 14px inherit;cursor:pointer;box-shadow:0 8px 18px #e8813a25;transition:.2s}
.fee-btn:hover{transform:translateY(-1px)}
.flash-success{display:flex;align-items:center;gap:10px;max-width:720px;margin:0 auto 14px;padding:12px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.history-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f5ece0;font-size:13px}
.history-row:last-child{border:0}
</style>
<div class="dashboard don-page">
    <div class="topbar">
        <div class="page-heading"><span>Donations › Fee Settings</span><small>Configure app handling charge and GST</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>
    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

    <div class="fee-wrap">
        <div class="fee-hero"><span class="hero-overline">Admin · Donations</span><h1>Donation Fee Settings</h1><p>Set the App Handling Charge and GST rate. Each change creates a new config — old transactions retain the rates used at the time of payment.</p></div>

        <div class="fee-section">
            <h2 class="fee-title"><span>₹</span> Current Active Configuration</h2>
            <form method="POST" action="{{ route('donation-fees.update') }}">
                @csrf @method('PUT')

                <div class="fee-row-2">
                    <div>
                        <label class="fee-label">App Handling Charge</label>
                        <div class="fee-input-wrap">
                            <span class="fee-prefix">₹</span>
                            <input type="number" name="handling_charge" class="fee-input" style="border-radius:0 10px 10px 0"
                                   value="{{ old('handling_charge', $config->handling_charge) }}"
                                   min="0" max="9999" step="0.01" required id="hc">
                        </div>
                        <span class="fee-help">Flat fee per donation (currently ₹{{ $config->handling_charge }})</span>
                        @error('handling_charge')<span class="fee-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="fee-label">GST Rate</label>
                        <div class="fee-input-wrap">
                            <input type="number" name="gst_rate" class="fee-input pct"
                                   value="{{ old('gst_rate', $config->gst_rate) }}"
                                   min="0" max="100" step="0.01" required id="gst">
                            <span class="fee-suffix">%</span>
                        </div>
                        <span class="fee-help">GST applied on the handling charge (currently {{ $config->gst_rate }}%)</span>
                        @error('gst_rate')<span class="fee-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Live example --}}
                <div class="fee-example">
                    <p class="fee-example-title">📋 Live Example — Donation of ₹500</p>
                    <div class="ex-row"><span class="el">Donation Amount</span><span class="er">₹500.00</span></div>
                    <div class="ex-row"><span class="el">App Handling Charge</span><span class="er" id="ex-hc">₹{{ number_format($config->handling_charge, 2) }}</span></div>
                    <div class="ex-row"><span class="el">GST on Handling Charge (<span id="ex-gst-pct">{{ $config->gst_rate }}</span>%)</span><span class="er" id="ex-gst">₹{{ number_format($config->handling_charge * $config->gst_rate / 100, 2) }}</span></div>
                    <div class="ex-total"><span class="el">Total Payable</span><span class="er" id="ex-total">₹{{ number_format(500 + $config->handling_charge + ($config->handling_charge * $config->gst_rate / 100), 2) }}</span></div>
                </div>

                <div class="fee-actions">
                    <button type="submit" class="fee-btn" onclick="return confirm('Save new fee configuration? This will apply to all new donations.')">💾 Save Configuration</button>
                </div>
            </form>
        </div>

        {{-- Config history --}}
        <div class="fee-section">
            <h2 class="fee-title"><span>📋</span> Configuration History</h2>
            @php $history = \App\Models\DonationFeeConfig::orderByDesc('id')->limit(10)->get(); @endphp
            @foreach($history as $h)
                <div class="history-row">
                    <div>
                        <span style="font-size:13px;font-weight:700;color:#2a1810">₹{{ $h->handling_charge }} + {{ $h->gst_rate }}% GST</span>
                        <span style="margin-left:8px;font-size:11px;color:#9a8070">{{ $h->created_at->format('d M Y, g:i A') }}</span>
                    </div>
                    @if($h->is_active)
                        <span style="padding:4px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750">● Active</span>
                    @else
                        <span style="padding:4px 9px;border-radius:20px;color:#9a8070;background:#f0f0f0;font-size:11px;font-weight:750">Superseded</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
<script>
const hcInput = document.getElementById('hc');
const gstInput = document.getElementById('gst');
function updateExample() {
    const hc = parseFloat(hcInput.value) || 0;
    const gstPct = parseFloat(gstInput.value) || 0;
    const gstAmt = Math.round(hc * gstPct / 100 * 100) / 100;
    const total = 500 + hc + gstAmt;
    document.getElementById('ex-hc').textContent = '₹' + hc.toFixed(2);
    document.getElementById('ex-gst-pct').textContent = gstPct;
    document.getElementById('ex-gst').textContent = '₹' + gstAmt.toFixed(2);
    document.getElementById('ex-total').textContent = '₹' + total.toFixed(2);
}
hcInput.addEventListener('input', updateExample);
gstInput.addEventListener('input', updateExample);
</script>
@endsection
