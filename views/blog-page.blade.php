@extends('layouts.blog')

@section('content')
    @include('includes.blog-nav')

    <div class="w-80 fw6 lh-title center sans-serif">
        <a href="/blog" class="link black">< Blog</a>
    </div>

    <div class="f3 pv4 fw6 measure tc lh-title center sans-serif">
        {{ $post->title }}
    </div>

    <div class="f4 measure tc lh-title center h3 sans-serif">
        {{ $post->author->name }} - {{ $post->date }}
    </div>

    <article>
        <div class="lh-copy measure-wide center pt2 ph2 pb5 serif" style="min-height: 80vh;">
            {!! $post->content !!}
        </div>
    </article>

    <div class="w-80 center">
        <div class="lh-title center" style="max-width: 700px; padding: 10px">Get updates</div>
        <div class="lh-title center" style="max-width: 700px; padding: 10px">
            <iframe src="/newsletter/subscribe" class="bn w-100 h5"></iframe>
        </div>
    </div>
@endsection
