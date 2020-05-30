@extends('layouts.marketing')

@section('content')
    <div class="w-80-l w-90 center">
        <div class="w-80 fw6 lh-title center sans-serif">
            <a href="/" class="link black">< Home</a>
        </div>

        <div class="f3 pv4 fw6 measure tc lh-title center sans-serif">
            {{ $post->title }}
        </div>

        <div class="f4 measure tc lh-title center h3 sans-serif">
            {{ $post->date }}
        </div>

        <article>
            <div class="lh-copy measure-wide center pt2 ph2 pb5 serif" style="min-height: 80vh;">
                {!! $post->content !!}
            </div>
        </article>

        <div class="w-80-l w-95 center">
            <div class="lh-title center" style="max-width: 700px; padding: 10px">Get updates</div>
            <div class="lh-title center" style="max-width: 700px; padding: 10px">
                <iframe src="/newsletter/subscribe" class="bn w-100 h5"></iframe>
            </div>
        </div>
    </div>

    <script>
      $(function () {
        lolight('pre');
        const images = document.querySelectorAll('img');
        mediumZoom(images);
      });
    </script>
@endsection

@section("css")
    <style>
        p {
            line-height: 1.5rem;
        }
        code {
            font-family: Menlo,monospace;
            font-size: .875rem;
            padding: 0 6px;
            border: 1px solid #d6e0ef;
            background: #f2f6fa;
            border-radius: 5px;
        }
    </style>
@endsection
