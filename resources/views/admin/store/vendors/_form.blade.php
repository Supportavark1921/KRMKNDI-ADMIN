<style>
.store-card{background:#fff;border:1px solid #e6e8f0;border-radius:16px;padding:24px;margin-bottom:18px}
.store-card h3{margin:0 0 18px;font-size:14px;font-weight:800;color:#15233d}
.fg label{display:block;font-size:11px;font-weight:700;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.fg input,.fg select,.fg textarea{width:100%;padding:10px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:14px;color:#15233d;background:#fafbfc;font-family:inherit}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#6246ea;outline:none;box-shadow:0 0 0 3px #6246ea18}
.fg-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.fg-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px}
.btn-run{background:#6246ea;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:800;cursor:pointer;padding:11px 20px}
</style>

@if(!isset($vendor))
<div class="store-card">
    <h3>Login Account (New User)</h3>
    <div class="fg-grid">
        <div class="fg">
            <label>Full Name *</label>
            <input name="user_name" value="{{ old('user_name') }}" required>
        </div>
        <div class="fg">
            <label>Email *</label>
            <input name="user_email" type="email" value="{{ old('user_email') }}" required>
        </div>
    </div>
    <div class="fg">
        <label>Password *</label>
        <input name="user_password" type="password" placeholder="Min 8 characters" required>
    </div>
</div>
@endif

<div class="store-card">
    <h3>Business Details</h3>
    <div class="fg-grid">
        <div class="fg">
            <label>Business Name *</label>
            <input name="business_name" value="{{ old('business_name', $vendor->business_name ?? '') }}" required>
        </div>
        <div class="fg">
            <label>Contact Person</label>
            <input name="contact_person" value="{{ old('contact_person', $vendor->contact_person ?? '') }}">
        </div>
    </div>
    <div class="fg-grid">
        <div class="fg">
            <label>Phone</label>
            <input name="phone" value="{{ old('phone', $vendor->phone ?? '') }}">
        </div>
        <div class="fg">
            <label>Business Email</label>
            <input name="email" type="email" value="{{ old('email', $vendor->email ?? '') }}">
        </div>
    </div>
    <div class="fg-grid">
        <div class="fg">
            <label>GSTIN</label>
            <input name="gstin" value="{{ old('gstin', $vendor->gstin ?? '') }}" placeholder="22AAAAA0000A1Z5">
        </div>
        <div class="fg">
            <label>PAN</label>
            <input name="pan" value="{{ old('pan', $vendor->pan ?? '') }}" placeholder="ABCDE1234F">
        </div>
    </div>
    <div class="fg-grid">
        <div class="fg">
            <label>Status</label>
            <select name="status">
                <option value="pending" @selected(old('status', $vendor->status ?? 'pending') === 'pending')>Pending Approval</option>
                <option value="active" @selected(old('status', $vendor->status ?? '') === 'active')>Active</option>
                <option value="suspended" @selected(old('status', $vendor->status ?? '') === 'suspended')>Suspended</option>
            </select>
        </div>
        <div class="fg">
            <label>City</label>
            <input name="city" value="{{ old('city', $vendor->city ?? '') }}">
        </div>
    </div>
    <div class="fg">
        <label>Address</label>
        <textarea name="address" rows="2">{{ old('address', $vendor->address ?? '') }}</textarea>
    </div>
</div>

<div class="store-card">
    <h3>Bank Details</h3>
    <div class="fg-grid-3">
        <div class="fg">
            <label>Account Number</label>
            <input name="bank_account_no" value="{{ old('bank_account_no', $vendor->bank_details['account_no'] ?? '') }}">
        </div>
        <div class="fg">
            <label>IFSC Code</label>
            <input name="bank_ifsc" value="{{ old('bank_ifsc', $vendor->bank_details['ifsc'] ?? '') }}">
        </div>
        <div class="fg">
            <label>Bank Name</label>
            <input name="bank_name" value="{{ old('bank_name', $vendor->bank_details['bank_name'] ?? '') }}">
        </div>
    </div>
</div>

@isset($vendor)
<div class="store-card">
    <h3>Admin Notes</h3>
    <div class="fg">
        <textarea name="admin_notes" rows="3" placeholder="Internal notes…">{{ old('admin_notes', $vendor->admin_notes ?? '') }}</textarea>
    </div>
</div>
@endisset

<div class="store-card">
    <h3>Logo</h3>
    @if(isset($vendor) && $vendor->logo)
        <img src="{{ asset('storage/'.$vendor->logo) }}" style="width:70px;height:70px;border-radius:10px;object-fit:cover;margin-bottom:10px;display:block">
    @endif
    <input type="file" name="logo" accept="image/*" style="font-size:13px">
</div>
