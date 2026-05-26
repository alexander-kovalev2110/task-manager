# Backend Architecture

This project is built with PHP + Symfony + Doctrine ORM and follows the principles of a layered architecture with strict separation between HTTP, application, and domain logic using CQRS (Command Query Responsibility Segregation).

The architecture ensures predictable data flow:

**Command (Write) Flow:**
FE → Kernel → Security → MapRequestPayload → Controller → CommandBus → CommandHandler → Domain Entities → Repository → JsonResponse → FE

**Query (Read) Flow:**
FE → Kernel → Security → Controller → QueryBus → QueryHandler → Repository → View DTO → JsonResponse → FE

---

## Architectural Principles

* **Separation of Concerns** — each layer has a single responsibility.
* **Thin Controllers / Fat Domain & Handlers** — controllers only delegate to command/query buses; business rules are enforced inside domain models, and orchestration lives in handlers.
* **DTO-driven boundaries** — request and response models are explicit.
* **Domain-first approach** — application handlers operate on domain entities and interfaces, abstracting database infrastructure.
* **Centralized validation** — input validation occurs during request decoding, before any business logic is executed.
* **Explicit CQRS data flow** — clear separation of write actions (Commands) and read actions (Queries) with no side effects.

---

## Request Lifecycle Overview

### 1. Kernel Layer (HTTP Entry Point)
* **Purpose:** Handles the incoming HTTP request and bootstraps the Symfony application.
* **Flow:** 
  ```
  Incoming HTTP Request → Kernel
  ```

### 2. Security Layer
* **Purpose:** Handles authentication and authorization.
* **Responsibilities:**
  * JWT authentication (resolving endpoints via tokens).
  * Checking user permissions.
  * Flow: `FE → Kernel → Security`

### 3. Argument Resolver Layer (`MapRequestPayload`)
* **Purpose:** Automatically deserialize the incoming HTTP request body into structured DTO models.
* **Responsibilities:**
  * Parse raw HTTP input.
  * Hydrate and validate the Request DTO.
  * Throw validation errors immediately on invalid payload structure (returning `422 Unprocessable Entity` or `400 Bad Request`).
* **Example:**
  * [AssignTaskRequest](file:///d:/KA/tests/github/task_manager/src/Application/DTO/AssignTaskRequest.php)

### 4. DTO Layer (Request Models)
* **Purpose:** Strongly typed input definitions protecting application boundaries.
* **Structure:**
  * [src/Application/DTO/](file:///d:/KA/tests/github/task_manager/src/Application/DTO)
* **Characteristics:**
  * Strict typing and readonly attributes.
  * Symfony validation constraints (`#[Assert\NotBlank]`, `#[Assert\Email]`, etc.).

### 5. Controller Layer (HTTP Orchestration)
* **Purpose:** Coordinates request handling without containing business or persistence logic.
* **Characteristics:**
  * No validation logic.
  * No database interaction.
  * Dispatches commands or queries to the bus.
* **Structure:**
  * [src/Infrastructure/Controller/Task/](file:///d:/KA/tests/github/task_manager/src/Infrastructure/Controller/Task)
  * [src/Infrastructure/Controller/Auth/](file:///d:/KA/tests/github/task_manager/src/Infrastructure/Controller/Auth)
* **Example Method:**
  ```php
  #[Route('/api/task/assignee', methods: ['PUT'])]
  public function __invoke(
      #[MapRequestPayload] AssignTaskRequest $request,
      CommandBus $commandBus
  ): JsonResponse
  ```

### 6. Messenger Bus
* **Purpose:** Decouples controllers from handlers.
* **Structure:**
  * [src/Infrastructure/Bus/](file:///d:/KA/tests/github/task_manager/src/Infrastructure/Bus) (e.g., [CommandBus.php](file:///d:/KA/tests/github/task_manager/src/Infrastructure/Bus/CommandBus.php) and [QueryBus.php](file:///d:/KA/tests/github/task_manager/src/Infrastructure/Bus/QueryBus.php))

### 7. Handler Layer (Application Logic)
* **Purpose:** Implements application-level orchestration and transactional boundaries.
* **Responsibilities:**
  * Retrieve domain entities from repositories.
  * Trigger state modifications on domain aggregates.
  * Save changed entities back to persistent storage.
* **Structure:**
  * [src/Application/Handler/](file:///d:/KA/tests/github/task_manager/src/Application/Handler) (Command Handlers)
  * [src/Application/QueryHandler/](file:///d:/KA/tests/github/task_manager/src/Application/QueryHandler) (Query Handlers)
* **Example Handlers:**
  * [AssignTaskHandler.php](file:///d:/KA/tests/github/task_manager/src/Application/Handler/AssignTaskHandler.php)
  * [GetTaskListHandler.php](file:///d:/KA/tests/github/task_manager/src/Application/QueryHandler/GetTaskListHandler.php)

### 8. Repository Layer (Data Access Interfaces)
* **Purpose:** Encapsulate database retrieval and persistence.
* **Structure:**
  * Domain interfaces: [src/Domain/Task/TaskRepositoryInterface.php](file:///d:/KA/tests/github/task_manager/src/Domain/Task/TaskRepositoryInterface.php)
  * Infrastructure implementations: [src/Infrastructure/Repository/](file:///d:/KA/tests/github/task_manager/src/Infrastructure/Repository) (using Doctrine Entity Manager).

### 9. Domain Layer (Entities & Value Objects)
* **Purpose:** Represent core business models, state machines, and validation invariants.
* **Structure:**
  * [src/Domain/Task/](file:///d:/KA/tests/github/task_manager/src/Domain/Task) (e.g., [Task.php](file:///d:/KA/tests/github/task_manager/src/Domain/Task/Task.php))
  * [src/Domain/User/](file:///d:/KA/tests/github/task_manager/src/Domain/User) (e.g., [User.php](file:///d:/KA/tests/github/task_manager/src/Domain/User/User.php) and Value Object [Email](file:///d:/KA/tests/github/task_manager/src/Domain/User/UserEmail.php))
* **Characteristics:**
  * Doctrine ORM configuration.
  * Enforces state machine transitions (e.g., `Task::start()` and `TaskStatus::start()`).
  * Free from framework/HTTP concerns.

### 10. Response Layer (Output Models / View DTOs)
* **Purpose:** Defines structured, immutable payloads returned by the API.
* **Example:**
  * [TaskView.php](file:///d:/KA/tests/github/task_manager/src/Application/DTO/TaskView.php)
* **Benefits:**
  * Prevents exposing internal ORM entity relations directly to JSON.
  * Decouples FE representation from the database schema.

---

### AI-Powered Subtask Generation (OpenAI Integration)

The application leverages the **OpenAI API** to enhance project management efficiency by automatically breaking down high-level tasks into actionable steps. 

#### Key Technical Implementation Details:
* **Smart Task Analysis:** Uses the highly efficient `gpt-4o-mini` model to programmatically analyze a task's title and generate exactly 5 concrete, context-aware subtasks.
* **Defensive Parsing & Validation:** Implements strict backend post-processing. The system automatically detects and strips Markdown code blocks (e.g., ````json ````), cleans whitespace, and validates the raw JSON structure before handling the data.
* **Type Safety & Reliability:** Utilizes explicit data mapping to enforce strong string types on the array elements, ensuring that the frontend receives a predictable and clean payload.
* **Graceful Degradation:** If the OpenAI API is unavailable or returns an invalid response, the system catches the failure globally, logs the error, and throws a controlled domain exception without crashing the core application workflow.

#### API Endpoint Example
* **POST** `/api/task/suggest-subtasks` — Accepts a JSON payload with `taskId` and returns a strictly structured JSON array of strings:
  ```json
  [
    "Install and configure the CI/CD tool (e.g., GitHub Actions)",
    "Create a configuration file for the build and test pipeline",
    "Set up automated execution of PHPUnit tests"
  ]
  ```

---

## Exception Handling & Validation

1. **Request Payload Validation:**
   Occurs at the resolver stage via `#[MapRequestPayload]`. Violations throw exceptions caught automatically by the API listener.
   
2. **Domain/Validation Exception Listener:**
   * Handled by [ApiExceptionListener.php](file:///d:/KA/tests/github/task_manager/src/Infrastructure/Http/ApiExceptionListener.php).
   * Unwraps Symfony Messenger's `HandlerFailedException` to expose the underlying domain reason.
   * Maps domain-specific and system exceptions to clean JSON formats and appropriate HTTP status codes (e.g., mapping `\DomainException` to `409 Conflict`).

---

## Data Flow Example: Assign Task

```
FE (PUT /api/task/assignee)
 ↓
Kernel
 ↓
Security (validate token)
 ↓
MapRequestPayload (construct & validate AssignTaskRequest DTO)
 ↓
AssignTaskController
 ↓
CommandBus (dispatch AssignTaskCommand)
 ↓
AssignTaskHandler
 ↓
DoctrineTaskRepository -> findById(taskId)
DoctrineUserRepository -> findByEmail(email)
 ↓
Task -> assignTo(User)  [Domain Rule: Cannot assign completed tasks]
 ↓
DoctrineTaskRepository -> save(Task)
 ↓
JsonResponse (201 Created)
 ↓
FE
```

---

## Folder Structure Overview

```
src/
  Application/
    Command/           # Command objects (write actions)
    DTO/               # Input request DTOs and output view DTOs
    Handler/           # Command handlers
    Query/             # Query objects (read actions)
    QueryHandler/      # Query handlers
    Service/           # Application-specific helper services (e.g. TokenService)
  Domain/
    Exception/         # Pure domain exceptions
    Task/              # Task entity, repository interface, and task enums
    User/              # User entity, email Value Object, and repository interface
  Infrastructure/
    Bus/               # CommandBus and QueryBus implementations
    Controller/        # API Controllers grouped by entity domain
    Doctrine/          # Custom types (e.g. EmailType)
    Http/              # Listeners (e.g. ApiExceptionListener)
    Repository/        # Doctrine repository implementations (data access)
  Kernel.php           # Symfony Kernel
```

---

## Design Benefits

* **Strict Separation of Concerns:** Core business rules are independent of Symfony, Messenger, or database structure.
* **CQRS Benefits:** Read models (`TaskView`) are optimized independently, while write logic ensures consistency.
* **Testability:** Core domain classes and handlers can be tested in isolation with simple unit tests.
* **Consistent API Contracts:** The output JSON schema is explicitly controlled by DTOs rather than direct serialization of entities.
