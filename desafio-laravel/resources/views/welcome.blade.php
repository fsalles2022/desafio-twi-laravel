<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAD - Vídeos</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; }
        .video-card { margin-bottom: 30px; }
    </style>
</head>
<body>
    <h1>🎬 Lista de Vídeos</h1>
    <div id="videos">
        Carregando vídeos...
    </div>

    <script>
        const API_URL = 'http://localhost:4000/videos';

        async function loadVideos() {
            const container = document.getElementById('videos');
            try {
                const res = await fetch(API_URL);
                const videos = await res.json();

                if (!videos || videos.length === 0) {
                    container.innerHTML = '<p>Nenhum vídeo disponível.</p>';
                    return;
                }

                container.innerHTML = '';
                videos.forEach(video => {
                    const div = document.createElement('div');
                    div.classList.add('video-card');
                    div.innerHTML = `
                        <h3>${video.name}</h3>
                        <video width="720" controls>
                            <source src="${video.url}" type="video/mp4">
                            Seu navegador não suporta vídeo.
                        </video>
                    `;
                    container.appendChild(div);
                });
            } catch (err) {
                console.error(err);
                container.innerHTML = '<p>Erro ao carregar vídeos.</p>';
            }
        }

        loadVideos();
    </script>
</body>
</html>
