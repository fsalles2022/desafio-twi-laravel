import express from "express";
import cors from "cors";
import fs from "fs";
import path from "path";

const app = express();
app.use(cors());

const PORT = 4000;
const VIDEOS_DIR = path.resolve("./videos");

// Endpoint que retorna a lista de vídeos
app.get("/videos", (req, res) => {
  const files = fs.readdirSync(VIDEOS_DIR);
  const videos = files.map(f => ({ name: f }));
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
    res.writeHead(200, { "Content-Length": fileSize, "Content-Type": "video/mp4" });
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

app.listen(PORT, () => console.log(`🎥 Video service running at http://localhost:${PORT}`));
