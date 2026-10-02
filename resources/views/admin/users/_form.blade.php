{{-- Shared form partial for create/edit --}}
<div class="store-card" style="max-width:680px">
    <div class="form-grid">

        <div class="form-group">
            <label class="form-label">Full name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-input @error('name') is-invalid @enderror" required>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-input @error('email') is-invalid @enderror" required>
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Password {{ isset($user) ? '(leave blank to keep current)' : '*' }}</label>
            <input type="password" name="password" class="form-input @error('password') is-invalid @enderror" {{ isset($user) ? '' : 'required' }} autocomplete="new-password">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="form-input" {{ isset($user) ? '' : 'required' }} autocomplete="new-password">
        </div>

        <div class="form-group">
            <label class="form-label">Role *</label>
            <select name="role" class="form-input @error('role') is-invalid @enderror"
                {{ (isset($user) && $user->id === auth()->id()) ? 'disabled' : '' }}>
                @foreach($roles as $r)
                <option value="{{ $r }}" @selected(old('role', $user->role ?? 'user') === $r)>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            @if(isset($user) && $user->id === auth()->id())
                <input type="hidden" name="role" value="{{ $user->role }}">
                <p class="form-hint">You cannot change your own role.</p>
            @endif
            @error('role')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" class="form-input @error('status') is-invalid @enderror">
                <option value="active"    @selected(old('status', $user->status ?? 'active') === 'active')>Active</option>
                <option value="suspended" @selected(old('status', $user->status ?? '') === 'suspended')>Suspended</option>
            </select>
            @error('status')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        {{-- Vendor business name shown when creating a vendor --}}
        <div class="form-group" id="vendor-name-group" style="{{ old('role', $user->role ?? 'user') === 'vendor' ? '' : 'display:none' }}">
            <label class="form-label">Business name</label>
            <input type="text" name="business_name" value="{{ old('business_name', $user->vendor->business_name ?? '') }}" class="form-input" placeholder="Auto-filled from name if blank">
        </div>

    </div>

    <div style="margin-top:20px;display:flex;gap:10px">
        <button type="submit" class="btn-primary">{{ isset($user) ? 'Update User' : 'Create User' }}</button>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>

<script>
document.querySelector('[name="role"]')?.addEventListener('change', function () {
    document.getElementById('vendor-name-group').style.display =
        this.value === 'vendor' ? '' : 'none';
});
</script>
