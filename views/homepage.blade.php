@extends('layouts.marketing')

@section('content')
    <div class="cf">
        <div class="fl w-third">
            <div class="pb3">Blog Posts...</div>

            <div class="georgia">
                @foreach($posts as $post)
                    <a class="link" href="/blog/{{ $post->slug }}">
                        <div class="pb3">{{ $post->title }}</div>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="fl w-third">
            <div class="lh-title tracked">Social</div>

            <div class="georgia">
                <a class="link" href="https://twitter.com/kyleparisi">
                    <div class="">Twitter</div>
                </a>
                <a class="link" href="https://github.com/kyleparisi">
                    <div class="">Github</div>
                </a>
                <a class="link" href="https://www.linkedin.com/in/kyleparisi">
                    <div class="">Linkedin</div>
                </a>
            </div>

            <div class="pt4 lh-title tracked">Life Missions</div>

            <div class="georgia">
                <a class="link" href="https://buildapart.io">
                    <div class="">Buildapart</div>
                </a>
                <a class="link" href="https://fluxion.app">
                    <div class="">Fluxion</div>
                </a>
                <div class="">Energy...</div>
            </div>
        </div>
    </div>
@endsection