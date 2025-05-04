@extends('frontend.layouts.main')

@section('content')

    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>


    <form action="" method="POST">
        @csrf
        <textarea name="content" id="editor"></textarea>
        <button type="submit">Saqlash</button>
    </form>

    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>

@endsection
