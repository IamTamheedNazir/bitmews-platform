<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityPostResource\Pages;
use App\Models\CommunityPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommunityPostResource extends Resource
{
    protected static ?string $model = CommunityPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Community';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Post Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'post' => 'Post',
                                'article' => 'Article',
                                'video' => 'Video',
                                'poll' => 'Poll',
                                'trade_idea' => 'Trade Idea',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Metadata')
                    ->schema([
                        Forms\Components\TagsInput::make('tags'),
                        Forms\Components\TagsInput::make('token_tags')
                            ->label('Token Tags'),
                        Forms\Components\Select::make('sentiment')
                            ->options([
                                'bullish' => 'Bullish',
                                'bearish' => 'Bearish',
                                'neutral' => 'Neutral',
                            ]),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'moderation' => 'Under Moderation',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('published'),
                    ])->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_sponsored')
                            ->default(false),
                        Forms\Components\Toggle::make('is_premium')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('published_at'),
                    ])->columns(3),

                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\TextInput::make('views_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('likes_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('comments_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('shares_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('earnings')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                    ])->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => 'post',
                        'success' => 'article',
                        'warning' => 'video',
                        'info' => 'poll',
                        'danger' => 'trade_idea',
                    ]),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('sentiment')
                    ->badge()
                    ->colors([
                        'success' => 'bullish',
                        'danger' => 'bearish',
                        'gray' => 'neutral',
                    ]),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                        'warning' => 'moderation',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('views_count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('likes_count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comments_count')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_sponsored')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_premium')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type'),
                Tables\Filters\SelectFilter::make('sentiment'),
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\TernaryFilter::make('is_sponsored'),
                Tables\Filters\TernaryFilter::make('is_premium'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommunityPosts::route('/'),
            'create' => Pages\CreateCommunityPost::route('/create'),
            'edit' => Pages\EditCommunityPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'moderation')->count() ?: null;
    }
}
