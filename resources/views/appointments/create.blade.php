@extends('layouts.app', ['title' => 'Book an appointment - ARK Jyotish'])

@section('content')
<main class="dashboard">
    <header class="topbar">
        <div class="page-heading"><span>Book an appointment</span><small>Request a session with Pandit Ji</small></div>
        <a class="nav-link topbar-link" href="{{ route('appointments.index') }}">My appointments</a>
    </header>

    <section class="booking-card">
        <span class="pill">Appointment booking</span>
        <h1>Book time with Pandit Ji</h1>
        <p>Choose a suitable date and time. Your request will be reviewed and confirmed by Pandit Ji.</p>

        @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif

        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf
            <div class="form-grid">
                <div>
                    <label>Service</label>
                    <select name="service" required>
                        <option value="">— Select a service —</option>
                        @foreach($services as $svc)
                            @php
                                $enName = $svc->name('en');
                                $hiName = $svc->name('hi');
                                $price  = $svc->amount();
                            @endphp
                            <option value="{{ $enName }}" @selected(old('service') === $enName)>
                                {{ $enName }}{{ $hiName ? ' — ' . $hiName : '' }}{{ $price ? ' (₹' . number_format($price) . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Phone number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210" required>
                </div>
                <div>
                    <label>Preferred date</label>
                    <input type="date" name="appointment_date" min="{{ now()->toDateString() }}" value="{{ old('appointment_date') }}" required>
                </div>
                <div>
                    <label for="appointment_time">Available time</label>
                    <select id="appointment_time" name="appointment_time" required disabled>
                        <option>Select a date first</option>
                    </select>
                    <small class="field-help" id="time-help">Only open time slots are shown.</small>
                </div>
            </div>

            <label>Notes for Pandit Ji <em>(optional)</em></label>
            <textarea name="notes" rows="4" placeholder="Any specific questions or details you'd like to share…">{{ old('notes') }}</textarea>

            <div style="display:flex;align-items:center;gap:16px">
                <button type="submit" style="width:auto;margin-top:24px;padding:13px 24px">Send booking request</button>
                <a href="{{ route('appointments.index') }}" style="margin-top:24px;color:#69758b;font-size:14px">Cancel</a>
            </div>
        </form>
    </section>
</main>

<script>
const dateInput = document.querySelector('[name="appointment_date"]');
const timeInput = document.querySelector('#appointment_time');
const timeHelp  = document.querySelector('#time-help');
const oldTime   = @json(old('appointment_time'));

async function loadTimes() {
    const date = dateInput.value;
    timeInput.innerHTML = '<option>Loading…</option>';
    timeInput.disabled = true;
    if (!date) {
        timeInput.innerHTML = '<option>Select a date first</option>';
        timeHelp.textContent = 'Only open time slots are shown.';
        return;
    }
    try {
        const res  = await fetch('{{ route('availability.times') }}?date=' + encodeURIComponent(date));
        const data = await res.json();
        timeInput.innerHTML = '<option value="">Select a time</option>';
        data.times.forEach(t => {
            const opt = new Option(
                new Date('2000-01-01T' + t).toLocaleTimeString([], {hour:'numeric', minute:'2-digit'}),
                t, false, t === oldTime
            );
            timeInput.add(opt);
        });
        timeInput.disabled = !data.times.length;
        timeHelp.textContent = data.times.length
            ? data.times.length + ' slot' + (data.times.length === 1 ? '' : 's') + ' available.'
            : 'No sessions available on this date. Please choose another day.';
    } catch {
        timeInput.innerHTML = '<option>Unable to load times</option>';
        timeHelp.textContent = 'Please try selecting the date again.';
    }
}

dateInput.addEventListener('change', loadTimes);
if (dateInput.value) loadTimes();
</script>
@endsection
