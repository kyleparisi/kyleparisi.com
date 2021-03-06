@extends('layouts.marketing')

@section('content')
    <div class="pt3 w-90 center">
        <div class="pl4">
            <a href="/components/table">Back</a>
        </div>
        <form method="post" style="min-height: 80vh;">
            <div class="pl4">Doc Editor</div>
            <div class="w-80 f4 lh-copy center pt5">
                <div class="pb3">
                    <input type="text" title="title" name="title" placeholder="Title"
                           value="{{ $note->title ?? "" }}"
                           class="input-reset ba b--black-30 pa2 b0 w-100">
                </div>
                <input id="x" type="hidden" name="content">
            </div>
            <div class="flex" style="min-height: 300px">
                <div class="pa3 w-50">
                    <label class="dn" for="content">Content</label>
                    <textarea id="content" name="content" class="w-100 h5 pa3">{{ $note->content ?? "" }}</textarea>
                </div>
                <div class="pa3 w-50">
                    <div id="preview"></div>
                </div>
            </div>
            <div class="w-80 f4 lh-copy center pv5">
                <div class="pv3 tr">
                    <button class="button">Post</button>
                </div>
            </div>
        </form>
    </div>

    <script>
      const element = document.getElementById("content");
      const preview = document.getElementById("preview");

      function save(event) {
        if (event) {
          event.preventDefault();
        }
        $.post("/parser/preview", {content: element.value}, body => {
          preview.innerHTML = body;
          lolight('pre');
        });
      }
      save();

      Mousetrap(element).bind('command+s', save);
      Mousetrap.bind('command+s', save);
      autosize(element);

      $(document).delegate('#content', 'keydown', function(e) {
        var keyCode = e.keyCode || e.which;

        if (keyCode == 9) {
          e.preventDefault();
          var start = this.selectionStart;
          var end = this.selectionEnd;

          // set textarea value to: text before caret + tab + text after caret
          $(this).val($(this).val().substring(0, start)
            + "\t"
            + $(this).val().substring(end));

          // put caret at right position again
          this.selectionStart =
            this.selectionEnd = start + 1;
        }
      });
    </script>
@endsection
