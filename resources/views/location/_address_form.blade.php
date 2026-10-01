{{--
    Reusable cascading address form partial.

    Usage:
        @include('location._address_form', [
            'prefix'  => 'address',           // field name prefix (optional, default '')
            'values'  => $model->address ?? [],  // existing values array (optional)
            'required' => true,               // mark fields required (optional)
        ])

    The form emits fields:
        {prefix}[country_id], {prefix}[state_id], {prefix}[district_id],
        {prefix}[city_id], {prefix}[pincode_id], {prefix}[address_line],
        {prefix}[landmark]
--}}

@php
    $prefix   = isset($prefix) && $prefix ? $prefix . '[%s]' : '%s';
    $field    = fn(string $n) => sprintf($prefix, $n);
    $val      = fn(string $n) => old($field($n), $values[$n] ?? '');
    $req      = ($required ?? false) ? 'required' : '';
@endphp

<div class="location-address-form" data-prefix="{{ $field('') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PIN code quick-lookup --}}
    <div style="display:flex;gap:10px;align-items:flex-end;margin-bottom:4px">
        <div style="flex:1">
            <label style="display:block;margin:0 0 7px;font-size:14px;font-weight:650">Quick PIN Lookup</label>
            <input type="text" id="laf-pinlookup" placeholder="Enter 6-digit PIN to auto-fill..."
                   maxlength="6" style="width:100%;padding:11px 14px;border:1px solid #e6e9f0;border-radius:11px;font:inherit">
        </div>
        <button type="button" onclick="lafLookupPin()" style="margin:0;width:auto;padding:11px 16px;border-radius:11px;background:#f0ecff;color:#5c4bb7;box-shadow:none;font-size:13px">Lookup</button>
    </div>
    <p id="laf-lookup-msg" style="margin:4px 0 14px;font-size:12px;color:#69758b;min-height:18px"></p>

    {{-- Country --}}
    <label style="display:block;margin:14px 0 7px;font-size:14px;font-weight:650">Country {{ $req ? '*' : '' }}</label>
    <select name="{{ $field('country_id') }}" id="laf-country" {{ $req }} onchange="lafLoadStates(this.value)"
            style="width:100%;padding:13px 14px;border:1px solid #e6e9f0;border-radius:11px;color:#15233d;background:#fff;font:inherit">
        <option value="">Select Country</option>
    </select>

    {{-- State --}}
    <label style="display:block;margin:14px 0 7px;font-size:14px;font-weight:650">State / Union Territory {{ $req ? '*' : '' }}</label>
    <select name="{{ $field('state_id') }}" id="laf-state" {{ $req }} onchange="lafLoadDistricts(this.value)" disabled
            style="width:100%;padding:13px 14px;border:1px solid #e6e9f0;border-radius:11px;color:#15233d;background:#fff;font:inherit">
        <option value="">Select country first</option>
    </select>

    {{-- District --}}
    <label style="display:block;margin:14px 0 7px;font-size:14px;font-weight:650">District {{ $req ? '*' : '' }}</label>
    <select name="{{ $field('district_id') }}" id="laf-district" {{ $req }} onchange="lafLoadCities(this.value)" disabled
            style="width:100%;padding:13px 14px;border:1px solid #e6e9f0;border-radius:11px;color:#15233d;background:#fff;font:inherit">
        <option value="">Select state first</option>
    </select>

    {{-- City --}}
    <label style="display:block;margin:14px 0 7px;font-size:14px;font-weight:650">City {{ $req ? '*' : '' }}</label>
    <select name="{{ $field('city_id') }}" id="laf-city" {{ $req }} onchange="lafLoadPincodes(this.value)" disabled
            style="width:100%;padding:13px 14px;border:1px solid #e6e9f0;border-radius:11px;color:#15233d;background:#fff;font:inherit">
        <option value="">Select district first</option>
    </select>

    {{-- PIN code --}}
    <label style="display:block;margin:14px 0 7px;font-size:14px;font-weight:650">PIN Code {{ $req ? '*' : '' }}</label>
    <select name="{{ $field('pincode_id') }}" id="laf-pincode" {{ $req }} disabled
            style="width:100%;padding:13px 14px;border:1px solid #e6e9f0;border-radius:11px;color:#15233d;background:#fff;font:inherit">
        <option value="">Select city first</option>
    </select>

    {{-- Address line --}}
    <label style="display:block;margin:14px 0 7px;font-size:14px;font-weight:650">Address Line {{ $req ? '*' : '' }}</label>
    <textarea name="{{ $field('address_line') }}" rows="3" {{ $req }} placeholder="House/flat, street, area..."
              style="width:100%;padding:13px 14px;border:1px solid #e6e9f0;border-radius:11px;font:inherit;resize:vertical">{{ $val('address_line') }}</textarea>

    {{-- Landmark --}}
    <label style="display:block;margin:14px 0 7px;font-size:14px;font-weight:650">Landmark <em style="font-weight:400;font-style:normal;color:#69758b">(optional)</em></label>
    <input type="text" name="{{ $field('landmark') }}" value="{{ $val('landmark') }}"
           placeholder="Near temple, opposite school..."
           style="width:100%;padding:13px 14px;border:1px solid #e6e9f0;border-radius:11px;font:inherit">
</div>

<script>
(function () {
    const BASE = '/api/locations';
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const sel  = id => document.getElementById(id);
    const setOptions = (el, items, labelFn, valFn, placeholder) => {
        el.innerHTML = `<option value="">${placeholder}</option>` +
            items.map(i => `<option value="${valFn(i)}">${labelFn(i)}</option>`).join('');
        el.disabled = items.length === 0;
    };
    const loading = el => { el.innerHTML = '<option>Loading…</option>'; el.disabled = true; };
    const clearFrom = (...ids) => ids.forEach(id => { sel(id).innerHTML = '<option value="">—</option>'; sel(id).disabled = true; });

    // Boot: load countries
    fetch(`${BASE}/countries`).then(r => r.json()).then(data => {
        setOptions(sel('laf-country'), data, d => d.name, d => d.id, 'Select Country');
        // Restore pre-selected values from PHP (old input or model)
        const savedCountry = '{{ $val('country_id') }}';
        if (savedCountry) {
            sel('laf-country').value = savedCountry;
            lafLoadStates(savedCountry, '{{ $val('state_id') }}');
        }
    });

    window.lafLoadStates = async function (countryId, preselectState) {
        clearFrom('laf-state', 'laf-district', 'laf-city', 'laf-pincode');
        if (!countryId) return;
        loading(sel('laf-state'));
        const data = await fetch(`${BASE}/countries/${countryId}/states`).then(r => r.json());
        setOptions(sel('laf-state'), data, d => d.name, d => d.id, 'Select State');
        if (preselectState) {
            sel('laf-state').value = preselectState;
            lafLoadDistricts(preselectState, '{{ $val('district_id') }}');
        }
    };

    window.lafLoadDistricts = async function (stateId, preselectDistrict) {
        clearFrom('laf-district', 'laf-city', 'laf-pincode');
        if (!stateId) return;
        loading(sel('laf-district'));
        const data = await fetch(`${BASE}/states/${stateId}/districts`).then(r => r.json());
        setOptions(sel('laf-district'), data, d => d.name, d => d.id, 'Select District');
        if (preselectDistrict) {
            sel('laf-district').value = preselectDistrict;
            lafLoadCities(preselectDistrict, '{{ $val('city_id') }}');
        }
    };

    window.lafLoadCities = async function (districtId, preselectCity) {
        clearFrom('laf-city', 'laf-pincode');
        if (!districtId) return;
        loading(sel('laf-city'));
        const data = await fetch(`${BASE}/districts/${districtId}/cities`).then(r => r.json());
        setOptions(sel('laf-city'), data, d => d.name, d => d.id, 'Select City');
        if (preselectCity) {
            sel('laf-city').value = preselectCity;
            lafLoadPincodes(preselectCity, '{{ $val('pincode_id') }}');
        }
    };

    window.lafLoadPincodes = async function (cityId, preselectPin) {
        clearFrom('laf-pincode');
        if (!cityId) return;
        loading(sel('laf-pincode'));
        const data = await fetch(`${BASE}/cities/${cityId}/pincodes`).then(r => r.json());
        setOptions(sel('laf-pincode'), data,
            d => `${d.pincode} — ${d.post_office_name}`,
            d => d.id,
            'Select PIN Code'
        );
        if (preselectPin) sel('laf-pincode').value = preselectPin;
    };

    window.lafLookupPin = async function () {
        const pin = sel('laf-pinlookup').value.trim();
        const msg = sel('laf-lookup-msg');
        if (pin.length !== 6 || !/^\d+$/.test(pin)) { msg.textContent = 'Enter a valid 6-digit PIN.'; return; }
        msg.textContent = 'Looking up…';
        const res = await fetch(`${BASE}/pincodes/${pin}`);
        if (!res.ok) { msg.textContent = 'No records found for this PIN code.'; return; }
        const data = await res.json();
        if (!data.length) { msg.textContent = 'No records found.'; return; }
        const first = data[0];
        msg.innerHTML = `Found: <strong>${first.state?.name}</strong> → <strong>${first.district?.name}</strong> — confirm below and select your post office.`;
        // Populate cascading dropdowns
        if (first.state_id) {
            const countryId = sel('laf-country').value;
            // Re-load states for the current country then select this state
            if (countryId) {
                const states = await fetch(`${BASE}/countries/${countryId}/states`).then(r => r.json());
                setOptions(sel('laf-state'), states, d => d.name, d => d.id, 'Select State');
                sel('laf-state').value = first.state_id;
                const districts = await fetch(`${BASE}/states/${first.state_id}/districts`).then(r => r.json());
                setOptions(sel('laf-district'), districts, d => d.name, d => d.id, 'Select District');
                if (first.district_id) {
                    sel('laf-district').value = first.district_id;
                    if (first.city_id) {
                        const cities = await fetch(`${BASE}/districts/${first.district_id}/cities`).then(r => r.json());
                        setOptions(sel('laf-city'), cities, d => d.name, d => d.id, 'Select City');
                        sel('laf-city').value = first.city_id;
                        const pins = await fetch(`${BASE}/cities/${first.city_id}/pincodes`).then(r => r.json());
                        setOptions(sel('laf-pincode'), pins, d => `${d.pincode} — ${d.post_office_name}`, d => d.id, 'Select PIN Code');
                        // Select the first matching PIN
                        const match = pins.find(p => p.pincode === pin);
                        if (match) sel('laf-pincode').value = match.id;
                    }
                }
            }
        }
    };
})();
</script>
