# Development & Best Practices

This document outlines the key technical decisions, architecture, and best practices implemented in this project.

- __Unit Tests__: Comprehensive unit tests are written for all major functionalities to ensure code quality and reliability.
- __Gitflow with PRs using AIs__: The project follows the Gitflow workflow, with pull requests reviewed by AI to maintain code standards.
- __Observer for Card Order__: A model observer (`CardObserver`) automatically manages the order of cards within columns, simplifying creation and reordering logic.
- __Sanctum Authentication__: API authentication is handled using Laravel Sanctum, providing a secure token-based system for protecting endpoints.
- __DRY Validation__: Custom validation rules are used to avoid repetition (Don't Repeat Yourself) when validating card positions and column data in incoming requests.
- __Dockerized Environment__: The development environment is containerized using Docker, with a `Dockerfile` that mimics the production setup, including health checks for service reliability.
- __Middleware for Authorization__: Middleware is employed to verify that a user is authenticated and has the necessary permissions to access specific resources.
- __Postman Collection__: A complete Postman collection is available in the `docs/postman` directory. It includes all API endpoints, environment variables, a basic workflow demonstration, and tests.
- __Test Data Factories__: Laravel's model factories are used to generate realistic test data, ensuring that unit tests are robust and meaningful.
- __Parallel Testing__: A specific parallel test is implemented for the `firstOrCreate` endpoint for columns to handle potential race conditions.
- __GitHub Actions (CI/CD)__: The repository is equipped with GitHub Actions for a full CI/CD pipeline:
  - **Linting**: Lint check on every push.
  - **Testing**: Unit, parallel, and randomized tests are run on every push.
  - **Auditing**: Security audits are performed on every push and on a weekly schedule.
  - **Deployment**: The application is automatically deployed to production on pushes to the `main` branch.
  - **Caching**: Workflow dependencies are cached to ensure faster execution times.
