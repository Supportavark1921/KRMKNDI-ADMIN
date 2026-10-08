@extends('layouts.app')
@section('content')
<style>
.pn-page{background:radial-gradient(circle at 88% 5%,#e8e1ff 0,transparent 22%),#f7f8fc!important}
.pn-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:860px;margin:32px auto 20px;padding:34px 42px;border-radius:22px;color:#fff;background:radial-gradient(circle at 82% 10%,#a87fd6 0,transparent 28%),linear-gradient(118deg,#1c2e5e,#4a3590)}
.pn-hero h1{margin:8px 0;font-size:34px;color:#fff}.pn-hero p{max-width:520px;margin:0;color:#dce4fc;line-height:1.6}
.hero-overline{color:#ffd585;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.pn-card{max-width:860px;margin:0 auto 40px;padding:36px 40px;border:1px solid #e5e7f0;border-radius:20px;background:#fff;box-shadow:0 17px 40px #2b33670e}
.pn-card h2{margin:0 0 22px;font-size:20px;color:#1c2a4a}
.pn-field{margin-bottom:18px}
.pn-field label{display:block;margin-bottom:6px;font-size:12px;font-weight:800;color:#606d83;text-transform:uppercase;letter-spacing:.06em}
.pn-field input[type=text],.pn-field textarea,.pn-field select{width:100%;padding:11px 14px;border:1px solid #e2e6f0;border-radius:10px;font:inherit;color:#1c2a4a;background:#fff;outline:none;transition:.2s;box-sizing:border-box}
.pn-field input:focus,.pn-field textarea:focus,.pn-field select:focus{border-color:#5c4bb7;box-shadow:0 0 0 3px #f0eeff}
.pn-field textarea{resize:vertical;min-height:80px}
.pn-radios{display:flex;gap:12px}
.pn-radio{flex:1;display:flex;align-items:center;gap:10px;padding:13px 16px;border:2px solid #e2e6f0;border-radius:12px;cursor:pointer;transition:.2s}
.pn-radio:has(input:checked){border-color:#5c4bb7;background:#f5f2ff}
.pn-radio input{accent-color:#5c4bb7}
.pn-radio span{font-size:14px;font-weight:600;color:#1c2a4a}
.pn-radio small{display:block;font-size:11px;color:#8090b0;font-weight:400}
.user-select-wrap{margin-top:10px;display:none}
.user-select-wrap.visible{display:block}
.user-select-wrap select{height:140px}
.pn-preview{padding:16px;border:1px solid #e2e6f0;border-radius:14px;background:#f8f9fc;margin-top:6px}
.pn-preview-label{font-size:10px;font-weight:800;color:#8090b0;text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px}
.pn-preview-notif{display:flex;align-items:flex-start;gap:12px;padding:14px;border-radius:12px;background:#fff;box-shadow:0 4px 14px #1c2a4a0d}
.pn-preview-icon{display:grid;width:40px;height:40px;flex:0 0 auto;place-items:center;border-radius:10px;font-size:20px;background:#f0eeff}
.pn-preview-copy b{display:block;font-size:13px;color:#1c2a4a}
.pn-preview-copy span{display:block;margin-top:2px;font-size:12px;color:#606d83;line-height:1.4}
.pn-preview-copy small{display:block;margin-top:4px;font-size:10px;color:#a0aab8}
.pn-divider{margin:24px 0;border:0;border-top:1px solid #edf0f5}
.pn-submit{display:inline-flex;align-items:center;gap:8px;padding:13px 28px;border-radius:12px;color:#fff;background:linear-gradient(135deg,#5c4bb7,#7b5ea7);font-size:14px;font-weight:700;border:none;cursor:pointer;transition:.2s;box-shadow:0 6px 18px #5c4bb728}
.pn-submit:hover{box-shadow:0 8px 24px #5c4bb740;transform:translateY(-1px)}
.flash-success{display:flex;align-items:center;gap:10px;max-width:860px;margin:0 auto 14px;padding:12px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.flash-error{display:flex;align-items:center;gap:10px;max-width:860px;margin:0 auto 14px;padding:12px 16px;border-radius:12px;background:#fdeaea;color:#8b2a2a;font-size:14px;font-weight:600}
.pn-deep-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.pn-color-row{display:flex;align-items:center;gap:10px}
.pn-color-row input[type=color]{width:44px;height:38px;padding:2px;border:1px solid #e2e6f0;border-radius:8px;cursor:pointer;background:#fff}
.pn-color-row input[type=text]{flex:1}
.pn-preview-image{width:100%;max-height:120px;object-fit:cover;border-radius:8px;margin-top:8px;display:none}
@media(max-width:600px){.pn-radios,.pn-deep-grid{grid-template-columns:1fr;flex-direction:column}.pn-card{padding:24px 20px}}
</style>
<div class="dashboard pn-page">
    <div class="topbar">
        <div class="page-heading"><span>Push Notifications</span><small>Send to app users</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))
        <div class="flash-success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error">✗ {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="flash-error">✗ {{ $errors->first() }}</div>
    @endif

    <div class="pn-hero">
        <div>
            <span class="hero-overline">Admin · Push Notifications</span>
            <h1>Send Push Notification</h1>
            <p>Reach your app users instantly on their devices.</p>
        </div>
        <span style="font-size:42px">🔔</span>
    </div>

    <form method="POST" action="{{ route('push-notifications.send') }}" id="pnForm">
        @csrf
        <div class="pn-card">
            <h2>Audience</h2>
            <div class="pn-field">
                <div class="pn-radios">
                    <label class="pn-radio">
                        <input type="radio" name="target" value="all" checked onchange="toggleUsers(this)">
                        <div><span>All Users</span><small>Every registered device</small></div>
                    </label>
                    <label class="pn-radio">
                        <input type="radio" name="target" value="users" onchange="toggleUsers(this)">
                        <div><span>Specific Users</span><small>Pick from the list</small></div>
                    </label>
                </div>
                <div class="user-select-wrap" id="userSelectWrap">
                    <label class="pn-field" style="margin-top:12px;margin-bottom:0">
                        <span style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;color:#606d83;text-transform:uppercase;letter-spacing:.06em">Select Users (hold Ctrl / ⌘ for multiple)</span>
                        <select name="user_ids[]" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ collect(old('user_ids', []))->contains($user->id) ? 'selected' : '' }}>
                                    {{ $user->name ?? 'User #'.$user->id }} — {{ $user->phone }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

            <hr class="pn-divider">
            <h2>Message</h2>

            <div class="pn-field">
                <label>Title</label>
                <input type="text" name="title" id="pnTitle" maxlength="100"
                    value="{{ old('title') }}"
                    placeholder="e.g. Your booking is confirmed"
                    oninput="updatePreview()">
            </div>
            <div class="pn-field">
                <label>Body</label>
                <textarea name="body" id="pnBody" maxlength="200"
                    placeholder="e.g. Your pooja with Guruji is scheduled for tomorrow at 10 AM."
                    oninput="updatePreview()">{{ old('body') }}</textarea>
            </div>

            <div class="pn-deep-grid">
                <div class="pn-field">
                    <label>Accent Colour</label>
                    <div class="pn-color-row">
                        <input type="color" id="pnColorPicker" value="{{ old('color', '#5c4bb7') }}" oninput="syncColor(this.value)">
                        <input type="text" name="color" id="pnColor" maxlength="7"
                            value="{{ old('color', '#5c4bb7') }}"
                            placeholder="#5c4bb7"
                            oninput="syncColorText(this.value)">
                    </div>
                </div>
                <div class="pn-field">
                    <label>Image URL <span style="font-weight:400;color:#aab">(optional)</span></label>
                    <input type="text" name="image" id="pnImage"
                        value="{{ old('image') }}"
                        placeholder="https://…/image.jpg"
                        oninput="updatePreview()">
                </div>
            </div>

            <div class="pn-field">
                <label>Preview</label>
                <div class="pn-preview">
                    <div class="pn-preview-label">Device notification</div>
                    <div class="pn-preview-notif" id="previewNotif">
                        <div class="pn-preview-icon" id="previewIcon">🪔</div>
                        <div class="pn-preview-copy">
                            <b id="previewTitle">Notification title</b>
                            <span id="previewBody">Notification body will appear here…</span>
                            <small>ARK Guruji · now</small>
                        </div>
                    </div>
                    <img id="previewImg" class="pn-preview-image" src="" alt="preview">
                </div>
            </div>

            <hr class="pn-divider">
            <h2>Deep Link <span style="font-size:12px;font-weight:400;color:#8090b0">(optional — opens a specific screen on tap)</span></h2>

            <div class="pn-deep-grid">
                <div class="pn-field">
                    <label>Screen</label>
                    <select name="screen" id="pnScreen" onchange="toggleTab()">
                        <option value="">— None —</option>
                        <option value="Home" {{ old('screen') === 'Home' ? 'selected' : '' }}>Home</option>
                        <option value="History" {{ old('screen') === 'History' ? 'selected' : '' }}>History</option>
                    </select>
                </div>
                <div class="pn-field" id="tabField" style="display:none">
                    <label>Tab</label>
                    <select name="tab">
                        <option value="">— None —</option>
                        <option value="bookings" {{ old('tab') === 'bookings' ? 'selected' : '' }}>Bookings</option>
                        <option value="donations" {{ old('tab') === 'donations' ? 'selected' : '' }}>Donations</option>
                    </select>
                </div>
            </div>

            <hr class="pn-divider">
            <button type="submit" class="pn-submit">🔔 Send Notification</button>
        </div>
    </form>
</div>

<script>
function toggleUsers(radio) {
    document.getElementById('userSelectWrap').classList.toggle('visible', radio.value === 'users');
}
function updatePreview() {
    const t     = document.getElementById('pnTitle').value;
    const b     = document.getElementById('pnBody').value;
    const color = document.getElementById('pnColor').value;
    const img   = document.getElementById('pnImage').value;
    document.getElementById('previewTitle').textContent = t || 'Notification title';
    document.getElementById('previewBody').textContent  = b || 'Notification body will appear here…';
    // accent colour on icon background
    const icon = document.getElementById('previewIcon');
    if (/^#[0-9a-fA-F]{6}$/.test(color)) {
        icon.style.background = color + '22';
    }
    // image preview
    const previewImg = document.getElementById('previewImg');
    if (img) {
        previewImg.src   = img;
        previewImg.style.display = 'block';
    } else {
        previewImg.style.display = 'none';
    }
}
function syncColor(val) {
    document.getElementById('pnColor').value = val;
    updatePreview();
}
function syncColorText(val) {
    if (/^#[0-9a-fA-F]{6}$/.test(val)) {
        document.getElementById('pnColorPicker').value = val;
    }
    updatePreview();
}
function toggleTab() {
    const screen = document.getElementById('pnScreen').value;
    document.getElementById('tabField').style.display = screen === 'History' ? '' : 'none';
}
// Restore state on validation error
(function(){
    const target = document.querySelector('input[name=target]:checked');
    if (target) toggleUsers(target);
    toggleTab();
    updatePreview();
})();
</script>
@endsection
