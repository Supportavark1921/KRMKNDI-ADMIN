@extends('layouts.app')
@section('title', 'Articles & Blog')
@section('content')
<style>
.art-page{background:radial-gradient(circle at 92% 4%,#e8f4ff 0,transparent 22%),#f5f9ff!important}
.art-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;max-width:1200px;margin:35px auto 22px;padding:38px 44px;border-radius:22px;color:#fff;background:radial-gradient(circle at 86% 10%,#60a5fa 0,transparent 26%),linear-gradient(118deg,#0c2461,#1a56db)}
.art-hero h1{margin:8px 0;font-size:38px;color:#fff}
.art-hero p{max-width:600px;margin:0;color:#bfdbfe;line-height:1.6}
.art-overline{color:#fde68a;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.art-add-btn{display:flex;align-items:center;gap:8px;flex:0 0 auto;padding:13px 18px;border-radius:12px;color:#0c2461;background:linear-gradient(120deg,#fde68a,#fbbf24);font-size:14px;font-weight:800;white-space:nowrap;text-decoration:none;box-shadow:0 10px 22px #08142f26;transition:transform .15s}
.art-add-btn:hover{transform:translateY(-2px)}
.art-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;max-width:1200px;margin:0 auto 18px}
.art-stat{display:flex;align-items:center;gap:12px;padding:17px 20px;border:1px solid #dbeafe;border-radius:15px;background:#fff}
.art-stat-icon{display:grid;width:38px;height:38px;place-items:center;border-radius:11px;font-size:18px}
.art-stat-icon.all{color:#1a56db;background:#dbeafe}
.art-stat-icon.pub{color:#1e6b47;background:#e2f7ed}
.art-stat-icon.draft{color:#7a5a10;background:#fff3d2}
.art-stat-icon.arch{color:#666;background:#eee}
.art-stat strong,.art-stat small{display:block}
.art-stat strong{color:#1b2240;font-size:20px}
.art-stat small{color:#7882a0;font-size:12px}
.art-toolbar{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 18px;flex-wrap:wrap}
.art-search{flex:1;min-width:180px;position:relative}
.art-search input{padding:11px 14px 11px 40px;border:1px solid #d1e4f6;border-radius:11px;font:inherit;color:#1e2640;background:#fff;width:100%;outline:none;transition:.2s}
.art-search input:focus{border-color:#1a56db;box-shadow:0 0 0 3px #dbeafe}
.art-search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9aa3bc;font-size:15px}
.art-filter select{padding:10px 12px;border:1px solid #d1e4f6;border-radius:11px;font:inherit;color:#3a4060;background:#fff;outline:none;cursor:pointer}
.art-board{max-width:1200px;margin:0 auto;border:1px solid #dbeafe;border-radius:20px;background:#fff;box-shadow:0 18px 45px #0c246109;overflow:hidden}
.art-table{width:100%;border-collapse:collapse}
.art-table thead th{padding:13px 16px;border-bottom:1px solid #eff6ff;background:#f8faff;color:#1e40af;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;text-align:left}
.art-table thead th:last-child{text-align:right}
.art-table tbody tr{border-bottom:1px solid #f0f7ff;transition:background .15s}
.art-table tbody tr:last-child{border:0}
.art-table tbody tr:hover{background:#f8faff}
.art-table td{padding:14px 16px;vertical-align:middle}
.art-thumb{width:56px;height:40px;border-radius:8px;object-fit:cover;background:#dbeafe;border:1px solid #bfdbfe;display:flex;align-items:center;justify-content:center;color:#1a56db;font-size:20px;overflow:hidden;flex-shrink:0}
.art-thumb img{width:100%;height:100%;object-fit:cover}
.art-title b{display:block;font-size:14px;color:#1b2240;margin-bottom:2px}
.art-title span{font-size:11px;color:#6b7a9a}
.art-chip{display:inline-block;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700;text-transform:capitalize}
.art-cat{color:#1e40af;background:#dbeafe}
.art-status-published{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#1e6b47;background:#e2f7ed;font-size:11px;font-weight:750}
.art-status-draft{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#7a5a10;background:#fff3d2;font-size:11px;font-weight:750}
.art-status-archived,.art-status-trashed{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:20px;color:#666;background:#eee;font-size:11px;font-weight:750}
.art-status-published::before,.art-status-draft::before,.art-status-archived::before,.art-status-trashed::before{content:"";width:6px;height:6px;border-radius:50%}
.art-status-published::before{background:#28a76a}
.art-status-draft::before{background:#f0a42e}
.art-status-archived::before,.art-status-trashed::before{background:#aaa}
.action-cell{display:flex;align-items:center;justify-content:flex-end;gap:6px}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:1px solid #e4e7f2;background:#fff;color:#555e7a;font-size:14px;cursor:pointer;transition:.15s;text-decoration:none}
.btn-icon:hover{border-color:#1a56db;color:#1a56db;background:#dbeafe}
.btn-icon.danger:hover{border-color:#e04a4a;color:#e04a4a;background:#fff0f0}
.btn-icon.success:hover{border-color:#28a76a;color:#28a76a;background:#e2f7ed}
.art-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:72px 24px;text-align:center}
.art-empty-icon{display:grid;width:60px;height:60px;place-items:center;border-radius:18px;background:#dbeafe;color:#1a56db;font-size:30px;margin:0 auto 16px}
.flash-success{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
.flash-error{display:flex;align-items:center;gap:10px;max-width:1200px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#fce8e8;color:#962a2a;font-size:14px;font-weight:600}
.pagination-wrap{display:flex;justify-content:center;padding:20px;border-top:1px solid #eff6ff}
</style>

<div class="dashboard art-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small>Articles &amp; Blog</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error">✗ {{ session('error') }}</div>@endif

    <div class="art-hero">
        <div>
            <span class="art-overline">Content · Articles &amp; Blog</span>
            <h1>Articles &amp; Blog</h1>
            <p>Create and manage articles and blog posts displayed in the mobile app. Control categories, status, and scheduling.</p>
        </div>
        @can('articles.create')
        <a href="{{ route('admin.articles.create') }}" class="art-add-btn">＋ New Article</a>
        @endcan
    </div>

    @php
        use App\Models\Article;
        $totalArt     = Article::withTrashed()->count();
        $publishedArt = Article::where('status','published')->count();
        $draftArt     = Article::where('status','draft')->count();
        $archivedArt  = Article::onlyTrashed()->count();
    @endphp
    <div class="art-stats">
        <div class="art-stat"><div class="art-stat-icon all">📰</div><div><strong>{{ $totalArt }}</strong><small>Total</small></div></div>
        <div class="art-stat"><div class="art-stat-icon pub">●</div><div><strong>{{ $publishedArt }}</strong><small>Published</small></div></div>
        <div class="art-stat"><div class="art-stat-icon draft">●</div><div><strong>{{ $draftArt }}</strong><small>Draft</small></div></div>
        <div class="art-stat"><div class="art-stat-icon arch">●</div><div><strong>{{ $archivedArt }}</strong><small>Archived</small></div></div>
    </div>

    <form method="GET" action="{{ route('admin.articles.index') }}" class="art-toolbar">
        <div class="art-search">
            <span class="art-search-icon">⌕</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search title or excerpt…" autocomplete="off">
        </div>
        <div class="art-filter">
            <select name="category" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach($categories as $c)
                <option value="{{ $c }}" @selected(request('category') === $c)>{{ ucfirst($c) }}</option>
                @endforeach
            </select>
        </div>
        <div class="art-filter">
            <select name="status" onchange="this.form.submit()">
                <option value="">All status</option>
                <option value="published" @selected(request('status') === 'published')>Published</option>
                <option value="draft"     @selected(request('status') === 'draft')>Draft</option>
                <option value="archived"  @selected(request('status') === 'archived')>Archived</option>
                <option value="trashed"   @selected(request('status') === 'trashed')>Deleted</option>
            </select>
        </div>
        <button type="submit" style="width:auto;margin:0;padding:10px 16px;border-radius:10px;background:#1a56db;color:#fff;border:0;cursor:pointer;font:700 13px inherit">Search</button>
        @if(request()->hasAny(['search','category','status']))
        <a href="{{ route('admin.articles.index') }}" style="padding:10px 14px;border-radius:10px;border:1px solid #d1e4f6;color:#1e40af;font-size:13px;font-weight:600;text-decoration:none;background:#fff">Clear</a>
        @endif
    </form>

    <div class="art-board">
        @if($articles->isEmpty())
        <div class="art-empty">
            <div class="art-empty-icon">📰</div>
            <h3>No articles yet</h3>
            <p>Create your first article or blog post to show in the mobile app.</p>
            @can('articles.create')
            <a href="{{ route('admin.articles.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:11px 18px;border-radius:10px;color:#fff;background:#1a56db;font-size:13px;font-weight:700;text-decoration:none;margin-top:4px">＋ New Article</a>
            @endcan
        </div>
        @else
        <table class="art-table">
            <thead>
                <tr>
                    <th style="width:70px">Cover</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($articles as $a)
            <tr style="{{ $a->trashed() ? 'opacity:.55' : '' }}">
                <td>
                    <div class="art-thumb">
                        @if($a->cover_image)
                        <img src="{{ Storage::url($a->cover_image) }}" alt="">
                        @else
                        📰
                        @endif
                    </div>
                </td>
                <td>
                    <div class="art-title">
                        <b>{{ $a->title }}</b>
                        @if($a->excerpt)<span>{{ Str::limit($a->excerpt, 70) }}</span>@endif
                    </div>
                </td>
                <td><span class="art-chip art-cat">{{ ucfirst($a->category) }}</span></td>
                <td style="font-size:13px">{{ $a->author?->name ?? '—' }}</td>
                <td>
                    @if($a->trashed())
                        <span class="art-status-trashed">Deleted</span>
                    @else
                        <span class="art-status-{{ $a->status }}">{{ ucfirst($a->status) }}</span>
                    @endif
                </td>
                <td style="font-size:12px;color:#6b7a9a;white-space:nowrap">
                    {{ $a->published_at?->format('d M Y') ?? '—' }}
                </td>
                <td>
                    <div class="action-cell">
                        @if($a->trashed())
                            @can('articles.restore')
                            <form method="POST" action="{{ route('admin.articles.restore', $a->id) }}" style="display:inline">
                                @csrf <button class="btn-icon success" title="Restore">↩</button>
                            </form>
                            @endcan
                        @else
                            @can('articles.view')
                            <a href="{{ route('admin.articles.show', $a) }}" class="btn-icon" title="View">👁</a>
                            @endcan
                            @can('articles.update')
                            <a href="{{ route('admin.articles.edit', $a) }}" class="btn-icon" title="Edit">✎</a>
                            @endcan
                            @can('articles.delete')
                            <form method="POST" action="{{ route('admin.articles.destroy', $a) }}" style="display:inline" onsubmit="return confirm('Archive this article?')">
                                @csrf @method('DELETE')
                                <button class="btn-icon danger" title="Archive">🗑</button>
                            </form>
                            @endcan
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @if($articles->hasPages())
        <div class="pagination-wrap">{{ $articles->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection
