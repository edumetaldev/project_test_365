# **Prueba técnica \- Backend Senior**

## **Contexto**

Eres parte del equipo técnico de una aerolínea ficticia llamada **SkyLink**.

SkyLink quiere un **dashboard interno** para visualizar en tiempo real:

* Reservas recientes y su estado.  
* Lista de pasajeros por vuelo.  
* Notificaciones del sistema (cambios de estado, cancelaciones, check-in completado).

El sistema **recibe eventos en tiempo real** desde su motor de reservas y necesita mostrarlos al dashboard inmediatamente.  
No hay UI obligatoria — puedes usar endpoints, colas y logs simulando el dashboard.

## **Objetivo de la prueba**

Construir una **API \+ servicio de streaming** que:

1. **Gestione pasajeros y reservas** (CRUD básico).  
2. **Reciba y procese eventos en tiempo real** (simulados).  
3. **Publique actualizaciones instantáneas** a un dashboard o suscriptor WebSocket.

Debe estar **bien documentado, escalable, modular y con decisiones técnicas justificadas**.

## **Requerimientos técnicos**

* **Backend principal:** Laravel 10+ (PHP 7.4+).  
* **Servicio de streaming:** Node.js (preferible con Socket.io o equivalente).  
* **Base de datos:** MySQL.  
* **Comunicación entre Laravel y Node:**  
  * Puede ser vía Redis pub/sub, base de datos, o colas simuladas.  
* **Uso obligatorio de IA** para:  
  * Escribir tests.  
  * Optimizar consultas SQL.  
  * Generar documentación (OpenAPI).

* **Arquitectura limpia:**

  * Separar lógica de dominio, controladores, repositorios y servicios.	  
    * Aplicar principios SOLID  
  * Incluir mínimo **1 patrón de diseño** (Observer, Pub/Sub, Strategy, etc.).

## **Escenario funcional**

### **1\. Endpoints en general**

Implementar en `/api`:

* `/reservations`

  * Crea reserva con pasajeros asociados.  
  * Estado inicial: `PENDING`.  
  * Guarda `flight_number`, `departure_time`, `passengers[]`.

* `/reservations/{id}/status`

  * Cambia estado a: `CONFIRMED`, `CANCELLED`, `CHECKED_IN`.  
  * Al cambiar estado, publicar evento en **servicio Node** para que el dashboard lo muestre en tiempo real.

* `/reservations`

  * Lista reservas (filtro por estado y fecha).

* `/passengers/{id}`

  * Detalle de pasajero.

### **2\. Servicio Node.js (realtime)**

* Escucha eventos enviados desde Laravel (pub/sub o polling).

Emite en un canal WebSocket:

 json  
`{`  
  `"event": "reservation.updated",`  
  `"data": {`  
    `"id": "abc123",`  
    `"status": "CONFIRMED",`  
    `"passengers": [...]`  
  `}`  
`}`

* Simular dashboard con un cliente WebSocket que imprime cambios en consola.

### **3\. Simulación de eventos externos**

* Crear comando Artisan que:

  * Cada 5 segundos genera un cambio de estado aleatorio en una reserva.  
  * Publica evento a Node.

* Node emite el cambio al cliente WebSocket en tiempo real.

### **4\. MySQL**

* Tablas: `reservations`, `passengers`, `notifications`.  
* Incluir `EXPLAIN` y propuesta de índices.

### **5\. Documentación y entregables**

* **README.md** con:  
  * Cómo correr el proyecto.  
  * Variables de entorno necesarias.  
  * Justificación técnica de la arquitectura.  
* Justificar patrones elegidos (p. ej., Pub/Sub, CQRS, Observer).  
* **OpenAPI** para endpoints Laravel.  
* **Mini ERD** de la base.  
* **Mínimo 1 test unitario** (Laravel) y **1 test de integración** (Node/Laravel).

