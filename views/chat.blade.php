@extends('layouts.marketing')

@section('css')
    <link rel="stylesheet" href="https://vue.kyleparisi.com/heffer.css">
@endsection

@section('content')
    <div class="w-80 fw6 lh-title center sans-serif">
        <a href="/" class="link black">< Home</a>
    </div>

    <div class="f3 pv4 fw6 measure tc lh-title center sans-serif">
        Chat Component
    </div>

    <article class="pa3">
        <div class="lh-copy w-50 pt2 ph2 pb5 serif" style="min-height: 80vh;">
            {!! $note->content ?? "" !!}
        </div>
        <div class="fixed right-0 top-0 w-50" id="chat" style="padding-top: 178px">
            <div class="pa3">
              <div id="chat">
                  <Chat></Chat>
              </div>
            </div>
        </div>
    </article>

    <div class="w-80-l w-95 center">
        <div class="lh-title center" style="max-width: 700px; padding: 10px">Get updates</div>
        <div class="lh-title center" style="max-width: 700px; padding: 10px">
            <iframe src="/newsletter/subscribe" class="bn w-100 h5"></iframe>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://vue.kyleparisi.com/heffer.umd.min.js"></script>
    <script>
      $(function () {
        lolight('pre');
        new Vue({el: "#chat"});
      });
    </script>
@endsection
