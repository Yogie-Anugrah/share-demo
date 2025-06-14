<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Share Page Demo</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .platform-btn { width: 100%; margin-bottom: 20px; font-size: 1.1rem; }
        .preview-img { max-width: 100%; border-radius: 12px; margin-bottom: 12px; }
        .modal-title { font-size: 1.3rem; }
        .modal-body { font-size: 1.05rem; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="text-center mb-4">Share & Discover</h2>
    <div class="mb-4 text-center">
        <span class="badge text-bg-info px-3 py-2">Token: <span id="token-text">{{ $token }}</span></span>
    </div>

    <div class="row">
        @foreach($platforms as $p)
        <div class="col-md-6 mb-3">
            <button class="btn btn-primary platform-btn"
                    data-api="{{ $p['api'] }}"
                    data-key="{{ $p['key'] }}"
                    data-name="{{ $p['name'] }}">
                {{ $p['name'] }}
            </button>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal for Red Note/Xiaohongshu Preview -->
<div class="modal fade" id="rednoteModal" tabindex="-1" aria-labelledby="rednoteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rednoteModalLabel">Share to Red Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="rednote-modal-body">
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p>Loading...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="share-rednote-btn" class="btn btn-success" style="display:none;">Share Now</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- JQuery + Bootstrap Bundle -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
    let token = $('#token-text').text().trim();
    let redNoteUrl = '';

    $('.platform-btn').on('click', function() {
        let api = $(this).data('api');
        let key = $(this).data('key');
        let name = $(this).data('name');

        // Red Note/Xiaohongshu: Show modal, preview, then share
        // if(key === 'rednote') {
        //     $('#rednote-modal-body').html(
        //         `<div class="text-center py-4">
        //             <div class="spinner-border text-primary" role="status"></div>
        //             <p>Loading...</p>
        //         </div>`
        //     );
        //     $('#share-rednote-btn').hide();

        //     $.ajax({
        //         url: api,
        //         headers: { 'store-token': token },
        //         success: function(res) {
        //             if(res.code !== 200 || !res.data || !res.data.info){
        //                 $('#rednote-modal-body').html('<div class="alert alert-danger">Failed to load content.</div>');
        //                 return;
        //             }
        //             let info = res.data.info;
        //             let config = res.data.config || {};
        //             redNoteUrl = `https://www.xiaohongshu.com/explore/${info.id}?token=${token}`;
        //             let html = '';
        //             if(info.image)
        //                 html += `<img src="${info.image}" class="preview-img mb-2" alt="cover">`;
        //             if(info.title)
        //                 html += `<h5 class="mb-2">${info.title}</h5>`;
        //             if(info.content)
        //                 html += `<div class="mb-2" style="white-space:pre-line;">${info.content}</div>`;
        //             if(config.shareInfo && config.shareInfo.images && config.shareInfo.images.length) {
        //                 html += `<div class="mb-2">Images:<br>`;
        //                 config.shareInfo.images.forEach(img =>
        //                     html += `<img src="${img}" class="preview-img mb-1" style="max-height:110px;">`
        //                 );
        //                 html += `</div>`;
        //             }
        //             $('#rednote-modal-body').html(html);
        //             $('#share-rednote-btn').show().off('click').on('click', function() {
        //                 window.open(redNoteUrl, '_blank');
        //                 $('#rednoteModal').modal('hide');
        //             });
        //         },
        //         error: function() {
        //             $('#rednote-modal-body').html('<div class="alert alert-danger">Failed to load content.</div>');
        //         }
        //     });

        //     let modal = new bootstrap.Modal(document.getElementById('rednoteModal'));
        //     modal.show();

        // } else {
        //     // Platform lain: langsung fetch API lalu redirect ke url dari API
        //     $.ajax({
        //         url: api,
        //         headers: { 'store-token': token },
        //         success: function(res) {
        //             if(res.code !== 200 || !res.data || !res.data.info){
        //                 alert('Failed to fetch share URL.');
        //                 return;
        //             }
        //             let url = res.data.info.url;
        //             if(!url) {
        //                 alert('No share URL available for this platform.');
        //                 return;
        //             }
        //             window.open(url, '_blank');
        //         },
        //         error: function() {
        //             alert('Error fetching share URL.');
        //         }
        //     });
        // }

        if(key === 'rednote') {
            // Ganti jadi ambil API → redirect ke note-view page
            $.ajax({
                url: api,
                headers: { 'store-token': token },
                success: function(res) {
                    if(res.code !== 200 || !res.data || !res.data.info){
                        alert('Failed to load Red Note content.');
                        return;
                    }

                    const info = res.data.info;
                    let config = res.data.config || {};
                    const title = encodeURIComponent(info.title_format || 'Untitled');
                    const content = encodeURIComponent(info.content || '');
                    const image = encodeURIComponent(info.image || '');
                    const images = encodeURIComponent((config.shareInfo?.images || []).join(','));

                    const fullPageUrl = `/note-view?title=${title}&content=${content}&image=${image}&images=${images}`;
                    window.open(fullPageUrl, '_blank');
                },
                error: function() {
                    alert('Failed to load Red Note content.');
                }
            });

            return;
        }

    });
});
</script>
</body>
</html>


{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shared Notes by Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: sans-serif;
            max-width: 768px;
            margin: auto;
            padding: 2rem;
            background: #f8f8f8;
        }
        .platform {
            background: white;
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }
        .platform h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        .platform p {
            font-size: 0.95rem;
            color: #555;
        }
        .open-button {
            background: #2563eb;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<h1>Shared Notes by Platform</h1>

@foreach ($platforms as $p)
    @php
        $encodedTitle = urlencode($p['title']);
        $encodedContent = urlencode($p['content']);
    @endphp

    <div class="platform">
        <h2>{{ $p['name'] }}</h2>
        <p>{{ Str::limit(strip_tags($p['content']), 150) }}</p>
        <a class="open-button"
           href="/note-view?title={{ $encodedTitle }}&content={{ $encodedContent }}"
           target="_blank">
            Open Full Page
        </a>
    </div>
@endforeach

</body>
</html> --}}
