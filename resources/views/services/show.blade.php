@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('content')
<style>
.svc-show-page{background:radial-gradient(circle at 90% 4%,#e6e0ff 0,transparent 22%),#f6f7fc!important}
.svc-show-wrap{max-width:960px;margin:0 auto}
.svc-show-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;margin:35px auto 22px;padding:34px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#9d80ef 0,transparent 26%),linear-gradient(118deg,#1a1442,#3a2d82)}
.svc-show-hero h1{margin:8px 0;font-size:32px;color:#fff}
.svc-show-hero p{margin:0;color:#d8d4f8;font-size:14px}
.hero-overline{color:#ffdc87;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.svc-show-grid{display:grid;grid-template-columns:300px 1fr;gap:18px;margin-bottom:18px}
.svc-panel{padding:24px;border:1px solid #e4e7f2;border-radius:16px;background:#fff;box-shadow:0 4px 14px #1e2a5a06}
.svc-panel-title{font-size:13px;font-weight:800;color:#6b748c;letter-spacing:.06em;text-transform:uppercase;margin:0 0 16px;padding-bottom:12px;border-bottom:1px solid #f0f2f8}
.svc-primary-img{width:100%;border-radius:12px;object-fit:cover;aspect-ratio:4/3}
.svc-img-placeholder{width:100%;aspect-ratio:4/3;border-radius:12px;background:#ede9ff;display:flex;align-items:center;justify-content:center;color:#8b78d8;font-size:48px}
.detail-row{display:flex;flex-direction:column;gap:3px;margin-bottom:16px}
.detail-row:last-child{margin-bottom:0}
.detail-label{font-size:11px;font-weight:800;color:#9aa3bc;text-transform:uppercase;letter-spacing:.05em}
.detail-value{font-size:15px;font-weight:700;color:#1b2240}
.detail-value.muted{font-weight:400;color:#555e7a;font-size:14px;line-height:1.6}
.lang-chip{display:inline-block;padding:4px 9px;border-radius:7px;background:#ede9ff;color:#5a42a8;font-size:11px;font-weight:800;letter-spacing:.04em;margin:2px 3px 2px 0;text-transform:uppercase}
.status-active{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:12px;font-weight:750}
.status-inactive{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:20px;color:#7a4a18;background:#fff3e0;font-size:12px;font-weight:750}
.status-active::before,.status-inactive::before{content:"";width:6px;height:6px;border-radius:50%}
.status-active::before{background:#28a76a}
.status-inactive::before{background:#e69c3a}
.svc-gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;margin-top:14px}
.svc-gallery-grid img{width:100%;border-radius:10px;aspect-ratio:4/3;object-fit:cover;border:1px solid #e4e7f2}
.lang-content-tab{display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap}
.lct-btn{padding:7px 14px;border-radius:9px;border:1px solid #e4e7f2;background:#f8f8ff;color:#555e7a;font-size:13px;font-weight:700;cursor:pointer;transition:.15s}
.lct-btn.active{border-color:#6246ea;background:#ede9ff;color:#4934c4}
.lct-panel{display:none}.lct-panel.active{display:block}
.api-example{background:#1b2240;border-radius:12px;padding:16px 18px;margin-top:14px;font-family:ui-monospace,monospace;font-size:12px;color:#c8d0e8;overflow-x:auto}
.api-example .kw{color:#9d80ef}.api-example .str{color:#f6c453}.api-example .num{color:#7ec8a4}
/* Guruji pricing table */
.gs-table{width:100%;border-collapse:collapse;font-size:14px}
.gs-table th{padding:10px 12px;border-bottom:2px solid #e4e7f2;background:#fafbff;color:#6b748c;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;text-align:left}
.gs-table td{padding:13px 12px;border-bottom:1px solid #f0f2f8;vertical-align:top}
.gs-table tr:last-child td{border-bottom:0}
.gs-table tr:hover td{background:#faf9ff}
.samagri-pill{display:inline-block;padding:3px 8px;border-radius:6px;background:#f0ecff;color:#5a42a8;font-size:11px;font-weight:700;margin:2px 3px 2px 0}
.gs-add-form{padding:20px 24px;border-top:2px dashed #e4e7f2;background:#fafbff;border-radius:0 0 16px 16px}
.gs-add-form .sf-label{display:block;margin:0 0 5px;font-size:12px;font-weight:700;color:#555e7a}
.gs-input{width:100%;padding:9px 11px;border:1px solid #dde1ee;border-radius:9px;font:inherit;color:#1b2240;outline:none}
.gs-input:focus{border-color:#6246ea;box-shadow:0 0 0 3px #ede9ff}
.gs-samagri-row{display:grid;grid-template-columns:1fr 120px 30px;gap:8px;align-items:center;margin-bottom:8px}
.svc-actions{display:flex;gap:10px;margin-top:18px}
.btn-edit{display:inline-flex;align-items:center;gap:6px;padding:11px 18px;border-radius:10px;background:#6246ea;color:#fff;font:700 14px inherit;text-decoration:none;transition:.15s}
.btn-edit:hover{background:#4934c4}
.btn-back{display:inline-flex;align-items:center;gap:6px;padding:11px 16px;border-radius:10px;border:1px solid #e4e7f2;color:#555e7a;background:#fff;font:600 14px inherit;text-decoration:none;transition:.15s}
.btn-back:hover{background:#f4f5fb}
@media(max-width:700px){.svc-show-grid{grid-template-columns:1fr}}
</style>

<div class="dashboard svc-show-page">
    <div class="topbar">
        <div class="page-heading">
            <span><a href="{{ route('services.index') }}" style="color:inherit;text-decoration:none">Services</a> › View</span>
            <small>{{ $service->name() }}</small>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    <div class="svc-show-wrap">
        <div class="svc-show-hero">
            <div>
                <span class="hero-overline">Admin · Services</span>
                <h1>{{ $service->name() }}</h1>
                <p>ID: {{ $service->id }} · Created {{ $service->created_at->format('d M Y') }}</p>
            </div>
            <span class="status-{{ $service->status }}">{{ ucfirst($service->status) }}</span>
        </div>

        <div class="svc-show-grid">
            {{-- Left: image + meta --}}
            <div style="display:flex;flex-direction:column;gap:18px">
                <div class="svc-panel">
                    <p class="svc-panel-title">Primary Image</p>
                    @if($service->primaryImage())
                        <img src="{{ Storage::disk('public')->url($service->primaryImage()) }}" class="svc-primary-img" alt="">
                    @else
                        <div class="svc-img-placeholder">✦</div>
                    @endif
                </div>

                <div class="svc-panel">
                    <p class="svc-panel-title">Pricing</p>
                    <div class="detail-row">
                        <span class="detail-label">Price</span>
                        <span class="detail-value">
                            {{ $service->amount() !== null ? ($service->currency() === 'INR' ? '₹' : $service->currency()) . number_format($service->amount()) : '—' }}
                        </span>
                    </div>
                    @if($service->discountAmount() !== null)
                        <div class="detail-row">
                            <span class="detail-label">Discount Price</span>
                            <span class="detail-value">{{ $service->currency() === 'INR' ? '₹' : $service->currency() }}{{ number_format($service->discountAmount()) }}</span>
                        </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Currency</span>
                        <span class="detail-value">{{ $service->currency() }}</span>
                    </div>
                </div>

                <div class="svc-panel">
                    <p class="svc-panel-title">Languages</p>
                    @foreach($service->activeLanguages() as $lang)
                        <span class="lang-chip">{{ $lang }} · {{ $languages[$lang] ?? strtoupper($lang) }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Right: translations + gallery + API --}}
            <div style="display:flex;flex-direction:column;gap:18px">
                <div class="svc-panel">
                    <p class="svc-panel-title">Content</p>
                    <div class="lang-content-tab">
                        @foreach($service->activeLanguages() as $i => $lang)
                            <button type="button" class="lct-btn {{ $i === 0 ? 'active' : '' }}"
                                    onclick="switchContent('{{ $lang }}')">
                                {{ $languages[$lang] ?? strtoupper($lang) }}
                            </button>
                        @endforeach
                    </div>
                    @foreach($service->activeLanguages() as $i => $lang)
                        <div class="lct-panel {{ $i === 0 ? 'active' : '' }}" id="content-{{ $lang }}">
                            <div class="detail-row">
                                <span class="detail-label">Name</span>
                                <span class="detail-value">{{ $service->translations[$lang]['name'] ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Title</span>
                                <span class="detail-value">{{ $service->translations[$lang]['title'] ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Description</span>
                                <span class="detail-value muted">{{ $service->translations[$lang]['description'] ?? '—' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($service->gallery()) > 0)
                    <div class="svc-panel">
                        <p class="svc-panel-title">Gallery ({{ count($service->gallery()) }})</p>
                        <div class="svc-gallery-grid">
                            @foreach($service->gallery() as $path)
                                <img src="{{ Storage::disk('public')->url($path) }}" alt="">
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="svc-panel">
                    <p class="svc-panel-title">API Preview</p>
                    <p style="color:#7882a0;font-size:13px;margin:0 0 4px">
                        Mobile APK endpoint:
                        <code style="background:#f0f2f8;padding:2px 6px;border-radius:5px;font-size:12px">/api/services/{{ $service->id }}?language=en</code>
                    </p>
                    <div class="api-example">{<br>
&nbsp;&nbsp;<span class="kw">"id"</span>: <span class="num">{{ $service->id }}</span>,<br>
&nbsp;&nbsp;<span class="kw">"name"</span>: <span class="str">"{{ $service->name() }}"</span>,<br>
&nbsp;&nbsp;<span class="kw">"title"</span>: <span class="str">"{{ $service->title() }}"</span>,<br>
&nbsp;&nbsp;<span class="kw">"available_languages"</span>: [{{ collect($service->activeLanguages())->map(fn($l) => '"'.$l.'"')->join(', ') }}],<br>
&nbsp;&nbsp;<span class="kw">"pricing"</span>: {<span class="kw">"amount"</span>: <span class="num">{{ $service->amount() ?? 'null' }}</span>, <span class="kw">"currency"</span>: <span class="str">"{{ $service->currency() }}"</span>},<br>
&nbsp;&nbsp;<span class="kw">"status"</span>: <span class="str">"{{ $service->status }}"</span><br>}</div>
                </div>
            </div>
        </div>

        {{-- Guruji Pricing & Samagri --}}
        @if(session('success'))<div style="margin-bottom:14px;padding:12px 16px;border-radius:10px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600">✓ {{ session('success') }}</div>@endif

        <div class="svc-panel" style="margin-bottom:18px;padding:0;overflow:hidden">
            <div style="padding:20px 24px;border-bottom:1px solid #f0f2f8;display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p class="svc-panel-title" style="margin:0;border:0;padding:0">Guruji Pricing &amp; Samagri</p>
                    <p style="margin:4px 0 0;color:#7882a0;font-size:12px">Each Guruji can have their own price and samagri list for this service.</p>
                </div>
                <button type="button" onclick="document.getElementById('gs-add-panel').classList.toggle('hidden')"
                        style="padding:8px 14px;border-radius:9px;border:1px dashed #c4b7f5;background:transparent;color:#6246ea;font:700 13px inherit;cursor:pointer">
                    ＋ Add Guruji
                </button>
            </div>

            @if($service->guruServices->isEmpty())
                <div style="padding:32px;text-align:center;color:#9aa3bc">
                    <div style="font-size:28px;margin-bottom:8px">🕉</div>
                    <p style="margin:0;font-size:14px">No Guruji pricing set yet. Add one below.</p>
                </div>
            @else
                <table class="gs-table">
                    <thead><tr>
                        <th>Guruji</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Samagri</th>
                        <th>Status</th>
                        <th style="text-align:right">Actions</th>
                    </tr></thead>
                    <tbody>
                    @foreach($service->guruServices as $gs)
                    <tr>
                        <td><strong>{{ $gs->guru->name }}</strong></td>
                        <td><strong>₹{{ number_format($gs->amount() ?? 0) }}</strong></td>
                        <td>{{ $gs->discountAmount() !== null ? '₹'.number_format($gs->discountAmount()) : '—' }}</td>
                        <td>
                            @forelse($gs->samagri() as $item)
                                <span class="samagri-pill">{{ $item['name'] }}@if(!empty($item['price'])) · ₹{{ $item['price'] }}@endif</span>
                            @empty
                                <span style="color:#9aa3bc;font-size:12px">—</span>
                            @endforelse
                        </td>
                        <td><span class="status-{{ $gs->status }}">{{ ucfirst($gs->status) }}</span></td>
                        <td style="text-align:right">
                            <button type="button"
                                    onclick="toggleEdit({{ $gs->id }})"
                                    style="padding:5px 10px;border-radius:7px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font:600 12px inherit;cursor:pointer">Edit</button>
                            <form method="POST" action="{{ route('guru-services.destroy', [$service, $gs]) }}" style="display:inline"
                                  onsubmit="return confirm('Remove this Guruji pricing?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="padding:5px 10px;border-radius:7px;border:1px solid #fdc9c9;background:#fff0f0;color:#d94040;font:600 12px inherit;cursor:pointer">Remove</button>
                            </form>
                        </td>
                    </tr>
                    {{-- Inline edit form --}}
                    <tr id="edit-row-{{ $gs->id }}" style="display:none;background:#fafbff">
                        <td colspan="6" style="padding:16px 20px">
                            <form method="POST" action="{{ route('guru-services.update', [$service, $gs]) }}">
                                @csrf @method('PUT')
                                <div style="display:grid;grid-template-columns:1fr 130px 130px 120px;gap:12px;margin-bottom:14px;align-items:end">
                                    <div>
                                        <label class="gs-add-form sf-label" style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Guruji</label>
                                        <div style="padding:9px 11px;border:1px solid #e4e7f2;border-radius:9px;background:#f8f8ff;color:#6b748c;font-size:13px">{{ $gs->guru->name }}</div>
                                    </div>
                                    <div>
                                        <label style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Price (₹) *</label>
                                        <input type="number" name="pricing_amount" class="gs-input" min="0" required value="{{ $gs->amount() }}">
                                    </div>
                                    <div>
                                        <label style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Discount (₹)</label>
                                        <input type="number" name="pricing_discount_amount" class="gs-input" min="0" value="{{ $gs->discountAmount() }}">
                                    </div>
                                    <div>
                                        <label style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Status</label>
                                        <select name="gs_status" class="gs-input">
                                            <option value="active" @selected($gs->status==='active')>Active</option>
                                            <option value="inactive" @selected($gs->status==='inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="pricing_currency" value="{{ $gs->currency() }}">
                                </div>
                                <p style="margin:0 0 8px;font-size:11px;font-weight:800;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Samagri List</p>
                                <div id="samagri-edit-{{ $gs->id }}" style="display:grid;gap:8px;margin-bottom:10px">
                                    @foreach($gs->samagri() as $i => $item)
                                    <div class="gs-samagri-row">
                                        <input type="text" name="pooja_samagri[{{ $i }}][name]" class="gs-input" placeholder="Item name" value="{{ $item['name'] }}" required>
                                        <input type="number" name="pooja_samagri[{{ $i }}][price]" class="gs-input" placeholder="₹ price" min="0" step="0.01" value="{{ $item['price'] ?? '' }}">
                                        <button type="button" onclick="this.closest('.gs-samagri-row').remove()" style="width:30px;height:36px;border:1px solid #fdc9c9;border-radius:7px;background:#fff0f0;color:#d94040;cursor:pointer;font-size:16px">×</button>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" onclick="addSamagriEditRow('samagri-edit-{{ $gs->id }}', {{ $gs->samagri() ? count($gs->samagri()) : 0 }})"
                                        style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border:1px dashed #c4b7f5;border-radius:8px;background:transparent;color:#6246ea;font:700 12px inherit;cursor:pointer;margin-bottom:14px">
                                    ＋ Add Item
                                </button>
                                <div style="display:flex;gap:8px">
                                    <button type="submit" style="padding:9px 18px;border-radius:9px;border:0;background:#6246ea;color:#fff;font:700 13px inherit;cursor:pointer">Save Changes</button>
                                    <button type="button" onclick="toggleEdit({{ $gs->id }})" style="padding:9px 14px;border-radius:9px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font:600 13px inherit;cursor:pointer">Cancel</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            {{-- Add new Guruji panel --}}
            <div id="gs-add-panel" class="gs-add-form hidden">
                <p style="margin:0 0 16px;font-size:13px;font-weight:800;color:#1b2240">Add Guruji Pricing</p>
                <form method="POST" action="{{ route('guru-services.store', $service) }}">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 130px 130px 120px;gap:12px;margin-bottom:16px;align-items:end">
                        <div>
                            <label style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Guruji *</label>
                            <select name="guru_id" class="gs-input" required>
                                <option value="">— Select Guruji —</option>
                                @foreach($gurus as $guru)
                                    @if(!in_array($guru->id, $assignedGuruIds))
                                    <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Price (₹) *</label>
                            <input type="number" name="pricing_amount" class="gs-input" min="0" required placeholder="0">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Discount (₹)</label>
                            <input type="number" name="pricing_discount_amount" class="gs-input" min="0" placeholder="Optional">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Status</label>
                            <select name="gs_status" class="gs-input">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <input type="hidden" name="pricing_currency" value="INR">
                    </div>
                    <p style="margin:0 0 8px;font-size:11px;font-weight:800;color:#555e7a;text-transform:uppercase;letter-spacing:.04em">Samagri List</p>
                    <div id="samagri-add-list" style="display:grid;gap:8px;margin-bottom:10px">
                        @if($service->pooja_samagri)
                            @foreach($service->pooja_samagri as $i => $item)
                            <div class="gs-samagri-row">
                                <input type="text" name="pooja_samagri[{{ $i }}][name]" class="gs-input" placeholder="Item name" value="{{ $item['name'] }}" required>
                                <input type="number" name="pooja_samagri[{{ $i }}][price]" class="gs-input" placeholder="₹ price" min="0" step="0.01" value="{{ $item['price'] ?? '' }}">
                                <button type="button" onclick="this.closest('.gs-samagri-row').remove()" style="width:30px;height:36px;border:1px solid #fdc9c9;border-radius:7px;background:#fff0f0;color:#d94040;cursor:pointer;font-size:16px">×</button>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" onclick="addSamagriAddRow()"
                            style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border:1px dashed #c4b7f5;border-radius:8px;background:transparent;color:#6246ea;font:700 12px inherit;cursor:pointer;margin-bottom:16px">
                        ＋ Add Samagri Item
                    </button>
                    <div style="display:flex;gap:8px">
                        <button type="submit" style="padding:9px 18px;border-radius:9px;border:0;background:#6246ea;color:#fff;font:700 13px inherit;cursor:pointer">Add Guruji Pricing</button>
                        <button type="button" onclick="document.getElementById('gs-add-panel').classList.add('hidden')" style="padding:9px 14px;border-radius:9px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font:600 13px inherit;cursor:pointer">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="svc-actions">
            <a href="{{ route('services.index') }}" class="btn-back">← Back to Services</a>
            <a href="{{ route('services.edit', $service) }}" class="btn-edit">✎ Edit Service</a>
            <form method="POST" action="{{ route('services.destroy', $service) }}"
                  onsubmit="return confirm('Delete this service? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" style="padding:11px 16px;border-radius:10px;border:1px solid #fdc9c9;color:#d94040;background:#fff0f0;font:600 14px inherit;cursor:pointer">🗑 Delete</button>
            </form>
        </div>
    </div>
</div>
<script>
function switchContent(lang) {
    document.querySelectorAll('.lct-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.lct-panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`.lct-btn[onclick*="${lang}"]`).classList.add('active');
    document.getElementById('content-' + lang).classList.add('active');
}

function toggleEdit(id) {
    const row = document.getElementById('edit-row-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

// samagri rows for the "Add Guruji" form
let addRowIdx = {{ $service->pooja_samagri ? count($service->pooja_samagri) : 0 }};
function addSamagriAddRow() {
    const i = addRowIdx++;
    const row = document.createElement('div');
    row.className = 'gs-samagri-row';
    row.innerHTML = `
        <input type="text" name="pooja_samagri[${i}][name]" class="gs-input" placeholder="Item name" required>
        <input type="number" name="pooja_samagri[${i}][price]" class="gs-input" placeholder="₹ price" min="0" step="0.01">
        <button type="button" onclick="this.closest('.gs-samagri-row').remove()" style="width:30px;height:36px;border:1px solid #fdc9c9;border-radius:7px;background:#fff0f0;color:#d94040;cursor:pointer;font-size:16px">×</button>`;
    document.getElementById('samagri-add-list').appendChild(row);
    row.querySelector('input[type=text]').focus();
}

// samagri rows for the inline edit forms
const editCounters = {};
function addSamagriEditRow(containerId, startIdx) {
    if (!editCounters[containerId]) editCounters[containerId] = startIdx;
    const i = editCounters[containerId]++;
    const row = document.createElement('div');
    row.className = 'gs-samagri-row';
    row.innerHTML = `
        <input type="text" name="pooja_samagri[${i}][name]" class="gs-input" placeholder="Item name" required>
        <input type="number" name="pooja_samagri[${i}][price]" class="gs-input" placeholder="₹ price" min="0" step="0.01">
        <button type="button" onclick="this.closest('.gs-samagri-row').remove()" style="width:30px;height:36px;border:1px solid #fdc9c9;border-radius:7px;background:#fff0f0;color:#d94040;cursor:pointer;font-size:16px">×</button>`;
    document.getElementById(containerId).appendChild(row);
    row.querySelector('input[type=text]').focus();
}
</script>
@endsection
