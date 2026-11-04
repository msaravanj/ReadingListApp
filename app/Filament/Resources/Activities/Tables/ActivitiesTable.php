<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('causer_id')
                    ->label('Causer ID')
                    ->sortable(),
                TextColumn::make('event')
                    ->badge()
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
                TextColumn::make('subject_id')
                    ->label('Subject ID')
                    ->sortable(),
                TextColumn::make('changes')
                    ->label('Changes')
                    ->getStateUsing(
                        fn (Activity $record): string => $record->event === 'updated'
                            ? self::formatChanges($record->attribute_changes)
                            : '',
                    )
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime(timezone: 'Europe/Zagreb', format: 'd.m.Y. H:i:s')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function formatValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '-';
    }

    /**
     * @param  Collection<string, mixed>|array<string, mixed>|string|null  $changes
     */
    private static function formatChanges(Collection|array|string|null $changes): string
    {
        if ($changes instanceof Collection) {
            $changes = $changes->toArray();
        }

        if ($changes === null || $changes === '') {
            return '-';
        }

        if (is_string($changes)) {
            return $changes;
        }

        $attributes = is_array($changes['attributes'] ?? null) ? $changes['attributes'] : [];
        $old = is_array($changes['old'] ?? null) ? $changes['old'] : [];
        $formattedChanges = [];

        foreach (array_unique([...array_keys($old), ...array_keys($attributes)]) as $attribute) {
            $hasOldValue = array_key_exists($attribute, $old);
            $hasNewValue = array_key_exists($attribute, $attributes);
            $label = Str::headline($attribute);

            if ($hasOldValue && $hasNewValue) {
                $formattedChanges[] = sprintf(
                    '%s: %s → %s',
                    $label,
                    self::formatValue($old[$attribute]),
                    self::formatValue($attributes[$attribute]),
                );
            } elseif ($hasNewValue) {
                $formattedChanges[] = sprintf('%s: %s', $label, self::formatValue($attributes[$attribute]));
            } else {
                $formattedChanges[] = sprintf('%s: %s', $label, self::formatValue($old[$attribute]));
            }
        }

        return implode('; ', $formattedChanges) ?: '-';
    }
}
