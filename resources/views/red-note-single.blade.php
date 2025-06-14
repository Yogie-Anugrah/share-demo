{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Red Note' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: system-ui, sans-serif;
            max-width: 720px;
            margin: 2rem auto;
            padding: 1rem;
            line-height: 1.7;
            color: #222;
        }
        h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .content {
            white-space: pre-wrap;
            margin-top: 1rem;
        }
        .copy-button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <h1>{{ $title }}</h1>

    <button class="copy-button" onclick="copyToClipboard()">📋 Copy to Clipboard</button>

    <div id="content" class="content">
        {!! $content !!}
    </div>

    <script>
        function copyToClipboard() {
            const el = document.createElement("textarea");
            el.value = document.getElementById("content").innerText;
            document.body.appendChild(el);
            el.select();
            document.execCommand("copy");
            document.body.removeChild(el);
            alert("Copied to clipboard!");
        }
    </script>
</body>
</html> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 2rem; font-family: sans-serif; }
        .preview-img { max-width: 100%; border-radius: 12px; margin-bottom: 12px; }
        .copy-btn { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-3">{{ $title }}</h2>

        @if(request()->has('image'))
            <img src="{{ request('image') }}" class="preview-img mb-3" alt="cover">
        @endif

        <div id="content-block" class="mb-3" style="white-space:pre-line;">{!! $content !!}</div>

        @if(request()->has('images'))
            @php
                $images = explode(',', request('images'));
            @endphp
            <div class="mb-3">
                <strong>Images:</strong><br>
                @foreach($images as $img)
                    <img src="{{ $img }}" class="preview-img mb-1" style="max-height:110px;">
                @endforeach
            </div>
        @endif

        <button class="btn btn-outline-primary copy-btn" onclick="copyToClipboard()">Copy Content</button>
    </div>

    <script>
        function copyToClipboard() {
            const text = document.getElementById('content-block').innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert("Content copied to clipboard!");
            });
        }
    </script>
</body>
</html>
