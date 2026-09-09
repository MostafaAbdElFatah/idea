<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

describe('schema', function (): void {
    it('creates the expected tables', function (string $table): void {
        expect(Schema::hasTable($table))->toBeTrue();
    })->with(['users', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'ideas', 'steps']);

    it('defines the users columns', function (): void {
        expect(Schema::hasColumns('users', ['id', 'first_name', 'last_name', 'email', 'email_verified_at', 'password', 'remember_token', 'created_at', 'updated_at']))->toBeTrue();
    });

    it('defines the ideas columns', function (): void {
        expect(Schema::hasColumns('ideas', ['id', 'user_id', 'title', 'description', 'status', 'image_path', 'links', 'created_at', 'updated_at']))->toBeTrue();
    });

    it('defines the steps columns', function (): void {
        expect(Schema::hasColumns('steps', ['id', 'idea_id', 'description', 'completed', 'created_at', 'updated_at']))->toBeTrue();
    });

    it('makes the users email unique', function (): void {
        $indexes = collect(Schema::getIndexes('users'));

        expect($indexes->contains(fn (array $index): bool => $index['unique'] && $index['columns'] === ['email']))->toBeTrue();
    });

    it('declares foreign keys with cascade on delete', function (string $table, string $column, string $foreignTable): void {
        $foreignKeys = collect(Schema::getForeignKeys($table));
        $match = $foreignKeys->first(fn (array $fk): bool => $fk['columns'] === [$column] && $fk['foreign_table'] === $foreignTable);

        expect($match)->not->toBeNull()
            ->and($match['on_delete'])->toBe('cascade');
    })->with([
        'ideas.user_id' => ['ideas', 'user_id', 'users'],
        'steps.idea_id' => ['steps', 'idea_id', 'ideas'],
    ]);

    it('marks nullable and defaulted columns on ideas', function (): void {
        $columns = collect(Schema::getColumns('ideas'))->keyBy('name');

        expect($columns['description']['nullable'])->toBeTrue()
            ->and($columns['image_path']['nullable'])->toBeTrue()
            ->and($columns['title']['nullable'])->toBeFalse()
            ->and($columns['status']['default'])->toContain('pending')
            ->and($columns['links']['default'])->toContain('[]');
    });

    it('defaults steps.completed to false', function (): void {
        $columns = collect(Schema::getColumns('steps'))->keyBy('name');

        expect($columns['completed']['nullable'])->toBeFalse()
            ->and((int) trim((string) $columns['completed']['default'], "'\""))->toBe(0);
    });
})->group('feature', 'database');
