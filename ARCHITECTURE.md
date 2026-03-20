# Architecture: circuit-breaker

## Purpose

A PHP implementation of the Circuit Breaker pattern for PrestaShop services. It protects against cascading failures when calling external HTTP services by tracking failure rates and temporarily refusing requests when a service appears unavailable.

## Directory Structure

```
src/
  Contract/                          — Interfaces defining all extension points
    Circuit_Breaker_Interface.php    — Primary contract: call(), is_opened(), etc.
    Client_Interface.php             — HTTP client abstraction
    Factory_Interface.php            — Factory for creating circuit breakers
    Factory_Settings_Interface.php   — Settings value object contract
    Place_Interface.php              — State (place) abstraction
    Storage_Interface.php            — Persistence contract for transactions
    System_Interface.php             — The finite-state machine definition
    Transaction_Interface.php        — Per-service call tracking
    Transition_Dispatcher_Interface.php — Optional Symfony event bridge
  Client/
    Guzzle_Client.php                — Guzzle 6/7 HTTP adapter
    Symfony_Http_Client.php          — Symfony HttpClient adapter
  Exception/                         — Domain exceptions hierarchy
  Place/
    Abstract_Place.php
    Closed_Place.php                 — Normal operation; failures tracked here
    Half_Open_Place.php              — Test probe after open period expires
    Open_Place.php                   — Circuit open; requests rejected
  Storage/
    Simple_Array.php                 — In-memory (per-request) storage
    Doctrine_Cache.php               — Doctrine Cache bridge (persistent)
    Symfony_Cache.php                — PSR-6/16 Symfony Cache bridge (persistent)
  System/
    Main_System.php                  — Assembles the three places into a FSM
  Transaction/
    Simple_Transaction.php           — Tracks service URL, failure count, timestamps
  Transition/
    Event_Dispatcher.php             — Dispatches Symfony events on state change
    Null_Dispatcher.php              — No-op dispatcher (default)
  Factory_Settings.php               — Fluent builder for factory configuration
  Partial_Circuit_Breaker.php        — Base class with FSM logic
  Simple_Circuit_Breaker.php         — In-memory circuit breaker (single request)
  Advanced_Circuit_Breaker.php       — Persistent circuit breaker (cross-request)
  State.php                          — State name constants
  Transition.php                     — Transition name constants
```

## Key Design Decisions

- **Three-state FSM** — CLOSED (normal) → OPEN (tripped) → HALF_OPEN (probe) → CLOSED. Prevents partial recovery loops.
- **Pluggable storage** — `Simple_Array` resets each request; `Doctrine_Cache` / `Symfony_Cache` survive across requests, enabling real circuit breaking.
- **Recursive retry within CLOSED** — `Simple_Circuit_Breaker::call()` retries up to `failures` times before opening the circuit.
- **Fallback callable** — callers supply a `$fallback` closure invoked whenever the circuit is open or exhausted, avoiding naked exceptions in production.

## Extension Points

- Implement `Client_Interface` to add a new HTTP library (e.g., cURL, ReactPHP).
- Implement `Storage_Interface` to use Redis, Memcached, or a database for state persistence.
- Inject a `Transition_Dispatcher_Interface` to observe or log state transitions via Symfony events.

## Dependency Flow

```
Caller
  └── Simple_Circuit_Breaker::call(url, params, fallback)
        ├── Partial_Circuit_Breaker (FSM logic)
        │     ├── Main_System (place lookup)
        │     │     └── Place (Closed/Half_Open/Open)
        │     ├── Simple_Array (storage)
        │     └── Simple_Transaction (failure counter)
        └── Client_Interface::request(url, params)
```
