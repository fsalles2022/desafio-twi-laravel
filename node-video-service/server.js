import express from "express";
import cors from "cors";
import multer from "multer";
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
app.use(cors());
app.use(express.json());

// Pasta onde os vídeos serão armazenados
const uploadPath = path.join(__dirname, "uploads");
if (!fs.existsSync(uploadPath)) {
    fs.mkdirSync(uploadPath, { recursive: true });
}

// Configuração do multer (upload de vídeo)
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, uploadPath);
    },
    filename: (req, file, cb) => {
        cb(null, file.originalname);
    },
});
const upload = multer({ storage });

// ========================
// 📤 Upload de vídeo
// ========================


// ========================
// 🎥 Stream de vídeo
// ========================
app.get("/stream/:filename", (req, res) => {
    const filePath = path.join(uploadPath, req.params.filename);

    if (!fs.existsSync(filePath)) {
        return res.status(404).json({ error: "Vídeo não encontrado" });
    }

    const stat = fs.statSync(filePath);
    const fileSize = stat.size;
    const range = req.headers.range;

    if (range) {
        const parts = range.replace(/bytes=/, "").split("-");
        const start = parseInt(parts[0], 10);
        const end = parts[1] ? parseInt(parts[1], 10) : fileSize - 1;

        const chunkSize = end - start + 1;
        const file = fs.createReadStream(filePath, { start, end });
        const head = {
            "Content-Range": `bytes ${start}-${end}/${fileSize}`,
            "Accept-Ranges": "bytes",
            "Content-Length": chunkSize,
            "Content-Type": "video/mp4",
        };

        res.writeHead(206, head);
        file.pipe(res);
    } else {
        const head = {
            "Content-Length": fileSize,
            "Content-Type": "video/mp4",
        };
        res.writeHead(200, head);
        fs.createReadStream(filePath).pipe(res);
    }
});

// ========================
// 🚀 Iniciar servidor
// ========================
const PORT = 4000;
// ========================
// 📤 Upload de vídeo
// ========================
app.post("/upload", upload.single("file"), (req, res) => {
    if (!req.file) {
        return res.status(400).json({ error: "Nenhum arquivo enviado" });
    }

    console.log("✅ Vídeo recebido:", req.file.originalname);

    res.json({
        message: "Upload realizado com sucesso",
        filename: req.file.filename,
        path: `/uploads/${req.file.filename}`,
    });
});

app.listen(PORT, () => {
    console.log(`✅ Node Video Service rodando em http://localhost:${PORT}`);
});
