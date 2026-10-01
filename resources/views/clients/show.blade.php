@extends('layouts.app', ['title' => $client->name . ' - ARK Jyotish'])

@section('content')
<main class="dashboard appointments-page admin-appointments">
    <header class="topbar">
        <a class="nav-link topbar-link" href="{{ route('clients.index') }}">← Clients</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </header>

    <div style="max-width:900px;margin:36px auto 0">

        {{-- Client header card --}}
        <div style="display:flex;align-items:center;gap:20px;padding:32px 36px;border:1px solid #e6e8f0;border-radius:20px;background:#fff;box-shadow:0 18px 45px #23325c0b;margin-bottom:20px">
            <div class="client-avatar" style="width:60px;height:60px;font-size:22px;flex:0 0 auto">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
            <div style="flex:1">
                <h1 style="margin:0 0 4px;font-size:28px;color:#15233d">{{ $client->name }}</h1>
                <p style="margin:0;color:#69758b;font-size:14px">{{ $client->email }} &nbsp;·&nbsp; Member since {{ $client->created_at->format('M Y') }}</p>
            </div>
            <div style="text-align:right">
                <span style="display:block;font-size:28px;font-weight:800;color:#15233d">{{ $client->appointments->count() }}</span>
                <small style="color:#69758b;font-size:12px">total appointments</small>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:20px">
            {{-- Birth / profile details --}}
            <div style="padding:26px;border:1px solid #e6e8f0;border-radius:18px;background:#fff">
                <h2 style="margin:0 0 18px;font-size:16px;color:#15233d">Birth details</h2>
                @if($client->clientProfile)
                    @php $p = $client->clientProfile; @endphp
                    <dl style="display:grid;gap:10px;margin:0">
                        @if($p->birth_date)
                            <div><dt style="font-size:11px;font-weight:800;color:#8a95a9;text-transform:uppercase;letter-spacing:.05em">Date of birth</dt><dd style="margin:3px 0 0;font-size:14px;color:#15233d">{{ \Carbon\Carbon::parse($p->birth_date)->format('d F Y') }}</dd></div>
                        @endif
                        @if($p->birth_time)
                            <div><dt style="font-size:11px;font-weight:800;color:#8a95a9;text-transform:uppercase;letter-spacing:.05em">Time of birth</dt><dd style="margin:3px 0 0;font-size:14px;color:#15233d">{{ \Carbon\Carbon::parse($p->birth_time, 'H:i')->format('g:i A') }}</dd></div>
                        @endif
                        @if($p->birth_place)
                            <div><dt style="font-size:11px;font-weight:800;color:#8a95a9;text-transform:uppercase;letter-spacing:.05em">Birth place</dt><dd style="margin:3px 0 0;font-size:14px;color:#15233d">{{ $p->birth_place }}</dd></div>
                        @endif
                        @if($p->address)
                            <div><dt style="font-size:11px;font-weight:800;color:#8a95a9;text-transform:uppercase;letter-spacing:.05em">Address</dt><dd style="margin:3px 0 0;font-size:14px;color:#15233d">{{ $p->address }}</dd></div>
                        @endif
                        @if($p->family_details)
                            <div><dt style="font-size:11px;font-weight:800;color:#8a95a9;text-transform:uppercase;letter-spacing:.05em">Family details</dt><dd style="margin:3px 0 0;font-size:14px;color:#15233d;white-space:pre-wrap">{{ $p->family_details }}</dd></div>
                        @endif
                        @if(!$p->birth_date && !$p->birth_time && !$p->birth_place)
                            <p style="color:#8a95a9;font-size:13px;margin:0">No birth details provided yet.</p>
                        @endif
                    </dl>
                @else
                    <p style="color:#8a95a9;font-size:13px;margin:0">Client has not filled in their profile.</p>
                @endif
            </div>

            {{-- Admin notes --}}
            <div style="padding:26px;border:1px solid #e6e8f0;border-radius:18px;background:#fff">
                <h2 style="margin:0 0 18px;font-size:16px;color:#15233d">Private notes</h2>
                <form method="POST" action="{{ route('clients.notes', $client) }}">
                    @csrf @method('PUT')
                    @if(session('success'))<div class="notice success" style="margin-bottom:14px">{{ session('success') }}</div>@endif
                    <textarea name="admin_notes" rows="6" placeholder="Notes visible only to you…" style="font-size:13px;line-height:1.6">{{ old('admin_notes', $client->clientProfile?->admin_notes) }}</textarea>
                    <button type="submit" style="width:auto;margin-top:12px;padding:10px 16px;font-size:13px">Save notes</button>
                </form>
            </div>
        </div>

        {{-- Appointment history --}}
        <div style="padding:26px;border:1px solid #e6e8f0;border-radius:18px;background:#fff;margin-bottom:40px">
            <h2 style="margin:0 0 18px;font-size:16px;color:#15233d">Appointment history</h2>
            @forelse($client->appointments as $appointment)
                <div style="display:grid;grid-template-columns:52px minmax(0,1fr) 110px;gap:14px;align-items:center;padding:14px 0;border-bottom:1px solid #edf0f5">
                    <div class="appointment-date" style="padding:7px;background:#f3f0fd;border-radius:10px;text-align:center">
                        <strong style="font-size:18px;color:#54419c">{{ $appointment->appointment_date->format('d') }}</strong>
                        <span style="display:block;font-size:10px;font-weight:800;color:#8d84a9;text-transform:uppercase">{{ $appointment->appointment_date->format('M') }}</span>
                    </div>
                    <div>
                        <span style="font-size:11px;font-weight:800;color:#755ab9;text-transform:uppercase;letter-spacing:.05em">{{ $appointment->service }}</span>
                        <p style="margin:3px 0 0;font-size:13px;color:#536078">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }} &nbsp;·&nbsp; {{ $appointment->appointment_date->format('l, d F Y') }}</p>
                    </div>
                    <div style="text-align:right"><span class="status status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span></div>
                </div>
            @empty
                <p style="color:#8a95a9;font-size:13px;margin:0">No appointments yet.</p>
            @endforelse
        </div>
    </div>
</main>
@endsection
