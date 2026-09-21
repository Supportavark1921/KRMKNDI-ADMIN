@extends('layouts.app', ['title' => 'Create account · ARK Jyotish'])

@section('content')
<main class="auth-page">
    <aside class="auth-art">
        <div class="brand"><span class="brand-mark">✦</span> ARK Jyotish</div>
        <div class="art-copy"><div class="eyebrow">Begin your journey</div><h1>Set your path among the stars.</h1><p>Create your space and access astrology designed around you.</p><div class="zodiac"><span>☉</span><span>☾</span><span>♃</span><span>♄</span></div></div>
    </aside>
    <section class="auth-panel"><div class="form-wrap">
        <h2>Create your account</h2><p class="subtext">Choose the account type that fits your role.</p>
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('register.store') }}">@csrf
            <div class="grid"><div><label for="name">Full name</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required></div><div><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required></div></div>
            <label>Account type</label><div class="roles"><label class="role"><input type="radio" name="role" value="user" {{ old('role', 'user') === 'user' ? 'checked' : '' }}><span><strong>✦ User</strong><small>Explore your journey</small></span></label><label class="role"><input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }}><span><strong>✦ Admin</strong><small>Manage the platform</small></span></label></div>
            <div class="grid"><div><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" required></div><div><label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div></div>
            <button type="submit">Create my account</button>
        </form>
        <p class="form-foot">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </div></section>
</main>
@endsection
