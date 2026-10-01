<style>
.store-card{background:#fff;border:1px solid #e6e8f0;border-radius:16px;padding:24px;margin-bottom:18px}
.store-card h3{margin:0 0 18px;font-size:14px;font-weight:800;color:#15233d}
.fg label{display:block;font-size:11px;font-weight:700;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.fg input,.fg select,.fg textarea{width:100%;padding:10px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:14px;color:#15233d;background:#fafbfc;font-family:inherit}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#6246ea;outline:none;box-shadow:0 0 0 3px #6246ea18}
.fg-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.btn-run{background:#6246ea;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:800;cursor:pointer;padding:11px 20px}
.btn-run:hover{background:#4934c4}
.toggle-row{display:flex;align-items:center;gap:12px}
.toggle-row label{margin:0;font-size:14px;color:#15233d;font-weight:600}
</style>

<div class="store-card">
    <h3>Basic Info</h3>
    <div class="fg-grid">
        <div class="fg">
            <label>Mataji Name *</label>
            <input name="name" value="{{ old('name', $mataji->name ?? '') }}" required>
        </div>
        <div class="fg">
            <label>Temple Name</label>
            <input name="temple_name" value="{{ old('temple_name', $mataji->temple_name ?? '') }}">
        </div>
    </div>
    <div class="fg" style="margin-bottom:14px">
        <label>Description</label>
        <textarea name="description" rows="3">{{ old('description', $mataji->description ?? '') }}</textarea>
    </div>
    <div class="fg-grid">
        <div class="fg">
            <label>Status</label>
            <select name="status">
                <option value="active" @selected(old('status', $mataji->status ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $mataji->status ?? '') === 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="fg" style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:2px">
            <div class="toggle-row">
                <input type="hidden" name="offering_available" value="0">
                <input type="checkbox" name="offering_available" value="1" id="offering_avail"
                    {{ old('offering_available', $mataji->offering_available ?? true) ? 'checked' : '' }}
                    style="width:16px;height:16px">
                <label for="offering_avail">Offering Available</label>
            </div>
        </div>
    </div>
</div>

<div class="store-card">
    <h3>Location</h3>
    <div class="fg" style="margin-bottom:14px">
        <label>Address</label>
        <input name="address" value="{{ old('address', $mataji->address ?? '') }}">
    </div>
    <div class="fg-grid">
        <div class="fg">
            <label>City</label>
            <input name="city" value="{{ old('city', $mataji->city ?? '') }}">
        </div>
        <div class="fg">
            <label>State</label>
            <input name="state" value="{{ old('state', $mataji->state ?? '') }}">
        </div>
    </div>
    <div class="fg" style="margin-bottom:0">
        <label>PIN Code</label>
        <input name="pin_code" value="{{ old('pin_code', $mataji->pin_code ?? '') }}" style="max-width:160px">
    </div>
</div>

<div class="store-card">
    <h3>Contact</h3>
    <div class="fg-grid">
        <div class="fg">
            <label>Phone</label>
            <input name="contact_phone" value="{{ old('contact_phone', $mataji->contact_info['phone'] ?? '') }}">
        </div>
        <div class="fg">
            <label>Email</label>
            <input name="contact_email" type="email" value="{{ old('contact_email', $mataji->contact_info['email'] ?? '') }}">
        </div>
    </div>
</div>

<div class="store-card">
    <h3>Image</h3>
    @if(isset($mataji) && $mataji->image)
        <img src="{{ asset('storage/'.$mataji->image) }}" style="width:80px;height:80px;border-radius:10px;object-fit:cover;margin-bottom:10px;display:block">
    @endif
    <input type="file" name="image" accept="image/*" style="font-size:13px">
</div>
