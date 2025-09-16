import { io } from "socket.io-client";

const socket = io("http://127.0.0.1:3000");

socket.on("reservation.updated", (data) => {
  console.log("Reserva actualizada:", data);
});
