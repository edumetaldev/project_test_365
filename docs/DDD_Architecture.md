# Arquitectura Domain-Driven Design (DDD) - Actualización de Reservaciones

## Resumen

Se ha refactorizado la funcionalidad de actualización de reservaciones para implementar una arquitectura Domain-Driven Design (DDD), separando claramente las responsabilidades entre las capas de dominio, aplicación e infraestructura.

## Estructura de Directorios

```
app/
├── Domain/
│   └── Reservation/
│       ├── Entities/
│       │   └── Reservation.php
│       ├── ValueObjects/
│       │   ├── ReservationId.php
│       │   ├── ReservationStatus.php
│       │   └── FlightNumber.php
│       ├── Repositories/
│       │   └── ReservationRepositoryInterface.php
│       ├── Services/
│       │   └── ReservationStatusService.php
│       └── Events/
│           └── ReservationStatusChanged.php
├── Application/
│   └── Reservation/
│       ├── Commands/
│       │   └── ChangeReservationStatusCommand.php
│       ├── Handlers/
│       │   └── ChangeReservationStatusHandler.php
│       └── DTOs/
│           └── ReservationDTO.php
├── Infrastructure/
│   └── Reservation/
│       └── Repositories/
│           └── EloquentReservationRepository.php
└── Http/
    └── Controllers/
        └── ReservationChangeStatusController.php
```

## Capas de la Arquitectura

### 1. Capa de Dominio (Domain Layer)

**Responsabilidad**: Contiene la lógica de negocio pura y las reglas del dominio.

#### Entidades
- **Reservation**: Entidad principal que encapsula la lógica de negocio de una reservación
  - Valida transiciones de estado
  - Genera eventos de dominio
  - Mantiene invariantes del dominio

#### Value Objects
- **ReservationId**: Identificador único de la reservación
- **ReservationStatus**: Estado de la reservación con validaciones y reglas de transición
- **FlightNumber**: Número de vuelo con validaciones específicas

#### Servicios de Dominio
- **ReservationStatusService**: Orquesta la lógica de cambio de estado

#### Eventos de Dominio
- **ReservationStatusChanged**: Evento disparado cuando cambia el estado de una reservación

### 2. Capa de Aplicación (Application Layer)

**Responsabilidad**: Orquesta los casos de uso y coordina entre el dominio y la infraestructura.

#### Commands
- **ChangeReservationStatusCommand**: Comando que representa la intención de cambiar el estado

#### Handlers
- **ChangeReservationStatusHandler**: Maneja la ejecución del comando

#### DTOs
- **ReservationDTO**: Objeto de transferencia de datos para la capa de presentación

### 3. Capa de Infraestructura (Infrastructure Layer)

**Responsabilidad**: Implementa las interfaces del dominio usando tecnologías específicas.

#### Repositorios
- **EloquentReservationRepository**: Implementación del repositorio usando Eloquent ORM

### 4. Capa de Presentación (Presentation Layer)

**Responsabilidad**: Maneja las peticiones HTTP y respuestas.

#### Controladores
- **ReservationChangeStatusController**: Controlador que recibe peticiones HTTP y delega a la capa de aplicación

## Reglas de Negocio Implementadas

### Transiciones de Estado Válidas

```
PENDING → CONFIRMED
PENDING → CANCELLED
CONFIRMED → CHECKED_IN
CONFIRMED → CANCELLED
```

### Estados Finales
- **CANCELLED**: No se puede modificar
- **CHECKED_IN**: No se puede modificar

### Validaciones
- Solo se pueden realizar transiciones válidas entre estados
- Las reservaciones en estados finales no pueden ser modificadas
- Validación de formato para números de vuelo
- Validación de identificadores de reservación

## Beneficios de la Refactorización

1. **Separación de Responsabilidades**: Cada capa tiene una responsabilidad específica
2. **Testabilidad**: La lógica de negocio puede ser probada independientemente
3. **Mantenibilidad**: Cambios en una capa no afectan necesariamente a otras
4. **Flexibilidad**: Fácil intercambio de implementaciones (ej: cambiar de Eloquent a otra ORM)
5. **Expresividad**: El código refleja claramente las reglas de negocio
6. **Eventos de Dominio**: Permite reaccionar a cambios importantes en el sistema

## Patrones Implementados

- **Repository Pattern**: Abstrae el acceso a datos
- **Command Pattern**: Encapsula operaciones como objetos
- **Domain Events**: Permite desacoplamiento mediante eventos
- **Value Objects**: Objetos inmutables que representan conceptos del dominio
- **Service Layer**: Encapsula lógica de negocio compleja

## Configuración

Los servicios están registrados en `AppServiceProvider.php`:

```php
// Repository bindings
$this->app->bind(
    ReservationRepositoryInterface::class,
    EloquentReservationRepository::class
);

// Service bindings
$this->app->bind(ReservationStatusService::class, function ($app) {
    return new ReservationStatusService(
        $app->make(ReservationRepositoryInterface::class)
    );
});

// Handler bindings
$this->app->bind(ChangeReservationStatusHandler::class, function ($app) {
    return new ChangeReservationStatusHandler(
        $app->make(ReservationStatusService::class),
        $app->make('events')
    );
});
```

## Testing

Se han actualizado las pruebas para validar:
- Transiciones válidas de estado
- Rechazo de transiciones inválidas
- Validación de reglas de negocio
- Manejo de errores del dominio
- Preservación de datos durante cambios de estado

## Uso

```php
// El controlador ahora usa la arquitectura DDD
$command = new ChangeReservationStatusCommand(
    ReservationId::fromInt($reservationId),
    ReservationStatus::fromString($request->getStatus())
);

$this->handler->handle($command);
```

Esta refactorización proporciona una base sólida para futuras extensiones y mantiene la integridad de las reglas de negocio del dominio de reservaciones.
