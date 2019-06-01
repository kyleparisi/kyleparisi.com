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

    <article class="cf pa3">
        <div class="fl lh-copy w-50 pt2 ph2 pb5 serif" style="min-height: 80vh;">
            {!! $note->content ?? "" !!}
        </div>
        <div class="relative fr w-50" style="min-height: 2138px">
            <div class="sticky" id="chat">
                <div class="pa3 w-100 h-100 flex" style="min-height: calc(100vh - 70px)">
                    <iframe class="w-100 ba b--light-gray" src="/chat.html"></iframe>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Faker/3.1.0/faker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://vue.kyleparisi.com/heffer.umd.min.js"></script>
    <script>
      $(function () {
        lolight('pre');
        new Vue({el: "#chat"});
      });

      function randomMessage() {
        return faker.lorem.words(
          faker.random.number({
            min: 1,
            max: 15
          })
        );
      }
      function fakeChat(key) {
        return {
          title: faker.random.words(5),
          image_url: faker.image.imageUrl(),
          owner: "1",
          read: "0",
          key,
          users: {
            "1": {
              id: "1",
              name: faker.name.findName(),
              image_url: faker.image.imageUrl()
            },
            "2": {
              id: "2",
              name: faker.name.findName(),
              image_url: faker.image.imageUrl()
            }
          },
          messages: []
        };
      }
    </script>
@endsection
