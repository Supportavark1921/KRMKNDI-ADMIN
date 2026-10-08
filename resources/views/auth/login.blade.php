@extends('layouts.app', ['title' => 'Sign in · ARK Jyotish'])

@section('content')
<main class="auth-page">
    <aside class="auth-art">
        <div class="brand">
            <img src="{{ asset('images/krmknd-brand-logo.png') }}" alt="krmknd" style="height:42px;width:auto;object-fit:contain;">
            <span style="color:#ffd575;font-weight:700;letter-spacing:.04em">krmknd</span>
        </div>
        <div class="art-copy"><div class="eyebrow">Ancient wisdom, thoughtfully presented</div><h1>Find clarity in every constellation.</h1><p>Your personal space for a more mindful astrological journey.</p><div class="zodiac"><span>♈</span><span>♉</span><span>♊</span><span>♋</span></div></div>
    </aside>
    <section class="auth-panel"><div class="form-wrap">
        <h2>Welcome back</h2><p class="subtext">Sign in to continue your journey with ARK Jyotish.</p>
        @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('login.attempt') }}">@csrf
            <label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
            <label class="check"><input name="remember" type="checkbox" value="1"> Keep me signed in</label>
            <button type="submit">Sign in securely</button>
        </form>
        <p class="form-foot">New to ARK Jyotish? <a href="{{ route('register') }}">Create an account</a></p>
    </div></section>
</main>
@endsection
