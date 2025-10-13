<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAD - Vídeos</title>
</head>
<body>
    <h1>🎬 Lista de Vídeos EAD</h1>

    @if(count($videos) > 0)
        @foreach($videos as $video)
            <div style="margin-bottom: 30px;">
                <h3>{{ $video['name'] }}</h3>
                <video width="720" height="360" controls>
                    <source src="{{ $video['url'] }}" type="video/mp4">
                    Seu navegador não suporta vídeo.
                </video>
            </div>
        @endforeach
    @else
        <p>Nenhum vídeo disponível.</p>
    @endif
</body>
</html>
