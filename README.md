<p align="center"><a href="https://futter.rocks" target="_blank"><img src="https://raw.githubusercontent.com/kolaente/futter.rocks/refs/heads/main/resources/logo.svg" width="100" alt="Futter.rocks Logo"></a></p>

<p align="center">
<a href="https://github.com/kolaente/futter.rocks/actions"><img src="https://github.com/kolaente/futter.rocks/workflows/CI/badge.svg" alt="Build Status"></a>
</p>

## Futter.rocks

A tool designed for planning and managing camp kitchens for (mostly) youth retreats. Features include:

- Creating meal plans
- Calculating quantities (with food factor adjustment)
- Managing recipes
- Simplifying shopping trips

Visit [the website](https://futter.rocks) to learn more.

## Technical Stack

Futter.rocks is built on Laravel, utilizing Livewire for reactive components and Filament to easily create forms and tables.

## Development

To get started with the development quickly:

1. Prerequisites:
    - [Devenv](https://devenv.sh/getting-started/)
    - Docker
2. Clone the repository
3. Enter a devenv shell: `devenv shell` (if you have envrc installed, this will happen automatically when you enter the directory)
4. Install php dependencies: `composer install`
5. Install frontend dependencies: `pnpm install`
6. Run `devenv up` in the project directory - this will start the Docker containers via Devenv
7. Access the application at http://localhost:8000

Devenv manages the development environment configuration while Docker provides the containerized services (database, etc.) needed for the application. The project uses pnpm for JavaScript dependency management.

## License

Futter.rocks is open-sourced software licensed under the [AGPLv3 license](https://opensource.org/license/agpl-v3).
