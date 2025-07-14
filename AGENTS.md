# AGENTS

This file provides guidance to AI coding agents (Claude, Codex, Gemini) when working with code in this repository.

## Project Overview

Futter.rocks is a Laravel-based application for planning and managing camp kitchens for youth retreats. Users can create meal plans, calculate quantities with food factor adjustments, manage recipes, and generate shopping lists.

## Technology Stack

- **Backend**: Laravel 12.0 (PHP 8.3+) with Jetstream/Sanctum
- **Frontend**: Livewire 3.0 + Filament 3.3 (forms/tables) + Tailwind CSS
- **Database**: PostgreSQL (dev), SQLite (test)
- **Testing**: Pest PHP
- **Build**: Vite 6.3 with pnpm 10.8
- **Environment**: Devenv (Nix-based) with Docker

## Development Commands

### Local Development

You can always assume the application is already running and accessible at `http://localhost:8000`. No need to start a dev server.

### Testing

```bash
# Run all tests
aa test

# Run filtered tests
atf <filter>
```

### Code Quality
```bash
# Check code style
composer lint

# Fix code style
composer lint:fix
```

### Artisan Commands
```bash
aa <command>
```

## Architecture

### Core Structure
- **App/Livewire/**: Main UI components (Events, Recipes, Groups)
- **App/Models/**: Eloquent models with team-scoped multi-tenancy
- **App/Services/**: Business logic services
- **App/Utils/**: Utility classes

### Key Models & Relationships
- **Event**: Central entity with meals, participant groups, shopping tours
- **Recipe**: Manages recipes with ingredients and meal associations
- **Meal**: Links events to recipes with date/time organization
- **ParticipantGroup**: Handles groups with food factors
- **Team**: Multi-tenancy support with CurrentTeam scope

### Frontend Architecture
- Livewire components for reactive UI (not traditional controllers)
- Filament forms and tables for complex interactions
- Tailwind CSS for styling
- Blade components in App/View/Components/

### Database
- PostgreSQL for development
- SQLite for testing
- Multi-tenancy via CurrentTeam scope on models
- Comprehensive migrations with proper relationships

### Translations

- Whenever you're asked to translate something, add the translated strings to [de.json](mdc:lang/de.json), translate it using the same style as the other existing translations and replace the usage of the original string with a call to __('STRING') (use {{ __('STRING') }} when in a blade template) so that the translated string is shown to the user.  The same applies when you're asked to extract strings for translation.
- Whenever you're updating a translation json file, the last line "___": "___" should be kept as the last line. This is to improve the git diff. 
- When adding new strings in the application, always add translations for it.

## Development Workflow

1. **Setup**: Clone repo, enter `devenv shell`, run `composer install` and `pnpm install`
2. **Development**: Start with `devenv up`, frontend with `pnpm dev`
3. **Testing**: Use `a test` for full suite, `atf <filter>` for specific tests
4. **Linting**: Run `composer lint` before commits
5. **CI/CD**: GitHub Actions handles testing, linting, and Docker image releases

## Key Features

- Multi-tenant team management
- Event-based meal planning
- Recipe import from URLs with ingredient parsing
- Shopping list generation with food factor calculations
- PDF generation for shopping lists and meal plans
- German internationalization support

## Development Notes

- Use Livewire testing methods for component tests
- All models should extend from base model with team scoping
- Follow Laravel conventions with Pest PHP testing
- Use Filament components for complex forms and tables
- Maintain comprehensive test coverage for core functionality
- Use the available components (buttons, messages, etc) whenever possible.
- Use artisan commands to create new models, controllers, factories, migrations, etc.
