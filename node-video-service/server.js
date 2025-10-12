import express from "express";
import cors from "cors";
import fs from "fs";
import path from "path";

const app = express();
app.use(cors());

const PORT = 4000;
const VIDEOS_DIR = path.resolve("./videos");

// Página HTML simples para teste
app.get("/", (req, res) => {
  const files = fs.existsSync(VIDEOS_DIR)
    ? fs.readdirSync(VIDEOS_DIR).filter(f => f.endsWith(".mp4"))
    : [];

  const videoList = files
    .map(f => `<li><a href="/stream/${f}" target="_blank">${f}</a></li>`)
    .join("");

  res.send(`
    <html>
      <head>
        <meta charset="utf-8"/>
        <title>Teste de Vídeo Service</title>
        <style>
          body { font-family: Arial; padding: 20px; }
          video { width: 720px; display: block; margin-bottom: 20px; }
        </style>
      </head>
      <body>
        <h1>🎬 Vídeo Service (porta ${PORT})</h1>
        <p>Vídeos disponíveis na pasta <code>/videos</code>:</p>
        <ul>${videoList || "<li>Nenhum vídeo encontrado.</li>"}</ul>

        ${
          files.length > 0
            ? `<video controls>
                 <source src="/stream/${files[0]}" type="video/mp4">
                 Seu navegador não suporta vídeo.
               </video>`
            : ""
        }
      </body>
    </html>
  `);
});

// Endpoint para listar vídeos (JSON)
app.get("/videos", (req, res) => {
  if (!fs.existsSync(VIDEOS_DIR)) return res.json([]);
  const files = fs.readdirSync(VIDEOS_DIR).filter(f => f.endsWith(".mp4"));
  const videos = files.map(f => ({
    name: f,
    url: `http://localhost:${PORT}/stream/${f}`,
  }));
  res.json(videos);
});

// Endpoint de streaming
app.get("/stream/:filename", (req, res) => {
  const filePath = path.join(VIDEOS_DIR, req.params.filename);
  if (!fs.existsSync(filePath)) return res.status(404).send("Vídeo não encontrado");

  const stat = fs.statSync(filePath);
  const fileSize = stat.size;
  const range = req.headers.range;

  if (!range) {
    res.writeHead(200, { 
      "Content-Length": fileSize,
      "Content-Type": "video/mp4"
    });
    fs.createReadStream(filePath).pipe(res);
  } else {
    const parts = range.replace(/bytes=/, "").split("-");
    const start = parseInt(parts[0], 10);
    const end = parts[1] ? parseInt(parts[1], 10) : fileSize - 1;
    const chunkSize = end - start + 1;

    const head = {
      "Content-Range": `bytes ${start}-${end}/${fileSize}`,
      "Accept-Ranges": "bytes",
      "Content-Length": chunkSize,
      "Content-Type": "video/mp4"
    };
    res.writeHead(206, head);
    fs.createReadStream(filePath, { start, end }).pipe(res);
  }
});

app.listen(PORT, () => {
  console.log(`🎥 Video service running at http://localhost:${PORT}`);
});
