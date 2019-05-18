@extends('layouts.marketing')

@section('content')
    @include('includes.blog-nav')

    <div style="min-height: 80vh;">
        @foreach($posts as $post)
            <a href="/blog/{{ $post->slug }}" class="link black dim">
                <div class="pv4 measure tc lh-title center h3 sans-serif">
                    <span class="fw5">{{ $post->title }}</span> - <span
                            class="fw1">{{ $post->author->name }} - {{ $post->date }}</span>
                </div>
            </a>
        @endforeach
    </div>


@endsection
