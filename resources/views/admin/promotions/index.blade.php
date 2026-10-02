@extends('layouts.app')
@section('title', 'App Content')
@section('content')
<div class="store-header">
    <div>
        <h1 class="store-title">App Content</h1>
        <p class="store-sub">Banners, promotions, announcements and offers shown in the mobile app.</p>
    </div>
    @can('promotions.create')
    <a href="{{ route('admin.promotions.create') }}" class="btn-primary">+ New</a>
    @endcan
</div>

<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Title…" class="form-input" style="width:180px">
    <select name="type" class="form-input">
        <option value="">All types</option>
        @foreach($types as $t)
        <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
        @endforeach
    </select>
    <select name="placement" class="form-input">
        <option value="">All placements</option>
        @foreach($placements as $p)
        <option value="{{ $p }}" @selected(request('placement') === $p)>{{ str_replace('_',' ',ucfirst($p)) }}</option>
        @endforeach
    </select>
    <select name="status" class="form-input">
        <option value="">All status</option>
        <option value="draft"    @selected(request('status') === 'draft')>Draft</option>
        <option value="active"   @selected(request('status') === 'active')>Active</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        <option value="trashed"  @selected(request('status') === 'trashed')>Archived</option>
    </select>
    <button type="submit" class="btn-secondary">Filter</button>
    <a href="{{ route('admin.promotions.index') }}" class="btn-secondary">Clear</a>
</form>

@if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

<div class="store-card">
    <table class="store-table">
        <thead><tr>
            <th style="width:60px">Image</th>
            <th>Title</th><th>Type</th><th>Placement</th><th>Audience</th><th>Status</th><th>Schedule</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($promotions as $p)
        <tr class="{{ $p->trashed() ? 'opacity-50' : '' }}">
            <td>
                @if($p->image)
                <img src="{{ Storage::url($p->image) }}" style="width:48px;height:36px;object-fit:cover;border-radius:4px">
                @else
                <div style="width:48px;height:36px;background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:18px">🖼</div>
                @endif
            </td>
            <td><strong>{{ $p->title }}</strong><br><small style="color:#888">{{ $p->cta_type !== 'none' ? 'CTA: '.$p->cta_type : '' }}</small></td>
            <td><span class="badge badge-active">{{ ucfirst($p->type) }}</span></td>
            <td>{{ str_replace('_',' ',ucfirst($p->placement)) }}</td>
            <td>{{ ucfirst($p->audience) }}</td>
            <td>
                @if($p->trashed()) <span class="badge badge-inactive">Archived</span>
                @elseif($p->status === 'active') <span class="badge badge-active">Active</span>
                @else <span class="badge badge-inactive">{{ ucfirst($p->status) }}</span>
                @endif
            </td>
            <td style="font-size:12px;white-space:nowrap">
                {{ $p->starts_at?->format('d M') ?? '—' }} → {{ $p->ends_at?->format('d M') ?? '∞' }}
            </td>
            <td style="text-align:right;white-space:nowrap">
                @if($p->trashed())
                    @can('promotions.restore')
                    <form method="POST" action="{{ route('admin.promotions.restore', $p->id) }}" style="display:inline">
                        @csrf <button class="btn-sm btn-success">Restore</button>
                    </form>
                    @endcan
                @else
                    @can('promotions.update')
                    <a href="{{ route('admin.promotions.edit', $p) }}" class="btn-sm">Edit</a>
                    @if($p->status === 'active')
                    <form method="POST" action="{{ route('admin.promotions.deactivate', $p) }}" style="display:inline">
                        @csrf <button class="btn-sm btn-warning">Deactivate</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.promotions.activate', $p) }}" style="display:inline">
                        @csrf <button class="btn-sm btn-success">Activate</button>
                    </form>
                    @endif
                    @endcan
                    @can('promotions.delete')
                    <form method="POST" action="{{ route('admin.promotions.destroy', $p) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn-sm btn-danger" onclick="return confirm('Archive this promotion?')">Archive</button>
                    </form>
                    @endcan
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:24px;color:#888">No promotions found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="padding:12px">{{ $promotions->links() }}</div>
</div>
@endsection
