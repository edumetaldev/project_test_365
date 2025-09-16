import { createClient } from "redis";
import { Server } from "socket.io";
import http from "http";

const server = http.createServer();
const io = new Server(server, { cors: { origin: "*" } });

const redis = createClient({ url: "redis://127.0.0.1:6379" });

redis.on("connect", () => console.log("🔌 Conectando a Redis..."));
redis.on("ready", () => console.log("✅ Redis listo!"));
redis.on("error", (err) => console.error("❌ Redis error:", err));

await redis.connect();

await redis.pSubscribe("*", (message, channel) => {
  console.log("📩 Canal:", channel);
  console.log("   Mensaje bruto:", message);

  try {
    const payload = JSON.parse(message);

    console.log("   ➡️ Evento:", payload.event);
    console.log("   ➡️ Data:");
    console.table(payload.data);

    io.emit(payload.event, payload.data);
  } catch (e) {
    console.error("⚠️ Error parseando mensaje:", e.message);
  }
});

io.on("connection", () => console.log("🟢 Cliente conectado al WS"));

server.listen(3000, () => console.log("🌐 WebSocket escuchando en :3000"));
