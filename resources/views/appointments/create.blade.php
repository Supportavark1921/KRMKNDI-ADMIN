@extends('layouts.app', ['title' => 'Book an appointment - ARK Jyotish'])

@section('content')
<main class="dashboard">
    <header class="topbar"><a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">*</span> ARK Jyotish</a><a class="nav-link" href="{{ route('appointments.index') }}">My appointments</a></header>
    <section class="booking-card">
        <span class="pill">Appointment booking</span><h1>Book time with Pandit Ji</h1><p>Choose a suitable date and time. Your request will be confirmed by Pandit Ji.</p>
        <form method="POST" action="{{ route('appointments.store') }}">@csrf
            <div class="form-grid">
                <div><label>Service</label><select name="service" required><option>Kundli consultation</option><option>Puja booking</option><option>Marriage matching</option><option>Griha pravesh / Muhurat</option></select></div>
                <div><label>Phone number</label><input name="phone" value="{{ old('phone') }}" required></div>
                <div><label>Preferred date</label><input type="date" name="appointment_date" min="{{ now()->toDateString() }}" value="{{ old('appointment_date') }}" required></div>
                <div><label for="appointment_time">Available time</label><select id="appointment_time" name="appointment_time" required disabled><option>Select a date first</option></select><small class="field-help" id="time-help">Only open time slots are shown.</small></div>
            </div>
            <label>Notes for Pandit Ji <em>(optional)</em></label><textarea name="notes" rows="4">{{ old('notes') }}</textarea>
            @if ($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
            <button>Send booking request</button>
        </form>
    </section>
</main>
<script>
const dateInput = document.querySelector('[name="appointment_date"]');
const timeInput = document.querySelector('#appointment_time');
const timeHelp = document.querySelector('#time-help');
const oldTime = @json(old('appointment_time'));
async function loadTimes() {
    const date = dateInput.value;
    timeInput.innerHTML = '<option>Loading available times...</option>';
    timeInput.disabled = true;
    if (!date) { timeInput.innerHTML = '<option>Select a date first</option>'; timeHelp.textContent = 'Only open time slots are shown.'; return; }
    try {
        const response = await fetch('{{ route('availability.times') }}?date=' + encodeURIComponent(date));
        const result = await response.json();
        timeInput.innerHTML = '<option value="">Select a time</option>';
        result.times.forEach(function (time) {
            const option = new Option(new Date('2000-01-01T' + time).toLocaleTimeString([], {hour:'numeric', minute:'2-digit'}), time, false, time === oldTime);
            timeInput.add(option);
        });
        timeInput.disabled = !result.times.length;
        timeHelp.textContent = result.times.length ? result.times.length + ' available slot' + (result.times.length === 1 ? '' : 's') + ' on this date.' : 'No sessions are available on this date. Please choose another day.';
    } catch { timeInput.innerHTML = '<option>Unable to load times</option>'; timeHelp.textContent = 'Please try selecting the date again.'; }
}
dateInput.addEventListener('change', loadTimes);
if (dateInput.value) loadTimes();
</script>
@endsection
