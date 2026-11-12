<?php

namespace App\Filament\Support;

use App\Support\Files;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class ContentFields
{
    /**
     * @return array<int, mixed>
     */
    public static function main(
        bool $withDate = false,
        bool $withLocation = false,
        bool $withSubtitle = true,
        bool $withIntro = true,
        bool $withBody2 = true,
    ): array {
        $fields = [
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
        ];

        if ($withSubtitle) {
            $fields[] = TextInput::make('subtitle')->maxLength(255);
        }

        $fields[] = TextInput::make('legacy_id')->numeric()->label('Original id');
        $fields[] = Toggle::make('is_published')->default(true);

        if ($withLocation) {
            $fields[] = TextInput::make('location')->maxLength(255);
            $fields[] = TextInput::make('year')->numeric();
        }

        if ($withDate) {
            $fields[] = TextInput::make('date_label')->label('Date')->maxLength(255);
            $fields[] = TextInput::make('year')->numeric();
        }

        if ($withIntro) {
            $fields[] = Textarea::make('intro')->rows(4)->columnSpanFull();
        }

        $fields[] = RichEditor::make('body')->columnSpanFull();

        if ($withBody2) {
            $fields[] = RichEditor::make('body2')->label('Second column')->columnSpanFull();
        }
        $fields[] = self::image('image_path')->label('Image')->columnSpanFull();

        return $fields;
    }

    /**
     * @return array<int, mixed>
     */
    public static function media(): array
    {
        return [
            Repeater::make('images')
                ->relationship('images')
                ->orderColumn('position')
                ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => [...$data, 'kind' => 'image'])
                ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => [...$data, 'kind' => 'image'])
                ->schema([
                    self::image('path')->label('Image')->required()->columnSpanFull(),
                    TextInput::make('title')->columnSpanFull(),
                    self::image('display_path')->label('Thumbnail')->columnSpanFull(),
                    TextInput::make('width')->numeric(),
                    TextInput::make('height')->numeric(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Repeater::make('pdfs')
                ->relationship('pdfs')
                ->orderColumn('position')
                ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => [...$data, 'kind' => 'pdf'])
                ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => [...$data, 'kind' => 'pdf'])
                ->schema([
                    TextInput::make('title')->required(),
                    self::file('path')
                        ->label('PDF')
                        ->acceptedFileTypes(['application/pdf'])
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    public static function image(string $name): FileUpload
    {
        return self::file($name)
            ->image()
            ->panelLayout('integrated')
            ->imagePreviewHeight('240')
            ->openable();
    }

    public static function file(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->disk('public')
            ->directory('uploads')
            ->visibility('public')
            ->fetchFileInformation(false)
            ->getUploadedFileUsing(function (FileUpload $component, string $file): ?array {
                if (self::isUnsafePath($file)) {
                    return null;
                }

                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $type = match ($extension) {
                    'jpg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'pdf' => 'application/pdf',
                    default => null,
                };

                $absolute = str_starts_with($file, 'uploads/')
                    ? storage_path('app/public/'.$file)
                    : public_path('files/'.$file);

                return [
                    'name' => basename($file),
                    'size' => is_file($absolute) ? (int) filesize($absolute) : 0,
                    'type' => $type,
                    'url' => Files::url($file),
                ];
            });
    }

    public static function section(string $heading, array $fields): Section
    {
        return Section::make($heading)->schema($fields)->columns(2);
    }

    private static function isUnsafePath(string $file): bool
    {
        return $file === ''
            || str_contains($file, '..')
            || str_contains($file, '\\')
            || str_starts_with($file, '/');
    }
}
