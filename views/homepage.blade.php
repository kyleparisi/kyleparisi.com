@extends('layouts.marketing')

@section('content')
    <div class="w-80-l w-90 center">
        <h1>Kyle Parisi <span class="f4 fw3 v-mid">- Mechanical engineer turned software developer</span></h1>
        <div class="cf">
            <div class="fl w-third-l">
                <div class="pb3 underline">Blog Posts</div>

                <div class="georgia">
                    @foreach($posts as $post)
                        <a class="link" href="/blog/{{ $post->slug }}">
                            <div class="pb3">{{ $post->title }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="fl w-third-l">
                <div class="lh-title tracked underline pb3">Components</div>

                <div class="georgia">
                    <a class="link" href="/components/chat">
                        <div class="pb2">Chat</div>
                    </a>
                </div>

                <div class="pt4 lh-title tracked underline pb3">Social</div>

                <div class="georgia">
                    <a class="link" href="https://twitter.com/kyleparisi">
                        <div class="pb2">Twitter</div>
                    </a>
                    <a class="link" href="https://github.com/kyleparisi">
                        <div class="pb2">Github</div>
                    </a>
                    <a class="link" href="https://www.linkedin.com/in/kyleparisi">
                        <div class="pb2">Linkedin</div>
                    </a>
                </div>

                <div class="pt4 lh-title tracked underline pb3">Life Missions</div>

                <div class="georgia">
                    <a class="link" href="https://buildapart.io">
                        <div class="pb2">Buildapart</div>
                    </a>
                    <a class="link" href="https://fluxion.app">
                        <div class="pb2">Fluxion</div>
                    </a>
                    <div class="pb2">Energy...</div>
                </div>
            </div>
        </div>
    </div>
@endsection