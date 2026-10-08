@extends('layouts.app')
@section('title', $article->title)
@section('content')
<style>
.art-show-page{background:radial-gradient(circle at 92% 4%,#e8f4ff 0,transparent 22%),#f5f9ff!important}
.art-show-hero{display:flex;align-items:flex-start;justify-content:space-between;gap:24px;max-width:960px;margin:35px auto 24px;padding:32px 40px;border-radius:20px;color:#fff;background:radial-gradient(circle at 86% 10%,#60a5fa 0,transparent 26%),linear-gradient(118deg,#0c2461,#1a56db)}
.art-show-hero h1{margin:8px 0 6px;font-size:26px;color:#fff;line-height:1.3}
.art-show-hero p{margin:0;color:#bfdbfe;font-size:14px;line-height:1.5}
.art-overline{color:#fde68a;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.art-show-meta{display:flex;flex-wrap:wrap;gap:10px;margin-top:14px;align-items:center}
.art-meta-chip{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700}
.art-meta-chip.cat{color:#1e40af;background:#dbeafe}
.art-meta-chip.published{color:#1e6b47;background:#e2f7ed}
.art-meta-chip.draft{color:#7a5a10;background:#fff3d2}
.art-meta-chip.archived{color:#555;background:#eee}
.art-show-card{max-width:960px;margin:0 auto;border:1px solid #dbeafe;border-radius:20px;background:#fff;box-shadow:0 18px 45px #0c246109;overflow:hidden}
.art-show-cover{width:100%;max-height:340px;object-fit:cover}
.art-show-body{padding:32px}
.art-show-body h2{font-size:13px;font-weight:800;color:#1e40af;letter-spacing:.07em;text-transform:uppercase;margin:0 0 10px}
.art-show-content{font-size:15px;color:#2a3050;line-height:1.8;white-space:pre-wrap}
.art-show-excerpt{font-size:14px;color:#4a556a;line-height:1.7;font-style:italic;padding:14px 18px;border-left:3px solid #60a5fa;background:#f0f7ff;border-radius:0 8px 8px 0;margin-bottom:24px}
.art-show-actions{display:flex;gap:10px;padding:22px 32px;background:#f8faff;border-top:1px solid #eff6ff;flex-wrap:wrap}
.art-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:10px;font:600 13px inherit;text-decoration:none;cursor:pointer;border:0;transition:.15s}
.art-btn-primary{color:#fff;background:#1a56db}
.art-btn-primary:hover{opacity:.9}
.art-btn-outline{color:#1e40af;background:#fff;border:1px solid #d1e4f6}
.art-btn-outline:hover{background:#f0f7ff}
.art-btn-danger{color:#e04a4a;background:#fff;border:1px solid #fca5a5}
.art-btn-danger:hover{background:#fff0f0}
.art-btn-success{color:#1e6b47;background:#e2f7ed;border:1px solid #a7f3d0}
.art-btn-success:hover{background:#d1fae5}
.art-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:16px}
.art-tag{padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;color:#1e40af;background:#eff6ff;border:1px solid #bfdbfe}
.flash-success{display:flex;align-items:center;gap:10px;max-width:960px;margin:0 auto 16px;padding:13px 16px;border-radius:12px;background:#e2f7ed;color:#1e5e42;font-size:14px;font-weight:600}
</style>

<div class="dashboard art-show-page">
    <div class="topbar">
        <div class="page-heading"><span>ARK Jyotish</span><small><a href="{{ route('admin.articles.index') }}" style="color:inherit">Articles</a> / View</small></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
    </div>

    @if(session('success'))<div class="flash-success">✓ {{ session('success') }}</div>@endif

    <div class="art-show-hero">
        <div style="flex:1">
            <span class="art-overline">Articles · Detail</span>
            <h1>{{ $article->title }}</h1>
            @if($article->excerpt)<p>{{ $article->excerpt }}</p>@endif
            <div class="art-show-meta">
                <span class="art-meta-chip cat">{{ ucfirst($article->category) }}</span>
                <span class="art-meta-chip {{ $article->status }}">{{ ucfirst($article->status) }}</span>
                @if($article->author)<span style="color:#bfdbfe;font-size:13px">By {{ $article->author->name }}</span>@endif
                @if($article->published_at)<span style="color:#bfdbfe;font-size:13px">{{ $article->published_at->format('d M Y') }}</span>@endif
            </div>
        </div>
        <div style="font-size:48px">📰</div>
    </div>

    <div class="art-show-card">
        @if($article->cover_image)
        <img src="{{ Storage::url($article->cover_image) }}" class="art-show-cover" alt="{{ $article->title }}">
        @endif

        <div class="art-show-body">
            @if($article->excerpt)
            <div class="art-show-excerpt">{{ $article->excerpt }}</div>
            @endif

            <h2>Article Body</h2>
            <div class="art-show-content">{{ $article->content }}</div>

            @if($article->tags)
            <div class="art-tags">
                @foreach($article->tags as $tag)
                <span class="art-tag"># {{ $tag }}</span>
                @endforeach
            </div>
            @endif

            @if($article->translations && isset($article->translations['hi']))
            <div style="margin-top:28px;padding-top:24px;border-top:1px solid #eff6ff">
                <h2>Hindi Translation</h2>
                @if(!empty($article->translations['hi']['title']))
                <p style="font-size:15px;font-weight:700;color:#1b2240;margin:0 0 8px">{{ $article->translations['hi']['title'] }}</p>
                @endif
                @if(!empty($article->translations['hi']['excerpt']))
                <div class="art-show-excerpt">{{ $article->translations['hi']['excerpt'] }}</div>
                @endif
                @if(!empty($article->translations['hi']['content']))
                <div class="art-show-content">{{ $article->translations['hi']['content'] }}</div>
                @endif
            </div>
            @endif
        </div>

        <div class="art-show-actions">
            @can('articles.update')
            <a href="{{ route('admin.articles.edit', $article) }}" class="art-btn art-btn-primary">✎ Edit</a>
            @endcan
            @can('articles.delete')
            <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Archive this article?')">
                @csrf @method('DELETE')
                <button class="art-btn art-btn-danger">🗑 Archive</button>
            </form>
            @endcan
            <a href="{{ route('admin.articles.index') }}" class="art-btn art-btn-outline">← Back to list</a>
        </div>
    </div>
</div>
@endsection
