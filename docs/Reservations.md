# Documentación del Controlador ReservationsController

## Información General

El `ReservationsController` es responsable de manejar las operaciones relacionadas con las reservas de vuelos en la API. Este controlador implementa las operaciones básicas de creación y listado de reservas.

**Versión de Laravel:** 12.0  
**Namespace:** `App\Http\Controllers`  
**Clase:** `ReservationsController` (final)

## Métodos del Controlador

### 1. index()

**Propósito:** Obtener todas las reservas existentes

**Endpoint:** `GET /api/reservations`

**Parámetros:** Ninguno

**Respuesta exitosa (200):**
```json
{
    "message": "Reservations retrieved successfully",
    "data": [
        {
            "id": 1,
            "flight_number": "AA123",
            "departure_time": "2025-12-25T10:30:00.000000Z",
            "status": "PENDING",
            "passengers": [
                {
                    "name": "Juan Pérez",
                    "document": "12345678"
                }
            ],
            "created_at": "2025-09-12T17:00:00.000000Z",
            "updated_at": "2025-09-12T17:00:00.000000Z"
        }
    ]
}
```

### 2. store(Request $request)

**Propósito:** Crear una nueva reserva

**Endpoint:** `POST /api/reservations`

**Parámetros requeridos:**
- `flight_number` (string, máximo 10 caracteres)
- `departure_time` (datetime, formato: Y-m-d H:i:s)
- `passengers` (array, mínimo 1 elemento)
- `passengers.*.name` (string, máximo 100 caracteres)
- `passengers.*.document` (string, máximo 20 caracteres)

**Parámetros opcionales:**
- `status` (string, valores permitidos: 'PENDING', 'CONFIRMED', 'CANCELLED')
  - Valor por defecto: 'PENDING'

**Respuesta exitosa (201):**
```json
{
    "message": "Reservation created successfully",
    "data": {
        "id": 1,
        "flight_number": "AA123",
        "departure_time": "2025-12-25T10:30:00.000000Z",
        "status": "PENDING",
        "passengers": [
            {
                "name": "Juan Pérez",
                "document": "12345678"
            }
        ],
        "created_at": "2025-09-12T17:00:00.000000Z",
        "updated_at": "2025-09-12T17:00:00.000000Z"
    }
}
```

**Respuesta de error de validación (422):**
```json
{
    "message": "Validation failed",
    "errors": {
        "flight_number": ["The flight number field is required."],
        "departure_time": ["The departure time does not match the format Y-m-d H:i:s."],
        "passengers": ["There must be at least one passenger."]
    }
}
```

## Validaciones Implementadas

### Validaciones del Campo `flight_number`
- **required:** El campo es obligatorio
- **string:** Debe ser una cadena de texto
- **max:10:** Máximo 10 caracteres

### Validaciones del Campo `departure_time`
- **required:** El campo es obligatorio
- **date_format:Y-m-d H:i:s:** Debe seguir el formato específico (ej: 2025-12-25 10:30:00)

### Validaciones del Campo `status`
- **sometimes:** Campo opcional
- **string:** Debe ser una cadena de texto
- **Rule::in(['PENDING', 'CONFIRMED', 'CANCELLED']):** Solo acepta estos valores

### Validaciones del Campo `passengers`
- **required:** El campo es obligatorio
- **array:** Debe ser un array
- **min:1:** Debe tener al menos un pasajero

### Validaciones de Pasajeros Individuales
- **passengers.*.name:**
  - **required:** Nombre obligatorio para cada pasajero
  - **string:** Debe ser una cadena de texto
  - **max:100:** Máximo 100 caracteres

- **passengers.*.document:**
  - **required:** Documento obligatorio para cada pasajero
  - **string:** Debe ser una cadena de texto
  - **max:20:** Máximo 20 caracteres

## Mensajes de Error Personalizados

El controlador incluye mensajes de error personalizados en español para mejorar la experiencia del usuario:

- `flight_number.required`: "The flight number field is required."
- `flight_number.max`: "The flight number may not be greater than 10 characters."
- `departure_time.required`: "The departure time field is required."
- `departure_time.date_format`: "The departure time does not match the format Y-m-d H:i:s."
- `passengers.required`: "The passengers field is required."
- `passengers.min`: "There must be at least one passenger."
- `passengers.*.name.required`: "Each passenger must have a name."
- `passengers.*.document.required`: "Each passenger must have a document."

## Tests Implementados

### 1. `test_can_create_reservation_successfully`
- **Propósito:** Verifica que se puede crear una reserva exitosamente con datos válidos
- **Datos de prueba:** Reserva con vuelo AA123, 2 pasajeros
- **Verificaciones:**
  - Status HTTP 201
  - Estructura JSON correcta
  - Datos guardados en base de datos
  - Status por defecto 'PENDING'

### 2. `test_can_create_reservation_with_default_status`
- **Propósito:** Confirma que el status por defecto 'PENDING' se asigna correctamente
- **Datos de prueba:** Reserva sin especificar status
- **Verificaciones:**
  - Status HTTP 201
  - Status asignado automáticamente

### 3. `test_create_reservation_fails_with_invalid_data`
- **Propósito:** Valida que los datos inválidos son rechazados apropiadamente
- **Datos de prueba:** Campos requeridos vacíos, fecha inválida, array de pasajeros vacío
- **Verificaciones:**
  - Status HTTP 422
  - Mensaje de error de validación
  - Estructura de errores correcta
  - No se guarda nada en base de datos

### 4. `test_create_reservation_fails_with_missing_passenger_data`
- **Propósito:** Verifica que los datos faltantes de pasajeros son detectados
- **Datos de prueba:** Pasajero sin documento
- **Verificaciones:**
  - Status HTTP 422
  - Error de validación específico

### 5. `test_can_list_empty_reservations`
- **Propósito:** Confirma que el listado funciona correctamente cuando no hay reservas
- **Verificaciones:**
  - Status HTTP 200
  - Array de datos vacío
  - Estructura JSON correcta

### 6. `test_can_list_reservations_successfully`
- **Propósito:** Verifica que el listado de reservas funciona con datos existentes
- **Datos de prueba:** Crea 2 reservas de prueba
- **Verificaciones:**
  - Status HTTP 200
  - Estructura JSON correcta
  - Cantidad correcta de reservas
  - Datos completos de cada reserva

### 7. `test_listed_reservations_contain_correct_passenger_data`
- **Propósito:** Confirma que los datos de pasajeros se incluyen correctamente en el listado
- **Datos de prueba:** Reserva con datos específicos de pasajeros
- **Verificaciones:**
  - Status HTTP 200
  - Datos de pasajeros exactos en la respuesta

## Manejo de Errores

El controlador implementa manejo de errores con try-catch para capturar excepciones durante la creación de reservas:

- **Éxito:** Retorna status 201 con los datos de la reserva creada
- **Error de validación:** Retorna status 422 con detalles de los errores
- **Error del servidor:** Retorna status 500 con mensaje de error

## Estructura del Modelo Reservation

El modelo `Reservation` asociado incluye:

**Campos fillable:**
- `flight_number`
- `departure_time`
- `status`
- `passengers`

**Casts automáticos:**
- `departure_time`: datetime
- `passengers`: array

**Atributos por defecto:**
- `status`: 'PENDING'

## Rutas API

Las rutas están definidas en `routes/api.php`:

```php
Route::apiResource('reservations', ReservationsController::class)->only(['index', 'store']);
```

Esto genera las siguientes rutas:
- `GET /api/reservations` → `index()`
- `POST /api/reservations` → `store()`