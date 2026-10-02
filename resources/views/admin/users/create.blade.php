@extends('layouts.app')
@section('title', 'New User')
@section('content')
<style>
.ux-form-page{background:radial-gradient(circle at 92% 4%,#dde4ff 0,transparent 22%),#f5f6fc!important}
.ux-form-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:860px;margin:35px auto 28px;padding:32px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#748ffc 0,transparent 26%),linear-gradient(118deg,#1c2a5e,#364bbf)}
.ux-form-hero h1{margin:6px 0 0;font-size:28px;color:#fff}
.ux-form-hero p{margin:5px 0 0;color:#ccd4f8;font-size:14px}
.ux-overline{color:#ffd787;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.ux-form-card{max-width:860px;margin:0 auto;border:1px solid #e4e7f2;border-radius:20px;background:#fff;box-shadow:0 18px 45px #1e2a5a09;overflow:hidden}
.ux-form-section{padding:28px 32px;border-bottom:1px solid #f0f2f8}
.ux-form-section:last-child{border-bottom:0}
.ux-section-title{font-size:13px;font-weight:800;color:#6b748c;letter-spacing:.06em;text-transform:uppercase;margin:0 0 18px}
.ux-field-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.ux-field{display:flex;flex-direction:column;gap:6px}
.ux-field.full{grid-column:1/-1}
.ux-label{font-size:13px;font-weight:700;color:#1b2240}
.ux-hint{font-size:12px;color:#7882a0;margin-top:2px}
.ux-input{padding:11px 14px;border:1px solid #e0e3ef;border-radius:10px;font:14px/1.4 inherit;color:#1b2240;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;width:100%}
.ux-input:focus{border-color:#3b5bdb;box-shadow:0 0 0 3px #e8edff}
.ux-input.is-invalid{border-color:#e04a4a}
select.ux-input{appearance:auto;cursor:pointer}
.ux-error{font-size:12px;color:#e04a4a;margin-top:3px}
.ux-role-card{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.ux-role-opt{position:relative}
.ux-role-opt input{position:absolute;opacity:0;pointer-events:none}
.ux-role-opt label{display:block;padding:13px 14px;border:2px solid #e4e7f2;border-radius:12px;cursor:pointer;transition:.15s}
.ux-role-opt input:checked + label{border-color:#3b5bdb;background:#e8edff}
.ux-role-opt label b{display:block;font-size:13px;color:#1b2240;margin-bottom:3px}
.ux-role-opt label small{display:block;font-size:11px;color:#7882a0}
.ux-role-opt input:checked + label b{color:#1e3fa0}
.ux-form-footer{display:flex;align-items:center;gap:12px;padding:22px 32px;background:#fafbff;border-top:1px solid #f0f2f8}
.ux-submit{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border:0;border-radius:10px;cursor:pointer;color:#fff;background:linear-gradient(100deg,#3b5bdb,#5578f0);box-shadow:0 6px 16px #3b5bdb2a;font:700 14px inherit;transition:opacity .15s,transform .1s}
.ux-submit:hover{opacity:.9;transform:translateY(-1px)}
.ux-cancel{display:inline-flex;align-items:center;padding:12px 18px;border-radius:10px;border:1px solid #e0e3ef;color:#555e7a;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.ux-cancel:hover{background:#f5f6fc}
.flash-error{display:flex;align-items:center;gap:10px;max-width:860px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fce8e8;color:#962a2a;font-size:14px;font-weight:600}
@media(max-width:700px){.ux-field-grid{grid-template-columns:1fr}.ux-role-card{grid-template-columns:1fr 1fr}}
</style>

<div class="dashboard ux-form-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small>Users / New User</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if($errors->any())
    <div class="flash-error">✗ Please fix the errors below before saving.</div>
    @endif

    <div class="ux-form-hero">
        <div>
            <span class="ux-overline">Users · Create</span>
            <h1>New User Account</h1>
            <p>Fill in the details below. The user will be able to log in immediately.</p>
        </div>
        <div style="font-size:48px">👤</div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="ux-form-card">

            {{-- Basic info --}}
            <div class="ux-form-section">
                <p class="ux-section-title">Account Details</p>
                <div class="ux-field-grid">
                    <div class="ux-field">
                        <label class="ux-label">Full name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="ux-input @error('name') is-invalid @enderror" required placeholder="e.g. Ravi Sharma">
                        @error('name')<p class="ux-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="ux-field">
                        <label class="ux-label">Email address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="ux-input @error('email') is-invalid @enderror" required placeholder="user@example.com">
                        @error('email')<p class="ux-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="ux-field">
                        <label class="ux-label">Password *</label>
                        <input type="password" name="password" class="ux-input @error('password') is-invalid @enderror" required autocomplete="new-password" placeholder="Min 8 characters">
                        @error('password')<p class="ux-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="ux-field">
                        <label class="ux-label">Confirm password *</label>
                        <input type="password" name="password_confirmation" class="ux-input" required autocomplete="new-password" placeholder="Repeat password">
                    </div>
                    <div class="ux-field">
                        <label class="ux-label">Status *</label>
                        <select name="status" class="ux-input @error('status') is-invalid @enderror">
                            <option value="active"    @selected(old('status','active') === 'active')>Active — can log in immediately</option>
                            <option value="suspended" @selected(old('status') === 'suspended')>Suspended — blocked from login</option>
                        </select>
                        @error('status')<p class="ux-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="ux-field" id="vendor-biz-field" style="{{ old('role') === 'vendor' ? '' : 'display:none' }}">
                        <label class="ux-label">Business name</label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}" class="ux-input" placeholder="Auto-filled from name if blank">
                        <span class="ux-hint">Only shown for Vendor role.</span>
                    </div>
                </div>
            </div>

            {{-- Role picker --}}
            <div class="ux-form-section">
                <p class="ux-section-title">Role *</p>
                @error('role')<p class="ux-error" style="margin-bottom:12px">{{ $message }}</p>@enderror
                <div class="ux-role-card">
                    @php
                        $roleDescriptions = [
                            'admin'   => 'Full system access',
                            'manager' => 'Broad operational access',
                            'support' => 'View & assist clients',
                            'guruji'  => 'Pooja & Mataji orders',
                            'vendor'  => 'Own store products',
                            'user'    => 'Mobile app end-user',
                        ];
                        $roleIcons = ['admin'=>'🛡','manager'=>'⚙','support'=>'🎧','guruji'=>'🕉','vendor'=>'🏪','user'=>'👤'];
                    @endphp
                    @foreach($roles as $r)
                    <div class="ux-role-opt">
                        <input type="radio" name="role" id="role_{{ $r }}" value="{{ $r }}" @checked(old('role','user') === $r)>
                        <label for="role_{{ $r }}">
                            <b>{{ $roleIcons[$r] ?? '' }} {{ ucfirst($r) }}</b>
                            <small>{{ $roleDescriptions[$r] ?? '' }}</small>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="ux-form-footer">
            <button type="submit" class="ux-submit">Create User</button>
            <a href="{{ route('admin.users.index') }}" class="ux-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[name="role"]').forEach(r => r.addEventListener('change', function () {
    document.getElementById('vendor-biz-field').style.display = this.value === 'vendor' ? '' : 'none';
}));
</script>
@endsection
